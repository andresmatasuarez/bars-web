<?php


/* ************************ THUMBNAIL SIZES ************************ */
if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
}

if ( function_exists( 'add_image_size' ) ) {
	add_image_size( 'movie-post-thumbnail', 160, 81, true);
	add_image_size( 'movie-post-image', 220, 129, true);
	add_image_size( 'movieblock-post-image', 110, 65, true);
}

/* ************ SIDEBAR WIDGETS ************ */
function add_youtube_sidebar_widget($class, $title, $video_id, $width, $height){
	return widgetify($class, $title, youtube_widget($video_id, $width, $height));
}

function add_image_sidebar_widget($class, $title, $img_url){
	return widgetify($class, $title, image_widget($img_url));
}

/* private: decorate content as a sidebar widget */
function widgetify($class, $title, $content){
	return '<div class="bars-widget ' . $class . '">
		<div class="bars-widget-header">
			<div class="bars-widget-logo"></div>
			<div class="bars-widget-title">' . $title . '</div>
		</div>
		<div class="bars-widget-content">' . $content . '</div>
	</div>';
}

/* private: YouTube video widget */
function youtube_widget($video_id, $width, $height){
	return '<div class="youtube-video">
		<iframe width="' . $width . '" height="' . $height . '" src="//www.youtube.com/embed/' . $video_id . '" frameborder="0" allowfullscreen></iframe>
	</div>';
}

/* private: Sidebar image widget */
function image_widget($img_url){
	return '<div class="image-container" style="width:300px;height:150px">
		<a class="fancybox sidebar" href="'. $img_url . '"><img src="' . $img_url . '" /></a>
	</div>';
}

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










/* ************ SEARCH ONLY THROUGH POSTS ************ */
function SearchFilter($query) {
	if ($query->is_search) {
		$query->set('post_type','post');
	}
	return $query;
}

add_filter('pre_get_posts','SearchFilter');

/**
 * Display the post content. Optionally allows post ID to be passed
 * @uses the_content()
 *
 * @param int $id Optional. Post ID.
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param bool $stripteaser Optional. Strip teaser content before the more text. Default is false.
 *
 * @src http://stephenharris.info/get-post-content-by-id/
 */
function get_content_by_id( $post_id=0, $more_link_text = null, $stripteaser = false ){
    global $post;
    $post = &get_post($post_id);
    setup_postdata( $post, $more_link_text, $stripteaser );
    the_content();
    wp_reset_postdata( $post );
}

/**
 * @src http://www.uplifted.net/programming/wordpress-get-the-excerpt-automatically-using-the-post-id-outside-of-the-loop/
 */
function get_excerpt_by_id($post_id, $word_count = 25){
    $the_post = get_post($post_id); //Gets post ID
    $the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
    $excerpt_length = $word_count; //Sets excerpt length by word count
    $the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
    $words = explode(' ', $the_excerpt, $excerpt_length);

    if(count($words) >= $excerpt_length) :
        array_pop($words);
        array_push($words, '…');
        $the_excerpt = implode(' ', $words);
    endif;

    $the_excerpt = '<p>' . $the_excerpt . '</p>';

    return $the_excerpt;
}

/* ************ English to spanish day name translation ************ */
function getSpanishDayName($englishDay){
	switch(strtolower($englishDay)){
		case 'sunday':		return 'domingo';
		case 'monday':		return 'lunes';
		case 'tuesday':		return 'martes';
		case 'wednesday':	return 'miércoles';
		case 'thursday':	return 'jueves';
		case 'friday':		return 'viernes';
		case 'saturday':	return 'sábado';
		default:			return null;
	}
}