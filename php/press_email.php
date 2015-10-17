<?php

  DEFINE('RECEIVER_NAME',   'Buenos Aires Rojo Sangre');
  DEFINE('RECEIVER_EMAIL',  'concursobars@gmail.com');
  DEFINE('SENDER_NAME',     'BARS - Prensa');
  DEFINE('SENDER_EMAIL',    'noreply@bars.com.ar');
  DEFINE('EMAIL_CHARSET',   'UTF-8');

  DEFINE('DATETIME_FIELD_ID',            'datetime');
  DEFINE('ACCREDITED_NAME_FIELD_ID',     'accredited_name');
  DEFINE('ACCREDITED_ID_FIELD_ID',       'accredited_id');
  DEFINE('ACCREDITED_CITY_FIELD_ID',     'accredited_city');
  DEFINE('ACCREDITED_COUNTRY_FIELD_ID',  'accredited_country');
  DEFINE('ACCREDITED_MOBILE_FIELD_ID',   'accredited_mobile');
  DEFINE('ACCREDITED_EMAIL_FIELD_ID',    'accredited_email');
  DEFINE('PRESS_MEDIA_NAME_FIELD_ID',    'press_media_name');
  DEFINE('PRESS_MEDIA_CONTACT_FIELD_ID', 'press_media_contact');
  DEFINE('PRESS_MEDIA_CITY_FIELD_ID',    'press_media_city');
  DEFINE('PRESS_MEDIA_COUNTRY_FIELD_ID', 'press_media_country');
  DEFINE('PRESS_MEDIA_EDITOR_FIELD_ID',  'press_media_editor');

  DEFINE('DATETIME_FIELD_LABEL',            'Fecha y hora');
  DEFINE('ACCREDITED_NAME_FIELD_LABEL',     'Apellido y nombre del acreditado');
  DEFINE('ACCREDITED_ID_FIELD_LABEL',       'Tipo y número de documento del acreditado');
  DEFINE('ACCREDITED_CITY_FIELD_LABEL',     'Localidad/Ciudad del acreditado');
  DEFINE('ACCREDITED_COUNTRY_FIELD_LABEL',  'País del acreditado');
  DEFINE('ACCREDITED_MOBILE_FIELD_LABEL',   'Celular de contacto del acreditado');
  DEFINE('ACCREDITED_EMAIL_FIELD_LABEL',    'E-mail del acreditado');
  DEFINE('PRESS_MEDIA_NAME_FIELD_LABEL',    'Medio al que representa');
  DEFINE('PRESS_MEDIA_CONTACT_FIELD_LABEL', 'Teléfono del medio de prensa');
  DEFINE('PRESS_MEDIA_CITY_FIELD_LABEL',    'Ciudad del medio de prensa');
  DEFINE('PRESS_MEDIA_COUNTRY_FIELD_LABEL', 'País del medio de prensa');
  DEFINE('PRESS_MEDIA_EDITOR_FIELD_LABEL',  'Nombre del editor del medio de prensa');

  DEFINE('ACCREDITED_NAME_MAX_LENGTH',     50);
  DEFINE('ACCREDITED_ID_MAX_LENGTH',       50);
  DEFINE('ACCREDITED_CITY_MAX_LENGTH',     50);
  DEFINE('ACCREDITED_COUNTRY_MAX_LENGTH',  20);
  DEFINE('ACCREDITED_MOBILE_MAX_LENGTH',   20);
  DEFINE('ACCREDITED_EMAIL_MAX_LENGTH',    100);
  DEFINE('PRESS_MEDIA_NAME_MAX_LENGTH',    50);
  DEFINE('PRESS_MEDIA_CONTACT_MAX_LENGTH', 50);
  DEFINE('PRESS_MEDIA_CITY_MAX_LENGTH',    50);
  DEFINE('PRESS_MEDIA_COUNTRY_MAX_LENGTH', 50);
  DEFINE('PRESS_MEDIA_EDITOR_MAX_LENGTH',  50);

  DEFINE('ERROR_ACCREDITED_NAME_REQUIRED',     'Apellido y nombre del acreditado es un campo obligatorio.');
  DEFINE('ERROR_ACCREDITED_ID_REQUIRED',       'Tipo y número de documento del acreditado es un campo obligatorio.');
  DEFINE('ERROR_ACCREDITED_CITY_REQUIRED',     'Ciudad del acreditado es un campo obligatorio.');
  DEFINE('ERROR_ACCREDITED_COUNTRY_REQUIRED',  'País del acreditado es un campo obligatorio.');
  DEFINE('ERROR_ACCREDITED_MOBILE_REQUIRED',   'Celular de contacto del acreditado es un campo obligatorio.');
  DEFINE('ERROR_ACCREDITED_EMAIL_REQUIRED',    'E-mail es un campo obligatorio.');
  DEFINE('ERROR_PRESS_MEDIA_NAME_REQUIRED',    'Nombre del medio de prensa es un campo obligatorio.');
  DEFINE('ERROR_PRESS_MEDIA_EDITOR_REQUIRED',  'Nombre del editor del medio de prensa es un campo obligatorio.');
  DEFINE('ERROR_PRESS_MEDIA_CONTACT_REQUIRED', 'Contacto del medio de prensa es un campo obligatorio.');

  DEFINE('ERROR_ACCREDITED_NAME_LENGTH',     'Longitud máxima del apellido y nombre del acreditado: 50 caracteres.');
  DEFINE('ERROR_ACCREDITED_ID_LENGTH',       'Longitud máxima del tipo y número de documento del acreditado: 50 caracteres.');
  DEFINE('ERROR_ACCREDITED_CITY_LENGTH',     'Longitud máxima de la ciudad del acreditado: 50 caracteres.');
  DEFINE('ERROR_ACCREDITED_COUNTRY_LENGTH',  'Longitud máxima del país del acreditado: 20 caracteres.');
  DEFINE('ERROR_ACCREDITED_MOBILE_LENGTH',   'Longitud máxima del celular de contacto del acreditado: 20 caracteres.');
  DEFINE('ERROR_ACCREDITED_EMAIL_LENGTH',    'Longitud máxima del e-mail del acreditado: 100 caracteres.');
  DEFINE('ERROR_PRESS_MEDIA_NAME_LENGTH',    'Longitud máxima del nombre del medio de prensa: 50 caracteres.');
  DEFINE('ERROR_PRESS_MEDIA_CONTACT_LENGTH', 'Longitud máxima del contacto del medio de prensa: 50 caracteres.');
  DEFINE('ERROR_PRESS_MEDIA_CITY_LENGTH',    'Longitud máxima de la ciudad del medio de prensa: 50 caracteres.');
  DEFINE('ERROR_PRESS_MEDIA_COUNTRY_LENGTH', 'Longitud máxima del país del medio de prensa: 50 caracteres.');
  DEFINE('ERROR_PRESS_MEDIA_EDITOR_LENGTH',  'Longitud máxima del nombre del editor del medio de prensa: 50 caracteres.');

  DEFINE('ERROR_MAIL', 'Surgió un problema al enviar el e-mail. Volvé a intentar más tarde.');
  DEFINE('ERROR_EMAIL_INVALID', 'Formato de e-mail inválido');

  require 'phpmailer/PHPMailerAutoload.php';

  date_default_timezone_set('America/Argentina/Buenos_Aires');

  // Form input
  $datetime            = date('d/m/Y h:i:s a');
  $accredited_name     = setOrFail(ACCREDITED_NAME_FIELD_ID,     true, ERROR_ACCREDITED_NAME_REQUIRED);
  $accredited_id       = setOrFail(ACCREDITED_ID_FIELD_ID,       true, ERROR_ACCREDITED_ID_REQUIRED);
  $accredited_city     = setOrFail(ACCREDITED_CITY_FIELD_ID,     true, ERROR_ACCREDITED_CITY_REQUIRED);
  $accredited_country  = setOrFail(ACCREDITED_COUNTRY_FIELD_ID,  true, ERROR_ACCREDITED_COUNTRY_REQUIRED);
  $accredited_mobile   = setOrFail(ACCREDITED_MOBILE_FIELD_ID,   true, ERROR_ACCREDITED_MOBILE_REQUIRED);
  $accredited_email    = setOrFail(ACCREDITED_EMAIL_FIELD_ID,    true, ERROR_ACCREDITED_EMAIL_REQUIRED);
  $press_media_name    = setOrFail(PRESS_MEDIA_NAME_FIELD_ID,    true, ERROR_PRESS_MEDIA_NAME_REQUIRED);
  $press_media_editor  = setOrFail(PRESS_MEDIA_EDITOR_FIELD_ID,  true, ERROR_PRESS_MEDIA_EDITOR_REQUIRED);
  $press_media_contact = setOrFail(PRESS_MEDIA_CONTACT_FIELD_ID, true, ERROR_PRESS_MEDIA_CONTACT_REQUIRED);
  $press_media_city    = setOrFail(PRESS_MEDIA_CITY_FIELD_ID,    false);
  $press_media_country = setOrFail(PRESS_MEDIA_COUNTRY_FIELD_ID, false);

  // Form input validation
  $accredited_name    = validateStr(sanitizeStr($accredited_name),    true, ACCREDITED_NAME_MAX_LENGTH,    ERROR_ACCREDITED_NAME_REQUIRED, ERROR_ACCREDITED_NAME_LENGTH);
  $accredited_id      = validateStr(sanitizeStr($accredited_id),      true, ACCREDITED_ID_MAX_LENGTH,      ERROR_ACCREDITED_ID_REQUIRED, ERROR_ACCREDITED_ID_LENGTH);
  $accredited_city    = validateStr(sanitizeStr($accredited_city),    true, ACCREDITED_CITY_MAX_LENGTH,    ERROR_ACCREDITED_CITY_REQUIRED, ERROR_ACCREDITED_CITY_LENGTH);
  $accredited_country = validateStr(sanitizeStr($accredited_country), true, ACCREDITED_COUNTRY_MAX_LENGTH, ERROR_ACCREDITED_COUNTRY_REQUIRED, ERROR_ACCREDITED_COUNTRY_LENGTH);
  $accredited_mobile  = validateStr(sanitizeStr($accredited_mobile),  true, ACCREDITED_MOBILE_MAX_LENGTH,  ERROR_ACCREDITED_MOBILE_REQUIRED, ERROR_ACCREDITED_MOBILE_LENGTH);
  $accredited_email   = validateStr(sanitizeEmail($accredited_email), true, ACCREDITED_EMAIL_MAX_LENGTH,   ERROR_ACCREDITED_EMAIL_REQUIRED, ERROR_ACCREDITED_EMAIL_LENGTH);
  $press_media_name   = validateStr(sanitizeEmail($press_media_name), true, PRESS_MEDIA_NAME_MAX_LENGTH,   ERROR_PRESS_MEDIA_NAME_REQUIRED, ERROR_PRESS_MEDIA_NAME_LENGTH);
  $press_media_editor  = validateStr(sanitizeEmail($press_media_editor),  true, PRESS_MEDIA_EDITOR_MAX_LENGTH,  ERROR_PRESS_MEDIA_EDITOR_REQUIRED, ERROR_PRESS_MEDIA_EDITOR_LENGTH);
  $press_media_contact   = validateStr(sanitizeEmail($press_media_contact),   true, PRESS_MEDIA_CONTACT_MAX_LENGTH,   ERROR_PRESS_MEDIA_CONTACT_REQUIRED, ERROR_PRESS_MEDIA_CONTACT_LENGTH);
  $press_media_city    = validateStr(sanitizeEmail($press_media_city),    false, PRESS_MEDIA_CITY_MAX_LENGTH,    '', ERROR_PRESS_MEDIA_CITY_LENGTH);
  $press_media_country = validateStr(sanitizeEmail($press_media_country), false, PRESS_MEDIA_COUNTRY_MAX_LENGTH, '', ERROR_PRESS_MEDIA_COUNTRY_LENGTH);

  $accredited_email = validateEmail($accredited_email, ERROR_EMAIL_INVALID);

  // Receiver & Sender
  $to = array( 'name' => RECEIVER_NAME, 'email' => RECEIVER_EMAIL );
  $from = array( 'name' => SENDER_NAME, 'email' => SENDER_EMAIL );

  // Configure PHPMailer
  $mailer = new PHPMailer;

  // SMTP Options
  //$mailer->isSMTP();                    // Set mailer to use SMTP
  //$mailer->Host = 'smtp.gmail.com';     // Specify main and backup server
  //$mailer->Port = 465;
  //$mailer->SMTPAuth = true;             // Enable SMTP authentication
  //$mailer->Username = '';               // SMTP username
  //$mailer->Password = '';               // SMTP password
  //$mailer->SMTPSecure = 'ssl';          // Enable encryption, 'ssl' also accepted

  // Headers
  $mailer->From = $from['email'];
  $mailer->addReplyTo($from['email']);
  $mailer->FromName = $from['name'];
  $mailer->addAddress($to['email'], $to['name']);

  $mailer->isHTML(true);                  // Set email format to HTML
  $mailer->CharSet = EMAIL_CHARSET;

  $mailer->Subject = $subject;
  $mailer->Body = emailHTMLBody(
    $datetime,
    $accredited_name,
    $accredited_id,
    $accredited_city,
    $accredited_country,
    $accredited_mobile,
    $accredited_email,
    $press_media_name,
    $press_media_contact,
    $press_media_city,
    $press_media_country,
    $press_media_editor
  );

  $mailer->AltBody = emailAltBody(
    $datetime,
    $accredited_name,
    $accredited_id,
    $accredited_city,
    $accredited_country,
    $accredited_mobile,
    $accredited_email,
    $press_media_name,
    $press_media_contact,
    $press_media_city,
    $press_media_country,
    $press_media_editor
  );

  if(!$mailer->send()) {
    fail(ERROR_MAIL);
  }

  success();

  function emailHTMLBody($datetime, $accredited_name, $accredited_id, $accredited_city, $accredited_country, $accredited_mobile, $accredited_email, $press_media_name, $press_media_contact, $press_media_city, $press_media_country, $press_media_editor){
    $template = file_get_contents('press_email.html');

    $template = str_replace('$' . DATETIME_FIELD_ID,            $datetime . ' ', $template);
    $template = str_replace('$' . ACCREDITED_NAME_FIELD_ID,     $accredited_name . ' ', $template);
    $template = str_replace('$' . ACCREDITED_ID_FIELD_ID,       $accredited_id . ' ', $template);
    $template = str_replace('$' . ACCREDITED_CITY_FIELD_ID,     $accredited_city . ' ', $template);
    $template = str_replace('$' . ACCREDITED_COUNTRY_FIELD_ID,  $accredited_country . ' ', $template);
    $template = str_replace('$' . ACCREDITED_MOBILE_FIELD_ID,   $accredited_mobile . ' ', $template);
    $template = str_replace('$' . ACCREDITED_EMAIL_FIELD_ID,    $accredited_email . ' ', $template);
    $template = str_replace('$' . PRESS_MEDIA_NAME_FIELD_ID,    $press_media_name . ' ', $template);
    $template = str_replace('$' . PRESS_MEDIA_CONTACT_FIELD_ID, $press_media_contact . ' ', $template);
    $template = str_replace('$' . PRESS_MEDIA_CITY_FIELD_ID,    $press_media_city . ' ', $template);
    $template = str_replace('$' . PRESS_MEDIA_COUNTRY_FIELD_ID, $press_media_country . ' ', $template);
    $template = str_replace('$' . PRESS_MEDIA_EDITOR_FIELD_ID,  $press_media_editor . ' ', $template);

    return $template;
  }

  function emailAltBody($datetime, $accredited_name, $accredited_id, $accredited_city, $accredited_country, $accredited_mobile, $accredited_email, $press_media_name, $press_media_contact, $press_media_city, $press_media_country, $press_media_editor){

    return  DATETIME_FIELD_LABEL            . ': ' . $datetime . "\r\n" .
            ACCREDITED_NAME_FIELD_LABEL     . ': ' . $accredited_name . "\r\n" .
            ACCREDITED_ID_FIELD_LABEL       . ': ' . $accredited_id . "\r\n" .
            ACCREDITED_CITY_FIELD_LABEL     . ': ' . $accredited_city . "\r\n" .
            ACCREDITED_COUNTRY_FIELD_LABEL  . ': ' . $accredited_country . "\r\n" .
            ACCREDITED_MOBILE_FIELD_LABEL   . ': ' . $accredited_mobile . "\r\n" .
            ACCREDITED_EMAIL_FIELD_LABEL    . ': ' . $accredited_email . "\r\n" .
            PRESS_MEDIA_NAME_FIELD_LABEL    . ': ' . $press_media_name . "\r\n" .
            PRESS_MEDIA_CONTACT_FIELD_LABEL . ': ' . $press_media_contact . "\r\n" .
            PRESS_MEDIA_CITY_FIELD_LABEL    . ': ' . $press_media_city . "\r\n" .
            PRESS_MEDIA_COUNTRY_FIELD_LABEL . ': ' . $press_media_country . "\r\n" .
            PRESS_MEDIA_EDITOR_FIELD_LABEL  . ': ' . $press_media_editor . "\r\n";
  }

  function setOrFail($fieldId, $required, $errorMessage){
    if ($required && !isset($_POST[$fieldId])){
      fail($errorMessage);
    }
    return $_POST[$fieldId];
  }

  function sanitizeStr($value){
    return filter_var($value, FILTER_SANITIZE_STRING);
  }

  function sanitizeEmail($value){
    return filter_var($value, FILTER_SANITIZE_EMAIL);
  }

  function sanitizeURL($value){
    $url = filter_var($value, FILTER_SANITIZE_URL);

    // @src http://stackoverflow.com/questions/2762061/how-to-add-http-if-its-not-exists-in-the-url
    return parse_url($url, PHP_URL_SCHEME) === null ? 'http://' . $url : $url;
  }

  function validateStr($value, $required, $maxLength, $errorRequired, $errorMaxLength){
    return validateMaxLength(validateRequired($value, $required, $errorRequired), $maxLength, $errorMaxLength);
  }

  function validateEmail($value, $errorMessage){
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)){
      fail($errorMessage);
    }
    return $value;
  }

  function validateURL($value, $errorMessage){
    if (!filter_var($value, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)){
      fail($errorMessage);
    }
    return $value;
  }

  function validateMaxLength($value, $maxLength, $errorMessage){
    if (strlen($value) > $maxLength){
      fail($errorMessage);
    }
    return $value;
  }

  function validateRequired($value, $required, $errorMessage){
    if ($required && $value == ''){
      fail($errorMessage);
    }
    return $value;
  }

  function success(){
    header('Content-type: application/json');
    echo json_encode(array('error' => false));
    exit;
  }

  function fail($errorMessage = ''){
    header('Content-type: application/json');
    echo json_encode(array('error' => true, 'message' => $errorMessage));
    exit;
  }

?>
