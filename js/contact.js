jQuery(document).ready(function($) {
	
	$('#success, #error').hide();
	
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
                type : 'POST',
                data : $(form).serialize(),
                success: function() {
                    $('#contact-form :input').attr('disabled', 'disabled');
                    $('#contact-form').fadeTo( "slow", 0.15, function() {
                        $(this).find(':input').attr('disabled', 'disabled');
                        $(this).find('label').css('cursor','default');
                        $('#success').fadeIn();
                    });
                },
                error: function() {
                    $('#contact-form').fadeTo( "slow", 0.15, function() {
                        $('#error').fadeIn();
                    });
                }
            });
			return false;
      }
    });
});
