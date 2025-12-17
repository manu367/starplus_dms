<?php
/* Database connection start */
require_once("../config/config.php");
//$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);
/////get status//
//$arrstatus = getFullStatus("master",$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$gatepassdata= $_REQUEST;
## selected  Status
if($_REQUEST['status']!=""){
	$status="status='".$_REQUEST['status']."'";
}else{
	$status="1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'cat_name ', 
	1 => 'status'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM product_cat_master where ".$status."";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("product-grid-data.php: get product master");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM product_cat_master where ".$status."";
if( !empty($gatepassdata['search']['value']) ) {   // if there is a search parameter, $gatepassdata['search']['value'] contains search parameter
	$sql.=" AND ( cat_name LIKE '".$gatepassdata['search']['value']."%'";    
	$sql.=" OR status LIKE '".$gatepassdata['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("product-grid-data.php: get product master");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$gatepassdata['order'][0]['column']]."   ".$gatepassdata['order'][0]['dir']."  LIMIT ".$gatepassdata['start']." ,".$gatepassdata['length']."   ";
/* $gatepassdata['order'][0]['column'] contains colmun index, $gatepassdata['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("prod-grid-data.php: get product master");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 

if($row['status'] == '1'){ $status = "Active";}
else { $status = "Deactive";}

 // preparing an array
	$nestedData=array(); 
	
	////// check this user have right to view the details
 
        $viewicon = "<div align='center'><a href='add_product.php?op=Edit&id=".$row['catid']."&status=".$_REQUEST['status']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit product details'></i></a></div>";
	
 
     
	$nestedData[] = "<div align='center'>".$j."</div>"; 
	$nestedData[] = $row["cat_name"];
	$nestedData[] = $row["product_code"];
	$nestedData[] = $row["short_code"];
	$nestedData[] = "<div align='center'>".$status."</div>";
	$nestedData[] = $viewicon;
	
	$data[] = $nestedData;
	$j++;
}



$json_data = array(
			"draw"            => intval( $gatepassdata['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);

echo json_encode($json_data);  // send data as json format
?>
