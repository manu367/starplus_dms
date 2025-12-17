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
//Invoice Status filter
#### seleted location code 
//$first_dt = "2020-01-01";
///Getting value from calender////

//// Apply Filter
///Getting value from calender////
if($_REQUEST['from_date']!=''){
	$str_filter="(create_date BETWEEN '$_REQUEST[from_date]' AND '$_REQUEST[to_date]') ";
}else{
	$str_filter="(create_date BETWEEN '$first_dt' AND '$today') ";
}
/// select status filter//
if($_REQUEST['status']!='all' && $_REQUEST['status']!=''){
	$status="status='$_REQUEST[status]'";
}else{
	$status="1";
}
if($_REQUEST['location_code']!='' && $_REQUEST['location_code']!='all'){
	$location_code=" and location_id='$_REQUEST[location_code]'";
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
$sql.=" FROM debit_note where $str_filter and $status and create_by = '".$_SESSION['userid']."' ";

$query=mysqli_query($link1, $sql) or die("debitnotelist-grid-data.php: get  users1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM debit_note where $str_filter and $status and create_by = '".$_SESSION['userid']."' ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( cust_id LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR ref_no LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR location_id LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR amount LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR create_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("debitnotelist-grid-data.php: get  users2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("debitnotelist-grid-data.php: get  users3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
      
		   
	    $nestedData=array(); 
		if($row['app_status']!='Approved' && $row['status']!='Cancelled' && $row['app_status']!='Rejected'){ $status="Pending For Approval"; } else{ if($row['status']=='Cancelled') { $status =  "Cancelled"; }else { $status =  $row['app_status'];}}
		
		//// check serial no. is uploaded or not
		##########################################################################################################################3
		 $viewicon = "<div align='center'><a href='view_dr_notes.php?ref_no=".base64_encode($row['ref_no'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view details'></i></a></div>";
		
		#############3cancel invoice#############################3
		 if($row['status']!='Cancelled'){ 
                   $cancel = "<div align='center'><a href='cancel_dr_notes.php?ref_no=".base64_encode($row['ref_no'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view details'></i></a></div>";
           }

       
		
		###############3  print of invoice ##################
		$print = "<div align='center'><a href='#' onClick=javascript:window.open('../print/print_debit_note.php?ref_no=".base64_encode($row['ref_no'])."','PrintInvoice','toolbar=no,status=no,resizable=yes,scrollbars=yes,width=860,height=700,top=50,left=350')><i class='fa fa-print fa-lg faicon' title='view details'></i></a></div>";

		#############

		$nestedData[] = $j; 
		$nestedData[] = getAnyParty($row["cust_id"],$link1);
		$nestedData[] = getAnyParty($row["location_id"],$link1);
	    $nestedData[] = $row["create_date"];
		$nestedData[] = $row["ref_no"];
		$nestedData[] = currencyFormat($row['amount']);
		$nestedData[] = $status;
	    $nestedData[] = $print;
		$nestedData[] = $viewicon;
		//$nestedData[] = $cancel;
	
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
