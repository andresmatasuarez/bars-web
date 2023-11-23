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

			<div id="react-root-selection"></div>
			<script>
				<?php
					$movieSections = array();
					foreach(getMovieSectionsForEdition($currentEdition) as $sectionId){
						$movieSections[$sectionId]= getMovieSectionLabel($sectionId);
					}

					$movies = getMoviesAndMovieBlocks($currentEdition);
					$prepareMovie = function($movie) {
						setup_postdata($movie); // post object only has post ID.
						return prepareMovieForDisplaying($movie);
					};

					echo 'window.MOVIES = ' . json_encode(array_map($prepareMovie, $movies)) . ';';
					echo 'window.MOVIE_SECTIONS = ' . json_encode($movieSections) . ';';
				?>
			</script>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/selection.iife.js"></script>

	<?php
		}
	?>
</div>

<?php
	get_sidebar();
	get_footer();
?>
