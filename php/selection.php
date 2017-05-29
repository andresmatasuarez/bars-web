<?php
/*
 * Template Name: tmpl_selection
 *
 * @package WordPress
 * @subpackage bars2013
 */

	require_once 'editions.php';

	get_header();

	if (!isset($_GET['edition'])){
		// TODO-TEMP Search all by TODO-TEMP
		// $currentEdition = Editions::current();
		$currentEdition = Editions::getByNumber(17);
		// TODO-TEMP
	} else {
		$currentEdition = Editions::getByNumber(htmlspecialchars($_GET['edition']));
	}

	$days = Editions::days($currentEdition);
?>

<script>
	window.CURRENT_EDITION = <?php echo $currentEdition['number'] ?>;
</script>

					<div id="page-selection" class="page" >

						<div class="page-header">

							Programación <?php echo $currentEdition['title']; ?>

							<select id="festival-edition-filters" >
								<?php
									foreach(Editions::all() as $edition){
										echo '<option value="' . $edition['number'] . '">' . $edition['title'] . '</option>';
									}
								?>
							</select>

							<br/>
							<span class="subheader">
							<?php
								$year           = $days[0]->format('Y');
								$firstDayName   = ucwords(getSpanishDayName($days[0]->format('l')));
								$firstDayNumber = $days[0]->format('d/m');
								$lastDayName    = ucwords(getSpanishDayName($days[count($days) - 1]->format('l')));
								$lastDayNumber  = $days[count($days) - 1]->format('d/m');

								echo '[ Semana del ' . $firstDayName . ' ' . $firstDayNumber;
								echo ' al ' . $lastDayName . ' ' . $lastDayNumber . ' de ' . $year . ' ]';
							?>
							</span>
						</div>

						<div class="scratch"></div>

						<div class="selection-schedule-container">
							<div class="schedule-header">
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
							</div>

							<div class="schedule">

							<?php
								$parity = 0;

								// For each festival day, show all the films that will be screened that day.
								foreach($days as $key => $day){

									$posts = getMoviesAndMovieBlocks($currentEdition, $day);

									// If no movies for this day, then continue with the next one.
									if (count($posts) == 0){
										continue;
									}

									$dayName = ucwords(getSpanishDayName($day->format('l')));
									$dayNumber = $day->format('d');

									echo '<div class="schedule-day ' . ($parity % 2 == 0 ? 'even' : 'odd') . '">';
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
													if ($datetime[0] == $day->format('m-d-Y')){
														$time = $datetime[1];

														echo '<div class="movie-post" section="' . $sectionValue . '">';
															echo '<div class="movie-post-hour">' . $time . '</div>';
															echo '<a href="#movie-container" link="' . get_post_permalink($post->ID) . '">';
																echo '<div class="movie-post-thumbnail">';
																	echo '<div class="movie-post-section">' . sectionByValue($sectionValue) . '</div>';
																	echo get_the_post_thumbnail($post->ID, 'movie-post-thumbnail');
																echo '</div>';
																echo '<div class="movie-post-title-container">';
																	echo '<div class="movie-post-title">';
																		echo '<span class="movie-post-title-text">';
																			echo get_the_title($post->ID);
																		echo '</span>';
																	echo '</div>';
																	echo '<div class="movie-post-info">' . $info . '</div>';
																echo '</div>';
															echo '</a>';
														echo '</div>';

													}
												}

											}

											// Restore global post data stomped by the_post().
											wp_reset_query();


										echo '</div>';
									echo '</div>';
									echo '<div class="scratch clear"></div>';

									$parity++;
								}

							?>

							</div>
						</div>

					</div>

					<div class="fancybox-display-ugly-fix" style="display: none;">
						<div id="movie-container">
						</div>
					</div>

	<?php
		get_sidebar();
		get_footer();
	?>
