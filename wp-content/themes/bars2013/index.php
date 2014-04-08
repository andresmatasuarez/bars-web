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
								<div class="slider-control left circle"><div class="arrow"></div></div>
								<div class="slider-control right circle"><div class="arrow"></div></div>
							</div>
					</div>
					
					<div id="page-home" >
						<div class="latest-posts-header posts-header">
							Últimas novedades
						</div>
						
						<div id="latest-posts" class="posts">
							<?php
								$recent_posts = wp_get_recent_posts( array('numberposts' => $latestPostsCount) );
								foreach( $recent_posts as $recent ){
									if (get_post_thumbnail_id($recent["ID"]) != ''){
										echo '<div class="clear scratch"></div>';
										echo '<div class="post">';
											echo '<div class="post-thumbnail">';
												echo '<a href="' . get_permalink($recent["ID"]) . '">';
													echo get_the_post_thumbnail($recent["ID"], 'recent-post-thumbnail');
												echo '</a>';
											echo '</div>';
											echo '<div class="post-info-container">';
												echo '<div class="post-date">';
													echo get_the_time(get_option('date_format'), $recent["ID"]);
												echo '</div>';
												echo '<div class="post-title">';
													echo '<a href="' . get_permalink($recent["ID"]) . '">';
														echo $recent["post_title"];
													echo '</a>';
												echo '</div>';
												echo '<div class="post-excerpt">' . get_excerpt_by_id($recent["ID"]) . '</div>';
												echo '<div class="post-footer">';
													echo '<div class="clear scratch"></div>';
							?>
													<div class="post-comment-count" >
														<a href="<?php echo get_permalink($recent["ID"]); ?>#disqus_thread" data-disqus-identifier="buenosairesrojosangre-<?php echo $recent["ID"]; ?>" ></a>
													</div>
													
													<div class="fb-like post-fb-like" data-href="<?php get_permalink($recent["ID"]); ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
													
													<a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink($recent["ID"]); ?>" data-via="wpbeginner" data-text="<?php $recent["post_title"]; ?>" data-count="horizontal">Tweet</a>
							<?php
												echo '</div>';
											echo '</div>';
										echo '</div>';
									}
								}
							?>
						</div>
						
						<div class="clear scratch"></div>
						
						<div class="more-posts-header posts-header">
							<a href="?page_id=14">
								Ver todas
							</a>
						</div>
						
					</div>
				
				
	<?php
		get_sidebar();
		get_footer();
	?>