<?php
/**
 * BARS 2026 Theme Functions
 *
 * @package BARS2026
 */

/**
 * Enqueue theme styles and scripts
 */
function bars2026_enqueue_assets() {
    // WordPress theme metadata stylesheet
    wp_enqueue_style(
        'bars2026-style',
        get_template_directory_uri() . '/style.css',
        array(),
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
}
add_action('wp_enqueue_scripts', 'bars2026_enqueue_assets');

/**
 * Theme setup
 */
function bars2026_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
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
        'footer'  => __('Footer Menu', 'bars2026'),
    ));
}
add_action('after_setup_theme', 'bars2026_setup');

