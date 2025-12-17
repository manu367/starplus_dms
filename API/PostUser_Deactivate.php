<?php
include_once 'db_functions.php';     
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
$user_id      = trim($data['eng_id'] ?? '');

// Step 4: Basic validation
if (empty($user_id)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// Step 5: Check if user already exists
$check_sql = mysqli_query($conn, "
    SELECT uid,name FROM admin_users 
    WHERE username = '".mysqli_real_escape_string($conn, $user_id)."'");

if (mysqli_num_rows($check_sql) == 0) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "User is not registered"]);
    exit;
}

if (mysqli_num_rows($check_sql) > 0) {
	$row_user=mysqli_fetch_array($check_sql);
	
	$usr_update="update admin_users set status='deactive',update_by='".$user_id."- by APP',updatedate='".date('Y-m-d H:i:s')."' where uid='".$row_user['uid']."'";
	
	if (mysqli_query($conn, $usr_update)) {
    echo json_encode([
        "status" => "success",
        "message" => "Technician status updated",
        "technician_id" => $user_id,
        "technician_name" => $row_user['name']
    ]);
} else {
    http_response_code(502);
    echo json_encode(["status" => "error", "message" => "Database insert failed", "error" => mysqli_error($conn)]);
}
	
}

?>
