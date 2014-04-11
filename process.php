<?php
 
	// Receiver email
    $to = 'amatasuarez@gmail.com';
	
	// Email headers & subject
    $headers = 'From: $from';
    $subject = "BARS / Contact form";
	
	// Form input
    $from = $_POST['email'];
    $name = $_POST['name'];
    $media = $_POST['media'];
    $media = $_POST['subject'];
    $media = $_POST['message'];
 
    $fields = [ 'name', 'email', 'media', 'subject', 'message' ];
 
    $body = "Here is what was sent:\n\n";
	foreach($fields as $field){
		$body = $body . '\n' . $field . $_POST[$field];
	}
 
    $send = mail($to, $subject, $body, $headers);
 
?>

<html>
<body>

Welcome <?php echo $_POST["name"]; ?><br>
Your email address is: <?php echo $_POST["email"]; ?>
Welcome <?php echo $_REQUEST['name']; ?><br>
Your email address is: <?php echo $_REQUEST['email']; ?>



</body>
</html>