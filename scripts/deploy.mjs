import { execSync } from 'node:child_process';
import path from 'node:path';
import fs from 'node:fs';

import { findDeployProblems } from './deploy-checks.mjs';

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
  config: [
    {
      local: 'server-config',
      remote: `${SITE_ROOT}`,
      noDelete: true,
      excludes: ['wp-config-secrets.example.php'],
    },
  ],
};

// npm script that builds each target's local folder. The deploy runs it first, so the
// uploaded files always come from the current checkout. `config` has nothing to build.
const BUILD_SCRIPTS = {
  plugins: 'build:plugins',
  bars2013: 'build:bars2013',
  bars2026: 'build:bars2026',
};

const MAX_RETRIES = 2;
const RETRY_DELAY_MS = 5000;

// --- Helpers ---

function readJson(file) {
  return fs.existsSync(file) ? JSON.parse(fs.readFileSync(file, 'utf8')) : null;
}

function currentRepoState() {
  const git = (cmd) => execSync(`git ${cmd}`, { encoding: 'utf8' });
  return {
    branch: git('rev-parse --abbrev-ref HEAD').trim(),
    changedFiles: git('status --porcelain').split('\n').filter(Boolean),
    lockfile: readJson('package-lock.json'),
    installedLockfile: readJson('node_modules/.package-lock.json'),
  };
}

/** Stops the deploy unless the repository is in a state that is safe to deploy. */
function checkSafeToDeploy({ skipChecks }) {
  const problems = findDeployProblems(currentRepoState());
  if (problems.length === 0) return;

  const report = problems.map((problem) => `\n  - ${problem}`).join('');
  if (skipChecks) {
    console.warn(`\n--unsafe-skip-checks: deploying anyway despite:${report}\n`);
    return;
  }
  console.error(`\nNot deploying:${report}`);
  console.error('\n(Emergency only: --unsafe-skip-checks deploys anyway.)');
  process.exit(1);
}

function buildTargets(targetNames) {
  const names = targetNames.includes('all') ? Object.keys(DEPLOY_TARGETS) : targetNames;
  for (const name of names) {
    const script = BUILD_SCRIPTS[name];
    if (!script) continue;
    console.log(`\nBuilding ${name} (npm run ${script})...`);
    execSync(`npm run ${script}`, { stdio: 'inherit' });
  }
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

function rsync(localDir, remoteDir, { sshHost, force, dryRun, noDelete, excludes = [] }) {
  const rsyncArgs = [
    'rsync',
    '-rlptD',
    '--checksum',
    ...(noDelete ? [] : ['--delete']),
    '--info=progress2',
    "--exclude='.gitkeep'",
    ...excludes.map((e) => `--exclude='${e}'`),
    '-e',
    'ssh',
    ...(force ? ['--ignore-times'] : []),
    ...(dryRun ? ['--dry-run'] : []),
    `${localDir}/`,
    `${sshHost}:${remoteDir}/`,
  ];

  execSync(rsyncArgs.join(' '), { stdio: 'inherit' });
}

function deployTarget(localDir, remoteDir, { noDelete, excludes, ...options }) {
  validateLocalDir(localDir);

  console.log(`\n  ${localDir} → ${remoteDir}`);

  for (let attempt = 1; attempt <= MAX_RETRIES; attempt++) {
    try {
      rsync(localDir, remoteDir, { ...options, noDelete, excludes });
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
const skipChecks = args.includes('--unsafe-skip-checks');
const targetArgs = args.filter((a) => !a.startsWith('--'));

if (targetArgs.length === 0) {
  console.error(
    'Usage: node --env-file=.env scripts/deploy.mjs [--force] [--dry-run] [--unsafe-skip-checks] <target> [target...]',
  );
  console.error('  targets: plugins, bars2013, bars2026, config, all');
  console.error('  --force:              Re-transfer all files regardless of checksum');
  console.error('  --dry-run:            Preview what would be transferred');
  console.error(
    '  --unsafe-skip-checks: Deploy even when not on a clean master with the lockfile packages (emergency only)',
  );
  process.exit(1);
}

const sshHost = process.env.SSH_HOST || 'bars';
const targets = resolveTargets(targetArgs);

checkSafeToDeploy({ skipChecks });

try {
  buildTargets(targetArgs);

  if (force) console.log('--force: re-transferring all files.');
  if (dryRun) console.log('--dry-run: previewing changes only.');
  console.log('\nDeploying:');

  for (const { local, remote, noDelete, excludes } of targets) {
    deployTarget(local, remote, { sshHost, force, dryRun, noDelete, excludes });
  }

  console.log('\nDeploy complete.');
} catch (err) {
  console.error('\nDeploy failed:', err.message);
  process.exit(1);
}
