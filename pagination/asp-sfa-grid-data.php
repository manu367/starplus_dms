<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
///// get access location ///
$accessLocation=getAccessLocation($_SESSION['userid'],$link1);
$accessState=getAccessState($_SESSION['userid'],$link1);
$locationstate = $_REQUEST['locationstate'];
$locationcity = $_REQUEST['locationcity'];
$locationtype = $_REQUEST['locationtype'];
$locationstatus = $_REQUEST['locationstatus'];
////// filters value/////
## selected state
## selected state
if($accessLocation!=""){
	$loc_code=" asc_code in (".$accessLocation.")";
}else{
	$loc_code="  ";
}
if($locationstate!=""){
	$loc_state="state='".$locationstate."'";
}else{
	$loc_state="state in (".$accessState.")";
}
## selected city
if($locationcity!=""){
	$loc_city="city='".$locationcity."'";
}else{
	$loc_city="1";
}
## selected location type
if($locationtype!=""){
	$loc_type="id_type='".$locationtype."'";
}else{
	$loc_type="1";
}
## selected location Status
if($locationstatus!=""){
	$loc_status="status='".$locationstatus."'";
}else{
	$loc_status="1";
}
//////End filters value/////
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'sno',
	2 => 'name',
	3 => 'state', 
	4 => 'id_type',
	5 => 'phone',
	6 => 'status'
	
);

// getting total number records without any search
/*if($_SESSION['user_level']!=''){
	$sql = "SELECT sno";
	$sql.=" FROM asc_master WHERE ".$loc_state." ".$loc_code." AND ".$loc_city." AND ".$loc_type." AND ".$loc_status." and user_level >= ".$_SESSION['user_level']."";
}else{*/
if($_SESSION['userid']=='admin'){	
	$sql = "SELECT sno";
	$sql.=" FROM asc_master WHERE ".$loc_state." AND ".$loc_city." AND ".$loc_type." AND ".$loc_status."";
}else{
	$sql = "SELECT sno";
	$sql.=" FROM asc_master WHERE ".$loc_state." AND ".$loc_city." AND ".$loc_type." AND ".$loc_status." AND ".$loc_code."";
}
//}
$query=mysqli_query($link1, $sql) or die("asc-grid-data.php: get asc master1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

/*if($_SESSION['user_level']!=''){
	$sql = "SELECT *";
	$sql.=" FROM asc_master WHERE ".$loc_state." ".$loc_code." AND ".$loc_city." AND ".$loc_type." AND ".$loc_status." and user_level >= ".$_SESSION['user_level']."";
}else{*/
if($_SESSION['userid']=='admin'){	
	$sql = "SELECT *";
	$sql.=" FROM asc_master WHERE ".$loc_state." AND ".$loc_city." AND ".$loc_type." AND ".$loc_status."";
}else{
	$sql = "SELECT *";
	$sql.=" FROM asc_master WHERE ".$loc_state." AND ".$loc_city." AND ".$loc_type." AND ".$loc_status." AND ".$loc_code."";
}
//}
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (uid LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR state LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR city LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR phone LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR id_type IN ( SELECT locationtype FROM location_type WHERE locationname LIKE '".$requestData['search']['value']."%') )";
}
$query=mysqli_query($link1, $sql) or die("asc-grid-data.php: get asc master2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']." ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("asc-grid-data.php: get asc master3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
		$nestedData=array(); 
		$cr_opt = "";
		$voucher_ext = "";
		if($row['id_type']=="HO" || $row['id_type']=="BR"){
			//$voucher_ext= "&nbsp;&nbsp;<a href='ledger_voucher_extension.php?locid=".base64_encode($row['asc_code'])."".$pagenav."' title='Update voucher extension name'><i class='fa fa-address-book-o fa-lg' title='Update voucher extension name'></i></a>";
		}else{
			$voucher_ext= "";
		}
		if($row['id_type']=="DS"){
			//$cr_opt = "&nbsp;&nbsp;<a href='update_credit_limit.php?locid=".base64_encode($row['asc_code'])."".$pagenav."' title='Update credit limit of this location'><i class='fa fa-credit-card fa-lg' title='Update credit limit of this location'></i></a>";
		}else{
			$cr_opt = "";
		}
		$apphierarchy = "";
		if($row['id_type']=="DS"){
		//$apphierarchy = "&nbsp;&nbsp;<a href='#' title='Approval Hierarchy' onClick = checkMapingInfo('".base64_encode($row['asc_code'])."','".base64_encode($row['name'].", ".$row["city"].", ".$row["state"])."');><i class='fa fa-sort-amount-asc fa-lg faicon' title='Approval Hierarchy'></i></a>";
		}
	////// check this user have right to view the details
        $viewicon = "<div align='center'><a href='asp_edit_sfa.php?op=edit&id=".base64_encode($row['sno'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view customer/location details'></i></a></div>";
		//$mapicon = "<div align='center'><a href='mappWithLocations.php?id=".base64_encode($row['sno'])."".$pagenav."' title='Mapp to parent location'><i class='fa fa-map-signs fa-lg' title='Mapp to parent location'></i></a>&nbsp;&nbsp;<a href='sub_location_master.php?locid=".base64_encode($row['asc_code'])."".$pagenav."' title='Add/View Sub-Location(Go-down)'><i class='fa fa-sitemap fa-lg' title='Add/View Sub-Location(Go-down)'></i></a>&nbsp;&nbsp;<a href='location_shipto_master.php?locid=".base64_encode($row['asc_code'])."".$pagenav."' title='Add/View Ship Address'><i class='fa fa-address-card fa-lg' title='Add/View Ship Address'></i></a>".$cr_opt."".$voucher_ext."".$apphierarchy."</div>";
		//$histicon = "<div align='center'><a href='asp_history.php?asccode=".base64_encode($row['asc_code'])."".$pagenav."'  title='party history'><i class='fa fa-history fa-lg' title='view history'></i></a></div>";
		
	$nestedData[] = $j; 
	$nestedData[] = $row["uid"];
	$nestedData[] = $row["name"];
	$nestedData[] = $row["state"];
	$nestedData[] = getLocationType($row['id_type'],$link1);
	$nestedData[] = $row["phone"];
	$nestedData[] = $row["status"];
	$nestedData[] = $viewicon;	
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
