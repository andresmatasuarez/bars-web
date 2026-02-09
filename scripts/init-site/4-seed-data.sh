#!/bin/bash
set -e

UPLOADS_DIR="/docker-entrypoint-init.d/uploads"
BACKUP_FILE="/docker-entrypoint-init.d/backup.xml"

MARKER_FILE_IMPORT_DONE="/bitnami/wordpress/.import_done"

if [ ! -f "$BACKUP_FILE" ]; then
  echo "No backup file found at $BACKUP_FILE, nothing to import."
  exit
fi

if [ -f "$MARKER_FILE_IMPORT_DONE" ]; then
  echo "✅ Skipping importing XML backup file and media library."
  exit
fi

echo "⏳ Importing $BACKUP_FILE..."

wp import "$BACKUP_FILE" --authors=create
wp search-replace "$SEED_DATA_OLD_HOSTNAME" "$SEED_DATA_NEW_HOSTNAME" --skip-columns=guid --all-tables
echo "✅ File $BACKUP_FILE successfully imported."

echo "⏳ Copying uploads to media library..."
cp -r "$UPLOADS_DIR"/* /bitnami/wordpress/wp-content/uploads/
echo "✅ All uploads copied to the media library successfully."

touch "$MARKER_FILE_IMPORT_DONE"
