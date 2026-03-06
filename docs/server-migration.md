# Server Migration Analysis

Living reference for upgrading the BARS production infrastructure from PHP 5.6 / WordPress 3.9. Last updated: March 2026.

See also: [server-access.md](server-access.md) for current SSH setup.

---

## Current Server Inventory

| Component         | Specification                                                    |
| ----------------- | ---------------------------------------------------------------- |
| **OS**            | Debian 7.11 (Wheezy) — EOL since 2018                           |
| **Kernel**        | 4.14.271-gnt.0-amd64 (custom QEMU kernel)                       |
| **Hardware**      | QEMU VPS: 2 CPU cores, 4 GB RAM, 79 GB disk (23 GB free)        |
| **PHP**           | 5.6.40 (`libapache2-mod-php5`, dotdeb package)                   |
| **MySQL**         | 5.5.62                                                           |
| **Apache**        | 2.2.22                                                           |
| **OpenSSH**       | 6.0 (RSA-only; no Ed25519)                                       |
| **WordPress**     | 3.9                                                              |
| **Control Panel** | gnupanel (custom, unmaintained)                                   |
| **Hosting**       | quintadimension.com (multi-tenant VPS, 23 vhosts total)          |
| **BARS storage**  | 3.3 GB total (3 GB media uploads + code/DB)                      |
| **Domain**        | `www.festivalrojosangre.com.ar`                                   |

---

## Why In-Place PHP/WP Upgrade Is Not Feasible

Three independent blockers — any one is fatal.

### Blocker 1: gnupanel crashes Apache on PHP 7+

gnupanel's Perl script (`/usr/local/gnupanel/configura-apache.pl`) hardcodes these directives into **every** vhost it generates:

```apache
php_admin_value safe_mode 1                          # removed in PHP 5.4
php_admin_value suhosin.executor.func.blacklist ...  # extension doesn't exist in PHP 7+
php_flag register_globals 0                          # removed in PHP 5.4
```

With `mod_php` (PHP 7+) loaded, Apache treats unknown `php_admin_value`/`php_flag` directives as **fatal errors** and refuses to start. This is not a warning — it's a hard crash. Zero pages served, for all 23 vhosts on the server.

Even if you manually edit the vhost files, gnupanel's daemon can regenerate them (via `reconfig-apache.sh`), re-injecting the broken directives and crashing Apache again.

### Blocker 2: Debian 7 has no PHP 7+ packages

- Debian Wheezy repos are archived — no new packages, ever.
- The dotdeb repository (source of PHP 5.6) is defunct and commented out in `/etc/apt/sources.list`.
- Sury's PPA (the standard way to get PHP 7/8 on Debian/Ubuntu) requires Debian 8+.
- Compiling from source on Debian 7 is theoretically possible but extremely fragile: OpenSSL 1.0.1 is dangerously outdated, glibc is too old for modern build tools, and missing build dependencies would need manual compilation too.

### Blocker 3: Debian 7 to 12 upgrade is 5 sequential hops

Going from Debian 7 to 12 requires stepping through 7 → 8 → 9 → 10 → 11 → 12. Each hop risks:

- gnupanel breaking entirely (designed for Debian 7 / Apache 2.2)
- MySQL 5.5 → MariaDB migration (Debian 10+ dropped MySQL)
- Custom kernel incompatibility with newer userspace libraries
- Package conflicts cascading across 5 generations of dependencies
- Extended downtime if any single hop fails
- No rollback path once changes are applied

### Could we just upgrade WordPress (without PHP)?

The ceiling is **WordPress 5.2** (last branch to officially support PHP 5.6), but WP 5.2 is also EOL with no security updates. Jumping from WP 3.9 to 5.2 spans 5 years of database schema changes with no rollback if migration fails. Not worth the risk for a lateral move between two unsupported versions.

---

## gnupanel Assessment

### What it manages on this server

