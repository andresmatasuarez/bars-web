# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Buenos Aires Rojo Sangre Film Festival website - a monorepo with npm workspaces containing a WordPress theme built using Vite, TypeScript, React, and LESS.

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
```

Site runs at `http://localhost:8083` (default). Database at port 3307.

## Architecture

### Build System

Source files in `themes/bars2013/assets/`, `themes/bars2013/php/`, and `shared/` are processed by Vite and copied to `wp-themes/{theme-name}/` (e.g., `wp-themes/bars2013/`). **Never edit files in `wp-themes/` output directories directly** - they are overwritten on build.

Two Vite entry points:
- **Main theme** (`themes/bars2013/vite/vite.config.ts`): Compiles LESS styles and legacy JS into `bars.js` (IIFE format)
- **Selection app** (`themes/bars2013/vite/selection.vite.config.ts`): React app for movie programming/search, outputs `selection.js`

### Key Directories

- `themes/bars2013/` - Main WordPress theme (npm workspace)
  - `assets/` - Source files (LESS, TypeScript, React apps, fonts)
  - `assets/react-apps/selection/` - React app for movie selection/filtering
  - `php/` - Theme-specific WordPress PHP templates and functions
  - `vite/` - Vite configuration
  - `vendor/` - PHP Composer dependencies
- `shared/` - Shared resources across themes
  - `editions.json` - **Single source of truth** for festival data (dates, venues, etc.)
  - `resources/` - Edition-specific assets (poster, programme, sponsors)
  - `php/` - Shared PHP utilities (editions.php, helpers.php)
- `wp-plugins/` - Custom WordPress plugins:
  - `movie-post-type/` - Movie custom post type with sections, screenings
  - `jury-post-type/` - Jury member custom post type
  - `bars-commons/` - Shared functionality
- `scripts/` - Project scripts
  - `init-site/` - Docker initialization scripts and seed data
  - `switch-theme.sh` - CLI tool to switch WordPress themes

### Adding a New Theme

Output paths are configured in multiple places that must stay in sync:
1. `themes/{name}/package.json` - `config.dest` and `clean` script
2. `themes/{name}/vite/*.config.ts` - `build.outDir` in each Vite config
3. `docker-compose.yml` - volume mount for the theme
4. `.gitignore` - ignore pattern for `wp-themes/{name}/**/*`

### Path Aliases

TypeScript and Vite configs use `@shared/*` alias to reference `shared/` directory (e.g., `import EDITIONS from '@shared/editions.json'`).

### Festival Data (`editions.json`)

`shared/editions.json` is the **single source of truth** for all festival information (edition dates, venues, deadlines, etc.).

- **TypeScript/JS files**: Import directly via `import EDITIONS from '@shared/editions.json'`
- **PHP files**: Use `shared/php/editions.php` which loads and parses `editions.json` behind the scenes, exposing helper functions like `Editions::venues()`, `Editions::from()`, `Editions::to()`, etc.
- **When you need festival data in PHP**, always check the `Editions` class first for an existing helper. If none exists, add a new static method to `shared/php/editions.php` following the existing patterns in that file. Do not create theme-local wrapper functions around `Editions`.

### Shared Template Parts (bars2026)

- **Page Hero**: All inner pages (everything except the landing) must use `get_template_part('template-parts/sections/page', 'hero', array('title' => '...', 'subtitle' => '...'))` for their heading section. Never inline a custom hero — this ensures consistent height and styling across all pages.

### Data Flow for Selection App

WordPress DB → PHP queries movies/screenings → JSON embedded in `selection.php` → React app consumes and renders with styled-components

### Temporary Files (`.qa/`)

All temporary artifacts — screenshots, test HTML/JS files, markdown reports, shell scripts, or any other throwaway file created during visual QA, debugging, or testing — go in the `.qa/` directory at the project root. This folder is gitignored. **Never** create temporary files in the project root, inside theme directories, or anywhere else in the source tree.

### Docker Setup

Two-phase initialization required for fresh setup:
1. **First run**: Comment out theme/plugin volumes in docker-compose.yml, start containers, wait for WP install, stop
2. **Second run**: Uncomment volumes, start containers - init scripts run once to activate theme, plugins, and import seed data

Marker files in `bars-web_bars-wordpress-data` volume control initialization state (`.user_scripts_initialized`, `.import_done`).

## Design-to-Code Conversion Rules

- **Text fidelity**: When converting a .pen design to code, use the **exact same text** as in the design. Do not paraphrase, summarize, or invent copy. The only exception is data that must be dynamically injected (festival dates, venue names/addresses, edition number, deadlines, etc.) — replace those with the appropriate `Editions::` helpers or PHP variables. If you are unsure whether a piece of text is static copy or dynamic data, ask the user.

## Deploy

Upload via FTP:
- `wp-plugins/` → `/2.0/wp-content/plugins`
- `wp-themes/bars2013/` → `/2.0/wp-content/themes/bars2013`
