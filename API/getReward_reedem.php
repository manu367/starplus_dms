<?php
// Set timezone to IST
date_default_timezone_set('Asia/Kolkata');
include_once 'db_functions.php'; 
include "mailer.php";  // mail function file
$db = new DB_Functions();

// DB connection via reflection
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn = $private_variable->getValue($db);

header("Content-Type: application/json");

// Read JSON Request
$inputJSON = file_get_contents("php://input");
$input = json_decode($inputJSON, true);

// Read POST values
$userid     = isset($input['userid']) ? trim($input['userid']) : '';
$redeem_pt  = isset($input['points']) ? (int) $input['points'] : 0;

// Validation
if($userid == "" || $redeem_pt <= 0){
    echo json_encode(["status" => "error", "message" => "Invalid userid or redeem points"]);
    exit;
}

// Check user balance
$sql = mysqli_query($conn, "SELECT reward FROM reward_wallet WHERE userid='".$userid."'");
if(mysqli_num_rows($sql) == 0){
    echo json_encode(["status" => "error", "message" => "User not found"]);
    exit;
}

$data = mysqli_fetch_assoc($sql);
$current_balance = (int)$data['reward'];

// Not enough points
if($current_balance < $redeem_pt){
    echo json_encode([
        "status" => "error",
        "message" => "Insufficient reward balance",
        "available_balance" => $current_balance
    ]);
    exit;
}

// Calculate redeem value
$redeem_value = $redeem_pt * 25;

// Insert redeem request (Pending)
$sql_reedem = mysqli_query($conn,"INSERT INTO reward_reedem_ledger SET 
    userid='$userid',
    reward='$redeem_pt',
    value='$redeem_value',
    type='OUT',
    status='Pending',
    entry_date='".date('Y-m-d')."',
    time='".date('H:i:s')."'
");

// Final Response
if($sql_reedem){
    
    // Email content
    $message = "<h1>Dear Admin,</h1>
    <p><b>Member:</b> $userid has requested for a redeem of $redeem_pt point(s) worth of ₦ $redeem_value</p>";
    
    // Call Mail Function
    $to = "chirag@candoursoft.com"; 
    $cc = "appsupport3@candoursoft.com";
    $subject = "Star Plus Battery : Request for redemption";
    sendMailSMTP($to, $subject, $message, $cc);

    echo json_encode([
        "status" => "success",
        "message" => "Reward redeem request submitted",
        "redeemed_points" => $redeem_pt,
      //  "remaining_balance" => $current_balance - $redeem_pt
    ]);
}else{
    echo json_encode(["status" => "error", "message" => "DB insert failed"]);
}
?>
