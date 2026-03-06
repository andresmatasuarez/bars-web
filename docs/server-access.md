# Server Access (SSH)

The production server (`quintadimension.com`) runs **OpenSSH 6.0 on Debian 7**. This guide covers SSH key setup for direct server access.

## SSH Key Setup

### 1. Generate an RSA key

The server does **not** support Ed25519 keys. Use RSA 4096:

```sh
ssh-keygen -t rsa -b 4096 -C "your-email@example.com" -f ~/.ssh/bars_rsa
```

When prompted, set a passphrase (4-5 random words is ideal).

### 2. Copy the public key to the server

```sh
ssh-copy-id -i ~/.ssh/bars_rsa.pub user@quintadimension.com
```

Replace `user` with your server username. You'll need the server password for this one-time step.

### 3. Configure `~/.ssh/config`

Add this block to your `~/.ssh/config`:

```
Host bars
    HostName quintadimension.com
    User your-username
    IdentityFile ~/.ssh/bars_rsa
    IdentitiesOnly yes
    PubkeyAcceptedAlgorithms +ssh-rsa
```

- **`IdentitiesOnly yes`** — prevents the SSH agent from offering other keys, which can cause auth failures if you have many keys loaded.
- **`PubkeyAcceptedAlgorithms +ssh-rsa`** — required because the server only supports the legacy `ssh-rsa` signature algorithm (see [Known Quirks](#known-quirks) below).

### 4. Test the connection

```sh
ssh bars
```

You should be prompted for your key's passphrase (not the server password).

## Known Quirks

The server runs **OpenSSH 6.0**, which predates support for:

- **Ed25519 keys** (added in OpenSSH 6.5)
- **rsa-sha2-256 / rsa-sha2-512** signature algorithms (added in OpenSSH 7.2)

Modern SSH clients (OpenSSH 8.8+) disabled the legacy `ssh-rsa` (SHA-1) algorithm by default. Without `PubkeyAcceptedAlgorithms +ssh-rsa` in your config, the client and server can't agree on a signature algorithm, and pubkey auth silently falls back to password auth.

**Symptoms if misconfigured**: SSH asks for your _server password_ instead of your _key passphrase_, even though the key is in `authorized_keys`.

## Revoking Access

To remove someone's SSH access, delete their public key from the server's `authorized_keys` file:

```sh
ssh bars
nano ~/.ssh/authorized_keys
# Find and delete the line containing their key (identified by the email comment at the end)
```

Or, to remove a specific key non-interactively:

```sh
ssh bars "grep -v 'their-email@example.com' ~/.ssh/authorized_keys > ~/.ssh/authorized_keys.tmp && mv ~/.ssh/authorized_keys.tmp ~/.ssh/authorized_keys"
```

## Server Prerequisites

### wp-config-secrets.php (one-time setup)

`wp-config.php` is version-controlled and deployed via rsync. It loads database credentials and salts from a separate `wp-config-secrets.php` file that lives only on the server.

To set it up:

1. SSH into the server and navigate to the site root:

```sh
ssh bars
cd /var/www/sitios/admin/quintadimension@quintadimension.com/quintadimension.com/subdominios/rojosangre/2.0
```

2. Create `wp-config-secrets.php` with the actual DB credentials and salts. Use `server-config/wp-config-secrets.example.php` as a template for the required constants.

3. Set ownership so Apache can read it:

```sh
chown www-data:www-data wp-config-secrets.php
```

This file is never deployed or overwritten — it exists only on the production server.

## Used by deploy scripts

The deploy, download, and OG cache scripts use this SSH configuration for all remote operations.
See [deploy.md](deploy.md) for full documentation on deploy commands and workflows.
See [server-migration.md](server-migration.md) for the full analysis of upgrading PHP/WP and migrating to a new server.
