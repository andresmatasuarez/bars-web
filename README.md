# bars-web

Buenos Aires Rojo Sangre Film Festival website - a monorepo with npm workspaces.

### Themes

Each theme has its own README with tech stack details, directory structure, and build info:

- [bars2013](themes/bars2013/README.md) - Legacy theme (2013-2025)
- [bars2026](themes/bars2026/README.md) - Current theme (2026+)

## Initial setup

1. `nvm install`
2. `npm install` (from project root - installs all workspace deps)
3. `cd themes/bars2013 && composer install` (PHP dependencies for bars2013 only)
4. Create `.env` by duplicating `.env-example` and adjusting as needed:
   ```sh
   cp .env-example .env
   ```
   The defaults work out of the box for local development (WordPress at port 8083, MySQL at port 3307). Edit `.env` if you need to change ports or credentials.

### Seed data

Initial setup consists of getting hold of existing data from the live site and seeding it into the new local installation.

#### 1. Exporting data

1. Head over to https://rojosangre.quintadimension.com/2.0/wordpress/wp-admin/ and login with admin credentials.
1. In the left-side menu, go to Tools > Export.
1. Perform an export selecting **"All content"**.
1. Rename the downloaded XML file to "backup.xml" and place it inside `<project-root>/docker/wordpress/init-site`.

#### 2. Downloading assets

We still need to download the images and files associated with the data we just exported to XML. These assets can be found in the BARS FTP server, in the remote directory `/2.0/wp-content/uploads` and must be downloaded into local folder `<project-root>/docker/wordpress/init-site/uploads`.

As of February 2026, there're over 20k assets (~2.9 GB) so downloading will probably be a very long process ¯\\\_(ツ)\_/¯.

```sh
npm run download:assets
```

This uses the same FTP credentials from `.env` as the deploy script. It's **incremental** — files that already exist locally are skipped. Re-run to resume after an interruption. Use `--force` to re-download everything:

```sh
node --env-file=.env scripts/download-assets.mjs --force
```

