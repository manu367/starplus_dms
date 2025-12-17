<?php	
require_once("security/dbh.php");
require 'PHPMailer/PHPMailerAutoload.php';
$today=date("Y-m-d");		
/////////////////////////////
function sendEmailNotification($subject, $content, $send_to, $send_cc, $send_bcc, $send_fromname){
	$err_msg = "";
	date_default_timezone_set('Etc/UTC');
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
	$mail->Username = "sales@candoursoft.com";
	//Password to use for SMTP authentication
	$mail->Password = "Cs@Sales12345";
	//Set who the message is to be sent from
	$mail->setFrom('sales@candoursoft.com', $send_fromname);
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
				$err_msg = "Skipping invalid address: {$add}\n";
			}
		}
	} else {
		try {
		$mail->addAddress($send_to);
		}catch (phpmailerException $e) {
			$err_msg = "Skipping invalid address: {$add}\n";
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
				$err_msg = "Skipping invalid address: {$add_cc}\n";
			}
		}
	} else {
		try {
		$mail->addCC($send_cc);
		}catch (phpmailerException $e) {
			$err_msg = "Skipping invalid address: {$add_cc}\n";
		}
	}
	//Set bcc
	$unique_bcc = implode(',',array_unique(explode(',', $send_bcc)));
	$addrs_bcc = explode(",", $unique_bcc);
	if (count($addrs_bcc) > 0) {
		for ($i = 0; $i < count($addrs_bcc); $i++) {
			try {
			 $add_bcc = trim($addrs_bcc[$i]);
			 $mail->addBCC($add_bcc);
			}catch (phpmailerException $e) {
				$err_msg = "Skipping invalid address: {$add_bcc}\n";
			}
		}
	} else {
		try {
		$mail->addBCC($send_bcc);
		}catch (phpmailerException $e) {
			$err_msg = "Skipping invalid address: {$add_bcc}\n";
		}
	}
	//$mail->addBCC("shekhar@candoursoft.com", '');
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
		$send_flag = 0;
		$msgg = "Mailer Error: " . $mail->ErrorInfo;
	} else {
		$send_flag = 1;
		$msgg = 'Mail Sent!';
	}
	return $resp = array("status"=>$send_flag,"msg"=>$msgg);
}
///// fetch all scheduled plan of user
///// get task details ///////
$sql_user = "SELECT assigned_user FROM pjp_data WHERE plan_date = '".$today."' GROUP BY assigned_user";
$res_user = mysqli_query($link1,$sql_user);
while($row_user = mysqli_fetch_assoc($res_user)){
	$user_data = mysqli_fetch_array(mysqli_query($link1,"select emailid , name from admin_users where username = '".$row_user['assigned_user']."'"));
	$msgg = "";
	$msgg.="<html xmlns='http://www.w3.org/1999/xhtml'><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/><style>.Table_body {font-size: 10px;font-family: Verdana, Arial, Helvetica, sans-serif; color: #330033; background-color:#E9E9E9}
	.Table_body_white {font-size: 12px;font-family: Verdana, Arial, Helvetica, sans-serif; color: #330033; background-color:#E8E8E8}
	.Table_body1 {font-size: 12px;font-family: Verdana, Arial, Helvetica, sans-serif; color: #000000; background-color:#B0D2C2;}
	.style1 {font-size: 12px;font-family: Verdana, Arial, Helvetica, sans-serif; color: #FF0000;FONT-WEIGHT: bold;}
	.style2 {font-size: 12px;font-family: Verdana, Arial, Helvetica, sans-serif; color: #000000;}
	</style>";
	$msgg.="<title>Day Planner</title></head><body><p>Dear ".$user_data['name'].",<br/> Your day plan details are mentioned below. Kindly plan accordingly.</p>";
	$msgg.="<table width='50%' border='1' cellpadding='2' cellspacing='0' bordercolor='#336699'>";
	$msgg.="<tr align='center'><td width='5%' height='18' class='Table_body_white'><strong>SNo.</strong></td>";
	$msgg.="<td width='15%' class='Table_body_white'><strong>Date</strong></td>";
	$msgg.="<td width='15%' class='Table_body_white'><strong>Task Name</strong></td>";
	$msgg.="<td width='15%' class='Table_body_white'><strong>Visit Area</strong></td></tr>";
	
	$res = mysqli_query($link1,"SELECT * FROM pjp_data WHERE plan_date = '".$today."' AND assigned_user = '".$row_user['assigned_user']."'") or die("Error in Selecting PJP".mysqli_error($link1));
	$i=1;
	while($row=mysqli_fetch_assoc($res)){
		$msgg.=" <tr 'class='Table_body'><td class='Table_body1' >&nbsp;".$i."</td>";
		$msgg.="<td class='Table_body1' align='center'>&nbsp;".$row['plan_date']."</td>";
		$msgg.="<td class='Table_body1' align='left'>&nbsp;".$row['task']."</td>";
		$msgg.="<td class='Table_body1' align='left'>".$row['visit_area']."</td></tr>";
		$i++;
	}
	$msgg.="</table></td></tr><tr><td height='15px'></td></tr></table></body></html>";
	$msgg.="<br /><br /><br />Regards,<br />Candour Software<br/>Please do not reply to this automated email..!!";
	$msgg.="<br /><img src='https://pre.cansale.in/demodms/img/inner_logo.png'/>";
	
	$subject = "Day Planner For ".$row_user['assigned_user'];
	$content = $msgg;
	$send_to = $user_data['party_email'];
	$send_cc = "shekhar@candoursoft.com";
	$send_bcc = "ravi@candoursoft.com";
	$send_fromname = "Day Planner";
	sendEmailNotification($subject, $content, $send_to, $send_cc, $send_bcc, $send_fromname);
}
?>