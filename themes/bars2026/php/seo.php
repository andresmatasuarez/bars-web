<?php
/**
 * SEO: meta descriptions, Open Graph, Twitter Cards, canonical URLs, JSON-LD, robots.txt
 *
 * @package BARS2026
 */

/**
 * Truncate text to a word boundary, stripping tags and collapsing whitespace.
 */
function bars_seo_truncate($text, $limit = 155) {
    $text = wp_strip_all_tags($text);
    $text = preg_replace('/\s+/', ' ', trim($text));
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    $truncated = mb_substr($text, 0, $limit);
    $last_space = mb_strrpos($truncated, ' ');
    if ($last_space !== false) {
        $truncated = mb_substr($truncated, 0, $last_space);
    }
    return $truncated . '...';
}

/**
 * Look up a movie or movieblock post from the ?f=<slug> query param.
 * Returns the WP_Post or null if not found / not on the programacion page.
 */
function bars_seo_get_movie_from_param() {
    static $cache = 'NOT_QUERIED';
    if ($cache !== 'NOT_QUERIED') {
        return $cache;
    }
    if (!is_page('programacion')) {
        $cache = null;
        return null;
    }
    $slug = isset($_GET['f']) ? sanitize_title($_GET['f']) : '';
    if (!$slug) {
        $cache = null;
        return null;
    }
    $post = get_page_by_path($slug, OBJECT, array('movie', 'movieblock'));
    $cache = $post ? $post : null;
    return $cache;
}

/**
 * Look up a jury post from the ?j=<slug> query param.
 * Returns the WP_Post or null if not found / not on the premios page.
 */
function bars_seo_get_jury_from_param() {
    static $cache = 'NOT_QUERIED';
    if ($cache !== 'NOT_QUERIED') {
        return $cache;
    }
    if (!is_page('premios')) {
        $cache = null;
        return null;
    }
    $slug = isset($_GET['j']) ? sanitize_title($_GET['j']) : '';
    if (!$slug) {
        $cache = null;
        return null;
    }
    $post = get_page_by_path($slug, OBJECT, 'jury');
    $cache = $post ? $post : null;
    return $cache;
}

/**
 * Get the current page's SEO title.
 */
function bars_seo_get_title() {
    $movie_post = bars_seo_get_movie_from_param();
    if ($movie_post) {
        $type = get_post_type($movie_post);
        if ($type === 'movie') {
            $directors = get_post_meta($movie_post->ID, '_movie_directors', true);
            $title = get_the_title($movie_post->ID);
            return $directors ? $title . ' - ' . $directors : $title;
        }
        return get_post_meta($movie_post->ID, '_movieblock_name', true) ?: get_the_title($movie_post->ID);
    }

    $jury_post = bars_seo_get_jury_from_param();
    if ($jury_post) {
        return get_post_meta($jury_post->ID, '_jury_name', true) ?: get_the_title($jury_post->ID);
    }

    if (is_front_page()) {
        return 'Buenos Aires Rojo Sangre - Festival Internacional de Cine de Terror, Fantasía y Bizarro';
    }

    if (is_singular('movie')) {
        $directors = get_post_meta(get_the_ID(), '_movie_directors', true);
        $title = get_the_title();
        return $directors ? $title . ' - ' . $directors : $title;
    }

    if (is_singular('movieblock')) {
        return get_post_meta(get_the_ID(), '_movieblock_name', true) ?: get_the_title();
    }

    if (is_singular()) {
        return get_the_title();
    }

    return get_bloginfo('name');
}

/**
 * Get the current page's meta description.
 */
