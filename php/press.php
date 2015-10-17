<?php
/*
 * Template Name: tmpl_press
 *
 * @package WordPress
 * @subpackage bars2013
 */

	get_header();

?>
				<div id="page-press" class="page">
					<div class="page-header">
						Prensa
					</div>

					<div class="scratch"></div>

					<div class="press-form-container text-opensans" >
						<p>
							<h3>Política de acreditaciones</h3>
							<ul>
								<li>
									El proceso de acreditaciones se llevará a cabo entre el <strong>lunes 19 de octubre</strong> al <strong>lunes ​2​ de noviembre</strong> inclusive, realizándose exclusivamente a través de la página web oficial.
								</li>
								<li>
									Se otorgará un cupo limitado de acreditaciones, y sólo una por medio, debido a las capacidades de las salas en las cuales se desarrolla el Festival.
								</li>
								<li>
									No se recibirán solicitudes fuera de las fechas consignadas.
								</li>
							</ul>

							<h3>
								Solicitud de Acreditación para prensa gráfica, agencias de noticias, sitios de internet, prensa de radio y TV
							</h3>
							<ul>
								<li>
									Completar y enviar el formulario de acreditaciones y, una vez evaluada la petición, se responderá con un correo, donde se informará si la acreditación fue aceptada o no por el Festival.
								</li>
							</ul>

							<h3>
								La Acreditación habilitará al portador a:
							</h3>
							<ul>
								<li>Asistir a cualquiera de las funciones de las 14hs.</li>
								<li>Acceder a una entrada por día a función a elección -se reserva el mismo día- según disponibilidad de la sala.</li>
								<li>Solicitar entrevistas.</li>
							</ul>
							<br />
							Quienes hayan sido acreditados podrán retirar su credencial desde el <strong>jueves 12/10 a las 15:30hs</strong>, hasta el <strong>domingo 15/10 inclusive</strong>, en el stand del Festival en el <strong>Complejo Monumental, Lavalle 836</strong>.
						</p>

						<form id="press-form" class="press-form" method="post">

							<h2>Datos de la persona</h2>
							<hr />

							<table>
								<tr id="accredited_name" class="press-form-input">
									<td class="press-form-input-label required">
										<label for="accredited_name">Apellido y nombre</label>
									</td>
									<td class="press-form-input-container">
										<input type="text" name="accredited_name" value=""/>
									</td>
								</tr>

								<tr id="accredited_id" class="press-form-input">
									<td class="press-form-input-label required">
										<label for="accredited_id">Tipo y número de documento</label>
									</td>
									<td class="press-form-input-container">
										<input type="text" name="accredited_id" value="" />
									</td>
								</tr>

								<tr id="accredited_city" class="press-form-input">
									<td class="press-form-input-label required">
										<label for="accredited_city">Localidad/Ciudad</label>
									</td>
									<td class="press-form-input-container">
										<input type="text" name="accredited_city" value="" />
									</td>
								</tr>

								<tr id="accredited_country" class="press-form-input">
									<td class="press-form-input-label required">
										<label for="accredited_country">País</label>
									</td>
									<td class="press-form-input-container">
										<input type="text" name="accredited_country" value="" />
									</td>
								</tr>

								<tr id="accredited_mobile" class="press-form-input">
									<td class="press-form-input-label required">
										<label for="accredited_mobile">Celular</label>
									</td>
									<td class="press-form-input-container">
										<input type="text" name="accredited_mobile" value="" />
									</td>
								</tr>

								<tr id="accredited_email" class="press-form-input">
									<td class="press-form-input-label required">
										<label for="accredited_email">Email</label>
									</td>
									<td class="press-form-input-container">
										<input type="text" name="accredited_email" value="" />
									</td>
								</tr>

							</table>

							<h2>Datos del medio</h2>
							<hr />

							<table>

								<tr id="press_media_name" class="press-form-input">
									<td class="press-form-input-label required">
										<label for="press_media_name">Medio al que representa (sólo uno)</label>
									</td>
									<td class="press-form-input-container">
										<input type="text" name="press_media_name" value="" />
									</td>
								</tr>

								<tr id="press_media_editor" class="press-form-input">
									<td class="press-form-input-label required">
										<label for="press_media_editor">Nombre del editor</label>
									</td>
									<td class="press-form-input-container">
										<input type="text" name="press_media_editor" value="" />
									</td>
								</tr>

								<tr id="press_media_contact" class="press-form-input">
									<td class="press-form-input-label required">
										<label for="press_media_contact">Contacto</label>
									</td>
									<td class="press-form-input-container">
										<input type="text" name="press_media_contact" value="" />
									</td>
								</tr>

								<tr id="press_media_city" class="press-form-input">
									<td class="press-form-input-label">
										<label for="press_media_city">Ciudad</label>
									</td>
									<td class="press-form-input-container">
										<input type="text" name="press_media_city" value="" />
									</td>
								</tr>

								<tr id="press_media_country" class="press-form-input">
									<td class="press-form-input-label">
										<label for="press_media_country">País</label>
									</td>
									<td class="press-form-input-container">
										<input type="text" name="press_media_country" value="" />
									</td>
								</tr>

								<tr id="submit" class="press-form-input">
									<td></td>
									<td>
										<input id="submit" type="submit" name="submit" value="Enviar" method="post" />
									</td>
								</tr>
							</table>

						</form>

						<div class="warning email-success">
							<div class="reason">Tu mensaje ha sido enviado!</div>
							<div class="description">Te responderemos a la brevedad.</div>
						</div>

						<div class="warning email-error">
							<div class="reason">Se ha producido un error</div>
							<div class="description">No se ha podido enviar el mensaje. Volvé a intentar más tarde.</div>
						</div>

					</div>

	<?php
		get_sidebar();
		get_footer();
	?>
