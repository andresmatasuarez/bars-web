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
function widgetify($class, $title = null, $content){
	return '
    <div class="bars-widget ' . $class . '">' . (
      is_null($title) ? '' : '
        <div class="bars-widget-header">
          <div class="bars-widget-logo"></div>
          <div class="bars-widget-title">' . $title . '</div>
        </div>'
      ) . '
      <div class="bars-widget-content">' . $content . '</div>
    </div>
  ';
}

/* private: YouTube video widget */
function youtube_widget($video_id, $width, $height){
	return '<div class="youtube-video">
		<iframe width="' . $width . '" height="' . $height . '" src="//www.youtube.com/embed/' . $video_id . '" frameborder="0" allowfullscreen></iframe>
	</div>';
}

/* private: Sidebar image widget */
function image_widget($img_url){
	return '<a class="fancybox sidebar" href="'. $img_url . '"><img src="' . $img_url . '" /></a>';
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

/* Pagination function */
/* src: http://www.wpbeginner.com/wp-themes/how-to-add-numeric-pagination-in-your-wordpress-theme/ */
function pagination($paged){
	if( is_singular() )
		return;

	global $wp_query;

	// Stop execution if there's only 1 page
	if( $wp_query->max_num_pages <= 1 )
		return;

	$max = intval( $wp_query->max_num_pages );

	// Add current page to the array
	if ( $paged >= 1 )
		$links[] = $paged;

	// Add the pages around the current page to the array
	if ( $paged >= 3 ) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if ( ( $paged + 2 ) <= $max ) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	echo '<div class="pagination">';
	echo '<div class="clear scratch"></div>';
	echo '<ul>';

	// Previous Post Link
	if ( get_previous_posts_link() )
		echo '<li class="previous-page">' . get_previous_posts_link() . '</li>';

	// Link to first page, plus ellipses if necessary
	if ( !in_array( 1, $links ) ) {
		$class = 1 == $paged ? 'active' : '';
		echo '<li class="' . $class . '">';
			echo '<a href="' . esc_url( get_pagenum_link( 1 ) ) . '">1</a>';
		echo '</li>';
		if ( !in_array( 2, $links ) )
			echo '<li>…</li>';
	}

	// Link to current page, plus 2 pages in either direction if necessary
	sort( $links );
	foreach ( $links as $link ) {
		$class = $paged == $link ? 'active' : '';
		echo '<li class="' . $class . '">';
			echo '<a href="' . esc_url( get_pagenum_link( $link ) ) . '">' . $link . '</a>';
		echo '</li>';
	}

	// Link to last page, plus ellipses if necessary
	if ( !in_array( $max, $links ) ) {
		if ( !in_array( $max - 1, $links ) )
			echo '<li>…</li>';
		$class = $paged == $max ? 'active' : '';
		echo '<li class="' . $class . '">';
			echo '<a href="' . esc_url( get_pagenum_link( $max ) ) . '">' . $max . '</a>';
		echo '</li>';
	}

	// Next Post Link
	if ( get_next_posts_link() )
		echo '<li class="next-page">' . get_next_posts_link() . '</li>';

	echo '</ul>';
	echo '<div class="clear scratch"></div>';
	echo '</div>';

}

/* ************ SEARCH ONLY THROUGH POSTS ************ */
// s_ha_dum answer --> http://wordpress.stackexchange.com/questions/106961/pagination-wont-work-with-search-results-template
function SearchFilter($query) {
	if ($query->is_search) {
		$query->set('post_type','post');
		$query->set('posts_per_page',5);
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

    $the_excerpt = '<p>' . $the_excerpt . '
											<a href="' . get_permalink($post_id) . '" class="read-more">
												Seguir leyendo
											</a>
										</p>';

    return $the_excerpt;
}

/* ************ English to spanish day name translation ************ */
function getSpanishDayName($englishDay){
	switch(strtolower($englishDay)){
		case 'sunday'    : return 'domingo';
		case 'monday'    : return 'lunes';
		case 'tuesday'   : return 'martes';
		case 'wednesday' : return 'miércoles';
		case 'thursday'  : return 'jueves';
		case 'friday'    : return 'viernes';
		case 'saturday'  : return 'sábado';
		default          : return null;
	}
}

/* ************ English to spanish month name translation ************ */
function getSpanishMonthName($englishMonth){
  switch(strtolower($englishMonth)){
    case 'january'   :  return 'enero';
    case 'february'  :  return 'febrero';
    case 'march'     :  return 'marzo';
    case 'april'     :  return 'abril';
    case 'may'       :  return 'mayo';
    case 'june'      :  return 'junio';
    case 'july'      :  return 'julio';
    case 'august'    :  return 'agosto';
    case 'september' :  return 'septiembre';
    case 'october'   :  return 'octubre';
    case 'november'  :  return 'noviembre';
    case 'december'  :  return 'diciembre';
    default          :  return null;
  }
}

/* ************ Display date in spanish ************ */
function displayDateInSpanish($date){
  return getSpanishDayName($date->format('l')) . ' ' . $date->format('j') . ' de ' . getSpanishMonthName($date->format('F'));
}

/* Render post as part of a list of posts. */
function renderPostInList($post_id){
	echo '
	<div class="clear scratch"></div>
	<div class="post">
		<div class="post-thumbnail">
			<a href="' . get_permalink($post_id) . '">' .
				get_the_post_thumbnail($post_id, 'recent-post-thumbnail') .
			'</a>
		</div>
		<div class="post-info-container">
			<div class="post-date">' .
				get_the_time(get_option('date_format'), $post_id) .
			'</div>
			<div class="post-title">
				<a href="' . get_permalink($post_id) . '">' .
					get_the_title($post_id) .
				'</a>
			</div>
			<div class="post-excerpt">' . get_excerpt_by_id($post_id) .
			'</div>
			<div class="post-footer">
				<div class="clear scratch"></div>
				<div class="post-comment-count" >
          <span class="fa fa-comments"></span>
					<a href="' . get_permalink($post_id) . '#disqus_thread" data-disqus-identifier="buenosairesrojosangre-' . get_the_ID($post_id) . '" ></a>
				</div>

				<div class="fb-like post-fb-like" data-href="' . get_permalink($post_id) . '" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>

				<a href="http://twitter.com/share" class="twitter-share-button" data-url="' . get_permalink($post_id) . '" data-via="wpbeginner" data-text="' . get_the_title($post_id) . '" data-count="horizontal">Tweet</a>
			</div>
		</div>
	</div>';
}

function inline_sponsors_css() {
  global $post;
  if ($post->post_name == 'auspiciantes') {
    $css = file_get_contents(dirname(__FILE__) . '/sponsorsStyles.css');
    echo '<style type="text/css">' . $css . '</style>';
  }
}

add_action('wp_head','inline_sponsors_css');
