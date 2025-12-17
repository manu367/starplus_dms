<?php
/* Database connection start */
require_once("../config/config.php");
$docid = base64_decode($_REQUEST['doc_id']);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;
$accesslocation = getAccessLocation($_SESSION['userid'], $link1);

////// filters value/////
$filter_str = 1;
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " and DATE(entry_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " and DATE(entry_date) <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST["task_type"]){
	$filter_str	.= " and task_type = '".$_REQUEST['task_type']."'";
}
if($_REQUEST["assign_to"]){
	$filter_str	.= " and entry_by = '".$_REQUEST['assign_to']."'";
}
//////End filters value/////


$columns = array(
    // datatable column index  => database column name
    0 => 'id',
    1 => 'entry_by',
    2 => 'plan_date',
    3 => 'sch_visit',
    4 => 'change_visit',
    5 => 'entry_date',
    6 => 'remark',
    7 => 'app_status'
);

// getting total number records without any search
if ($_SESSION['userid'] == "admin") {
    $sql = "SELECT id ";
    $sql .= " FROM deviation_request WHERE  ".$filter_str." ";
} else {
    $sql = "SELECT id ";
    $sql .= " FROM deviation_request WHERE ".$filter_str." ";
}


// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("deviation-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if ($_SESSION['userid'] == "admin") {
    $sql = "SELECT * ";
    $sql .= " FROM deviation_request WHERE ".$filter_str." ";
} else {
    $sql = "SELECT * ";
    $sql .= " FROM deviation_request WHERE ".$filter_str." ";
}

if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql .= " AND (id LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR entry_by LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR plan_date LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR sch_visit LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR change_visit LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR entry_date LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR remark LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR app_status LIKE '" . $requestData['search']['value'] . "%' )";
}

$query = mysqli_query($link1, $sql) or die("deviation-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
//echo $sql;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
$query = mysqli_query($link1, $sql) or die("deviation-grid-data.php: ERROR! 3");

$data = array();
$j = 1;
while ($row = mysqli_fetch_array($query)) { // preparing an array
    
    // echo $row['id'];
    // exit;
    $schdate = mysqli_fetch_assoc(mysqli_query($link1,"SELECT plan_date FROM pjp_data WHERE id='".$row["pjp_id"]."'"));
   
     // START ACTION //
        $action="";
        if($row['app_status']=="Pending For Approval"){ 
        	$action="<a href='#' onClick=openActionModel('".base64_encode($row['id'])."','".base64_encode($row['app_status'])."')><i class='fa fa-edit fa-lg' title='Take Action Against Request'></i></a>";}
        else{
        }
       
      // END ACTION //
      
    $nestedData = array();
    $nestedData[] = $j;
    $nestedData[] = getAdminDetails($row['entry_by'],"name",$link1)." (".$row['entry_by'].")";
    $nestedData[] = $schdate['plan_date'];
    $nestedData[] = $row['sch_visit'];
    $nestedData[] = $row['change_visit'];
    $nestedData[] = $row['entry_date'];
    $nestedData[] = $row['remark'];
    $nestedData[] = $row['app_status'];
    $nestedData[] = $action;

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