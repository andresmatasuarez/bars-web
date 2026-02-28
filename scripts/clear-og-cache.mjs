import { Client } from 'basic-ftp';
import tls from 'node:tls';

function requireEnv(name) {
  const value = process.env[name];
  if (!value) {
    console.error(`Missing required environment variable: ${name}`);
    console.error('Make sure your .env file contains FTP_HOST, FTP_USER, and FTP_PASS.');
    process.exit(1);
  }
  return value;
}

const host = requireEnv('FTP_HOST');
const user = requireEnv('FTP_USER');
const password = requireEnv('FTP_PASS');
const port = parseInt(process.env.FTP_PORT || '21', 10);
const remoteBase = process.env.FTP_REMOTE_BASE || '/2.0/wp-content/';

const ogCacheDir = remoteBase + 'uploads/og-cache/';

const client = new Client();

try {
  console.log(`Connecting to ${host}:${port}...`);
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
  console.log('Connected.');

  console.log(`Removing ${ogCacheDir}...`);
  await client.removeDir(ogCacheDir);
  console.log('OG cache cleared.');
} catch (err) {
  if (err.code === 550 || (err.message && err.message.includes('550'))) {
    console.log('OG cache directory does not exist — already clean.');
  } else {
    console.error('Failed to clear OG cache:', err.message);
    process.exit(1);
  }
} finally {
  client.close();
}
