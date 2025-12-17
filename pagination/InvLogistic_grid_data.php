<?php
/* Database connection start */
require_once("../config/config.php");
$requestData= $_REQUEST;

// FILTER //
$filter_str = "";
if($_REQUEST['fdate']=="" && $_REQUEST['tdate']==""){ $filter_str	.= " AND entry_date = '".$today."'";}
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
	$filter_str	.= " AND status = '".$_REQUEST['status']."'";
}
/* Database connection end */
$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'to_location', 
	2 => 'from_location',
	3 => 'challan_no',
	4 => 'sale_date',
	5 => 'po_no',
    6 => 'entry_by',
    7 => 'status'
  
);

// getting total number records without any search
if($_SESSION["userid"]=="admin"){
	$sql = "SELECT id";
	$sql.=" FROM billing_master WHERE from_location in (".$accesslocation.") ".$filter_str."AND status IN ('Pending') ";
}else{
	$sql = "SELECT id";
	$sql.=" FROM billing_master WHERE from_location in (".$accesslocation.") ".$filter_str."AND status IN ('Pending')";
}
// echo $sql;
// exit;
$query=mysqli_query($link1, $sql) or die("InvLogistic-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if($_SESSION["userid"]=="admin"){
	$sql = "SELECT id, to_location, from_location,challan_no, sale_date, po_no,entry_by,status";
	$sql.=" FROM billing_master WHERE from_location in (".$accesslocation.") ".$filter_str."AND status IN ('Pending')";
}else{
	$sql = "SELECT id, to_location, from_location,challan_no, sale_date,po_no,entry_by, status";
	$sql.=" FROM billing_master WHERE from_location in (".$accesslocation.") ".$filter_str."AND status IN ('Pending')";
}
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( to_location LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR from_location LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR challan_no LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR sale_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR po_no LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR entry_by LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
    
}
// echo $sql;
// exit;
$query=mysqli_query($link1, $sql) or die("InvLogistic-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query);


$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']." ";
// $sql;exit;
$query=mysqli_query($link1, $sql) or die("InvLogistic-grid-data.php: ERROR! 3");

$data = array();
$j=1;
while ($row = mysqli_fetch_array($query)) { // preparing an array
	
	// STATUS //
	if($row['status']=="PFA"){ $sts = "<span class='red_small'>".$row['status']."</span>";}else{ $sts = $row['status'];}
	// STATUS //
	

	// START POD //
	$podupd="";
	if($row['pod1']=="" && $row['pod2']==""){ 
		$podupd="<a href='#' onClick=openPOD('".base64_encode($row['challan_no'])."')><i class='fa fa-upload fa-lg' title='POD Attachment'></i></a>";
	}else{
		if($row['pod1']){
			$podupd="<a href='".$row['pod1']."'  title='view' target='_blank'><i class='fa fa-download fa-lg' title='view/download POD1'></i></a>&nbsp;&nbsp;";
		}
		if($row['pod2']){ 
			$podupd="<a href='".$row['pod2']."'  title='view' target='_blank'><i class='fa fa-download fa-lg' title='view/download POD2'></i></a>";
		 }
	}
    // END POD //


	// START VIEW //
	$view="";
	$view="<a href='invoiceDetails.php?id=".base64_encode($row['challan_no'])."".$pagenav." title='Invoice Details'<i class='fa fa-eye fa-lg' title='Invoice Details'></i></a>";
	// END VIEW //
	

	// START UPDATE //
	$rs12=mysqli_query($link1,"SELECT imei_attach, prod_code FROM billing_model_data WHERE challan_no='".$row['challan_no']."'");
	$check=1;
   while($row12=mysqli_fetch_array($rs12)){
	 $get_result12 = explode("~",getAnyDetails($row12['prod_code'],"productcode,is_serialize","productcode" ,"product_master",$link1));
	 if($get_result12[1]=='Y'){ if($row12['imei_attach']=="Y"){ $check*=1;}else{ $check*=0;}}else{ $check*=1;}
   }

	$update="";
	    if($check==1){
		if($row['status']=="Pending"){
	    $update="<a href='updateCourierDetials.php?id=".base64_encode($row['challan_no'])."".$pagenav."title='Update Courier Details'><i class='fa fa-edit fa-lg' title='Update Courier Details'></i></a>";}}
		else{
		$update = "<b style='color:#F30'>Please Upload Serial No. for Serialized Product</b>";}
	// END UPDATE //

    	$nestedData = array();
		$nestedData[] = $j;
		$nestedData[] = str_replace("~",",",getLocationDetails($row['to_location'],"name,city",$link1));
		$nestedData[] = str_replace("~",",",getLocationDetails($row['from_location'],"name,city",$link1));
		$nestedData[] = $row["challan_no"];
		$nestedData[] = $row["sale_date"];
        $nestedData[] = $row["po_no"];
		$nestedData[] = getAdminDetails($row['entry_by'],"name",$link1);
		$nestedData[] = $sts;
		$nestedData[] = $view;
		$nestedData[] = $podupd;
		$nestedData[] = $update;	
		$data[] = $nestedData;
		$j++;
	}

$json_data = array(
			"draw"  => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);

echo json_encode($json_data);  // send data as json format
?>
