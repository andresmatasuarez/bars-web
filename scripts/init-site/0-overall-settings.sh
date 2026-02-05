#!/bin/bash
set -e

echo "Setting permalink structure..."
wp rewrite structure '/%postname%/'
wp rewrite flush
echo "✅ Permalink structure set"

echo "Setting timezone to UTC-3 (Buenos Aires)..."
wp option update timezone_string "UTC-3"
echo "✅ Timezone set"
