<?php
/*
 * Template Name: tmpl_selection
 *
 * @package WordPress
 * @subpackage bars2013
 */

	require_once 'helpers.php';
	require_once 'elements.php';
	require_once 'editions.php';

	get_header();

	$latestEdition = Editions::current();
	if (!isset($_GET['edition'])){
	  $currentEdition = $latestEdition;
	} else {
		$currentEdition = Editions::getByNumber(htmlspecialchars($_GET['edition']));
	}

	$venues = Editions::venues($currentEdition);
?>

<script>
	window.LATEST_EDITION = <?php echo $latestEdition['number'] ?>;
	window.CURRENT_EDITION = <?php echo $currentEdition['number'] ?>;
</script>

					<div id="page-selection" class="page" >

						<div class="page-header">

							<div class="page-title">
								<div class="title-text">
									Programación <?php echo Editions::getTitle($currentEdition); ?>
								</div>
								<?php
									renderSelector('edition-selector', 'Ediciones anteriores', Editions::getMapOfTitleByNumber());
								?>
							</div>

							<span class="subheader">
							<?php
								if (Editions::areDatesDefined($currentEdition)) {
									$days           = Editions::days($currentEdition);
									$year           = $days[0]->format('Y');
									$firstDayName   = ucwords(getSpanishDayName($days[0]->format('l')));
									$firstDayNumber = $days[0]->format('d/m');
									$lastDayName    = ucwords(getSpanishDayName($days[count($days) - 1]->format('l')));
									$lastDayNumber  = $days[count($days) - 1]->format('d/m');

									echo 'Semana del ' . $firstDayName . ' ' . $firstDayNumber;
									echo ' al ' . $lastDayName . ' ' . $lastDayNumber . ' de ' . $year . '';
								}
							?>
							</span>
						</div>

						<div class="scratch"></div>

						<?php
							if (countMovieEntriesForEdition($currentEdition) === "0") {
								if (Editions::isCallClosed($currentEdition)) {
									if ($currentEdition === Editions::current()) {
										renderWarningMessage(
											'La programación se está definiendo',
											'Estate atento!<br />
											La grilla para este año ya se encuentra en proceso de selección.<br />
											Estará disponible en los próximos días. <br />'
										);
									} else {
										$linkToContact = get_permalink(get_page_by_path('contacto'));
										renderWarningMessage(
											'La programación para esta <br /> edición del festival no está disponible',
											"Volvé a intentar más tarde o <br /> comunicate con nosotros usando el <a href=\"{$linkToContact}\">formulario de contacto</a>."
										);
									}
								} else {
									$linkToCall = get_permalink(get_page_by_path('convocatoria'));
									renderWarningMessage(
										'La convocatoria para este año <br /> aún está abierta!',
										"No pierdas la oportunidad de proyectar tu película/corto en el festival! <br />
										Echale un vistazo a los <a href=\"{$linkToCall}\">términos, bases y condiciones</a>
										y envianos el material."
									);
								}
							} else {
						?>
								<div class="selection-schedule-container">
									<div class="schedule-header">
										<div class="schedule-filters">
											<?php
												$movieSections = array('all' => 'Todas');
												foreach(getMovieSectionsForEdition($currentEdition) as $section){
													$movieSections[$section]= $sections[$section]['label'];
												}

												renderSelector('schedule-section-filters', 'Secciones', $movieSections, 'movie-section-selector');
											?>
										</div>
									</div>

									<div class="schedule">
									<?php
										$parity = 0;

										// Render movies that are available for the whole duration of the festival,
										// not tied to any specific day/time.
										$posts = getMoviesAndMovieBlocksAvailableForTheWholeDurationOfTheFestival($currentEdition);
										if (!empty($posts)){
											// echo '<div class="page-header">Disponibles durante todo el festival</div>';
											// echo '<div class="scratch clear"></div>';

											$dayDisplay = "<div class=\"schedule-day-name\">MIRALAS<br/>CUALQUIER DÍA</div>";
											renderScheduleDay($currentEdition['number'], DATE_FULL_TAG, $dayDisplay, $parity % 2 === 0, $posts, $venues);
											echo '<div class="scratch clear"></div>';
											echo '<br />';
											echo '<div class="scratch clear"></div>';

											$parity++;
										}

										// For each festival day, show all the films that will be screened that day.
										foreach($days as $key => $day){
											$posts = getMoviesAndMovieBlocks($currentEdition, $day);

											// If no movies for this day, then continue with the next one.
											if (empty($posts)){
												continue;
											}

											$dayName = ucwords(getSpanishDayName($day->format('l')));
											$dayNumber = $day->format('d');
											$dayDisplay = "<div class=\"schedule-day-name\">{$dayName}</div><div class=\"schedule-day-number\">{$dayNumber}</div>";
											renderScheduleDay($currentEdition['number'], $day, $dayDisplay, $parity % 2 === 0, $posts, $venues);

											echo '<div class="scratch clear"></div>';

											$parity++;
										}
									?>
									</div>

								</div>
						<?php
							}
						?>
					</div>

					<div class="fancybox-display-ugly-fix" style="display: none;">
						<div id="movie-container">
						</div>
					</div>

	<?php
		get_sidebar();
		get_footer();
	?>
