<?php

if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
}

if ( function_exists( 'add_image_size' ) ) {
	add_image_size( 'search-thumbnail', 200, 150, true);
}

if ( function_exists( 'add_image_size' ) ) {
	add_image_size( 'recent-post-thumbnail', 350, 160, true);
}

function SearchFilter($query) {
	if ($query->is_search) {
	$query->set('post_type','post');
	}
	return $query;
}

add_filter('pre_get_posts','SearchFilter');

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