<?php
require("../mailclass/class.phpmailerCustomer.php");
	   ///////Mail Function//////////////////
function send_mail_function($mail_text,$send_to,$send_cc,$mail_subject)
{
date_default_timezone_set('Etc/UTC');
$mail = new PHPMailer();//$mail = new PHPMailer();
$mail->IsSMTP();
$Mail->SMTPSecure  = "tls";
$mail->Host="mail.candoursoft.com";
$mail->Port=587;
//$mail->Timeout = 3600; 
//$mail->SMTPKeepAlive = true;  
//$mail->Mailer = "smtp";
$mail->SMTPAuth=true;
//$mail->CharSet = 'utf-8';  
$mail->SMTPDebug = 1;
$mail->Username='donotreply@candoursoft.com';
$mail->Password='Css@#123'; 
$mail->AddCC($send_cc, '');
	
		try {
			$add = trim($send_to);
			$mail->AddAddress($add);
			$mail->Body = $mail_text;
			$mail->Subject = $mail_subject;
			if ($mail->send()) {
				//echo "Send";
			}else{
				echo "Mailer Error: " . $mail->ErrorInfo;
			}
		
		}catch (phpmailerException $e) {
			echo "Skipping invalid address: {$add}\n";
		}
}
/////////Mail Function End///////
 ?>