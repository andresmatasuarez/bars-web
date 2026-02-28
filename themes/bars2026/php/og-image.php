<?php
/**
 * OG Image Generator: branded composite images for movie/movieblock sharing.
 *
 * Generates 1200x630 JPEG images with the movie poster as background,
 * overlaid with festival logo, edition title, section label, and movie title.
 * Images are cached to wp-content/uploads/og-cache/ and served as static files.
 *
 * Requires PHP GD with FreeType support. Falls back gracefully to raw poster
 * if GD is unavailable.
 *
 * @package BARS2026
 */

// --------------------------------------------------------------------------
// Constants
// --------------------------------------------------------------------------

define('BARS_OG_WIDTH', 1200);
define('BARS_OG_HEIGHT', 630);
define('BARS_OG_JPEG_QUALITY', 90);

// --------------------------------------------------------------------------
// Cache helpers
// --------------------------------------------------------------------------

/**
 * Get the filesystem path to the OG image cache directory.
 * Creates it if it doesn't exist.
 *
 * @return string|false Directory path, or false on failure.
 */
function bars_og_get_cache_dir() {
    $upload_dir = wp_upload_dir();
    $cache_dir = $upload_dir['basedir'] . '/og-cache';
    if (!is_dir($cache_dir)) {
        if (!wp_mkdir_p($cache_dir)) {
            return false;
        }
    }
    return $cache_dir;
}

/**
 * Get the filesystem path for a post's cached OG image.
 *
 * @param WP_Post $post
 * @return string|false
 */
function bars_og_get_cache_path($post) {
    $cache_dir = bars_og_get_cache_dir();
    if (!$cache_dir) {
        return false;
    }
    return $cache_dir . '/' . get_post_type($post) . '-' . $post->ID . '.jpg';
}

/**
 * Get the public URL for a post's cached OG image.
 *
 * @param WP_Post $post
 * @return string
 */
function bars_og_get_cache_url($post) {
    $upload_dir = wp_upload_dir();
    return $upload_dir['baseurl'] . '/og-cache/' . get_post_type($post) . '-' . $post->ID . '.jpg';
}

// --------------------------------------------------------------------------
// Image loading helpers
// --------------------------------------------------------------------------

/**
 * Create a GD image resource from a local file, auto-detecting format.
 *
 * @param string $path Filesystem path to image.
 * @return resource|false GD image resource or false.
 */
function bars_og_imagecreate_from_file($path) {
    if (!file_exists($path)) {
        return false;
    }
    $info = getimagesize($path);
    if (!$info) {
        return false;
    }
    switch ($info[2]) {
        case IMAGETYPE_JPEG:
            return imagecreatefromjpeg($path);
        case IMAGETYPE_PNG:
            return imagecreatefrompng($path);
        case IMAGETYPE_GIF:
            return imagecreatefromgif($path);
        case IMAGETYPE_WEBP:
            if (function_exists('imagecreatefromwebp')) {
                return imagecreatefromwebp($path);
            }
            return false;
        default:
            return false;
    }
}

/**
 * Load a poster image for a post as a GD resource.
 *
 * Tries in order:
 *   1. Post thumbnail (local attachment)
 *   2. _movie_poster meta (remote URL) — movies only
 *   3. Edition poster (local file from theme)
 *
 * @param WP_Post $post
 * @return resource|false GD image resource or false.
 */
function bars_og_load_poster_image($post) {
    $type = get_post_type($post);

    // 1. Post thumbnail
    $thumb_id = get_post_thumbnail_id($post->ID);
    if ($thumb_id) {
        $thumb_path = get_attached_file($thumb_id);
        if ($thumb_path) {
            $img = bars_og_imagecreate_from_file($thumb_path);
            if ($img) {
                return $img;
            }
        }
    }

    // 2. _movie_poster meta (remote URL) — movies only
    if ($type === 'movie') {
        $poster_url = get_post_meta($post->ID, '_movie_poster', true);
        if ($poster_url) {
            $response = wp_remote_get($poster_url, array('timeout' => 15));
            if (!is_wp_error($response)) {
                $body = wp_remote_retrieve_body($response);
                if ($body) {
                    $img = @imagecreatefromstring($body);
                    if ($img) {
                        return $img;
                    }
                }
            }
        }
    }

    // 3. Edition poster
    $edition_key = ($type === 'movieblock') ? '_movieblock_edition' : '_movie_edition';
    $edition_meta = get_post_meta($post->ID, $edition_key, true);
    $edition = null;
    if ($edition_meta) {
        $number = intval(str_replace('bars', '', $edition_meta));
        if ($number) {
            $edition = Editions::getByNumber($number);
        }
    }
    if (!$edition) {
        $edition = Editions::current();
    }
    if (!empty($edition['poster'])) {
        $poster_path = get_template_directory() . '/' . $edition['poster'];
        $img = bars_og_imagecreate_from_file($poster_path);
        if ($img) {
            return $img;
        }
    }

    return false;
}

