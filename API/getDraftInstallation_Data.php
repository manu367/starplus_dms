<?php
include_once 'db_functions.php';     
$db = new DB_Functions();
$today=date('Y-m-d');
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn = $private_variable->getValue($db);
header('Content-Type: application/json');
$eng_id=$_REQUEST['eng_id']; 
$a = array();  
$b = array();
if (empty($eng_id)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Technician ID Missing"]);
    exit;
}

$check_sql = mysqli_query($conn, "SELECT * FROM installation_data WHERE  userid = '".$eng_id."' and post_status='D'");

if (mysqli_num_rows($check_sql) > 0) {
while($data=mysqli_fetch_array($check_sql)){	

$img_url="https://starplus.cancrm.in/".$data['img_url'];

$b['userid']= trim($data['userid']);
$b['document_no']= trim($data['document_no']);
$b['customer_Name']=trim($data['customer_Name']);
$b['mobileNumber']=trim($data['mobile_no']);
$b['emailId']=strtolower(trim($data['email']));
$b['fullAddress']=($data['address']);
$b['city']=($data['city']);
$b['state']= ($data['state']);
$b['zip']= trim($data['pincode']);
$b['installationDate']  = trim($data['installation_date']);
$b['invoiceNumber']  = trim($data['invoice_no']);
$b['picInstallation']  = $img_url.$data['img_installation'];
$b['picProduct']  = $img_url.$data['img_product'];
$b['picSheet']  = $img_url.$data['img_sheet'];
$b['productCode']  = trim($data['product_code']);
$b['serialNumber'] = trim($data['serial_no']);
$b['purchaseDate']  = trim($data['dop']);
$b['status']  = trim($data['status']);
$b['storeName']  = trim($data['store_name']);
$b['post_type']  = trim($data['post_status']);   //// for save data D => Draft & S => Save 
	array_push($a,$b); 	
}

echo json_encode($a);
	}
	else{
		http_response_code(400);
		echo json_encode(["status" => "error", "message" => "No Draft Installation"]);
		exit;	
	}
?>
