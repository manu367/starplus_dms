<?php
include_once 'db_functions.php';    
$db = new DB_Functions();

$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn = $private_variable->getValue($db);

header("Content-Type: application/json; charset=UTF-8");

// Read JSON data
$input = json_decode(file_get_contents("php://input"), true);

$doc_no  = $input['document_no'] ?? '';
$userid  = $input['userid'] ?? '';

// Base64 images
$img_installation_b64 = $input['img_installation'] ?? '';
$img_product_b64      = $input['img_product'] ?? '';
$img_sheet_b64        = $input['img_sheet'] ?? '';

// Validate
if($doc_no == "" || $userid == ""){
    echo json_encode(["status"=>"error","message"=>"Missing document_no or userid"]);
    exit;
}

// Current Month Folder
$monthFolder = date('Y-m');     
$basePath = "../installation_uploads/";
$uploadPath = $basePath.$monthFolder."/";
$savePath = "installation_uploads/".$monthFolder."/";
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
$img_inst  = saveBase64Image($img_installation_b64, $uploadPath, "installation");
$img_prod  = saveBase64Image($img_product_b64, $uploadPath, "product");
$img_sheet = saveBase64Image($img_sheet_b64, $uploadPath, "sheet");

// Update DB
mysqli_query($conn, "
    UPDATE installation_data SET
		img_url='".$savePath."',
        img_installation='$img_inst',
        img_product='$img_prod',
        img_sheet='$img_sheet'
    WHERE document_no='$doc_no' AND userid='$userid'
");

echo json_encode([
    "status" => "success",
    "message" => "Images uploaded successfully",
    "folder" => $monthFolder,
    "saved_files" => [
        "installation" => $img_inst,
        "product" => $img_prod,
        "sheet" => $img_sheet
    ]
]);
?>