// --------------------------------------------------------------------------
// Drawing helpers
// --------------------------------------------------------------------------

/**
 * Draw a filled rounded rectangle with alpha support.
 *
 * Uses non-overlapping regions (corner arcs + edge strips + centre fill)
 * so semi-transparent colours blend correctly.
 *
 * @param resource $canvas GD image resource.
 * @param int      $x1     Left edge.
 * @param int      $y1     Top edge.
 * @param int      $x2     Right edge.
 * @param int      $y2     Bottom edge.
 * @param int      $radius Corner radius in pixels.
 * @param int      $color  GD colour (may include alpha).
 */
function bars_og_draw_rounded_rect($canvas, $x1, $y1, $x2, $y2, $radius, $color) {
    $d = $radius * 2;
    // Four corner arcs
    imagefilledarc($canvas, $x1 + $radius, $y1 + $radius, $d, $d, 180, 270, $color, IMG_ARC_PIE);
    imagefilledarc($canvas, $x2 - $radius, $y1 + $radius, $d, $d, 270, 360, $color, IMG_ARC_PIE);
    imagefilledarc($canvas, $x1 + $radius, $y2 - $radius, $d, $d, 90, 180, $color, IMG_ARC_PIE);
    imagefilledarc($canvas, $x2 - $radius, $y2 - $radius, $d, $d, 0, 90, $color, IMG_ARC_PIE);
    // Edge strips (between corners)
    imagefilledrectangle($canvas, $x1 + $radius, $y1, $x2 - $radius, $y1 + $radius - 1, $color);  // top
    imagefilledrectangle($canvas, $x1 + $radius, $y2 - $radius + 1, $x2 - $radius, $y2, $color);  // bottom
    imagefilledrectangle($canvas, $x1, $y1 + $radius, $x1 + $radius - 1, $y2 - $radius, $color);  // left
    imagefilledrectangle($canvas, $x2 - $radius + 1, $y1 + $radius, $x2, $y2 - $radius, $color);  // right
    // Centre fill
    imagefilledrectangle($canvas, $x1 + $radius, $y1 + $radius, $x2 - $radius, $y2 - $radius, $color);
}

// --------------------------------------------------------------------------
// Text rendering helpers
// --------------------------------------------------------------------------

/**
 * Draw left-aligned text at a given position, auto-shrinking if too wide.
 *
 * @param resource $canvas    GD image resource.
 * @param string   $font      Path to TTF font file.
 * @param float    $size      Initial font size in points.
 * @param float    $min_size  Minimum font size before giving up on shrinking.
 * @param int      $x         X coordinate for text left edge.
 * @param int      $y         Y coordinate for text baseline.
 * @param string   $text      Text to render.
 * @param int      $color     GD color identifier.
 * @param int      $max_width Maximum width in pixels.
 */
function bars_og_draw_text($canvas, $font, $size, $min_size, $x, $y, $text, $color, $max_width) {
    $current_size = $size;
    while ($current_size >= $min_size) {
        $bbox = imagettfbbox($current_size, 0, $font, $text);
        $text_width = abs($bbox[2] - $bbox[0]);
        if ($text_width <= $max_width) {
            imagettftext($canvas, $current_size, 0, $x, $y, $color, $font, $text);
            return;
        }
        $current_size -= 1;
    }
    imagettftext($canvas, $min_size, 0, $x, $y, $color, $font, $text);
}

/**
 * Word-wrap text into lines that fit within a maximum pixel width.
 *
 * @param string $text      The text to wrap.
 * @param string $font      Path to TTF font file.
 * @param float  $size      Font size in points.
 * @param int    $max_width Maximum line width in pixels.
 * @param int    $max_lines Maximum number of lines (default 2). Excess is truncated with "...".
 * @return array Array of line strings.
 */
