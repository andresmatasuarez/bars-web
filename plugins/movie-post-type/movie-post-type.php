<?php
/*
	Plugin Name: Movie Post Type
	Description: Movie post type for BARS wordpress template. Tutorial from http://wp.tutsplus.com/tutorials/plugins/a-guide-to-wordpress-custom-post-types-creation-display-and-meta-boxes/.
	Version: 1.0
	Author: Andrés Mata Suárez
	License: GPLv2
*/

	add_action('admin_init', 'admin_load_scripts');
	function admin_load_scripts() {
		$admin_movie_file = plugins_url( 'admin-movie.js', __FILE__ );
		$admin_movie_block_file = plugins_url( 'admin-movie-block.js', __FILE__ );

		wp_enqueue_script('admin-movie', $admin_movie_file, array('jquery'));
		wp_enqueue_script('admin-movie-block', $admin_movie_block_file, array('jquery'));

		if (!class_exists('Editions')) {
			$path = get_template_directory() . '/editions.php';
			if (file_exists($path)) require_once $path;
		}

		if (class_exists('Editions')) {
			$venuesByEdition = array();
			foreach (Editions::all() as $ed) {
				$venuesByEdition['bars' . $ed['number']] = isset($ed['venues']) ? $ed['venues'] : array();
			}
			wp_localize_script('admin-movie', 'BARS_SCREENINGS', array(
				'venuesByEdition' => $venuesByEdition
			));
		}
	}

	/************************************************************************************************/

	/* ***** ACTIONS ***** */
	add_action('init', 'register_movie_post_type' );
	add_action('add_meta_boxes', 'add_movie_meta_box');
	add_action('save_post', 'save_movie');

	add_action('init', 'register_movieblock_post_type' );
	add_action('add_meta_boxes', 'add_movieblock_meta_box');
	add_action('save_post', 'save_movieblock');

	add_action('save_post_movie', 'invalidateFestivalMetricsCache');
	add_action('save_post_movieblock', 'invalidateFestivalMetricsCache');

	add_action('init', 'initialize');

	$MOVIE_SECTIONS = array (
		// Bars 2013
		'opening'                             => array ( 'label' => 'Función de apertura',         'value' => 'opening' ),
		'shortFilm'                           => array ( 'label' => 'Cortos fuera de competencia', 'value' => 'shortFilm' ),
		'shortFilmCompetition'                => array ( 'label' => 'Cortos en competencia',       'value' => 'shortFilmCompetition' ),
		'mediumLengthFilm'                    => array ( 'label' => 'Mediometrajes',               'value' => 'mediumLengthFilm' ),
		'internationalFeatureFilmCompetition' => array ( 'label' => 'Competencia Internacional',   'value' => 'internationalFeatureFilmCompetition' ),
		'iberoamericanFeatureFilmCompetition' => array ( 'label' => 'Competencia Iberoamericana',  'value' => 'iberoamericanFeatureFilmCompetition' ),
		'releases'                            => array ( 'label' => 'Novedades',                   'value' => 'releases' ),
		'anioVerde'                           => array ( 'label' => 'Argentina Año Verde',         'value' => 'anioVerde' ),
		'herederosDelTerror'                  => array ( 'label' => 'Herederos Del Teror',         'value' => 'herederosDelTerror' ),
		'laCripta'                            => array ( 'label' => 'La Cripta',                   'value' => 'laCripta' ),
		'documentary'                         => array ( 'label' => 'Documental',                  'value' => 'documentary' ),
		'imperdibles'                         => array ( 'label' => 'Imperdibles',                 'value' => 'imperdibles' ),

		// Bars 2013-only sections
		'raroVhs'                             => array ( 'label' => 'Raro VHS: Tapes Rojo Sangre', 'value' => 'raroVhs' ),
		'filmotecaPresenta'                   => array ( 'label' => 'Filmoteca Presenta',          'value' => 'filmotecaPresenta' ),
		'sangreSudorYLagrimas'                => array ( 'label' => 'Sangre, Sudor y Lágrimas',    'value' => 'sangreSudorYLagrimas' ),

		// Bars 2014 new sections
		'bizarreCompetition' => array ( 'label' => 'Competencia Bizarra', 'value' => 'bizarreCompetition' ),
		'lastPage'           => array ( 'label' => 'Última página',       'value' => 'lastPage' ),
		'freakingNazis'      => array ( 'label' => 'Jodidos Nazis',       'value' => 'freakingNazis' ),
		'closingFilm'        => array ( 'label' => 'Película de cierre',  'value' => 'closingFilm' ),

		// Bars 2015 new sections
		'argentinianFeatureFilmCompetition' => array ( 'label' => 'Competencia Argentina', 'value' => 'argentinianFeatureFilmCompetition' ),
		'reposiciones'                      => array ( 'label' => 'Reposiciones',          'value' => 'reposiciones' ),

		// Bars 2016 new sections
		'deodatoTribute'    => array ( 'label' => 'Homenaje Deodato',     'value' => 'deodatoTribute' ),
		'specialScreenings' => array ( 'label' => 'Funciones especiales', 'value' => 'specialScreenings' ),

		// Bars 2017 new sections
		'bloodyWeekend' => array ( 'label' => 'Fin de semana sangriento', 'value' => 'bloodyWeekend' ),

		// Bars 2019 new sections
		'japaneseInvasion' => array ( 'label' => 'Invasión Japón', 'value' => 'japaneseInvasion' ),

		// Bars 2020 new sections
		'barsContarPrize' => array ( 'label' => 'Premio BARS/CONTAR', 'value' => 'barsContarPrize' ),
		'argentinianOutlook' => array ( 'label' => 'Panorama Argentino', 'value' => 'argentinianOutlook' ),
		'onlineActivities' => array ( 'label' => 'Actividades online', 'value' => 'onlineActivities' ),

		// Bars 2021 new sections
		'reaparecidos' => array ( 'label' => 'Reaparecidos', 'value' => 'reaparecidos' ),
		'madeInTaiwan' => array ( 'label' => 'Made in Taiwan', 'value' => 'madeInTaiwan' ),

		// Bars 2023 new sections
		'panoramaBizarro' => array ( 'label' => 'Panorama Bizarro', 'value' => 'panoramaBizarro' ),

		// Bars 2024 new sections
    'clasicosDeTerror' => array ( 'label' => 'Clásicos de terror', 'value' => 'clasicosDeTerror' ),
    'cortos25años' => array ( 'label' => 'Cortos BARS 25 años', 'value' => 'cortos25años' ),

    // Bars 2025 new sections
		'barsAnimado' => array ( 'label' => 'BARS Animado', 'value' => 'barsAnimado' ),
	);

	function initialize(){
		global $movieSections;
		global $movie_prefix;
		global $movie_fields;
		global $movieblock_prefix;
		global $movieblock_fields;
		global $movieBlocks;
		global $barscommons_editionOptions;
		global $MOVIE_SECTIONS;

		$movieSections = $MOVIE_SECTIONS;

		$currentEditionKey = reset($barscommons_editionOptions)['value']; // https://stackoverflow.com/a/1028677
		$movieBlocks = movieBlocks($currentEditionKey);

		/* ***** MOVIE FIELD DEFINITIONS ***** */
		$movie_prefix = '_movie_';
		$movie_fields = array(
			array(
				'id'    => $movie_prefix . 'edition',
				'label' => 'Edition',
				'type'  => 'select',
				'options' => $barscommons_editionOptions
			),
			array(
				'id'    => $movie_prefix . 'movieblock',
				'label' => 'Movie block',
				'type'  => 'select',
				'options' => $movieBlocks
			),
			array(
				'id'    => $movie_prefix . 'section',
				'label' => 'Section',
				'type'  => 'select',
				'options' => $movieSections
			),
			array(
				'id'    => $movie_prefix . 'name',
				'label' => 'Name',
				'type'  => 'text'
			),
			array(
				'id'    => $movie_prefix . 'country',
				'label' => 'Country',
				'type'  => 'text'
			),
			array(
				'id'    => $movie_prefix . 'year',
				'label' => 'Year',
				'type'  => 'text'
			),
			array(
				'id'    => $movie_prefix . 'directors',
				'label' => 'Directors',
				'desc' => 'Comma-separated',
				'type'  => 'text'
			),
			array(
				'id'    => $movie_prefix . 'cast',
				'label' => 'Cast',
				'desc' => 'Comma-separated',
				'type'  => 'text'
			),
			array(
				'id'    => $movie_prefix . 'runtime',
				'label' => 'Runtime',
				'desc' => 'Duration in minutes',
				'type'  => 'text'
			),
			array(
				'id'    => $movie_prefix . 'trailer',
				'label' => 'Trailer',
				'desc' => 'YouTube/Vimeo video URL',
				'type'  => 'text'
			),
				array(
				'id'    => $movie_prefix . 'screenings',
				'label' => 'Film screenings',
				'type'  => 'custom',
				'render' => 'render_screenings_field'
			),
			array(
				'id'    => $movie_prefix . 'streamingLink',
				'label' => 'Streaming link (for online editions only)',
				'type'  => 'text'
			),
			array(
				'id'    => $movie_prefix . 'synopsis',
				'label' => 'Synopsis',
				'type'  => 'textarea'
			),
			array(
				'id'    => $movie_prefix . 'comments',
				'label' => 'Comments',
				'type'  => 'textarea'
			)
		);

		$movieblock_prefix = '_movieblock_';
		$movieblock_fields = array(
			array(
				'id'    => $movieblock_prefix . 'edition',
				'label' => 'Edition',
				'type'  => 'select',
				'options' => $barscommons_editionOptions
			),
			array(
				'id'    => $movieblock_prefix . 'section',
				'label' => 'Section',
				'type'  => 'select',
				'options' => $movieSections
			),
			array(
				'id'    => $movieblock_prefix . 'name',
				'label' => 'Name',
				'type'  => 'text'
			),
			array(
				'id'    => $movieblock_prefix . 'runtime',
				'label' => 'Runtime',
				'desc' => 'Duration in minutes',
				'type'  => 'text'
			),
			array(
				'id'    => $movieblock_prefix . 'screenings',
				'label' => 'Film screenings',
				'type'  => 'custom',
				'render' => 'render_screenings_field'
			),
			array(
				'id'    => $movieblock_prefix . 'streamingLink',
				'label' => 'Streaming link (for online editions only)',
				'type'  => 'text'
			),
		);
	}

	/* ***** FUNCTIONS ***** */
	function render_screenings_field($post, $field, $meta) {
		echo '<input type="hidden" name="' . $field['id'] . '" id="' . $field['id']
			. '" value="' . esc_attr($meta) . '" />';
		echo '<div class="bars-screenings-repeater" data-field-id="' . $field['id'] . '"></div>';
	}

	function register_movie_post_type() {
		register_post_type( 'movie',
			array(
				'labels' => array(
					'name' => 'Movies',
					'singular_name' => 'Movie',
					'add_new' => 'Add New',
					'add_new_item' => 'Add New Movie',
					'edit' => 'Edit',
					'edit_item' => 'Edit Movie',
					'new_item' => 'New Movie',
					'view' => 'View',
					'view_item' => 'View Movie',
					'not_found' => 'No Movies found',
					'not_found_in_trash' => 'No Movie found in Trash',
					'parent' => 'Parent Movie'
				),

				'public' => true,
				'menu_position' => 15,
				'supports' => array( 'thumbnail' ),
				'taxonomies' => array( '' ),
				//'menu_icon' => plugins_url( 'images/image.png', __FILE__ ),
				'has_archive' => true,
				'capability_type' => 'post'
			)
		);
	}

	function register_movieblock_post_type() {
		register_post_type( 'movieblock',
			array(
				'labels' => array(
					'name' => 'Movie Block',
					'singular_name' => 'Movie Block',
					'add_new' => 'Add New',
					'add_new_item' => 'Add New Movie Block',
					'edit' => 'Edit',
					'edit_item' => 'Edit Movie Block',
					'new_item' => 'New Movie Block',
					'view' => 'View',
					'view_item' => 'View Movie Block',
					'search_items' => 'Search Movie Block',
					'not_found' => 'No Movie Block found',
					'not_found_in_trash' => 'No Movie Block found in Trash',
					'parent' => 'Parent Movie Block'
				),

				'public' => true,
				'menu_position' => 15,
				'supports' => array( 'thumbnail' ),
				'taxonomies' => array( '' ),
				//'menu_icon' => plugins_url( 'images/image.png', __FILE__ ),
				'has_archive' => true,
				'capability_type' => 'post'
			)
		);
	}

	// Add movie meta box.
	function add_movie_meta_box() {
		add_meta_box(
			'movie_meta_box', // $id
			'Movie Info', // $title
			'show_movie_meta_box', // $callback
			'movie', // $page
			'normal', // $context
			'high'); // $priority
	}

	// Add short film series meta box.
	function add_movieblock_meta_box() {
		add_meta_box(
			'movieblock_meta_box', // $id
			'Movie Block Info', // $title
			'show_movieblock_meta_box', // $callback
			'movieblock', // $page
			'normal', // $context
			'high'); // $priority
	}

	// Output movie meta box.
	function show_movie_meta_box($post) {
		global $movie_fields;
		barscommons_show_meta_box_inputs('movie_meta_box_nonce', basename(__FILE__), $post, $movie_fields);
	}

	// Output movieblock meta box.
	function show_movieblock_meta_box($post) {
		global $movieblock_fields;
		barscommons_show_meta_box_inputs('movieblock_meta_box_nonce', basename(__FILE__), $post, $movieblock_fields);
	}

	// Save movie data
	function save_movie($post_id) {
		global $movie_prefix;
		global $movie_fields;

		barscommons_save_custom_post(
			'movie',
			'save_movie',
			'movie_meta_box_nonce',
			basename(__FILE__),
			$post_id,
			$movie_prefix . 'name',
			$movie_fields
		);
	}

	// Save short film series data
	function save_movieblock($post_id) {
		global $movieblock_prefix;
		global $movieblock_fields;

		barscommons_save_custom_post(
			'movieblock',
			'save_movieblock',
			'movieblock_meta_box_nonce',
			basename(__FILE__),
			$post_id,
			$movieblock_prefix . 'name',
			$movieblock_fields
		);
	}

	function movieBlocks($editionKey){
		global $post;
		$query = new WP_Query(array(
			'post_type' => array( 'movieblock' ),
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(array(
			  'key'     => '_movieblock_edition',
			  'value'   => $editionKey,
			  'compare' => 'LIKE'
      ))
		));

		$movieBlocks = array();
		$movieBlocks[-1] = array('label' => 'Ninguno', 'value' => -1 );

		while ($query->have_posts()){
			$query->the_post();
			$movieBlockID = $post->ID;
			$movieBlockName = get_post_meta($post->ID, '_movieblock_name', true);

			$movieBlocks[$movieBlockID] = array('label' => strval($movieBlockName), 'value' => $movieBlockID);
		}

		// Restore global post data stomped by the_post().
		wp_reset_query();

		return $movieBlocks;
	}

	function getMovieEntriesQuery($withDayCondition = false) {
		global $wpdb;
		$movieDayCondition = $withDayCondition ? "AND m2.meta_key = '_movie_screenings' AND m2.meta_value LIKE '%s'" : "";
		$movieBlockDayCondition = $withDayCondition ? "AND m2.meta_key = '_movieblock_screenings' AND m2.meta_value LIKE '%s'" : "";
		return "
			(
				SELECT	post.ID, post.post_author, post.post_date, post.post_content
				FROM	{$wpdb->prefix}posts post
						INNER JOIN {$wpdb->prefix}postmeta m1 ON post.ID = m1.post_id
						INNER JOIN {$wpdb->prefix}postmeta m2 ON post.ID = m2.post_id
						INNER JOIN {$wpdb->prefix}postmeta m3 ON post.ID = m3.post_id
				WHERE	post.post_status = 'publish' AND post.post_type = 'movie'
						AND m1.meta_key = '_movie_edition' AND m1.meta_value = %s
						{$movieDayCondition}
						AND (
								'_movie_movieblock' NOT IN (
									SELECT DISTINCT m4.meta_key
									FROM {$wpdb->prefix}postmeta m4
									WHERE post.ID = m4.post_id
								) OR ( m3.meta_key = '_movie_movieblock' AND m3.meta_value = -1)
							)
				GROUP BY post.ID
			)
			UNION
			(
				SELECT	post.ID, post.post_author, post.post_date, post.post_content
				FROM	{$wpdb->prefix}posts post
						INNER JOIN {$wpdb->prefix}postmeta m1 ON post.ID = m1.post_id
						INNER JOIN {$wpdb->prefix}postmeta m2 ON post.ID = m2.post_id
				WHERE	post.post_status = 'publish' AND post.post_type = 'movieblock'
						AND m1.meta_key = '_movieblock_edition' AND m1.meta_value = %s
						{$movieBlockDayCondition}
				GROUP BY post.ID
			)
		";
	}

	function getMoviesAndMovieBlocks($edition, $day = NULL) {
		global $wpdb;
		$edition = 'bars' . $edition['number'];

		if (is_null($day)) {
			return $wpdb->get_results($wpdb->prepare(getMovieEntriesQuery(false), $edition, $edition));
		}

		$day = $day->format('m-d-Y');
		return $wpdb->get_results($wpdb->prepare(getMovieEntriesQuery(true), $edition, "%{$day}%", $edition, "%{$day}%"));
	}

	function countMovieEntriesForEdition($edition) {
		global $wpdb;
		$edition = 'bars' . $edition['number'];
		return $wpdb->get_var($wpdb->prepare("
			SELECT COUNT(*) FROM (
				" . getMovieEntriesQuery() . "
			) as total
		", $edition, $edition));
	}

	function getMovieSectionsForEdition($edition) {
		global $wpdb;
		$edition = 'bars' . $edition['number'];
		return array_unique(array_map(function ($post) {
			return get_post_type($post->ID) === 'movie' ?
				get_post_meta($post->ID, '_movie_section', true) :
				get_post_meta($post->ID, '_movieblock_section', true);
		}, $wpdb->get_results($wpdb->prepare(getMovieEntriesQuery(false), $edition, $edition))));
	}

	function getMovieSectionLabel($value){
		global $MOVIE_SECTIONS;
		return $MOVIE_SECTIONS[$value]['label'];
	}

	function getFestivalMetrics() {
		$use_cache = !defined('WP_DEBUG') || !WP_DEBUG;

		if ($use_cache) {
			$cached = get_transient('bars_festival_metrics');
			if ($cached !== false) {
				return $cached;
			}
		}

		$current = Editions::current();
		$currentKey = 'bars' . $current['number'];
		$currentMovies = getMovieCount($currentKey);

		if ($currentMovies > 0) {
			$metrics = array(
				'editions'    => $current['number'],
				'movies'      => $currentMovies,
				'short_films' => getShortFilmCount($currentKey),
				'countries'   => getCountryCount($currentKey),
				'fallback'    => false,
			);
		} else {
			$prev = Editions::getByNumber($current['number'] - 1);
			$prevKey = 'bars' . $prev['number'];
			$metrics = array(
				'editions'        => $current['number'],
				'movies'          => getMovieCount($prevKey),
				'short_films'     => getShortFilmCount($prevKey),
				'countries'       => getCountryCount($prevKey),
				'fallback'        => true,
				'fallback_number' => $prev['number'],
			);
		}

		if ($use_cache) {
			set_transient('bars_festival_metrics', $metrics, 7 * DAY_IN_SECONDS);
		}
		return $metrics;
	}

	/**
	 * Count feature-length movies (excluding shorts and online activities) that are
	 * NOT inside a movieblock, for a single edition.
	 */
	function getMovieCount($editionKey) {
		global $wpdb;

		$excludedSections = array('shortFilm', 'shortFilmCompetition', 'cortos25años', 'bloodyWeekend', 'onlineActivities');
		$excludedPlaceholders = implode(',', array_fill(0, count($excludedSections), '%s'));

		$sql = "
			SELECT COUNT(DISTINCT post.ID)
			FROM {$wpdb->prefix}posts post
			INNER JOIN {$wpdb->prefix}postmeta m_edition
				ON post.ID = m_edition.post_id AND m_edition.meta_key = '_movie_edition'
			INNER JOIN {$wpdb->prefix}postmeta m_section
				ON post.ID = m_section.post_id AND m_section.meta_key = '_movie_section'
			LEFT JOIN {$wpdb->prefix}postmeta m_mb
				ON post.ID = m_mb.post_id AND m_mb.meta_key = '_movie_movieblock'
			WHERE post.post_status = 'publish'
			  AND post.post_type = 'movie'
			  AND m_edition.meta_value = %s
			  AND m_section.meta_value NOT IN ($excludedPlaceholders)
			  AND (m_mb.meta_value IS NULL OR m_mb.meta_value = '' OR m_mb.meta_value = '-1')
		";

		$params = array_merge(array($editionKey), $excludedSections);
		$total = (int) $wpdb->get_var($wpdb->prepare($sql, $params));

		return (int) (floor($total / 5) * 5);
	}

	/**
	 * Count distinct countries across ALL movies in the edition (features + shorts in blocks).
	 * Includes feature films (not in excluded sections, not in a movieblock) AND
	 * short films that belong to a movieblock (valid _movie_movieblock).
	 */
	function getCountryCount($editionKey) {
		global $wpdb;

		$excludedSections = array('shortFilm', 'shortFilmCompetition', 'cortos25años', 'bloodyWeekend', 'onlineActivities');
		$excludedPlaceholders = implode(',', array_fill(0, count($excludedSections), '%s'));

		$sql = "
			SELECT DISTINCT m_country.meta_value AS country
			FROM {$wpdb->prefix}posts post
			INNER JOIN {$wpdb->prefix}postmeta m_edition
				ON post.ID = m_edition.post_id AND m_edition.meta_key = '_movie_edition'
			INNER JOIN {$wpdb->prefix}postmeta m_section
				ON post.ID = m_section.post_id AND m_section.meta_key = '_movie_section'
			INNER JOIN {$wpdb->prefix}postmeta m_country
				ON post.ID = m_country.post_id AND m_country.meta_key = '_movie_country'
			LEFT JOIN {$wpdb->prefix}postmeta m_mb
				ON post.ID = m_mb.post_id AND m_mb.meta_key = '_movie_movieblock'
			WHERE post.post_status = 'publish'
			  AND post.post_type = 'movie'
			  AND m_edition.meta_value = %s
			  AND m_country.meta_value != ''
			  AND (
			    m_section.meta_value NOT IN ($excludedPlaceholders)
			    OR (m_mb.meta_value IS NOT NULL AND m_mb.meta_value != '' AND m_mb.meta_value != '-1')
			  )
		";

		$params = array_merge(array($editionKey), $excludedSections);
		$results = $wpdb->get_results($wpdb->prepare($sql, $params));

		if (empty($results)) {
			return 0;
		}

		$countries = array();
		foreach ($results as $row) {
			$parsed = parseCountryField($row->country);
			foreach ($parsed as $country) {
				$countries[$country] = true;
			}
		}

		$total = count($countries);
		return (int) (floor($total / 5) * 5);
	}

	/**
	 * Estimate short film count using the A + B × avg formula.
	 *
	 * A = individual short films that belong to a movieblock (fully entered in DB)
	 * B = standalone short-section entries NOT in a movieblock (each represents ~1 block's worth)
	 * avg = A / number of distinct movieblocks containing shorts
	 * Result = ceil(A + B × avg)
	 */
	function getShortFilmCount($editionKey) {
		global $wpdb;

		$shortSections = array('shortFilm', 'shortFilmCompetition', 'cortos25años', 'bloodyWeekend');
		$shortPlaceholders = implode(',', array_fill(0, count($shortSections), '%s'));

		// Step 1: Count individual shorts inside movieblocks (A) and how many distinct blocks they span.
		// By convention, any movie assigned to a movieblock is a short film, regardless of its section.
		$sql_a = "
			SELECT COUNT(DISTINCT post.ID) AS total,
			       COUNT(DISTINCT m_mb.meta_value) AS block_count
			FROM {$wpdb->prefix}posts post
			INNER JOIN {$wpdb->prefix}postmeta m_edition
				ON post.ID = m_edition.post_id AND m_edition.meta_key = '_movie_edition'
			INNER JOIN {$wpdb->prefix}postmeta m_mb
				ON post.ID = m_mb.post_id AND m_mb.meta_key = '_movie_movieblock'
			WHERE post.post_status = 'publish'
			  AND post.post_type = 'movie'
			  AND m_edition.meta_value = %s
			  AND m_mb.meta_value IS NOT NULL AND m_mb.meta_value != '' AND m_mb.meta_value != '-1'
		";

		$row_a = $wpdb->get_row($wpdb->prepare($sql_a, $editionKey));

		$a = (int) $row_a->total;
		$blockCount = (int) $row_a->block_count;
		// When no movieblocks exist, each standalone entry counts as 1 short
		$avg = $blockCount > 0 ? $a / $blockCount : 1;

		// Step 2: Count standalone short-section entries NOT in a movieblock (B)
		$sql_b = "
			SELECT COUNT(DISTINCT post.ID)
			FROM {$wpdb->prefix}posts post
			INNER JOIN {$wpdb->prefix}postmeta m_edition
				ON post.ID = m_edition.post_id AND m_edition.meta_key = '_movie_edition'
			INNER JOIN {$wpdb->prefix}postmeta m_section
				ON post.ID = m_section.post_id AND m_section.meta_key = '_movie_section'
			LEFT JOIN {$wpdb->prefix}postmeta m_mb
				ON post.ID = m_mb.post_id AND m_mb.meta_key = '_movie_movieblock'
			WHERE post.post_status = 'publish'
			  AND post.post_type = 'movie'
			  AND m_edition.meta_value = %s
			  AND m_section.meta_value IN ($shortPlaceholders)
			  AND (m_mb.meta_value IS NULL OR m_mb.meta_value = '' OR m_mb.meta_value = '-1')
		";

		$params_b = array_merge(array($editionKey), $shortSections);
		$b = (int) $wpdb->get_var($wpdb->prepare($sql_b, $params_b));

		// Step 3: Estimate total = ceil(A + B × avg)
		$total = (int) ceil($a + $b * $avg);
		return (int) (floor($total / 5) * 5);
	}

	function parseCountryField($countryValue) {
		$countryValue = trim($countryValue);
		if ($countryValue === '') {
			return array();
		}

		$parts = preg_split('/\s*[\/,\-]\s*/u', $countryValue);
		$countries = array();
		foreach ($parts as $part) {
			$part = trim($part);
			if ($part !== '') {
				$countries[] = mb_strtolower($part, 'UTF-8');
			}
		}

		return $countries;
	}

	function invalidateFestivalMetricsCache() {
		delete_transient('bars_festival_metrics');
	}

?>
