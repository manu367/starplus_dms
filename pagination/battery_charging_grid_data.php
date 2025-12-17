<?php
/* Database connection start */
require_once("../config/config.php");
if($_REQUEST['fdate']=="" && $_REQUEST['tdate']==""){ $filter_str	.= " AND import_date = '".$today."'";}
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;

$columns = array(
    // datatable column index  => database column name
    0 => 'id',
    1 => 'doc_no',
    2 => 'prod_code',
    3 => 'serial_no',
    4 => 'status',
    5 => 'input_voltage'
);


// getting total number records without any search
if ($_SESSION["userid"] == "admin") {
    $sql = "SELECT *";
    $sql .= " FROM battery_charging_status WHERE import_date >= '".$_REQUEST["fdate"]."' AND import_date <= '".$_REQUEST["tdate"]."' " . $filter_str . " ";
} else {
    $sql = "SELECT *";
    $sql .= " FROM battery_charging_status WHERE import_date >= '".$_REQUEST["fdate"]."' AND import_date <= '".$_REQUEST["tdate"]."'   " . $filter_str ."";
}
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("battery-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if ($_SESSION["userid"] == "admin") {
    $sql = "SELECT *";
    $sql .= " FROM battery_charging_status WHERE import_date >= '".$_REQUEST["fdate"]."' AND import_date <= '".$_REQUEST["tdate"]."' " . $filter_str . "";
} else {
    $sql = "SELECT *";
    $sql .= " FROM battery_charging_status WHERE import_date >= '".$_REQUEST["fdate"]."' AND import_date <= '".$_REQUEST["tdate"]."'   " . $filter_str . "";
}
if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql .= " AND ( doc_no LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR prod_code LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR serial_no LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR status LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR input_voltage LIKE '" . $requestData['search']['value'] . "%' )";
}
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("battery-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
// echo $sql;
// exit;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
$query = mysqli_query($link1, $sql) or die("battery-grid-data.php: ERROR! 3");

$data = array();
$j = 1;
while ($row_dv = mysqli_fetch_array($query)) { // preparing an array
    $nestedData = array();
    if(substr($row_dv["doc_no"],0,3)=="GRN"){
        $from_party = mysqli_fetch_assoc(mysqli_query($link1, "SELECT from_location AS party_code FROM billing_master WHERE challan_no='".$row_dv['doc_no']."'"));
    }else{
        $from_party = mysqli_fetch_assoc(mysqli_query($link1, "SELECT po_to AS party_code FROM vendor_order_master WHERE po_no='".$row_dv['doc_no']."'"));
    }
  
$charging="";
$charging1="";
$charging = "<a href='#' class='btn " . $btncolor . " title='Check Charging Status' onClick=openActionModel('".$row_dv['id'].",".$from_party['party_code']."')><i class='fa fa-bolt' title='Check Charging Status'></i></a>";

$charging1 = "&nbsp; <a href='#' class='btn " . $btncolor . " title='Check Charging History' onClick=openHistoryModel('".$row_dv['serial_no']."','".$row_dv['prod_code']."','".$row_dv['import_date']."')><i class='fa fa-history' title='Check Charging History'></i></a>";
   
// END ACTION //
    
    $nestedData[] = $j;
    $nestedData[] = getVendorDetails($from_party["party_code"],"name,city,state",$link1)." (".$from_party["party_code"].")";
    $nestedData[] = $row_dv['doc_no'];
    $nestedData[] = getProductDetails($row_dv["prod_code"],"productname",$link1)." (".$row_dv["prod_code"].")";
    // $nestedData[] = getAdminDetails($row['create_by'],"name",$link1);
    $nestedData[] = $row_dv["serial_no"];
    $nestedData[] = $row_dv["status"];
    $nestedData[] =  "<b>Input Voltage:</b> ".$row_dv["input_voltage"]."<br/><b>Output Voltage:</b> ".$row_dv["output_voltage"];
    $nestedData[] = $charging.$charging1;

    $data[] = $nestedData;
    $j++;
}

$json_data = array(
    "draw"            => intval($requestData['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
    "recordsTotal"    => intval($totalData),  // total number of records
    "recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $data   // total data array
);

echo json_encode($json_data);  // send data as json format
?>