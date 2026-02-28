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
docker compose exec wordpress rm -rf /var/www/html/wp-content/uploads/og-cache/
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
- `server-config/` - Server configuration files deployed to the web root (`/2.0/`)
  - `.htaccess` - Apache config (HTTPS redirect, W3TC cache rules, WordPress rewrites)
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

The entrypoint (`docker/wordpress/init-entrypoint.sh`) runs the official WP entrypoint first, then installs WP core, the wordpress-importer plugin, and executes init scripts from `/docker-entrypoint-init.d/`.

Marker files in the `bars-web_bars-wordpress-data` volume (mounted at `/var/www/html/`) control initialization state (`.user_scripts_initialized`, `.import_done`). Rebuild the image with `docker compose build` after Dockerfile changes.

## Design-to-Code Conversion Rules

- **Text fidelity**: When converting a .pen design to code, use the **exact same text** as in the design. Do not paraphrase, summarize, or invent copy. The only exception is data that must be dynamically injected (festival dates, venue names/addresses, edition number, deadlines, etc.) — replace those with the appropriate `Editions::` helpers or PHP variables. If you are unsure whether a piece of text is static copy or dynamic data, ask the user.

## Deploy

Requires FTP credentials in `.env` (see `.env-example`). Uses `basic-ftp` package with **incremental deploys** — only new/changed files are uploaded based on SHA-256 content hashes.

```bash
npm run deploy            # Deploy everything (plugins + themes + server config)
npm run deploy:plugins    # Deploy all plugins
npm run deploy:bars2013   # Deploy bars2013 theme
npm run deploy:bars2026   # Deploy bars2026 theme
npm run deploy:config     # Deploy server config (.htaccess)
```

**Force full deploy** (skips manifest comparison, uploads everything):
```bash
node --env-file=.env scripts/deploy.mjs --force bars2026
```

Manifests are stored in `deploy/` (committed to VC). Sourcemap files (`.map`) are excluded from manifests and always re-uploaded.

Remote path mapping (handled automatically):
- `wp-plugins/{name}/` → `/2.0/wp-content/plugins/{name}`
- `wp-themes/bars2013/` → `/2.0/wp-content/themes/bars2013`
- `wp-themes/bars2026/` → `/2.0/wp-content/themes/bars2026`
- `server-config/` → `/2.0/` (root-level files like `.htaccess`)

## Command Delegation (MANDATORY)

_CRITICAL_: The following commands produce large outputs that consume excessive context window tokens. You MUST delegate these commands to a sub-agent using the Task tool. Never run these commands directly in the main conversation.

### Commands That MUST Be Delegated

| Command                | Description                    |
| ---------------------- | ------------------------------ |
| `npm run build`        | Build all themes and plugins   |
| `npm run build:bars2013` | Build bars2013 theme         |
| `npm run build:bars2026` | Build bars2026 theme         |
| `npm run lint:bars2013`  | Lint bars2013 theme          |
| `npm run lint:bars2026`  | Lint bars2026 theme          |
| `npm run test:bars2026`  | Run bars2026 test suite      |
| `npm run test:integration` | Run integration tests (Docker) |
| `npm test`             | Run all tests (unit + integration) |
| `npm run format`       | Run Prettier on all source files |

### How to Delegate

Use the Task tool with subagent_type: "Bash":

Task tool parameters:

- subagent_type: "Bash"
- description: "Run npm build" (brief 3-5 word description)
- prompt: See template below

### Sub-Agent Prompt Template

Run the following command and report the result:

Command: `npm run build`

Instructions:

1. Execute the command
2. Wait for completion
3. Report back:
   - If SUCCESS: Respond with "Command succeeded" and any relevant summary (e.g., "Built 42 packages")
   - If FAILURE: Respond with "Command failed" followed by the relevant error messages. Focus on actionable errors, not the full output.

[Optional context from calling agent, e.g.: "Pay attention to errors in packages/model or packages/react-components"]

### Why This Matters

Commands like `npm run build` and `npm run test:bars2026` may generate extensive output that would consume the main agent's context window, leaving less room for actual problem-solving. By delegating to a sub-agent:

1. The main agent preserves context for code analysis and implementation
2. The sub-agent uses a fresh context window dedicated to command execution
3. Only relevant results (success/failure + errors) return to the main conversation

### When in Doubt, Delegate

If you encounter a command not listed above but suspect it might produce large output (e.g., running multiple packages, full codebase operations, or commands with verbose flags), delegate it to a sub-agent. It is better to over-delegate than to flood the main context with unnecessary output.
