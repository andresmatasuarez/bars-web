<?php
/*
 * Template Name: tmpl_contact
 *
 * @package WordPress
 * @subpackage bars2013
 */
	get_header();
?>

					<div class="page-header">
						Contacto
					</div>
					
					<div id="page-contact" class="page-content text-opensans" >
						<p>
							Por cualquier duda, consulta o si representás a un medio de prensa, dejanos tu mensaje!
						</p>
						
						<form class="contact-form" id="contact-form" method="post">
							<div id="name" class="contact-form-input">
								<label class="required" for="name" >Tu nombre</label>
								<div class="contact-form-input-container">
									<input type="text" name="name" value=""/>
								</div>
								<div class="contact-form-input-hint">
									45 caracteres restantes.
								</div>
							</div>	
							<div id="email" class="contact-form-input">
								<label class="required" for="email">Tu email</label>
								<div class="contact-form-input-container">
									<input type="text" name="email" value="" />
								</div>
								<div class="contact-form-input-hint">
									45 caracteres restantes.
								</div>
							</div>
							
							<div id="media" class="contact-form-input"> 
								<label for="media">Medio de prensa</label>
								<div class="contact-form-input-container">
									<input type="text" name="media" value="" />
								</div>
								<div class="contact-form-input-hint">
									45 caracteres restantes.
								</div>
							</div>
							
							<div id="subject" class="contact-form-input">
								<label class="required" for="subject">Asunto</label>
								<div class="contact-form-input-container">
									<input type="text" name="subject" value="" />
								</div>
								<div class="contact-form-input-hint">
									100 caracteres restantes.
								</div>
							</div>
							<div id="message" class="contact-form-input">
								<label class="required" for="message">Mensaje</label>
								<div class="contact-form-input-container">
									<textarea name="message" ></textarea>
								</div>
								<div class="contact-form-input-hint">
									255 caracteres restantes.
								</div>
							</div>
							<div id="submit" class="contact-form-input">
								<input id="submit" type="submit" name="submit" value="Enviar" />
							</div>
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