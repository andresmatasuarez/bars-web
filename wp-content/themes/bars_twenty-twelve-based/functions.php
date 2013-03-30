<?php

add_action( 'init', 'bars_theme_setup' );

function bars_theme_setup() {

	if (function_exists('add_theme_support')) {
		add_theme_support('post-thumbnails');
		set_post_thumbnail_size(250, 125, true);
	}
}

function sample_function_erasure() {
	
}