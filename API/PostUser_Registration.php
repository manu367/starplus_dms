<?php
date_default_timezone_set('Asia/Kolkata');
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
$fullName      = trim($data['fullName'] ?? '');
$username      = trim($data['username'] ?? '');
$referral      = trim($data['referral'] ?? '');
$emailId       = strtolower(trim($data['emailId'] ?? ''));
$verified      = ($data['verified'] ?? false) ? 1 : 0;
$referralCode  = trim($data['referralCode'] ?? '');
$type          = trim($data['type'] ?? '');
$points        = (int)($data['points'] ?? 0);
$device_token  = ($data['token'] ?? '');

// Base64 images
$img_profile_b64 = $data['img_profile'] ?? '';

// Current Month Folder
$monthFolder = date('Y-m');     
$basePath = "../profile_uploads/";
$uploadPath = $basePath.$monthFolder."/";
$savePath = "profile_uploads/".$monthFolder."/";
// Create folder if not exist
if (!file_exists($uploadPath)) {
    mkdir($uploadPath, 0777, true);
}

// FUNCTION: Save Base64 image to folder
function saveBase64Image($base64String, $uploadPath, $prefix){
    if ($base64String == "") return "";

    // Remove base64 metadata section if present
    if (strpos($base64String, "base64,") !== false) {
        $base64String = explode("base64,", $base64String)[1];
    }

    // Decode base64
    $data = base64_decode($base64String);

    if (!$data) return "";

    // Generate filename
    $fileName = uniqid()."_".$prefix.".jpg";

    // Save file
    file_put_contents($uploadPath.$fileName, $data);

    return $fileName;
}

// Convert Base64 → Image File
$img_user_profile  = saveBase64Image($img_installation_b64, $uploadPath, "profile");

// Step 4: Basic validation
if (empty($fullName) || empty($username) || empty($emailId) || empty($type)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// Step 5: Check if user already exists
$check_sql = mysqli_query($conn, "
    SELECT uid FROM admin_users 
    WHERE phone = '".mysqli_real_escape_string($conn, $username)."' 
       OR emailid = '".mysqli_real_escape_string($conn, $emailId)."'
");

if (mysqli_num_rows($check_sql) > 0) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "User already registered"]);
    exit;
}

// Step 6: Generate new technician code
$result_max = mysqli_query($conn, "SELECT MAX(uid) AS max_uid FROM admin_users");
$row_max = mysqli_fetch_assoc($result_max);
$next_uid = (int)$row_max['max_uid'] + 1;

$pad = str_pad($next_uid, 3, "0", STR_PAD_LEFT);
$admiCode = "STUSR" . $pad;
$pwd = $admiCode . "@123";

// Step 7: Insert user
$usr_add = "
    INSERT INTO admin_users 
    (username, password, name, utype, phone, emailid, status, create_by, createdate, designationid, additional_otp_login, device_token, referral, referralCode, verified,profile_img_name,profile_img_path) 
    VALUES 
    (
        '$admiCode',
        '$pwd',
        '".mysqli_real_escape_string($conn, $fullName)."',
        '9',
        '".mysqli_real_escape_string($conn, $username)."',
        '".mysqli_real_escape_string($conn, $emailId)."',
        'A',
        'App',
        '".date("Y-m-d H:i:s")."',
        '14',
        'N',
        '".mysqli_real_escape_string($conn, $device_token)."',
        '".mysqli_real_escape_string($conn, $referral)."',
        '".mysqli_real_escape_string($conn, $referralCode)."',
        '$verified',
		'".$img_user_profile."',
		'".$savePath."'
    )
";

if (mysqli_query($conn, $usr_add)) {
    echo json_encode([
        "status" => "success",
        "message" => "Technician details saved successfully",
        "technician_id" => $admiCode,
        "technician_name" => $fullName
    ]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database insert failed", "error" => mysqli_error($conn)]);
}
?>
