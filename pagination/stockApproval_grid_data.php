<?php
/* Database connection start */
require_once("../config/config.php");
$requestData = $_REQUEST;
	///// get access location ///
    $accesslocation=getAccessLocation($_SESSION['userid'],$link1);

$columns = array(
    // datatable column index  => database column name
    0 => 'id',
    1 => 'from_location',
    2 => 'to_location',
    3 => 'challan_no',
    4 => 'sale_date', 
    5 => 'entry_by',
    6 => 'status'
);

// getting total number records without any search
if ($_SESSION["userid"] == "admin") {
    $sql = "SELECT *";
    $sql .= " FROM billing_master where from_location in (".$accesslocation.")  and type = 'STN'";
} else {
    $sql = "SELECT *";
    $sql .= " FROM billing_master  where to_location in (".$accesslocation.")  and type = 'GRN'";
}
// echo $sql;
// exit;

$query = mysqli_query($link1, $sql) or die("stock-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if ($_SESSION["userid"] == "admin") {
    $sql = "SELECT *";
    $sql .= " FROM billing_master where from_location in (".$accesslocation.")  and type = 'STN' ";
} else {
    $sql = "SELECT *";
    $sql .= " FROM billing_master where from_location in (".$accesslocation.")  and type = 'STN'";
}
if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql .= " AND ( to_location  LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR from_location LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR challan_no LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR sale_date LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR entry_by LIKE '" . $requestData['search']['value'] . "%'";
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
   
//// check serial no. is uploaded or not

    // STATUS //
	   $status ="";
	   if($row['status']=="PFA"){ 
		$status = "<span class='red_small'>".$row['status']."</span>";
	    }else{
		$status = $row['status'];
	    }
    // STATUS //
   
    //START VIEW//
    $view="";
    $view = "<a href='stockTransferApprovalPageN.php?op=app&id=" . base64_encode($row['challan_no']) . "" . $pagenav . "   title='view'><i class='fa fa-eye fa-lg' title='view details'></i></a>";
    //END VIEW//

    
    //EDIT//
    $edit="";
    if($row['status']=="PFA"){
    $edit = "<a href='editstnDetails.php?op=edit&id=" . base64_encode($row['challan_no']) . "" . $pagenav . "   title='edit'><i class='fa fa-pencil fa-lg' title='edit details'></i></a>";
    }
    //EDIT//

   
    $nestedData = array();
    $nestedData[] = $j;
    $nestedData[] = getLocationDetails($row['from_location'],"name,city,state",$link1);
    $nestedData[] = getLocationDetails($row['to_location'],"name,city,state",$link1);
	$nestedData[] = $row['challan_no'];
	$nestedData[] = $row['sale_date'];
    $nestedData[] = getAdminDetails($row['entry_by'],"name",$link1);
    $nestedData[] = $status;
	$nestedData[] = $view;
    $nestedData[] = $edit;
    
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