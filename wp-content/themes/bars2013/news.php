<?php
/**
 * Template Name: tmpl_news
 *
 * @package WordPress
 * @subpackage bars2013
 */

	get_header();
?>
					
					<div class="page-header">
						Novedades
					</div>
					
					<div id="page-news" >
					
						<?php
							$paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
							$args = array( 'post_type' => 'post', 'posts_per_page' => 5, 'paged' => $paged );							
							$wp_query = new WP_Query($args);
						?>
						
						<div class="latest-posts">
						
							<?php
								while ( have_posts() ) :
									the_post();
									if (get_post_thumbnail_id(get_the_ID()) != ''){
										echo '<div class="clear scratch"></div>';
										echo '<div class="post">';
											echo '<div class="post-thumbnail">';
												echo '<a href="' . get_permalink() . '">';
													the_post_thumbnail('recent-post-thumbnail');
												echo '</a>';
											echo '</div>';
											echo '<div class="post-info-container">';
												echo '<div class="post-date">';
													echo get_the_time(get_option('date_format'));
												echo '</div>';
												echo '<div class="post-title">';
													echo '<a href="' . get_permalink() . '">';
														the_title();
													echo '</a>';
												echo '</div>';
												echo '<div class="post-excerpt">' . get_excerpt_by_id(get_the_ID()) . '</div>';
												echo '<div class="post-footer">';
													echo '<div class="clear scratch"></div>';
							?>
													<div class="post-comment-count" >
														<a href="<?php echo get_permalink(); ?>#disqus_thread" data-disqus-identifier="buenosairesrojosangre-<?php the_ID(); ?>" ></a>
													</div>
													
													<div class="fb-like post-fb-like" data-href="<?php get_permalink(); ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
													
													<a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink(); ?>" data-via="wpbeginner" data-text="<?php the_title(); ?>" data-count="horizontal">Tweet</a>
							<?php
												echo '</div>';
											echo '</div>';
										echo '</div>';
									}
								endwhile;
							?>
						</div>
						
						<?php pagination($paged); ?>
						
					</div>
				
				
	<?php
		get_sidebar();
		get_footer();
	?>