<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;
// FILTER //
$filter_str = "";
if($_REQUEST['fdate']=="" && $_REQUEST['tdate']==""){ $filter_str	.= " AND entry_date = '".$today."'";}
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND entry_date >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND entry_date <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['location_code'] !=''){
	$filter_str	.= " AND location_code = '".$_REQUEST['location_code']."'";
}
if($_REQUEST['status'] !=''){
	$filter_str	.= " AND status = '".$_REQUEST['status']."'";
}
//// get cancel rights
$isCnlRight = getCancelRightNew($_SESSION['userid'],"4",$link1);
///// get access location ///
$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
$columns = array(
    // datatable column index  => database column name
    0 => 'id',
    1 => 'location_code',
    2 => 'doc_no',
    3 => 'requested_date',
    4 => 'remark',
    5 => 'entry_by',
    6 => 'status'
);
// getting total number records without any search
if ($_SESSION["userid"] == "admin") {
    $sql = "SELECT id";
    $sql .= " FROM stockconvert_master where 1 " . $filter_str . "  ";
} else {
    $sql = "SELECT id";
    $sql .= " FROM stockconvert_master where 1 ".$filter_str." AND location_code in (" . $accesslocation . ")  ";
}
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("stock-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if ($_SESSION["userid"] == "admin") {
    $sql = "SELECT id, location_code, doc_no,requested_date, remark, entry_by, status,serial_attach";
    $sql .= " FROM stockconvert_master where 1 " . $filter_str . " ";
} else {
    $sql = "SELECT id, location_code, doc_no,requested_date, remark, entry_by, status,serial_attach";
    $sql .= " FROM stockconvert_master where 1 ".$filter_str." AND location_code in (" . $accesslocation . ") ";
}
if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql .= " AND ( location_code LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR doc_no LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR requested_date LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR remark LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR doc_no LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR entry_by LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR entry_date LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR status LIKE '" . $requestData['search']['value'] . "%' )";
}
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("stock-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
// echo $sql;
// exit;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
$query = mysqli_query($link1, $sql) or die("stock-grid-data.php: ERROR! 3");

$data = array();
$j = 1;
while ($row = mysqli_fetch_array($query)) { // preparing an array
   
    $rs12=mysqli_query($link1,"SELECT serial_attach, prod_code FROM stockconvert_data WHERE doc_no='".$row['doc_no']."'");
	$check=1;
	while($row12=mysqli_fetch_array($rs12)){
		$get_result12 = explode("~",getAnyDetails($row12['prod_code'],"productcode,is_serialize","productcode" ,"product_master",$link1));
		if($get_result12[1]=='Y'){ 
			if($row12['serial_attach']=="Y"){ 
				$check*=1;
			}else{ 
				$check*=0;
			}
		}else{ 
			$check*=1;
		}
	}
    // START STATUS //
    $status="";
    if ($row['status'] == "PFA") {
        $status = "<span class='red_small'>" . $row['status'] . "</span>";
    } else {
        $status = $row['status'];
    }
    // END STATUS//    
   	// START PRINT //
   	$print="";
   	$print2="";
   	if($check==1)
    {     
     	$print="<a href='../print/stockconvert_print.php?rb=view&id=".base64_encode($row['doc_no']).$pagenav."' target='_blank' title='Print'><i class='fa fa-print fa-lg' title='Print'></i></a>";
		if($row['serial_attach']=="Y" && $row['status']!="Cancelled"){
         	$print2="&nbsp;&nbsp;<a href='../print/stockconvert_serialprint.php?rb=view&id=".base64_encode($row['doc_no'])."".$pagenav."' target='_blank'  title='Print Serial'><i class='fa fa-print fa-lg' title='Print Serial'></i></a>"; 
        }
      	else
        {
        	$print2="";
      	}
    }
    else
	{
		$print="<b style='color:#F30'>Please Upload Serial No. for Serialized Product</b>";
       	$print2="";
	}
    // END PRINT//
    // START SERIAL //   
   	$serial="";
	$serial2="";
	if($row['status']!="Cancelled")
	{
    	if($row['serial_attach']=="")
    	{ 
        	if($check==0)
        	{
           		$serial = "<a href='upload_convert_serial.php?id=".base64_encode($row["doc_no"]).$pagenav."' title='".$imeitag." Attach'<i class='fa fa-upload fa-lg'></i></a>&nbsp;&nbsp;"; 
           		$serial2 = "<a href='serial_scan_stock_convert.php?id=".base64_encode($row["doc_no"])."&docdate=".base64_encode($row["entry_date"])."&location=".base64_encode($row["location_code"])."&stocktype=".base64_encode($row["stock_type"]).$pagenav."' title='Serial Scan' <i class='fa fa-qrcode fa-lg'></i></a>";
        	}	  
        	else{
                $serial2 = "Not Applicable";
            }
    	}
    	else
    	{
        	$serial ="YES"; 
    	}
	}
    //END SERIAL //
    // START VIEW//
    $view = "";
    $view = "<div align='center'><a href='stockconvert_view.php?op=edit&id=" . base64_encode($row['doc_no']) . "" . $pagenav . "   title='view'><i class='fa fa-eye fa-lg' title='view details'></i></a></div>";
    // END VIEW//
    // CANCEL // 
   	$cancel="";
    if($isCnlRight==1){
    	if($row['status']=="Processed"){
    		$cancel ="<a href='cancelstockconvert.php?id=" . base64_encode($row['doc_no']) . "" . $pagenav . "'  title='cancel'><i class='fa fa-remove fa-lg' title='cancel'></i></a>";
		}
	}
    // CANCEL //
    $nestedData = array();
    $nestedData[] = $j;
    $nestedData[] = getLocationDetails($row['location_code'], "name,city,state", $link1);
    $nestedData[] = $row['doc_no'];
    $nestedData[] = $row['requested_date'];
    $nestedData[] = $row['remark'];
    $nestedData[] = getAdminDetails($row['entry_by'], "name", $link1);
    $nestedData[] = $status;
    $nestedData[] = $print.$print2;
    $nestedData[] = $serial.$serial2;
    $nestedData[] = $view;
    $nestedData[] = $cancel;
    $data[] = $nestedData;
    $j++;
}

$json_data = array(
    "draw"  => intval($requestData['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
    "recordsTotal"    => intval($totalData),  // total number of records
    "recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $data   // total data array
);

echo json_encode($json_data);  // send data as json format
?>
