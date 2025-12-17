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
	1 => 'scheme_code',
	2 => 'scheme_name',
	3 => 'scheme_description',
	4 => 'valid_from',
	5 => 'valid_to',
	6 => 'status'
);

// getting total number records without any search
$sql = "SELECT id";
$sql.=" FROM reward_scheme_master WHERE ".$status;
$query=mysqli_query($link1, $sql) or die("sch-grid-data.php: get sch1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM reward_scheme_master WHERE ".$status;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( scheme_name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR scheme_description LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR valid_from LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR valid_to LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
	
	$query=mysqli_query($link1, $sql) or die("sch-grid-data.php: get sch2");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
}
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("sch-grid-data.php: get sch3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    ////// check this user have right to view the details
    //if($get_opr_rgts['view']=="Y"){
        $viewicon = "<div align='center'><a href='edit_reward_sch.php?op=edit&id=".base64_encode($row['id'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit details'></i></a></div>";
    //}else{
        //$viewicon = "";
    //}
	$mapicon = "<div align='center'><a href='scheme_mapping.php?id=".base64_encode($row['id'])."".$pagenav."' title='Map scheme'><i class='fa fa-sitemap fa-lg' title='Map scheme'></i></a></div>";
	
	$nestedData[] = $j;
	$nestedData[] = $row["scheme_code"];
	$nestedData[] = $row["scheme_name"];
	$nestedData[] = $row["scheme_description"];
	$nestedData[] = $row["valid_from"];
	$nestedData[] = $row["valid_to"];
	$nestedData[] = $row["status"];
	if($row['attachment']){
		$nestedData[] = "<div align='center'><a href='".$row['attachment']."' target='_blank' title='Download Attachment'><i class='fa fa-download fa-lg faicon' title='Download Attachment'></i></a></div>";
	}else{
		$nestedData[] = "Not Attached";
	}
	$nestedData[] = $mapicon;
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
