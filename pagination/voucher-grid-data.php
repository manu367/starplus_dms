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
if($_REQUEST['status']!="" && $_REQUEST['status']!="all"){
	$status="status='".$_REQUEST['status']."'";
}else{
	$status="1";
}



$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'voucher_type', 
	2 => 'voucher_for',
	3 => 'nature_of_voucher',
	4 => 'status'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM voucher_master where $status";
$query=mysqli_query($link1, $sql) or die("voucher-grid-data.php: get vc1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM voucher_master where $status ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( voucher_type LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR nature_of_voucher LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR voucher_for LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("voucher-grid-data.php: get vc2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("voucher-grid-data.php: get vc3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    
    ////// check this user have right to view the details
    //if($get_opr_rgts['view']=="Y"){
        $viewicon = "<div align='center'><a href='addvoucher.php?op=edit&id=".$row['id']."&status=".$_REQUEST['status']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit details'></i></a></div>";
    //}else{
        //$viewicon = "";
    //}
     
	$nestedData[] = $j; 
	$nestedData[] = $row["voucher_type"];
	$nestedData[] = $row["voucher_for"];
	$nestedData[] = $row["nature_of_voucher"];
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
