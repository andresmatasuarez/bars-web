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

					<div id="slider" >
						<?php
							$recent_posts = wp_get_recent_posts( array('numberposts' => '6') );
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
								<div class="slider-control left circle"><div class="arrow"></div></div>
								<div class="slider-control right circle"><div class="arrow"></div></div>
							</div>
					</div>
					
					<div id="news" >
					
					</div>
				</div>
				
				<div id="sidebar-container">
					<?php get_sidebar(); ?>
				</div>
			</div>
		</div>
	</div>
	
	<?php get_footer(); ?>