function bars_og_word_wrap($text, $font, $size, $max_width, $max_lines) {
    $words = explode(' ', $text);
    $lines = array();
    $current_line = '';

    foreach ($words as $word) {
        $test = $current_line === '' ? $word : $current_line . ' ' . $word;
        $bbox = imagettfbbox($size, 0, $font, $test);
        $test_width = abs($bbox[2] - $bbox[0]);
        if ($test_width <= $max_width || $current_line === '') {
            $current_line = $test;
        } else {
            $lines[] = $current_line;
            $current_line = $word;
        }
    }
    if ($current_line !== '') {
        $lines[] = $current_line;
    }

    // Truncate to max_lines with ellipsis on the last line
    if (count($lines) > $max_lines) {
        $last = $lines[$max_lines - 1];
        $remaining_words = explode(' ', $last);
        // Try removing words from the end until "word... " fits
        while (count($remaining_words) > 0) {
            $candidate = implode(' ', $remaining_words) . '...';
            $bbox = imagettfbbox($size, 0, $font, $candidate);
            if (abs($bbox[2] - $bbox[0]) <= $max_width) {
                break;
            }
            array_pop($remaining_words);
        }
        if (count($remaining_words) > 0) {
            $lines[$max_lines - 1] = implode(' ', $remaining_words) . '...';
        } else {
            $lines[$max_lines - 1] = '...';
        }
        $lines = array_slice($lines, 0, $max_lines);
    }

    return $lines;
}

// --------------------------------------------------------------------------
// Core generator
// --------------------------------------------------------------------------

/**
 * Generate a branded OG image for a movie/movieblock post.
 *
 * @param WP_Post $post Movie or movieblock post.
 * @return string|false Filesystem path to the generated JPEG, or false on failure.
 */