Alternatively, you can use any FTP client such as [Filezilla](https://filezilla-project.org/) to download `/2.0/wp-content/uploads` into `docker/wordpress/init-site/uploads`.

### Docker setup

Build the custom WordPress image and start all services:

```sh
docker compose build
docker compose up -d
```

On the first run, the container automatically installs WordPress, activates the theme and plugins, and imports seed data (from `docker/wordpress/init-site/`). Subsequent runs skip all of this (guarded by marker files in the volume).

The `uploads-server` service (an nginx container) serves the local uploads over Docker's internal network, so `wp import` downloads attachments locally instead of from the remote server. This brings import time down from ~3.5 hours to ~1.5 hours. After a successful import, you can comment out the `uploads-server` service — it's only needed during the initial seed.

For a completely fresh start (wipes all data):

```sh
docker compose down -v
docker compose build
docker compose up -d
```

##### Switching themes

Use `./scripts/switch-theme.sh` to switch the active WordPress theme in the Docker container (e.g. between `bars2013` and `bars2026`). For production switching (via wp-admin, no WP-CLI), see [docs/theme-switching.md](docs/theme-switching.md).

##### Marker files

Our custom entrypoint and init scripts rely on marker files to detect whether initialization has already been done.

These marker files are stored in the `bars-web_bars-wordpress-data` volume and you can play around with their existence to perform again or completely skip steps in this initialization process.

1. Run `docker volume inspect bars-web_bars-wordpress-data` and take note of the value of `Mountpoint`. It's a path to the volume folder in the host system.
1. Then, depending on your needs, you may run one or more of the following:

   ```sh
   # Tells the container to re-run initial scripts.
   sudo rm <mountpoint>/.user_scripts_initialized

   # Tells the container to do no importing.
   sudo touch <mountpoint>/.import_done
   ```

1. Now, you can run:
   ```sh
   docker compose up -d
   ```

## Project Structure

```
bars-web/
├─ themes/
│  ├─ bars2013/              # Legacy theme — see themes/bars2013/README.md
│  └─ bars2026/              # Current theme — see themes/bars2026/README.md
├─ shared/
│  ├─ editions.json          # Single source of truth for festival data
│  ├─ resources/             # Edition-specific assets (poster, programme, sponsors)
│  └─ php/                   # Shared PHP utilities (editions.php, helpers.php)
├─ wp-themes/
│  ├─ bars2013/              # Build output — DO NOT EDIT
│  └─ bars2026/              # Build output — DO NOT EDIT
├─ plugins/                  # Plugin source
│  ├─ bars-commons/
│  ├─ jury-post-type/
│  └─ movie-post-type/
├─ wp-plugins/               # Build output — DO NOT EDIT
│  ├─ bars-commons/
│  ├─ jury-post-type/
│  └─ movie-post-type/
├─ tests/
│  └─ integration/            # HTTP-level tests against Docker WordPress
├─ docs/                     # Documentation (theme-switching.md)
├─ docker/
│  └─ wordpress/             # Dockerfile, entrypoint, init-site/
├─ server-config/
│  ├─ wp/                    # Deployed to /2.0/ (WordPress .htaccess)
│  └─ root/                  # Deployed to / (web root: robots.txt, redirect .htaccess)
├─ scripts/                  # switch-theme.sh, deploy.mjs
├─ deploy/                  # Deploy manifests (content hashes) — committed to VC
├─ package.json              # Workspace root
└─ tsconfig.base.json
```

The screening data model (traditional, streaming always-available, streaming day-specific) is defined in `shared/ts/selection/types.ts`. See the [movie-post-type plugin README](plugins/movie-post-type/README.md#screening-format) for the raw format specification.

## Production Environment

As of February 2026, the live site runs:

- **PHP**: 5.6.40-1~dotdeb+7.1
- **WordPress**: 3.9

All theme and plugin PHP code must be compatible with these versions. See `CLAUDE.md` for specific syntax and function constraints.

## Development

### Run local version

Running a local version of the site involves two different processes:

1. Listen for changes and output theme files:

   ```
   npm run dev:bars2013
   # or
   npm run dev:bars2026
   ```

   Or from the theme directory:

   ```
   cd themes/bars2026 && npm run dev
   ```

2. Watch for plugin changes and copy to output:

   ```
   npm run dev:plugins
   ```

3. Start the wordpress/mysql services:

   ```
   docker compose up -d
   ```

   Assuming we're using the default values in `.env-example`:
   - Site will be available at `http://localhost:8083`
   - You can connect to the database from your host machine via:

   ```sh
   mysql -h127.0.0.1 -u root -P3307 -p barsweb_docker
   ```

### Caching gotchas

- Some plugins cache data in WordPress transients (e.g. festival metrics are cached for 7 days). When `WP_DEBUG` is `true` (default in Docker), these caches are bypassed and changes take effect immediately on refresh. The `WORDPRESS_DEBUG` env var in `docker-compose.yml` is read at runtime, so changes take effect on restart.

- **OG images** (sharing cards for movies/movieblocks) are cached to `wp-content/uploads/og-cache/`. They regenerate automatically when a post is saved or the festival edition changes. To force regeneration:

   ```sh
   npm run og:clear:local    # Clear local Docker og-cache
   npm run og:clear:remote   # Clear remote (live) og-cache via FTP
   ```

   After clearing, visit any movie page — the OG image is generated on the first request. To view the generated image directly, open `http://localhost:8083/wp-content/uploads/og-cache/movie-{ID}.jpg` in your browser, or inspect the `og:image` meta tag on the movie page for the full URL.

   When changing the OG image generation logic (`themes/bars2026/php/og-image.php`), bump `BARS_OG_VERSION` in that file, clear both caches, and deploy. The version is appended as `?v=N` to the `og:image` URL, forcing social platforms to re-fetch.

### Available Scripts

From the root:

- `npm run dev:bars2013` / `npm run dev:bars2026` - Start theme development mode
- `npm run build:bars2013` / `npm run build:bars2026` - Build theme for production
- `npm run lint:bars2013` / `npm run lint:bars2026` - Run ESLint
- `npm run test:bars2026` - Run test suite (shared + bars2026)
- `npm test` - Run all tests (unit + integration)
- `npm run test:integration` - Run integration tests (requires Docker)
- `npm run dev:plugins` - Watch and copy plugin files to `wp-plugins/`
- `npm run build:plugins` - Build plugins for production
- `npm run og:clear:local` - Clear local Docker OG image cache
- `npm run og:clear:remote` - Clear remote (live) OG image cache via FTP

From each theme directory (`themes/bars2013` or `themes/bars2026`):

- `npm run dev` - Start development mode
- `npm run build` - Build for production
- `npm run lint` - Run ESLint
- `npm run lint:autofix` - Run ESLint with auto-fix
- `npm run typecheck` - Run TypeScript type checking

From `themes/bars2026` only:

- `npm run test` - Run tests
- `npm run test:watch` - Run tests in watch mode
- `npm run test:coverage` - Run tests with coverage

## Testing

### Unit tests

[Vitest](https://vitest.dev/) with jsdom environment. Only the `bars2026` workspace has test infrastructure — `bars2013` has none.

### Running tests

```bash
# From project root
npm run test:bars2026

# From theme directory
cd themes/bars2026
npm run test              # Single run
npm run test:watch        # Watch mode
npm run test:coverage     # With coverage report
```

### Configuration

| File | Purpose |
|---|---|
| `themes/bars2026/vitest.config.ts` | Vitest config — collects tests from `shared/ts/**/*.test.ts` and `themes/bars2026/ts/**/*.test.ts` |
| `themes/bars2026/test-setup.ts` | Global setup — loads `@testing-library/jest-dom` matchers, clears `localStorage` after each test |

Vitest globals (`describe`, `it`, `expect`, `vi`) are available without imports — configured via `globals: true` in vitest config and `vitest/globals` in tsconfig types. The `@shared/*` path alias works in tests the same as in source.

### Conventions

- **Co-location**: Test files live next to their source as `{module}.test.ts`
- **Fixtures**: `shared/ts/__fixtures__/movies.ts` provides factory functions (`createMovie()`, `createTraditionalScreening()`, `createAlwaysAvailableStreaming()`, `createRegularStreaming()`, `createScreeningWithMovie()`, `createMoviesForDay()`)
- **Window globals**: Mock with `vi.stubGlobal('MOVIES', [...])`, clean up with `vi.unstubAllGlobals()` in `afterEach`
- **Time-dependent tests**: `vi.useFakeTimers()` + `vi.setSystemTime(...)` in `beforeEach`, `vi.useRealTimers()` in `afterEach`
- **React hooks**: Test via `@testing-library/react` (`renderHook`, `act`)

### Integration tests

HTTP-level tests that run against the Docker WordPress instance. See [tests/integration/README.md](tests/integration/README.md).

## Deploy

Requires FTP credentials in `.env` (see `.env-example` for the variables).

### How it works

Deploys are **incremental**. The script computes a SHA-256 content hash for every file in the build output, compares it against the previous deploy's manifest, and only uploads new or changed files. Files removed from the build are surgically deleted from the remote — no more wiping the entire directory.

### Commands

Build first, then deploy:

```sh
npm run build         # Build everything
npm run deploy        # Deploy everything (plugins + both themes)
```

Or deploy individually:

```sh
npm run deploy:plugins    # All plugins
npm run deploy:bars2013   # bars2013 theme
npm run deploy:bars2026   # bars2026 theme
npm run deploy:config     # Server config (web root + WordPress .htaccess)
```

### Force deploy

Skips manifest comparison and uploads everything. Use when the remote was manually modified or manifests are out of sync:

```sh
node --env-file=.env scripts/deploy.mjs --force bars2026
```

### Sourcemaps

`.map` files are excluded from manifests and always re-uploaded. They contain absolute paths that differ across machines, so hash comparison would be misleading.

### Crash safety

The manifest is saved **only after all uploads succeed**. If a deploy is interrupted, the next run sees the old manifest and retries all the changes.

### Manifests

Stored in `deploy/` (committed to VC). One JSON file per target, with the filename derived from the local path (`/` → `--`), e.g. `wp-themes--bars2026.manifest.json`.

### Edge cases

| Scenario                        | What happens                                                                                                   |
| ------------------------------- | -------------------------------------------------------------------------------------------------------------- |
| First deploy (no manifest)      | All files uploaded, manifest created                                                                           |
| Interrupted deploy              | Old manifest retained; next run re-uploads everything that changed                                             |
| File deleted from build         | Deleted from remote, removed from manifest                                                                     |
| Remote manually modified        | Run with `--force` to re-sync everything                                                                       |
| Rebuild with no source changes  | Hashes match — "No changes detected, skipping"                                                                 |
| Deploy from a different machine | Hashed content is the same, so only truly different files upload (except `.map` files, which always re-upload) |
