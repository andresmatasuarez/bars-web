# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Buenos Aires Rojo Sangre Film Festival website - a monorepo with npm workspaces containing a WordPress theme built using Vite, TypeScript, React, and LESS.

## Production Environment

As of February 2026, the live site runs **PHP 5.6.40** and **WordPress 3.9**. All PHP code must be compatible with both:

- **No PHP 7.0+ syntax**: no `??` (null coalescing), no scalar type hints (`string $param`), no return type declarations (`: string`), no `<=>`, no anonymous classes, no `??=`.
- **No WP 4.0+ functions** without a polyfill or `function_exists()` guard. Notable unavailable functions: `get_the_post_thumbnail_url()` (4.4), `wp_json_encode()` (4.1), `wp_body_open()` (5.2), `wp_get_attachment_image_url()` (4.4).
- **`get_template_part()` third `$args` parameter** was added in WP 5.5. To pass data to template parts, set `$GLOBALS['page_hero_args']` (or similar) before the call, and in the template part fall back to the global: `$_args = !empty($args) ? $args : (isset($GLOBALS['page_hero_args']) ? $GLOBALS['page_hero_args'] : array());`. See `page-hero.php` for the canonical example.
- `add_theme_support('title-tag')` (4.1) and `add_theme_support('custom-logo')` (4.5) are called but silently ignored on WP 3.9 — the theme includes manual fallbacks.

## Commands

```bash
# Development (starts Vite watch mode for all assets)
npm run dev:bars2013
# Or from theme directory:
cd themes/bars2013 && npm run dev

# Start WordPress + MySQL containers
docker compose -f docker-compose.yml up -d

# Production build
npm run build:bars2013

# Linting
npm run lint:bars2013
# Or with autofix from theme directory:
cd themes/bars2013 && npm run lint:autofix

# Type checking
cd themes/bars2013 && npm run typecheck

# Install PHP dependencies
cd themes/bars2013 && composer install

# Plugin development (copy .php/.js to wp-plugins/)
npm run dev:plugins

# Plugin production build
npm run build:plugins
```

Site runs at `http://localhost:8083` (default). Database at port 3307.

```bash
# Clear OG image cache (forces regeneration on next page visit)
npm run og:clear:local    # Local Docker
npm run og:clear:remote   # Remote (live) via SSH
```

## Code Formatting

**After editing or creating any file matched by `.prettierrc`** (TS, TSX, JS, JSON, CSS, etc.), run `npx prettier --write <file>` to ensure it matches the project's formatting config. Never leave files unformatted.

## Testing

Vitest with jsdom. Only `bars2026` has tests — `bars2013` has none. Config at `themes/bars2026/vitest.config.ts` collects from both `shared/ts/**/*.test.ts` and `themes/bars2026/ts/**/*.test.ts`.

```bash
npm run test:bars2026              # From root
cd themes/bars2026 && npm run test # From theme dir
```

### Integration tests (Docker)

HTTP-level tests for redirects and OG tags against Docker WordPress:

```bash
npm run test:integration  # From root (requires Docker running)
```

### All tests

```bash
npm test  # Runs unit tests (bars2026) + integration tests
```

Key conventions:
- Co-located test files: `{module}.test.ts` next to source
- Fixtures: `shared/ts/__fixtures__/movies.ts` (factory functions for Movie, Screening types)
- Mock window globals: `vi.stubGlobal()` / `vi.unstubAllGlobals()`
- Mock time: `vi.useFakeTimers()` / `vi.setSystemTime()` / `vi.useRealTimers()`
- React hooks: `@testing-library/react` (`renderHook`, `act`)

## Architecture

### Build System

Source files in `themes/{name}/assets/`, `themes/{name}/php/`, and `shared/` are processed by Vite and copied to `wp-themes/{name}/`. Plugin source files in `plugins/` are copied (`.php` and `.js` only) to `wp-plugins/` via `cpx`. **Never edit files in `wp-themes/` or `wp-plugins/` output directories directly** - they are overwritten on build.

Each theme has two Vite entry points (`themes/{name}/vite/vite.config.ts` and `themes/{name}/vite/selection.vite.config.ts`):

- **bars2013**: Main config compiles LESS styles and legacy JS into `bars.js` (IIFE format). Selection config builds the React selection app into `selection.js`.
- **bars2026**: Main config compiles Tailwind CSS and TypeScript. Selection config builds the React selection app into `selection.js`.

### Key Directories

