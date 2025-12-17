<?php
/* Database connection start */
require_once("../config/config.php");
$docid = base64_decode($_REQUEST['doc_id']);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;
//$accesslocation = getAccessLocation($_SESSION['userid'], $link1);
////// filters value/////
$filter_str = 1;
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " and DATE(entry_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " and DATE(entry_date) <= '".$_REQUEST['tdate']."'";
}
//////End filters value/////
$columns = array(
    // datatable column index  => database column name
    0 => 'id',
    1 => 'pjp_name',
    2 => 'start_date',
    3 => 'end_date',
    4 => 'entry_by',
    5 => 'entry_date'
);
// getting total number records without any search
$sql = "SELECT id ";
$sql .= " FROM pjp_master WHERE ".$filter_str."";
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("pjp-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT * ";
$sql .= " FROM pjp_master WHERE ".$filter_str."";

if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql .= " AND (id LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR pjp_name LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR start_date LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR end_date LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR entry_by LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR entry_date LIKE '" . $requestData['search']['value'] . "%' )";
}

$query = mysqli_query($link1, $sql) or die("pjp-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
//echo $sql;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
$query = mysqli_query($link1, $sql) or die("pjp-grid-data.php: ERROR! 3");

$data = array();
$j = 1;
while ($row = mysqli_fetch_array($query)) { // preparing an array

    $nestedData = array();
    $nestedData[] = $j;
    $nestedData[] = $row['pjp_name'];
    $nestedData[] = $row['start_date'];
    $nestedData[] = $row['end_date'];
    $nestedData[] = getAdminDetails($row['entry_by'],"name",$link1);
    $nestedData[] = $row['entry_date'];
    
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