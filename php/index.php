<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage bars2013
 */

	get_header();
?>

					<div id="page-home" class="page" >
						<div class="slider" >
							<?php

								$latestPostsCount = 5;

								$recent_posts = wp_get_recent_posts( array('numberposts' => $latestPostsCount) );
								$position = 0;
								foreach( $recent_posts as $recent ){
									if (get_post_thumbnail_id($recent["ID"]) != ''){
										echo '<div class="slide" slide-position="' . $position++ . '" permalink="' . get_permalink($recent["ID"]) . '">';

											echo '<img src="' . wp_get_attachment_url( get_post_thumbnail_id($recent["ID"])) . '"></img>';
											echo '<div class="slide-caption">';
												echo '<div class="slide-title">' . $recent["post_title"] . '</div>';
												echo '<div class="slide-excerpt">' . get_excerpt_by_id($recent["ID"]) . '</div>';
											echo '</div>';
										echo '</div>';
									}
								}
							?>
							<div class="slider-controls">
								<div class="slider-control left circle">
									<span class="fa fa-arrow-left"></span>
								</div>
								<div class="slider-control right circle">
									<span class="fa fa-arrow-right"></span>
								</div>
							</div>
						</div>

						<div class="latest-posts-header page-section-title">
							Últimas novedades
						</div>

						<div id="latest-posts" class="posts">
							<?php
								$recent_posts = wp_get_recent_posts( array('numberposts' => $latestPostsCount) );
								foreach( $recent_posts as $recent ){
									renderPostInList($recent['ID']);
								}
							?>
						</div>

						<div class="more-posts">
							<a href="<?php echo get_page_link(get_page_by_title('Novedades')->ID); ?>">
								Ver todas
							</a>
						</div>

					</div>


	<?php
		get_sidebar();
		get_footer();
	?>
