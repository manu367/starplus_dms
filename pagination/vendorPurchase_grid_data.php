<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
////// filters value/////
if($_REQUEST["fdate"]){
	$fdatefilter = " AND entry_date >= '".$_REQUEST["fdate"]."'";
}
if($_REQUEST["tdate"]){
	$tdatefilter = " AND entry_date <= '".$_REQUEST["tdate"]."'";
}
if($_REQUEST["po_to"]){
	$vndfilter = " AND po_to = '".$_REQUEST["po_to"]."'";
}

	//// get cancel rights
	$isCnlRight = getCancelRightNew($_SESSION['userid'],"9",$link1);
	///// get access location ///
	$accesslocation = getAccessLocation($_SESSION['userid'],$link1);	

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
//////

// getting total number records without any search
if($_SESSION['userid']=="admin"){
	$sql = "SELECT id ";
	$sql .= " FROM vendor_order_master WHERE req_type='VPO' AND po_from IN (".$accesslocation.") ".$fdatefilter." ".$tdatefilter." ".$vndfilter."";
}else{
	$sql = "SELECT id ";
	$sql .= " FROM vendor_order_master WHERE req_type='VPO' AND po_from IN (".$accesslocation.") ".$fdatefilter." ".$tdatefilter." ".$vndfilter."";
}
// echo $sql;
// exit;
$query=mysqli_query($link1, $sql) or die("vendor-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if($_SESSION['userid']=="admin"){
	$sql = "SELECT * ";
	$sql .= " FROM vendor_order_master WHERE req_type='VPO' AND po_from IN (".$accesslocation.") ".$fdatefilter." ".$tdatefilter." ".$vndfilter."";
}else{
	$sql = "SELECT * ";
	$sql .= " FROM vendor_order_master WHERE req_type='VPO' AND po_from IN (".$accesslocation.") ".$fdatefilter." ".$tdatefilter." ".$vndfilter."";
}


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (po_from LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR po_to LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR po_no LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR requested_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR create_by LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}

$query=mysqli_query($link1, $sql) or die("vendor-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
//echo $sql;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("vendor-grid-data.php: ERROR! 3");

$data = array();
$j=1;
while ($row = mysqli_fetch_array($query)) { // preparing an array
	
	//// check serial no. is uploaded or not
	
//status//
	$status="";
	if($row['status']=="Pending"){ $status = "<span class='red_small'>".$row['status']."</span>";}else{ $status = $row['status'];}
//status//

//view//
	$view = "";
	$view = "<a href='vpoDetailsN.php?op=edit&id=".base64_encode($row['po_no']).$pagenav."' title='view '><i class='fa fa-eye fa-lg' title='View Details'></i></a>";
//view//
	
//print//
    $print = "";
	$print = "<a href='../print/vpo_print.php?id=".base64_encode($row['po_no']).$pagenav."' title='view' target='_blank'><i class='fa fa-print fa-lg' title='PO Print'></i></a>";
//print//

//cancel//
	$cancel="";
	if($isCnlRight == 1){
		if($row['status']!='Received' && $row['status']!='Cancelled') {
			$cancel = "<a href='cancelvendorPurchaseN.php?op=cancel&id=".base64_encode($row['po_no']).$pagenav."' title='Cancel PO'><i class='fa fa-trash fa-lg' title='Cancel PO'></i></a>";
		}
	}
//cancel//
	
    $nestedData = array();	
	$nestedData[] = $j;
	// $nestedData[] = str_replace("~",",",getLocationDetails($row['po_from'],"name,city,state",$link1));
	// $nestedData[] = str_replace("~",",",getVendorDetails($row['po_to'],"name,city,state",$link1));
	$nestedData[] = getAnyParty($row['po_from'],$link1);
	$nestedData[] = getAnyParty($row['po_to'],$link1);
	$nestedData[] = $row["po_no"];
	$nestedData[] = $row["requested_date"];
	$nestedData[] = getAdminDetails($row['create_by'],"name", $link1);
	$nestedData[] = $status;
	$nestedData[] = $view;
	$nestedData[] = $print;
	$nestedData[] = $cancel;
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