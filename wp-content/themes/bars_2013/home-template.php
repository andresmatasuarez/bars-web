<?php
/**
 *
	Template Name: Home template
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			
			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
						<div class="entry-thumbnail">
							<?php the_post_thumbnail(); ?>
						</div>
						<?php endif; ?>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<div id="slider">
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
						
						<div class="bars-recent-posts">
							<?php
								$recent_posts = wp_get_recent_posts( array('numberposts' => '6') );
								foreach( $recent_posts as $recent ){
									if (get_post_thumbnail_id($recent["ID"]) != ''){
										echo '<div class="latest-post">';
											echo '<div class="post-thumbnail">' . get_the_post_thumbnail($recent["ID"], 'recent-post-thumbnail');
											echo '</div>';
											echo '<div class="post-date">';
												echo get_the_time(get_option('date_format'), $recent["ID"]);
											echo '</div>';
											echo '<div class="post-title">';
												echo '<a href="' . get_permalink($recent["ID"]) . '">';
													echo $recent["post_title"];
												echo '</a>';
											echo '</div>';
										echo '</div>';
									}
								}
							?>
						
						</div><!-- .entry-content -->
						
						<div class="bars-recent-posts-footer"></div>
						
					</div><!-- .entry-content -->

				</article><!-- #post -->
			<?php endwhile; ?>

		</div><!-- #content -->
		
	</div><!-- #primary -->
	
			
<?php get_sidebar(); ?>
	

<?php get_footer(); ?>