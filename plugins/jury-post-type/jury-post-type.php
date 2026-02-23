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
		global $barscommons_editionOptions;

		$jurySections = array (
			'internationalFeatureFilmCompetition' => array('label' => 'Competencia Internacional de Largometrajes',  'value' => 'internationalFeatureFilmCompetition'),
			'iberoamericanFeatureFilmCompetition' => array('label' => 'Competencia Iberoamericana de Largometrajes', 'value' => 'iberoamericanFeatureFilmCompetition'),
			'internationalShortFilmCompetition'   => array('label' => 'Competencia Internacional de Cortometrajes',  'value' => 'internationalShortFilmCompetition'),
			'reaparicionesCompetition'            => array('label' => 'Competencia "Reapariciones"',                 'value' => 'reaparicionesCompetition'),
			'finDeSemanaSangriento'               => array('label' => 'Concurso: "Fin de Semana Sangriento"',        'value' => 'finDeSemanaSangriento'),

			// BARS 2024 sections
			'competenciaArgentinaDeLargometrajes' => array('label' => 'Competencia Argentina de Largometrajes', 'value' => 'competenciaArgentinaDeLargometrajes'),
		);

		/* ***** JURY FIELD DEFINITIONS ***** */
		$juryPrefix = '_jury_';
		$juryFields = array(
			array(
				'id'    => $juryPrefix . 'edition',
				'label' => 'Edition',
				'type'  => 'select',
				'options' => $barscommons_editionOptions
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
		barscommons_show_meta_box_inputs('jury_meta_box_nonce', basename(__FILE__), $post, $juryFields);
	}

	// Save jury data
	function save_jury($post_id) {
		global $juryPrefix;
		global $juryFields;

		barscommons_save_custom_post(
			'jury',
			'save_jury',
			'jury_meta_box_nonce',
			basename(__FILE__),
			$post_id,
			$juryPrefix . 'name',
			$juryFields
		);
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
	  	'thumbnail' => get_the_post_thumbnail($juryPost->ID, get_template() . '-jury-post-thumbnail')
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
