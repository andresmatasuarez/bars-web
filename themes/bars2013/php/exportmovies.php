<?php

	/*
	 * Template Name: tmpl_exportmovies
	 *
	 * @package WordPress
	 * @subpackage bars2013
	 */

	$edition = 'bars14';
	$days = array(	0 => '10-31-2013', 1 => '11-01-2013', 2 => '11-02-2013',
									3 => '11-03-2013', 4 => '11-04-2013', 5 => '11-05-2013',	6 => '11-06-2013');

	// Iterate over all movies and movieblocks copying
	// post_title into post_name after sanitizing it.
	foreach($days as $key => $day){						
		$movies = getMoviesAndMovieBlocks($edition, $day);

		// foreach ($movies as $movie){
		// 	$post = get_post($movie->ID);
		// 	wp_update_post(array(
		// 		'ID' => $post->ID,
		// 		'post_name' => sanitize_title($post->post_title)
		// 	));
		// }

		foreach ($movies as $movie){
			$post = get_post($movie->ID);
			foreach($post as $key => $value){
				echo $key . ': ' . $value . '<br/>';
			}
		}

	}

?>