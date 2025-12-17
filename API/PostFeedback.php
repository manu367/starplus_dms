<?php
// Set timezone to Indian Standard Time
date_default_timezone_set('Asia/Kolkata');
include_once 'db_functions.php';
include "mailer.php";  // mail function file     
$db = new DB_Functions();

$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn = $private_variable->getValue($db);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Step 1: Get raw JSON input
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Step 2: Validate payload
if (!$data) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid JSON payload"]);
    exit;
}

// Step 3: Extract fields
$userid      = trim($data['userid'] ?? '');
$emailId    = trim($data['email'] ?? '');
$mobile     = strtolower(trim($data['mobile'] ?? ''));
$rmk  = strtolower(trim($data['remark'] ?? ''));

// Step 4: Basic validation
if (empty($userid) || empty($emailId) || empty($mobile) || empty($rmk)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing ".$userid." fields"]);
    exit;
}
// Step 7: Update user details
$feedback_update = "insert into feedback_data SET  
    userid = '".$userid."',
    email = '".$emailId."',
	mobile='".$mobile."',
	remark='".$rmk."',
    entry_date = '".date("Y-m-d")."',
    entry_time = '".date("H:i:s")."'";

if (mysqli_query($conn, $feedback_update)) {
	
	// Email content (HTML Styled)
	$message = "
    <h2>New Feedback Submitted</h2>
    <p><b>Name:</b> $userid</p>
    <p><b>Email ID:</b> $emailId</p>
    <p><b>Phone:</b> $mobile</p>
    <p><b>Comment:</b><br> $rmk</p>";
	
	// Define receiver mail id & subject
	$to= "chirag@candoursoft.com";   // change admin mail here
	$cc="appsupport3@candoursoft.com";
	$subject  = "Star Plus Battery : New Feedback";
	
	// Call mail function
	$response = sendMailSMTP($to, $subject, $message,$cc);
	
	// Return back response
	//echo json_encode($response);
	
	
    echo json_encode([
        "status" => "success",
        "message" => "Feedback details update successfully",
        "technician_id" => $userid,
    ]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database insert failed", "error" => mysqli_error($conn)]);
}
?>
