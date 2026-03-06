# Domain Migration: festivalrojosangre.com.ar

**Date:** March 2026
**Purpose:** Migrate the BARS website from `rojosangre.quintadimension.com` to `www.festivalrojosangre.com.ar`, and remove the legacy `/2.0/` path prefix.

---

## Background

The BARS website originally lived at `https://rojosangre.quintadimension.com/2.0/` (a subdomain of the hosting provider's domain). The `/2.0/` path was a legacy artifact — WordPress was installed in a subdirectory because the web root contained old festival files from 2002–2011.

**Phase 1 (March 2026):** Domain migration to `www.festivalrojosangre.com.ar`.
**Phase 2 (March 2026):** Removed `/2.0/` by changing the DocumentRoot to point to the `/2.0/` directory directly.

The canonical URL is now `https://www.festivalrojosangre.com.ar/`.

### Architecture

The server uses **gnupanel**, a hosting panel that manages Apache virtual hosts:

```
gnupanel webapp → PostgreSQL database → Perl daemon (every ~6h) → Apache vhost files
```

**Critical:** Manual edits to Apache vhost files are overwritten by the daemon. All configuration changes must go through the `gnupanel_apacheconf` PostgreSQL table.

The daemon reads rows from `gnupanel_apacheconf` and generates vhost files at:
- `/var/lib/gnupanel/etc/apache2/sites-enabled/`
- `/var/lib/gnupanel/etc/apache2/ssl/sites-enabled/`

WordPress uses `$_SERVER['SERVER_NAME']` dynamically for `WP_HOME`/`WP_SITEURL`/`WP_CONTENT_URL` in `wp-config.php`. The path components were updated to remove `/2.0` when the DocumentRoot changed.

---

## Current Configuration

### Database Rows (`gnupanel_apacheconf`)

| id_apache | Domain | Role | State |
|---|---|---|---|
| 14 | `www.festivalrojosangre.com.ar` | **Serves the site** (primary) | `redirigir=0`, documentroot points to `rojosangre/2.0` |
| 41 | `www.festivalrojosangre.com.ar` | **Serves the site** (duplicate entry) | Same as id 14 |
| 40 | `festivalrojosangre.com.ar` | **Redirects** to `https://www.festivalrojosangre.com.ar/` | `redirigir=1` |
| 15 | `www.www.festivalrojosangre.com.ar` | **Redirects** to `https://www.festivalrojosangre.com.ar/` | `redirigir=1` (gnupanel artifact) |
| 13 | `rojosangre.quintadimension.com` | **Redirects** to `https://www.festivalrojosangre.com.ar/` | `redirigir=1` |
| 16 | `www.rojosangre.quintadimension.com` | **Redirects** to `https://www.festivalrojosangre.com.ar/` | `redirigir=1` |

### DocumentRoot

The DocumentRoot now points to the WordPress installation directory:
```
/var/www/sitios/admin/quintadimension@quintadimension.com/quintadimension.com/subdominios/rojosangre/2.0
```

The parent directory (`rojosangre/`) still contains the old legacy files, but Apache no longer serves from there.

### wp-config.php

> **Note:** `wp-config.php` is now version-controlled at `server-config/wp-config.php`. The snippet below reflects the state at migration time — see the version-controlled file for current values.

```php
define('WP_SITEURL', 'https://' . $_SERVER['SERVER_NAME'] . '/wordpress');
define('WP_HOME', 'https://' . $_SERVER['SERVER_NAME']);
define('WP_CONTENT_URL', 'https://' . $_SERVER['SERVER_NAME'] . '/wp-content');
```

---

## How to Verify Everything Is Working

Run these curl commands from any machine:

```bash
# 1. Main site — should return 200
curl -I https://www.festivalrojosangre.com.ar/

# 2. Bare domain → www redirect
curl -I https://festivalrojosangre.com.ar/

# 3. HTTP → HTTPS redirect
curl -I http://www.festivalrojosangre.com.ar/

# 4. Old /2.0/ URLs redirect to root (301)
curl -I https://www.festivalrojosangre.com.ar/2.0/
curl -I https://www.festivalrojosangre.com.ar/2.0/peliculas/

# 5. Old domain → new domain redirect
curl -I https://rojosangre.quintadimension.com/
curl -I https://rojosangre.quintadimension.com/2.0/peliculas/

# 6. WP admin accessible
curl -I https://www.festivalrojosangre.com.ar/wordpress/wp-admin/
```

Expected results:
- #1: `200 OK`
- #2, #3, #5: `302 Found` with `Location:` header pointing to correct destination
- #4: `301 Moved Permanently` to the path without `/2.0/`
- #6: `200 OK` or `302` redirect to login page

---

## Post-Migration SEO Checklist

After completing the server migration, these steps help search engines and social platforms update to the new URLs faster. They are optional — the 301 redirects and correct canonical tags will cause re-indexing over time — but they accelerate the process.

### Why no code changes are needed

All URL generation is dynamic:
- **PHP** (OG tags, canonical, sitemap, JSON-LD): uses `home_url()`, `get_permalink()`, `wp_upload_dir()`
- **TypeScript** (list sharing, movie/jury modal sharing): uses `window.location`

Since `WP_HOME` was updated to remove `/2.0`, all WordPress-generated URLs are already correct.

### Google Search Console

1. If the property is registered as `https://www.festivalrojosangre.com.ar/2.0/`, add a new property for `https://www.festivalrojosangre.com.ar/`
2. Submit the updated sitemap URL: `https://www.festivalrojosangre.com.ar/wp-sitemap.xml`
3. Use the URL Inspection tool to request re-indexing of key pages (homepage, /seleccion/, popular movie pages)
4. Monitor the "Coverage" report — old `/2.0/` URLs should show as "Page with redirect"

### Social Platform Cache Clearing

Social platforms cache OG metadata aggressively. After migration, shared links may still show old `/2.0/` URLs in previews until the cache expires or is manually flushed.

- **Facebook**: Paste key URLs into the [Sharing Debugger](https://developers.facebook.com/tools/debug/) and click "Scrape Again"
- **Twitter/X**: Paste URLs into the [Card Validator](https://cards-dev.twitter.com/validator) to refresh card data
- **WhatsApp / Telegram**: These cache by URL and don't offer a manual flush tool. The site supports an invalidation query param (`?_v=N`) for forcing re-fetch — see commit `cfa55af`

Alternatively, use the OG cache clear scripts to regenerate OG images if needed:
```sh
npm run og:clear:local    # Local Docker
npm run og:clear:remote   # Remote (live) via SSH
```

### What's already handled automatically

- **Canonical URLs**: `seo.php` outputs `<link rel="canonical">` and `og:url` via `home_url()` — already pointing to root
- **Sitemap**: WordPress generates `wp-sitemap.xml` with correct URLs; `robots.txt` points to it
- **301 redirects**: `.htaccess` redirects `/2.0/*` → `/*` (permanent) — search engines follow and update their index
- **Structured data**: JSON-LD schemas use `home_url()` for all URL properties

---

## How to Fully Restore to Pre-Migration State

This reverts ALL domains to their original configuration (pre-domain-migration, pre-DocumentRoot change):

```bash
su postgres -c "psql -d gnupanel -c \"
-- Restore festivalrojosangre.com.ar entries to parking (redirect to old domain)
UPDATE gnupanel_apacheconf SET redirigir=1, dominio_destino='http://rojosangre.quintadimension.com/', documentroot='/usr/share/gnupanel/gnupanel', estado=1 WHERE id_apache IN (14, 41);
UPDATE gnupanel_apacheconf SET dominio_destino='http://rojosangre.quintadimension.com/', estado=1 WHERE id_apache IN (40, 15);

-- Restore rojosangre.quintadimension.com to serve the site directly
UPDATE gnupanel_apacheconf SET redirigir=0, dominio_destino=NULL, documentroot='/var/www/sitios/admin/quintadimension@quintadimension.com/quintadimension.com/subdominios/rojosangre', estado=1 WHERE id_apache IN (13, 16);
\""
```

Then revert `wp-config.php` paths to include `/2.0`:
```php
define('WP_SITEURL', 'https://' . $_SERVER['SERVER_NAME'] . '/2.0/wordpress');
define('WP_HOME', 'https://' . $_SERVER['SERVER_NAME'] . '/2.0');
define('WP_CONTENT_URL', 'https://' . $_SERVER['SERVER_NAME'] . '/2.0/wp-content');
```

Wait ~30 seconds for the daemon to regenerate vhost files, then verify with `curl -I https://rojosangre.quintadimension.com/2.0/`.

A backup SQL file was also saved during migration at `/root/gnupanel_rollback_YYYYMMDD.sql`.

### If Apache is completely down

```bash
# Restore vhost files directly from backup
cp /root/sites-enabled-backup-YYYYMMDD/* /var/lib/gnupanel/etc/apache2/sites-enabled/
cp /root/ssl-sites-enabled-backup-YYYYMMDD/* /var/lib/gnupanel/etc/apache2/ssl/sites-enabled/
apache2ctl configtest && apache2ctl graceful

# Then fix the DB too (daemon will overwrite files eventually)
su postgres -c "psql -d gnupanel -f /root/gnupanel_rollback_YYYYMMDD.sql"
```

---

## How to Re-Apply Migration if gnupanel Overwrites

If gnupanel overwrites our changes (e.g., after a panel update or someone modifies domains in the panel UI), run these SQL commands:

```bash
su postgres -c "psql -d gnupanel -c \"
-- Make www.festivalrojosangre.com.ar serve the site (DocumentRoot = /2.0 subdir)
UPDATE gnupanel_apacheconf SET
  redirigir = 0,
  dominio_destino = NULL,
  documentroot = '/var/www/sitios/admin/quintadimension@quintadimension.com/quintadimension.com/subdominios/rojosangre/2.0',
  estado = 1
WHERE id_apache IN (14, 41);

-- Redirect bare domain and www.www artifact to www
UPDATE gnupanel_apacheconf SET
  dominio_destino = 'https://www.festivalrojosangre.com.ar/',
  estado = 1
WHERE id_apache IN (40, 15);

-- Redirect old domain to new domain
UPDATE gnupanel_apacheconf SET
  redirigir = 1,
  dominio_destino = 'https://www.festivalrojosangre.com.ar/',
  estado = 1
WHERE id_apache IN (13, 16);
\""
```

These commands are idempotent and safe to run multiple times.

If the daemon isn't picking up changes (vhost file unchanged after 60 seconds):

```bash
/usr/local/gnupanel/reconfig-apache.sh
```

---

## gnupanel Safety Rules

After migration, do NOT modify the following in gnupanel's web UI:

- **`festivalrojosangre.com.ar`** in parking/domain sections — gnupanel may reset the documentroot
- **`rojosangre.quintadimension.com`** in subdomain settings — may undo the redirect
- **SSL settings** for either domain — may interfere with cert configuration

Other domains (`quintadimension.com`, `fmfenix.com.ar`, etc.) can be managed normally.

---

## SSL Certificate Monitoring

Certificates auto-renew via `/usr/local/gnupanel/certbot-renew.sh` (uses acme.sh, runs every 12h).

Check expiry dates:

```bash
ssh bars "openssl x509 -enddate -noout -in /etc/letsencrypt/live/www.festivalrojosangre.com.ar/fullchain.pem"
ssh bars "openssl x509 -enddate -noout -in /etc/letsencrypt/live/festivalrojosangre.com.ar/fullchain.pem"
```

---

## 302 vs 301 Redirect Limitation

gnupanel's Perl script (`configura-apache.pl` line ~712) generates `Redirect /` (HTTP 302 temporary) for all parked/redirected domains. For SEO, the old domain redirect should ideally be 301 (permanent).

**Impact:** Search engines will be slower to transfer rankings from the old domain to the new one. However, since `seo.php` uses `home_url('/')` for canonical URLs, search engines see the correct canonical on the new domain regardless.

**Future fix (optional):** Modify line ~712 of `/usr/local/gnupanel/configura-apache.pl` to use `Redirect permanent` instead of `Redirect`. This affects all parked domains on the server, so evaluate whether that's desired before making the change.

---

## Important: Trailing Slash in `dominio_destino`

Apache's `Redirect /` directive concatenates the destination URL with the remaining request path. Without a trailing slash on the destination (e.g., `https://www.festivalrojosangre.com.ar`), a request for `/foo` would redirect to `https://www.festivalrojosangre.com.arfoo` (missing the slash separator). Always ensure `dominio_destino` values end with `/`.
