<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
////// filters value/////
$filter_str = "";
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND DATE(entry_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND DATE(entry_date) <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['prod_cat'] !=''){
	$filter_str	.= " AND prod_catid = '".$_REQUEST['prod_cat']."'";
}
if($_REQUEST['prod_subcat'] !=''){
	$filter_str	.= " AND prod_subcatid = '".$_REQUEST['prod_subcat']."'";
}
if($_REQUEST['prod_brand'] !=''){
	$filter_str	.= " AND brand_id ='".$_REQUEST['prod_brand']."'";
}
if($_REQUEST['prod_code'] !=''){
	$filter_str .= " AND prod_code ='".$_REQUEST['prod_code']."'";
}
if($_REQUEST['state'] !=''){
	$filter_str .= " AND state ='".$_REQUEST['state']."'";
}
if($_REQUEST['city'] !=''){
	$filter_str .= " AND city ='".$_REQUEST['city']."'";
}
if($_REQUEST['location_code']!=""){
	$location = "location_code='".$_REQUEST['location_code']."'";
}else{
	$acc_loc = getAccessLocation($_SESSION['userid'],$link1);
	$location = "location_code IN (".$acc_loc.")";
}
//////End filters value/////
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'serial_no',
	2 => 'location_code',
	3 => 'prod_code',
	4 => 'prod_name', 
	5 => 'invoice_no',
	6 => 'invoice_date',
	7 => 'customer_name',
	8 => 'contact_no',
	9 => 'status'
);

// getting total number records without any search
$sql = "SELECT id";
$sql.=" FROM sale_registration WHERE 1 ".$filter_str." AND ".$location;
$query=mysqli_query($link1, $sql) or die("saleregister-grid-data.php: get sr1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT *";
$sql.=" FROM sale_registration WHERE 1 ".$filter_str." AND ".$location;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (serial_no LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR location_code LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR prod_code LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR prod_name LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR invoice_no LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR invoice_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR customer_name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR state LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR city LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR pincode LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR contact_no LIKE '".$requestData['search']['value']."%' )";
	
	$query=mysqli_query($link1, $sql) or die("saleregister-grid-data.php: get sr2");
	$totalFiltered = mysqli_num_rows($query); 
}
// when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']." ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("saleregister-grid-data.php: get sr3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
	////// check this user have right to view the details
	if ($row['photo'] != '') {
		$imgicon = '<img src="../salesapi/'.$row['photo'].'" alt="" id="image'.$j.'" onClick="getThisValue('.$j.')" style="width: 100%;"/>';
	}else{
		$imgicon = "Not clicked";
	}
	if($row['status']!="Cancelled"){
		$cancel = "&nbsp;&nbsp;<i class='fa fa-trash fa-lg' title='Cancel this sale' onClick=openModelUpd('".base64_encode($row['id'])."');></i>";
	}else{
		$cancel = "";
	}

	$response = "<table class=table table-bordered width=100%><thead><tr><th>Brand</th><th>Model</th><th>Product Category</th><th>Product Sub Cat</th></tr></thead><tbody>";
	$response .= "<tr><td>".getAnyDetails($row["brand_id"],"make","id","make_master",$link1)."</td><td>".$row["model_name"]."</td><td>".getAnyDetails($row["prod_catid"],"cat_name","catid","product_cat_master",$link1)."</td><td>".getAnyDetails($row["prod_subcatid"],"prod_sub_cat","psubcatid","product_sub_category",$link1)."</td></tr>";
	$response .= "</tbody></table>";
	
	$response2 = "<table class=table table-bordered width=100%><thead><tr><th>State</th><th>City</th><th>Pincode</th><th>Address</th></tr></thead><tbody>";
	$response2 .= "<tr><td>".$row["state"]."</td><td>".$row["city"]."</td><td>".$row["pincode"]."</td><td>".$row["address"]."</td></tr>";
	$response2 .= "</tbody></table>";
	
		
	$nestedData[] = $j."".$cancel; 
	$nestedData[] = $row["serial_no"];
	$nestedData[] = str_replace("~"," , ",getAnyDetails($row["location_code"],"name,city,state,asc_code","asc_code","asc_master",$link1));
	$nestedData[] = "<a href='#' title='Product Details' data-toggle='popover' data-trigger='focus' data-placement='left' data-content='".$response."'>".$row["prod_code"]."</a>";
	$nestedData[] = $row["prod_name"];
	$nestedData[] = $row["invoice_no"];
	$nestedData[] = $row["invoice_date"];
	$nestedData[] = $row["customer_name"];
	$nestedData[] = "<a href='#' title='Customer Details' data-toggle='popover' data-trigger='focus' data-placement='left' data-content='".$response2."'>".$row["contact_no"]."</a>";
	$nestedData[] = $row['status'];
	$nestedData[] = $imgicon;
	
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
