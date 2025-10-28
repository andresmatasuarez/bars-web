<?php
/*
 * Template Name: tmpl_press
 *
 * @package WordPress
 * @subpackage bars2013
 */

	get_header();

	$edition = Editions::current();

	$pressPassesDeadline = Editions::getPressPassesDeadline($edition);
	$pressPassesPickupDates = Editions::getPressPassesPickupDates($edition);
	$pressPassesPickupLocations = Editions::getPressPassesPickupLocations($edition);
	$pressPassesPickupAdditionalInfo = Editions::getPressPassesAdditionalInfo($edition);
	$pressPassesCredentialsFormURL = Editions::getPressPassesCredentialsFormURL($edition);
?>
				<div id="page-press" class="page">
					<div class="page-header">
						Prensa
					</div>

					<div class="scratch"></div>

					<div class="press-main-container text-opensans" >
						<p>
							<h3>Política de acreditaciones</h3>
							<ul>
								<li>
									El proceso de acreditaciones se llevará a cabo hasta el
									<strong><?php echo displayDateInSpanish($pressPassesDeadline); ?></strong>
									inclusive, realizándose exclusivamente a través del siguiente formulario: <a href="<?php echo $pressPassesCredentialsFormURL; ?>" target="_blank">click aquí</a>
								</li>
								<li>
									Una vez evaluada la petición, se responderá con un correo, donde se informará si la acreditación fue aceptada o no por el Festival.
								</li>
								<li>
									Se otorgará un cupo limitado de acreditaciones, y sólo una por medio, debido a las capacidades de las salas en las cuales se desarrolla el Festival.
								</li>
								<li>
									No se recibirán solicitudes fuera de las fechas consignadas.
								</li>
							</ul>

							<h3>
								La Acreditación habilitará al portador a:
							</h3>
							<ul>
								<li>Asistir a una (1) de las tres (3) funciones de las 16 hs por día.</li>
								<li>Acceder a una (1) entrada extra por día, a cualquier función a elección -según disponibilidad de la sala.</li>
								<li>Solicitar entrevistas.</li>
							</ul>
							<br />

							La acreditación es personal e intransferible. Únicamente la persona que acredite identidad con su credencial podrá retirar sus vouchers.
							<br />
							<br />

							<!--
								TODO add press passes pick-up dates to editions.json.
								Defaultear all previous editions dates to the ones from 2018 edition.

								- 2018 edition
								  - from date: 30/nov
								  - to date: 3/dic.

								- 2019 edition
								  - from date: 21/Nov
								  - to date: 25/dic.

								Display corresponding festival venues as well.
							-->
							Quienes hayan sido acreditados podrán retirar su credencial
							<?php
								if (isset($pressPassesPickupDates['from'])) {
							?>
								desde el <strong><?php echo displayDateInSpanish($pressPassesPickupDates['from']); ?></strong>
							<?php
								}

								if (isset($pressPassesPickupDates['to'])) {
							?>
								hasta el <strong><?php echo displayDateInSpanish($pressPassesPickupDates['to']); ?> inclusive</strong>
							<?php
								}

								if (isset($pressPassesPickupLocations)) {
									echo 'en los stands del Festival en ';
									foreach($pressPassesPickupLocations as $index => $pickupLocation) {
										echo '<strong>' . $pickupLocation . '</strong>';

										if ($index == count($pressPassesPickupLocations) - 2) {
											echo ' y ';
										} else if ($index != count($pressPassesPickupLocations) - 1) {
											echo ', ';
										}
									}
								} else {
									echo 'en los stands del Festival';
								}

								if (isset($pressPassesPickupAdditionalInfo)) {
									echo ', ' . $pressPassesPickupAdditionalInfo;
								}

								echo '.';
							?>
						</p>
					</div>

	<?php
		get_sidebar();
		get_footer();
	?>
