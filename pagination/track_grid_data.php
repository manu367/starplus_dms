<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$fromd = date_format(date_create($_REQUEST['fdate']), "Y-m-d");
$tod = date_format(date_create($_REQUEST['tdate']), "Y-m-d");
////// filters value/////
$date=date("Y-m-d");
if($_REQUEST['fdate'] !='')
{
	$fdate = $_REQUEST['fdate'];
}
if($_REQUEST['tdate'] !='')
{
	$tdate = $_REQUEST['tdate'];
}

if($_REQUEST["isp_name"]){ 
    $uid = "userid ='".$_REQUEST["isp_name"]."'";}
    else{ $uid = "1";}

//////End filters value/////
$accesslocation=getAccessLocation($_SESSION['userid'],$link1);

$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'userid',
	2 => 'address', 
	3 => 'entry_date',
	4 => 'update_date'
);

// getting total number records without any search
if($_SESSION['userid']=="admin"){
	$sql = "SELECT distinct(userid), address,update_date,entry_date,latitude,longitude ";
	$sql .= " FROM user_track WHERE ".$uid." and  entry_date BETWEEN '" . $fromd . "' and '" . $tod . "'";
}else{
	$sql = "SELECT distinct(userid), address,update_date,entry_date,latitude,longitude";
	$sql .= " FROM user_track WHERE ".$uid." and entry_date BETWEEN '" . $fromd . "' and '" . $tod . "'";
}

// echo $sql;
// exit;
$query=mysqli_query($link1, $sql) or die("track-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if($_SESSION['userid']=="admin"){
	$sql = "SELECT distinct(userid), address,update_date,entry_date,latitude,longitude  ";
	$sql .= " FROM user_track WHERE ".$uid." and  entry_date BETWEEN '" . $fromd . "' and '" . $tod . "'";
}else{
	$sql = "SELECT distinct(userid), address,update_date,entry_date,latitude,longitude ";
	$sql .= " FROM user_track WHERE ".$uid."  and entry_date BETWEEN '" . $fromd . "' and '" . $tod . "'";
}


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (id LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR userid LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR address LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR entry_date LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR update_date LIKE '".$requestData['search']['value']."%' )";
}

$query=mysqli_query($link1, $sql) or die("track-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
//echo $sql;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("track-grid-data.php: ERROR! 3");

$data = array();
$j=1;
while ($row = mysqli_fetch_array($query)) { // preparing an array
	$nestedData = array();
	
    if($isp_name!="" ){
        //$sql1 = "SELECT distinct(eng_id), address,update_date,travel_date FROM lead_user_track where eng_id='$isp_name'  and travel_date BETWEEN '" . $fromd . "' and '" . $tod . "' order by travel_date,travel_time desc";
             $sql1 = "SELECT distinct(userid), address,update_date,entry_date,latitude,longitude FROM user_track where userid='$isp_name'  and entry_date BETWEEN '" . $fromd . "' and '" . $tod . "' ";
        }else{
         //$sql1 = "SELECT distinct(eng_id), address,update_date,travel_date FROM lead_user_track where travel_date BETWEEN '" . $fromd . "' and '" . $tod . "' order by travel_date,travel_time desc ";
             $sql1 = "SELECT distinct(userid), address,update_date,entry_date,latitude,longitude FROM user_track where entry_date BETWEEN '" . $fromd . "' and '" . $tod . "' ";
            }
       $datetime = $row['update_date'];
       $t = explode(" ",$datetime);
     
	$nestedData[] = $j;
    $nestedData[] = $row['userid'];
	$nestedData[] = "latitude->".$row['latitude']." , longitude->".$row['longitude']."<br/>".$row['address'];
    $nestedData[] = $t[0];
    $nestedData[] = $t[1];

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