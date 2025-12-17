<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (i.e, get/post) global array to a variable  
$requestData = $_REQUEST;
$accessLocation=getAccessLocation($_SESSION['userid'],$link1);
$accessState=getAccessState($_SESSION['userid'],$link1);
$locationstate = $_REQUEST['locationstate'];
$product = $_REQUEST['product'];
$locationtype = $_REQUEST['locationtype'];
////// filters value/////

## selected state
if($locationstate!=""){
	$loc_state="state='".$locationstate."'";
}else{
	$loc_state="1";
}
## selected product
if($product!=""){
	$product="product_code='".$product."'";
}else{
	$product="1";
}
## selected location type
if($locationtype!=""){
	$loc_type="location_type='".$locationtype."'";
}else{
	$loc_type="1";
}
//////End filters value/////


$columns = array(
    // datatable column index  => database column name
    0 => 'id',
    1 => 'state',
    2 => 'location_type',
    3 => 'product_code',
    4 => 'mrp',
    5 => 'price',
    6 => 'status'
);


// getting total number records without any search
if ($_SESSION['userid'] == "admin") {
    $sql = "SELECT id ";
    $sql .= " FROM price_master  WHERE ".$loc_state." AND ".$product." AND ".$loc_type."";
} else {
    $sql = "SELECT id ";
    $sql .= " FROM price_master WHERE ".$loc_state." AND ".$product." AND ".$loc_type."";
}
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("price-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if ($_SESSION['userid'] == "admin") {
    $sql = "SELECT * ";
    $sql .= " FROM price_master WHERE ".$loc_state." AND ".$product." AND ".$loc_type."";
} else {
    $sql = "SELECT * ";
    $sql .= " FROM price_master WHERE ".$loc_state." AND ".$product." AND ".$loc_type."";
}

if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql .= " AND (state LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR location_type LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR product_code LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR mrp LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR price LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR status LIKE '" . $requestData['search']['value'] . "%' )";
}

$query = mysqli_query($link1, $sql) or die("price-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
//echo $sql;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
$query = mysqli_query($link1, $sql) or die("price-grid-data.php: ERROR! 3");

$data = array();
$j = 1;
while ($row = mysqli_fetch_array($query)) { // preparing an array

    //// check serial no. is uploaded or not

    $view = "";
    $view = "<a href='edit_price.php?op=edit&id=" . base64_encode($row['id']) . $pagenav . "' title='view '><i class='fa fa-eye fa-lg' title='View Details'></i></a>";

    $nestedData = array();
    $nestedData[] = $j;
    $nestedData[] = $row["state"];
    $nestedData[] = getLocationType($row['location_type'],$link1);
    $nestedData[] = getProduct($row['product_code'],$link1)." - ".$row['product_code'];
    $nestedData[] = $row['mrp'];
    $nestedData[] = $row['price'];
    $nestedData[] = $row['status'];
    $nestedData[] = $view;
   
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