<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;
// FILTER //
$filter_str = "";
if($_REQUEST['fdate']=="" && $_REQUEST['tdate']==""){ $filter_str	.= " AND DATE(entry_date) = '".$today."'";}
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND DATE(entry_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND DATE(entry_date) <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['location_code'] !=''){
	$filter_str	.= " AND to_location = '".$_REQUEST['location_code']."'";
}
if($_REQUEST['status'] !=''){
	$filter_str	.= " AND status = '".$_REQUEST['status']."'";
}
//// get cancel rights
//$isCnlRight = getCancelRightNew($_SESSION['userid'],"3",$link1);
///// get access location ///
$accesslocation=getAccessLocation($_SESSION['userid'],$link1);

$columns = array(
    // datatable column index  => database column name
    0 => 'id',
    1 => 'sale_type',
    2 => 'from_location_name',
    3 => 'to_location_name',
    4 => 'doc_no',
    5 => 'doc_date',
	6 => 'entry_by',
	7 => 'status'
);
// getting total number records without any search
if ($_SESSION["userid"] == "admin" || $_SESSION['utype']=="1") {
    $sql = "SELECT id";
    $sql .= " FROM sale_uploader WHERE 1 " . $filter_str . " ";
} else {
    $sql = "SELECT id";
    $sql .= " FROM sale_uploader WHERE 1 " . $filter_str . "  AND to_location IN (".$accesslocation.")";
}
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("saleupd-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if ($_SESSION["userid"] == "admin") {
    $sql = "SELECT *";
    $sql .= " FROM sale_uploader WHERE 1 " . $filter_str . " ";
} else {
    $sql = "SELECT *";
    $sql .= " FROM sale_uploader WHERE 1 " . $filter_str . "  AND to_location in (".$accesslocation.")";
}
if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql .= " AND ( sale_type LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR from_location_name LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR to_location_name LIKE '" . $requestData['search']['value'] . "%'";
	$sql .= " OR to_location_sapcode LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR doc_no LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR status LIKE '" . $requestData['search']['value'] . "%' )";
}
$sql .= " GROUP BY doc_no";
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("saleupd-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
// echo $sql;
// exit;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
$query = mysqli_query($link1, $sql) or die("saleupd-grid-data.php: ERROR! 3");

$data = array();
$j = 1;
while ($row = mysqli_fetch_array($query)) { // preparing an array   
	//start status//
	$status="";
	if($row['status']=="Pending"){ 
    	$status = "<span class='red_small'>".$row['status']."</span>";
	}
    else{ 
    	$status = $row['status'];
	}
	//end status//
    // START PRINT //
    $print="";
	$print = "<a href='../print/sale_upload_print.php?id=".base64_encode($row['doc_no'])."' target='_blank' title='Print Invoice'><i class='fa fa-print fa-lg' title='Print Invoice'></i></a>";
    // END PRINT//
    //START VIEW//
    $view="";
    $view = "<div align='center'><a href='sale_upload_view.php?id=" . base64_encode($row['doc_no']) . "" . $pagenav . "   title='view'><i class='fa fa-eye fa-lg' title='view details'></i></a></div>";
    //END VIEW//
     //START CANCEL//
    $cancel="";
    if($isCnlRight==1){
        if ($row['status'] != 'Cancelled') {
            //$cancel = "<div align='center'> <a href='cancelopeningDetails.php?op=cancel&id=" . base64_encode($row['doc_no']) . "" . $pagenav . "'title='Cancel This Challan'><i class='fa fa-trash fa-lg' title='Cancel This Challan'></i></a></div>";
        }
    }
    // END CANCEL//
    $nestedData = array();
    $nestedData[] = $j;
    $nestedData[] = $row['sale_type'];
    $nestedData[] = $row['from_location_name'];
    $nestedData[] = $row['to_location_name'];
	$nestedData[] = $row['to_location_sapcode'];
	$nestedData[] = $row['doc_no'];
	$nestedData[] = $row['doc_date'];
    $nestedData[] = getAdminDetails($row['entry_by'],"name",$link1)." ".$row['entry_by'];
    $nestedData[] = $status;
    $nestedData[] = $print;
    $nestedData[] = $view;
    $nestedData[] = $cancel;
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