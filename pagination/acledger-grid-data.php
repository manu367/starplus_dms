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

## selected  Status
if($_REQUEST['achead']!="" && $_REQUEST['achead']!="all"){	
	$achead="ac_head_id='".$_REQUEST['achead']."'";
}else{
	$achead="1";
}
## selected  Status
if($_REQUEST['acgroup']!="" && $_REQUEST['acgroup']!="all"){	
	$acgroup="group_id='".$_REQUEST['acgroup']."'";
}else{
	$acgroup="1";
}


$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'ledger_name', 
	2 => 'ac_head_name',
	3 => 'ac_group_name',
	4 => 'status'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM account_ledger WHERE ".$status." AND ".$achead." AND ".$acgroup;
$query=mysqli_query($link1, $sql) or die("acledger-grid-data.php: get account ledger1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM account_ledger WHERE ".$status." AND ".$achead." AND ".$acgroup;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( ledger_name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR ac_head_name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR ac_group_name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("acledger-grid-data.php: get account ledger2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("acledger-grid-data.php: get account ledger3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    
    ////// check this user have right to view the details
    //if($get_opr_rgts['view']=="Y"){
        $viewicon = "<div align='center'><a href='add_account_ledger.php?op=edit&id=".$row['id']."&status=".$_REQUEST['status']."&achead=".$_REQUEST['achead']."&acgroup=".$_REQUEST['acgroup']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit details'></i></a></div>";
    //}else{
        //$viewicon = "";
    //}
     
	$nestedData[] = $j; 
	$nestedData[] = $row["ledger_name"];
	$nestedData[] = $row["ac_head_name"];
	$nestedData[] = $row["ac_group_name"];
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
