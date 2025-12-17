<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once 'db_functions.php';     
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
require '../PHPMailer/PHPMailerAutoload.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Step 1: Read incoming JSON payload
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data || empty($data['email']) || empty($data['mobile'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Email address or Mobile No. is missing in request"]);
    exit;
}

$email = trim($data['email']);
$mobile = trim($data['mobile']);

#### Check if requested details already avaialbe then it will not register again

$sql_user=mysqli_query($conn,"select uid from admin_users where (phone='".$username."' or emailid='".$email."')");
if(mysqli_num_rows($sql_user)==0){

// Step 2: Generate 4-digit OTP
$otp = rand(1000, 9999);

// Step 3: Send OTP using Gmail SMTP
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'cspl.css0201@gmail.com'; 
    $mail->Password   = 'qfof ogco qpza cmcl';      // 🔹 Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('cspl.css0201@gmail.com', 'Starplus Automailer');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Your Login OTP';
    $mail->Body    = "<p>Your login OTP is: <b>$otp</b></p>";
    $mail->AltBody = "Your login OTP is: $otp";

    $mail->send();

    // Step 4: Send success response
    echo json_encode([
        "status" => "success",
        "message" => "OTP sent successfully",
        "email" => $email,
        "otp" => $otp
    ]);
} catch (Exception $e) {
    // Step 5: Failure response
    echo json_encode([
        "status" => "error",
        "message" => "Failed to send OTP",
        "error" => $mail->ErrorInfo
    ]);
	
}

}
else
{
	http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Email id or Mobile No. is already registered"]);
    exit;
}
?>
