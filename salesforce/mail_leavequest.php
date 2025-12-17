<?php
$today=date("Y-m-d");
/**
 * This example shows settings to use when sending via Google's Gmail servers.
 * The IMAP section shows how to save this message to the 'Sent Mail' folder using IMAP commands.
 */

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

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
//$mail->Debugoutput = 'html';

//Set the hostname of the mail server
$mail->Host = 'mail.candoursoft.com';
// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6

//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 25;

//Set the encryption system to use - ssl (deprecated) or tls
$mail->SMTPSecure = 'tls';

//Whether to use SMTP authentication
$mail->SMTPAuth = true;

//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = "sulakshna@candoursoft.com";

//Password to use for SMTP authentication
$mail->Password = "cs@#123";

//Set an alternative reply-to address
//$mail->addReplyTo('replyto@example.com', 'First Last');



//Set who the message is to be sent from
$mail->setFrom('sulakshna@candoursoft.com', 'Leave Application');

//Set who the message is to be sent to
$to="sulakshna.bhardwaj@gmail.com";
//$cc="$hremail[emailid]";
$cc = "sonu.kumar@candoursoft.com";
 ////$cc="sonu.kumar@candoursoft.com ,chirag@candoursoft.com";
//$cc="sonu.kumar@candoursoft.com ,chirag@candoursoft.com";

///$mail->addCC('sonu.kumar@candoursoft.com', 'Sonu Dhaliya');
//$mail->addAddress('chirag@candoursoft.com,shekhar@candoursoft.com,sulakshna@candoursoft.com','');
$unique_to = implode(',',array_unique(explode(',', $to)));
	$addresses = explode(",", $unique_to);
	if (count($addresses) > 0) {
		for ($i = 0; $i < count($addresses); $i++) {
			try {
			 $add = trim($addresses[$i]);
			 $mail->addAddress($add);
			}catch (phpmailerException $e) {
				echo "Skipping invalid address: {$add}\n";
			}
		}
	} else {
		try {
		$mail->addAddress($send_to);
		}catch (phpmailerException $e) {
				echo "Skipping invalid address: {$add}\n";
		}
	}
	if($cc!=""){
		$unique_cc = implode(',',array_unique(explode(',', $cc)));
		$addrs_cc = explode(",", $unique_cc);
		if (count($addrs_cc) > 0) {
			for ($i = 0; $i < count($addrs_cc); $i++) {
				try {
				 $add_cc = trim($addrs_cc[$i]);
				 $mail->addBCC($add_cc);
				}catch (phpmailerException $e) {
					echo "Skipping invalid address: {$add_cc}\n";
				}
			}
		} else {
			try {
			$mail->addBCC($send_cc);
			}catch (phpmailerException $e) {
					echo "Skipping invalid address: {$add_cc}\n";
			}
		}
	}


//Set the subject line
$mail->Subject = 'Leave Application';

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML('Dear , <br/><br/>




Regards<br/><br/>

');

//Replace the plain text body with one created manually
//$mail->AltBody = 'This is a plain-text message body';

//Attach an image file
//$mail->addAttachment('download_stock_report/wh_stock_report-'.$today.'.xls');

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error:" . $mail->ErrorInfo;
} else {
    echo "Message sent!";
	///// move to parent page
		header("Location:lead_list.php");
		exit;
    //Section 2: IMAP
    //Uncomment these to save your message in the 'Sent Mail' folder.
    #if (save_mail($mail)) {
    #    echo "Message saved!";
    #}
}

//Section 2: IMAP
//IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
//Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
//You can use imap_getmailboxes($imapStream, '/imap/ssl') to get a list of available folders or labels, this can
//be useful if you are trying to get this working on a non-Gmail IMAP server.
/*function save_mail($mail) {
    //You can change 'Sent Mail' to any other folder or tag
    $path = "{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail";

    //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
    $imapStream = imap_open($path, $mail->Username, $mail->Password);

    $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
    imap_close($imapStream);

    return $result;
}*/
