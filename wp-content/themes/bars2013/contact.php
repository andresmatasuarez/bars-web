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
						
						<form class="contact-form" id="contact-form" method="post">
							<table>
								<tr id="name" class="contact-form-input">
									<td class="contact-form-input-label required">
										<label for="name" >Nombre</label>
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
								
								<tr id="media" class="contact-form-input">
									<td class="contact-form-input-label">
										<label for="media">Organización / Medio de prensa</label>
									</td>
									<td class="contact-form-input-container">
										<input type="text" name="media" value="" />
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
									<td>
										<input id="submit" type="submit" name="submit" action="process.php" value="Enviar" method="post" />
									</td>
								</tr>
							</table>
						</form>
						
						<div id="success">
							<p>Your message was sent succssfully! I will be in touch as soon as I can.</p>
						</div>
						
						<div id="error">
							<p>Error.</p>
						</div>
						
					</div>
				
	<?php
		get_sidebar();
		get_footer();
	?>