<?php
include_once 'db_functions.php';     
$db = new DB_Functions();
$today=date('Y-m-d');
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
$cust_name    = trim($data['customer_Name'] ?? '');
$cust_mobile    = trim($data['mobileNumber'] ?? '');
$emailId     = strtolower(trim($data['emailId'] ?? ''));
$address     = ($data['fullAddress'] ?? '');
$city      = ($data['city'] ?? '');
$state     = ($data['state'] ?? '');
$pincode   = trim($data['zip'] ?? '');
$inst_date  = trim($data['installationDate'] ?? '');
$inv_no  = trim($data['invoiceNumber'] ?? '');
$img_inst  = trim($data['picInstallation'] ?? '');
$img_product  = trim($data['picProduct'] ?? '');
$img_sheet  = trim($data['picSheet'] ?? '');
$item_code  = trim($data['productCode'] ?? '');
$sr  = trim($data['serialNumber'] ?? '');
$dop  = trim($data['purchaseDate'] ?? '');
$status  = trim($data['status'] ?? '');
$storename  = trim($data['storeName'] ?? '');
$post_type  = trim($data['post_type'] ?? '');   //// for save data D => Draft & S => Save 

// Step 4: Basic validation
if (empty($userid) || empty($cust_mobile) || empty($cust_name) || empty($city) || empty($state) || empty($item_code) || empty($sr)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

if($post_type=="S"){

// Step 5: Check if user already exists
$check_sql = mysqli_query($conn, "SELECT id FROM installation_data WHERE  serial_no = '".$sr."' and post_status='S'");

if (mysqli_num_rows($check_sql) == 0) {
	
	$check_inst_st = mysqli_query($conn, "SELECT id,document_no,userid FROM installation_data WHERE  serial_no = '".$sr."' and post_status='D'");
	if (mysqli_num_rows($check_inst_st) == 0) {
		
		$sql_inst=mysqli_query($conn,"select max(temp_no) as tmp_no from installation_data");
		$row_inst=mysqli_fetch_array($sql_inst);
		$temp_no=$row_inst['tmp_no'];

		// Increment it
		$nxt_temp = str_pad($temp_no + 1, 5, '0', STR_PAD_LEFT);
		$prefix = "STRINST";
		$year = date("Y"); // current year, e.g., 2025
		$doc_no=$prefix . $year . $nxt_temp;	
			
	// Step 7: Update user details
	$installation_sql = "INSERT INTO installation_data SET  
			userid = '".$userid."',
			document_no = '".$doc_no."',
			temp_no = '".$nxt_temp."',
			customer_Name = '".$cust_name."',
			mobile_no = '".$cust_mobile."',
			email = '".$emailId."',
			address = '".mysqli_real_escape_string($conn, $address)."',
			city = '".$city."',
			state = '".$state."',
			pincode = '".$pincode."',
			installation_date = '".$today."',
			invoice_no = '".$inv_no."',
			product_code = '".$item_code."',
			serial_no = '".$sr."',
			dop = '".$dop."',
			status = '".$status."',
			post_status='S',
			store_name = '".$storename."',
			img_installation = '".$img_inst."',
			img_product = '".$img_product."',
			img_sheet = '".$img_sheet."',
			entry_date = '".$today."',
			entry_time = '".date('H:i:s')."'";
	
	if (mysqli_query($conn, $installation_sql)) {
		echo json_encode([
			"status" => "success",
			"message" => "Installation Registered successfully",
			"technician_id" => $userid,
			"document_no" => $doc_no
		]);
	} else {
		http_response_code(500);
		echo json_encode(["status" => "error", "message" => "Database insert failed", "error" => mysqli_error($conn)]);
	}
	
	}
	
	else{
	
		$row_inst_st=mysqli_fetch_array($check_inst_st);
		$result_update_inst="update installation_data SET
        customer_Name = '".$cust_name."',
        mobile_no = '".$cust_mobile."',
        email = '".$emailId."',
        address = '".mysqli_real_escape_string($conn, $address)."',
        city = '".$city."',
        state = '".$state."',
        pincode = '".$pincode."',
        installation_date = '".$today."',
        invoice_no = '".$inv_no."',
        product_code = '".$item_code."',
        serial_no = '".$sr."',
        dop = '".$dop."',
        status = '".$status."',
		post_status='S',
        store_name = '".$storename."',
        img_installation = '".$img_inst."',
        img_product = '".$img_product."',
        img_sheet = '".$img_sheet."'
		where id='".$row_inst_st['id']."'";
		
		if (mysqli_query($conn, $result_update_inst)) {
    	echo json_encode([
        "status" => "success",
        "message" => "Installation Registered successfully",
        "technician_id" => $userid,
		"document_no" => $row_inst_st['document_no']
		]);
		exit;
		} 	
	}
			
	}
	else{
		http_response_code(400);
		echo json_encode(["status" => "error", "message" => "Installation already Registered with this Serial No."]);
		exit;	
	}


} ////// END IF for Data Save
 #### FOr Saving into Draft
else if($post_type=="D"){

// Step 5: Check if user already exists
$check_sql = mysqli_query($conn, "SELECT id FROM installation_data WHERE  serial_no = '".$sr."'");

if (mysqli_num_rows($check_sql) == 0) {
	
$sql_inst=mysqli_query($conn,"select  max(temp_no) as tmp_no from installation_data");
$row_inst=mysqli_fetch_array($sql_inst);
$temp_no=$row_inst['tmp_no'];

// Increment it
$nxt_temp = str_pad($temp_no + 1, 5, '0', STR_PAD_LEFT);
$prefix = "STRINST";
$year = date("Y"); // current year, e.g., 2025
$doc_no=$prefix . $year . $nxt_temp;	
	
// Step 7: Update user details
$installation_sql = "INSERT INTO installation_data SET  
        userid = '".$userid."',
        document_no = '".$doc_no."',
        temp_no = '".$nxt_temp."',
        customer_Name = '".$cust_name."',
        mobile_no = '".$cust_mobile."',
        email = '".$emailId."',
        address = '".mysqli_real_escape_string($conn, $address)."',
        city = '".$city."',
        state = '".$state."',
        pincode = '".$pincode."',
        installation_date = '".$today."',
        invoice_no = '".$inv_no."',
        product_code = '".$item_code."',
        serial_no = '".$sr."',
        dop = '".$dop."',
        status = 'Draft',
		post_status='D',
        store_name = '".$storename."',
        img_installation = '".$img_inst."',
        img_product = '".$img_product."',
        img_sheet = '".$img_sheet."',
        entry_date = '".$today."',
        entry_time = '".date('H:i:s')."'";

if (mysqli_query($conn, $installation_sql)) {
    echo json_encode([
        "status" => "success",
        "message" => "Installation Registered in Draft Mode",
        "technician_id" => $userid,
		"document_no" => $doc_no
    ]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database insert failed", "error" => mysqli_error($conn)]);
}

}
else{
 	http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Installation already Registered with this Serial No."]);
    exit;	
}
	
	
}
?>
