<?php
/* Database connection start */
require_once("../config/config.php");
$_SESSION["messageIdentLP"]="";
$_SESSION['messageIdentCLP']="";
$_SESSION['messageIdentLPC']="";
$_SESSION['messageIdentLP1']="";
/* Database connection end */
// storing  request (i.e, get/post) global array to a variable  
$requestData = $_REQUEST;

////// filters value/////
$filter_str = "";
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND entry_date >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND entry_date <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['from_location'] !=''){
	$filter_str	.= " AND po_from = '".$_REQUEST['from_location']."'";
}
if($_REQUEST['to_location'] !=''){
	$filter_str	.= " AND po_to = '".$_REQUEST['to_location']."'";
}

$accesslocation = getAccessLocation($_SESSION['userid'], $link1);
// $cancel_right = mysqli_num_rows(mysqli_query($link1, "select id from access_cancel_rights where  uid='$_SESSION[userid]' and status='Y' and cancel_type='13'"));
$isCnlRight = getCancelRightNew($_SESSION['userid'],"2",$link1);

$columns = array(
    // datatable column index  => database column name
    0 => 'id',
    1 => 'po_from',
    2 => 'po_to',
    3 => 'po_no',
    4 => 'requested_date',
    5 => 'create_by',
    6 => 'status'
);
//////

// getting total number records without any search
if ($_SESSION['userid'] == "admin") {
    $sql = "SELECT id ";
    $sql .= " FROM vendor_order_master where po_to in (" . $accesslocation . ")  ".$filter_str."  ";
} else {
    $sql = "SELECT id ";
    $sql .= " FROM vendor_order_master where po_to in (" . $accesslocation . ")  ".$filter_str."  ";
}
//echo $sql;
// exit;
$query = mysqli_query($link1, $sql) or die("local-grid-data.php: ERROR! 1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if ($_SESSION['userid'] == "admin") {
    $sql = "SELECT * ";
    $sql .= " FROM vendor_order_master where po_to in (" . $accesslocation . ")  ".$filter_str."";
} else {
    $sql = "SELECT * ";
    $sql .= " FROM vendor_order_master where po_to in (" . $accesslocation . ")  ".$filter_str."";
}

if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql .= " AND (po_from LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR po_to LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR po_no LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR requested_date LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR create_by LIKE '" . $requestData['search']['value'] . "%'";
    $sql .= " OR status LIKE '" . $requestData['search']['value'] . "%' )";
}

$query = mysqli_query($link1, $sql) or die("local-grid-data.php: ERROR! 2");
//echo $sql;
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
//echo $sql;
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
$query = mysqli_query($link1, $sql) or die("local-grid-data.php: ERROR! 3");

