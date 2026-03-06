import { execSync } from 'node:child_process';
import path from 'node:path';
import fs from 'node:fs';

// --- Configuration ---

const SITE_ROOT =
  '/var/www/sitios/admin/quintadimension@quintadimension.com/quintadimension.com/subdominios/rojosangre/2.0';

const DEPLOY_TARGETS = {
  plugins: [
    {
      local: 'wp-plugins/bars-commons',
      remote: `${SITE_ROOT}/wp-content/plugins/bars-commons`,
    },
    {
      local: 'wp-plugins/jury-post-type',
      remote: `${SITE_ROOT}/wp-content/plugins/jury-post-type`,
    },
    {
      local: 'wp-plugins/movie-post-type',
      remote: `${SITE_ROOT}/wp-content/plugins/movie-post-type`,
    },
  ],
  bars2013: [
    {
      local: 'wp-themes/bars2013',
      remote: `${SITE_ROOT}/wp-content/themes/bars2013`,
    },
  ],
  bars2026: [
    {
      local: 'wp-themes/bars2026',
      remote: `${SITE_ROOT}/wp-content/themes/bars2026`,
    },
  ],
  config: [{ local: 'server-config', remote: `${SITE_ROOT}`, noDelete: true }],
};

const MAX_RETRIES = 2;
const RETRY_DELAY_MS = 5000;

// --- Helpers ---

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

function validateLocalDir(localDir) {
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
}

function rsync(localDir, remoteDir, { sshHost, force, dryRun, noDelete }) {
  const rsyncArgs = [
    'rsync',
    '-rlptD',
    '--checksum',
    ...(noDelete ? [] : ['--delete']),
    '--info=progress2',
    "--exclude='.gitkeep'",
    '-e',
    'ssh',
    ...(force ? ['--ignore-times'] : []),
    ...(dryRun ? ['--dry-run'] : []),
    `${localDir}/`,
    `${sshHost}:${remoteDir}/`,
  ];

  execSync(rsyncArgs.join(' '), { stdio: 'inherit' });
}

function deployTarget(localDir, remoteDir, { noDelete, ...options }) {
  validateLocalDir(localDir);

  console.log(`\n  ${localDir} → ${remoteDir}`);

  for (let attempt = 1; attempt <= MAX_RETRIES; attempt++) {
    try {
      rsync(localDir, remoteDir, { ...options, noDelete });
      return;
    } catch (err) {
      if (attempt === MAX_RETRIES) throw err;
      console.log(`  Retry ${attempt}/${MAX_RETRIES - 1} after ${RETRY_DELAY_MS / 1000}s...`);
      execSync(`sleep ${RETRY_DELAY_MS / 1000}`);
    }
  }
}

// --- Main ---

const args = process.argv.slice(2);
const force = args.includes('--force');
const dryRun = args.includes('--dry-run');
const targetArgs = args.filter((a) => !a.startsWith('--'));

if (targetArgs.length === 0) {
  console.error(
    'Usage: node --env-file=.env scripts/deploy.mjs [--force] [--dry-run] <target> [target...]',
  );
  console.error('  targets: plugins, bars2013, bars2026, config, all');
  console.error('  --force:   Re-transfer all files regardless of checksum');
  console.error('  --dry-run: Preview what would be transferred');
  process.exit(1);
}

const sshHost = process.env.SSH_HOST || 'bars';
const targets = resolveTargets(targetArgs);

try {
  if (force) console.log('--force: re-transferring all files.');
  if (dryRun) console.log('--dry-run: previewing changes only.');
  console.log('\nDeploying:');

  for (const { local, remote, noDelete } of targets) {
    deployTarget(local, remote, { sshHost, force, dryRun, noDelete });
  }

  console.log('\nDeploy complete.');
} catch (err) {
  console.error('\nDeploy failed:', err.message);
  process.exit(1);
}
