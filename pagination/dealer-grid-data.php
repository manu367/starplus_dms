<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
///// get access location ///
//$accessLocation=getAccessLocation($_SESSION['userid'],$link1);
//$accessState=getAccessState($_SESSION['userid'],$link1);

$fdate = $_REQUEST['fdate'];
$tdate = $_REQUEST['tdate'];
$locationstate = $_REQUEST['locationstate'];
$locationcity = $_REQUEST['locationcity'];
$locationtype = $_REQUEST['locationtype'];
$locationstatus = $_REQUEST['locationstatus'];
////// filters value/////
## selected state
if($locationstate!=""){
	$loc_state=" a. state='".$locationstate."'";
}else{
	//$loc_state=" a.state in (".$accessState.")";
	$loc_state=" 1";
}
## selected city
if($locationcity!=""){
	$loc_city=" a.city='".$locationcity."'";
}else{
	$loc_city=" 1";
}
## selected location type
if($locationtype!=""){
	$loc_type="a.id_type='".$locationtype."'";
}else{
	$loc_type="1";
}
## selected location Status
if($locationstatus!=""){
	$loc_status="a.status='".$locationstatus."'";
}else{
	$loc_status="1";
}
//////End filters value/////
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'sno',
	2 => 'name',
	3 => 'state', 
	4 => 'id_type',
	5 => 'phone',
	6 => 'status'
	
);

// getting total number records without any search
$sql = "SELECT a.sno";
$sql.=" FROM asc_master a, dealer_visit b WHERE ".$loc_state." AND ".$loc_city." AND ".$loc_type." AND ".$loc_status." AND b.party_code=a.asc_code AND b.dealer_type='New' AND a.start_date>='".$fdate."' AND a.start_date<='".$tdate."'";
$query=mysqli_query($link1, $sql) or die("dealer-grid-data.php: get dealer master1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT a.sno,a.asc_code, a.name, a.state, a.city, a.id_type, a.phone, a.create_by, a.start_date, a.status";
$sql.=" FROM asc_master a, dealer_visit b WHERE ".$loc_state." AND ".$loc_city." AND ".$loc_type." AND ".$loc_status." AND b.party_code=a.asc_code AND b.dealer_type='New' AND a.start_date>='".$fdate."' AND a.start_date<='".$tdate."'";

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (a.asc_code LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR a.name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR a.state LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR a.city LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR a.phone LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR a.create_by LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR a.start_date LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR a.id_type IN ( SELECT locationtype FROM location_type WHERE locationname LIKE '".$requestData['search']['value']."%') )";
}
$query=mysqli_query($link1, $sql) or die("dealer-grid-data.php: get dealer master2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']." ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("dealer-grid-data.php: get dealer master3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
	
	////// check this user have right to view the details
	$viewicon = "";
    $viewicon = "<div align='center'><a href='#' class='btn btn-primary' onClick=checkLocationInfo('".$row['sno']."');>View</a></div>";
	$nestedData[] = $j; 
	$nestedData[] = $row["asc_code"];
	$nestedData[] = $row["name"];
	$nestedData[] = $row["state"];
	$nestedData[] = getLocationType($row['id_type'],$link1);
	$nestedData[] = $row["phone"];
	$nestedData[] = $row["status"];
	$nestedData[] = $row["create_by"];
	$nestedData[] = $row["start_date"];
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
