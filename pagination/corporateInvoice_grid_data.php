<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
////// filters value/////
$filter_str = "";
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND entry_date >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND entry_date <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['from_location'] !=''){
	$filter_str	.= " AND from_location = '".$_REQUEST['from_location']."'";
}
if($_REQUEST['to_location'] !=''){
	$filter_str	.= " AND to_location = '".$_REQUEST['to_location']."'";
}
if($_REQUEST['docType'] !=''){
	$filter_str	.= " AND document_type = '".$_REQUEST['docType']."'";
}
if($_REQUEST['status'] !=''){
	if($_REQUEST['status']=="Pending For Serial"){
		$filter_str	.= " AND challan_no IN (SELECT challan_no FROM billing_model_data WHERE imei_attach='' AND prod_code IN (SELECT productcode FROM product_master WHERE is_serialize='Y'))";
	}else{
		$filter_str	.= " AND status = '".$_REQUEST['status']."'";
	}
}
$accesslocation=getAccessLocation($_SESSION['userid'],$link1);

$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => '',
	2 => 'from_location', 
	3 => 'to_location',
	4 => 'challan_no',
	5 => 'sale_date',
	6 => '',
	7 => 'po_no',	
    8 => 'status',
    9 => ''
);
//////
$cancel_right=mysqli_num_rows(mysqli_query($link1,"select id from access_ops_rights where  uid='".$_SESSION['userid']."' and status='Y' and ops_name='CANCEL' AND ops_id='5'"));
// getting total number records without any search
if($_SESSION['userid']=="admin"){
	$sql = "SELECT id ";
	$sql .= " FROM billing_master WHERE 1 AND type NOT IN ('RETAIL','GRN','DIRECT SALE RETURN','LP','CLP') ".$filter_str." AND document_type NOT IN ('PRN')";
}else{
	$sql = "SELECT id ";
	$sql .= " FROM billing_master WHERE from_location in (".$accesslocation.") AND type NOT IN ('RETAIL','GRN','DIRECT SALE RETURN','LP','CLP') ".$filter_str." AND document_type NOT IN ('PRN')";
}
// echo $sql;
// exit;
$query=mysqli_query($link1, $sql) or die("invoice-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if($_SESSION['userid']=="admin"){
	$sql = "SELECT * ";
	$sql .= " FROM billing_master WHERE 1 AND type NOT IN ('RETAIL','GRN','DIRECT SALE RETURN','LP','CLP') ".$filter_str." AND document_type NOT IN ('PRN')";
}else{
	$sql = "SELECT * ";
	$sql .= " FROM billing_master WHERE from_location in (".$accesslocation.") AND type NOT IN ('RETAIL','GRN','DIRECT SALE RETURN','LP','CLP') ".$filter_str." AND document_type NOT IN ('PRN')";
}

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (from_location LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR to_location LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR challan_no LIKE '%".$requestData['search']['value']."%'";
	$sql.=" OR sale_date LIKE '".$requestData['search']['value']."%'";
	// $sql.=" OR  LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR po_no LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
    // $sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}

$query=mysqli_query($link1, $sql) or die("invoice-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
//echo $sql;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("invoice-grid-data.php: ERROR! 3");

$data = array();
$j=1;
while ($row = mysqli_fetch_array($query)) { // preparing an array
	$nestedData = array();
	
	//// check serial no. is uploaded or not
	$rs12=mysqli_query($link1,"SELECT imei_attach, prod_code FROM billing_model_data WHERE challan_no='".$row['challan_no']."'");
	$check=1;
	while($row12=mysqli_fetch_array($rs12)){
		$get_result12 = explode("~",getAnyDetails($row12['prod_code'],"productcode,is_serialize","productcode" ,"product_master",$link1));
		if($get_result12[1]=='Y'){ if($row12['imei_attach']=="Y"){ $check*=1;}else{ $check*=0;}}else{ $check*=1;}
	}
	$pend_aging="";
	if($row['status']=="Pending"){$pend_aging = daysDifference($today,$row['sale_date']);}else{ $pend_aging="";}
	////
	$tallysync= "";
	if($row['post_in_tally']=="Y"){
		$tallysync = '<i class="fa fa-refresh fa-lg text-success" aria-hidden="true" title="sync in tally"></i>';
	}else{
		$tallysync = '<i class="fa fa-ban fa-lg  text-danger" aria-hidden="true" title="not sync in tally"></i>';
	}
	$status="";
	if($row['status']=="PFA"){ $status = "<span class='red_small'>".$row['status']."</span>";}else{ $status = $row['status'];}
	
	$scan="";
	if($row['status']!="Cancelled" && $row['status']!="Rejected"){
		if($row['imei_attach']==""){ 
			if($check==0){
				$scan = "<a href='invoiceUploadImei.php?challan_no=".$row['challan_no'].$pagenav."' title='".$imeitag."Attach'><i class='fa fa-upload fa-lg'></i></a> &nbsp;&nbsp;&nbsp;<a href='invoiceScanImei.php?id=".base64_encode($row['challan_no'])."&invdate=".base64_encode($row['sale_date'])."&invloc=".base64_encode($row['from_location'])."&invto=".base64_encode($row['to_location']).$pagenav."' title='".$imeitag."Scan'><i class='fa fa-qrcode fa-lg'></i></a>";
			}else{ 
				$scan = "Not Applicable";
			}
		}else{ 
			$scan = "YES";
		}
	}
	
	$print = "";
	if($check==1 || $row['status']=="Cancelled"){
		if($row['billing_type']=="COMBO"){
			$print = "<a href='../print/print_combo_invoice.php?rb=view&id=".base64_encode($row['challan_no']).$pagenav."' target='_blank' title='Print Invoice'><i class='fa fa-print fa-lg' title='Print Invoice'></i></a>";
		}else{
			$print = "<a href='../print/print_invoice.php?rb=view&id=".base64_encode($row['challan_no']).$pagenav."' target='_blank' title='Print Invoice'><i class='fa fa-print fa-lg' title='Print Invoice'></i></a>";
		}
		if($row['imei_attach']){
			$print .= "&nbsp;&nbsp;<a href='../print/print_imei.php?rb=view&id=".base64_encode($row['challan_no']).$pagenav."' target='_blank' title='Print".$imeitag."'><i class='fa fa-print fa-lg' title='Print".$imeitag."'></i></a>";
		}
	}else{ 
		$print = "<b style='color:#F30'>Please Upload Serial No. for Serialized Product</b>";
	}
	
	$view = "";
	$view = "<a href='invoiceDetailsN.php?id=".base64_encode($row['challan_no']).$pagenav."' title='Invoice Details'><i class='fa fa-eye fa-lg' title='Invoice Details'></i></a>";
	
	$cancel="";
	if($cancel_right > 0){ 
		if(($row['status']=="Pending") || ($row['status']=="Dispatched")){
			$cancel = "<a href='cancelCorporateInvoiceN.php?id=".base64_encode($row['challan_no']).$pagenav."' title='Cancel Invoice'><i class='fa fa-remove fa-lg' title='Cancel Invoice'></i></a>";
		}
	}
	if(($row['status']=="Pending") || ($row['status']=="Dispatched") && $row['document_type']=="INVOICE"){
		if(daysDifference($today,$row['sale_date'])>1){
			$cancel .= "&nbsp;&nbsp;<a href='generateCN_Corporate.php?id=".base64_encode($row['challan_no']).$pagenav."' title='Sale Return'><i class='fa fa-reply-all fa-lg' title='Sale Return'></i></a>";
		}
	}
	
	$nestedData[] = $j;
	$nestedData[] = $tallysync;
	$nestedData[] = getLocationDetails($row['from_location'], "name,city,state,asc_code", $link1);
	$nestedData[] = getLocationDetails($row['to_location'], "name,city,state,asc_code", $link1);
	$nestedData[] = $row["challan_no"];
	$nestedData[] = $row["sale_date"];
	$nestedData[] = $pend_aging;
	$nestedData[] = $row["po_no"];
	$nestedData[] = $status;
	$nestedData[] = $scan;
	$nestedData[] = $print;
	$nestedData[] = $view;
	$nestedData[] = $cancel;
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