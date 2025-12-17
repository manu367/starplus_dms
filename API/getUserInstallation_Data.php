<?php
include_once 'db_functions.php';     

$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn = $private_variable->getValue($db);

header('Content-Type: application/json');

$eng_id = isset($_REQUEST['eng_id']) ? trim($_REQUEST['eng_id']) : '';

if ($eng_id == "") {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Technician ID Missing"]);
    exit;
}

/* ------------------ GET INSTALLATION LIST ------------------ */
$sql = mysqli_query($conn, "SELECT * FROM installation_data WHERE userid = '".$eng_id."'");

if (mysqli_num_rows($sql) == 0) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "No Installation Records Found"]);
    exit;
}

$installation_list = [];
while ($data = mysqli_fetch_assoc($sql)) {
	
	$img_url="https://starplus.cancrm.in/".$data['img_url'];

    $tmp = [];
    $tmp['userid']            = trim($data['userid']);
    $tmp['document_no']       = trim($data['document_no']);
    $tmp['customer_Name']     = trim($data['customer_Name']);
    $tmp['mobileNumber']      = trim($data['mobile_no']);
    $tmp['emailId']           = strtolower(trim($data['email']));
    $tmp['fullAddress']       = ($data['address']);
    $tmp['city']              = ($data['city']);
    $tmp['state']             = ($data['state']);
    $tmp['zip']               = trim($data['pincode']);
    $tmp['installationDate']  = trim($data['installation_date']);
    $tmp['invoiceNumber']     = trim($data['invoice_no']);
    $tmp['picInstallation']   = $img_url.$data['img_installation'];
    $tmp['picProduct']        = $img_url.$data['img_product'];
    $tmp['picSheet']          = $img_url.$data['img_sheet'];
    $tmp['productCode']       = trim($data['product_code']);
    $tmp['serialNumber']      = trim($data['serial_no']);
    $tmp['purchaseDate']      = trim($data['dop']);
    $tmp['status']            = trim($data['status']);
    $tmp['storeName']         = trim($data['store_name']);
    $tmp['post_type']         = trim($data['post_status']);  // D => Draft, S => Save

    $installation_list[] = $tmp;
}

/* ------------------ COUNT STATUS ------------------ */
$pending_count  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM installation_data WHERE userid = '".$eng_id."' AND status='Pending'"));
$approved_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM installation_data WHERE userid = '".$eng_id."' AND status='Approved'"));
$draft_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM installation_data WHERE userid = '".$eng_id."' AND status='Draft'"));

/* ------------------ FINAL RESPONSE ------------------ */
$response = [
    "userid"           => $eng_id,
    "pending_count"    => $pending_count,
    "approved_count"   => $approved_count,
	"draft_count"      => $draft_count,
    "installation_data" => $installation_list
];

echo json_encode($response, JSON_UNESCAPED_SLASHES);
exit;

?>
