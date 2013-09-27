<?php

/*
 * Filters for strings of text
 */
foreach ( 
	array( 
		'wp_theater-section_title',
		'wp_theater-text',
		'wp_theater-title',
		'wp_theater-video_title'
	) as $filter ) {

	if ( is_admin() ) {
		// These are expensive. Run only on admin pages for defense in depth.
		add_filter( $filter, 'sanitize_text_field'  );
		add_filter( $filter, 'wp_kses_data'         );
	}
	add_filter( $filter, '_wp_specialchars', 30   );

}

/*
 * Filters for urls
 */
foreach ( 
	array(
		'wp_theater-url',
		'wp_theater-get_request_url',
		'wp_theater-get_more_link'
	) as $filter ) {

	if ( is_admin() )
		add_filter( $filter, 'wp_strip_all_tags' );
	add_filter( $filter, 'esc_url'           );
	if ( is_admin() )
		add_filter( $filter, 'wp_kses_data'    );
}

/*
 * before save filters
 */
if (is_admin()) {


	/*
	 * Filters for strings of text before saving
	 */
	foreach ( 
		array(
			'pre_wp_theater-text',
		) as $filter ) {

		add_filter( $filter, 'sanitize_text_field'    );
		add_filter( $filter, 'wp_filter_kses'         );
		add_filter( $filter, '_wp_specialchars', 30   );
	}

	/*
	 * Filters for urls before saving
	 */
	foreach ( 
		array(
			'pre_wp_theater-url',
		) as $filter ) {

		add_filter( $filter, 'wp_strip_all_tags' );
		add_filter( $filter, 'esc_url_raw'       );
		add_filter( $filter, 'wp_filter_kses'    );
	}

}