<?php
/* Database connection start */
require_once("../config/config.php");
$accessState=getAccessState($_SESSION['userid'],$link1);
/////get status//
//$arrstatus = getFullStatus("master",$link1);
///// get operation rights
//$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
////// filters value/////
$filter = "";
## selected state
if($_REQUEST['location_state']!=""){
	$filter .=" AND state='".$_REQUEST['location_state']."'";
}
## selected product
if($_REQUEST['product']!=""){
	$filter .=" AND partcode='".$_REQUEST['product']."'";
}
## selected location type
if($_REQUEST['location_type']!=""){
	$filter .=" AND id_type='".$_REQUEST['location_type']."'";
}
## selected  Status
if($_REQUEST['status']!="" && $_REQUEST['status']!="all"){
	$filter .=" AND status='".$_REQUEST['status']."'";
}
//////End filters value/////
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'state', 
	2 => 'id_type',
	3 => 'partcode',
	4 => 'reward_point',
	5 => 'parent_party_reward',
	6 => 'status'
);

// getting total number records without any search
$sql = "SELECT id";
$sql.=" FROM reward_points_master WHERE state IN (".$accessState.") ".$filter;
$query=mysqli_query($link1, $sql) or die("rwdpoint-grid-data.php: get sch1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT *";
$sql.=" FROM reward_points_master WHERE state IN (".$accessState.") ".$filter;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( state LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR id_type LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR partcode LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR reward_point LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR parent_party_reward LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
	
	$query=mysqli_query($link1, $sql) or die("rwdpoint-grid-data.php: get sch2");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
}
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("rwdpoint-grid-data.php: get sch3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    ////// check this user have right to view the details
    //if($get_opr_rgts['view']=="Y"){
        $viewicon = "<div align='center'><a href='edit_reward_point.php?op=edit&id=".base64_encode($row['id'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit details'></i></a></div>";
    //}else{
        //$viewicon = "";
    //}
	
	$nestedData[] = $j; 
	$nestedData[] = $row["state"];
	$nestedData[] = getLocationType($row["id_type"],$link1);
	$nestedData[] = $row["partcode"];
	$nestedData[] = $row["reward_point"];
	$nestedData[] = $row["parent_party_reward"];
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