function bars_og_generate($post) {
    // Check GD prerequisites
    if (!function_exists('imagecreatetruecolor') || !function_exists('imagettftext')) {
        return false;
    }

    $font_bebas = get_template_directory() . '/og-image-fonts/BebasNeue-Regular.ttf';
    if (!file_exists($font_bebas)) {
        return false;
    }

    $font_cormorant = get_template_directory() . '/og-image-fonts/CormorantGaramond-SemiBold.ttf';
    $font_inter = get_template_directory() . '/og-image-fonts/Inter-SemiBold.ttf';
    // Fall back to Bebas Neue if new fonts aren't available
    if (!file_exists($font_cormorant)) {
        $font_cormorant = $font_bebas;
    }
    if (!file_exists($font_inter)) {
        $font_inter = $font_bebas;
    }

    // Proportional bar dimensions
    $top_bar_h = (int) round(BARS_OG_HEIGHT * 0.11);
    $pad_x = (int) round(BARS_OG_WIDTH * 0.05);

    $cache_path = bars_og_get_cache_path($post);
    if (!$cache_path) {
        return false;
    }

    // Load poster
    $poster = bars_og_load_poster_image($post);
    if (!$poster) {
        return false;
    }

    // Create canvas
    $canvas = imagecreatetruecolor(BARS_OG_WIDTH, BARS_OG_HEIGHT);
    if (!$canvas) {
        imagedestroy($poster);
        return false;
    }

    // Cover-fit the poster onto the canvas (center-crop + scale)
    $src_w = imagesx($poster);
    $src_h = imagesy($poster);
    $scale = max(BARS_OG_WIDTH / $src_w, BARS_OG_HEIGHT / $src_h);
    $scaled_w = (int) ceil($src_w * $scale);
    $scaled_h = (int) ceil($src_h * $scale);
    $offset_x = (int) ((BARS_OG_WIDTH - $scaled_w) / 2);
    $offset_y = (int) ((BARS_OG_HEIGHT - $scaled_h) / 2);
    imagecopyresampled($canvas, $poster, $offset_x, $offset_y, 0, 0, $scaled_w, $scaled_h, $src_w, $src_h);
    imagedestroy($poster);

    // Enable alpha blending for transparent overlays
    imagealphablending($canvas, true);

    // Semi-transparent black: alpha 40 out of 127 ≈ 69% opacity
    $overlay = imagecolorallocatealpha($canvas, 0, 0, 0, 40);
    $white = imagecolorallocate($canvas, 255, 255, 255);

    // --- Top bar ---
    imagefilledrectangle($canvas, 0, 0, BARS_OG_WIDTH - 1, $top_bar_h - 1, $overlay);

    // Logo in top-left
    $logo_right_edge = $pad_x; // fallback if no logo
    $logo_path = get_template_directory() . '/resources/bars_logo_dark.png';
    if (file_exists($logo_path)) {
        $logo = imagecreatefrompng($logo_path);
        if ($logo) {
            imagealphablending($logo, true);
            $logo_src_w = imagesx($logo);
            $logo_src_h = imagesy($logo);
            // Scale logo to fit within top bar height with padding
            $logo_max_h = $top_bar_h - 16; // 8px padding top+bottom
            $logo_scale = $logo_max_h / $logo_src_h;
            $logo_dst_w = (int) round($logo_src_w * $logo_scale);
            $logo_dst_h = (int) round($logo_src_h * $logo_scale);
            $logo_y = (int) (($top_bar_h - $logo_dst_h) / 2);
            imagecopyresampled($canvas, $logo, $pad_x, $logo_y, 0, 0, $logo_dst_w, $logo_dst_h, $logo_src_w, $logo_src_h);
            imagedestroy($logo);
            $logo_right_edge = $pad_x + $logo_dst_w;
        }
    }

    // Edition title left-aligned after logo (e.g. "BARS XXVI")
    $type = get_post_type($post);
    $edition_key = ($type === 'movieblock') ? '_movieblock_edition' : '_movie_edition';
    $edition_meta = get_post_meta($post->ID, $edition_key, true);
    $edition_title = '';
    if ($edition_meta) {
        $number = intval(str_replace('bars', '', $edition_meta));
        if ($number) {
            $edition = Editions::getByNumber($number);
            if ($edition) {
                $edition_title = Editions::getTitle($edition);
            }
        }
    }
    if (!$edition_title) {
        $edition_title = Editions::getTitle();
    }

    $edition_size = 28;
    $bbox = imagettfbbox($edition_size, 0, $font_bebas, $edition_title);
    $edition_text_h = abs($bbox[7] - $bbox[1]);
    $edition_x = $logo_right_edge + 12;
    $edition_y = (int) (($top_bar_h + $edition_text_h) / 2);
    imagettftext($canvas, $edition_size, 0, $edition_x, $edition_y, $white, $font_bebas, $edition_title);

    // Category badge (pill shape, right-aligned in the top bar)
    $section_key = ($type === 'movieblock') ? '_movieblock_section' : '_movie_section';
    $section_id = get_post_meta($post->ID, $section_key, true);
    if ($section_id && function_exists('getMovieSectionLabel')) {
        $section_label = getMovieSectionLabel($section_id);
        if ($section_label) {
            $section_label = mb_strtoupper($section_label);
            $badge_font_size = 16;
            $badge_pad_x = 12;
            $badge_pad_y = 6;
            $badge_radius = 6;

            // Measure text
            $bbox_badge = imagettfbbox($badge_font_size, 0, $font_inter, $section_label);
            $badge_text_w = abs($bbox_badge[2] - $bbox_badge[0]);
            $badge_text_h = abs($bbox_badge[7] - $bbox_badge[1]);
            $badge_text_ascent = abs($bbox_badge[7]);

            // Badge rect dimensions
            $badge_w = $badge_text_w + (2 * $badge_pad_x);
            $badge_h = $badge_text_h + (2 * $badge_pad_y);

            // Position: right-aligned at pad_x from right edge, vertically centred in top bar
            $badge_x2 = BARS_OG_WIDTH - $pad_x;
            $badge_x1 = $badge_x2 - $badge_w;
            $badge_y1 = (int) (($top_bar_h - $badge_h) / 2);
            $badge_y2 = $badge_y1 + $badge_h;

            // Badge background: rgba(139,0,0,0.27) → GD alpha = 127 * (1 - 0.27) ≈ 93
            $badge_bg = imagecolorallocatealpha($canvas, 139, 0, 0, 93);
            bars_og_draw_rounded_rect($canvas, $badge_x1, $badge_y1, $badge_x2, $badge_y2, $badge_radius, $badge_bg);

            // Badge text: #D4726A
            $badge_text_color = imagecolorallocate($canvas, 212, 114, 106);
            $badge_text_x = $badge_x1 + $badge_pad_x;
            $badge_text_y = $badge_y1 + $badge_pad_y + $badge_text_ascent;
            imagettftext($canvas, $badge_font_size, 0, $badge_text_x, $badge_text_y, $badge_text_color, $font_inter, $section_label);
        }
    }

    // --- Bottom section: movie title bar ---
    $bottom_max_w = BARS_OG_WIDTH - (2 * $pad_x);

    // Get movie title (mixed case — not uppercased)
    $movie_title = '';
    if ($type === 'movieblock') {
        $movie_title = get_post_meta($post->ID, '_movieblock_name', true);
    }
    if (!$movie_title) {
        $movie_title = get_the_title($post->ID);
    }

    // Word-wrap movie title with auto-shrinking font
    $title_size = 38;
    $title_min_size = 22;
    $title_max_lines = 2;
    $title_line_spacing = 1.25; // line-height multiplier
    $title_pad_y = 20;         // vertical padding inside the bar

    $title_lines = array();
    $current_title_size = $title_size;
    while ($current_title_size >= $title_min_size) {
        $title_lines = bars_og_word_wrap($movie_title, $font_cormorant, $current_title_size, $bottom_max_w, $title_max_lines);
        // If it fits in max_lines without truncation, or we're at min size, use it
        $words_in_title = count(explode(' ', $movie_title));
        $words_in_result = 0;
        foreach ($title_lines as $line) {
            $words_in_result += count(explode(' ', rtrim($line, '.')));
        }
        if (count($title_lines) <= $title_max_lines) {
            break;
        }
        $current_title_size -= 1;
    }

    // Measure line height from the chosen font size
    $bbox_sample = imagettfbbox($current_title_size, 0, $font_cormorant, 'Áy');
    $title_line_h = (int) round(abs($bbox_sample[7] - $bbox_sample[1]) * $title_line_spacing);
    $title_ascent = abs($bbox_sample[7]);

    // Calculate title bar height
    $line_count = count($title_lines);
    $title_bar_h = (($line_count + 1) * $title_line_h) + (2 * $title_pad_y);
    $title_bar_top = BARS_OG_HEIGHT - $title_bar_h;

    // Draw title bar (semi-transparent black, full width)
    imagefilledrectangle($canvas, 0, $title_bar_top, BARS_OG_WIDTH - 1, BARS_OG_HEIGHT - 1, $overlay);

    // Render each title line
    for ($i = 0; $i < $line_count; $i++) {
        $line_y = $title_bar_top + $title_pad_y + $title_ascent + ($i * $title_line_h);
        imagettftext($canvas, $current_title_size, 0, $pad_x, $line_y, $white, $font_cormorant, $title_lines[$i]);
    }

    // Save
    $result = imagejpeg($canvas, $cache_path, BARS_OG_JPEG_QUALITY);
    imagedestroy($canvas);

    return $result ? $cache_path : false;
}

