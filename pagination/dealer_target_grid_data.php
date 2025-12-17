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
if($_SESSION['userid']=="admin"){
	if($_REQUEST['user_id']){
		$team2 = getTeamMembers($_REQUEST['user_id'],$link1);
		if($team2){
			$team2 = $team2.",'".$_REQUEST['user_id']."'"; 
		}else{
			$team2 = "'".$_REQUEST['user_id']."'"; 
		}
		$filter_str	.= " AND user_id IN (".$team2.")";
	}else{
		$filter_str	.= " ";
	}
}else{
	if($_REQUEST['user_id']){
		$team3 = getTeamMembers($_REQUEST['user_id'],$link1);
		if($team3){
			$team3 = $team2.",'".$_REQUEST['user_id']."'"; 
		}else{
			$team3 = "'".$_REQUEST['user_id']."'"; 
		}
		$filter_str	.= " AND user_id IN (".$team3.")";
	}else{
		$filter_str	.= " AND user_id IN (".$team.")";
	}
}
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'target_no', 
	2 => 'user_id',
	3 => 'month',
	4 => 'year',
	5 => 'party_code',
	6 => 'status'	
);

// getting total number records without any search
if($_SESSION["userid"]=="admin"){
	$sql = "SELECT id";
	$sql.=" FROM dealer_target WHERE 1 ".$filter_str." ";
}else{
	$sql = "SELECT id";
	$sql.=" FROM dealer_target WHERE 1 ".$filter_str."";
}
// echo $sql;
// exit;
$query=mysqli_query($link1, $sql) or die("target-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if($_SESSION["userid"]=="admin"){
	$sql = "SELECT id, target_no,user_id,month, year, party_code,status";
	$sql.=" FROM dealer_target WHERE 1 ".$filter_str." ";
}else{
	$sql = "SELECT id, target_no,user_id, month, year, party_code, status";
	$sql.=" FROM dealer_target WHERE 1 ".$filter_str."";
}
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( target_no LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR user_id LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR month LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR year LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR party_code LIKE '".$requestData['search']['value']."%'";
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
    
        $viewicon="";
        $viewicon = "<div align='center'><a href='dealer_target_view.php?op=edit&id=".base64_encode($row['target_no'])."&user_id=".$_REQUEST['user_id']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit details'></i></a></div>";
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
	else{ $mnth = $row['month'];}

	$nestedData[] = $j; 
	$nestedData[] = $row["target_no"];
	$nestedData[] = getAdminDetails($row['user_id'],"name,username,oth_empid",$link1);
	$nestedData[] = $mnth;
	$nestedData[] = $row["year"];
    $nestedData[] = getAnyDetails($row["party_code"],"name,city,state","asc_code","asc_master",$link1);
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
