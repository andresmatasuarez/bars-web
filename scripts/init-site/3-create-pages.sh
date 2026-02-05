#!/bin/bash
set -e

echo "Deleting existing pages (including the sample one)..."
wp post delete $(wp post list --post_type=page --fields=ID --format=ids) --force 2>/dev/null || echo "Existing pages already deleted."
echo "✅ Existing pages deleted"

# ⚠️ EARLY EXIT
# Pages are already being imported from the backup file.
# For the moment, there's no need in running this script.
exit;

# Define an associative array of page data
declare -A pages
pages["news"]="title:Novedades,slug:novedades,template:news.php,order:1"
pages["festival"]="title:BARS,slug:bars,template:festival.php,order:2"
pages["rojo_sangre_tv"]="title:#RojoSangreTV,slug:rojosangretv,template:rojo_sangre_tv.php,order:3"
pages["selection"]="title:Programacion,slug:programacion,template:selection.php,order:4"
pages["juries"]="title:Premios y jurados,slug:premios-y-jurados,template:juries.php,order:5"
pages["call"]="title:Convocatoria,slug:convocatoria,template:page-call.php,order:6"
pages["sponsors"]="title:Auspiciantes,slug:auspiciantes,template:sponsors.php,order:7"
pages["press"]="title:Prensa,slug:prensa,template:page-press.php,order:8"
pages["contact"]="title:Contacto,slug:contacto,template:contact.php,order:9"

# --- bars2013 ----------------------
# pages["news"]="title:Novedades,slug:novedades,template:news.php,order:1"
# pages["festival"]="title:BARS,slug:bars,template:festival.php,order:2"
# pages["rojo_sangre_tv"]="title:#RojoSangreTV,slug:rojosangretv,template:rojo_sangre_tv.php,order:3"
# pages["selection"]="title:Programacion,slug:programacion,template:selection.php,order:4"
# pages["juries"]="title:Premios y jurados,slug:premios-y-jurados,template:juries.php,order:5"
# pages["call"]="title:Convocatoria,slug:convocatoria,template:call.php,order:6"
# pages["sponsors"]="title:Auspiciantes,slug:auspiciantes,template:sponsors.php,order:7"
# pages["press"]="title:Prensa,slug:prensa,template:press.php,order:8"
# pages["contact"]="title:Contacto,slug:contacto,template:contact.php,order:9"

# Loop through the pages array
for page_key in "${!pages[@]}"; do
    # Split the page data string into separate variables
    IFS=',' read -r -a page_data <<< "${pages[$page_key]}"
    title=$(echo "${page_data[0]}" | cut -d':' -f2)
    slug=$(echo "${page_data[1]}" | cut -d':' -f2)
    template=$(echo "${page_data[2]}" | cut -d':' -f2)
    order=$(echo "${page_data[3]}" | cut -d':' -f2)

    # Check if a page with the given slug already exists
    PAGE_ID=$(wp post list --post_type=page --name="$slug" --fields=ID --format=ids)

    # If the page ID is empty, the page does not exist
    if [ -z "$PAGE_ID" ]; then
        echo "Creating page: '$title' with slug '$slug'..."
        wp post create --post_type=page --post_title="$title" --post_name="$slug" --page_template="$template" --menu_order="$order" --post_status=publish
    else
        echo "Page with slug '$slug' already exists with ID: $PAGE_ID. Skipping creation."
    fi
done

echo "✅ Pages successfully created."
