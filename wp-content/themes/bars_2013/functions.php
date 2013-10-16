<?php

// Various thumbnail sizes.
if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
}

if ( function_exists( 'add_image_size' ) ) {
	add_image_size( 'search-thumbnail', 200, 150, true);
}

if ( function_exists( 'add_image_size' ) ) {
	add_image_size( 'recent-post-thumbnail', 322, 160, true);
}

// Hide some plugins from panel for non-admin users.
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

// Search only through posts.
function SearchFilter($query) {
	if ($query->is_search) {
	$query->set('post_type','post');
	}
	return $query;
}

add_filter('pre_get_posts','SearchFilter');

// Disqus embed comments
function disqus_embed($disqus_shortname) {
    global $post;
    wp_enqueue_script('disqus_embed','http://'.$disqus_shortname.'.disqus.com/embed.js');
    echo '<div id="disqus_thread"></div>
    <script type="text/javascript">
        var disqus_shortname = "'.$disqus_shortname.'";
        var disqus_title = "'.$post->post_title.'";
        var disqus_url = "'.get_permalink($post->ID).'";
        var disqus_identifier = "'.$disqus_shortname.'-'.$post->ID.'";
    </script>';
}