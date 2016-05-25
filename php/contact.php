<?php
/*
 * Template Name: tmpl_contact
 *
 * @package WordPress
 * @subpackage bars2013
 */

	get_header();

?>
				<div id="page-contact" class="page">
					<div class="page-header">
						Contacto
					</div>

					<div class="scratch"></div>

					<div class="contact-form-container text-opensans" >
						<p>
							Por cualquier duda, consulta o si representás a un medio de prensa, dejanos tu mensaje!
						</p>

						<form id="contact-form" class="contact-form" method="post">
							<table>
								<tr id="name" class="contact-form-input">
									<td class="contact-form-input-label required">
										<label for="name" >Nombre / Medio de prensa</label>
									</td>
									<td class="contact-form-input-container">
										<input type="text" name="name" value=""/>
									</td>
								</tr>
								<tr id="email" class="contact-form-input">
									<td class="contact-form-input-label required">
										<label for="email">Email</label>
									</td>
									<td class="contact-form-input-container">
										<input type="text" name="email" value="" />
									</td>
								</tr>

								<tr id="url" class="contact-form-input">
									<td class="contact-form-input-label">
										<label for="url">Sitio web</label>
									</td>
									<td class="contact-form-input-container">
										<input type="text" name="url" value="" />
									</td>
								</tr>

								<tr id="subject" class="contact-form-input">
									<td class="contact-form-input-label required">
										<label for="subject">Asunto</label>
									</td>
									<td class="contact-form-input-container">
										<input type="text" name="subject" value="" />
									</td>
								</tr>
								<tr id="message" class="contact-form-input">
									<td class="contact-form-input-label required">
										<label for="message">Mensaje</label>
									</td>
									<td class="contact-form-input-container">
										<textarea name="message" ></textarea>
									</td>
								</tr>
								<tr id="submit" class="contact-form-input">
									<td></td>
									<td>
										<input id="submit" type="submit" name="submit" value="Enviar" method="post" />
									</td>
								</tr>
							</table>
						</form>

						<div class="warning email-success">
							<div><span class="fa fa-send"></span></div>
							<div class="reason">Tu mensaje ha sido enviado!</div>
							<div class="description">Te responderemos a la brevedad.</div>
						</div>

						<div class="warning email-error">
							<div><span class="fa fa-frown-o"></span></div>
							<div class="reason">Se ha producido un error</div>
							<div class="description">No se ha podido enviar el mensaje. Volvé a intentar más tarde.</div>
						</div>

					</div>

	<?php
		get_sidebar();
		get_footer();
	?>
