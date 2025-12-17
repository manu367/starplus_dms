<?php
/* Database connection start */
require_once("../config/config.php");


/* Database connection end */

// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
////// filters value/////
## selected state
/*if($requestData['val1']['value']!=""){
	$loc_state="state='".$requestData['val1']['value']."'";
}else{
	$loc_state="1";
}
## selected product
if($requestData['val2']['value']!=""){
	$product="product_code='".$requestData['val2']['value']."'";
}else{
	$product="1";
}
## selected location type
if($requestData['val3']['value']!=""){
	$loc_type="location_type='".$requestData['val3']['value']."'";
}else{
	$loc_type="1";
}*/

$columns = array( 
// datatable column index  => database column name
	0 => 'state', 
	1 => 'location_type',
	2 => 'product_code',
	3 => 'mrp',
	4 => 'price',
	5 => 'status'
);

// getting total number records without any search
$sql = "SELECT state, location_type, product_code, mrp, price, status";
$sql.=" FROM price_master";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("price-grid-data.php: get price");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT state, location_type, product_code, mrp, price, status";
$sql.=" FROM price_master WHERE 1=1";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( state LIKE '".$requestData['search']['value']."%' ";    
	$sql.=" OR location_type LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR product_code LIKE '".$requestData['search']['value']."%' )";
	$sql.=" OR mrp LIKE '".$requestData['search']['value']."%' )";
	$sql.=" OR price LIKE '".$requestData['search']['value']."%' )";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("price-grid-data.php: get price");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("price-grid-data.php: get price");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
     
	$nestedData[] = $j; 
	$nestedData[] = $row["state"];
	$nestedData[] = $row["location_type"];
	$nestedData[] = $row["product_code"];
	$nestedData[] = $row["mrp"];
	$nestedData[] = $row["price"];
	$nestedData[] = $row["status"];
	$nestedData[] = "<a href='edit_price.php?op=edit&id=".base64_encode($row1['id'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg' title='view details'></i></a>";
	
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
