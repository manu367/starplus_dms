<?php
/* Database connection start */
require_once("../config/config.php");
/////get status//
//$arrstatus = getFullStatus("master",$link1);
///// get operation rights
//$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Status
if(!empty($_REQUEST['status'])){
	$status=" (status='$_REQUEST[status]' or app_status='$_REQUEST[status]')";
}else{
	$status="  1";
}
#### seleted location code 
///Getting value from calender////
if($_REQUEST['fromdate']!=''){
	$str_filter=" and (create_date BETWEEN '$_REQUEST[fromdate]' AND '$_REQUEST[todate]') ";
}else{
	$str_filter=" and (create_date BETWEEN '$today' AND '$today') ";
}
///select Location filter
////// filter////
if($_REQUEST['location_code']!='' && $_REQUEST['location_code']!='all'){
    $location_code=" and location_id='".$_REQUEST['location_code']."'";
}else{
	$location_code=" and location_id in (select location_id from access_location where uid='$_SESSION[userid]' and status='Y')";
}

$columns = array( 
// datatable column index  => database column name
	0 => 'sno',
	1 => 'cust_id', 
	2 => 'location_id',
	3 => 'create_date',
	4 => 'ref_no',
	5 => 'amount',
	6 => 'status'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM credit_note  where $status $str_filter $location_code   and status='Pending For Approval' ";
$query=mysqli_query($link1, $sql) or die("creditnoteapprv-grid-data.php: get  users1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM  credit_note  where  $status $str_filter $location_code   and status='Pending For Approval'  ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( cust_id LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR location_id LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR ref_no LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR amount LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR create_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}

$query=mysqli_query($link1, $sql) or die("creditnoteapprv-grid-data.php: get  users2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
	
$query=mysqli_query($link1, $sql) or die("creditnoteapprv-grid-data.php: get  users3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
		   
	    $nestedData=array(); 
		########status condition ###############################3
		if($row['app_status']!='Approved' && $row['status']!='Cancelled' && $row['app_status']!='Rejected'){
			$status =  $row['status']; } else{ if($row[status]=='Cancelled'){ $status = "Cancelled"; } else{ $status =  $row['app_status'];}}
		
        $viewicon = "<div align='center'><a href='view_App_Credit_note.php?ref_no=".$row['ref_no']."&from_date=".$_REQUEST['fromdate']."&to_date=".$_REQUEST['todate']."&location_code=".$_REQUEST['location_code']."&status=".$_REQUEST['status']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view details'></i></a></div>";
		
     
		$nestedData[] = $j; 
		$nestedData[] = getAnyParty($row["cust_id"],$link1);
		$nestedData[] = getAnyParty($row["location_id"],$link1);
		$nestedData[] = $row["create_date"];
		$nestedData[] = $row["ref_no"];	
		$nestedData[] = $row["amount"];	
		$nestedData[] = $status;		
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
