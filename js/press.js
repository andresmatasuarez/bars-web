jQuery(document).ready(function($) {

	$('.email-success, .email-error').hide();

	$('#press-form').validate({
		rules: {
			accredited_name: {
				required: true,
				maxlength: 50
			},
			accredited_id: {
				required: true,
				maxlength: 50
			},
			accredited_city: {
				required: true,
				maxlength: 50
			},
			accredited_country: {
				required: true,
				maxlength: 20
			},
			accredited_mobile: {
				required: true,
				maxlength: 20
			},
			accredited_email: {
				required: true,
				email: true,
				maxlength: 100
			},
			press_media_name: {
				required: true,
				maxlength: 50
			},
			press_media_editor: {
				required: true,
				maxlength: 50
			},
			press_media_contact: {
				required: true,
				maxlength: 50
			},
			press_media_city: {
				maxlength: 50
			},
			press_media_country: {
				maxlength: 50
			}
		},

		messages: {
			accredited_name: {
				required:		'• Nombre es un campo requerido.',
				maxlength:	'• Longitud máxima del nombre: 50 caracteres.'
			},
			accredited_id: {
				required:		'• Tipo y número de documento es un campo requerido.',
				maxlength:	'• Longitud máxima del tipo y número de documento: 50 caracteres.'
			},
			accredited_city: {
				required:		'• Ciudad es un campo requerido.',
				maxlength:	'• Longitud máxima de ciudad: 50 caracteres.'
			},
			accredited_country: {
				required:		'• País es un campo requerido.',
				maxlength:	'• Longitud máxima de país: 20 caracteres.'
			},
			accredited_mobile: {
				required:		'• Celular es un campo requerido.',
				maxlength:	'• Longitud máxima del celular: 20 caracteres.'
			},
			accredited_email: {
				required:		'• E-mail es un campo requerido.',
				email:			'• Ingresá una dirección de e-mail válida.',
				maxlength:	'• Longitud máxima del e-mail: 100 caracteres.'
			},
			press_media_name: {
				required:		'• Nombre del medio es un campo requerido.',
				maxlength:	'• Longitud máxima del nombre del medio: 50 caracteres.'
			},
			press_media_editor: {
				required:		'• Nombre del editor del medio es un campo requerido.',
				maxlength:	'• Longitud máxima del nombre del editor del medio: 50 caracteres.'
			},
			press_media_contact: {
				required:		'• Contacto del medio es un campo requerido.',
				maxlength:	'• Longitud máxima del teléfono del medio: 50 caracteres.'
			},
			press_media_city: {
				maxlength:	'• Longitud máxima de la ciudad del medio: 50 caracteres.'
			},
			press_media_country: {
				maxlength:	'• Longitud máxima del país del medio: 50 caracteres.'
			}
		},

		submitHandler: function(form) {
			$(form).ajaxSubmit({
				url: '/2.0/wp-content/themes/bars2013/press_email.php',
				type: 'POST',
				data: $(form).formSerialize(),
				dataType: 'json',
				success: function(data) {
					if (!data.error){
						$('#press-form :input').attr('disabled', 'disabled');
						$('#press-form').fadeOut(function() {
							$('.email-success').fadeIn();
						});
					} else {
						$('#press-form :input').attr('disabled', 'disabled');
						$('#press-form').fadeOut(function() {
							$('.email-error').fadeIn();
							$('.email-error .description').text(data.message);
						});
					}
				},
				error: function(data) {
					$('#press-form :input').attr('disabled', 'disabled');
					$('#press-form').fadeOut(function() {
						$('.email-error').fadeIn();
					});
				}
			});
			return false;
		}
	});
});
