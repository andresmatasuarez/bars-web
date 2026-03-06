# Deploy, Download & Remote Scripts

All remote operations use **SSH/rsync** via the `bars` host alias. See [server-access.md](server-access.md) for SSH key setup.

## Prerequisites

- SSH access configured (see [server-access.md](server-access.md))
- `rsync` installed locally (included by default on macOS and most Linux distros)

## Deploy

Deploys use `rsync` over SSH with content-checksum comparison (`--checksum`). Only changed files are transferred. Removed files are deleted from the remote (`--delete`).

### Commands

Build first, then deploy:

```sh
npm run build         # Build everything
npm run deploy        # Deploy everything (plugins + both themes + server config)
```

Or deploy individually:

```sh
npm run deploy:plugins    # All plugins
npm run deploy:bars2013   # bars2013 theme
npm run deploy:bars2026   # bars2026 theme
npm run deploy:config     # Server config (.htaccess, robots.txt, wp-config.php)
```

### Force deploy

Re-transfers all files regardless of checksum. Use when the remote was manually modified:

```sh
npm run deploy:bars2026 -- --force
```

### Dry-run

Preview what would be transferred without actually deploying:

```sh
npm run deploy:bars2026 -- --dry-run
```

### Remote path mapping

| Target | Local | Remote |
|--------|-------|--------|
| bars-commons | `wp-plugins/bars-commons/` | `<SITE_ROOT>/wp-content/plugins/bars-commons/` |
| jury-post-type | `wp-plugins/jury-post-type/` | `<SITE_ROOT>/wp-content/plugins/jury-post-type/` |
| movie-post-type | `wp-plugins/movie-post-type/` | `<SITE_ROOT>/wp-content/plugins/movie-post-type/` |
| bars2013 | `wp-themes/bars2013/` | `<SITE_ROOT>/wp-content/themes/bars2013/` |
| bars2026 | `wp-themes/bars2026/` | `<SITE_ROOT>/wp-content/themes/bars2026/` |
| config | `server-config/` | `<SITE_ROOT>/` |

Where `<SITE_ROOT>` = `/var/www/sitios/admin/quintadimension@quintadimension.com/quintadimension.com/subdominios/rojosangre/2.0`

## Download assets

Downloads the `wp-content/uploads/` directory from the remote server into `docker/wordpress/init-site/uploads/` for local development seeding.

```sh
npm run download:assets
```

Rsync skips unchanged files automatically (mtime + size comparison). Use `--force` to re-evaluate all files:

```sh
npm run download:assets -- --force
```

## OG cache clear

Clears the remote OG image cache (forces regeneration on next page visit):

```sh
npm run og:clear:remote
```

For local Docker:

```sh
npm run og:clear:local
```

## SSH_HOST override

All scripts default to the `bars` SSH alias (from `~/.ssh/config`). Override via the `SSH_HOST` environment variable in `.env`:

```
SSH_HOST=bars
```