$data = array();
$j = 1;
while ($row = mysqli_fetch_array($query)) { // preparing an array

    //// check serial no. is uploaded or not

    $rs12 = mysqli_query($link1, "SELECT imei_attach, prod_code FROM vendor_order_data WHERE po_no='" . $row['po_no'] . "'");
    $check = 1;
    while ($row12 = mysqli_fetch_array($rs12)) {
        $get_result12 = explode("~", getAnyDetails($row12['prod_code'], "productcode,is_serialize", "productcode", "product_master", $link1));
        if ($get_result12[1] == 'Y') {
            if ($row12['imei_attach'] == "Y") {
                $check *= 1;
            } else {
                $check *= 0;
            }
        } else {
            $check *= 1;
        }
    }

    $post_in_tally = getAnyDetails($row['po_no'],"post_in_tally2","challan_no" ,"billing_master",$link1);
   
//tallysync//
    $tallysync = "";
    if ($row['post_in_tally'] == "Y") {
        $tallysync = '<i class="fa fa-refresh fa-lg text-success" aria-hidden="true" title="sync in tally"></i>';
    } else {
        $tallysync = '<i class="fa fa-ban fa-lg  text-danger" aria-hidden="true" title="not sync in tally"></i>';
    }
//tallysync//

//status//
    $status = "";
    if ($row['status'] == "Pending") {
        $status = "<span class='red_small'>" . $row['status'] . "</span>";
    } else {
        $status = $row['status'];
    }
//status//

//scan// 
    $scan="";
    $scan1="";
    $scan2="";

    
     if($row['status']!="Cancelled"){
        if($row['imei_attach']==""){ 
            if($check==0){
              if($row["req_type"]=="LP"){ 

             $scan="<a href='poUploadImeiLP.php?id=" . base64_encode($row['po_no']) . $pagenav . "' title='" . $imeitag . "Attach'><i class='fa fa-upload fa-lg'></i></a>";
            }   
        else
        {
        $scan1="<a href='poUploadImei.php?id=" . base64_encode($row['po_no']) . $pagenav . "' title='" . $imeitag . "Attach'><i class='fa fa-upload fa-lg'></i></a>";
        }  
         $scan2= "<a href='poScanImei.php?id=" . base64_encode($row['po_no']) . "&invdate=" . base64_encode($row['requested_date']) . "&invloc=" . base64_encode($row['po_from']) . "&invto=" . base64_encode($row['po_to']) . $pagenav . "' title='" . $imeitag . "Scan'><i class='fa fa-qrcode fa-lg'></i></a>"; }
        else
        { 
            $scan1= "Not Applicable";
            $scan2= "";
        }
          }
        else
        { 
            $scan= "YES";
        }
    }
//scan//

//print//
  
    $print = "";
    $print1 = "";
    if ($check == 1 || $row['status']=="Cancelled") {
        $print = "<a href='../print/vendor_print_invoice.php?rb=view&id=" . base64_encode($row['po_no']) . $pagenav . "' target='_blank'  title='Print Invoice'><i class='fa fa-print fa-lg' title='Print Invoice'></i></a>";

        if ($row['imei_attach']) {
            $print1 = "&nbsp;&nbsp;<a href='../print/vendor_print_imei.php?rb=view&id=" . base64_encode($row['po_no']) . $pagenav . "' target='_blank'  title='Print " . $imeitag . "'><i class='fa fa-print fa-lg' title='Print " . $imeitag . "'></i></a>";
        } else {
            $print1 = "";
        }
    } else {
        $print = "<b style='color:#F30'>Please Upload Serial No. for Serialized Product</b>";
    }

//print//

//view//
    $view = "";
    $view = "<a href='localPurchaseDetails.php?op=edit&id=" . base64_encode($row['po_no']) . $pagenav . "' title='view '><i class='fa fa-eye fa-lg' title='View Details'></i></a>";
//view//

//receive//
    $receive = "";
    $receive1 = "";
    if ($row['status'] == "Pending") {
        if ($check == 1) {
            $receive = "<a href='recLocalPurchaseDetailsN.php?op=edit&id=" . base64_encode($row['po_no']) . $pagenav . "' title='Receive PO'><i class='fa fa-shopping-bag fa-lg' title='Receive PO'></i></a>";
        } else {
            $receive = "<b style='color:#F30'>Please Upload Serial No. for Serialized Product</b>";
        }
    }
//receive//

//cancel//
    $cancel = "";
    if($isCnlRight == 1){
        if ($row['status'] != 'Cancelled') {
            $cancel = "<a href='cancelvpoDetails.php?op=cancel&id=" . base64_encode($row['po_no']) . $pagenav . "' title='Cancel PO'><i class='fa fa-trash fa-lg' title='Cancel PO'></i></a>";
        }
    }
//cancel//

    $nestedData = array();
    $nestedData[] = $j;
    $nestedData[] = $tallysync;
    $nestedData[] = getAnyParty($row['po_from'],$link1);
    $nestedData[] = getAnyParty($row['po_to'],$link1);
    // $nestedData[] = getLocationDetails($row['po_to'],"name,city,state",$link1);
    $nestedData[] = $row["po_no"];
    $nestedData[] = $row["requested_date"];
    $nestedData[] = $row["invoice_no"];
    $nestedData[] = getAdminDetails($row['create_by'], "name", $link1);
    $nestedData[] = $status;
    $nestedData[] = $scan . $scan1;
    $nestedData[] = $print . $print1;
    $nestedData[] = $view;
    $nestedData[] = $receive . $receive1;
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
?>