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
						
						<form class="contact-form" name="contact" method="post">
							<div class="contact-form-input contact-form-input-name">
								<label class="required" for="name" >Nombre</label>
								<input type="text" name="name" id="name" value="" required/>
								<span class="contact-form-input-hint">
									45 caracteres restantes.
								</span>
							</div>	
							<div class="contact-form-input contact-form-input-email">
								<label class="required" for="email">Email</label>
								<input type="text" name="email" id="email"  value="" required/>
								<span class="contact-form-input-hint">
									45 caracteres restantes.
								</span>
							</div>
							
							<div class="clear"></div>
							
							<div class="contact-form-input contact-form-input-media"> 
								<label for="media">Medio de prensa</label>
								<input type="text" name="media" id="media"  value="" />
								<span class="contact-form-input-hint">
									45 caracteres restantes.
								</span>
							</div>
							
							<div class="contact-form-input contact-form-input-media"> 
								<label for="media">Medio de prensa</label>
								<input type="text" name="media" id="media"  value="" />
								<span class="contact-form-input-hint">
									45 caracteres restantes.
								</span>
							</div>
							
							<div class="contact-form-input contact-form-input-subject">
								<label class="required" for="subject">Asunto</label>
								<input type="text" name="subject" id="subject"  value="" />
								<span class="contact-form-input-hint">
									100 caracteres restantes.
								</span>
							</div>
							<div class="contact-form-input contact-form-input-message">
								<label class="required" for="message">Message</label>
								<textarea name="message" id="message" required></textarea>
								<span class="contact-form-input-hint">
									255 caracteres restantes.
								</span>
							</div>
							<div class="contact-form-input contact-form-input-captcha">
								<label class="required" for="captcha">Name the small house pet that says "<i>meow</i>"</label>
								<input type="text" name="captcha" value="" required/>
								<span class="contact-form-input-hint"></span>
							</div>
							<div class="contact-form-input">
								<input id="submit" type="submit" name="submit" value="Send" />
							</div>
								
						</form>
						
						<div id="success">
							<p>Your message was sent succssfully! I will be in touch as soon as I can.</p>
						</div>

						<div id="error">
							<p>Something went wrong, try refreshing and submitting the form again.</p>
						</div>
						
					</div>
				
	<?php
		get_sidebar();
		get_footer();
	?>