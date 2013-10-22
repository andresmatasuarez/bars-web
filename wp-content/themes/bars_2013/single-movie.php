<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

?>

<div id="movie">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<div class="movie-screenings-info">
					<div class="movie-section">
						<?php echo sectionByValue(get_post_meta($post->ID, '_movie_section', true));?>
					</div>
					
					<div class="movie-screenings">
						<div class="movie-screenings-caption">Mirala los días</div>
					<?php
						$datetimes = array_map('trim', explode(',', get_post_meta($post->ID, '_movie_screenings', true)));
						foreach($datetimes as $key => $datetime){
							$dt = preg_split('/[\s]+/', $datetime);
							$dayName = ucwords(getSpanishDayName(DateTime::createFromFormat('m-d-Y', $dt[0])->format('l')));
							$dayNumber = DateTime::createFromFormat('m-d-Y', $dt[0])->format('d');
							$time = $dt[1];
							echo '<div class="movie-screening">';
								echo '<div class="movie-screening-dayname">';
									echo $dayName;
								echo '</div>';
								echo '<div class="movie-screening-daynumber">';
									echo $dayNumber;
								echo '</div>';
								echo '<div class="movie-screening-hour">';
									echo $time;
								echo '</div>';
							echo '</div>';
						}
					?>
					</div>
				</div>

				<article id="movie-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="movie-info-container">
						<div class="movie-image">
							<?php the_post_thumbnail('movie-post-image'); ?>
						</div>
						<div class="movie-info">
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
							<div class="movie-directors">
								<?php
									$directors = explode(',', get_post_meta($post->ID, '_movie_directors', true));
									
									if (sizeof($directors) != 0){
										echo 'Directores: ' . trim(current($directors));
									
										foreach(array_slice($directors, 1) as $director){
											echo ', ' . trim($director);
										}
									}
								
								?>
							</div>
							<div class="movie-cast">
								<?php
									$cast = explode(',', get_post_meta($post->ID, '_movie_cast', true));
									
									if (sizeof($cast) != 0){
										echo 'Elenco: ' . current($cast);
									
										foreach(array_slice($cast, 1) as $cast){
											echo ', ' . $cast;
										}
									}
								?>
							</div>
						</div>
						<div class="movie-synopsis">
							<p><?php echo get_post_meta($post->ID, '_movie_synopsis', true); ?></p>
						</div>
					</div>
					
					<div class="movie-trailer-container">
						<?php echo wp_oembed_get( get_post_meta($post->ID, '_movie_trailer', true), array( 'width' => '100%' ) ); ?>
					</div>
			</article>

			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->
</div>