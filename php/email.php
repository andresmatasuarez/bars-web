<?php

	DEFINE('RECEIVER_NAME',		'Buenos Aires Rojo Sangre');
	DEFINE('RECEIVER_EMAIL',	'materialbars@gmail.com');
	DEFINE('SENDER_NAME',			'BARS - Contacto');
	DEFINE('SENDER_EMAIL',		'noreply@bars.com.ar');
	DEFINE('EMAIL_CHARSET',		'UTF-8');

	DEFINE('DATETIME_FIELD_ID',	'datetime');
	DEFINE('NAME_FIELD_ID',			'name');
	DEFINE('EMAIL_FIELD_ID',		'email');
	DEFINE('URL_FIELD_ID',			'url');
	DEFINE('SUBJECT_FIELD_ID',	'subject');
	DEFINE('MESSAGE_FIELD_ID',	'message');

	DEFINE('DATETIME_FIELD_LABEL',	'Fecha y hora');
	DEFINE('NAME_FIELD_LABEL',			'Nombre');
	DEFINE('EMAIL_FIELD_LABEL',			'E-mail');
	DEFINE('URL_FIELD_LABEL',				'Sitio web');
	DEFINE('SUBJECT_FIELD_LABEL',		'Asunto');
	DEFINE('MESSAGE_FIELD_LABEL',		'Mensaje');

	DEFINE('NAME_FIELD_MAX_LENGTH',			50);
	DEFINE('EMAIL_FIELD_MAX_LENGTH',		100);
	DEFINE('URL_FIELD_MAX_LENGTH',			100);
	DEFINE('SUBJECT_FIELD_MAX_LENGTH',	50);
	DEFINE('MESSAGE_FIELD_MAX_LENGTH',	450);

	DEFINE('ERROR_NAME_REQUIRED',			'Nombre es un campo obligatorio.');
	DEFINE('ERROR_EMAIL_REQUIRED',		'E-mail es un campo obligatorio.');
	DEFINE('ERROR_SUBJECT_REQUIRED',	'Asunto es un campo obligatorio.');
	DEFINE('ERROR_MESSAGE_REQUIRED',	'Mensaje es un campo obligatorio.');

	DEFINE('ERROR_NAME_LENGTH',			'Longitud máxima del nombre: 50 caracteres.');
	DEFINE('ERROR_EMAIL_LENGTH',		'Longitud máxima del e-mail: 100 caracteres.');
	DEFINE('ERROR_URL_LENGTH',			'Longitud máxima de la URL: 100 caracteres.');
	DEFINE('ERROR_SUBJECT_LENGTH',	'Longitud máxima del asunto: 50 caracteres.');
	DEFINE('ERROR_MESSAGE_LENGTH',	'Longitud máxima del mensaje: 450 caracteres.');

	DEFINE('ERROR_MAIL', 'Surgió un problema al enviar el e-mail. Volvé a intentar más tarde.');
	DEFINE('ERROR_EMAIL_INVALID', 'Formato de e-mail inválido');
	DEFINE('ERROR_URL_INVALID', 'Formato de la URL inválido');

	require 'vendor/phpmailer/phpmailer/PHPMailerAutoload.php';

	date_default_timezone_set('America/Argentina/Buenos_Aires');

	// Form input
	$datetime =	date('d/m/Y h:i:s a');
	$name			=	setOrFail( NAME_FIELD_ID,       true,    ERROR_NAME_REQUIRED);
	$email 		=	setOrFail( EMAIL_FIELD_ID,      true,    EMAIL_FIELD_LABEL);
	$url			=	setOrFail( URL_FIELD_ID,        false);
	$subject	=	setOrFail( SUBJECT_FIELD_ID,    true,    SUBJECT_FIELD_LABEL);
	$message	=	setOrFail( MESSAGE_FIELD_ID,    true,    ERROR_MESSAGE_REQUIRED);

	// Form input validation
	$name			= validateStr(sanitizeStr($name),			true,		NAME_FIELD_MAX_LENGTH,		ERROR_NAME_REQUIRED,		ERROR_NAME_LENGTH);
	$email		= validateStr(sanitizeEmail($email),	true,		EMAIL_FIELD_MAX_LENGTH,		ERROR_EMAIL_REQUIRED,		ERROR_EMAIL_LENGTH);
	$url			= validateStr(sanitizeURL($url),			false,	URL_FIELD_MAX_LENGTH,			'',											ERROR_URL_LENGTH);
	$subject	= validateStr(sanitizeStr($subject),	true,		SUBJECT_FIELD_MAX_LENGTH,	ERROR_SUBJECT_REQUIRED,	ERROR_SUBJECT_LENGTH);
	$message	= validateStr(sanitizeStr($message),	true,		MESSAGE_FIELD_MAX_LENGTH,	ERROR_MESSAGE_REQUIRED,	ERROR_MESSAGE_LENGTH);

	$url = validateURL($url, ERROR_URL_INVALID);
	$email = validateEmail($email, ERROR_EMAIL_INVALID);
	$message = nl2br($message);

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

	$mailer->isHTML(true);									// Set email format to HTML
	$mailer->CharSet = EMAIL_CHARSET;

	$mailer->Subject = $subject;
	$mailer->Body    = emailHTMLBody($datetime, $name, $email, $url, $subject, $message);
	$mailer->AltBody = emailAltBody($datetime, $name, $email, $url, $subject, $message);

	if(!$mailer->send()) {
	   fail(ERROR_MAIL);
	}

	success();

	function emailHTMLBody($datetime, $name, $email, $url, $subject, $message){
		$template = file_get_contents('email.html');
		$template = str_replace('$' . DATETIME_FIELD_ID,	$datetime . ' ', $template);
		$template = str_replace('$' . NAME_FIELD_ID,			$name . ' ', $template);
		$template = str_replace('$' . EMAIL_FIELD_ID,			$email . ' ', $template);
		$template = str_replace('$' . URL_FIELD_ID,				$url . ' ', $template);
		$template = str_replace('$' . SUBJECT_FIELD_ID,		$subject . ' ', $template);
		$template = str_replace('$' . MESSAGE_FIELD_ID,		$message . ' ', $template);
		return $template;
	}

	function emailAltBody($datetime, $name, $email, $url, $subject, $message){
		return	DATETIME_FIELD_LABEL	. ': ' . $datetime . "\r\n" .
						NAME_FIELD_LABEL			. ': ' . $name . "\r\n" .
						EMAIL_FIELD_LABEL			. ': ' . $email . "\r\n" .
						URL_FIELD_LABEL				. ': ' . ($url ?: '') . "\r\n" .
						SUBJECT_FIELD_LABEL		. ': ' . $subject . "\r\n" .
						MESSAGE_FIELD_LABEL		. ': ' . $message . "\r\n";
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
