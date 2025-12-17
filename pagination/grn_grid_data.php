<?php
/* Database connection start */
require_once("../config/config.php");
$_SESSION["messageIdent"]="";
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;
	//// get cancel rights
	$isCnlRight = getCancelRightNew($_SESSION['userid'],"1",$link1);
	///// get access location ///
	$accesslocation=getAccessLocation($_SESSION['userid'],$link1);		
////// filters value/////
$filter_str = "";
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND entry_date >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND entry_date <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['from_location'] !=''){
	$filter_str	.= " AND from_location = '".$_REQUEST['from_location']."'";
}
if($_REQUEST['to_location'] !=''){
	$filter_str	.= " AND to_location = '".$_REQUEST['to_location']."'";
}

$columns = array(
    // datatable column index  => database column name
    0 => 'id',
    1 => 'from_location',
    2 => 'to_location',
    3 => 'challan_no',
    4 => 'sale_date' 
);
// getting total number records without any search
if ($_SESSION["userid"] == "admin") {
    $sql = "SELECT id";
    $sql .= " FROM billing_master where to_location in (".$accesslocation.")  and type = 'GRN' ".$filter_str."";
} else {
    $sql = "SELECT id";
    $sql .= " FROM billing_master  where to_location in (".$accesslocation.")  and type = 'GRN' ".$filter_str."";
}
// echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("stock-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if ($_SESSION["userid"] == "admin") {
    $sql = "SELECT *";
    $sql .= " FROM billing_master where to_location in (".$accesslocation.")  and type = 'GRN' ".$filter_str."";
} else {
    $sql = "SELECT *";
    $sql .= " FROM billing_master where to_location in (".$accesslocation.")  and type = 'GRN' ".$filter_str."";
}
if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql .= " AND ( from_location LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR to_location LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR challan_no LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR sale_date LIKE '" . $requestData['search']['value'] . "%' )";
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
       $numrow = 0;
				  $rs12=mysqli_query($link1,"SELECT imei_attach, prod_code FROM billing_model_data WHERE challan_no='".$row['challan_no']."'");
				  $numrow = mysqli_num_rows($rs12);
				  $check=1;
				  while($row12=mysqli_fetch_array($rs12)){
					$get_result12 = explode("~",getAnyDetails($row12['prod_code'],"productcode,is_serialize","productcode" ,"product_master",$link1));
					if($get_result12[1]=='Y'){ if($row12['imei_attach']=="Y"){ $check*=1;}else{ $check*=0;}}else{ $check*=1;}
				  }

//tallysync//
$tallysync= "";
	if($row['post_in_tally2']=="Y"){
		$tallysync = '<i class="fa fa-refresh fa-lg text-success" aria-hidden="true" title="sync in tally"></i>';
	}else{
		$tallysync = '<i class="fa fa-ban fa-lg  text-danger" aria-hidden="true" title="not sync in tally"></i>';
	}
//tallysync//


//pod//

$pod="";
$pod1="";
$pod2="";
if($row['grn_doc']){
	$pod= "<a href='".$row['grn_doc']."'  title='view' target='_blank'><i class='fa fa-download fa-lg' title='view/download invoice'></i></a>";}

	$pod1="";
	if($row['pod1']){ 
		$pod1= "<a href='".$row['pod1']."'  title='view' target='_blank'><i class='fa fa-download fa-lg' title='view/download POD1'></i></a>";}

	$pod2="";
	if($row['pod2']){
		$pod2="<a href='".$row['pod2']."'  title='view' target='_blank'><i class='fa fa-download fa-lg' title='view/download POD2'></i></a>";
	}
//pod//


    // START PRINT //
    $print="";
    $print2="";
    if ($check == 1 || $row['status']=="Cancelled") {
        $print = "<a href='../print/grn_print.php?id=" . base64_encode($row['challan_no']) . "" . $pagenav . "  target='_blank' title='view'><i class='fa fa-print fa-lg' title='view details'></i></a>";

        if ($row['imei_attach'] == "Y") {
            $print2 = "&nbsp;&nbsp;<a href='../print/grnimei_print.php?id=" . base64_encode($row['challan_no']) . "" . $pagenav . "  target='_blank' title='view ".$imeitag."'><i class='fa fa-print fa-lg' title='view details'></i></a>";
         } else{
			$print2 = "";
		}
    }
    else {
        $print = "<b style='color:#F30'>Please Upload Serial No. for Serialized Product</b>";
        $print2 = "";
    }

    // END PRINT//
	
    //START VIEW//
    $view="";
    $view = "<div align='center'><a href='statusgrn_view.php?id=edit&id=" . base64_encode($row['challan_no']) . "" . $pagenav . "   title='view'><i class='fa fa-eye fa-lg' title='view details'></i></a></div>";
    //END VIEW//
  
    //START CANCEL//
    $cancel="";
    //if ($_SESSION['userid'] == 'admin') {
		 if($isCnlRight == 1){
        if ($row['status'] != 'Cancelled') {
            $cancel = "<div align='center'> <a href='cancelgrn.php?op=cancel&id=" . base64_encode($row['challan_no']) . "" . $pagenav . "'title='Cancel GRN'><i class='fa fa-trash fa-lg' title='Cancel GRN'></i></a></div>";
        }
    }
    // END CANCEL//

	
	
    
    $nestedData = array();
    $nestedData[] = $j;
	$nestedData[] = $tallysync;
    $nestedData[] = str_replace("~",",",getVendorDetails($row['from_location'],"name,city,state",$link1));
    $nestedData[] = str_replace("~",",",getLocationDetails($row['to_location'],"name,city,state",$link1));
	$nestedData[] = $row['challan_no']."<br/><i>(".$row['status'].")</i>";
	$nestedData[] = $row['inv_ref_no']."<br/>".$row['ref_no']."";
    $nestedData[] = dt_format($row['sale_date']);
    $nestedData[] = $pod.$pod1.$pod2;
	$nestedData[] = $view;
    $nestedData[] = $print.$print2;
    $nestedData[] = $cancel;

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