function bars_seo_get_description() {
    $movie_post = bars_seo_get_movie_from_param();
    if ($movie_post) {
        $type = get_post_type($movie_post);
        if ($type === 'movie') {
            $synopsis = get_post_meta($movie_post->ID, '_movie_synopsis', true);
            if ($synopsis) {
                return bars_seo_truncate($synopsis);
            }
            return bars_seo_truncate(get_the_title($movie_post->ID) . ' - Buenos Aires Rojo Sangre');
        }
        // movieblock
        $shorts = getShortsForBlock($movie_post->ID);
        if (!empty($shorts)) {
            $titles = array_map(function($s) { return $s['title']; }, $shorts);
            return bars_seo_truncate('Bloque de cortometrajes: ' . implode(', ', $titles));
        }
        $name = get_post_meta($movie_post->ID, '_movieblock_name', true);
        return $name ? bars_seo_truncate($name) : '';
    }

    $jury_post = bars_seo_get_jury_from_param();
    if ($jury_post) {
        $desc = get_post_meta($jury_post->ID, '_jury_description', true);
        if ($desc) {
            return bars_seo_truncate($desc);
        }
        $name = get_post_meta($jury_post->ID, '_jury_name', true) ?: get_the_title($jury_post->ID);
        return bars_seo_truncate($name . ' - Jurado del Festival Buenos Aires Rojo Sangre');
    }

    // Front page
    if (is_front_page()) {
        return 'Festival Internacional de Cine de Terror, Fantasía y Bizarro';
    }

    // Single movie
    if (is_singular('movie')) {
        $synopsis = get_post_meta(get_the_ID(), '_movie_synopsis', true);
        if ($synopsis) {
            return bars_seo_truncate($synopsis);
        }
        return bars_seo_truncate(get_the_title() . ' - Buenos Aires Rojo Sangre');
    }

    // Single movieblock
    if (is_singular('movieblock')) {
        $shorts = getShortsForBlock(get_the_ID());
        if (!empty($shorts)) {
            $titles = array_map(function($s) { return $s['title']; }, $shorts);
            return bars_seo_truncate('Bloque de cortometrajes: ' . implode(', ', $titles));
        }
        $name = get_post_meta(get_the_ID(), '_movieblock_name', true);
        return $name ? bars_seo_truncate($name) : '';
    }

    // Single post (news article)
    if (is_singular('post')) {
        $excerpt = get_the_excerpt();
        if ($excerpt) {
            return bars_seo_truncate($excerpt);
        }
        return bars_seo_truncate(get_the_content());
    }

    // Static pages — per-slug descriptions
    if (is_page()) {
        $slug = get_post_field('post_name', get_the_ID());
        $descriptions = array(
            'noticias'     => 'Noticias y novedades del Festival Buenos Aires Rojo Sangre.',
            'programacion' => 'Programación completa del Festival Buenos Aires Rojo Sangre: películas, horarios y sedes.',
            'premios'      => 'Premios y jurados del Festival Buenos Aires Rojo Sangre.',
            'convocatoria' => 'Convocatoria para películas del Festival Buenos Aires Rojo Sangre. Bases, plazos e inscripción.',
            'prensa'       => 'Acreditación de prensa para el Festival Buenos Aires Rojo Sangre.',
            'festival'     => 'Historia y trayectoria del Festival Buenos Aires Rojo Sangre, el festival de cine de género más importante de Argentina.',
        );
        if (isset($descriptions[$slug])) {
            return $descriptions[$slug];
        }
    }

    // Fallback
    $tagline = get_bloginfo('description');
    return $tagline ?: '';
}

/**
 * Get the OG image URL + dimensions for the current page.
 *
 * Returns array('url' => string, 'width' => int|0, 'height' => int|0).
 */
