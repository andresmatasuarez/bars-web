import { Client } from 'basic-ftp';
import tls from 'node:tls';
import path from 'node:path';
import fs from 'node:fs';
import { createHash } from 'node:crypto';
import { createReadStream } from 'node:fs';

// --- Configuration ---

const DEPLOY_TARGETS = {
  plugins: [
    { local: 'wp-plugins/bars-commons', remote: 'plugins/bars-commons' },
    { local: 'wp-plugins/jury-post-type', remote: 'plugins/jury-post-type' },
    { local: 'wp-plugins/movie-post-type', remote: 'plugins/movie-post-type' },
  ],
  bars2013: [{ local: 'wp-themes/bars2013', remote: 'themes/bars2013' }],
  bars2026: [{ local: 'wp-themes/bars2026', remote: 'themes/bars2026' }],
  config: [
    { local: 'server-config/wp', remoteBase: '/2.0/' },
    { local: 'server-config/root', remoteBase: '/' },
  ],
};

const MAX_RETRIES = 3;
const MANIFEST_DIR = 'deploy';

const PROGRESS_THRESHOLD = 50 * 1024; // 50 KB — skip progress for smaller files

// --- Helpers ---

/** Format bytes as human-readable size (e.g. "2 KB", "1.2 MB"). */
function formatSize(bytes) {
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${Math.round(bytes / 1024)} KB`;
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

/** Format current time as HH:MM:SS. */
function formatTimestamp() {
  const d = new Date();
  return `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}:${String(d.getSeconds()).padStart(2, '0')}`;
}

/** Format milliseconds as human-readable duration (e.g. "2m 15s"). */
function formatDuration(ms) {
  const s = Math.round(ms / 1000);
  if (s < 1) return '<1s';
  if (s < 60) return `${s}s`;
  const m = Math.floor(s / 60);
  if (m < 60) return `${m}m ${s % 60}s`;
  return `${Math.floor(m / 60)}h ${m % 60}m`;
}

function resolveTargets(args) {
  const targets = [];
  for (const arg of args) {
    if (arg === 'all') {
      return Object.values(DEPLOY_TARGETS).flat();
    }
    const mapping = DEPLOY_TARGETS[arg];
    if (!mapping) {
      console.error(`Unknown target: "${arg}"`);
      console.error(`Valid targets: ${Object.keys(DEPLOY_TARGETS).join(', ')}, all`);
      process.exit(1);
    }
    targets.push(...mapping);
  }
  return targets;
}

function requireEnv(name) {
  const value = process.env[name];
  if (!value) {
    console.error(`Missing required environment variable: ${name}`);
    console.error('Make sure your .env file contains FTP_HOST, FTP_USER, and FTP_PASS.');
    process.exit(1);
  }
  return value;
}

/** Collect all files in a directory tree, returning paths relative to root. */
function walkDir(dir, prefix = '') {
  const results = [];
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    const rel = prefix ? `${prefix}/${entry.name}` : entry.name;
    if (entry.isDirectory()) {
      results.push(...walkDir(path.join(dir, entry.name), rel));
    } else {
      results.push(rel);
    }
  }
  return results;
}

// --- Manifest / Hashing ---

/** Stream a file through SHA-256 and return hex digest. */
function hashFile(filePath) {
  return new Promise((resolve, reject) => {
    const hash = createHash('sha256');
    const stream = createReadStream(filePath);
    stream.on('data', (chunk) => hash.update(chunk));
    stream.on('end', () => resolve(hash.digest('hex')));
    stream.on('error', reject);
  });
}

/** Hash all files in localDir, skipping .map files. Returns { relPath: hash }. */
async function computeManifest(localDir) {
  const files = walkDir(localDir);
  const manifest = {};

  // Hash files in parallel (batches of 50 to avoid fd exhaustion)
  const nonMapFiles = files.filter((f) => !f.endsWith('.map'));
  const BATCH_SIZE = 50;
  for (let i = 0; i < nonMapFiles.length; i += BATCH_SIZE) {
    const batch = nonMapFiles.slice(i, i + BATCH_SIZE);
    const hashes = await Promise.all(batch.map((rel) => hashFile(path.join(localDir, rel))));
    batch.forEach((rel, idx) => {
      manifest[rel] = hashes[idx];
    });
  }

  return manifest;
}

/** Compare previous and current manifests. Returns { toUpload, toDelete }. */
function diffManifests(previous, current) {
  const toUpload = [];
  const toDelete = [];

  // Files that are new or changed
  for (const [rel, hash] of Object.entries(current)) {
    if (!previous[rel] || previous[rel] !== hash) {
      toUpload.push(rel);
    }
  }

  // Files that were removed
  for (const rel of Object.keys(previous)) {
    if (!(rel in current)) {
      toDelete.push(rel);
    }
  }

  return { toUpload, toDelete };
}

/** Derive manifest file path from localDir (e.g. "wp-themes/bars2026" → "deploy/wp-themes--bars2026.manifest.json"). */
function manifestPath(localDir) {
  const name = localDir.replace(/\//g, '--');
  return path.join(MANIFEST_DIR, `${name}.manifest.json`);
}

function loadManifest(filePath) {
  try {
    return JSON.parse(fs.readFileSync(filePath, 'utf-8'));
  } catch {
    return {};
  }
}

function saveManifest(filePath, manifest) {
  fs.mkdirSync(path.dirname(filePath), { recursive: true });
  fs.writeFileSync(filePath, JSON.stringify(manifest, null, 2) + '\n');
}

// --- FTP ---

async function connectClient(client) {
  client.ftp.ipFamily = 4;
  await client.access({
    host,
    port,
    user,
    password,
    secure: true,
    secureOptions: { rejectUnauthorized: false, maxVersion: 'TLSv1.2' },
  });
  // Fix TLS session reuse for data connections (Node.js 17+).
  const controlSocket = client.ftp.socket;
  if (controlSocket instanceof tls.TLSSocket) {
    const session = controlSocket.getSession();
    if (session) {
      controlSocket.getSession = () => session;
    }
  }
}

async function connectWithRetry(client, { maxRetries = 5, label = 'Reconnecting' } = {}) {
  for (let attempt = 1; attempt <= maxRetries; attempt++) {
    try {
      await connectClient(client);
      return;
    } catch (err) {
      if (attempt === maxRetries) throw err;
      const delay = Math.min(5000 * 2 ** (attempt - 1), 60000);
      console.log(
        `${label}... attempt ${attempt}/${maxRetries} failed (${err.message}), retrying in ${delay / 1000}s`,
      );
      await new Promise((r) => setTimeout(r, delay));
    }
  }
}

async function uploadWithRetry(
  client,
  localPath,
  remotePath,
  { index, total, fileSize, displayName, suffix = '' },
) {
  const pad = String(total).length;
  const prefix = `    [${String(index).padStart(pad)}/${total}]`;
  const sizeStr = formatSize(fileSize);
  const showProgress = fileSize >= PROGRESS_THRESHOLD;

  for (let attempt = 1; attempt <= MAX_RETRIES; attempt++) {
    const retryTag = attempt > 1 ? ` retry ${attempt - 1}/${MAX_RETRIES - 1}` : '';

    if (showProgress) {
      if (retryTag) {
        process.stdout.write(`${prefix}  ${displayName} (${sizeStr}) ...${retryTag}\n`);
      }
      client.trackProgress((info) => {
        const pct = Math.round((info.bytes / fileSize) * 100);
        process.stdout.write(`\r${prefix}  ${displayName} (${sizeStr}) ... ${pct}%`);
      });
    }

    try {
      await client.uploadFrom(localPath, remotePath);
      client.trackProgress();
      if (showProgress) process.stdout.write('\r');
      console.log(`${formatTimestamp()}  ${prefix}  ${displayName} (${sizeStr}) \u2713${suffix}`);
      return;
    } catch (err) {
      client.trackProgress();
      if (showProgress) process.stdout.write('\n');
      if (attempt === MAX_RETRIES) throw err;
      console.log(
        `${prefix}  ${displayName} (${sizeStr}) ... retry ${attempt}/${MAX_RETRIES - 1} (${err.message})`,
      );
      client.close();
      await connectWithRetry(client);
    }
  }
}

/** Delete specific files from the remote server. Tolerates "file not found" errors. */
async function deleteRemoteFiles(client, remoteDir, files) {
  for (const rel of files) {
    const remotePath = path.posix.join(remoteDir, rel);
    try {
      await client.remove(remotePath);
    } catch {
      // File may already be gone — ignore
    }
  }
}

async function deployDir(client, localDir, remoteDir, force) {
  const localPath = path.resolve(localDir);

  if (!fs.existsSync(localPath)) {
    console.error(`Local directory does not exist: ${localPath}`);
    console.error('Did you run the build first?');
    process.exit(1);
  }

  const entries = fs.readdirSync(localPath);
  if (entries.length === 0 || (entries.length === 1 && entries[0] === '.gitkeep')) {
    console.error(`Local directory is empty: ${localPath}`);
    console.error('Did you run the build first?');
    process.exit(1);
  }

  console.log(`\n  ${localDir} → ${remoteDir}`);

  // Compute current manifest (hashes all files except .map)
  const currentManifest = await computeManifest(localPath);

  // Load previous manifest (or empty if first deploy / --force)
  const mPath = manifestPath(localDir);
  const previousManifest = force ? {} : loadManifest(mPath);

  // Diff manifests
  const { toUpload, toDelete } = diffManifests(previousManifest, currentManifest);

  // Collect .map files (always uploaded, excluded from manifest)
  const allFiles = walkDir(localPath);
  const mapFiles = allFiles.filter((f) => f.endsWith('.map'));

  if (toUpload.length === 0 && toDelete.length === 0 && mapFiles.length === 0) {
    console.log('    No changes detected — skipping.');
    return;
  }

  // Combine files to upload: changed/new files + .map files (deduplicated)
  const uploadSet = new Set([...toUpload, ...mapFiles]);
  const filesToUpload = [...uploadSet];

  // Summary
  if (toUpload.length > 0) console.log(`    ${toUpload.length} file(s) to upload`);
  if (mapFiles.length > 0) console.log(`    ${mapFiles.length} sourcemap file(s) to upload`);
  if (toDelete.length > 0) console.log(`    ${toDelete.length} file(s) to delete`);

  // Delete removed files from remote
  if (toDelete.length > 0) {
    await deleteRemoteFiles(client, remoteDir, toDelete);
    console.log(`    Deleted ${toDelete.length} remote file(s)`);
  }

  // Ensure remote base directory exists
  await client.ensureDir(remoteDir);
  await client.cd('/');

  // Upload new/changed files
  const dirs = new Set();
  const total = filesToUpload.length;
  const totalBytes = filesToUpload.reduce(
    (sum, rel) => sum + fs.statSync(path.join(localPath, rel)).size,
    0,
  );
  let bytesTransferredSoFar = 0;
  const transferStartTime = Date.now();

  for (let i = 0; i < total; i++) {
    const relFile = filesToUpload[i];
    // Ensure remote subdirectories exist
    const relDir = path.posix.dirname(relFile);
    if (relDir !== '.' && !dirs.has(relDir)) {
      await client.ensureDir(path.posix.join(remoteDir, relDir));
      await client.cd('/');
      dirs.add(relDir);
    }
    const localFile = path.join(localPath, relFile);
    const remoteFile = path.posix.join(remoteDir, relFile);
    const fileSize = fs.statSync(localFile).size;

    // Compute ETA suffix (suppress until 3 files done AND 3s elapsed; omit for last file)
    let suffix = '';
    const elapsed = Date.now() - transferStartTime;
    if (i >= 3 && elapsed >= 3000 && i < total - 1 && bytesTransferredSoFar > 0) {
      const bytesPerMs = bytesTransferredSoFar / elapsed;
      const remainingBytes = totalBytes - bytesTransferredSoFar;
      const etaMs = remainingBytes / bytesPerMs;
      suffix = `  ETA ${formatDuration(etaMs)}`;
    }

    await uploadWithRetry(client, localFile, remoteFile, {
      index: i + 1,
      total,
      fileSize,
      displayName: relFile,
      suffix,
    });
    bytesTransferredSoFar += fileSize;
  }

  const totalElapsed = Date.now() - transferStartTime;
  console.log(`    ${total}/${total} files uploaded in ${formatDuration(totalElapsed)}`);

  // Save manifest only after all uploads succeed (crash-safe)
  saveManifest(mPath, currentManifest);
}

// --- Main ---

const args = process.argv.slice(2);
const force = args.includes('--force');
const targetArgs = args.filter((a) => a !== '--force');

if (targetArgs.length === 0) {
  console.error('Usage: node --env-file=.env scripts/deploy.mjs [--force] <target> [target...]');
  console.error('  targets: plugins, bars2013, bars2026, config, all');
  console.error('  --force: Skip manifest comparison, upload everything');
  process.exit(1);
}

const host = requireEnv('FTP_HOST');
const user = requireEnv('FTP_USER');
const password = requireEnv('FTP_PASS');
const port = parseInt(process.env.FTP_PORT || '21', 10);
const remoteBase = process.env.FTP_REMOTE_BASE || '/2.0/wp-content/';

const targets = resolveTargets(targetArgs);

const client = new Client();

try {
  console.log(`Connecting to ${host}:${port}...`);
  await connectWithRetry(client, { label: 'Connecting' });
  console.log('Connected.');

  if (force) console.log('\n--force: uploading all files regardless of manifest.');
  console.log('\nDeploying:');

  for (const { local, remote, remoteBase: customBase } of targets) {
    const base = customBase || remoteBase;
    const fullRemote = remote ? path.posix.join(base, remote) : base;
    await deployDir(client, local, fullRemote, force);
  }

  console.log('\nDeploy complete.');
} catch (err) {
  console.error('\nDeploy failed:', err.message);
  process.exit(1);
} finally {
  client.close();
}
