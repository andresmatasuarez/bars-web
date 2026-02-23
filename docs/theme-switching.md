# Theme Switching Guide

This document covers everything needed to switch between `bars2013` and `bars2026` on both the local Docker environment and the live production server.

## Table of Contents

- [Theme Comparison](#theme-comparison)
- [How It Works](#how-it-works)
- [Local (Docker)](#local-docker)
- [One-Time Production Setup](#one-time-production-setup)
- [Production: Switch TO bars2026](#production-switch-to-bars2026)
- [Production: Switch BACK TO bars2013](#production-switch-back-to-bars2013)

---

## Theme Comparison

### Pages

Each theme owns a set of WordPress pages. Three slugs overlap between themes and require special handling during switching.

| bars2026         | Slug           | bars2013          | Slug                | Overlapping? |
| ---------------- | -------------- | ----------------- | ------------------- | ------------ |
| Home             | `home`         | -                 | -                   | No           |
| Noticias         | `noticias`     | -                 | -                   | No           |
| El festival      | `festival`     | -                 | -                   | No           |
| Premios          | `premios`      | -                 | -                   | No           |
| -                | -              | Novedades         | `novedades`         | No           |
| -                | -              | BARS              | `bars`              | No           |
| -                | -              | #RojoSangreTV     | `rojosangretv`      | No           |
| -                | -              | Premios y jurados | `premios-y-jurados` | No           |
| -                | -              | Auspiciantes      | `auspiciantes`      | No           |
| -                | -              | Contacto          | `contacto`          | No           |
| **Programacion** | `programacion` | **Programacion**  | `programacion`      | **Yes**      |
| **Convocatoria** | `convocatoria` | **Convocatoria**  | `convocatoria`      | **Yes**      |
| **Prensa**       | `prensa`       | **Prensa**        | `prensa`            | **Yes**      |

**Page templates** (set via `_wp_page_template` post meta):

| Theme    | Page              | Template                         |
| -------- | ----------------- | -------------------------------- |
| bars2026 | Home              | _(none - uses `front-page.php`)_ |
| bars2026 | Noticias          | `page-news.php`                  |
| bars2026 | El festival       | `page-about.php`                 |
| bars2026 | Programacion      | `page-selection.php`             |
| bars2026 | Premios           | `page-awards.php`                |
| bars2026 | Convocatoria      | `page-call.php`                  |
| bars2026 | Prensa            | `page-press.php`                 |
| bars2013 | Novedades         | `news.php`                       |
| bars2013 | BARS              | `festival.php`                   |
| bars2013 | #RojoSangreTV     | `rojo_sangre_tv.php`             |
| bars2013 | Programacion      | `selection.php`                  |
| bars2013 | Premios y jurados | `juries.php`                     |
| bars2013 | Convocatoria      | `call.php`                       |
| bars2013 | Auspiciantes      | `sponsors.php`                   |
| bars2013 | Prensa            | `press.php`                      |
| bars2013 | Contacto          | `contact.php`                    |

### Reading Settings (Front Page)

| Setting         | bars2026             | bars2013               |
| --------------- | -------------------- | ---------------------- |
| `show_on_front` | `page` (static page) | `posts` (latest posts) |
| `page_on_front` | ID of "Home" page    | `0`                    |

bars2026 uses `front-page.php` which WordPress only loads when Reading settings are set to "A static page". bars2013 uses `index.php` (latest posts mode).

### Registered Menus (reference)

| Theme    | Menus                                        |
| -------- | -------------------------------------------- |
| bars2026 | `primary`, `footer-festival`, `footer-legal` |
| bars2013 | `primary`                                    |

Both themes hardcode their navigation, so no menu assignment is needed during switching. Listed here for reference only.

### Registered Image Sizes (reference)

All image sizes are registered centrally in `plugins/bars-commons/bars-commons.php` (`barscommons_register_image_sizes`), not in each theme's `functions.php`. This ensures WordPress generates all crops on every upload regardless of which theme is active, eliminating the need to regenerate thumbnails after switching themes.

Shared/plugin code that needs the active theme's size uses `get_template() . '-size-name'` to resolve dynamically.

| Theme    | Size Name                        | Dimensions | Hard Crop |
| -------- | -------------------------------- | ---------- | --------- |
| bars2026 | `bars2026-news-featured`         | 800x450    | Yes       |
| bars2026 | `bars2026-news-card`             | 400x225    | Yes       |
| bars2026 | `bars2026-movie-post-thumbnail`  | 400x225    | Yes       |
| bars2026 | `bars2026-sponsor-logo`          | 120x60     | No        |
| bars2026 | `bars2026-jury-post-thumbnail`   | 300x300    | Yes       |
| bars2013 | `bars2013-movie-post-thumbnail`  | 160x81     | Yes       |
| bars2013 | `bars2013-movie-post-image`      | 220x129    | Yes       |
| bars2013 | `bars2013-movieblock-post-image` | 110x65     | Yes       |
| bars2013 | `bars2013-jury-post-thumbnail`   | 180x180    | Yes       |

### Required Plugins (same for both)

- `bars-commons`
- `movie-post-type`
- `jury-post-type`

These are theme-independent and should always be active.

### Permalink Structure (same for both)

`/%postname%/` - no changes needed when switching.

---

## How It Works

Both themes' pages coexist in WordPress at all times. Each page has custom meta fields that control ownership and slug management:

| Meta Key      | Purpose                                    | Example Value                            |
| ------------- | ------------------------------------------ | ---------------------------------------- |
| `_bars_theme` | Which theme owns this page                 | `bars2026` or `bars2013`                 |
| `_bars_slug`  | Canonical slug (only on overlapping pages) | `programacion`, `convocatoria`, `prensa` |

When switching to a theme, we:

1. **Activate the theme** in WordPress
2. **Draft the other theme's pages** and free overlapping slugs by renaming them to `{slug}-off-{theme}` (e.g., `programacion-off-bars2013`)
3. **Publish the target theme's pages** and restore clean slugs (e.g., `programacion`)
4. **Update Reading settings** (`show_on_front` and `page_on_front`)
5. **Flush permalinks** so WordPress rewrites are rebuilt

---

## Local (Docker)

Use the script:

```sh
./scripts/switch-theme.sh bars2026   # Switch to bars2026
./scripts/switch-theme.sh bars2013   # Switch to bars2013
./scripts/switch-theme.sh            # Show current theme
```

The script performs all 5 steps automatically via WP-CLI inside the Docker container. See `scripts/switch-theme.sh` for the full implementation.

---

## Production: Switch TO bars2026

> **Table prefix**: The queries below use `wp_` as the default table prefix. If your WordPress install uses a different prefix, replace `wp_` accordingly.

### Step 1: Activate the theme

**wp-admin > Appearance > Themes** > Find `bars2026` > Click **Activate**.

This must be done via wp-admin (not SQL) so WordPress's `switch_theme` hooks fire, clearing caches and updating internal state.

> _What the script does: `wp theme activate bars2026`_

### Step 2: Draft bars2013 pages and free overlapping slugs

Drafts all 9 bars2013 pages. The 3 overlapping pages (`programacion`, `convocatoria`, `prensa`) get their slugs renamed to `{slug}-off-bars2013` to free the clean slug for bars2026. Run both queries in order:

```sql
-- 2a. Free overlapping slugs (only renames pages currently holding the clean slug)
UPDATE wp_posts p
INNER JOIN wp_postmeta pm_theme ON p.ID = pm_theme.post_id AND pm_theme.meta_key = '_bars_theme' AND pm_theme.meta_value = 'bars2013'
INNER JOIN wp_postmeta pm_slug  ON p.ID = pm_slug.post_id  AND pm_slug.meta_key = '_bars_slug'
SET p.post_name = CONCAT(pm_slug.meta_value, '-off-bars2013')
WHERE p.post_type = 'page'
  AND p.post_name = pm_slug.meta_value;

-- 2b. Draft all bars2013 pages
UPDATE wp_posts p
INNER JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_bars_theme' AND pm.meta_value = 'bars2013'
SET p.post_status = 'draft'
WHERE p.post_type = 'page';
```

> _What the script does: Loops through all pages with `_bars_theme` meta, drafts non-target pages, and renames overlapping slugs to `{slug}-off-{owner}` (only when the page currently holds the clean slug)._

### Step 3: Publish bars2026 pages and restore clean slugs

Publishes all 7 bars2026 pages. The 3 overlapping pages get their clean slugs restored from `_bars_slug` meta. Run both queries in order:

```sql
-- 3a. Restore clean slugs for overlapping pages
UPDATE wp_posts p
INNER JOIN wp_postmeta pm_theme ON p.ID = pm_theme.post_id AND pm_theme.meta_key = '_bars_theme' AND pm_theme.meta_value = 'bars2026'
INNER JOIN wp_postmeta pm_slug  ON p.ID = pm_slug.post_id  AND pm_slug.meta_key = '_bars_slug'
SET p.post_name = pm_slug.meta_value
WHERE p.post_type = 'page';

-- 3b. Publish all bars2026 pages
UPDATE wp_posts p
INNER JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_bars_theme' AND pm.meta_value = 'bars2026'
SET p.post_status = 'publish'
WHERE p.post_type = 'page';
```

> _What the script does: Finds all pages where `_bars_theme = bars2026`, restores canonical slugs from `_bars_slug` meta, and publishes them._

### Step 4: Update Reading settings

Sets bars2026's static front page by looking up the "Home" page ID dynamically:

```sql
UPDATE wp_options SET option_value = 'page' WHERE option_name = 'show_on_front';
UPDATE wp_options SET option_value = (
  SELECT p.ID FROM wp_posts p
  INNER JOIN wp_postmeta pm ON p.ID = pm.post_id
  WHERE pm.meta_key = '_bars_theme' AND pm.meta_value = 'bars2026'
    AND p.post_type = 'page' AND p.post_name = 'home'
  LIMIT 1
) WHERE option_name = 'page_on_front';
```

> _What the script does: `wp option update show_on_front page` and `wp option update page_on_front <home-page-id>`._

### Step 5: Flush rewrite rules

Deleting the stored rewrite rules forces WordPress to regenerate them on the next page load:

```sql
DELETE FROM wp_options WHERE option_name = 'rewrite_rules';
```

> _What the script does: `wp rewrite flush`._

### Step 6: Verify

Visit the site to confirm the switch worked. WordPress regenerates rewrite rules automatically on the first request after deletion.

Optionally, run this query to audit page states:

```sql
SELECT p.ID, p.post_title, p.post_name, p.post_status, pm.meta_value AS theme
FROM wp_posts p
INNER JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_bars_theme'
WHERE p.post_type = 'page'
ORDER BY pm.meta_value, p.menu_order;
```

---

## Production: Switch BACK TO bars2013

> **Table prefix**: The queries below use `wp_` as the default table prefix. If your WordPress install uses a different prefix, replace `wp_` accordingly.

### Step 1: Activate the theme

**wp-admin > Appearance > Themes** > Find `bars2013` > Click **Activate**.

This must be done via wp-admin (not SQL) so WordPress's `switch_theme` hooks fire, clearing caches and updating internal state.

> _What the script does: `wp theme activate bars2013`_

### Step 2: Draft bars2026 pages and free overlapping slugs

Drafts all 7 bars2026 pages. The 3 overlapping pages get their slugs renamed to `{slug}-off-bars2026`. Run both queries in order:

```sql
-- 2a. Free overlapping slugs (only renames pages currently holding the clean slug)
UPDATE wp_posts p
INNER JOIN wp_postmeta pm_theme ON p.ID = pm_theme.post_id AND pm_theme.meta_key = '_bars_theme' AND pm_theme.meta_value = 'bars2026'
INNER JOIN wp_postmeta pm_slug  ON p.ID = pm_slug.post_id  AND pm_slug.meta_key = '_bars_slug'
SET p.post_name = CONCAT(pm_slug.meta_value, '-off-bars2026')
WHERE p.post_type = 'page'
  AND p.post_name = pm_slug.meta_value;

-- 2b. Draft all bars2026 pages
UPDATE wp_posts p
INNER JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_bars_theme' AND pm.meta_value = 'bars2026'
SET p.post_status = 'draft'
WHERE p.post_type = 'page';
```

> _What the script does: Loops through all pages with `_bars_theme` meta, drafts non-target pages, and renames overlapping slugs to `{slug}-off-{owner}` (only when the page currently holds the clean slug)._

### Step 3: Publish bars2013 pages and restore clean slugs

Publishes all 9 bars2013 pages. The 3 overlapping pages get their clean slugs restored. Run both queries in order:

```sql
-- 3a. Restore clean slugs for overlapping pages
UPDATE wp_posts p
INNER JOIN wp_postmeta pm_theme ON p.ID = pm_theme.post_id AND pm_theme.meta_key = '_bars_theme' AND pm_theme.meta_value = 'bars2013'
INNER JOIN wp_postmeta pm_slug  ON p.ID = pm_slug.post_id  AND pm_slug.meta_key = '_bars_slug'
SET p.post_name = pm_slug.meta_value
WHERE p.post_type = 'page';

-- 3b. Publish all bars2013 pages
UPDATE wp_posts p
INNER JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_bars_theme' AND pm.meta_value = 'bars2013'
SET p.post_status = 'publish'
WHERE p.post_type = 'page';
```

> _What the script does: Finds all pages where `_bars_theme = bars2013`, restores canonical slugs from `_bars_slug` meta, and publishes them._

### Step 4: Update Reading settings

bars2013 uses "latest posts" mode — no front page needed:

```sql
UPDATE wp_options SET option_value = 'posts' WHERE option_name = 'show_on_front';
UPDATE wp_options SET option_value = '0' WHERE option_name = 'page_on_front';
```

> _What the script does: `wp option update show_on_front posts` and `wp option update page_on_front 0`._

### Step 5: Flush rewrite rules

```sql
DELETE FROM wp_options WHERE option_name = 'rewrite_rules';
```

> _What the script does: `wp rewrite flush`._

### Step 6: Verify

Visit the site to confirm the switch worked. WordPress regenerates rewrite rules automatically on the first request.

Optionally, audit page states:

```sql
SELECT p.ID, p.post_title, p.post_name, p.post_status, pm.meta_value AS theme
FROM wp_posts p
INNER JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_bars_theme'
WHERE p.post_type = 'page'
ORDER BY pm.meta_value, p.menu_order;
```
