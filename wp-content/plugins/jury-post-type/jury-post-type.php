<?php
/*
	Plugin Name: Jury Post Type
	Description: Jury post type for BARS wordpress template. Tutorial from http://wp.tutsplus.com/tutorials/plugins/a-guide-to-wordpress-custom-post-types-creation-display-and-meta-boxes/.
	Version: 1.0
	Author: Andrés Mata Suárez
	License: GPLv2
*/

	add_action('admin_init', 'admin_load_jury_scripts');
	function admin_load_jury_scripts() {
		$admin_jury_file = plugins_url( 'admin-jury.js', __FILE__ );

		wp_enqueue_script('admin-jury', $admin_jury_file, array('jquery'));
	}

	/************************************************************************************************/

	/* ***** ACTIONS ***** */
	add_action('init', 'register_jury_post_type' );
	add_action('add_meta_boxes', 'add_jury_meta_box');
	add_action('save_post', 'save_jury');

	add_action('init', 'initialize_jury');

	function initialize_jury(){
		global $jurySections;
		global $juryPrefix;
		global $juryFields;
		global $jurySections;

		$editionOptions = array (
			'bars23' => array ( 'label' => 'BARS 2022',	'value' => 'bars23' ),
			'bars22' => array ( 'label' => 'BARS 2021',	'value' => 'bars22' ),
			'bars21' => array ( 'label' => 'BARS 2020',	'value' => 'bars21' ),
			'bars20' => array ( 'label' => 'BARS 2019',	'value' => 'bars20' ),
			'bars19' => array ( 'label' => 'BARS 2018',	'value' => 'bars19' ),
			'bars18' => array ( 'label' => 'BARS 2017',	'value' => 'bars18' ),
			'bars17' => array ( 'label' => 'BARS 2016',	'value' => 'bars17' ),
			'bars16' => array ( 'label' => 'BARS 2015',	'value' => 'bars16' ),
			'bars15' => array ( 'label' => 'BARS 2014',	'value' => 'bars15' ),
			'bars14' => array ( 'label' => 'BARS 2013',	'value' => 'bars14' )
		);

		$jurySections = array (
			'internationalFeatureFilmCompetition' => array('label' => 'Competencia Internacional de Largometrajes',  'value' => 'internationalFeatureFilmCompetition'),
			'iberoamericanFeatureFilmCompetition' => array('label' => 'Competencia Iberoamericana de Largometrajes', 'value' => 'iberoamericanFeatureFilmCompetition'),
			'internationalShortFilmCompetition'   => array('label' => 'Competencia Internacional de Cortometrajes',  'value' => 'internationalShortFilmCompetition'),
			'reaparicionesCompetition'            => array('label' => 'Competencia "Reapariciones"',                 'value' => 'reaparicionesCompetition'),
			'finDeSemanaSangriento'               => array('label' => 'Concurso: "Fin de Semana Sangriento"',        'value' => 'finDeSemanaSangriento'),
		);

		/* ***** JURY FIELD DEFINITIONS ***** */
		$juryPrefix = '_jury_';
		$juryFields = array(
			array(
				'id'    => $juryPrefix . 'edition',
				'label' => 'Edition',
				'type'  => 'select',
				'options' => $editionOptions
			),
			array(
				'id'    => $juryPrefix . 'section',
				'label' => 'Section',
				'type'  => 'select',
				'options' => $jurySections
			),
			array(
				'id'    => $juryPrefix . 'name',
				'label' => 'Full name',
				'type'  => 'text'
			),
			array(
				'id'    => $juryPrefix . 'description',
				'label' => 'Description',
				'type'  => 'richeditor'
			)
		);
	}

	/* ***** FUNCTIONS ***** */
	function register_jury_post_type() {
		register_post_type( 'jury',
			array(
				'labels' => array(
					'name' => 'Juries',
					'singular_name' => 'Jury',
					'add_new' => 'Add New',
					'add_new_item' => 'Add New Jury',
					'edit' => 'Edit',
					'edit_item' => 'Edit Jury',
					'new_item' => 'New Jury',
					'view' => 'View',
					'view_item' => 'View Jury',
					'not_found' => 'No Juries found',
					'not_found_in_trash' => 'No Jury found in Trash',
					'parent' => 'Parent Jury'
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

	// Add jury meta box.
	function add_jury_meta_box() {
		add_meta_box(
			'jury_meta_box', // $id
			'Jury Info', // $title
			'show_jury_meta_box', // $callback
			'jury', // $page
			'normal', // $context
			'high'); // $priority
	}

	// Output jury meta box.
	function show_jury_meta_box($post) {
		global $juryFields;

		// Use nonce for verification
		echo '<input type="hidden" name="jury_meta_box_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';

		// Begin the field table and loop
		echo '<table class="form-table">';

		// Echo each field as a row
		foreach ($juryFields as $field) {
			echo_bars_plugin_field($post, $field);
		}

		echo '</table>';
	}

	// Save jury data
	function save_jury($post_id) {
		if ($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}

		global $juryPrefix;
		global $juryFields;

		// Verify nonce
		if (!wp_verify_nonce($_POST['jury_meta_box_nonce'], basename(__FILE__)))
			return;

		// Check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;

		// Check permissions
		if ($_POST['post_type'] != 'jury' || !current_user_can('edit_post', $post_id))
			return;

		// Loop through fields and save the data
		foreach ($juryFields as $field) {
			if ( isset( $_POST[$field['id']] ) && $_POST[$field['id']] != '' ) {

				if ($field['id'] == $juryPrefix . 'name'){
					// To avoid infinite loop: save_post -> wp_update_post -> save_post -> ...
					remove_action('save_post', 'save_jury');
					wp_update_post(array(
						'ID' => $post_id,
						'post_title' => $_POST[$field['id']],
						'post_name' => sanitize_title($_POST[$field['id']]),
						'post_author' => get_current_user_id(),
						'post_content' => ''
					));
					add_action('save_post', 'save_jury');
				}
				update_post_meta( $post_id, $field['id'], $_POST[$field['id']] );
			}
		}
	}

	function getJuryEntriesQuery() {
		global $wpdb;
		return "
			(
				SELECT	post.ID, post.post_author, post.post_date, post.post_content
				FROM	{$wpdb->prefix}posts post
						INNER JOIN {$wpdb->prefix}postmeta m1 ON post.ID = m1.post_id
						INNER JOIN {$wpdb->prefix}postmeta m2 ON post.ID = m2.post_id
						INNER JOIN {$wpdb->prefix}postmeta m3 ON post.ID = m3.post_id
				WHERE	post.post_status = 'publish' AND post.post_type = 'jury'
						AND m1.meta_key = '_jury_edition' AND m1.meta_value = %s
				GROUP BY post.ID
			)
		";
	}

	function prepareJury($juryPost) {
		global $juryPrefix;
		global $juryFields;

	  $postMeta = get_post_meta($juryPost->ID);

	  $juryObj = array(
	  	'postId' => $juryPost->ID,
	  	'thumbnail' => get_the_post_thumbnail($juryPost->ID, 'jury-post-thumbnail') // thumbnail size defined in functions.php
	  );

	  foreach($juryFields as $field) {
	  	$fieldName = str_replace($juryPrefix, '', $field['id']);
	  	$juryObj[$fieldName] = $postMeta[$field['id']][0];
	  }

	  return $juryObj;
	}

	function prepareJuries($juryPosts) {
		$prepared = array_map('prepareJury', $juryPosts);

		$indexedBySection = array();
		foreach ($prepared as $jury) {
		  $indexedBySection[$jury['section']][] = $jury;
		}

		return $indexedBySection;
	}

	function getJuries($edition) {
		global $wpdb;
		$edition = 'bars' . $edition['number'];
		$posts = $wpdb->get_results($wpdb->prepare(getJuryEntriesQuery(), $edition));
		return prepareJuries($posts);
	}

	function getJurySectionLabel($sectionId) {
		global $jurySections;

		if (isset($jurySections[$sectionId])) {
			return $jurySections[$sectionId]['label'];
		}

		return $sectionId;
	}

?>
