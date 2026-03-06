import { execSync } from 'node:child_process';

const sshHost = process.env.SSH_HOST || 'bars';
const siteRoot =
  '/var/www/sitios/admin/quintadimension@quintadimension.com/quintadimension.com/subdominios/rojosangre/2.0';
const defaultOutput = 'docker/wordpress/init-site/backup.xml';

const args = process.argv.slice(2);
const outputIdx = args.indexOf('--output');
const output = outputIdx !== -1 && args[outputIdx + 1] ? args[outputIdx + 1] : defaultOutput;

function formatElapsed(ms) {
  const totalSeconds = Math.round(ms / 1000);
  const minutes = Math.floor(totalSeconds / 60);
  const seconds = totalSeconds % 60;
  return minutes > 0 ? `${minutes}m ${seconds}s` : `${seconds}s`;
}

console.log(`Exporting site backup from ${sshHost} to ${output}...`);

const startTime = Date.now();

// Use PHP's export_wp() directly instead of WP-CLI's `wp export`, because
// WP-CLI omits <wp:attachment_url> tags from the XML. export_wp() outputs
// the full WordPress eXtended RSS including attachment URLs.
// Pipe PHP via stdin to avoid shell quoting issues with nested quotes.
const phpCode = `<?php
$_SERVER['SERVER_NAME'] = 'www.festivalrojosangre.com.ar';
$_SERVER['HTTP_HOST'] = 'www.festivalrojosangre.com.ar';
define('ABSPATH', '${siteRoot}/wordpress/');
define('WPINC', 'wp-includes');
require_once(ABSPATH . 'wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/export.php');
export_wp();
`;

try {
  execSync(`ssh ${sshHost} 'php -d error_reporting=0' > '${output}'`, {
    input: phpCode,
    stdio: ['pipe', 'inherit', 'inherit'],
  });
  console.log(`Export complete in ${formatElapsed(Date.now() - startTime)}.`);
} catch (err) {
  console.error(`Export failed after ${formatElapsed(Date.now() - startTime)}: ${err.message}`);
  process.exit(1);
}
