<?php
/**
 * Template Name: 'Programacion' template
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php

			// BARS14 edition hardcode. TODO -> remove hardcoding and extend for consequent editions.
			$edition = 'bars15';
			$editionLabel = 'BARS 15';

			/*
			// BARS14 ordered days array.
			$days = array(	0 => '10-31-2013', 1 => '11-01-2013', 2 => '11-02-2013',
						3 => '11-03-2013', 4 => '11-04-2013', 5 => '11-05-2013',
						6 => '11-06-2013');
			*/

			// BARS15 ordered days array.
			$days = array(
				0 => '10-31-2013', 1 => '11-01-2013', 2 => '11-02-2013',
				3 => '11-03-2013', 4 => '11-04-2013', 5 => '11-05-2013',
				6 => '11-06-2013');

			/* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
						<div class="entry-thumbnail">
							<?php the_post_thumbnail(); ?>
						</div>
						<?php endif; ?>
						<h1 class="entry-title"><?php echo get_the_title() . ' ' . $editionLabel; ?></h1>
						<div class="scratch subtle-shadow"></div>
						<div class="schedule-week">
						<?php
							$year = DateTime::createFromFormat('m-d-Y', $days[0])->format('Y');
							$firstDayName = ucwords(getSpanishDayName(DateTime::createFromFormat('m-d-Y', $days[0])->format('l')));
							$firstDayNumber = DateTime::createFromFormat('m-d-Y', $days[0])->format('d/m');
							$lastDayName = ucwords(getSpanishDayName(DateTime::createFromFormat('m-d-Y', $days[count($days) - 1])->format('l')));
							$lastDayNumber = DateTime::createFromFormat('m-d-Y', $days[count($days) - 1])->format('d/m');
							echo 'Semana del ' . $firstDayName . ' ' . $firstDayNumber;
							echo ' al ' . $lastDayName . ' ' . $lastDayNumber . ' de ' . $year;
						?>
						</div>
					</header><!-- .entry-header -->

					<div class="entry-content">
					
						<div class="schedule-filters">
							<select id="schedule-section-filters" >
								<option value="all">Todas</option>
								<?php 
									foreach($sections as $section){
										echo '<option value="' . $section['value'] . '">' . $section['label'] . '</option>';
									}
								?>
							</select>
						</div>
						
						<div id="schedule">
						
						<?php
							
							// For each festival day, show all the films that will be screened that day.
							foreach($days as $key => $day){
							
								$posts = getMoviesAndMovieBlocks($edition, $day);
								
								// If no movies for this day, then continue with the next one.
								if (count($posts) == 0){
									continue;
								}
								
								
								$dayName = ucwords(getSpanishDayName(DateTime::createFromFormat('m-d-Y', $day)->format('l')));
								$dayNumber = DateTime::createFromFormat('m-d-Y', $day)->format('d');
								
								echo '<div class="schedule-day ' . ($key % 2 == 0 ? 'even' : 'odd') . '">';
									echo '<div class="schedule-day-info">';
										echo '<div class="schedule-day-name">';
											echo $dayName;
										echo '</div>';
										echo '<div class="schedule-day-number">';
											echo $dayNumber;
										echo '</div>';
									echo '</div>';
									echo '<div class="movie-posts">';
										
										foreach($posts as $post){
											// post object only has post ID.
											setup_postdata($post);
											
											// Preparing data
											$isMovie = get_post_type($post->ID) == 'movie';
											$sectionValue = get_post_meta($post->ID, $isMovie ? '_movie_section' : '_movieblock_section', true);
											$screeningsValue = get_post_meta($post->ID, $isMovie ? '_movie_screenings' : '_movieblock_screenings', true);
											
											if ($isMovie){
												$year = get_post_meta($post->ID, '_movie_year', true);
												$country = get_post_meta($post->ID, '_movie_country', true);
												$runtime = get_post_meta($post->ID, '_movie_runtime', true);
												$info = ($year != '') ? $year : '';
												if ($country != '')
													$info = ($info != '' ? $info . ' - ' : '') . $country;
												if ($runtime)
													$info = ($info != '' ? $info . ' - ' : '') . $runtime . ' min.';
													
											} else {
												$info = get_post_meta($post->ID, '_movieblock_runtime', true) . ' min.';
											}
											
											// Extract screening hour for this day.
											$screenings = array_map('trim', explode(',', $screeningsValue));
											foreach($screenings as $key => $screening){
												$datetime = preg_split('/[\s]+/', $screening);
												if ($datetime[0] == $day){
													$time = $datetime[1];
													break;
												}
											}
											
											echo '<div class="movie-post" section="' . $sectionValue . '">';
												echo '<div class="movie-post-hour">' . $time . '</div>';
												echo '<a href="' . get_permalink($post->ID) . '" rel="lightbox[ababab]">';
													echo '<div class="movie-post-thumbnail">';
														echo '<div class="movie-post-section">' . sectionByValue($sectionValue) . '</div>';
														echo get_the_post_thumbnail($post->ID, 'movie-post-thumbnail');
													echo '</div>';
													echo '<div class="movie-post-title">';
														echo '<span class="movie-post-title-text">';
															echo get_the_title($post->ID);
														echo '</span>';
													echo '</div>';
													echo '<div class="movie-post-info">' . $info . '</div>';
												echo '</a>';
											echo '</div>';
											
										}
										
										// Restore global post data stomped by the_post().
										wp_reset_query();
										
									
									echo '</div>';
								echo '</div>';
								echo '<div class="clear scratch"></div>';
							}
							
						?>
						
						</div>
						
					</div><!-- .entry-content -->
					
				</article><!-- #post -->

			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>