- `themes/bars2013/` - Legacy WordPress theme (npm workspace)
  - `assets/` - Source files (LESS, TypeScript, React apps, fonts)
  - `assets/react-apps/selection/` - React app for movie selection/filtering
  - `php/` - Theme-specific WordPress PHP templates and functions
  - `vite/` - Vite configuration
  - `vendor/` - PHP Composer dependencies
- `themes/bars2026/` - Current WordPress theme (npm workspace)
  - `assets/` - Source files (Tailwind CSS, TypeScript, React apps, fonts)
  - `php/` - Theme-specific WordPress PHP templates and functions
  - `vite/` - Vite configuration
- `shared/` - Shared resources across themes
  - `editions.json` - **Single source of truth** for festival data (dates, venues, etc.)
  - `resources/` - Edition-specific assets (poster, programme, sponsors)
  - `raw/` - Original source files (high-res logos, etc.) for reference only — not part of the build
  - `php/` - Shared PHP utilities (editions.php, helpers.php)
- `server-config/` - Server configuration files (deployed to DocumentRoot)
  - `.htaccess` - Apache config (HTTPS redirect, backward-compat /2.0/ redirect, W3TC cache rules, WordPress rewrites)
  - `robots.txt` - Search engine crawling rules and sitemap reference
  - `wp-config.php` - WordPress configuration (version-controlled; loads secrets via `require`)
  - `wp-config-secrets.example.php` - Template for the server-only secrets file (not deployed)
  - Note: `wp-config-secrets.php` lives only on the production server (DB credentials + salts). See `docs/server-access.md`.
- `plugins/` - Custom WordPress plugins (source):
  - `movie-post-type/` - Movie custom post type with sections, screenings
  - `jury-post-type/` - Jury member custom post type
  - `bars-commons/` - Shared functionality
- `wp-plugins/` - Plugin build output (`.php` + `.js` only) — DO NOT EDIT
- `docker/wordpress/init-site/` - Docker initialization scripts and seed data
- `scripts/` - Project scripts
  - `switch-theme.sh` - CLI tool to switch WordPress themes

### Adding a New Theme

Output paths are configured in multiple places that must stay in sync:

1. `themes/{name}/package.json` - `config.dest` and `clean` script
2. `themes/{name}/vite/*.config.ts` - `build.outDir` in each Vite config
3. `docker-compose.yml` - volume mount for the theme
4. `.gitignore` - ignore pattern for `wp-themes/{name}/**/*`

### Adding a New Plugin

1. Create the plugin directory under `plugins/{name}/`
2. Add `wp-plugins/{name}/**/*` and `!wp-plugins/{name}/.gitkeep` to `.gitignore`
3. Add `"wp-plugins/{name}/*"` to the `clean:plugins` script in root `package.json`
4. Create `wp-plugins/{name}/.gitkeep`
5. Add a volume mount in `docker-compose.yml` for `./wp-plugins/{name}`

### Path Aliases

TypeScript and Vite configs use `@shared/*` alias to reference `shared/` directory (e.g., `import EDITIONS from '@shared/editions.json'`).

### Festival Data (`editions.json`)

`shared/editions.json` is the **single source of truth** for all festival information (edition dates, venues, deadlines, etc.).

- **TypeScript/JS files**: Import directly via `import EDITIONS from '@shared/editions.json'`
- **PHP files**: Use `shared/php/editions.php` which loads and parses `editions.json` behind the scenes, exposing helper functions like `Editions::venues()`, `Editions::from()`, `Editions::to()`, etc.
- **When you need festival data in PHP**, always check the `Editions` class first for an existing helper. If none exists, add a new static method to `shared/php/editions.php` following the existing patterns in that file. Do not create theme-local wrapper functions around `Editions`.
- **Edition colors**: each edition can have `colors` (two hex values picked by hand from its poster). bars2026 uses the current edition's colors for links, dates, the active menu line and a glow at the top of pages. To pick them for a new edition, use the `edition-colors` skill (`.claude/skills/edition-colors/`).

### Sponsor Logos

Sponsor logos live in `shared/resources/{edition}/sponsors/` and `editions.json` points to them (`sponsors[].items[].logo`). They are PNGs with a transparent background in two formats:

- Up to 2025: 140×80.
- From 2026 on: 280×160 (double size, sharper on high-resolution screens). These file names end in `@2x`, for example `auspiciante_bars@2x.png`, so scripts can find them with `*@2x.png`.

The sponsor section shows both formats at the same height, so they can be mixed in one edition.

### Shared Template Parts (bars2026)

- **Page Hero**: All inner pages (everything except the landing) must use `get_template_part('template-parts/sections/page', 'hero', array('title' => '...', 'subtitle' => '...'))` for their heading section. Never inline a custom hero — this ensures consistent height and styling across all pages.

