#!/bin/bash
# Usage: ./scripts/switch-theme.sh [theme-name]
# Examples:
#   ./scripts/switch-theme.sh bars2013
#   ./scripts/switch-theme.sh bars2026
#   ./scripts/switch-theme.sh          # Shows current theme

WP="docker compose exec -T wordpress wp --allow-root"

if [ -z "$1" ]; then
  echo "Current theme:"
  $WP theme list --status=active --format=table
  echo ""
  echo "Usage: $0 <theme-name>"
  echo "Available: bars2013, bars2026"
  exit 0
fi

TARGET="$1"

# ---------------------------------------------------------------------------
# 1. Activate the WordPress theme
# ---------------------------------------------------------------------------
$WP theme activate "$TARGET"

# ---------------------------------------------------------------------------
# 2. Deactivate ALL other themes' pages: draft them and free overlapping slugs
# ---------------------------------------------------------------------------
echo ""
echo "Toggling page statuses for theme: $TARGET"

OTHER_IDS=$($WP post list --post_type=page --post_status=any --meta_key=_bars_theme --format=ids 2>/dev/null || true)

for ID in $OTHER_IDS; do
  OWNER=$($WP post meta get "$ID" _bars_theme)
  if [ "$OWNER" != "$TARGET" ]; then
    # Free overlapping slug if this page currently holds the clean version
    CANONICAL=$($WP post meta get "$ID" _bars_slug 2>/dev/null || true)
    if [ -n "$CANONICAL" ]; then
      CURRENT_SLUG=$($WP post get "$ID" --field=post_name)
      if [ "$CANONICAL" = "$CURRENT_SLUG" ]; then
        $WP post update "$ID" --post_name="${CANONICAL}-off-${OWNER}" --quiet
      fi
    fi
    $WP post update "$ID" --post_status=draft --quiet
  fi
done

# ---------------------------------------------------------------------------
# 3. Activate target theme's pages: restore clean slugs and publish
# ---------------------------------------------------------------------------
TARGET_IDS=$($WP post list --post_type=page --post_status=any --meta_key=_bars_theme --meta_value="$TARGET" --format=ids 2>/dev/null || true)

for ID in $TARGET_IDS; do
  CANONICAL=$($WP post meta get "$ID" _bars_slug 2>/dev/null || true)
  if [ -n "$CANONICAL" ]; then
    $WP post update "$ID" --post_name="$CANONICAL" --quiet
  fi
  $WP post update "$ID" --post_status=publish --quiet
done

echo "✅ Pages toggled: $TARGET pages published, other themes' pages drafted."
