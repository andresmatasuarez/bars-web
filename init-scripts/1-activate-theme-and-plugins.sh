#!/bin/bash
set -e

echo "Activating theme: 'bars2013'..."
wp theme activate bars2013 --allow-root

echo "Activating plugin: 'bars-commons'..."
wp plugin activate bars-commons --allow-root

echo "Activating plugin: 'movie-post-type'..."
wp plugin activate movie-post-type --allow-root

echo "Activating plugin: 'jury-post-type'..."
wp plugin activate jury-post-type --allow-root

echo "Flushing rewrite rules for CPTs 'movie', 'movieblock' and 'jury'"
wp rewrite flush

echo "✅ Themes & plugins successfully activated!"
