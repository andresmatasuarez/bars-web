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
require_once get_template_directory() . '/og-image.php';
require_once get_template_directory() . '/favicon.php';

/**
 * Version for a theme file, used as its ?ver= value: the file's modification
 * time. The server sends no cache headers, so browsers guess how long to keep
 * files. A new ?ver= after every deploy makes them fetch the new build.
 */
function bars2026_asset_version($file) {
    $path = get_template_directory() . '/' . $file;
    return file_exists($path) ? (string) filemtime($path) : '1.0.0';
}

/**
 * Enqueue theme styles and scripts
 */
function bars2026_enqueue_assets() {
    // Main compiled CSS (from Vite/Tailwind)
    wp_enqueue_style(
        'bars2026-main',
        get_template_directory_uri() . '/bars2026.css',
        array(),
        bars2026_asset_version('bars2026.css')
    );

    // WordPress theme metadata stylesheet
    wp_enqueue_style(
        'bars2026-style',
        get_template_directory_uri() . '/style.css',
        array('bars2026-main'),
        bars2026_asset_version('style.css')
    );

    // Main theme script (compiled by Vite)
    wp_enqueue_script(
        'bars2026-script',
        get_template_directory_uri() . '/bars2026.iife.js',
        array(),
        bars2026_asset_version('bars2026.iife.js'),
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

    // Image sizes are registered centrally in bars-commons plugin
    // (see barscommons_register_image_sizes) so all crops exist regardless of active theme.
}
add_action('after_setup_theme', 'bars2026_setup');

/**
 * Title for archive pages (category, tag, author, date). Works on WP 3.9:
 * the_archive_title() needs WP 4.1, and calling it was a fatal error.
 */
function bars2026_archive_title() {
    if (is_category()) {
        $title = 'Categoría: ' . single_cat_title('', false);
    } elseif (is_tag()) {
        $title = 'Etiqueta: ' . single_tag_title('', false);
    } elseif (is_author()) {
        $author = get_queried_object();
        $title = 'Autor: ' . ($author ? $author->display_name : '');
    } elseif (is_year()) {
        $title = 'Año: ' . get_the_date('Y');
    } elseif (is_month()) {
        $title = 'Mes: ' . get_the_date('F \d\e Y');
    } elseif (is_day()) {
        $title = 'Día: ' . get_the_date('j \d\e F \d\e Y');
    } elseif (is_post_type_archive()) {
        $title = post_type_archive_title('', false);
    } elseif (is_tax()) {
        $title = single_term_title('', false);
    } else {
        $title = 'Archivo';
    }
    echo $title;
}

/**
 * Page links for a list of posts, with the same look as the news page.
 * Works on WP 3.9: the_posts_pagination() needs WP 4.1, and calling it
 * was a fatal error. $query defaults to the main query.
 */
function bars2026_pagination($query = null) {
    if (!$query) {
        global $wp_query;
        $query = $wp_query;
    }
    if ($query->max_num_pages < 2) {
        return;
    }

    $big = 999999999; // placeholder page number, replaced by %#%
    $links = paginate_links(array(
        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format'    => '/page/%#%/',
        'total'     => $query->max_num_pages,
        'current'   => max(1, get_query_var('paged')),
        'type'      => 'array',
        'prev_text' => '<span class="hidden lg:inline">← Anterior</span><span class="lg:hidden">←</span>',
        'next_text' => '<span class="hidden lg:inline">Siguiente →</span><span class="lg:hidden">→</span>',
        'mid_size'  => 1,
    ));
    if (!$links) {
        return;
    }

    echo '<nav class="flex items-center justify-center gap-2 mt-12">';
    foreach ($links as $link) {
        if (strpos($link, 'current') !== false) {
            echo '<span class="inline-flex items-center justify-center rounded text-[13px] lg:text-[13px] font-semibold text-white bg-bars-primary px-3.5 py-2.5 lg:px-3.5 lg:py-2.5">'
                . strip_tags($link) . '</span>';
        } else {
            echo str_replace(
                '<a ',
                '<a class="inline-flex items-center justify-center rounded text-[13px] lg:text-[13px] font-medium text-white/60 bg-[#1A1A1A] px-3 py-2 lg:px-3.5 lg:py-2.5 hover:bg-[#252525] transition-colors" ',
                $link
            );
        }
    }
    echo '</nav>';
}
