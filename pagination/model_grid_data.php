<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
///// get access location ///
$brand = $_REQUEST['brand'];
$product_cat = $_REQUEST['product_cat'];
$product_sub_cat = $_REQUEST['product_sub_cat'];
$product = $_REQUEST['product'];
// $accessBrand=getAccessBrand($_SESSION['userid'],$link1);

////// filters value/////

if($brand!=""){
	$pro_brand="brand='".$brand."'";
}else{
	$pro_brand="1";
}

if($product_cat!=""){
	$pc = "productid='".$product_cat."'";
}else{
	$pc = "1";
}
if($product_sub_cat!=""){
	$psc = "productsubcat='".$product_sub_cat."'";
}else{
	$psc = "1";
}

if($product!=""){
	$product="productcode='".$product."'";
}else{
	$product="1";
}

//////End filters value/////
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'productcode',
	2 => 'productname',
	3 => 'model_name', 
	4 => 'productcategory',
	5 => 'productsubcat',
	6 => 'brand',
    7 => 'hsn_code',
    8 => 'status',
    9 => 'createdate'
);
// getting total number records without any search


if($_SESSION['userid']=='admin'){	
	$sql = "SELECT id";
	$sql.=" FROM product_master WHERE ".$pro_brand." AND ".$psc." AND ".$product."";
}else{
	$sql = "SELECT id";
	$sql.=" FROM product_master WHERE ".$product."  AND ".$psc." AND ".$product."";
}
// echo $sql;
// exit;

$query=mysqli_query($link1, $sql) or die("product-grid-data.php: ERROR 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if($_SESSION['userid']=='admin'){	
	$sql = "SELECT *";
	$sql.=" FROM product_master WHERE ".$product." AND ".$psc." AND ".$product."";
}else{
	$sql = "SELECT *";
	$sql.=" FROM product_master WHERE ".$product."  AND ".$psc." AND ".$product."";
}

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter

	$sql.=" AND (productcode LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR productname LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR model_name LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR productcategory LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR productsubcat LIKE '".$requestData['search']['value']."%'"; 
    $sql.=" OR brand LIKE '".$requestData['search']['value']."%'"; 
    $sql.=" OR hsn_code LIKE '".$requestData['search']['value']."%'"; 
    $sql.=" OR status LIKE '".$requestData['search']['value']."%'"; 
    $sql.=" OR createdate LIKE '".$requestData['search']['value']."%' ) "; 
    
}
$query=mysqli_query($link1, $sql) or die("product-grid-data.php: ERROR 2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']." ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("product-grid-data.php: ERROR 3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
		$nestedData=array(); 
		
	////// check this user have right to view the details

    $view = "<a href='edit_model.php?op=edit&id=".base64_encode($row['id'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg' title='view details'></i></a>";
	
	
	$nestedData[] = $j; 
	$nestedData[] = $row["productcode"];
	$nestedData[] = $row["productname"];
	$nestedData[] = $row["model_name"];
	$nestedData[] = getAnyDetails($row['productcategory'],"cat_name","catid" ,"product_cat_master"  ,$link1);
	$nestedData[] = getAnyDetails($row['productsubcat'],"prod_sub_cat","psubcatid" ,"product_sub_category"  ,$link1);
    $nestedData[] = getAnyDetails($row['brand'],"make","id" ,"make_master"  ,$link1);
    $nestedData[] = $row["hsn_code"];
    $nestedData[] = $row["status"];
    $nestedData[] = $row["createdate"];
	$nestedData[] = $view;
	
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
