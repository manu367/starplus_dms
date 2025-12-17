<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
////// filters value/////
$filter_str = "";
if($_REQUEST['selyear'] !=''){
	$filter_str	.= " AND budget_year = '".$_REQUEST['selyear']."'";
}
if($_REQUEST['selmonth'] !=''){
	$mnth = date("m", strtotime($_REQUEST["selmonth"]."-".$_REQUEST["selyear"]));
	$filter_str	.= " AND month = '".$mnth."'";
}

if($_REQUEST['party_code'] !=""){
	$filter_str	.=" AND party_id='".$_REQUEST['party_code']."'";
}else{
	$filter_str	.=" AND party_id IN (".$accesslocation.")";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'party_name', 
	2 => 'claim_type',
	3 => 'budget_year',
	4 => 'budget_yearly',
	5 => 'budget_monthly',
	6 => 'man_power',
	7 => 'status'	
);

// getting total number records without any search

$sql = "SELECT id";
$sql.=" FROM claim_budget WHERE 1 ".$filter_str."";

$query=mysqli_query($link1, $sql) or die("claimbgt-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT id, party_name,claim_type, budget_year, budget_yearly, budget_monthly, man_power, status";
$sql.=" FROM claim_budget WHERE 1 ".$filter_str."";

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( party_name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR claim_type LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR budget_year LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR budget_yearly LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR budget_monthly LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
	$query=mysqli_query($link1, $sql) or die("claimbgt-grid-data.php: ERROR! 2");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
}
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("claimbgt-grid-data.php: ERROR! 3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	
	$nestedData=array(); 

    $viewicon = "<div align='center'><a href='claim_budget_view.php?id=".base64_encode($row['id'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view details'></i></a></div>";
	$mnth = "";
	if($row['month'] == '01'){ $mnth = "JAN"; }
	else if($row['month'] == '02'){ $mnth = "FEB"; }
	else if($row['month'] == '03'){ $mnth = "MAR"; }
	else if($row['month'] == '04'){ $mnth = "APR"; }
	else if($row['month'] == '05'){ $mnth = "MAY"; }
	else if($row['month'] == '06'){ $mnth = "JUN"; }
	else if($row['month'] == '07'){ $mnth = "JUL"; }
	else if($row['month'] == '08'){ $mnth = "AUG"; }
	else if($row['month'] == '09'){ $mnth = "SEP"; }
	else if($row['month'] == '10'){ $mnth = "OCT"; }
	else if($row['month'] == '11'){ $mnth = "NOV"; }
	else if($row['month'] == '12'){ $mnth = "DEC"; }
	else{}
	
	if($row["status"]==1){ $sts="Active";}else if($row["status"]==2){ $sts="Deactive";}else{$sts=$row["status"];}
	
	$nestedData[] = $j; 
	$nestedData[] = $row["party_name"];
	$nestedData[] = $row['claim_type'];
	$nestedData[] = $row['budget_year'];
	$nestedData[] = $row["budget_yearly"];
	$nestedData[] = $row["budget_monthly"];
	$nestedData[] = $row["man_power"];
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