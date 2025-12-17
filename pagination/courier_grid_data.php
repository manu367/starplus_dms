<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$locationstate = $_REQUEST['locationstate'];
$locationcity = $_REQUEST['locationcity'];
$status = $_REQUEST['status'];
////// filters value/////
## selected state
if($locationstate!=""){
	$loc_state="state='".$locationstate."'";
}else{
	$loc_state="1";
}
## selected city
if($locationcity!=""){
	$loc_city="city='".$locationcity."'";
}else{
	$loc_city="1";
}

## selected location Status
if($status!=""){
	$loc_status="status='".$status."'";
}else{
	$loc_status="1";
}
//////End filters value/////


$columns = array( 
// datatable column index  => database column name
	0 => 'sno',
	1 => 'couriername',
	2 => 'contact_person', 
	3 => 'couriercode',
	4 => 'email',
	5 => 'phone',
	6 => 'addrs',
	7 => 'city',	
    8 => 'state',
    9 => 'status'
);

// getting total number records without any search
if($_SESSION['userid']=="admin"){
	$sql = "SELECT sno ";
	$sql .= " FROM diesl_master WHERE $loc_state and $loc_city and  $loc_status";
}else{
	$sql = "SELECT sno ";
	$sql .= " FROM diesl_master WHERE $loc_state and $loc_city and  $loc_status";
}
// echo $sql;
// exit;
$query=mysqli_query($link1, $sql) or die("courier-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if($_SESSION['userid']=="admin"){
	$sql = "SELECT * ";
	$sql .= " FROM diesl_master WHERE $loc_state and $loc_city and  $loc_status";
}else{
	$sql = "SELECT * ";
	$sql .= " FROM diesl_master WHERE $loc_state and $loc_city and  $loc_status";
}

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (couriername LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR contact_person LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR couriercode LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR email LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR phone LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR addrs LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR city LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR state LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}

$query=mysqli_query($link1, $sql) or die("courier-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
//echo $sql;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("courier-grid-data.php: ERROR! 3");

$data = array();
$j=1;
while ($row = mysqli_fetch_array($query)) { // preparing an array
	$nestedData = array();
	
	//// check serial no. is uploaded or not
	
	$view = "";
	$view = "<a href='edit_courier.php?Submit=edit&id=".base64_encode($row['sno']).$pagenav."' title='View'><i class='fa fa-eye fa-lg' title='View Details'></i></a>";
	
	$nestedData[] = $j;
	$nestedData[] = $row["couriername"];
	$nestedData[] = $row["contact_person"];
	$nestedData[] = $row["couriercode"];
    $nestedData[] = $row["email"];
    $nestedData[] = $row["phone"];
    $nestedData[] = $row["addrs"];
    $nestedData[] = $row["city"];
    $nestedData[] = $row["state"];
    $nestedData[] = $row["status"];
	$nestedData[] = $view;

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