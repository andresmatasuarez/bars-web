<?php
/**
 * Template Name: 'Programacion' template
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php

			// BARS14 edition hardcode. TODO -> remove hardcoding and extend for consequent editions.
			$edition = 'bars14';
			$editionLabel = 'BARS 14';
			
			// BARS14 ordered days array.
			$days = array(	0 => '10-31-2013', 1 => '11-01-2013', 2 => '11-02-2013',
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
								$args = array(
									'post_type' => 'movie',
									'meta_query' => array(
											array( 'key' => '_movie_edition', 'value' => $edition ),
											array( 'key' => '_movie_screenings', 'value' => $day, 'compare' => 'LIKE' )
									),
									'post_status' => 'publish',
									'posts_per_page' => -1,
									'caller_get_posts'=> 1
								);
								
								$query = new WP_Query($args);
								
								// If no movies for this day, then continue with the next one.
								if (!$query->have_posts()){
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
										
										while ($query->have_posts()){
											$query->the_post();
											$sectionValue = get_post_meta($post->ID, '_movie_section', true);
											
											// Extract screening hour for this day.
											$screenings = array_map('trim', explode(',', get_post_meta($post->ID, '_movie_screenings', true)));
											foreach($screenings as $key => $screening){
												$datetime = preg_split('/[\s]+/', $screening);
												if ($datetime[0] == $day)
													$time = $datetime[1];
											}
											
											echo '<div class="movie-post" section="' . $sectionValue . '">';
												echo '<div class="movie-post-hour">' . $time . '</div>';
												echo '<a href="' . get_permalink($post->ID) . '" rel="lightbox[ababab]">';
													echo '<div class="movie-post-thumbnail">';
														if ($sectionValue != ''){
															echo '<div class="movie-post-section">' . sectionByValue($sectionValue) . '</div>';
														}
														echo get_the_post_thumbnail($post->ID, 'movie-post-thumbnail');
													echo '</div>';
													echo '<div class="movie-post-title">';
														echo '<span class="movie-post-title-text">';
															echo $post->post_title;
														echo '</span>';
													echo '</div>';
													echo '<div class="movie-post-info">' . get_post_meta($post->ID, '_movie_year', true) . ' - ' . get_post_meta($post->ID, '_movie_country', true) . ' - ' . get_post_meta($post->ID, '_movie_runtime', true) . ' min. </div>';
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