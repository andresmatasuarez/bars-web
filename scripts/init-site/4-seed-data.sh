#!/bin/bash
set -e

BACKUP_FILE="/docker-entrypoint-init.d/backup.xml"
UPLOADS_DIR="/docker-entrypoint-init.d/uploads"
MARKER_FILE_IMPORT_DONE="/var/www/html/.import_done"
UPLOADS_SERVER_URL="http://uploads-server"

if [ ! -f "$BACKUP_FILE" ]; then
  echo "No backup file found at $BACKUP_FILE, nothing to import."
  exit
fi

if [ -f "$MARKER_FILE_IMPORT_DONE" ]; then
  echo "✅ Skipping importing XML backup file and media library."
  exit
fi

# Detect if local uploads server is running (nginx container).
# If available, rewrite attachment URLs for fast local downloads.
# If not, fall back to remote downloads (slow).
IMPORT_FILE="$BACKUP_FILE"
if timeout 2 bash -c '</dev/tcp/uploads-server/80' 2>/dev/null; then
  echo "✅ Local uploads server detected — using fast local import."
  sed "s|<wp:attachment_url>${SEED_DATA_OLD_HOSTNAME}/wp-content/uploads/|<wp:attachment_url>${UPLOADS_SERVER_URL}/|g" \
    "$BACKUP_FILE" > /tmp/backup-local.xml
  IMPORT_FILE="/tmp/backup-local.xml"
else
  echo "⚠️  No local uploads server detected — downloading from remote (slow)."
  echo "💡 Enable the uploads-server service in docker-compose.yml for faster imports."
fi

# WordPress's download_url() rejects hostnames without a TLD (like "uploads-server")
# because wp_http_validate_url() treats them as internal/unsafe (SSRF protection).
# This temporary must-use plugin whitelists "uploads-server" so the importer can
# download attachments from our local nginx container during seed import.
MU_PLUGIN="/var/www/html/wp-content/mu-plugins/allow-uploads-server.php"
if [ "$IMPORT_FILE" != "$BACKUP_FILE" ]; then
  mkdir -p "$(dirname "$MU_PLUGIN")"
  cat > "$MU_PLUGIN" << 'MUEOF'
<?php
// Temporary mu-plugin: allows wp import to download from the Docker
// "uploads-server" container. Removed automatically after import.
// Without this, download_url() rejects "http://uploads-server/..."
// because the hostname has no TLD (WordPress SSRF protection).
add_filter('http_request_host_is_external', function($external, $host) {
    if ($host === 'uploads-server') return true;
    return $external;
}, 10, 2);
MUEOF
fi

echo "⏳ Importing $IMPORT_FILE..."
wp import "$IMPORT_FILE" --authors=create
rm -f /tmp/backup-local.xml
rm -f "$MU_PLUGIN"

wp search-replace "$SEED_DATA_OLD_HOSTNAME" "$SEED_DATA_NEW_HOSTNAME" --skip-columns=guid --all-tables
echo "✅ File $IMPORT_FILE successfully imported."

echo "⏳ Copying uploads to media library..."
cp -r "$UPLOADS_DIR"/* /var/www/html/wp-content/uploads/
echo "✅ All uploads copied to the media library successfully."

touch "$MARKER_FILE_IMPORT_DONE"
