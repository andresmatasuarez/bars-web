#!/bin/bash
# Usage: ./scripts/switch-theme.sh [theme-name]
# Examples:
#   ./scripts/switch-theme.sh bars2013
#   ./scripts/switch-theme.sh bars2026
#   ./scripts/switch-theme.sh          # Shows current theme

if [ -z "$1" ]; then
  echo "Current theme:"
  docker compose exec -T wordpress wp theme list --status=active --allow-root --format=table
  echo ""
  echo "Usage: $0 <theme-name>"
  echo "Available: bars2013, bars2026"
  exit 0
fi

docker compose exec -T wordpress wp theme activate "$1" --allow-root
