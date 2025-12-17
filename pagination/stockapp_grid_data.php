<?php
/* Database connection start */
require_once("../config/config.php");
$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;

////// filters value/////
$filter_str = 1;
if ($_REQUEST['fdate'] != '') {
    $filter_str    .= " AND DATE(entry_date) >= '" . $_REQUEST['fdate'] . "'";
}
if ($_REQUEST['tdate'] != '') {
    $filter_str    .= " AND DATE(entry_date) <= '" . $_REQUEST['tdate'] . "'";
}
if ($_REQUEST['main_location'] != '') {
    $filter_str    .= " AND main_location = '" . $_REQUEST['main_location'] . "'";
}
if ($_REQUEST['stock_from'] != '') {
    $filter_str    .= " AND from_location = '" . $_REQUEST['stock_from'] . "'";
}


$columns = array(
    // datatable column index  => database column name
    0 => 'id',
    1 => 'main_location',
    2 => 'from_location',
    3 => 'to_location',
    4 => 'move_stocktype',
    5 => 'doc_no',
    6 => 'entry_date',
    7 => 'status',
    8 => 'app_status'
);
// getting total number records without any search
if ($_SESSION["userid"] == "admin") {
    $sql = "SELECT id";
    $sql .= " FROM stock_movement_master WHERE  main_location IN (".$accesslocation.") AND ".$filter_str."";
} else {
    $sql = "SELECT id";
    $sql .= " FROM stock_movement_master WHERE main_location IN (".$accesslocation.") AND ".$filter_str."";
}
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("stock-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if ($_SESSION["userid"] == "admin") {
    $sql = "SELECT id, main_location, from_location,to_location, move_stocktype, doc_no, entry_date, status,app_status";
    $sql .= " FROM stock_movement_master WHERE main_location IN (".$accesslocation.") AND ".$filter_str."";
} else {
    $sql = "SELECT id, main_location, from_location,to_location, move_stocktype, doc_no, entry_date, status,app_status";
    $sql .= " FROM stock_movement_master WHERE main_location IN (".$accesslocation.") AND ".$filter_str." ";
}
if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql .= " AND ( main_location LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR from_location LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR to_location LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR move_stocktype LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR doc_no LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR entry_date LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR app_status LIKE '" . $requestData['search']['value'] . "%'";
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

    $billfrom=getLocationDetails($row['from_location'],"name,city,state",$link1);
	$explodevalf=explode("~",$billfrom);
		 if($explodevalf[0]){ $fromparty=$billfrom; }else{ $fromparty=getAnyDetails($row['from_location'],"sub_location_name","sub_location","sub_location_master",$link1);}
				  /// move to party
	 $billto=getLocationDetails($row['to_location'],"name,city,state",$link1);
	$explodeval=explode("~",$billto);
			if($explodeval[0]){ $toparty=$billto; }else{ $toparty=getAnyDetails($row['to_location'],"sub_location_name","sub_location","sub_location_master",$link1);}

    // START STATUS //
        if($row['status']=="PFA"){ $sts = "<span class='red_small'>".$row['status']."</span>";}else{ $sts = $row['status'];}
    // END STATUS //

    
     // START ACTION //
     if($row['status']=="PFA"){
        $action =  "<div align='center'><a href='#' class='btn " . $btncolor . " title='Approval Action' onClick=openAppModel('" . base64_encode($row['doc_no'])."')><i class='fa fa-gavel' title='Approval Action'></i></a></div>";
         }
         else {
        $action =$row['app_status'];
         }
    // END ACTION //

    // START VIEW //
        $view = "";
        $view = "<a href='#' class='btn " . $btncolor . " title='Stock movement info' onClick=openDocModel('" . base64_encode($row['doc_no'])."')><i class='fa fa-info-circle' title='Stock movement info'></i></a>";
    // END VIEW //

   
    // START PRINT //

      $print = "<div align='center'><a href='../print/print_stockmovement.php?rb=view&id=" . base64_encode($row['doc_no']) . "" . $pagenav . "' class='btn " . $btncolor . "  title='Print Document'><i class='fa fa-print fa-lg' title='Print Document'></i></a></div>";

       if ($row['serial_attach']) {
       $print = "<a href='../print/print_imei.php?rb=view&id=" . base64_encode($row['doc_no']) . "" . $pagenav . "'   title='Print Serial'><i class='fa fa-print fa-lg' title='Print Serial'></i></a>";
        }
    // END PRINT //
    

    $nestedData = array();
    $nestedData[] = $j;
    $nestedData[] = str_replace("~",",",getLocationDetails($row['main_location'],"name,city,state",$link1));
    $nestedData[] = str_replace("~",",",$fromparty);
    $nestedData[] = str_replace("~",",",$toparty);
    $nestedData[] = getStockTypeName($row['move_stocktype']);
    $nestedData[] = $row['doc_no'];
    $nestedData[] = $row['entry_date'];
    $nestedData[] = $sts;
    $nestedData[] = $action;
    $nestedData[] = $view;
    $nestedData[] = $print;
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