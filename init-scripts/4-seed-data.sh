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

echo "⏳ Adding uploads to media library..."
#####################################
# Import uploads in batches of 50
#####################################
BATCH_SIZE=50
batch=""
count=0

while IFS= read -r -d '' file; do
  batch="$batch $file"
  (( count = count + 1 ))

  if (( count >= BATCH_SIZE )); then
    # call wp media import with the batch (files first, then options)
    echo "Importing batch of uploads: $batch..."
    wp media import $batch --preserve-filetime

    # reset
    batch=""
    count=0
  fi
done < <(find -L "$UPLOADS_DIR" -type f \( -iname '*.jpg' -o -iname '*.jpeg' -o -iname '*.png' -o -iname '*.gif' -o -iname '*.webp' \) -print0)

# final batch (if any)
if (( count > 0 )); then
  echo "Importing batch of uploads: $batch..."
  wp media import $batch --preserve-filetime
fi

echo "✅ All uploads added to the media library successfully."
#####################################
#####################################

touch "$MARKER_FILE_IMPORT_DONE"
