<?php
/**
 *
 * @package WordPress
 * @subpackage bars2013
 */

	require_once 'helpers.php';
	require_once 'elements.php';
	require_once 'editions.php';

	if (!isset($_GET['edition'])){
		$currentEdition = Editions::current();
	} else {
		$currentEdition = Editions::getByNumber(htmlspecialchars($_GET['edition']));
	}
	$venues = Editions::venues($currentEdition);

	echo json_encode(array(
		"id" => $post->ID,
		"title" => get_the_title($post->ID),
		"section" => get_post_meta($post->ID, '_movie_section', true),
		"year" => get_post_meta($post->ID, '_movie_year', true),
		"country" => get_post_meta($post->ID, '_movie_country', true),
		"runtime" => get_post_meta($post->ID, '_movie_runtime', true),
		"directors" => get_post_meta($post->ID, '_movie_directors', true),
		"cast" => get_post_meta($post->ID, '_movie_cast', true),
		"synopsis" => get_post_meta($post->ID, '_movie_synopsis', true),
		"comments" => get_post_meta($post->ID, '_movie_comments', true),
		"trailerUrl" => get_post_meta($post->ID, '_movie_trailer', true),
		"website" => get_post_meta($post->ID, '_movie_website', true),
		"imdb" => get_post_meta($post->ID, '_movie_imdb', true),
		"image" => get_the_post_thumbnail($post->ID, 'movie-post-image'),

		"streamingLink" => get_post_meta($post->ID, '_movie_streamingLink', true)
	));
?>
