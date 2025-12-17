<?php
/* Database connection start */
require_once("../config/config.php");
///// get access location ///
$accesslocation = getAccessLocation($_SESSION['userid'],$link1);
$cancel_right = mysqli_num_rows(mysqli_query($link1,"SELECT id FROM access_cancel_rights WHERE uid='".$_SESSION['userid']."' AND status='Y' AND cancel_type='13'"));	
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;

if($_REQUEST["fdate"]){
	$fdatefilter = " AND entry_date >= '".$_REQUEST["fdate"]."'";
}
if($_REQUEST["tdate"]){
	$tdatefilter = " AND entry_date <= '".$_REQUEST["tdate"]."'";
}
if($_REQUEST["po_to"]){
	$vndfilter = " AND po_to = '".$_REQUEST["po_to"]."'";
}

$columns = array(
    // datatable column index  => database column name
    0 => 'id',
    1 => 'po_from',
    2 => 'po_to',
    3 => 'po_no',
    4 => 'requested_date',
    5 => 'create_by',
    6 => 'status'
   
);

// getting total number records without any search
if ($_SESSION["userid"] == "admin") {
    $sql = "SELECT *";
    $sql .= " FROM vendor_order_master WHERE  po_from IN (".$accesslocation.")  AND req_type='VPO' AND status='Pending' ".$fdatefilter." ".$tdatefilter." ".$vndfilter." ";
} else {
    $sql = "SELECT *";
    $sql .= " FROM vendor_order_master WHERE  po_from IN (".$accesslocation.")  AND req_type='VPO' AND status='Pending' ".$fdatefilter." ".$tdatefilter." ".$vndfilter." ";
}
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("grn-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if ($_SESSION["userid"] == "admin") {
    $sql = "SELECT *";
    $sql .= " FROM vendor_order_master WHERE  po_from IN (".$accesslocation.")  AND req_type='VPO' AND status='Pending' ".$fdatefilter." ".$tdatefilter." ".$vndfilter." ";
} else {
    $sql = "SELECT *";
    $sql .= " FROM vendor_order_master WHERE  po_from IN (".$accesslocation.")  AND req_type='VPO' AND status='Pending' ".$fdatefilter." ".$tdatefilter." ".$vndfilter."  ";
}
if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql .= " AND ( main_location LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR from_location LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR to_location LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR move_stocktype LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR doc_no LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR entry_date LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR app_status LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR status LIKE '" . $requestData['search']['value'] . "%' )";
}
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("grn-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
// echo $sql;
// exit;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
$query = mysqli_query($link1, $sql) or die("grn-grid-data.php: ERROR! 3");

$data = array();
$j = 1;
while ($row = mysqli_fetch_array($query)) { // preparing an array
    $nestedData = array();
    
   //STATUS//
    if($row['status']=="Pending"){
         $status = "<span class='red_small'>".$row['status']."</span>";}
         else{ $status = $row['status'];}
   //STATUS//

    //START VIEW//
    $view="";
    $view = "<a href='vpoDetailsN.php?req=grnAgainstPO&id=" . base64_encode($row['po_no']) . "" . $pagenav . "   title='view'><i class='fa fa-eye fa-lg' title='view details'></i></a>";
    //END VIEW//

   //RECEIVE PO//
   if($row['status']=="Pending"){
   $receive="";
   $receive = "<a href='receivegrn_viewN.php?op=edit&id==" . base64_encode($row['po_no']) . "" . $pagenav . "   title='Receive PO'><i class='fa fa-shopping-bag fa-lg' title='Receive PO'></i></a>";}
   //RECEIVE PO//

    
    $nestedData[] = $j;
    $nestedData[] = str_replace("~",",",getLocationDetails($row['po_from'],"name,city,state",$link1));
    $nestedData[] = str_replace("~",",",getVendorDetails($row['po_to'],"name,city,state",$link1));
    $nestedData[] = $row['po_no'];
    $nestedData[] = dt_format($row['requested_date']);
    $nestedData[] = getAdminDetails($row['create_by'],"name",$link1);
    $nestedData[] = $status;
    $nestedData[] = $view;
    $nestedData[] = $receive;

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