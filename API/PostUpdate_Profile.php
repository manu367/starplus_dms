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
$userid      = trim($data['userid'] ?? '');
$username    = trim($data['username'] ?? '');
$emailId     = strtolower(trim($data['emailId'] ?? ''));
$altContact  = strtolower(trim($data['altContact'] ?? ''));
$address     = ($data['address'] ?? '');
$city      = ($data['city'] ?? '');
$state      = ($data['state'] ?? '');
$pwd  = trim($data['password'] ?? '');
$pincode          = trim($data['zip'] ?? '');

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
$img_user_profile  = saveBase64Image($img_profile_b64, $uploadPath, "profile");


// Step 4: Basic validation
if (empty($userid) || empty($username) || empty($city) || empty($state) || empty($pwd)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// Step 5: Check if user already exists
$check_sql = mysqli_query($conn, "
    SELECT uid FROM admin_users 
    WHERE  username = '".mysqli_real_escape_string($conn, $userid)."'
");

if (mysqli_num_rows($check_sql) > 0) {
// Step 7: Update user details
$usr_update = "UPDATE admin_users SET  
    password = '".$pwd."',
    emailid = '".$emailId."',
    updatedate = '".date("Y-m-d H:i:s")."',
    update_by = '".$userid."',
    alternate_contact = '".$altContact."',
    address = '".mysqli_real_escape_string($conn, $address)."',
    city = '".$city."',
    state = '".$state."',
    pincode = '".$pincode."',
	profile_img_name='".$img_user_profile."',
	profile_img_path='".$savePath."'
WHERE username = '".$userid."'";

if (mysqli_query($conn, $usr_update)) {
    echo json_encode([
        "status" => "success",
        "message" => "Technician details update successfully",
        "technician_id" => $userid,
    ]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database insert failed", "error" => mysqli_error($conn)]);
}

}
else{
 	http_response_code(400);
    echo json_encode(["status" => "error", "message" => "User ID is not registered"]);
    exit;	
}
?>
