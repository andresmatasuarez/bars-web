<?php
/**
 * Template Name: tmpl_news
 *
 * @package WordPress
 * @subpackage bars2013
 */

	get_header();
	
	$paged = esc_attr(get_query_var('paged') ? absint(get_query_var('paged')) : 1);
	$args = array( 'post_type' => 'post', 'posts_per_page' => 5, 'paged' => $paged);
	$wp_query = new WP_Query($args);
	
?>
					<div id="page-news" class="page">
					
						<div class="page-header">
							Novedades
						</div>
					
						<div id="latest-posts" class="posts">
						<?php
							while ( have_posts() ) {
								the_post();
								renderPostInList(get_the_ID());
							}
						?>
						</div>
						
						<?php pagination($paged); ?>
						
					</div>
				
				
	<?php
		get_sidebar();
		get_footer();
	?>