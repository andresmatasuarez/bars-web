#!/bin/bash
set -euo pipefail

# ---------------------------------------------------------------------------
# Custom entrypoint for the BARS WordPress container.
#
# Wraps the official wordpress:apache entrypoint to add:
#   - wp-config.php regeneration from env vars on every start
#   - WordPress core installation (first run)
#   - One-time site initialization (theme, plugins, settings, seed data)
# ---------------------------------------------------------------------------

WP_PATH="/var/www/html"
INIT_DATA="/docker-entrypoint-init.d"
MARKER_INIT="$WP_PATH/.user_scripts_initialized"

export WP_CLI_ALLOW_ROOT=1

# ---------------------------------------------------------------------------
# Always regenerate wp-config.php from env vars.
#
# The official entrypoint bakes WORDPRESS_CONFIG_EXTRA, WORDPRESS_DEBUG, etc.
# into wp-config.php at generation time. Deleting it here ensures env var
# changes in docker-compose.yml take effect on every restart.
# ---------------------------------------------------------------------------
rm -f "$WP_PATH/wp-config.php"

# ---------------------------------------------------------------------------
# Run the official WordPress entrypoint.
#
# This copies WP core files into the volume (if missing) and generates
# wp-config.php from WORDPRESS_DB_* / WORDPRESS_CONFIG_EXTRA env vars.
#
# The official entrypoint `exec`s into apache2-foreground at the end, but we
# still need to run more setup. A temporary no-op "apache2-foreground" in
# PATH lets the entrypoint finish setup and return control to us.
# ---------------------------------------------------------------------------
mkdir -p /tmp/fake-bin
printf '#!/bin/sh\nexit 0\n' > /tmp/fake-bin/apache2-foreground
chmod +x /tmp/fake-bin/apache2-foreground
(PATH="/tmp/fake-bin:$PATH" /usr/local/bin/docker-entrypoint.sh apache2-foreground)
rm -rf /tmp/fake-bin

# ---------------------------------------------------------------------------
# Install WordPress core if not already installed.
# ---------------------------------------------------------------------------
if ! wp core is-installed --path="$WP_PATH" 2>/dev/null; then
  echo "==> Installing WordPress..."
  wp core install --path="$WP_PATH" \
    --url="http://localhost:${WORDPRESS_PORT:-80}" \
    --title="${WORDPRESS_BLOG_NAME:-WordPress}" \
    --admin_user="${WORDPRESS_ADMIN_USERNAME:-admin}" \
    --admin_password="${WORDPRESS_ADMIN_PASSWORD:-admin}" \
    --admin_email="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}" \
    --skip-email
fi

# ---------------------------------------------------------------------------
# One-time site initialization (guarded by marker file).
#
# Sets up WordPress settings, activates the theme and plugins, imports seed
# data, and creates pages. Only runs on the very first start; subsequent
# starts skip this entirely.
# ---------------------------------------------------------------------------
if [ ! -f "$MARKER_INIT" ]; then
  echo "==> Running first-time site initialization..."

  # WordPress settings
  wp rewrite structure '/%postname%/' --path="$WP_PATH"
  wp option update timezone_string "UTC-3" --path="$WP_PATH"

  # Activate theme and plugins
  wp theme activate bars2026 --path="$WP_PATH"
  wp plugin activate bars-commons movie-post-type jury-post-type --path="$WP_PATH"

  # Flushing...
  wp rewrite flush --path="$WP_PATH"

  # Delete default sample post
  wp post delete 1 --force --path="$WP_PATH" 2>/dev/null || true

  # Import seed data (backup.xml + uploads) and create WordPress pages.
  # These are kept as separate scripts because they contain complex logic
  # with their own error handling and marker files.
  (cd "$WP_PATH" && bash "$INIT_DATA/seed-data.sh")
  (cd "$WP_PATH" && bash "$INIT_DATA/create-pages.sh")

  touch "$MARKER_INIT"
  echo "==> Site initialization complete."
fi

# ---------------------------------------------------------------------------
# Start Apache.
# ---------------------------------------------------------------------------
echo "==> Starting Apache..."
exec apache2-foreground