function bars_seo_get_image_meta() {
    $empty = array('url' => '', 'width' => 0, 'height' => 0);

    // Helper: build meta from a post thumbnail attachment
    $from_thumbnail = function($post_id) {
        $thumb_id = get_post_thumbnail_id($post_id);
        if (!$thumb_id) {
            return null;
        }
        $src = wp_get_attachment_image_src($thumb_id, 'large');
        if ($src) {
            return array('url' => $src[0], 'width' => (int) $src[1], 'height' => (int) $src[2]);
        }
        return null;
    };

    $movie_post = bars_seo_get_movie_from_param();
    if ($movie_post) {
        $meta = $from_thumbnail($movie_post->ID);
        if ($meta) {
            return $meta;
        }
        if (get_post_type($movie_post) === 'movie') {
            $poster = get_post_meta($movie_post->ID, '_movie_poster', true);
            if ($poster) {
                return array('url' => $poster, 'width' => 0, 'height' => 0);
            }
        }
    }

    $jury_post = bars_seo_get_jury_from_param();
    if ($jury_post) {
        $meta = $from_thumbnail($jury_post->ID);
        if ($meta) {
            return $meta;
        }
    }

    // Single post/movie — use post thumbnail
    if (is_singular()) {
        $meta = $from_thumbnail(get_the_ID());
        if ($meta) {
            return $meta;
        }

        // Movies: try _movie_poster meta
        if (get_post_type() === 'movie') {
            $poster = get_post_meta(get_the_ID(), '_movie_poster', true);
            if ($poster) {
                return array('url' => $poster, 'width' => 0, 'height' => 0);
            }
        }
    }

    // Fallback: current edition poster
    $edition = Editions::current();
    if (!empty($edition['poster'])) {
        return array('url' => get_template_directory_uri() . '/' . $edition['poster'], 'width' => 0, 'height' => 0);
    }

    return $empty;
}

/**
 * Get the OG image URL for the current page (convenience wrapper).
 */
function bars_seo_get_image() {
    $meta = bars_seo_get_image_meta();
    return $meta['url'];
}

/**
 * Get the canonical URL for the current page.
 */
function bars_seo_get_canonical() {
    // Movie modal URLs: canonical stays at /programacion (not indexed individually)
    if (bars_seo_get_movie_from_param()) {
        return home_url('/programacion');
    }

    // Jury modal URLs: canonical stays at /premios
    if (bars_seo_get_jury_from_param()) {
        return home_url('/premios');
    }

    if (is_front_page()) {
        return home_url('/');
    }

    if (is_singular()) {
        return get_permalink();
    }

    // News archive page
    if (is_home()) {
        return home_url('/noticias');
    }

    // Edition URLs: canonical is the base page without the edition param
    if ((is_page('programacion') || is_page('premios')) && isset($_GET['e'])) {
        $slug = get_post_field('post_name', get_the_ID());
        return home_url('/' . $slug);
    }

    return home_url($_SERVER['REQUEST_URI']);
}

/**
 * Get the og:url for the current page.
 *
 * Unlike the canonical URL (which always points to the parent page for SEO),
 * og:url includes ?f= / ?j= params so social media crawlers cache each
 * movie/jury share independently. Also includes ?e=<number> when the post
 * belongs to a past edition.
 */
function bars_seo_get_og_url() {
    $movie_post = bars_seo_get_movie_from_param();
    if ($movie_post) {
        $slug = get_post_field('post_name', $movie_post->ID);
        $params = 'f=' . urlencode($slug);

        // Auto-detect past editions
        $edition_meta = get_post_meta($movie_post->ID, '_movie_edition', true);
        if (!$edition_meta) {
            $edition_meta = get_post_meta($movie_post->ID, '_movieblock_edition', true);
        }
        if ($edition_meta) {
            $edition_number = intval(str_replace('bars', '', $edition_meta));
            $current = Editions::current();
            if ($edition_number && $edition_number !== $current['number']) {
                $params .= '&e=' . $edition_number;
            }
        }

        return home_url('/programacion?' . $params);
    }

    $jury_post = bars_seo_get_jury_from_param();
    if ($jury_post) {
        $slug = get_post_field('post_name', $jury_post->ID);
        $params = 'j=' . urlencode($slug);

        // Auto-detect past editions
        $edition_meta = get_post_meta($jury_post->ID, '_jury_edition', true);
        if ($edition_meta) {
            $edition_number = intval(str_replace('bars', '', $edition_meta));
            $current = Editions::current();
            if ($edition_number && $edition_number !== $current['number']) {
                $params .= '&e=' . $edition_number;
            }
        }

        return home_url('/premios?' . $params);
    }

    return bars_seo_get_canonical();
}

