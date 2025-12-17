<?php		
/////////////////////////////
function send_mail_fun($content,$subject,$send_to,$send_cc,$send_bcc,$send_from){
	
	//date_default_timezone_set('Etc/UTC');
	$time_zone=time() + 0;	
	date_default_timezone_set ("Asia/Calcutta");
	$err_msg="";
	require '../PHPMailer/PHPMailerAutoload.php';
	//Create a new PHPMailer instance
	$mail = new PHPMailer;

	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
	
	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	//$mail->SMTPDebug = 2;
	
	//Ask for HTML-friendly debug output
	$mail->Debugoutput = 'html';

	//Set the hostname of the mail server
	$mail->Host = 'mail.candoursoft.com';
	// use
	// $mail->Host = gethostbyname('smtp.gmail.com');
	// if your network does not support SMTP over IPv6

	//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
	$mail->Port = 587;

	//Set the encryption system to use - ssl (deprecated) or tls
	$mail->SMTPSecure = 'tls';
	$mail->Priority = 1;

	//Whether to use SMTP authentication
	$mail->SMTPAuth = true;
	$mail->SMTPOptions = array(
	'ssl' => array(
	'verify_peer' => false,
	'verify_peer_name' => false,
	'allow_self_signed' => true
	)
	);

	//Username to use for SMTP authentication - use full email address for gmail
	$mail->Username = "donotreply@candoursoft.com";

	//Password to use for SMTP authentication
	$mail->Password = "Cspl$#@2023css";

	//Set who the message is to be sent from
	$mail->setFrom('donotreply@candoursoft.com', $send_from);

	//Set an alternative reply-to address
	//$mail->addReplyTo('shekhargupta.1989@gmail.com', 'Shekhar Gupta');

	//Set who the message is to be sent to
	//$mail->addAddress($send_to, '');
	
	$unique_to = implode(',',array_unique(explode(',', $send_to)));
	$addresses = explode(",", $unique_to);
	if (count($addresses) > 0) {
		for ($i = 0; $i < count($addresses); $i++) {
			try {
			 $add = trim($addresses[$i]);
			 $mail->addAddress($add);
			}catch (phpmailerException $e) {
				$err_msg .= "Skipping invalid address: {$add}\n";
			}
		}
	} else {
		try {
		$mail->addAddress($send_to);
		}catch (phpmailerException $e) {
				$err_msg .= "Skipping invalid address: {$add}\n";
		}
	}
	//Set cc
	//$mail->addCC($send_cc, '');
	$unique_cc = implode(',',array_unique(explode(',', $send_cc)));
	$addrs_cc = explode(",", $unique_cc);
	if (count($addrs_cc) > 0) {
		for ($i = 0; $i < count($addrs_cc); $i++) {
			try {
			 $add_cc = trim($addrs_cc[$i]);
			 $mail->addCC($add_cc);
			}catch (phpmailerException $e) {
				$err_msg .= "Skipping invalid address: {$add_cc}\n";
			}
		}
	} else {
		try {
		$mail->addCC($send_cc);
		}catch (phpmailerException $e) {
				$err_msg .= "Skipping invalid address: {$add_cc}\n";
		}
	}
	
	//Set bcc
	if($send_bcc){
		$mail->addBCC($send_bcc);
	}	
	//$mail->addBCC("chirag@candoursoft.com", '');

	//Set the subject line
	$mail->Subject = $subject;

	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$mail->isHTML(true);
	//$mail->msgHTML(file_get_contents('email_print_new2.php'), dirname(__FILE__));

	//Replace the plain text body with one created manually
	$mail->Body = $content;

	//Attach an image file
	/*for($j=0; $j<count($piattach); $j++){
		$mail->addAttachment("../pi_attach/".$piattach[$j].".pdf");
	}*/
	//send the message, check for errors
	if (!$mail->send()) {
		$err_msg .= "Mailer Error: " . $mail->ErrorInfo;
		return "0~".$err_msg;
	} else {
		return "1";
	}
}
?>