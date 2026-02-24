import { Client } from "basic-ftp";
import tls from "node:tls";
import path from "node:path";
import fs from "node:fs";

// --- Configuration ---

const REMOTE_DIR = "/2.0/wp-content/uploads";
const LOCAL_DIR = "docker/wordpress/init-site/uploads";

const MAX_RETRIES = 3;
const PROGRESS_THRESHOLD = 50 * 1024; // 50 KB — skip progress for smaller files

// --- Helpers ---

/** Format bytes as human-readable size (e.g. "2 KB", "1.2 MB", "2.7 GB"). */
function formatSize(bytes) {
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${Math.round(bytes / 1024)} KB`;
  if (bytes < 1024 * 1024 * 1024) return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
  return `${(bytes / (1024 * 1024 * 1024)).toFixed(1)} GB`;
}

/** Format current time as HH:MM:SS. */
function formatTimestamp() {
  const d = new Date();
  return `${String(d.getHours()).padStart(2, "0")}:${String(d.getMinutes()).padStart(2, "0")}:${String(d.getSeconds()).padStart(2, "0")}`;
}

/** Format milliseconds as human-readable duration (e.g. "2m 15s"). */
function formatDuration(ms) {
  const s = Math.round(ms / 1000);
  if (s < 1) return "<1s";
  if (s < 60) return `${s}s`;
  const m = Math.floor(s / 60);
  if (m < 60) return `${m}m ${s % 60}s`;
  return `${Math.floor(m / 60)}h ${m % 60}m`;
}

function requireEnv(name) {
  const value = process.env[name];
  if (!value) {
    console.error(`Missing required environment variable: ${name}`);
    console.error("Make sure your .env file contains FTP_HOST, FTP_USER, and FTP_PASS.");
    process.exit(1);
  }
  return value;
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
    secureOptions: { rejectUnauthorized: false, maxVersion: "TLSv1.2" },
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

async function connectWithRetry(client, { maxRetries = 5, label = "Reconnecting" } = {}) {
  for (let attempt = 1; attempt <= maxRetries; attempt++) {
    try {
      await connectClient(client);
      return;
    } catch (err) {
      if (attempt === maxRetries) throw err;
      const delay = Math.min(5000 * 2 ** (attempt - 1), 60000);
      console.log(`${label}... attempt ${attempt}/${maxRetries} failed (${err.message}), retrying in ${delay / 1000}s`);
      await new Promise((r) => setTimeout(r, delay));
    }
  }
}

async function listWithRetry(client, remotePath) {
  for (let attempt = 1; attempt <= MAX_RETRIES; attempt++) {
    try {
      return await client.list(remotePath);
    } catch (err) {
      if (attempt === MAX_RETRIES) throw err;
      console.log(`\nListing ${remotePath} failed (${err.message}), retry ${attempt}/${MAX_RETRIES - 1}`);
      client.close();
      await connectWithRetry(client);
    }
  }
}

/** Recursively list all files under a remote directory. Returns [{ path, size }]. */
async function listRemoteFilesRecursive(client, remotePath, prefix = "") {
  const results = [];
  const entries = await listWithRetry(client, remotePath);

  for (const entry of entries) {
    const rel = prefix ? `${prefix}/${entry.name}` : entry.name;
    if (entry.isDirectory) {
      const sub = await listRemoteFilesRecursive(
        client,
        path.posix.join(remotePath, entry.name),
        rel
      );
      results.push(...sub);
      // Show running count
      process.stdout.write(`\rScanning remote files... ${(results.length).toLocaleString()} found`);
    } else if (entry.isFile) {
      results.push({ path: rel, size: entry.size });
    }
  }

  return results;
}

async function downloadWithRetry(client, localFile, remoteFile, { index, total, fileSize, displayName, suffix = "" }) {
  const pad = String(total).length;
  const prefix = `  [${String(index).padStart(pad, "0")}/${total}]`;
  const sizeStr = formatSize(fileSize);
  const showProgress = fileSize >= PROGRESS_THRESHOLD;

  // Ensure local parent directory exists
  fs.mkdirSync(path.dirname(localFile), { recursive: true });

  for (let attempt = 1; attempt <= MAX_RETRIES; attempt++) {
    const retryTag = attempt > 1 ? ` retry ${attempt - 1}/${MAX_RETRIES - 1}` : "";

    if (showProgress) {
      if (retryTag) {
        process.stdout.write(`${prefix}  ${displayName} (${sizeStr}) ...${retryTag}\n`);
      }
      client.trackProgress((info) => {
        const pct = fileSize > 0 ? Math.round((info.bytes / fileSize) * 100) : 0;
        process.stdout.write(`\r${prefix}  ${displayName} (${sizeStr}) ... ${pct}%`);
      });
    }

    try {
      await client.downloadTo(localFile, remoteFile);
      client.trackProgress();
      if (showProgress) process.stdout.write("\r");
      console.log(`${formatTimestamp()}  ${prefix}  ${displayName} (${sizeStr}) \u2713${suffix}`);
      return;
    } catch (err) {
      client.trackProgress();
      if (showProgress) process.stdout.write("\n");
      // Clean up partial download
      try { fs.unlinkSync(localFile); } catch {}
      if (attempt === MAX_RETRIES) throw err;
      console.log(`${prefix}  ${displayName} (${sizeStr}) ... retry ${attempt}/${MAX_RETRIES - 1} (${err.message})`);
      client.close();
      await connectWithRetry(client);
    }
  }
}

// --- Main ---

const args = process.argv.slice(2);
const force = args.includes("--force");

const host = requireEnv("FTP_HOST");
const user = requireEnv("FTP_USER");
const password = requireEnv("FTP_PASS");
const port = parseInt(process.env.FTP_PORT || "21", 10);

const client = new Client();

try {
  console.log(`Connecting to ${host}:${port}...`);
  await connectWithRetry(client, { label: "Connecting" });
  console.log("Connected.\n");

  // 1. Scan remote files
  const remoteFiles = await listRemoteFilesRecursive(client, REMOTE_DIR);
  const totalRemoteSize = remoteFiles.reduce((sum, f) => sum + f.size, 0);
  process.stdout.write(`\rScanning remote files... ${remoteFiles.length.toLocaleString()} found (${formatSize(totalRemoteSize)})\n`);

  if (remoteFiles.length === 0) {
    console.log("\nNo remote files found.");
    process.exit(0);
  }

  // 2. Filter out files that already exist locally (unless --force)
  let toDownload;
  if (force) {
    toDownload = remoteFiles;
    console.log(`\n--force: downloading all ${remoteFiles.length.toLocaleString()} files.\n`);
  } else {
    const localBase = path.resolve(LOCAL_DIR);
    toDownload = remoteFiles.filter((f) => !fs.existsSync(path.join(localBase, f.path)));
    const alreadyLocal = remoteFiles.length - toDownload.length;

    if (toDownload.length === 0) {
      console.log(`\nAll ${remoteFiles.length.toLocaleString()} files already downloaded.`);
      process.exit(0);
    }

    console.log(
      `\n${remoteFiles.length.toLocaleString()} remote files, ` +
      `${alreadyLocal.toLocaleString()} already local \u2014 ` +
      `downloading ${toDownload.length.toLocaleString()} files.\n`
    );
  }

  // 3. Download missing files
  const total = toDownload.length;
  const totalBytes = toDownload.reduce((sum, f) => sum + f.size, 0);
  let downloadedBytes = 0;
  const transferStartTime = Date.now();

  for (let i = 0; i < total; i++) {
    const file = toDownload[i];
    const localFile = path.resolve(LOCAL_DIR, file.path);
    const remoteFile = path.posix.join(REMOTE_DIR, file.path);

    // Compute ETA suffix (suppress until 3 files done AND 3s elapsed; omit for last file)
    let suffix = "";
    const elapsed = Date.now() - transferStartTime;
    if (i >= 3 && elapsed >= 3000 && i < total - 1 && downloadedBytes > 0) {
      const bytesPerMs = downloadedBytes / elapsed;
      const remainingBytes = totalBytes - downloadedBytes;
      const etaMs = remainingBytes / bytesPerMs;
      suffix = `  ETA ${formatDuration(etaMs)}`;
    }

    await downloadWithRetry(client, localFile, remoteFile, {
      index: i + 1,
      total,
      fileSize: file.size,
      displayName: file.path,
      suffix,
    });

    downloadedBytes += file.size;
  }

  const totalElapsed = Date.now() - transferStartTime;
  console.log(`\nDownloaded ${total.toLocaleString()} files (${formatSize(downloadedBytes)}) in ${formatDuration(totalElapsed)}.`);
} catch (err) {
  console.error("\nDownload failed:", err.message);
  process.exit(1);
} finally {
  client.close();
}
