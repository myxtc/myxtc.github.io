<?php

session_cache_limiter('nocache');
header('Expires: ' . gmdate('r', 0));
header('Content-type: application/json');

// Include PHPMailer class
include("PHPMailer/PHPMailerAutoload.php");

// grab reCaptcha library
require_once "reCaptcha/recaptchalib.php";

///////////////////////////////////////////////////////////////////////

// Enter your email address below.
$to_address = "email_address@gmail.com"; 

// Enter your secret key (google captcha)
$secret = "6Ld41gITAAAAAFjfJWE_Kj4qxE0cOoGmeq8TVw2c";
 
//////////////////////////////////////////////////////////////////////


// Verify if data has been entered
if(!empty($_POST['email_']) || !empty($_POST['message_']) || !empty($_POST['subject_'])) {

	$subject = $_POST['subject_'];
	$name = $_POST['name_'];
	$email = $_POST['email_'];

	// Configure the fields list that you want to receive on the email.
	$fields = array(
		0 => array(
			'text' => 'Subject',
			'val' => $_POST['subject_']
		),
		1 => array(
			'text' => 'Name',
			'val' => $_POST['name_']
		),
		2 => array(
			'text' => 'Email address',
			'val' => $_POST['email_']
		),
		3 => array(
			'text' => 'Phone',
			'val' => $_POST['phone_']
		),
		4 => array(
			'text' => 'Select',
			'val' => $_POST['select_']
		),
		5 => array(
			'text' => 'Radio',
			'val' => $_POST['radio_']
		),
		6 => array(
			'text' => 'Checkbox',
			'val' => $_POST['checkbox_']
		),
		7 => array(
			'text' => 'Message',
			'val' => $_POST['message_']
		)
	);


	$message = "";
	foreach($fields as $field) {
		$message .= $field['text'].": " . htmlspecialchars($field['val'], ENT_QUOTES) . "<br>\n";
	}


	$mail = new PHPMailer;

	$mail->IsSMTP(); // Set mailer to use SMTP
	$mail->SMTPDebug = 0; // Debug Mode


	// If you don't receive the email, try to configure the parameters below:

	// $mail->Host = 'mail.yourserver.com';	// Specify main and backup server
	// $mail->SMTPAuth = true; // Enable SMTP authentication
	// $mail->Username = 'username'; // SMTP username
	// $mail->Password = 'secret'; // SMTP password
	// $mail->SMTPSecure = 'tls'; // Enable encryption, 'ssl' also accepted


	$mail->From = $email;
	$mail->FromName = $name;
	$mail->AddAddress($to_address);	
	$mail->AddReplyTo($email, $name);

	
	if (!empty($_POST['send_copy_'])) {
		$mail->AddAddress($email);
	}
	

	$mail->IsHTML(true); // Set email format to HTML
	$mail->CharSet = 'UTF-8';

	$mail->Subject = $subject;
	$mail->Body    = $message;


	// Google CAPTCHA
	$resp = null; // empty response
	$reCaptcha = new ReCaptcha($secret); // check secret key

	// if submitted check response
	if ($_POST["g-recaptcha-response"]) {
	    $resp = $reCaptcha->verifyResponse(
	        $_SERVER["REMOTE_ADDR"],
	        $_POST["g-recaptcha-response"]
	    );
	}

	// if captcha is ok, send email
	if ($resp != null && $resp->success) {

	    if($mail->Send()) {
	    	$result = array ('response'=>'success');
		} else {
			$result = array ('response'=>'error' , 'error_message'=> $mail->ErrorInfo);
		}
		
	} else {
		$result = array ('response'=>'error' , 'error_message'=>'Google ReCaptcha did not work');
	}

	echo json_encode($result);

} else {

	$result = array ('response'=>'error' , 'error_message'=>'Data has not been entered');
	echo json_encode($result);

}
?>
