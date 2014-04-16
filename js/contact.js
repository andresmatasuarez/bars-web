jQuery(document).ready(function($) {
	
	$('.email-success, .email-error').hide();
	
	$('#contact-form').validate({
        rules: {
            name: 'required',
            email: {
                required: true,
                email: true
            },
            subject: 'required',
            message: 'required'
        },
		
        messages: {
            name: '• Nombre es un campo requerido (*).',
            email: {
                required: '• Email es un campo requerido (*).',
				email: '• Ingresá una dirección de email válida.'
            },
			subject: '• Asunto es un campo requerido (*).',
            message: '• Mensaje es un campo requerido (*).'
        },
		
        submitHandler: function(form) {
            $(form).ajaxSubmit({
                url: '/2.0/wp-content/themes/bars2013/email.php',
                type: 'POST',
                data: $(form).formSerialize(),
                success: function() {
                    $('#contact-form :input').attr('disabled', 'disabled');
                    $('#contact-form').fadeOut('slow', function() {
                        $('.email-success').fadeIn();
                    });
                },
                error: function() {
                    $('#contact-form').fadeTo('slow', function() {
                        $('.email-error').fadeIn();
                    });
                }
            });
			return false;
      }
    });
});
