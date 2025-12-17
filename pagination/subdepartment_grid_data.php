<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (i.e, get/post) global array to a variable  
$requestData = $_REQUEST;

$columns = array(
    // datatable column index  => database column name
    0 => 'subdeptid',
    1 => 'subdept',
    2 => 'department',
    3 => 'createdate'
);

// getting total number records without any search
if ($_SESSION['userid'] == "admin") {
    $sql = "SELECT subdeptid ";
    $sql .= " FROM hrms_subdepartment_master where 1 ";
} else {
    $sql = "SELECT subdeptid ";
    $sql .= " FROM hrms_subdepartment_master where 1";
}
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("subdepartment-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if ($_SESSION['userid'] == "admin") {
    $sql = "SELECT * ";
    $sql .= " FROM hrms_subdepartment_master where 1 ";
} else {
    $sql = "SELECT * ";
    $sql .= " FROM hrms_subdepartment_master where 1";
}

if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql .= " AND (subdeptid LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR subdept LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR department LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR createdate LIKE '" . $requestData['search']['value'] . "%')";
}

$query = mysqli_query($link1, $sql) or die("subdepartment-grid-data.php: ERROR! 2");
// echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
//echo $sql;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
$query = mysqli_query($link1, $sql) or die("subdepartment-grid-data.php: ERROR! 3");

$data = array();
$j = 1;
while ($row = mysqli_fetch_array($query)) { // preparing an array

    //// check serial no. is uploaded or not
     
    //status//
    $status="";
    if($row['status']=='1'){ 
        $status="Active";
    }
    else
    {
        $status="Deactive";
    }
    //status//

    //view//
    $edit = "";
    $edit = "<a href='subdepartment_edit.php?id=" . base64_encode($row['subdeptid']) . $pagenav . "' title='Edit'><i class='fa fa-edit fa-lg' title='Edit'></i></a>";
   //view//
  
    $nestedData = array();
    $nestedData[] = $j;
    $nestedData[] = $row["subdept"];
    $nestedData[] = $row['department'];
    $nestedData[] = $row['createdate'];
    $nestedData[] = $status;
    $nestedData[] =  $edit;
   
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