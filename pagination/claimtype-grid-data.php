<?php
/* Database connection start */
require_once("../config/config.php");
//$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);
/////get status//
//$arrstatus = getFullStatus("master",$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Status
if($_REQUEST['status']!=""){
	$status="status='".$_REQUEST['status']."'";
}else{
	$status="1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'id', 
	1 => 'claim_type',
	2 => 'status'
);

// getting total number records without any search
$sql = "SELECT id";
$sql.=" FROM claim_type_master where ".$status." ";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("claimtype-grid-data.php: get claimtype master1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM claim_type_master where ".$status." ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( claim_type LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
	$query=mysqli_query($link1, $sql) or die("claimtype-grid-data.php: get claimtype master2");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result.
}
 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("claimtype-grid-data.php: get claimtype master3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
	
	////// check this user have right to view the details
    //if($get_opr_rgts['view']=="Y"){
        $viewicon = "<div align='center'><a href='add_claim_type.php?op=Edit&id=".$row['id']."&status=".$_REQUEST['status'].$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit claim type details'></i></a></div>";
	
    //}else{
       // $viewicon = "";
   // }
	if($row["status"]=="1"){ $sts="Active";}else if($row["status"]=="2"){ $sts="Deactive";}else{$sts=$row["status"];}
     
	$nestedData[] = $j; 
	$nestedData[] = $row["claim_type"];
	$nestedData[] = $sts;
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
