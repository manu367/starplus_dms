<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
////// filters value/////
$filter_str = 1;
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " and DATE(entry_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " and DATE(entry_date) <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['feedback_by'] !=''){
	$filter_str	.= " and entry_by = '".$_REQUEST['feedback_by']."'";
}
if($_REQUEST['feedback_type'] !=''){
	$filter_str	.= " and module = '".$_REQUEST['feedback_type']."'";
}
//////End filters value/////
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'query',
	2 => 'problem',
	3 => 'module', 
	4 => 'request',
	5 => 'request',
	6 => 'entry_by',
	7 => 'entry_date'	
);

// getting total number records without any search
$sql = "SELECT id";
$sql.=" FROM query_master where ".$filter_str."";
$query=mysqli_query($link1, $sql) or die("fb-grid-data.php: get fb1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT *";
$sql.=" FROM query_master where ".$filter_str."";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (query LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR problem LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR module LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR request LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR entry_by LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR entry_by IN (select username FROM admin_users WHERE name LIKE '".$requestData['search']['value']."%' OR oth_empid LIKE '".$requestData['search']['value']."%')";
	$sql.=" OR entry_date LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("fb-grid-data.php: get fb2");
$totalFiltered = mysqli_num_rows($query); 
// when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']." ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("fb-grid-data.php: get fb3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
	$expl = explode("~",$row["request"]);
	////// check this user have right to view the details
        $locicon = "<div align='center'><a href='#' id='loc".$j."' title='Location Details' data-toggle='popover' data-trigger='focus' data-content='".$row['address']."'><i class='fa fa-map-marker fa-lg'></i></a></div>";
		if ($row['attachment'] != '') {
			$imgicon = '<img src="../salesapi/feedbackimg/'.substr($row['entry_date'],0,7).'/'.$row['attachment'].'" alt="" id="image'.$j.'" onClick="getThisValue('.$j.')" style="width: 100%;"/>';
		}else{
			$imgicon = "Not clicked";
		}
		
	$nestedData[] = $j; 
	$nestedData[] = $row["query"];
	$nestedData[] = $row["problem"];
	$nestedData[] = $row["module"];
	$nestedData[] = $expl[0];
	$nestedData[] = $expl[1];
	$nestedData[] = getAdminDetails($row['entry_by'],"name",$link1);
	$nestedData[] = $row['entry_date'];
	$nestedData[] = $locicon;
	$nestedData[] = $imgicon;
	
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