/**
 * Get the OG type for the current page.
 */
function bars_seo_get_og_type() {
    if (bars_seo_get_movie_from_param()) {
        return 'video.movie';
    }
    if (bars_seo_get_jury_from_param()) {
        return 'profile';
    }
    if (is_front_page()) {
        return 'website';
    }
    if (is_singular('movie') || is_singular('movieblock')) {
        return 'video.movie';
    }
    if (is_singular('post')) {
        return 'article';
    }
    return 'website';
}

// ---------------------------------------------------------------------------
// wp_head hooks
// ---------------------------------------------------------------------------

/**
 * Override the <title> tag on WP 4.4+ so it reflects movie/jury context.
 * On WP 3.9, the header.php fallback handles this instead.
 */
add_filter('pre_get_document_title', function($title) {
    $seo_title = bars_seo_get_title();
    if ($seo_title) {
        return $seo_title . ' – ' . get_bloginfo('name');
    }
    return $title;
});

/**
 * Output meta description.
 */
function bars_seo_meta_description() {
    $description = bars_seo_get_description();
    if ($description) {
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }
}
add_action('wp_head', 'bars_seo_meta_description', 1);

/**
 * Output noindex for edition-parameterized URLs (?e=N).
 * The canonical URL for the current edition is the one without any query param.
 */
function bars_seo_noindex_past_editions() {
    if (!is_page('programacion') && !is_page('premios')) {
        return;
    }
    if (!isset($_GET['e'])) {
        return;
    }
    echo '<meta name="robots" content="noindex, follow">' . "\n";
}
add_action('wp_head', 'bars_seo_noindex_past_editions', 1);

/**
 * Output Open Graph tags.
 */
function bars_seo_open_graph() {
    $title       = bars_seo_get_title();
    $description = bars_seo_get_description();
    $url         = bars_seo_get_og_url();
    $type        = bars_seo_get_og_type();
    $image_meta  = bars_seo_get_image_meta();

    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr($type) . '">' . "\n";
    echo '<meta property="og:site_name" content="Buenos Aires Rojo Sangre">' . "\n";
    echo '<meta property="og:locale" content="es_AR">' . "\n";
    if ($image_meta['url']) {
        // Ensures og:image scheme matches the page (http vs https). Can remove if WP config uses correct scheme for uploads.
        $image_url = set_url_scheme($image_meta['url']);
        echo '<meta property="og:image" content="' . esc_url($image_url) . '">' . "\n";
        if ($image_meta['width']) {
            echo '<meta property="og:image:width" content="' . intval($image_meta['width']) . '">' . "\n";
            echo '<meta property="og:image:height" content="' . intval($image_meta['height']) . '">' . "\n";
        }
    }
}
add_action('wp_head', 'bars_seo_open_graph', 2);

/**
 * Output Twitter Card tags.
 */
function bars_seo_twitter_card() {
    $title       = bars_seo_get_title();
    $description = bars_seo_get_description();
    $image       = bars_seo_get_image();

    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
    if ($image) {
        // Ensures twitter:image scheme matches the page (http vs https). Can remove if WP config uses correct scheme for uploads.
        $image = set_url_scheme($image);
        echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
    }
}
add_action('wp_head', 'bars_seo_twitter_card', 3);

/**
 * Output canonical URL.
 */
function bars_seo_canonical() {
    $url = bars_seo_get_canonical();
    if ($url) {
        echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
    }
}
add_action('wp_head', 'bars_seo_canonical', 4);

// Prevents WP core from adding a duplicate canonical. Review after WP upgrade — may still be needed.
remove_action('wp_head', 'rel_canonical');

