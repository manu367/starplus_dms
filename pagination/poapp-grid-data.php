<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
////// filters value/////
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
if($_REQUEST['fdate']=="" && $_REQUEST['tdate']==""){ $filter_str	.= " AND entry_date = '".$today."'";}
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND entry_date >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND entry_date <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['from_location'] !=''){
	$filter_str	.= " AND po_from = '".$_REQUEST['from_location']."'";
}
if($_REQUEST['to_location'] !=''){
	$filter_str	.= " AND po_to = '".$_REQUEST['to_location']."'";
}
if($_REQUEST['status'] !=''){
	$filter_str	.= " AND status = '".$_REQUEST['status']."'";
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
//////End filters value/////
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'po_to',
	2 => 'po_from',
	3 => 'po_no', 
	4 => 'requested_date',
	5 => 'req_type',
	6 => 'create_by',
	7 => 'status'
	
);

// getting total number records without any search
if($_SESSION["userid"]=="admin"){
	$sql = "SELECT id";
	$sql.= " FROM purchase_order_master WHERE 1 ".$filter_str." ".$statt."";
}else{
	$sql = "SELECT id";
	$sql.= " FROM purchase_order_master WHERE po_from in (".$accesslocation.") AND ((po_no IN (SELECT po_no FROM purchase_order_data WHERE psc_id IN (".$accesspsc.") AND brand_id IN (".$accessbrand.")) AND req_type!='COMBO PO') OR req_type='COMBO PO') ".$filter_str." ".$statt."";
}
$query=mysqli_query($link1, $sql) or die("asc-grid-data.php: get po app1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if($_SESSION["userid"]=="admin"){
	$sql = "SELECT *";
	$sql.= " FROM purchase_order_master WHERE 1 ".$filter_str." ".$statt."";
}else{
	$sql = "SELECT *";
	$sql.= " FROM purchase_order_master WHERE po_from in (".$accesslocation.") AND ((po_no IN (SELECT po_no FROM purchase_order_data WHERE psc_id IN (".$accesspsc.") AND brand_id IN (".$accessbrand.")) AND req_type!='COMBO PO') OR req_type='COMBO PO') ".$filter_str." ".$statt."";
}
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (po_to LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR po_from LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR po_no LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR requested_date LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR create_by LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("asc-grid-data.php: get po app2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']." ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("asc-grid-data.php: get po app3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
	$viewicon = "";
	////// check this user have right to view the details
    $viewicon = "<div align='center'><a href='poApprovalPage.php?id=".base64_encode($row['po_no'])."&fdate=".base64_encode($_REQUEST['fdate'])."&tdate=".base64_encode($_REQUEST['tdate'])."&from_state=".base64_encode($_REQUEST['from_state'])."&from_location=".base64_encode($_REQUEST['from_location'])."&to_location=".base64_encode($_REQUEST['to_location'])."&status=".base64_encode($_REQUEST['status'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view po details'></i></a></div>";
		
	if($row['status']=="PFA"){ $sts = "<span class='red_small'>".$row['status']."</span>";}else{ $sts = $row['status'];}
	
	$nestedData[] = $j; 
	$nestedData[] = str_replace("~",",",getLocationDetails($row['po_to'],"name,city,state,asc_code",$link1));
	$nestedData[] = str_replace("~",",",getLocationDetails($row['po_from'],"name,city,state,asc_code",$link1));
	$nestedData[] = $row["po_no"];
	$nestedData[] = $row['requested_date']." ".$row['entry_time'];
	$nestedData[] = $row["req_type"];
	$nestedData[] = getAdminDetails($row['create_by'],"name",$link1);
	$nestedData[] = $sts;
	$nestedData[] = $viewicon;
	
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