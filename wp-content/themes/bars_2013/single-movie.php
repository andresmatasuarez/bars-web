<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

?>

<div class="movie">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<article id="movie-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="movie-image-container">
						<div class="movie-trailer-container"><?php echo wp_oembed_get( get_post_meta($post->ID, '_movie_trailer', true) ); ?></div>
					</div>
					<div class="movie-info-container">
						<!-- <div class="movie-image">
							<?php //the_post_thumbnail('movie-post-image'); ?>
						</div> -->
						<div class="movie-year movie-country"><?php echo get_post_meta($post->ID, '_movie_year', true); ?> | <?php echo get_post_meta($post->ID, '_movie_country', true);?></div>
						<div class="movie-title"><?php the_title(); ?></div>
						<div class="movie-links">
							<?php
								$website = get_post_meta($post->ID, '_movie_website', true);
								$imdb = get_post_meta($post->ID, '_movie_imdb', true);
								
								if ($website != '')
									echo '<a target="_blank" href="' . $website . '">Sitio oficial</a> | ';
								
								if ($imdb != '')
									echo '<a target="_blank" href="' . $imdb . '">IMDB</a>';
							?>
						</div>
						<div class="movie-runtime"><?php echo get_post_meta($post->ID, '_movie_runtime', true); ?> minutos</div>
						<div class="movie-directors"><?php
							$directors = explode(',', get_post_meta($post->ID, '_movie_directors', true));
							
							echo 'Directores: ';
							
							if (sizeof($directors) != 0){
								echo trim(current($directors));
							
								foreach(array_slice($directors, 1) as $director){
									echo ', ' . trim($director);
								}
							}
						
						?></div>
						<div class="movie-cast"><?php
							$cast = explode(',', get_post_meta($post->ID, '_movie_cast', true));
							
							echo 'Elenco: ';
							
							if (sizeof($cast) != 0){
								echo current($cast);
							
								foreach(array_slice($cast, 1) as $cast){
									echo ', ' . $cast;
								}
							}
						
						?></div>
						<div class="movie-synopsis"><p><?php echo get_post_meta($post->ID, '_movie_synopsis', true); ?></p></div>
					</div>
			</article>

			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->
</div>