/**
 * Output JSON-LD structured data.
 */
function bars_seo_jsonld() {
    $schemas = array();

    // Organization — present on every page
    $schemas[] = array(
        '@type' => 'Organization',
        'name' => 'Buenos Aires Rojo Sangre',
        'url' => home_url('/'),
        'logo' => get_template_directory_uri() . '/resources/bars_logo.png',
        'sameAs' => array(
            'https://www.facebook.com/buenosairesrojosangre',
            'https://www.instagram.com/festivalrojosangre/',
            'https://www.youtube.com/user/rojosangrefestival',
            'https://x.com/rojosangre',
        ),
    );

    // Front page: WebSite + Event
    if (is_front_page()) {
        $schemas[] = array(
            '@type' => 'WebSite',
            'name' => 'Buenos Aires Rojo Sangre',
            'url' => home_url('/'),
        );

        $edition = Editions::current();
        $from = Editions::from($edition);
        $to = Editions::to($edition);

        $event = array(
            '@type' => 'Event',
            'name' => Editions::getTitle($edition) . ' - Buenos Aires Rojo Sangre',
            'url' => home_url('/'),
        );

        if ($from) {
            $event['startDate'] = $from->format('Y-m-d');
        }
        if ($to) {
            $event['endDate'] = $to->format('Y-m-d');
        }

        if (!empty($edition['poster'])) {
            $event['image'] = get_template_directory_uri() . '/' . $edition['poster'];
        }

        $venues = Editions::venues($edition);
        if ($venues) {
            $event_locations = array();
            foreach ($venues as $venue) {
                if (!empty($venue['online'])) {
                    continue;
                }
                $event_locations[] = array(
                    '@type' => 'Place',
                    'name' => $venue['name'],
                    'address' => $venue['address'],
                );
            }
            if (count($event_locations) === 1) {
                $event['location'] = $event_locations[0];
            } elseif (count($event_locations) > 1) {
                $event['location'] = $event_locations;
            }
        }

        $schemas[] = $event;
    }

    // Single post: NewsArticle
    if (is_singular('post')) {
        $article = array(
            '@type' => 'NewsArticle',
            'headline' => get_the_title(),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Organization',
                'name' => 'Buenos Aires Rojo Sangre',
            ),
        );
        $thumb = get_the_post_thumbnail_url(get_the_ID(), 'large');
        if ($thumb) {
            $article['image'] = $thumb;
        }
        $schemas[] = $article;
    }

    // Single movie: Movie
    if (is_singular('movie')) {
        $movie = array(
            '@type' => 'Movie',
            'name' => get_the_title(),
        );
        $directors = get_post_meta(get_the_ID(), '_movie_directors', true);
        if ($directors) {
            $movie['director'] = array(
                '@type' => 'Person',
                'name' => $directors,
            );
        }
        $country = get_post_meta(get_the_ID(), '_movie_country', true);
        if ($country) {
            $movie['countryOfOrigin'] = $country;
        }
        $runtime = get_post_meta(get_the_ID(), '_movie_runtime', true);
        if ($runtime) {
            $movie['duration'] = 'PT' . intval($runtime) . 'M';
        }
        $synopsis = get_post_meta(get_the_ID(), '_movie_synopsis', true);
        if ($synopsis) {
            $movie['description'] = bars_seo_truncate($synopsis, 300);
        }
        $year = get_post_meta(get_the_ID(), '_movie_year', true);
        if ($year) {
            $movie['dateCreated'] = $year;
        }
        $schemas[] = $movie;
    }

    // Single movieblock: Movie (compilation)
    if (is_singular('movieblock')) {
        $block = array(
            '@type' => 'Movie',
            'name' => get_post_meta(get_the_ID(), '_movieblock_name', true) ?: get_the_title(),
        );
        $runtime = get_post_meta(get_the_ID(), '_movieblock_runtime', true);
        if ($runtime) {
            $block['duration'] = 'PT' . intval($runtime) . 'M';
        }
        $schemas[] = $block;
    }

    // Movie/movieblock from ?f= param (programacion page)
    $movie_param = bars_seo_get_movie_from_param();
    if ($movie_param) {
        $param_type = get_post_type($movie_param);
        if ($param_type === 'movie') {
            $movie = array(
                '@type' => 'Movie',
                'name' => get_the_title($movie_param->ID),
            );
            $directors = get_post_meta($movie_param->ID, '_movie_directors', true);
            if ($directors) {
                $movie['director'] = array(
                    '@type' => 'Person',
                    'name' => $directors,
                );
            }
            $country = get_post_meta($movie_param->ID, '_movie_country', true);
            if ($country) {
                $movie['countryOfOrigin'] = $country;
            }
            $runtime = get_post_meta($movie_param->ID, '_movie_runtime', true);
            if ($runtime) {
                $movie['duration'] = 'PT' . intval($runtime) . 'M';
            }
            $synopsis = get_post_meta($movie_param->ID, '_movie_synopsis', true);
            if ($synopsis) {
                $movie['description'] = bars_seo_truncate($synopsis, 300);
            }
            $year = get_post_meta($movie_param->ID, '_movie_year', true);
            if ($year) {
                $movie['dateCreated'] = $year;
            }
            $schemas[] = $movie;
        } elseif ($param_type === 'movieblock') {
            $block = array(
                '@type' => 'Movie',
                'name' => get_post_meta($movie_param->ID, '_movieblock_name', true) ?: get_the_title($movie_param->ID),
            );
            $runtime = get_post_meta($movie_param->ID, '_movieblock_runtime', true);
            if ($runtime) {
                $block['duration'] = 'PT' . intval($runtime) . 'M';
            }
            $schemas[] = $block;
        }
    }

    // Jury from ?j= param (premios page)
    $jury_param = bars_seo_get_jury_from_param();
    if ($jury_param) {
        $person = array(
            '@type' => 'Person',
            'name' => get_post_meta($jury_param->ID, '_jury_name', true) ?: get_the_title($jury_param->ID),
        );
        $desc = get_post_meta($jury_param->ID, '_jury_description', true);
        if ($desc) {
            $person['description'] = bars_seo_truncate($desc, 300);
        }
        $thumb = get_the_post_thumbnail_url($jury_param->ID, 'large');
        if ($thumb) {
            $person['image'] = $thumb;
        }
        $schemas[] = $person;
    }

    // Output
    $jsonld = array(
        '@context' => 'https://schema.org',
        '@graph' => $schemas,
    );

    echo '<script type="application/ld+json">' . "\n";
    echo json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    echo "\n" . '</script>' . "\n";
}
add_action('wp_head', 'bars_seo_jsonld', 5);

// ---------------------------------------------------------------------------
// Redirect standalone movie/movieblock/jury URLs to their modal pages
// ---------------------------------------------------------------------------

add_action('template_redirect', function() {
    if (is_singular('movie') || is_singular('movieblock')) {
        $slug = get_post_field('post_name', get_the_ID());
        wp_redirect(home_url('/programacion?f=' . $slug), 301);
        exit;
    }
    if (is_singular('jury')) {
        $slug = get_post_field('post_name', get_the_ID());
        wp_redirect(home_url('/premios?j=' . $slug), 301);
        exit;
    }
});

// ---------------------------------------------------------------------------
// robots.txt
// ---------------------------------------------------------------------------

/**
 * Add Sitemap directive to robots.txt (only if not already present).
 */
function bars_seo_robots_txt($output) {
    $sitemap_url = home_url('/wp-sitemap.xml');
    if (strpos($output, $sitemap_url) === false) {
        $output .= "\nSitemap: " . $sitemap_url . "\n";
    }
    return $output;
}
add_filter('robots_txt', 'bars_seo_robots_txt');
