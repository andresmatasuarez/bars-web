<?php
/**
 *
 * @package WordPress
 * @subpackage bars2013
 */

	require_once 'editions.php';

	if (!isset($_GET['edition'])){
		$currentEdition = Editions::current();
	} else {
		$currentEdition = Editions::getByNumber(htmlspecialchars($_GET['edition']));
	}
	$venues = Editions::venues($currentEdition);


	$movieBlock = array(
		"title" => get_the_title($post->ID),
		"section" => get_post_meta($post->ID, '_movieblock_section', true),
		"image" => get_the_post_thumbnail($post->ID, 'bars2013-movie-post-image' /* 'bars2013-movieblock-post-image' */),
		"runtime" => get_post_meta($post->ID, '_movieblock_runtime', true),
		"streamingLink" => get_post_meta($post->ID, '_movieblock_streamingLink', true),
		"movies" => array()
	);

	$query = new WP_query(array(
		'post_type' => 'movie',
		'meta_key' => '_movie_movieblock',
		'meta_value' => $post->ID,
		'posts_per_page' => -1,
		'post_status' => 'publish'
	));

	while($query->have_posts()){
		$query->next_post();

		$movie = array(
			"id" => $post->ID,
			"title" => get_the_title($query->post->ID),
			"year" => get_post_meta($query->post->ID, '_movie_year', true),
			"country" => get_post_meta($query->post->ID, '_movie_country', true),
			"runtime" => get_post_meta($query->post->ID, '_movie_runtime', true),
			"directors" => get_post_meta($query->post->ID, '_movie_directors', true),
			"cast" => get_post_meta($query->post->ID, '_movie_cast', true),
			"synopsis" => get_post_meta($query->post->ID, '_movie_synopsis', true),
			"comments" => get_post_meta($query->post->ID, '_movie_comments', true),
			"trailerUrl" => get_post_meta($query->post->ID, '_movie_trailer', true),
			"image" => get_the_post_thumbnail($query->post->ID, 'bars2013-movie-post-image'),
			"thumbnail" => get_the_post_thumbnail($query->post->ID, 'bars2013-movie-post-thumbnail'),
		);

		$movieBlock['movies'][] = $movie;

	}

	echo json_encode($movieBlock);

	wp_reset_postdata();

?>
