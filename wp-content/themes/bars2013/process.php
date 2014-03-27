<?php
 
	// Receiver email
    $to = 'amatasuarez@gmail.com';
	
	// Email headers & subject
    $headers = 'From: $from';
    $subject = "BARS / Contact form";
	
	// Form input
    $from = $_REQUEST['email'];
    $name = $_REQUEST['name'];
    $media = $_REQUEST['media'];
    $media = $_REQUEST['subject'];
    $media = $_REQUEST['message'];
 
    $fields = [ 'name', 'email', 'media', 'subject', 'message' ];
 
    $body = "Here is what was sent:\n\n";
	foreach($fields as $field){
		$body = $body . '\n' . $field . $_REQUEST[$field];
	}
 
    $send = mail($to, $subject, $body, $headers);
 
?>