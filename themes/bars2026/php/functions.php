<?php
/**
 * BARS 2026 Theme Functions
 *
 * @package BARS2026
 */

// Polyfill for get_the_post_thumbnail_url() — added in WP 4.4, but live site runs WP 3.9 (as of Feb 2026)
if (!function_exists('get_the_post_thumbnail_url')) {
    function get_the_post_thumbnail_url($post = null, $size = 'post-thumbnail') {
        $thumb_id = get_post_thumbnail_id($post);
        if (!$thumb_id) {
            return false;
        }
        $src = wp_get_attachment_image_src($thumb_id, $size);
        return $src ? $src[0] : false;
    }
}

// Include shared helpers
require_once get_template_directory() . '/helpers.php';
require_once get_template_directory() . '/editions.php';
require_once get_template_directory() . '/icons.php';
require_once get_template_directory() . '/seo.php';

/**
 * Enqueue theme styles and scripts
 */
function bars2026_enqueue_assets() {
    // Main compiled CSS (from Vite/Tailwind)
    wp_enqueue_style(
        'bars2026-main',
        get_template_directory_uri() . '/bars2026.css',
        array(),
        '1.0.0'
    );

    // WordPress theme metadata stylesheet
    wp_enqueue_style(
        'bars2026-style',
        get_template_directory_uri() . '/style.css',
        array('bars2026-main'),
        '1.0.0'
    );

    // Main theme script (compiled by Vite)
    wp_enqueue_script(
        'bars2026-script',
        get_template_directory_uri() . '/bars2026.iife.js',
        array(),
        '1.0.0',
        true
    );

    // Pass PHP data to JavaScript
    wp_localize_script('bars2026-script', 'BARS_DATA', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'themeUrl' => get_template_directory_uri(),
    ));
}
add_action('wp_enqueue_scripts', 'bars2026_enqueue_assets');

/**
 * Theme setup
 */
function bars2026_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height' => 44,
        'width' => 44,
        'flex-height' => true,
        'flex-width' => true,
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // Register nav menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'bars2026'),
        'footer-festival' => __('Footer - Festival', 'bars2026'),
        'footer-legal' => __('Footer - Legal', 'bars2026'),
    ));

    // Add image sizes
    add_image_size('news-featured', 800, 450, true);
    add_image_size('news-card', 400, 225, true);
    add_image_size('movie-post-thumbnail', 400, 225, true);
    add_image_size('sponsor-logo', 120, 60, false);
}
add_action('after_setup_theme', 'bars2026_setup');