- **Apache vhosts** — generates and manages configs for all 23 domains
- **SSL certificates** — Let's Encrypt auto-renewal via `certbot-renew.sh` (runs every 12 hours)
- **Backups** — `gnupanel-backup.sh` runs daily at 2:45 AM
- **Stats** — `genera-estadisticas.pl` (Webalizer/AWStats) runs daily
- **Email** — Postfix MTA, Amavis spam filtering, Mailman mailing lists
- **DNS** — `pdns_notify.sh` runs hourly (PowerDNS)
- **Traffic/plan management** — billing quotas, resource tracking

### What BARS uses from gnupanel

Only two things:

1. **Apache vhost configuration** for `www.festivalrojosangre.com.ar` — essentially static (DocumentRoot + redirect rules). Configured via direct SQL and hasn't changed since the domain migration.
2. **SSL certificate auto-renewal** — Let's Encrypt certs via gnupanel's certbot script.

### Can it be removed?

- **For BARS on a new server**: Yes. Apache/Nginx + certbot configured directly, no gnupanel needed.
- **On the current shared server**: No. gnupanel manages 22 other vhosts, email, DNS, backups, and stats for unrelated customer domains. Removing it would break everything else.

### `reconfig-apache.sh` behavior

Sets ALL database rows' `estado = 0`, waits for the Perl daemon to process them (up to 30 min), sleeps 72 seconds, then stops and starts Apache. **The site goes down** during the sleep + restart phase (~2 min downtime).

---

## Recommended Path: Migrate to a New Server

A new dedicated server for BARS eliminates gnupanel, the ancient OS, and the PHP/MySQL constraints in one move. The current server continues running for the other 22 vhosts — BARS just moves off it.

### BARS Site Requirements

- **Storage**: 3.3 GB (3 GB media uploads) + database
- **PHP**: 8.x with extensions (gd, mbstring, curl, intl, mysqlnd, json, xml)
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Custom domain**: `www.festivalrojosangre.com.ar` with DNS control
- **SSL**: Let's Encrypt or equivalent
- **SSH access**: required for rsync deploys
- **Traffic profile**: low to moderate (film festival = seasonal spikes)

---

## Hosting Options

### Oracle Cloud Always Free Tier (best free option)

**Specs:**

- ARM VM: up to 4 OCPUs + 24 GB RAM (VM.Standard.A1.Flex) — far more than needed
- Block storage: 200 GB free
- Bandwidth: 10 TB/month outbound
- Full root access: install PHP 8.2, MySQL 8.0, Apache/Nginx
- SSH access for rsync deploys

**Caveats:**

- Requires credit card for verification (never charged for Always Free resources)
- **ARM architecture** (aarch64) — PHP/MySQL work fine, but potential edge cases with some extensions
- **Instance reclamation risk**: Oracle may reclaim idle Always Free instances (rare for active sites, but not guaranteed)
- No SLA, no support on free tier
- Argentina-issued credit cards may be rejected during signup

### Paid alternatives ($4-6/month)

| Provider       | Plan                               | Specs                              |
| -------------- | ---------------------------------- | ---------------------------------- |
| **Hetzner**    | CX22                               | 2 vCPU, 4 GB RAM, 40 GB SSD, ~€4/mo |
| **DigitalOcean** | Basic Droplet                     | 1 vCPU, 1 GB RAM, 25 GB SSD, $6/mo |
| **Vultr**      | Cloud Compute                      | 1 vCPU, 1 GB RAM, 25 GB SSD, $6/mo |

All provide x86_64, full SLA, SSH access, and straightforward signup.

### Comparison

| Factor              | Oracle Cloud Free           | Paid VPS ($4-6/mo)           |
| ------------------- | --------------------------- | ---------------------------- |
| **Specs**           | 4 OCPU + 24 GB RAM (ARM)   | 1-2 vCPU + 1-4 GB RAM (x86) |
| **Architecture**    | ARM — works, minor quirks   | x86_64 — universal           |
| **Reliability**     | No SLA; reclamation risk    | Full SLA, never reclaimed    |
| **Support**         | None                        | Basic ticket support         |
| **Signup**          | Credit card sometimes rejected | Straightforward           |
| **IP reputation**   | Sometimes flagged (free tier abuse) | Clean IPs             |
| **Backups**         | Manual only                 | $1-2/mo extra for snapshots  |

