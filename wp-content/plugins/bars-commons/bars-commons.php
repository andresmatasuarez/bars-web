<?php
/*
	Plugin Name: BARS commons
	Description: BARS commons for BARS wordpress template. Tutorial from http://wp.tutsplus.com/tutorials/plugins/a-guide-to-wordpress-custom-post-types-creation-display-and-meta-boxes/.
	Version: 1.0
	Author: Andrés Mata Suárez
	License: GPLv2
*/

	add_action('admin_init', 'admin_load_bars_commons_scripts');
	function admin_load_bars_commons_scripts() {
		$admin_bars_commons_file = plugins_url( 'admin-bars-commons.js', __FILE__ );

		wp_enqueue_script('admin-bars-commons', $admin_bars_commons_file, array('jquery'));
	}

	/************************************************************************************************/

	/* ***** ACTIONS ***** */
	add_action('init', 'initialize_bars_commons');

	function initialize_bars_commons(){
		global $barscommons_editionOptions;

		$barscommons_editionOptions = array (
			'bars25' => array ( 'label' => 'BARS 2024',	'value' => 'bars25' ),
			'bars24' => array ( 'label' => 'BARS 2023',	'value' => 'bars24' ),
			'bars23' => array ( 'label' => 'BARS 2022',	'value' => 'bars23' ),
			'bars22' => array ( 'label' => 'BARS 2021',	'value' => 'bars22' ),
			'bars21' => array ( 'label' => 'BARS 2020',	'value' => 'bars21' ),
			'bars20' => array ( 'label' => 'BARS 2019',	'value' => 'bars20' ),
			'bars19' => array ( 'label' => 'BARS 2018',	'value' => 'bars19' ),
			'bars18' => array ( 'label' => 'BARS 2017',	'value' => 'bars18' ),
			'bars17' => array ( 'label' => 'BARS 2016',	'value' => 'bars17' ),
			'bars16' => array ( 'label' => 'BARS 2015',	'value' => 'bars16' ),
			'bars15' => array ( 'label' => 'BARS 2014',	'value' => 'bars15' ),
			'bars14' => array ( 'label' => 'BARS 2013',	'value' => 'bars14' ),
		);
	}

	function barscommons_render_post_meta_input($post, $field){
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
					case 'richeditor':
						wp_editor($meta, $field['id'], array(
								'wpautop'       => true,
								'media_buttons' => false,
								'textarea_name' => $field['id'],
								'textarea_rows' => 10,
								'teeny'         => true
							)
						);
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

	function barscommons_save_custom_post($post_type, $customPostSaveFunctionName, $metaBoxNonceFieldId, $metaBoxNonceAction, $post_id, $nameFieldId, $fields) {
		if ($_SERVER['REQUEST_METHOD'] != 'POST'){
			return;
		}

		// Verify nonce
		if (isset($_POST[$metaBoxNonceFieldId]) && !wp_verify_nonce($_POST[$metaBoxNonceFieldId], $metaBoxNonceAction))
			return;

		// Check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;

		// Check permissions
		if ($_POST['post_type'] != $post_type || !current_user_can('edit_post', $post_id))
			return;

		// Loop through fields and save the data
		foreach ($fields as $field) {
			if ( isset( $_POST[$field['id']] ) && $_POST[$field['id']] != '' ) {
				if ($field['id'] == $nameFieldId){
					// To avoid infinite loop: save_post -> wp_update_post -> save_post -> ...
					remove_action('save_post', $customPostSaveFunctionName);
					wp_update_post(array(
						'ID' => $post_id,
						'post_title' => $_POST[$field['id']],
						'post_name' => sanitize_title($_POST[$field['id']]),
						'post_author' => get_current_user_id(),
						'post_content' => ''
					));
					add_action('save_post', $customPostSaveFunctionName);
				}
				update_post_meta( $post_id, $field['id'], $_POST[$field['id']] );
			}
		}
	}

	function barscommons_show_meta_box_inputs($metaBoxNonceFieldId, $metaBoxNonceAction, $post, $fields) {
		// Use nonce for verification
		echo '<input type="hidden" name="' . $metaBoxNonceFieldId . '" value="' . wp_create_nonce($metaBoxNonceAction) . '" />';

		// Begin the field table and loop
		echo '<table class="form-table">';

		// Echo each field as a row
		foreach ($fields as $field) {
			barscommons_render_post_meta_input($post, $field);
		}

		echo '</table>';
	}

?>
