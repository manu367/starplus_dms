<?php
/* Database connection start */
require_once("../config/config.php");
$docid = base64_decode($_REQUEST['id']);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
///// get access location ///
$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
///// get access product sub cat
$accesspsc=getAccessProduct($_SESSION['userid'],$link1);
///// get access brand
$accessbrand=getAccessBrand($_SESSION['userid'],$link1);
///// get access state
$accessstate = getAccessState($_SESSION['userid'],$link1);
///get access state code which is used in location code string
$accessstatecode = getAccessStateCode($_SESSION['userid'],$link1);
////// filters value/////
$filter_str = "";
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND entry_date >= '".$_REQUEST['fdate']."'";
}else{
	$filter_str	.= " AND entry_date = '".$today."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND entry_date <= '".$_REQUEST['tdate']."'";
}else{
	$filter_str	.= " AND entry_date = '".$today."'";
}
if($_REQUEST['from_location'] !=''){
	$filter_str	.= " AND po_from = '".$_REQUEST['from_location']."'";
}
if($_REQUEST['to_location'] !=''){
	$filter_str	.= " AND po_to = '".$_REQUEST['to_location']."'";
}

if($_SESSION["userid"]=="admin"){                              
	if($_REQUEST['from_state']){ 
		$pst_state = explode("~",$_REQUEST['from_state']); 
		$stat = " AND state='".$pst_state[0]."'";
		$statt = " AND SUBSTRING(po_from, 5, 2) = '".$pst_state[1]."'";
	}else{ 
		$stat = "";
		$statt = "";
	}
}else{
	if($_REQUEST['from_state']){ 
		$pst_state = explode("~",$_REQUEST['from_state']); 
		$stat = " AND state='".$pst_state[0]."'";
		$statt = " AND SUBSTRING(po_from, 5, 2) = '".$pst_state[1]."'";
	}else{ 
		$stat = " AND state IN (".$accessstate.")";
		$statt = " AND SUBSTRING(po_from, 5, 2) IN (".$accessstatecode.")";
	}
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
if($_SESSION["userid"]=="admin"){
	$sql = "SELECT id";
	$sql.=" FROM purchase_order_master where 1 ".$filter_str." ".$statt." AND status='Approved'";
}else{
	$sql = "SELECT id";
	$sql.=" FROM purchase_order_master po_from in (".$accesslocation.") AND req_type NOT IN ('COMBO PO') AND status='Approved' AND po_no IN (SELECT po_no FROM purchase_order_data WHERE psc_id IN (".$accesspsc.") AND brand_id IN (".$accessbrand.")) ".$filter_str." ".$statt." ";
}

$query=mysqli_query($link1, $sql) or die("corporate-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if($_SESSION["userid"]=="admin"){
	$sql = "SELECT id, po_from,po_to,po_no, requested_date,create_by, status";
	$sql.=" FROM purchase_order_master where 1  ".$filter_str." ".$statt." AND status='Approved'";
}else{
	$sql = "SELECT id, po_from,po_to,po_no, requested_date,create_by, status";
	$sql.=" FROM purchase_order_master po_from in (".$accesslocation.") AND req_type NOT IN ('COMBO PO') AND status='Approved' AND po_no IN (SELECT po_no FROM purchase_order_data WHERE psc_id IN (".$accesspsc.") AND brand_id IN (".$accessbrand.")) ".$filter_str." ".$statt." ";
}


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( po_from LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR po_to LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR po_no LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR requested_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR create_by LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}


$query=mysqli_query($link1, $sql) or die("corporate-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("corporate-grid-data.php: ERROR! 3");
$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
    ////// check this user have right to view the details
    
	// STATUS //
	   $status ="";
	   if($row['status']=="PFA"){ 
		$status = "<span class='red_small'>".$row['status']."</span>";
	    }else{
		$status = $row['status'];
	    }
    // STATUS //
	
	// VIEW //
		$view="";
        $view = "<div align='center'><a href='poDetails.php?id=".base64_encode($row['po_no']).$pagenav."' title='view'><i class='fa fa-eye fa-lg ' title='view details'></i></a></div>";
    // VIEW //

	// INVOICING //
		$invoicing="";
		$invoicing2="";
        if($row["sale_type"]=="PRIMARY" || substr($row['po_from'],0,4)=="EASR"|| substr($row['po_from'],0,4)=="EART" || substr($row['po_from'],0,4)=="EABR"){
			
		$invoicing=	"<a href='#' onclick=openPoAllocation('".base64_encode($row['po_no'])."')> <i class='fa fa-external-link fa-lg' title='PO Allocation'></i></a>&nbsp;&nbsp;";}

		$invoicing2= "<a href='invoiceAgainstPON_ADV.php?id=".base64_encode($row['po_no'])."".$pagenav."' title='Go to Advance invoicing'><i class='fa fa-envelope-open fa-lg ' title='Go to Advance invoicing'></i></a>";
	// INVOICING //
		
	$nestedData=array();     
	$nestedData[] = $j; 
	$nestedData[] = getLocationDetails($row['po_from'],"name,city,state",$link1);
	$nestedData[] = getLocationDetails($row['po_to'],"name,city,state",$link1);
	$nestedData[] = $row["po_no"];
	$nestedData[] = $row["requested_date"];
	$nestedData[] = getAdminDetails($row["create_by"],"name",$link1);
    $nestedData[] = $status;
    $nestedData[] = $view;
    $nestedData[] = $invoicing.$invoicing2;
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