**Bottom line**: Oracle Free Tier would probably work fine in practice. Paid options ($4-6/mo) buy peace of mind — no reclamation risk, no ARM quirks, guaranteed SLA.

---

## Migration Outline

1. **Provision new server** — Debian 12 or Ubuntu 24.04 LTS
2. **Install modern stack** — PHP 8.2+, MySQL 8.0 or MariaDB 10.6, Apache 2.4 or Nginx, certbot
3. **Install WordPress 6.7** fresh
4. **Export/import database** from current server
5. **Copy `wp-content/uploads/`** (3 GB of media) via rsync
6. **Update deploy scripts** — point rsync target to new server
7. **Deploy themes + plugins** via existing rsync workflow
8. **Apply code modernizations** (see next section)
9. **Run integration tests** against new server
10. **DNS cutover** — point `www.festivalrojosangre.com.ar` to new server IP
    - Set low TTL (300s) a few days before the switch
    - Old server continues serving during DNS propagation
    - Effectively zero downtime
11. **Monitor for 1-2 weeks**, then remove BARS vhost from old server

---

## Code Modernizations After Migration

Once running on PHP 8.x + WordPress 6.7, these compatibility workarounds can be removed. **Do not apply any of these while still on PHP 5.6 / WP 3.9.**

### Category A: Remove `$GLOBALS` workaround for `get_template_part()` args (7 files)

WP 5.5+ natively passes the third argument as `$args`. The `$GLOBALS` fallback pattern becomes unnecessary.

**Callers — remove `$GLOBALS` assignment before `get_template_part()`:**

1. `themes/bars2026/php/page-about.php`
2. `themes/bars2026/php/page-call.php`
3. `themes/bars2026/php/page-news.php`
4. `themes/bars2026/php/page-awards.php`
5. `themes/bars2026/php/page-press.php`
6. `themes/bars2026/php/page-selection.php`

**Receiver — remove `$GLOBALS` fallback:**

7. `themes/bars2026/php/template-parts/sections/page-hero.php`

### Category B: Replace `isset() ? x : default` with null coalescing `??` (~20 instances)

PHP 7.0+ supports the `??` operator.

- `shared/php/editions.php` (~8 instances)
- `shared/php/helpers.php` (~2 instances)
- `themes/bars2026/php/seo.php` (~3 instances)
- `themes/bars2026/php/template-parts/sections/page-hero.php`
- `themes/bars2026/php/template-parts/sections/sponsors-section.php`
- `themes/bars2026/php/page-selection.php`
- `themes/bars2026/php/page-press.php`
- `themes/bars2026/php/page-call.php`
- `plugins/movie-post-type/movie-post-type.php`
- `plugins/bars-commons/bars-commons.php`

### Category C: Remove `function_exists()` guards and polyfills (4 changes)

| File | What to remove |
| ---- | -------------- |
| `themes/bars2026/php/functions.php` | Delete `get_the_post_thumbnail_url()` polyfill (WP 4.4 function) |
| `themes/bars2026/php/header.php` | Delete `_wp_render_title_tag` fallback (WP 4.1 function) |
| `themes/bars2026/php/header.php` | Remove `function_exists('wp_body_open')` guard (WP 5.2 function) — call directly |
| `themes/bars2026/php/template-parts/sections/sponsors-section.php` | Remove `function_exists()` guards around helper functions |

### Category D: Update documentation (2 files)

| File | What to change |
| ---- | -------------- |
| `CLAUDE.md` | Rewrite "Production Environment" section: remove PHP 5.6/WP 3.9 constraints, `get_template_part()` workaround docs, and "No PHP 7.0+ syntax" rules |
| `themes/bars2026/php/seo.php` | Remove WP 3.9 compatibility comments |

### Category E: Optional — add PHP type declarations

With PHP 7.0+, scalar type hints and return type declarations become possible. Low priority, do incrementally:

- `shared/php/editions.php` — type hints for `Editions::` static methods
- `shared/php/helpers.php` — type hints for helper functions
