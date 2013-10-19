<?php
/**
 * Template Name: 'Programacion' template
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
						<h1 class="entry-title"><?php the_title(); ?></h1>
						<div class="scratch subtle-shadow">
						</div>
					</header><!-- .entry-header -->

					<div class="entry-content">
						
						<div class="movie-posts">
						
							<?php
								// Filter only 'movie' posts from 'bars14' edition.
								$type = 'movie';
								$args = array(
									'post_type' => $type,
									'meta_key' => '_movie_edition',
									'meta_value' => 'bars14',
									'post_status' => 'publish',
									'posts_per_page' => -1,
									'caller_get_posts'=> 1
								);

								$query = null;
								$query = new WP_Query($args);
								while ($query->have_posts()){
									$query->the_post();
									echo '<div class="movie-post">';
										echo '<a href="' . get_permalink($post->ID) . '" rel="lightbox[ababab]">';
										echo '<div class="movie-post-thumbnail">' . get_the_post_thumbnail($post->ID, 'movie-post-thumbnail') . '</div>';
										echo '<div class="movie-post-title">' . $post->post_title . '</div>';
										echo '<div class="movie-post-info">' . get_post_meta($post->ID, '_movie_year', true) . ' - ' . get_post_meta($post->ID, '_movie_country', true) . ' - ' . get_post_meta($post->ID, '_movie_runtime', true) . ' min. </div>';
										echo '</a>';
									echo '</div>';
								}
								
								// Restore global post data stomped by the_post().
								wp_reset_query();
							?>
						
						</div>
						
						
						
					</div><!-- .entry-content -->
					
				</article><!-- #post -->

			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>