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
	}

	/************************************************************************************************/

	/* ***** ACTIONS ***** */
	add_action('init', 'register_movie_post_type' );
	add_action('add_meta_boxes', 'add_movie_meta_box');
	add_action('save_post', 'save_movie');

	add_action('init', 'register_movieblock_post_type' );
	add_action('add_meta_boxes', 'add_movieblock_meta_box');
	add_action('save_post', 'save_movieblock');

	add_action('init', 'initialize');

	function initialize(){
		global $sections;
		global $movie_prefix;
		global $movie_fields;
		global $movieblock_prefix;
		global $movieblock_fields;
		global $movieBlocks;

		$movieBlocks = movieBlocks();

		/* ***** MOVIE SECTIONS ***** */
		$sections = array (
			'opening' => array ( 'label' => 'Función de apertura', 'value' => 'opening' ),
			'shortFilm' => array ( 'label' => 'Cortos fuera de competencia', 'value' => 'shortFilm' ),
			'shortFilmCompetition' => array ( 'label' => 'Cortos en competencia', 'value' => 'shortFilmCompetition' ),
			'mediumLengthFilm' => array ( 'label' => 'Mediometrajes', 'value' => 'mediumLengthFilm' ),
			'internationalFeatureFilmCompetition' => array ( 'label' => 'Competencia Internacional', 'value' => 'internationalFeatureFilmCompetition' ),
			'iberoamericanFeatureFilmCompetition' => array ( 'label' => 'Competencia Iberoamericana', 'value' => 'iberoamericanFeatureFilmCompetition' ),
			'releases' => array ( 'label' => 'Novedades', 'value' => 'releases' ),
			'raroVhs' => array ( 'label' => 'Raro VHS: Tapes Rojo Sangre', 'value' => 'raroVhs' ),
			'anioVerde' => array ( 'label' => 'Argentina Año Verde', 'value' => 'anioVerde' ),
			'herederosDelTerror' => array ( 'label' => 'Herederos Del Teror', 'value' => 'herederosDelTerror' ),
			'filmotecaPresenta' => array ( 'label' => 'Filmoteca Presenta', 'value' => 'filmotecaPresenta' ),
			'laCripta' => array ( 'label' => 'La Cripta', 'value' => 'laCripta' ),
			'sangreSudorYLagrimas' => array ( 'label' => 'Sangre, Sudor y Lágrimas', 'value' => 'sangreSudorYLagrimas' ),
			'documentary' => array ( 'label' => 'Documental', 'value' => 'documentary' ),
			'imperdibles' => array ( 'label' => 'Imperdibles', 'value' => 'imperdibles' )
		);

		/* ***** MOVIE FIELD DEFINITIONS ***** */
		$movie_prefix = '_movie_';
		$movie_fields = array(
			array(
				'id'    => $movie_prefix . 'edition',
				'label' => 'Edition',
				'type'  => 'select',
				'options' => array (
					'bars15' => array ( 'label' => 'BARS 15',	'value' => 'bars15' ),
					'bars14' => array ( 'label' => 'BARS 14',	'value' => 'bars14' )
				)
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
				'options' => $sections
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
				'desc' => 'YouTube video ID',
				'type'  => 'text'
			),
			array(
				'id'    => $movie_prefix . 'website',
				'label' => 'Web',
				'type'  => 'text'
			),
			array(
				'id'    => $movie_prefix . 'imdb',
				'label' => 'IMDB',
				'type'  => 'text'
			),
			array(
				'id'    => $movie_prefix . 'screenings',
				'label' => 'Film screenings',
				'desc' => 'Format: mm-dd-yyyy hh:mm. comma-separated',
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
				'options' => array (
					'bars15' => array ( 'label' => 'BARS 15',	'value' => 'bars15' ),
					'bars14' => array ( 'label' => 'BARS 14',	'value' => 'bars14' )
				)
			),
			array(
				'id'    => $movieblock_prefix . 'section',
				'label' => 'Section',
				'type'  => 'select',
				'options' => $sections
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
				'desc' => 'Format: mm-dd-yyyy hh:mm. comma-separated',
				'type'  => 'text'
			)
		);
	}

	function sectionByValue($value){
		global $sections;
		return $sections[$value]['label'];
	}

	/* ***** FUNCTIONS ***** */
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
	function show_movie_meta_box() {
		global $movie_fields;

		// Use nonce for verification
		echo '<input type="hidden" name="movie_meta_box_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';

		// Begin the field table and loop
		echo '<table class="form-table">';

		// Echo each field as a row
		foreach ($movie_fields as $field) {
			echo_field($field);
		}

		echo '</table>';
	}

	// Output movieblock meta box.
	function show_movieblock_meta_box() {
		global $movieblock_fields;

		// Use nonce for verification
		echo '<input type="hidden" name="movieblock_meta_box_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';

		// Begin the field table and loop
		echo '<table class="form-table">';

		// Echo each field as a row
		foreach ($movieblock_fields as $field) {
			echo_field($field);
		}

		echo '</table>';
	}

	function echo_field($field){
		global $post;
		// Get value of this field if it exists for this post
		$meta = get_post_meta($post->ID, $field['id'], true);

		echo '<tr>
				<th><label for="' . $field['id'] . '">' . $field['label'] . '</label></th>
				<td>';
				switch($field['type']) {
					case 'text':
						echo '<input type="text" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . $meta . '" size="30" />';
						if (isset($field['desc']) && strlen($field['desc']) != 0){
							echo '<br /><span class="description">' . $field['desc'] . '</span>';
						}
						break;
					case 'textarea':
						echo '<textarea name="' . $field['id'] . '" id="' . $field['id'] . '" cols="60" rows="4">' . $meta . '</textarea>';
						if (isset($field['desc']) && strlen($field['desc']) != 0){
							echo '<br /><span class="description">' . $field['desc'] . '</span>';
						}
						break;
					case 'checkbox':
						echo '<input type="checkbox" name="' . $field['id'] . '" id="' . $field['id'] . '" ', $meta ? ' checked="checked"' : '','/>
							<label for="' . $field['id'] . '">' . $field['desc'] . '</label>';
						break;
					case 'select':
						echo '<select name="' . $field['id'] . '" id="' . $field['id'] . '">';
						foreach ($field['options'] as $option) {
							echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="' . $option['value'] . '">' . $option['label'] . '</option>';
						}
						if (isset($field['desc']) && strlen($field['desc']) != 0){
							echo '<br /><span class="description">' . $field['desc'] . '</span>';
						}
						break;
				}
		echo '</td></tr>';
	}

	// Save movie data
	function save_movie($post_id) {
		if ($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}

		global $movie_prefix;
		global $movie_fields;

		// Verify nonce
		if (!wp_verify_nonce($_POST['movie_meta_box_nonce'], basename(__FILE__)))
			return;

		// Check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;

		// Check permissions
		if ($_POST['post_type'] != 'movie' || !current_user_can('edit_post', $post_id))
			return;

		// Loop through fields and save the data
		foreach ($movie_fields as $field) {
			if ( isset( $_POST[$field['id']] ) && $_POST[$field['id']] != '' ) {
				if ($field['id'] == $movie_prefix . 'name'){
					// To avoid infinite loop: save_post -> wp_update_post -> save_post -> ...
					remove_action('save_post', 'save_movie');
					wp_update_post(array(
						'ID' => $post_id,
						'post_title' => $_POST[$field['id']],
						'post_name' => sanitize_title($_POST[$field['id']]),
						'post_author' => get_current_user_id(),
						'post_content' => ''
					));
					add_action('save_post', 'save_movie');
				}
				update_post_meta( $post_id, $field['id'], $_POST[$field['id']] );
			}
		}
	}

	// Save short film series data
	function save_movieblock($post_id) {
		if ($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}

		global $movieblock_prefix;
		global $movieblock_fields;

		// Verify nonce
		if (isset($_POST['movieblock_meta_box_nonce']) && !wp_verify_nonce($_POST['movieblock_meta_box_nonce'], basename(__FILE__)))
			return;

		// Check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;

		// Check permissions
		if ($_POST['post_type'] != 'movieblock' || !current_user_can('edit_post', $post_id))
			return;

		// Loop through fields and save the data
		foreach ($movieblock_fields as $field) {
			if ( isset( $_POST[$field['id']] ) && $_POST[$field['id']] != '' ) {
				if ($field['id'] == $movieblock_prefix . 'name'){
					// To avoid infinite loop: save_post -> wp_update_post -> save_post -> ...
					remove_action('save_post', 'save_movieblock');
					wp_update_post(array(
						'ID' => $post_id,
						'post_title' => $_POST[$field['id']],
						'post_name' => sanitize_title($_POST[$field['id']]),
						'post_author' => get_current_user_id(),
						'post_content' => ''
					));
					add_action('save_post', 'save_movieblock');
				}
				update_post_meta( $post_id, $field['id'], $_POST[$field['id']] );
			}
		}
	}

	function movieBlocks(){
		global $post;
		$query = new WP_Query(array(
			'post_type' => array( 'movieblock' ),
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(array(
			  'key'     => '_movieblock_edition',
			  'value'   => 'bars15',
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


	function getMoviesAndMovieBlocks($edition, $day) {
		global $wpdb;

		$edition = 'bars' . $edition['number'];
		$day = $day->format('m-d-Y');

		return $wpdb->get_results($wpdb->prepare("
			(
				SELECT	post.ID, post.post_author, post.post_date, post.post_content
				FROM	{$wpdb->prefix}posts post
						INNER JOIN {$wpdb->prefix}postmeta m1 ON post.ID = m1.post_id
						INNER JOIN {$wpdb->prefix}postmeta m2 ON post.ID = m2.post_id
						INNER JOIN {$wpdb->prefix}postmeta m3 ON post.ID = m3.post_id
				WHERE	post.post_status = 'publish' AND post.post_type = 'movie'
						AND m1.meta_key = '_movie_edition' AND m1.meta_value = %s
						AND m2.meta_key = '_movie_screenings' AND m2.meta_value LIKE '%%%s%%'
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
						AND m2.meta_key = '_movieblock_screenings' AND m2.meta_value LIKE '%%%s%%'
				GROUP BY post.ID
			)
		", $edition, $day, $edition, $day));
	}

	// @src http://stackoverflow.com/questions/2762061/how-to-add-http-if-its-not-exists-in-the-url
	function addHttp($url) {
		if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
				$url = "http://" . $url;
		}
		return $url;
	}

?>