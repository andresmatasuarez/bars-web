<?php
/*
 * Template Name: tmpl_sponsors
 *
 * @package WordPress
 * @subpackage bars2013
 */
	require_once 'helpers.php';
	require_once 'elements.php';
	require_once 'editions.php';

	get_header();

	$latestEdition = Editions::current();
?>
				<div id="page-sponsors" class="page">

					<?php
						$noSponsorsDefined = !isset($latestEdition['sponsors']) || empty($latestEdition['sponsors']);

						if ($noSponsorsDefined) {
							$linkToContact = get_permalink(get_page_by_path('contacto'));
							renderWarningMessage(
							  'Los auspiciantes para esta <br /> edición del festival no están disponibles',
							  "Volvé a intentar más tarde o <br /> comunicate con nosotros usando el <a href=\"{$linkToContact}\">formulario de contacto</a>."
							);
						} else {
							foreach($latestEdition['sponsors'] as $section) {
					?>
								<div class="page-header">
									<?php echo $section['title'] ?>
								</div>

								<div class="clear scratch"></div>

								<div class="sponsors">
									<?php
 									foreach($section['items'] as $sponsorItem) {
 										$logoPath = get_bloginfo('template_directory') . $sponsorItem['logo'];
 									?>
 										<div class="sponsor" style="background-image: url('<?php echo $logoPath ?>');">
	 									<?php
	 										$isClickableSponsor = isset($sponsorItem['href']) && !empty($sponsorItem['href']);
	 										if ($isClickableSponsor) {
	 									?>
	 											<a target="_blank" rel="noopener noreferrer" href="<?php echo $sponsorItem['href'] ?>" alt="<?php echo $sponsorItem['alt'] ?>" ></a>
										<?php
											}
										?>
									  </div>
									<?php
 									}
									?>
								</div>
					<?php
							}
						}
					?>
				</div>
	<?php
		get_sidebar();
		get_footer();
	?>
