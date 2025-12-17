<?php
/////////////////////////////
function send_mail_fun($content,$send_to,$send_cc,$subject,$inv1)
{

//$msg = base64_decode($inv1);
date_default_timezone_set('Etc/UTC');
require("../mailclass/class.phpmailerPWD.php");
$mail = new PHPMailer();//$mail = new PHPMailer();
$mail->IsSMTP();
$mail->Host = "mail.candoursoft.com";
$mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = "sonu.kumar@candoursoft.com";
//Password to use for SMTP authentication
$mail->Password = "Cs@#123";

//Set who the message is to be sent from
//$mail->setFrom('sonu.kumar@candoursoft.com', 'SMRN Details');
//Set who the message is to be sent to
$mail->addAddress($send_to, '');
//Set cc
$mail->addCC($send_cc, '');
//Set bcc
$mail->addBCC("sulakshna@candoursoft.com", '');
$mail->Subject = 'Auto Generated Mail';
//convert HTML into a basic plain-text alternative body
$mail->isHTML(true);
//Replace the plain text body with one created manually
$mail->Body = $content;
$mail->AddAttachment($inv1.".".pdf);
$mail->WordWrap = 50;
//Attach an image file
//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Email sent!";
}
}
send_mail_fun($pdfname,"sulakshna@candoursoft.com","sonu.kumar@candoursoft.com","test1",$inv);
 ?>