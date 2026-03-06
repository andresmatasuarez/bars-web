import { execSync } from 'node:child_process';

const sshHost = process.env.SSH_HOST || 'bars';
const siteRoot =
  '/var/www/sitios/admin/quintadimension@quintadimension.com/quintadimension.com/subdominios/rojosangre/2.0';
const remoteUploads = `${siteRoot}/wp-content/uploads/`;
const defaultLocalUploads = 'docker/wordpress/init-site/uploads/';

const args = process.argv.slice(2);
const force = args.includes('--force');
const outputIdx = args.indexOf('--output');
let localUploads = outputIdx !== -1 && args[outputIdx + 1] ? args[outputIdx + 1] : defaultLocalUploads;
if (!localUploads.endsWith('/')) localUploads += '/';

function formatElapsed(ms) {
  const totalSeconds = Math.round(ms / 1000);
  const minutes = Math.floor(totalSeconds / 60);
  const seconds = totalSeconds % 60;
  return minutes > 0 ? `${minutes}m ${seconds}s` : `${seconds}s`;
}

const rsyncArgs = [
  'rsync',
  '-a',
  '--info=progress2',
  '-e',
  'ssh',
  ...(force ? ['--ignore-times'] : []),
  `${sshHost}:${remoteUploads}`,
  localUploads,
];

console.log(`Syncing uploads from ${sshHost} to ${localUploads}...`);
if (force) console.log('--force: re-evaluating all files.');

const startTime = Date.now();

try {
  execSync(rsyncArgs.join(' '), { stdio: 'inherit' });
  console.log(`Download complete in ${formatElapsed(Date.now() - startTime)}.`);
} catch (err) {
  console.error(`Download failed after ${formatElapsed(Date.now() - startTime)}: ${err.message}`);
  process.exit(1);
}
