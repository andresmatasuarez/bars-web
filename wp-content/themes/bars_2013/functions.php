<?php

//if( !is_admin()){
//	wp_deregister_script('jquery');
//	wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"), false, '1.9.1');
//	wp_enqueue_script('jquery');
//}

/* ************************ THUMBNAIL SIZES ************************ */
if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
}

if ( function_exists( 'add_image_size' ) ) {
	add_image_size( 'search-thumbnail', 200, 150, true);
	add_image_size( 'recent-post-thumbnail', 322, 160, true);
	add_image_size( 'movie-post-thumbnail', 175, 88, true);
	add_image_size( 'movie-post-image', 200, 117, true);
}

/* ************ HIDE SLIDEDECK2 FROM WORDPRESS PANEL ************ */
if (is_admin() && ! current_user_can('install_plugins')) {
    add_action('admin_init', 'remove_slidedeck_menu_page');
    add_action('admin_footer', 'remove_slidedeck_media_button');
}

// Remove SlideDeck2 menu page.
function remove_slidedeck_menu_page() {
    remove_menu_page('slidedeck2-lite.php');
}

// Remove the SlideDeck2 button from the post editor.
function remove_slidedeck_media_button() {
    echo <<<JQUERY
<script>
jQuery(document).ready(function($) {
	$('#add_slidedeck').remove();
});
</script>
JQUERY;
}

/* ************ SEARCH ONLY THROUGH POSTS ************ */
function SearchFilter($query) {
	if ($query->is_search) {
		$query->set('post_type','post');
	}
	return $query;
}

add_filter('pre_get_posts','SearchFilter');

/* ************ DISQUS EMBED COMMENTS ************ */
function disqus_embed($disqus_shortname) {
    global $post;
    wp_enqueue_script('disqus_embed', 'http://' . $disqus_shortname . '.disqus.com/embed.js');
    echo '<div id="disqus_thread"></div>
    <script type="text/javascript">
        var disqus_shortname = "' . $disqus_shortname . '";
        var disqus_title = "' . $post->post_title . '";
        var disqus_url = "' . get_permalink($post->ID) . '";
        var disqus_identifier = "' . $disqus_shortname . '-' . $post->ID . '";
    </script>';
}