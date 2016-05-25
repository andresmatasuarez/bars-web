import $ from 'jquery';

export default function contactForm() {

  $('.email-success, .email-error').hide();

  $('#contact-form').validate({
    rules: {
      name    : { required: true, maxlength: 50 },
      email   : { required: true, maxlength: 100, email: true },
      subject : { required: true, maxlength: 50 },
      message : { required: true, maxlength: 450 },
      url     : { maxlength: 100, url: true }
    },

    messages: {
      name: {
        required: '• Nombre es un campo requerido.',
        maxlength: '• Longitud máxima del nombre: 50 caracteres.'
      },
      email: {
        required: '• E-mail es un campo requerido.',
        email: '• Ingresá una dirección de e-mail válida.',
        maxlength: '• Longitud máxima del e-mail: 100 caracteres.'
      },
      url: {
        url: '• Ingresá una URL válida.',
        maxlength: '• Longitud máxima de la URL: 100 caracteres.'
      },
      subject: {
        required: '• Asunto es un campo requerido (*).',
        maxlength: '• Longitud máxima del asunto: 50 caracteres.'
      },
      message: {
        required: '• Mensaje es un campo requerido (*).',
        maxlength: '• Longitud máxima del mensaje: 450 caracteres.'
      }
    },

    submitHandler(form) {
      const formObject = $(form);
      formObject.ajaxSubmit({
        url      : '/2.0/wp-content/themes/bars2013/email.php',
        type     : 'POST',
        dataType : 'json',
        data     : formObject.formSerialize(),
        success(data) {
          if (data.error){
            $('#contact-form :input').attr('disabled', 'disabled');
            $('#contact-form').fadeOut(() => {
              $('.email-error').fadeIn();
              $('.email-error .description').text(data.message);
            });
          } else {
            $('#contact-form :input').attr('disabled', 'disabled');
            $('#contact-form').fadeOut(() => {
              $('.email-success').fadeIn();
            });
          }
        },
        error(data) {
          $('#contact-form :input').attr('disabled', 'disabled');
          $('#contact-form').fadeOut(() => {
              $('.email-error').fadeIn();
          });
        }
      });
      return false;
    }
  });
}
