<?php
/* Database connection start */
require_once("../config/config.php");

/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;


$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'userid', 
	2 => 'visit_date',
	3 => 'visit_city',
	4 => 'dealer_type',
	5 => 'party_code',
    6 => 'address',
    7 => 'remark',
    8 => 'location'
 
);
if($_REQUEST["username"]){ 
    $uid = "userid ='".$_REQUEST["username"]."'";}
    else{ $uid = "1";}

// getting total number records without any search
if($_SESSION["userid"]=="admin"){
	$sql = "SELECT id";
	$sql.=" FROM dealer_visit WHERE ".$uid." AND visit_date >= '".$_REQUEST["fdate"]."' AND visit_date <= '".$_REQUEST["tdate"]."'";
}else{
	$sql = "SELECT id";
	$sql.=" FROM dealer_visit WHERE ".$uid." AND visit_date >= '".$_REQUEST["fdate"]."' AND visit_date <= '".$_REQUEST["tdate"]."'";
}
// echo $sql;
// exit;
$query=mysqli_query($link1, $sql) or die("dealer-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if($_SESSION["userid"]=="admin"){
	$sql = "SELECT *";
	$sql.=" FROM dealer_visit WHERE ".$uid." AND visit_date >= '".$_REQUEST["fdate"]."' AND visit_date <= '".$_REQUEST["tdate"]."'";
}else{
	$sql = "SELECT *";
	$sql.=" FROM dealer_visit WHERE ".$uid." AND visit_date >= '".$_REQUEST["fdate"]."' AND visit_date <= '".$_REQUEST["tdate"]."'";
}
// echo $sql;
// exit;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( id LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR userid LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR visit_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR visit_city LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR dealer_type LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR party_code LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR address LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR location LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR remark LIKE '".$requestData['search']['value']."%' )";
}

$query=mysqli_query($link1, $sql) or die("dealer-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("dealer-grid-data.php: ERROR! 3");

$data = array();
$j=1;

while( $row_dv=mysqli_fetch_array($query) ) {  // preparing an array
	
	$nestedData=array(); 
   
    $cordinate ="".$row_dv["latitude"].", ".$row_dv["longitude"]."";
    $center_loc = $row_dv["latitude"].", ".$row_dv["longitude"];
    $username = mysqli_fetch_assoc(mysqli_query($link1, "SELECT name,oth_empid FROM admin_users WHERE username='".$row_dv['userid']."'"));


    $map = "";
    $map = "<a href='https://www.google.com/maps/dir/".$cordinate."/@".$center_loc.",13z' target='_blank' class='btn ".$btncolor."' title='check on google map'><i class='fa fa-map-marker' title='check on google map'></i></a>";
	
   
	$nestedData[] = $j; 
    $nestedData[] = $username['name']."| ".$row_dv['userid']." |".$username['oth_empid'];
	$nestedData[] = $row_dv['visit_date'];
	$nestedData[] = $row_dv['visit_city'];
	$nestedData[] = $row_dv['dealer_type'];
	$nestedData[] = str_replace("~"," , ",getAnyDetails($row_dv["party_code"],"name,city,state","asc_code","asc_master",$link1))." ".$row_dv["party_code"];
    $nestedData[] = $row_dv['address'];
    $nestedData[] = $row_dv['remark'];
    $nestedData[] = $map;

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
