jQuery(document).ready(function($) {
	
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
            name: 'Ingresá tu nombre antes de continuar.',
            email: {
                required: 'Ingresá tu dirección de correo electrónico antes de continuar.',
				email: 'Ingresá una dirección de correo electrónico válida.'
            },
			subject: 'Especificá un asunto antes de continuar.',
            message: 'Escribí un mensaje antes de continuar.'
        },
		
        submitHandler: function(form) {
            $(form).ajaxSubmit({
                type : "POST",
                data : $(form).serialize(),
                url : "../process.php",
                success: function() {
                    $('#page-contact .contact-form :input').attr('disabled', 'disabled');
                    $('#page-contact .contact-form').fadeTo( "slow", 0.15, function() {
                        $(this).find(':input').attr('disabled', 'disabled');
                        $(this).find('label').css('cursor','default');
                        $('#success').fadeIn();
                    });
                },
                error: function() {
					alert(1);
                    $('#page-contact .contact-form').fadeTo( "slow", 0.15, function() {
                        $('#error').fadeIn();
                    });
                }
            });
			alert(1);
			return false;
      }
    });

});