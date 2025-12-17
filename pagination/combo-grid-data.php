<?php
/* Database connection start */
require_once("../config/config.php");
//$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);
/////////check approval is applicable or not
//$apply_app = getAnyDetails(base64_decode($_REQUEST['pid']),"apply_approval","tabid","tab_master",$link1);
/////get status//
//$arrstatus = getFullStatus("",$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Status
if(!empty($_REQUEST['status'])){
	$status="status='".$_REQUEST['status']."'";
}else{
	$status="1";
}
## selected  BOM Model
if(!empty($_REQUEST['bom_model'])){
	$bommodel="bom_modelcode='".$_REQUEST['bom_model']."'";
}else{
	$bommodel="1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'bom_modelname',
	2 => 'bom_modelcode',
	3 => 'bom_hsn', 
	4 => 'status'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM combo_master WHERE ".$status." AND ".$bommodel." GROUP BY bomid";
$query=mysqli_query($link1, $sql) or die("combo-grid-data.php: get combo master1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM combo_master WHERE ".$status." AND ".$bommodel;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (bom_modelname LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR bom_modelcode LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR bom_hsn LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$sql.=" GROUP BY bomid";
$query=mysqli_query($link1, $sql) or die("combo-grid-data.php: get bom master2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("combo-grid-data.php: get combo master3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
	
	////// check this user have right to view the details
    //if($get_opr_rgts['view']=="Y"){
        $viewicon = "<div align='center'><a href='view_combo.php?id=".base64_encode($row['bomid'])."&status=".$_REQUEST['status']."&bom_model=".$_REQUEST['bom_model']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view combo details'></i></a></div>";
	
    //}else{
        //$viewicon = "";
    //}
    if($row["status"] == 1){ $sts = "Active";}else{ $sts = "Deactive";}
	$nestedData[] = $j; 
	$nestedData[] = $row["bom_modelname"];
	$nestedData[] = $row["bom_modelcode"];
	$nestedData[] = $row["bom_hsn"];
	$nestedData[] = $sts;
	$nestedData[] = $viewicon;
	////// check if approval is applicable or not
	if($apply_app=="Y"){
		if($get_opr_rgts["approval"]=="Y"){
			if($row['status']==3){
				$nestedData[] = "<div align='center'><a href='approval_combo.php?id=".base64_encode($row['bomid'])."&status=".$_REQUEST['status']."&bom_model=".$_REQUEST['bom_model']."".$pagenav."' title='Take Action'><i class='fa fa-legal fa-lg faicon' title='Take Action'></i></a></div>";
			}else{
				$actiontaken = getAnyDetails($row["bomid"],"action_taken","ref_no","approval_activity",$link1);
				$nestedData[] = $arrstatus[$actiontaken];
			}
		}else{
			$nestedData[] = "Not Authorized";
		}
	}
	////// check if print right is assigned
	//if($get_opr_rgts["print"]=="Y"){
		$nestedData[] = "<div align='center'><a href='combo_print.php?id=".base64_encode($row['bomid'])."&status=".$_REQUEST['status']."&bom_model=".$_REQUEST['bom_model']."".$pagenav."' target='_blank' title='Print'><i class='fa fa-print fa-lg faicon' title='Print'></i></a></div>";
	//}else{
		//$nestedData[] = "";
	//}
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
