<?php
/*
 * Template Name: tmpl_call
 *
 * @package WordPress
 * @subpackage bars2013
 */

	require_once 'helpers.php';
	require_once 'editions.php';

	get_header();

	$edition = Editions::current();

	$from          = Editions::from($edition);
	$to            = Editions::to($edition);
	$call          = Editions::call($edition);
	$call_deadline = Editions::callDeadline($edition);
	$call_is_closed = Editions::isCallClosed($edition);

	$displayYear = intval(is_null($from) ? (new DateTime())->format('Y') : $from->format('Y')) - 1;
	$shouldDisplayTBA = !Editions::areDatesDefined($edition);

	$terms = $call['terms'];
	$terms = str_replace('%%DEADLINE%%', getDateInSpanish($call_deadline), $terms);

	// Replace movies' eligible date in terms. Date fallbacks to current year's first day if
	// festival edition doesn't specifically set it.
	$moviesEligibleFromDate = is_null($call['moviesEligibleFrom']) ? "{$displayYear}-01-01T03:00:00.000Z" : $call['moviesEligibleFrom'];
	$terms = str_replace('%%ELIGIBILE_FROM_DATE%%', getDateInSpanish(parseDate($moviesEligibleFromDate)), $terms);

	if (isset($call['form'])) {
		$terms = str_replace('%%FORM%%', $call['form'], $terms);
	}
?>

					<div id="page-call" class="page <?php echo ($call_is_closed ? 'call-is-closed' : '') ?>" >
						<?php $call_is_closed && renderWarningMessage(
							'Convocatoria cerrada hasta el año que viene',
							"Las bases se dejan a la vista para que los interesados se vayan preparando.<br />
							Los links a los formularios de inscripción se encuentran deshabilitados hasta la próxima convocatoria."
						); ?>

						<div class="scratch"></div>

						<div class="page-header">
								Convocatoria
								<?php echo Editions::romanNumerals($edition); ?>
								Festival Buenos Aires Rojo Sangre
							<br />
							<span class="subheader">
								<?php
									if ($shouldDisplayTBA) {
								?>
										<span>TBA</span>
										<?php echo $displayYear; ?>
								<?php
									} else if (Editions::shouldDisplayOnlyMonths($edition)) {
										$sameMonth = $from->format('F') == $to->format('F');
										if ($sameMonth) {
								?>
											<span style="text-transform: capitalize;">
												<?php echo getSpanishMonthName($to->format('F')); ?>
											</span>
											de
											<?php echo $displayYear; ?>
								<?php
										} else {
								?>
											<span style="text-transform: capitalize;">
												<?php echo getSpanishMonthName($from->format('F')); ?>
											</span>
											/
											<?php echo getSpanishMonthName($to->format('F')); ?>
											<?php echo $displayYear; ?>
								<?php
										}
								?>
								<?php
									} else {
										$sameMonth = $from->format('F') == $to->format('F');
										if ($sameMonth) {
								?>
											<?php echo $from->format('j'); ?>
											al
											<?php echo getDateInSpanish($to); ?>
								<?php
										} else {
								?>
											<?php echo $from->format('j'); ?>
											de
											<?php echo getSpanishMonthName($from->format('F')); ?>
											al
											<?php echo getDateInSpanish($to); ?>
									<?php
										}
									?>
								<?php
									}
								?>
								---
								Recepción de material hasta:
								<?php echo getDateInSpanish($call_deadline); ?>
							</span>
							<br />
							<?php if (isset($call['terms_en'])) { ?>
								<a class="subheader" href="<?php echo get_bloginfo('template_directory') . $call['terms_en']; ?>" target="_blank">Terms in English</a>
							<?php } ?>
							<?php if (isset($call['terms_es'])) { ?>
								<a class="subheader" href="<?php echo get_bloginfo('template_directory') . $call['terms_es']; ?>" target="_blank">Términos en Español</a>
							<?php } ?>
						</div>

						<div class="scratch"></div>

						<div class="basis text-opensans">
							<?php echo $terms; ?>
						</div>

						<div class="scratch"></div>

						<div class="basis-footer text-oswald">
							<?php if (isset($call['form'])) { ?>
								<a href="<?php echo $call['form'] ?>" target="blank" >Ir al formulario online</a> |
							<?php } ?>
							Ver la hoja de autorización

							<?php
								$authSheets = $call['authorization'];
								echo ' - ';
								if (isset($authSheets['es'])) {
									echo '<a href="' . get_bloginfo('template_directory') . $authSheets['es']. '" target="blank">ES</a> - ';
								}
								if (isset($authSheets['en'])) {
									echo '<a href="' . get_bloginfo('template_directory') . $authSheets['en']. '" target="blank">EN</a> - ';
								}
								if (isset($authSheets['es_docx'])) {
									echo '<a href="' . get_bloginfo('template_directory') . $authSheets['es_docx']. '" target="blank">ES (.docx)</a> - ';
								}
								if (isset($authSheets['en_docx'])) {
									echo '<a href="' . get_bloginfo('template_directory') . $authSheets['en_docx']. '" target="blank">EN (.docx)</a> -';
								}
							?>
						</div>

					</div>

	<?php
		get_sidebar();
		get_footer();
	?>
