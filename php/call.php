<?php
/*
 * Template Name: tmpl_call
 *
 * @package WordPress
 * @subpackage bars2013
 */

	require_once 'editions.php';

	get_header();

	$edition = Editions::current();

	$from          = Editions::from($edition);
	$to            = Editions::to($edition);
	$call          = Editions::call($edition);
	$call_deadline = Editions::callDeadline($edition);

	$year = $from->format('Y');
	if (isset($call['form'])) {
		$terms = str_replace('%%FORM%%', $call['form'], $call['terms']);
	} else {
		$terms = $call['terms'];
	}

	$call_is_closed = strtotime('now') > strtotime($call['to']);

?>

					<div id="page-call" class="page <?php echo ($call_is_closed ? 'call-is-closed' : '') ?>" >

						<div class="call-is-closed-message">
							<div>
								<span class="fa fa-exclamation-circle"></span>
							</div>
							<h1>Convocatoria cerrada hasta el año que viene</h1>
							<h3>Las bases se dejan a la vista para que los interesados se vayan preparando.</h3>
							<h3>Los links a los formularios de inscripción se encuentran deshabilitados hasta la próxima convocatoria.</h3>
						</div>

						<div class="scratch"></div>

						<div class="page-header">
								Convocatoria
								<?php echo $edition['number']; ?>
								Festival Buenos Aires Rojo Sangre
							<br />
							<span class="subheader">
								[ Semana del
								<?php echo $from->format('j'); ?>
								de
								<?php echo getSpanishMonthName($from->format('F')); ?>
								al
								<?php echo $to->format('j'); ?>
								de
								<?php echo getSpanishMonthName($to->format('F')); ?>
								de
								<?php echo $year; ?>
								--- Recepción de material hasta:
								<?php echo $call_deadline->format('j'); ?>
								de
								<?php echo getSpanishMonthName($call_deadline->format('F')); ?>
								de
								<?php echo $year; ?>
								]
							</span>
							<br />
							<?php if (isset($call['terms_en'])) { ?>
								<a class="subheader" href="<?php echo get_bloginfo('template_directory') . $call['terms_en']; ?>" target="_blank">Terms in English</a>
							<?php } ?>
						</div>

						<div class="scratch"></div>

						<div class="basis text-opensans">

							<?php echo $terms; ?>

						</div>

<!--
						<table class="banners">
							<tr>
								<td>
									<a href="http://www.festhome.com/" target="blank"><img src="http://festhome.com/img/festivalkit/festivalkit_small_spa.png"></a>
								</td>
								<td>
									<a href="http://www.moviebeta.com/" target="blank"><img src="http://festival.movibeta.com/web/views/images/user_logo_header.png"></a>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<a href="http://www.clickforfestivals.com/" target="blank"><img src="http://www.clickforfestivals.com/images/logo_cabecera.jpg"></a>
								</td>
								<td>
								</td>
							</tr>
						</table>
-->

						<div class="scratch"></div>

						<div class="basis-footer text-oswald">
							<?php if (isset($call['form'])) { ?>
								<a href="<?php echo $call['form'] ?>" target="blank" >Ir al formulario online</a> |
							<?php } ?>
							Ver la hoja de autorización

							<?php
								foreach ($call['authorization'] as $key => $value) {
									echo '<a href="' . get_bloginfo('template_directory') . $value . '" target="blank">' . strtoupper($key) . '</a> ';
								}
							?>
						</div>

					</div>

	<?php
		get_sidebar();
		get_footer();
	?>
