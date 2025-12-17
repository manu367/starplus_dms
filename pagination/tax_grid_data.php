<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (i.e, get/post) global array to a variable  
$requestData = $_REQUEST;

$columns = array(
    // datatable column index  => database column name
    0 => 'id',
    1 => 'chapter_no',
    2 => 'hsn_code',
    3 => 'hsn_description',
    4 => 'cgst',
    5 => 'sgst',
    6 => 'igst',
    7 => 'status'

);

// getting total number records without any search
if ($_SESSION['userid'] == "admin") {
    $sql = "SELECT id ";
    $sql .= " FROM tax_hsn_master ";
} else {
    $sql = "SELECT id ";
    $sql .= " FROM tax_hsn_master ";
}
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("tax-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if ($_SESSION['userid'] == "admin") {
    $sql = "SELECT * ";
    $sql .= " FROM tax_hsn_master";
} else {
    $sql = "SELECT * ";
    $sql .= " FROM tax_hsn_master";
}

if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql .= " AND (id LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR chapter_no LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR hsn_code LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR hsn_description LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR cgst LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR sgst LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR igst LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR status LIKE '" . $requestData['search']['value'] . "%')";
}

$query = mysqli_query($link1, $sql) or die("tax-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
//echo $sql;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
$query = mysqli_query($link1, $sql) or die("tax-grid-data.php: ERROR! 3");

$data = array();
$j = 1;
while ($row = mysqli_fetch_array($query)) { // preparing an array

    //// check serial no. is uploaded or not

    $view = "";
    $view = "<a href='edit_tax.php?op=edit&id=" . base64_encode($row['id']) . $pagenav . "' title='view '><i class='fa fa-eye fa-lg' title='View Details'></i></a>";

  
    $nestedData = array();
    $nestedData[] = $j;
    $nestedData[] = $row["chapter_no"];
    $nestedData[] = $row['hsn_code'];
    $nestedData[] = $row['hsn_description'];
    $nestedData[] = $row['cgst'];
    $nestedData[] =  $row['sgst'];
    $nestedData[] =  $row['igst'];
    $nestedData[] =  $row['status'];
    $nestedData[] =  $view;
   
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