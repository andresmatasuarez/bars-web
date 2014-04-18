		jQuery(document).ready(function($) {
	
	$('.email-success, .email-error').hide();
	
	$('#contact-form').validate({
				rules: {
						name: {
								required: true,
								maxlength: 50
						},
						email: {
								required: true,
								email: true,
								maxlength: 100
						},
						url: {
								url: true,
								maxlength: 100
						},
						subject: {
								required: true,
								maxlength: 50
						},
						message: {
								required: true,
								maxlength: 450
						}
				},
		
				messages: {
						name: {
							required:		'• Nombre es un campo requerido.',
							maxlength:	'• Longitud máxima del nombre: 50 caracteres.'
						},
						email: {
							required:		'• E-mail es un campo requerido.',
							email:			'• Ingresá una dirección de e-mail válida.',
							maxlength:	'• Longitud máxima del e-mail: 100 caracteres.'
						},
						url: {
							url:				'• Ingresá una URL válida.',
							maxlength:	'• Longitud máxima de la URL: 100 caracteres.'
						},
						subject: {
							required:		'• Asunto es un campo requerido (*).',
							maxlength:	'• Longitud máxima del asunto: 50 caracteres.'
						},
						message: {
							required:		'• Mensaje es un campo requerido (*).',
							maxlength:	'• Longitud máxima del mensaje: 450 caracteres.'
						}
				},
		
				submitHandler: function(form) {
						$(form).ajaxSubmit({
								url: '/2.0/wp-content/themes/bars2013/email.php',
								type: 'POST',
								data: $(form).formSerialize(),
								dataType: 'json',
								success: function(data) {
										if (!data.error){
												$('#contact-form :input').attr('disabled', 'disabled');
												$('#contact-form').fadeOut(function() {
														$('.email-success').fadeIn();
												});
										} else {
												$('#contact-form :input').attr('disabled', 'disabled');
												$('#contact-form').fadeOut(function() {
														$('.email-error').fadeIn();
														$('.email-error .description').text(data.message);
												});
										}
								},
								error: function(data) {
										$('#contact-form :input').attr('disabled', 'disabled');
										$('#contact-form').fadeOut(function() {
												$('.email-error').fadeIn();
										});
								}
						});
			return false;
			}
		});
});
