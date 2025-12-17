<?php
/* Database connection start */
require_once("../config/config.php");

/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 => 'status',
	1 => 'sno',
	2 => 'name',
	3 => 'city', 
	4 => 'state',
	5 => 'country',
	6 => 'phone',
	7 => 'email',
    8 => 'address'
);

// getting total number records without any search
if($_SESSION['userid']=="admin"){
	$sql = "SELECT sno ";
	$sql .= " FROM vendor_master";
}else{
	$sql = "SELECT sno ";
	$sql .= " FROM vendor_master ";
}
// echo $sql;
// exit;
$query=mysqli_query($link1, $sql) or die("vendor-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if($_SESSION['userid']=="admin"){
	$sql = "SELECT * ";
	$sql .= " FROM vendor_master  WHERE 1";
}else{
	$sql = "SELECT * ";
	$sql .= " FROM vendor_master WHERE 1";
}
// echo $sql;
// exit;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR city LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR state LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR country LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR phone LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR email LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR address LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("vendor-grid-data.php: ERROR! 2");
//echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
// echo $sql;
// exit;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("vendor-grid-data.php: ERROR! 3");

$data = array();
$j=1;
while ($row = mysqli_fetch_array($query)) { // preparing an array
	$nestedData = array();
     
	    
	//status//
	$status="";
	$status = "<a href='#' class='style1' onClick=confirmDel('activeVendor.php?a=".$row['sno']."&status=".$row['status']."')>".$row['status']."</a>";
    //status//

	//edit//
    $edit="";

    if($row['vendor_origin'] == 'Domestic') 
	{
		$edit = "<a href ='editvendor.php?sno=".$row['sno']."&vendor_origin=".$row['vendor_origin']."&mode_of_ship=".$row['mode_of_ship']."'><img src='../img/view4.png' alt='Edit' align='center' border='0' ></a>";
	}
        else
		{
			$edit = "<a href = editforeignvendor.php?sno=".$row['sno']."&vendor_origin=".$row['vendor_origin']."&mode_of_ship=".$row['mode_of_ship'].">
			<img src='../img/view4.png' alt='Edit' align='center' border='0' ></a>"; 
		}
    //edit//
		
	//// check serial no. is uploaded or not

    $nestedData[] = $status;
    $nestedData[] = $edit;
    $nestedData[] = $j;
	$nestedData[] = $row["name"];
	$nestedData[] = $row["city"];
	$nestedData[] = $row["state"];
    $nestedData[] = $row["country"];
    $nestedData[] = $row["phone"];
    $nestedData[] = $row["email"];
    $nestedData[] = $row["address"];
 
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
?>