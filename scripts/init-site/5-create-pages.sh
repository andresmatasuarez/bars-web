#!/bin/bash
set -e

# ---------------------------------------------------------------------------
# Create WordPress pages for ALL themes and set up ownership metadata.
#
# Each page gets a `_bars_theme` post-meta so switch-theme.sh knows which
# theme owns it.  Pages whose slug is needed by more than one theme also
# get a `_bars_slug` meta (the canonical/clean slug).  On theme switch the
# active theme's pages get the clean slug; everyone else's get
# `{slug}-off-{theme}` (e.g. programacion-off-bars2013).
#
# This script runs AFTER 4-seed-data.sh (backup.xml import) and deletes
# all pre-existing pages first, so there are no duplicates.
# ---------------------------------------------------------------------------

echo "Deleting existing pages (including the sample one)..."
wp post delete $(wp post list --post_type=page --fields=ID --format=ids) --force 2>/dev/null || echo "No existing pages to delete."
echo "✅ Existing pages deleted"

# ---------------------------------------------------------------------------
# Page definitions per theme
# Format: "title|slug|template|order"
# ---------------------------------------------------------------------------

bars2026_pages=(
  "Home|home||1"
  "Noticias|noticias|page-news.php|2"
  "El festival|festival|page-about.php|3"
  "Programacion|programacion||4"
  "Premios|premios|page-awards.php|5"
  "Convocatoria|convocatoria|page-call.php|6"
  "Prensa|prensa|page-press.php|7"
)

bars2013_pages=(
  "Novedades|novedades|news.php|1"
  "BARS|bars|festival.php|2"
  "#RojoSangreTV|rojosangretv|rojo_sangre_tv.php|3"
  "Programacion|programacion|selection.php|4"
  "Premios y jurados|premios-y-jurados|juries.php|5"
  "Convocatoria|convocatoria|call.php|6"
  "Auspiciantes|auspiciantes|sponsors.php|7"
  "Prensa|prensa|press.php|8"
  "Contacto|contacto|contact.php|9"
)

# Slugs that are claimed by more than one theme.
# Pages with these slugs get `_bars_slug` meta so switch-theme.sh can swap them.
overlapping_slugs="programacion convocatoria prensa"

is_overlapping() {
  local slug="$1"
  for s in $overlapping_slugs; do
    [ "$s" = "$slug" ] && return 0
  done
  return 1
}

# ---------------------------------------------------------------------------
# Detect the currently active theme
# ---------------------------------------------------------------------------
ACTIVE_THEME=$(wp option get stylesheet)
echo "Active theme: $ACTIVE_THEME"

# ---------------------------------------------------------------------------
# Helper: create pages for a given theme
# $1 = theme name (e.g. bars2026)
# $2 = name of the array variable holding page definitions
# ---------------------------------------------------------------------------
create_pages_for_theme() {
  local theme="$1"
  shift
  local pages=("$@")

  local is_active=false
  [ "$theme" = "$ACTIVE_THEME" ] && is_active=true

  for entry in "${pages[@]}"; do
    IFS='|' read -r title slug template order <<< "$entry"

    # Determine the slug to actually use during creation.
    # For overlapping slugs, only the active theme gets the clean slug;
    # others get {slug}-off-{theme} to avoid WordPress uniqueness conflicts.
    # (WordPress normalises "--" to "-", so a per-theme suffix is needed.)
    local create_slug="$slug"
    if is_overlapping "$slug" && [ "$is_active" = false ]; then
      create_slug="${slug}-off-${theme}"
    fi

    # Determine status
    local status="publish"
    [ "$is_active" = false ] && status="draft"

    echo "  Creating [$theme] '$title' (slug: $create_slug, status: $status)..."

    local args=(
      --post_type=page
      "--post_title=$title"
      "--post_name=$create_slug"
      "--menu_order=$order"
      "--post_status=$status"
    )

    PAGE_ID=$(wp post create "${args[@]}" --porcelain)

    # Set page template via meta (bypasses active-theme validation that
    # --page_template enforces, so inactive themes' templates work too)
    if [ -n "$template" ]; then
      wp post meta set "$PAGE_ID" _wp_page_template "$template"
    fi

    # Set ownership meta
    wp post meta set "$PAGE_ID" _bars_theme "$theme"

    # Set canonical slug meta for overlapping slugs
    if is_overlapping "$slug"; then
      wp post meta set "$PAGE_ID" _bars_slug "$slug"
    fi
  done
}

# ---------------------------------------------------------------------------
# Create all pages
# ---------------------------------------------------------------------------
echo ""
echo "Creating bars2026 pages..."
create_pages_for_theme "bars2026" "${bars2026_pages[@]}"

echo ""
echo "Creating bars2013 pages..."
create_pages_for_theme "bars2013" "${bars2013_pages[@]}"

echo ""
echo "✅ All pages successfully created."
