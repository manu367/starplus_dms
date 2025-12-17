<?php
/* Database connection start */
require_once("../config/config.php");


/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
////// filters value/////
$filter_str = "";
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND DATE(entry_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND DATE(entry_date) <= '".$_REQUEST['tdate']."'";
}

if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	if($_REQUEST['user_id']){
		$team2 = getTeamMembers($_REQUEST['user_id'],$link1);
		if($team2){
			$team2 = $team2.",'".$_REQUEST['user_id']."'"; 
		}else{
			$team2 = "'".$_REQUEST['user_id']."'"; 
		}
		$filter_str .= " AND user_id IN (".$team2.")";
	}else{
		$filter_str .= " ";
	}
}else{
	if($_REQUEST['user_id']){
		$team3 = getTeamMembers($_REQUEST['user_id'],$link1);
		if($team3){
			$team3 = $team3.",'".$_REQUEST['user_id']."'"; 
		}else{
			$team3 = "'".$_REQUEST['user_id']."'"; 
		}
		$filter_str .= " AND user_id IN (".$team3.")";
	}else{
		$filter_str .= " AND user_id IN (".$team.")";
	}
}

$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'ref_no',
	2 => 'activity_type', 
	3 => 'activity_date',
	4 => 'party_name',
    5 => 'status',
	6 => 'user_id'
);
//////

// getting total number records without any search
if($_SESSION['userid']=="admin"){
	$sql = "SELECT id ";
	$sql .= " FROM activity_master where 1 ".$filter_str."";
}else{
	$sql = "SELECT id ";
	$sql .= " FROM activity_master where  ".$filter_str."";
}
// echo $sql;
// exit;
$query=mysqli_query($link1, $sql) or die("user-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if($_SESSION['userid']=="admin"){
	$sql = "SELECT * ";
	$sql .= " FROM activity_master where 1 ".$filter_str."";
}else{
	$sql = "SELECT * ";
	$sql .= " FROM activity_master where  ".$filter_str."";
}

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (ref_no LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR activity_type LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR activity_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR party_name LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR status LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR user_id LIKE '".$requestData['search']['value']."%' )";
}

$query=mysqli_query($link1, $sql) or die("user-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
//echo $sql;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("user-grid-data.php: ERROR! 3");

$data = array();
$j=1;
while ($row = mysqli_fetch_array($query)) { // preparing an array
    $nestedData = array();	

    $view = "";
	$view = "<a href='user_activityview.php?id=".base64_encode($row['id']).$pagenav."' title='view '><i class='fa fa-eye fa-lg' title='View Details'></i></a>";
	
	$update_activity = "";
	$update_activity = "&nbsp;<a href='#' title='Update' onClick=openModelUpd('".base64_encode($row['id'])."');><i class='fa fa-pencil-square-o fa-lg' title='Update Activity'></i></a>";
	
	$claim = "";
	if($row['status']=="Complete"){
		$claim = "&nbsp;<a href='../claim/claim_request.php?op=add&pid=133&hid=FN14' title='Add Claim'><i class='fa fa-clipboard fa-lg' title='Add Claim'></i></a>";
	}

    $imgicon = "";
    if ($row['initial_attach'] != '') {
		$ext = pathinfo($row['initial_attach'], PATHINFO_EXTENSION);
		if($ext=="jpeg" || $ext=="jpg" || $ext=="JPEG" || $ext=="JPG" || $ext=="PNG" || $ext=="png"){
        	$imgicon = '<img src="../salesapi/activityimg/'.substr($row['entry_date'],0,7).'/'.$row['initial_attach'].'" alt="" id="image" " style="width: 50%;"/>';
		}else{
			$imgicon = '<a href="../salesapi/activityimg/'.substr($row['entry_date'],0,7).'/'.$row['initial_attach'].'" target="_blank" alt="" id="atch"><i class="fa fa-file fa-lg" title="View Attachment"></i></a>';
		}
    }else{
        $imgicon = "Not clicked";
    }

	$nestedData[] = $j; 
	$nestedData[] = $row["ref_no"];
	$nestedData[] = $row["activity_type"];
	$nestedData[] = $row["activity_date"];
	$nestedData[] = $row['party_name'];
	$nestedData[] = $row['status'];
    $nestedData[] =  getAdminDetails($row['user_id'],"name",$link1);
	$nestedData[] = $row['activity_action'];
    $nestedData[] = $view."&nbsp;".$update_activity."&nbsp;".$claim;
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