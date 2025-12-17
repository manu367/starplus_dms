<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
////// filters value/////
$filter_str = "";
if($_REQUEST['selyear'] !=''){
	$filter_str	.= " AND year = '".$_REQUEST['selyear']."'";
}
if($_REQUEST['selmonth'] !=''){
	$mnth = date("m", strtotime($_REQUEST["selmonth"]."-".$_REQUEST["selyear"]));
	$filter_str	.= " AND month = '".$mnth."'";
}

if(!empty($_REQUEST['user_id'])){
	$user_id=" AND user_id='".$_REQUEST['user_id']."'";
}else{
	$user_id="";
}
if($_REQUEST['user_id']){
	$child = $_REQUEST['user_id'];
}else{
	$child = getHierarchyStr($_SESSION["userid"], $link1, "");
}
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'target_no', 
	2 => 'user_id',
	3 => 'month',
	4 => 'year',
	5 => 'target_type',
	6 => 'status'	
);

// getting total number records without any search
if($_SESSION["userid"]=="admin"){
	$sql = "SELECT id";
	$sql.=" FROM sf_target_master WHERE 1 ".$filter_str." ".$user_id;
}else{
	$sql = "SELECT id";
	$sql.=" FROM sf_target_master WHERE 1 ".$filter_str." AND (user_id IN ('".str_replace(",","','",$child)."') OR user_id='".$_SESSION["userid"]."')";
}
$query=mysqli_query($link1, $sql) or die("target-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if($_SESSION["userid"]=="admin"){
	$sql = "SELECT id, target_no,user_id,month, year, target_type,status";
	$sql.=" FROM sf_target_master WHERE 1 ".$filter_str." ".$user_id;
}else{
	$sql = "SELECT id, target_no,user_id, month, year, target_type, status";
	$sql.=" FROM sf_target_master WHERE 1 ".$filter_str." AND (user_id IN ('".str_replace(",","','",$child)."') OR user_id='".$_SESSION["userid"]."')";
}
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( target_no LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR user_id LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR month LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR year LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR target_type LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}

$query=mysqli_query($link1, $sql) or die("target-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("target-grid-data.php: ERROR! 3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	
	$nestedData=array(); 

    $viewicon = "<div align='center'><a href='target_view.php?id=".base64_encode($row['id'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view details'></i></a></div>";
	$mnth = "";
	if($row['month'] == '01'){ $mnth = "JAN"; }
	else if($row['month'] == '02'){ $mnth = "FEB"; }
	else if($row['month'] == '03'){ $mnth = "MAR"; }
	else if($row['month'] == '04'){ $mnth = "APR"; }
	else if($row['month'] == '05'){ $mnth = "MAY"; }
	else if($row['month'] == '06'){ $mnth = "JUN"; }
	else if($row['month'] == '07'){ $mnth = "JUL"; }
	else if($row['month'] == '08'){ $mnth = "AUG"; }
	else if($row['month'] == '09'){ $mnth = "SEP"; }
	else if($row['month'] == '10'){ $mnth = "OCT"; }
	else if($row['month'] == '11'){ $mnth = "NOV"; }
	else if($row['month'] == '12'){ $mnth = "DEC"; }
	else{}
	
	
	$nestedData[] = $j; 
	$nestedData[] = $row["target_no"];
	$nestedData[] = getAdminDetails($row['user_id'],"name,username,oth_empid",$link1);
	$nestedData[] = $mnth;
	$nestedData[] = $row["year"];
	$nestedData[] = $row["target_type"];
	$nestedData[] = $row["status"];
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