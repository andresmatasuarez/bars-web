#!/bin/bash
set -euo pipefail

# ---------------------------------------------------------------------------
# Custom entrypoint wrapper for the BARS WordPress container.
#
# 1. Calls the official wordpress:apache entrypoint (copies WP core files,
#    generates wp-config.php from env vars).
# 2. Waits for the database to be ready.
# 3. Installs WordPress core if not already installed.
# 4. Installs the wordpress-importer plugin via WP-CLI.
# 5. Runs init scripts from /docker-entrypoint-init.d/ (once, guarded by
#    a marker file).
# 6. Execs into apache2-foreground.
# ---------------------------------------------------------------------------

WP_PATH="/var/www/html"
MARKER_FILE="$WP_PATH/.user_scripts_initialized"

# All wp-cli commands run as root inside the container.
export WP_CLI_ALLOW_ROOT=1

# ---------------------------------------------------------------------------
# Phase 0: Pre-entrypoint cleanup.
#
# a) Always regenerate wp-config.php from env vars. The official entrypoint
#    bakes WORDPRESS_CONFIG_EXTRA, WORDPRESS_DEBUG, etc. into the file at
#    generation time, so env var changes only take effect if the file is
#    recreated. Deleting it here ensures the entrypoint regenerates it.
#
# b) Fix file ownership left over from the Bitnami image (uid 1001 →
#    www-data). Only runs when Bitnami-owned files are detected.
# ---------------------------------------------------------------------------
if [ -f "$WP_PATH/wp-config.php" ]; then
  echo "==> Removing wp-config.php so the official entrypoint regenerates it from env vars..."
  rm -f "$WP_PATH/wp-config.php"
fi

if find "$WP_PATH/wp-content" -maxdepth 0 -user 1001 -print -quit 2>/dev/null | grep -q .; then
  echo "==> Fixing file ownership (Bitnami uid 1001 → www-data)..."
  chown -R www-data:www-data "$WP_PATH/wp-content"
fi

# ---------------------------------------------------------------------------
# Phase 1: Run the official WordPress entrypoint.
#
# This handles copying WP core files into the volume (if missing) and
# generating wp-config.php from WORDPRESS_DB_* env vars.
#
# The official entrypoint only runs setup when $1 is "apache2-foreground"
# (or "php-fpm"). It then `exec`s into that command. We need setup to run
# but don't want Apache to start yet, so we:
#   1. Create a temporary no-op "apache2-foreground" script
#   2. Run the official entrypoint in a subshell with that in PATH
#   3. The entrypoint does all setup, then exec's our no-op (which exits)
#   4. The subshell ends, control returns to us
# ---------------------------------------------------------------------------
echo "==> Running official WordPress entrypoint..."
mkdir -p /tmp/fake-bin
printf '#!/bin/sh\nexit 0\n' > /tmp/fake-bin/apache2-foreground
chmod +x /tmp/fake-bin/apache2-foreground
(
  export PATH="/tmp/fake-bin:$PATH"
  /usr/local/bin/docker-entrypoint.sh apache2-foreground
)
rm -rf /tmp/fake-bin

# ---------------------------------------------------------------------------
# Phase 2: Wait for the database to be ready.
#
# Uses mysqladmin ping with --skip-ssl because the MySQL container uses
# self-signed certs and the MariaDB client rejects them by default.
# ---------------------------------------------------------------------------
echo "==> Waiting for database..."
retries=30
until mysqladmin ping \
    -h "${WORDPRESS_DB_HOST%%:*}" \
    -u "$WORDPRESS_DB_USER" \
    -p"$WORDPRESS_DB_PASSWORD" \
    --skip-ssl --silent 2>/dev/null; do
  retries=$((retries - 1))
  if [ "$retries" -le 0 ]; then
    echo "ERROR: Database not reachable after 30 attempts. Aborting."
    exit 1
  fi
  echo "    Database not ready yet, retrying in 2s... ($retries attempts left)"
  sleep 2
done
echo "==> Database is ready."

# ---------------------------------------------------------------------------
# Phase 3: Install WordPress core if not already installed.
# ---------------------------------------------------------------------------
if ! wp core is-installed --path="$WP_PATH" 2>/dev/null; then
  echo "==> Installing WordPress..."
  wp core install \
    --path="$WP_PATH" \
    --url="http://localhost:${WORDPRESS_PORT:-80}" \
    --title="${WORDPRESS_BLOG_NAME:-WordPress}" \
    --admin_user="${WORDPRESS_ADMIN_USERNAME:-admin}" \
    --admin_password="${WORDPRESS_ADMIN_PASSWORD:-admin}" \
    --admin_email="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}" \
    --skip-email
  echo "==> WordPress installed."
else
  echo "==> WordPress already installed, skipping core install."
fi

# ---------------------------------------------------------------------------
# Phase 4: Install the wordpress-importer plugin (idempotent).
# ---------------------------------------------------------------------------
if ! wp plugin is-installed wordpress-importer --path="$WP_PATH" 2>/dev/null; then
  echo "==> Installing wordpress-importer plugin..."
  wp plugin install wordpress-importer --activate --path="$WP_PATH"
  echo "==> wordpress-importer installed."
else
  echo "==> wordpress-importer already installed, skipping."
fi

# ---------------------------------------------------------------------------
# Phase 5: Run init scripts (once).
# ---------------------------------------------------------------------------
INIT_DIR="/docker-entrypoint-init.d"

if [ -d "$INIT_DIR" ] && [ ! -f "$MARKER_FILE" ]; then
  echo "==> Running init scripts from $INIT_DIR..."
  for script in "$INIT_DIR"/*.sh; do
    [ -f "$script" ] || continue
    echo "--- Running $(basename "$script")..."
    # Run each script in a subshell so `set -e` in scripts doesn't kill us
    (cd "$WP_PATH" && bash "$script")
    echo "--- $(basename "$script") done."
  done
  touch "$MARKER_FILE"
  echo "==> Init scripts complete. Marker file created."
else
  if [ -f "$MARKER_FILE" ]; then
    echo "==> Init scripts already ran (marker file found), skipping."
  else
    echo "==> No init scripts directory found, skipping."
  fi
fi

# ---------------------------------------------------------------------------
# Phase 6: Start Apache.
# ---------------------------------------------------------------------------
echo "==> Starting Apache..."
exec apache2-foreground
