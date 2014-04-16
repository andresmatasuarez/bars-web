<?php

    require 'phpmailer/PHPMailerAutoload.php';
 
    // Receiver & Sender
    $to = array( name => 'Buenos Aires Rojo Sangre', email => 'amatasuarez@gmail.com' );
    $from = array( name => 'BARS - Contacto', email => 'noreply@bars.com.ar' );
    
    // Form input
    // if (!isset($_POST['name']) || !isset($_POST['email']) ||
    //     !isset($_POST['subject']) || !isset($_POST['message'])){
    //     exit
    // }

    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $media = filter_var($_POST['media'], FILTER_SANITIZE_STRING);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

    // if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
    //     exit
    // }

    // Configure PHPMailer
    $mailer = new PHPMailer;

    //$mailer->isSMTP();                                      // Set mailer to use SMTP
    //$mailer->Host = 'smtp.gmail.com';                    // Specify main and backup server
    //$mailer->Port = 465;
    //$mailer->SMTPAuth = true;                               // Enable SMTP authentication
    //$mailer->Username = '';                            // SMTP username
    //$mailer->Password = '';                           // SMTP password
    //$mailer->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted

    $mailer->From = $from['email'];
    $mailer->addReplyTo($from['email']);
    $mailer->FromName = $from['name'];
    $mailer->addAddress($to['email'], $to['name']);

    $mailer->WordWrap = 50;                                 // Set word wrap to 50 characters
    //$mailer->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mailer->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    $mailer->isHTML(true);                                  // Set email format to HTML

    $mailer->Subject = $subject;
    $mailer->Body    = emailAltBody($name, $email, $media, $subject, $message);
    $mailer->AltBody = emailAltBody($name, $email, $media, $subject, $message);

    if(!$mailer->send()) {
       exit;
    }

    function emailHTMLBody($name, $email, $media, $subject, $message){
        
    }

    function emailAltBody($name, $email, $media, $subject, $message){
        return  'Nombre: ' . $name . "\r\n" . 
                'Email: ' . $email . "\r\n" . 
                'Medio de prensa: ' . ($media ?: '') . "\r\n" . 
                'Asunto: ' . $subject . "\r\n" . 
                'Mensaje: ' . $message . "\r\n";
    }

?>