### SEO (bars2026)

Custom SEO implementation in `themes/bars2026/php/seo.php` — no plugin. Outputs meta descriptions, Open Graph/Twitter Card tags, canonical URLs, and JSON-LD structured data (Organization, Event, NewsArticle, Movie schemas) via `wp_head` hooks. Also customizes `robots.txt` to include WordPress's native sitemap URL.

### Data Flow for Selection App

WordPress DB → PHP queries movies/screenings → JSON embedded in `selection.php` → React app consumes and renders with styled-components

### Screening Types

Raw screening strings are stored in `_movie_screenings` post meta (comma-separated), parsed by `shared/php/helpers.php:parseScreening()`, and typed in `shared/ts/selection/types.ts`.

| Type | `streaming` | `alwaysAvailable` | `isoDate` | `time` | Raw format |
|---|---|---|---|---|---|
| Traditional | `false`/absent | N/A | present | present | `venue.room:mm-dd-yyyy hh:mm` |
| Streaming (always available) | `true` | `true` | `null` | absent | `streaming!venue:full` |
| Streaming (day-specific) | `true` | `false`/absent | present | absent | `streaming!venue:mm-dd-yyyy` |

Key rules: streaming screenings never have `time`, commas separate multiple screenings, date format is `m-d-Y` (month-day-year), timezone is `America/Argentina/Buenos_Aires`.

### Temporary Files (`.qa/`)

All temporary artifacts — screenshots, test HTML/JS files, markdown reports, shell scripts, or any other throwaway file created during visual QA, debugging, or testing — go in the `.qa/` directory at the project root. This folder is gitignored. **Never** create temporary files in the project root, inside theme directories, or anywhere else in the source tree.

### Docker Setup

Uses a custom image built from `docker/wordpress/Dockerfile` (extends `wordpress:apache` with WP-CLI and a custom entrypoint). Single-phase init: `docker compose build && docker compose up -d` handles everything automatically.

The entrypoint (`docker/wordpress/init-entrypoint.sh`) runs the official WP entrypoint first, then installs WP core, activates the theme and plugins, and executes init scripts from `/docker-entrypoint-init.d/`.

Marker files in the `bars-web_bars-wordpress-data` volume (mounted at `/var/www/html/`) control initialization state (`.user_scripts_initialized`, `.import_done`). Rebuild the image with `docker compose build` after Dockerfile changes.

## Design-to-Code Conversion Rules

- **Text fidelity**: When converting a .pen design to code, use the **exact same text** as in the design. Do not paraphrase, summarize, or invent copy. The only exception is data that must be dynamically injected (festival dates, venue names/addresses, edition number, deadlines, etc.) — replace those with the appropriate `Editions::` helpers or PHP variables. If you are unsure whether a piece of text is static copy or dynamic data, ask the user.

## Deploy

Uses `rsync` over SSH for incremental deploys. Requires SSH access configured (`ssh bars`) — see `docs/server-access.md`.

```bash
npm run deploy            # Deploy everything (plugins + themes + server config)
npm run deploy:plugins    # Deploy all plugins
npm run deploy:bars2013   # Deploy bars2013 theme
npm run deploy:bars2026   # Deploy bars2026 theme
npm run deploy:config     # Deploy server config (.htaccess)
```

**Force full deploy** (re-transfers all files regardless of checksum):
```bash
npm run deploy:bars2026 -- --force
```

**Dry-run** (preview what would be transferred):
```bash
npm run deploy:bars2026 -- --dry-run
```

Remote path mapping (rsync over SSH, absolute paths on server):
- `wp-plugins/{name}/` → `<SITE_ROOT>/wp-content/plugins/{name}`
- `wp-themes/bars2013/` → `<SITE_ROOT>/wp-content/themes/bars2013`
- `wp-themes/bars2026/` → `<SITE_ROOT>/wp-content/themes/bars2026`
- `server-config/` → `<SITE_ROOT>/`

## Server Access (SSH)

The production server is accessible via `ssh bars` (configured in the developer's `~/.ssh/config`). See `docs/server-access.md` for setup instructions.

**CRITICAL — READ-ONLY BY DEFAULT**: You (Claude), any sub-agent, and any MCP tool MUST NOT modify, create, delete, or overwrite any file or configuration on the remote server without explicit user approval first. Always ask before running any command that changes server state (e.g., `rm`, `mv`, `cp`, `sed -i`, `apt`, `systemctl`, writing to files, restarting services). Read-only commands (`ls`, `cat`, `grep`, `tail`, `df`, `du`, `ps`, etc.) are fine without asking.
