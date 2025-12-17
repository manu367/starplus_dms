<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
////// filters value/////
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	
}else{
	$team = getTeamMembers($_SESSION['userid'],$link1);
	if($team){
		$team = $team.",'".$_SESSION['userid']."'"; 
	}else{
		$team = "'".$_SESSION['userid']."'"; 
	}
}
$filter_str = "";
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND a.entry_date >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND a.entry_date <= '".$_REQUEST['tdate']."'";
}
/*if($_REQUEST['isp_name'] !=''){
	$filter_str	.= " AND a.userid = '".$_REQUEST['isp_name']."'";
}*/
if($_REQUEST['department']){
	$deptqry = " AND b.department ='".$_REQUEST['department']."'";
}else{
	$deptqry = "";
}
if($_REQUEST['subdepartment']){
	$subdeptqry = " AND b.subdepartment ='".$_REQUEST['subdepartment']."'";
}else{
	$subdeptqry = "";
}
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	if($_REQUEST['isp_name']){
		$team = getTeamMembers($_REQUEST['isp_name'],$link1);
		if($team){
			$team = $team.",'".$_REQUEST['isp_name']."'"; 
		}else{
			$team = "'".$_REQUEST['isp_name']."'"; 
		}
		$filter_str .= " AND a.userid IN (".$team.")";
	}else{
		$filter_str .= " ";
	}
	
	
}else{
	if($_REQUEST['isp_name']){
		$team = getTeamMembers($_REQUEST['isp_name'],$link1);
		if($team){
			$team = $team.",'".$_REQUEST['isp_name']."'"; 
		}else{
			$team = "'".$_REQUEST['isp_name']."'"; 
		}
		$filter_str .= " AND a.userid IN (".$team.")";
	}else{
		$filter_str .= " AND a.userid IN (".$team.")";
	}
}
//////End filters value/////
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'userid',
	2 => 'novisit',
	3 => 'totdist', 
	4 => 'entry_date'	
);

// getting total number records without any search
$sql = "SELECT id";
$sql.=" FROM user_track a, admin_users b WHERE 1=1 AND a.userid=b.username ".$subdeptqry." ".$deptqry." ".$filter_str." GROUP BY a.userid,a.entry_date";
$query=mysqli_query($link1, $sql) or die("trackdistance-grid-data.php: get TD1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT a.userid, a.entry_date, SUM(a.travel_km) AS totdist, COUNT(a.id) AS novisit, b.name, b.oth_empid,b.department";
$sql.=" FROM user_track a, admin_users b WHERE 1=1 AND a.userid=b.username ".$subdeptqry." ".$deptqry." ".$filter_str." GROUP BY a.userid,a.entry_date";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (a.userid LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR b.name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR a.entry_date LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("trackdistance-grid-data.php: get TD2");
$totalFiltered = mysqli_num_rows($query); 
// when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']." ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("trackdistance-grid-data.php: get TD3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
	$gapiicon ="";
	$viewicon ="";
	$gapi_dist = "";
	$viewicon ="<a href='view_distance_covered.php?rb=view&id=".base64_encode($row['userid'])."&travel_date=".base64_encode($row['entry_date'])."&total_distance=".base64_encode($row['totdist'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg' title='view details'></i></a>";
	
	//// check google api is called or not
	$res_gapi = mysqli_query($link1,"SELECT id FROM gapi_request WHERE userid='".$row['userid']."' AND request_date='".$row['entry_date']."'");
	$num_gapi = mysqli_num_rows($res_gapi);
	if($num_gapi>0){
		//// calculate google api distance
		$res_gapi = mysqli_query($link1,"SELECT SUM(distance) AS gapidist FROM google_api_response WHERE `userid` LIKE '".$row['userid']."' AND `entry_date` = '".$row['entry_date']."'");
		$row_gapi = mysqli_fetch_assoc($res_gapi);
		$gapi_dist = round($row_gapi["gapidist"]/1000);
		
		$gapiicon = "&nbsp;&nbsp;<a href='view_gapi_data.php?id=".base64_encode($row['userid'])."&travel_date=".base64_encode($row['entry_date'])."".$pagenav."' title='view google api details'><i class='fa fa-map fa-lg' title='view google api details'></i></a>";
	}else{
		if($row['entry_date']!=$today){
		$gapiicon = "&nbsp;&nbsp;<a href='manual_call_google_api.php?id=".base64_encode($row['userid'])."&travel_date=".base64_encode($row['entry_date'])."".$pagenav."' title='call google api for calculating distance'><i class='fa fa-empire fa-lg' title='call google api for calculating distance'></i></a>";
		}else{
			$gapiicon = "";
		}
	}		
	$nestedData[] = $j; 
	//$nestedData[] = str_replace("~",",",getAdminDetails($row['userid'],"name,username,oth_empid",$link1));
	$nestedData[] = $row['name'].", ".$row['userid'].", ".$row['oth_empid'];
	$nestedData[] = getAnyDetails($row["department"],"dname","departmentid","hrms_department_master",$link1);
	$nestedData[] = $row["novisit"];
	$nestedData[] = $row["totdist"];
	$nestedData[] = $gapi_dist;
	$nestedData[] = $row['entry_date'];
	$nestedData[] = "<div align='center'>".$viewicon." ".$gapiicon."</div>";
		
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
