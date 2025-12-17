<?php
require '../PHPMailer/PHPMailerAutoload.php';

function sendMailSMTP($to, $subject, $message,$cc="", $fromEmail = "cspl.css0201@gmail.com", $fromName="Starplus Automailer") {

    $mail = new PHPMailer(true);
    try {
        // SMTP Setups
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        // ?? Gmail SMTP App Password (NOT normal password)
        $mail->Username   = 'cspl.css0201@gmail.com';   
        $mail->Password   = 'qfofogcoqpzacmcl';         

        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Sender Info
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to);
		$mail->addCC(trim($cc));
        // Email Body
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);

        $mail->send();

        return ["status" => "success", "message" => "Email sent successfully"];

    } catch (Exception $e) {
        return ["status" => "error", "message" => "Mailer Error: {$mail->ErrorInfo}"];
    }
}
?>
