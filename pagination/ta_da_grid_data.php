<?php
/* Database connection start */
require_once("../config/config.php");

/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

////// filters value/////
$filter_str = 1;
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " and expense_date >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " and expense_date <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['user_id'] !=''){
	$filter_str	.= " and userid = '".$_REQUEST['user_id']."'";
}
if($_REQUEST['status'] !=''){
	$filter_str	.= " and status = '".$_REQUEST['status']."'";
}
//////End filters value/////

$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'system_ref_no', 
	2 => 'expense_date',
	3 => 'entry_date',
	4 => 'total_amt',
	5 => 'status'	
);

// getting total number records without any search
if($_SESSION["userid"]=="admin"){
	$sql = "SELECT id";
	$sql.=" FROM ta_da WHERE ".$filter_str." ";
}else{
	$sql = "SELECT id";
	$sql.=" FROM ta_da WHERE ".$filter_str."";
}
// echo $sql;
// exit;
$query=mysqli_query($link1, $sql) or die("ta-da-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if($_SESSION["userid"]=="admin"){
	$sql = "SELECT *";
	$sql.=" FROM ta_da where ".$filter_str." ";
}else{
	$sql = "SELECT *";
	$sql.=" FROM ta_da where ".$filter_str." ";
}
// echo $sql;
// exit;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( id LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR system_ref_no LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR expense_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR entry_date LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR total_amt LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}

$query=mysqli_query($link1, $sql) or die("ta-da-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("ta-da-grid-data.php: ERROR! 3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    $userdet = explode("~",getAdminDetails($row['userid'],"name,oth_empid",$link1));

//status//
    $status ="";
    if($row['status']=="Pending"){ 
     $status = "<span class='red_small'>".$row['status']."</span>";
     }else{
     $status = $row['status'];
     }
//status//

//view//
    $view = "";
	$view = "<a href='tadaApprovalPage.php?id=".base64_encode($row['system_ref_no']).$pagenav."' title='view '><i class='fa fa-eye fa-lg' title='view Details'></i></a>";
//view//

	$nestedData[] = $j; 
	$nestedData[] = $userdet[0]." (".$row['userid'].")";
	$nestedData[] = $userdet[1];
	$nestedData[] = $row['system_ref_no'];
	$nestedData[] = $row['expense_date'];
	$nestedData[] = $row['entry_date']." ".$row['entry_time'];
	$nestedData[] = $row['total_amt'];
	$nestedData[] = $status;
    $nestedData[] = $view;

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
