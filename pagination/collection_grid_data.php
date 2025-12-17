<?php
/* Database connection start */
require_once("../config/config.php");
@extract($_POST);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;
////// filters value/////
$filter_str = 1;
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " and DATE(entry_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " and DATE(entry_date) <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['user_id'] !=''){
	$filter_str	.= " and user_id = '".$_REQUEST['user_id']."'";
}
if($_REQUEST['status'] !=''){
	$filter_str	.= " and status = '".$_REQUEST['status']."'";
}

$columns = array(
    // datatable column index  => database column name
    0 => 'id',
    1 => 'transaction_date',
    2 => 'entry_date',
    3 => 'amount',
    4 => 'status'
  
);

// getting total number records without any search
if ($_SESSION['userid'] == "admin") {
    $sql = "SELECT id ";
    $sql .= " FROM party_collection WHERE  ".$filter_str."";
} else {
    $sql = "SELECT id ";
    $sql .= " FROM party_collection WHERE ".$filter_str." AND user_id IN (SELECT username FROM admin_users WHERE reporting_manager='".$_SESSION["userid"]."')";
}
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("collection-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if ($_SESSION['userid'] == "admin") {
    $sql = "SELECT * ";
    $sql .= " FROM party_collection WHERE ".$filter_str."";
} else {
    $sql = "SELECT * ";
    $sql .= " FROM party_collection WHERE ".$filter_str." AND user_id IN (SELECT username FROM admin_users WHERE reporting_manager='".$_SESSION["userid"]."')";
}

if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql .= " AND (id LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR transaction_date LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR entry_date LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR amount LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR status LIKE '" . $requestData['search']['value'] . "%' )";
}

$query = mysqli_query($link1, $sql) or die("collection-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
//echo $sql;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
$query = mysqli_query($link1, $sql) or die("collection-grid-data.php: ERROR! 3");

$data = array();
$j = 1;
while ($row = mysqli_fetch_array($query)) { // preparing an array
    $userdet = explode("~",getAdminDetails($row['user_id'],"name,oth_empid",$link1));

// STATUS //
    $status ="";
    if($row['status']=="Pending"){ 
     $status = "<span class='red_small'>".$row['status']."</span>";
     }else{
     $status = $row['status'];
     }
// STATUS //

    //START VIEW//
       $view="";
       $view = "<a href='collectionAppPage.php?id=" . base64_encode($row['id']) . "" . $pagenav . "   title='view'><i class='fa fa-eye fa-lg' title='view details'></i></a>";
    //END VIEW//

    $nestedData = array();
    $nestedData[] = $j;
    $nestedData[] = $userdet[0]." (".$row['user_id'].")";
    $nestedData[] = $userdet['1'];
    $nestedData[] = str_replace("~",",",getLocationDetails($row['party_code'],"name,city,state,asc_code",$link1));
    $nestedData[] = $row['transaction_date'];
    $nestedData[] = $row['entry_date'];
    $nestedData[] = $row['amount'];
    $nestedData[] = $status;
    $nestedData[] = $view;
    
    $data[] = $nestedData;
    $j++;
}

$json_data = array(
    "draw"            => intval($requestData['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
    "recordsTotal"    => intval($totalData),  // total number of records
    "recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $data   // total data array
);

echo json_encode($json_data);  // send data as json format
?>