<?php
/* Database connection start */
require_once("../config/config.php");

/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
////// filters value/////
$filter_str = "1";
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND DATE(create_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND DATE(create_date) <= '".$_REQUEST['tdate']."'";
}

$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'serial_no', 
	2 => 'product_code',
	3 => 'model_code',
	4 => 'product_name',
	5 => 'dealer_code',
	6 => 'dealer_name',
	7 => 'create_date',
	8 => 'update_date'	
);

// getting total number records without any search
if($_SESSION["userid"]=="admin"){
	$sql = "SELECT id";
	$sql.=" FROM serial_upload_data WHERE ".$filter_str." ";
}else{
	$sql = "SELECT id";
	$sql.=" FROM serial_upload_data WHERE ".$filter_str."";
}
$query=mysqli_query($link1, $sql) or die("serial-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if($_SESSION["userid"]=="admin"){
	$sql = "SELECT id, serial_no,product_code,model_code, product_name, dealer_code,dealer_name,create_date,update_date";
	$sql.=" FROM serial_upload_data WHERE ".$filter_str." ";
}else{
	$sql = "SELECT id, serial_no,product_code, model_code, product_name, dealer_code, dealer_name,create_date,update_date";
	$sql.=" FROM serial_upload_data WHERE ".$filter_str."";
}
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( serial_no LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR product_code LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR model_code LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR product_name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR dealer_code LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR dealer_name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR create_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR update_date LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("serial-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("serial-grid-data.php: ERROR! 3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] = $row["serial_no"];
	// $nestedData[] = getAdminDetails($row['user_id'],"name,username,oth_empid",$link1);
	$nestedData[] = $row["product_code"];
	$nestedData[] = $row["model_code"];
	$nestedData[] = $row["product_name"];
	$nestedData[] = $row["dealer_code"];
	$nestedData[] = $row["dealer_name"];
	$nestedData[] = $row["create_date"];
	$nestedData[] = $row["update_date"];

	
	$data[] = $nestedData;
	$j++;
}


$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);

echo json_encode($json_data);  // send data as json format
?>
