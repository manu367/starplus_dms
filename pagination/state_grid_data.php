<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (i.e, get/post) global array to a variable  
$requestData = $_REQUEST;

$columns = array(
    // datatable column index  => database column name
    0 => 'sno',
    1 => 'state',
    2 => 'zone',
    3 => 'code',
    4 => 'statecode'

);

// getting total number records without any search
if ($_SESSION['userid'] == "admin") {
    $sql = "SELECT sno ";
    $sql .= " FROM state_master where 1 ";
} else {
    $sql = "SELECT sno ";
    $sql .= " FROM state_master where 1";
}
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("state-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if ($_SESSION['userid'] == "admin") {
    $sql = "SELECT * ";
    $sql .= " FROM state_master where 1";
} else {
    $sql = "SELECT * ";
    $sql .= " FROM state_master where 1";
}

if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql .= " AND (state LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR zone LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR code LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR statecode LIKE '" . $requestData['search']['value'] . "%')";
}

$query = mysqli_query($link1, $sql) or die("state-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
//echo $sql;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
$query = mysqli_query($link1, $sql) or die("state-grid-data.php: ERROR! 3");

$data = array();
$j = 1;
while ($row = mysqli_fetch_array($query)) { // preparing an array

    //// check serial no. is uploaded or not

    //view//
    $view = "";
    $view = "<a href='edit_state.php?op=edit&id=" . base64_encode($row['sno']) . $pagenav . "' title='view '><i class='fa fa-eye fa-lg' title='View Details'></i></a>";
    //view//

    $nestedData = array();
    $nestedData[] = $j;
    $nestedData[] = $row["state"];
    $nestedData[] = $row['zone'];
    $nestedData[] = $row['code'];
    $nestedData[] = $row['statecode'];
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