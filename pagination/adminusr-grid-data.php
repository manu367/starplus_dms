<?php
/* Database connection start */
require_once("../config/config.php");
/////get status//
//$arrstatus = getFullStatus("master",$link1);
///// get operation rights
//$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);
///// get access location ///
$accessLocation=getAccessLocation($_SESSION['userid'],$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Status
if(!empty($_REQUEST['status'])){
	$status="status='".$_REQUEST['status']."'";
}else{
	$status="1";
}
if(!empty($_REQUEST['u_type'])){
	$utype=" AND utype='".$_REQUEST['u_type']."'";
}else{
	$utype="";
}
if($_SESSION['userid']=="admin"){
	$checkmainadmin = "";
}else{
	$checkmainadmin = " AND username!='admin'";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'uid',
	1 => 'username', 
	2 => 'oth_empid',
	3 => 'name',
	4 => 'utype',
	5 => 'phone',
	6 => 'emailid',
	7 => 'status',
	8 => 'create_by',
	9 => 'createdate'
);

// getting total number records without any search
if($_SESSION["userid"]=="admin" || $_SESSION["utype"]=="1"){
	$sql = "SELECT uid";
	$sql.=" FROM admin_users WHERE ".$status." ".$utype." ".$checkmainadmin."";
}else{
	$sql = "SELECT uid";
	$sql.=" FROM admin_users WHERE ".$status." ".$utype." ".$checkmainadmin." AND owner_code IN (".$accessLocation.")";
}
$query=mysqli_query($link1, $sql) or die("admin-grid-data.php: get admin users1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if($_SESSION["userid"]=="admin" || $_SESSION["utype"]=="1"){
	$sql = "SELECT uid, username, oth_empid, name, utype, phone, emailid, status,app_logout,create_by,createdate";
	$sql.=" FROM admin_users WHERE ".$status." ".$utype." ".$checkmainadmin."";
}else{
	$sql = "SELECT uid, username, oth_empid, name, utype, phone, emailid, status,app_logout,create_by,createdate";
	$sql.=" FROM admin_users WHERE ".$status." ".$utype." ".$checkmainadmin." AND owner_code IN (".$accessLocation.")";
}
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( username LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR oth_empid LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR utype LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR phone LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR emailid LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR create_by LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR createdate LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("admin-grid-data.php: get admin users2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("admin-grid-data.php: get admin users3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    
    ////// check this user have right to view the details
    //if($get_opr_rgts['view']=="Y"){
        $viewicon = "<div align='center'><a href='addAdminUser.php?op=edit&id=".base64_encode($row['username'])."&status=".$_REQUEST['status']."&u_type=".$_REQUEST['u_type']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit details'></i></a></div>";
		
    //}else{
        //$viewicon = "";
    //}
	$histyicon = "<div align='center'><a href='emp_history.php?op=edit&empcode=".base64_encode($row['username'])."&status=".$_REQUEST['status']."&u_type=".$_REQUEST['u_type']."".$pagenav."' title='view history'><i class='fa fa-history fa-lg faicon' title='view history'></i></a></div>";
	
	$applogouticon = '<div align="center"><input type="checkbox" class="togg" id="app_logout'.$row['uid'].'" name="app_logout'.$row['uid'].'"';
					 if($row['app_logout']=="1"){
	$applogouticon .= ' checked ';
					} 
    $applogouticon .= 'onChange=appLogout("'.$row['uid'].'")><span id="'.$row['uid'].'"></span></div>';
     
	$nestedData[] = $j; 
	$nestedData[] = $row["username"];
	$nestedData[] = $row["oth_empid"];
	$nestedData[] = $row["name"];
	$nestedData[] = gettypeName($row['utype'],$link1);
	$nestedData[] = $row["phone"];
	$nestedData[] = $row["emailid"];
	$nestedData[] = $row["status"];
	if($row["create_by"]){
		$nestedData[] = getAdminDetails($row["create_by"],"name",$link1).",".$row["create_by"];
		$nestedData[] = $row["createdate"];
	}else{
		$credet = mysqli_fetch_assoc(mysqli_query($link1,"SELECT userid,update_on FROM `daily_activities` WHERE `ref_no` LIKE '".$row["username"]."' AND action_taken='ADD'"));
		if($credet["userid"]){
			$nestedData[] = getAdminDetails($credet["userid"],"name",$link1).",".$credet["userid"];
			$nestedData[] = $credet["update_on"];
		}else{
			$nestedData[] = "Backend";
			$nestedData[] = $row["createdate"];
		}
	}
	$nestedData[] = $viewicon;
	$nestedData[] = $histyicon;
	$nestedData[] = $applogouticon;
	
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