// --------------------------------------------------------------------------
// Public API
// --------------------------------------------------------------------------

/**
 * Purge the entire OG image cache when the festival edition changes.
 *
 * Compares a stored edition number in og-cache/.edition against the
 * current edition. If they differ (or the marker is missing), all
 * cached JPEGs are deleted and the marker is updated. Runs once —
 * on the first request after the edition changes.
 */
function bars_og_check_edition() {
    $cache_dir = bars_og_get_cache_dir();
    if (!$cache_dir) {
        return;
    }
    $marker = $cache_dir . '/.edition';
    $current = Editions::current();
    $current_number = $current['number'];

    $stored = file_exists($marker) ? trim(file_get_contents($marker)) : '';
    if ($stored === (string) $current_number) {
        return;
    }

    // Edition changed — purge all cached images
    $files = glob($cache_dir . '/*.jpg');
    if ($files) {
        foreach ($files as $file) {
            @unlink($file);
        }
    }
    @file_put_contents($marker, $current_number);
}

/**
 * Get or generate a branded OG image for a movie/movieblock post.
 *
 * @param WP_Post $post Movie or movieblock post.
 * @return array|false Array with 'url', 'width', 'height' keys, or false on failure.
 */
function bars_og_get_or_generate($post) {
    bars_og_check_edition();
    $cache_path = bars_og_get_cache_path($post);
    if (!$cache_path) {
        return false;
    }

    if (file_exists($cache_path)) {
        return array(
            'url' => bars_og_get_cache_url($post),
            'width' => BARS_OG_WIDTH,
            'height' => BARS_OG_HEIGHT,
        );
    }

    $generated = bars_og_generate($post);
    if (!$generated) {
        return false;
    }

    return array(
        'url' => bars_og_get_cache_url($post),
        'width' => BARS_OG_WIDTH,
        'height' => BARS_OG_HEIGHT,
    );
}

// --------------------------------------------------------------------------
// Cache invalidation
// --------------------------------------------------------------------------

/**
 * Delete cached OG image when a movie/movieblock is saved.
 *
 * @param int $post_id
 */
function bars_og_invalidate_cache($post_id) {
    // Skip autosaves and revisions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (wp_is_post_revision($post_id)) {
        return;
    }

    $post = get_post($post_id);
    if (!$post) {
        return;
    }

    $cache_path = bars_og_get_cache_path($post);
    if ($cache_path && file_exists($cache_path)) {
        @unlink($cache_path);
    }
}
add_action('save_post_movie', 'bars_og_invalidate_cache');
add_action('save_post_movieblock', 'bars_og_invalidate_cache');
