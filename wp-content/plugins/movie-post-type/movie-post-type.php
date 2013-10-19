<?php
/*
	Plugin Name: Movie Post Type
	Description: Movie post type for BARS wordpress template. Tutorial from http://wp.tutsplus.com/tutorials/plugins/a-guide-to-wordpress-custom-post-types-creation-display-and-meta-boxes/.
	Version: 1.0
	Author: Andrés Mata Suárez
	License: GPLv2
*/


	/* ***** ACTIONS ***** */
	add_action('init', 'create_movie_post_type' );
	add_action('add_meta_boxes', 'add_movie_meta_box');
	add_action('save_post', 'save_movie');


	/* ***** MOVIE FIELD DEFINITIONS ***** */
	// Name, trailer, poster, country, year of release, runtime, synopsis, website, imdb
	$prefix = '_movie_';
	$movie_fields = array(
		array(
			'id'    => $prefix . 'edition',
			'label' => 'Edition',
			'type'  => 'select',
			'options' => array (  
							'bars14' => array (  
								'label' => 'BARS 14',
								'value' => 'bars14'
							)  
						)
		),
		array(
			'id'    => $prefix . 'name',
			'label' => 'Name',
			'type'  => 'text'
		),
		array(
			'id'    => $prefix . 'country',
			'label' => 'Country',
			'type'  => 'text'
		),
		array(
			'id'    => $prefix . 'year',
			'label' => 'Year',
			'type'  => 'text'
		),
		array(
			'id'    => $prefix . 'directors',
			'label' => 'Directors',
			'desc' => 'Comma-separated',
			'type'  => 'text'
		),
		array(
			'id'    => $prefix . 'cast',
			'label' => 'Cast',
			'desc' => 'Comma-separated',
			'type'  => 'text'
		),
		array(
			'id'    => $prefix . 'runtime',
			'label' => 'Runtime',
			'desc' => 'Duration in minutes',
			'type'  => 'text'
		),
		array(
			'id'    => $prefix . 'trailer',
			'label' => 'Trailer',
			'desc' => 'YouTube video ID',
			'type'  => 'text'
		),
		array(
			'id'    => $prefix . 'website',
			'label' => 'Web',
			'type'  => 'text'
		),
		array(
			'id'    => $prefix . 'imdb',
			'label' => 'IMDB',
			'type'  => 'text'
		),
		array(
			'id'    => $prefix . 'screenings',
			'label' => 'Film screenings',
			'desc' => 'Format: mm-dd-yyyy. comma-separated',
			'type'  => 'text'
		),
		array(
			'id'    => $prefix . 'synopsis',
			'label' => 'Synopsis',
			'type'  => 'textarea'
		)
	);
	
	
	
	/* ***** FUNCTIONS ***** */
	function create_movie_post_type() {
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
					'search_items' => 'Search Movie',
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
	
	// Output movie meta box.
	function show_movie_meta_box() {
		global $movie_fields;
		global $post;
		
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
						echo '<br /><span class="description">' . $field['desc'] . '</span>';
						break;
					case 'textarea':  
						echo '<textarea name="' . $field['id'] . '" id="' . $field['id'] . '" cols="60" rows="4">' . $meta . '</textarea>';
						echo '<br /><span class="description">' . $field['desc'] . '</span>';
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
						echo '</select><br /><span class="description">' . $field['desc'] . '</span>';  
						break; 
				}
		echo '</td></tr>';
	}
		
	// Save movie data
	function save_movie($post_id, $post) {
		global $prefix;
		global $movie_fields;
		  
		// Verify nonce
		if (!wp_verify_nonce($_POST['movie_meta_box_nonce'], basename(__FILE__)))
			return;
		
		// Check autosave  
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;
			
		// Check permissions
		//echo '<script type="text/javascript">alert("' .  . '");</script>';
		if ($_POST['post_type'] != 'movie' || !current_user_can('edit_post', $post_id))
			return;
		  
		// Loop through fields and save the data  
		foreach ($movie_fields as $field) {
			if ( isset( $_POST[$field['id']] ) && $_POST[$field['id']] != '' ) {
				if ($field['id'] == $prefix . 'name'){
					// To avoid infinite loop: save_post -> wp_update_post -> save_post -> ...
					remove_action('save_post', 'save_movie');
					wp_update_post(array('ID' => $post_id, 'post_title' => $_POST[$field['id']]));
					remove_action('save_post', 'save_movie');
				}
				update_post_meta( $post_id, $field['id'], $_POST[$field['id']] );
			}
		}
	}

?>