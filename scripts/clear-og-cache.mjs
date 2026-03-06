import { execSync } from 'node:child_process';

const sshHost = process.env.SSH_HOST || 'bars';
const siteRoot =
  '/var/www/sitios/admin/quintadimension@quintadimension.com/quintadimension.com/subdominios/rojosangre/2.0';
const ogCachePath = `${siteRoot}/wp-content/uploads/og-cache`;

console.log('Clearing remote OG cache...');
execSync(`ssh ${sshHost} 'rm -rf ${ogCachePath}'`, { stdio: 'inherit' });
console.log('OG cache cleared.');
