<?php
/* Database connection start */
require_once("../config/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

///////filter value///////

$fromd = $_REQUEST['fdate'];
$tod = $_REQUEST['tdate'];
if ($_REQUEST['username'] != '') {
    $sqldata.=" AND a.user_id = '".$_REQUEST['username']."'";
}
if ($_REQUEST['fdate'] != '' or $_REQUEST['tdate'] != '') {
    $sqldata.=" AND a.in_datetime BETWEEN '" . $fromd . "' and '" . $tod . "'";
}else{
    $sqldata.=" AND a.in_datetime BETWEEN '" . $today . "' and '" . $today . "'";
}

if($_REQUEST['department']){
	$deptqry = " AND b.department ='".$_REQUEST['department']."'";
}else{
	$deptqry = "";
}
if($_REQUEST['subdepartment']){
	$subdeptqry = " AND b.subdepartment ='".$_REQUEST['subdepartment']."'";
}else{
	$subdeptqry = "";
}

$columns = array( 
// datatable column index  => database column name
	0 => 'name',
	1 => 'in_datetime',
	2 => 'address_in',
	3 => 'Image_in', 
	4 => 'out_datetime',
	5 => 'address_out',
	6 => 'Image_out'
);


// getting total number records without any search
$sql = "SELECT a.*,b.name, b.oth_empid,b.department, b.subdepartment";
$sql.=" FROM user_travel_plan a, admin_users b WHERE 1=1 AND a.user_id=b.username ".$subdeptqry." ".$deptqry." ".$user_id." ".$sqldata." ";

$query=mysqli_query($link1, $sql) or die("travel-grid-data.php: ERROR-1");

$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT a.*,b.name, b.oth_empid,b.department, b.subdepartment";
$sql.=" FROM user_travel_plan a, admin_users b WHERE 1=1 AND a.user_id=b.username ".$subdeptqry." ".$deptqry." ".$user_id." ".$sqldata."";

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (a.name LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR a.in_datetime LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR a.address_in LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR a.Image_in LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR a.out_datetime LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR b.name LIKE '".$requestData['search']['value']."%' OR b.oth_empid LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR a.address_out LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR a.Image_out LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("travel-grid-data.php: ERROR-2");
$totalFiltered = mysqli_num_rows($query); 
// when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']." ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("travel-grid-data.php: ERROR-3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
  
      $imgin="";
      if ($row['Image_in'] != '') 
      {
        $imgin ='<img src="../salesapi/travelimg/'.substr($row["insert_date"],0,7).'/'.$row['Image_in'].'"alt="" id="image'.$i.'" onClick="getThisValue('.$i.')" style="width: 100%;"/>';
      }
      

      $imgout="";
      if ($row['Image_out'] != '') 
      {
        $imgout ='<img src="../salesapi/travelimg/'.substr($row["insert_date"],0,7).'/'.$row['Image_out'].'"alt="" id="image1'.$i.'" onClick="getThisValue('.$i.')" style="width: 100%;"/>';
      }

	
     
	// $nestedData[] = $j; 
	$nestedData[] = $row['name']."| ".$row['user_id']." |".$row['oth_empid'];
    $nestedData[] = getAnyDetails($row["department"],"dname","departmentid","hrms_department_master",$link1);
    $nestedData[] = getAnyDetails($row["subdepartment"],"subdept","subdeptid","hrms_subdepartment_master",$link1);
	$nestedData[] = $row["in_datetime"];
	$nestedData[] = wordwrap($row["address_in"], 30, "<br>", 1);
    $nestedData[] = $imgin;
    $nestedData[] = $row["out_datetime"];
	$nestedData[] = wordwrap($row['address_out'], 30, "<br>", 1);
	$nestedData[] = $imgout;
	
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
