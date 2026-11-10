# Deploy, Download & Remote Scripts

All remote operations use **SSH/rsync** via the `bars` host alias. See [server-access.md](server-access.md) for SSH key setup.

## Prerequisites

- SSH access configured (see [server-access.md](server-access.md))
- `rsync` installed locally (included by default on macOS and most Linux distros)

## Deploy

Deploys use `rsync` over SSH with content-checksum comparison (`--checksum`). Only changed files are transferred. Removed files are deleted from the remote (`--delete`).

### Commands

Each deploy checks the repository, builds what it uploads, then uploads it (see [Safety checks](#safety-checks)). You do not need to run `npm run build` first.

```sh
npm run deploy        # Deploy everything (plugins + both themes + server config)
```

Or deploy individually:

```sh
npm run deploy:plugins    # All plugins
npm run deploy:bars2013   # bars2013 theme
npm run deploy:bars2026   # bars2026 theme
npm run deploy:config     # Server config (.htaccess, robots.txt, wp-config.php)
```

### Safety checks

A deploy uploads whatever the local build folders (`wp-themes/`, `wp-plugins/`) hold, whichever branch built them. Installed packages also stay the same when you switch branches, so building `master`'s code with another branch's packages can produce a broken site (for example, a newer Vite bundled two copies of React and the programme page crashed).

So before uploading, `scripts/deploy.mjs` checks that:

1. You are on `master`. Merge your work first, then `git switch master`.
2. `git status` is clean: no uncommitted changes and no untracked files. Commit or stash them first.
3. The installed packages match `package-lock.json`. It compares npm's record of what is installed (`node_modules/.package-lock.json`) with the lockfile. Run `npm ci` to fix it, for example after switching branches.

If any check fails, the deploy stops before contacting the server and lists every problem, for example:

```
Not deploying:
  - You are on branch "my-feature". Deploys go out from master: merge your work, then run `git switch master`.
  - The installed packages do not match package-lock.json. Run `npm ci` first:
    node_modules/vite: installed 8.3.7, lockfile 4.5.14
```

When the checks pass, the deploy builds each theme or plugin target itself (`npm run build:bars2026`, `build:bars2013`, `build:plugins`) and then uploads, so the upload always matches the current commit. `deploy:config` has nothing to build.

The checks live in `scripts/deploy-checks.mjs` and are tested by `npm run test:scripts` (part of `npm test`).

In an emergency, `--unsafe-skip-checks` deploys even when the checks fail. It prints the problems and goes on, and it still builds first:

```sh
npm run deploy:bars2026 -- --unsafe-skip-checks
```

### Force deploy

Re-transfers all files regardless of checksum. Use when the remote was manually modified:

```sh
npm run deploy:bars2026 -- --force
```

### Dry-run

Preview what would be transferred without actually deploying. The safety checks and the build still run; only the upload is skipped:

```sh
npm run deploy:bars2026 -- --dry-run
```

To see file by file what would change on the server (useful to spot files that were edited by hand there), run rsync yourself with `-i` after a build:

```sh
rsync -rlptD --checksum --delete -i --dry-run --exclude='.gitkeep' -e ssh \
  wp-themes/bars2026/ bars:<SITE_ROOT>/wp-content/themes/bars2026/
```

Lines starting with `<f` and containing `c` or `s` are files whose content would change; `*deleting` lines are files that would be removed from the server.

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
