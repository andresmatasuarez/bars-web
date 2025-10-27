#!/bin/bash
set -e

echo "Deleting sample post..."
wp post delete 1 --force 2>/dev/null || echo "Sample posts already deleted."
echo "✅ Sample post successfully deleted."
