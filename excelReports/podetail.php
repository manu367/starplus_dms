<?php 
print("\n");
print("\n");

//// extract all encoded variables
$from_date=base64_decode($_REQUEST['fdate']);
$to_date=base64_decode($_REQUEST['tdate']);
$from_stat=base64_decode($_REQUEST['from_stat']);
$from_statcod=base64_decode($_REQUEST['from_statcod']);
$from_state = $from_stat."~".$from_statcod;
$po_from=base64_decode($_REQUEST['po_from']);
$po_to=base64_decode($_REQUEST['po_to']);
$product=base64_decode($_REQUEST['pro']);
$status=base64_decode($_REQUEST['sta']);
$psc=base64_decode($_REQUEST['product_subcat']);
$potype=base64_decode($_REQUEST['po_type']);
$rsmid=base64_decode($_REQUEST['rsm_id']);
/////// access role
$rolestr = getAccessRole($_SESSION['userid'],$link1);
///get access state code which is used in location code string
$accessstatecode = getAccessStateCode($_SESSION['userid'],$link1);
////// get access location
if($rsmid!=""){
	$child = getHierarchyStr($rsmid, $link1, "");
	$rsm = " AND (b.create_by = '".$rsmid."' OR b.create_by IN ('".str_replace(",","','",$child)."'))";
}else{
	//if($_SESSION["userid"]=="admin"){
		$rsm = "";
	//}else{
		//$child = getHierarchyStr($_SESSION["userid"], $link1, "");
		//$rsm = " AND b.create_by IN ('".str_replace(",","','",$child)."')";
	//}
}
if($_SESSION["userid"]=="admin"){                              
	if($from_stat){ 
		$pst_state = explode("~",$from_state); 
		$statt = " AND SUBSTRING(b.po_from, 5, 2) = '".$pst_state[1]."'";
	}else{ 
		$stat = "";
		$statt = "";
	}
}else{
	if($from_stat){ 
		$pst_state = explode("~",$from_state); 
		$statt = " AND SUBSTRING(b.po_from, 5, 2) = '".$pst_state[1]."'";
	}else{ 
		//$accessstate = getAccessState($_SESSION['userid'],$link1);
		$statt = " AND SUBSTRING(b.po_from, 5, 2) IN (".$accessstatecode.")";
	}
}
////// filters value/////
if($status==''){ 
	$status='1'; 
} else{ 
	$status="(b.status='".$status."') ";
}
if($product==''){ 
	$product_code='1'; 
} else{ 
	$product_code="(a.prod_code='".$product."') ";
}
if($po_from==''){ 
	if($_SESSION["userid"]=="admin"){
		$from_party = "1";
	}else{
		//$location = getAccessLocation($_SESSION['userid'],$link1);
		//$from_party="b.po_from in (".$location.")"; 
		$from_party = " SUBSTRING(b.po_from, 3, 2) IN (".$rolestr.")";
	}
	
} else{ 
	$from_party="(b.po_from='".$po_from."') "; 
}
if($po_to==''){ 
	$to_party="1"; 
} else{ 
	$to_party="(b.po_to='".$po_to."') "; 
}
if($from_date=='' || $to_date==''){ 
	$sql_date='1'; 
}else{ 
	$sql_date="(b.requested_date>='".$from_date."' and b.requested_date<='".$to_date."')";
}
if($potype){ 
	$sale_type = " AND a.sale_type='".$potype."'";
}else{ 
	$sale_type = "";
}
if($psc){ 
	$psc_cat = " AND a.psc_id='".$psc."'";
}else{ 
	$psc_cat = "";
}
//////End filters value/////
//if($_SESSION["userid"]=="EAUSR659"){
//echo "SELECT * FROM purchase_order_data a INNER JOIN purchase_order_master b ON a.po_no=b.po_no WHERE ".$from_party." AND ".$to_party." AND ".$sql_date." AND ".$product_code." AND ".$status." ".$sale_type." ".$psc_cat." ".$statt;
//}
$sql = mysqli_query($link1,"SELECT * FROM purchase_order_data a INNER JOIN purchase_order_master b ON a.po_no=b.po_no WHERE ".$from_party." AND ".$to_party." AND ".$sql_date." AND ".$product_code." AND ".$status." ".$sale_type." ".$psc_cat." ".$statt." ".$rsm)or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>

<td><strong>PO From Code</strong></td>
<td><strong>PO From Name</strong></td>
<td><strong>PO From City</strong></td>
<td><strong>PO From State</strong></td>
<td><strong>PO From Phone</strong></td>

<td><strong>PO To Code</strong></td>
<td><strong>PO To Name</strong></td>
<td><strong>PO To City</strong></td>
<td><strong>PO To State</strong></td>
<td><strong>PO To Address</strong></td>
<td><strong>PO To Phone</strong></td>

<td><strong>PO No.</strong></td>
<td><strong>PO Date</strong></td>
<td><strong>PO Type</strong></td>
<td><strong>Create By</strong></td>
<td><strong>Create Date</strong></td>
<td><strong>Sales Person</strong></td>
<td><strong>Sales Person Id</strong></td>
<td><strong>Reporting Person</strong></td>
<td><strong>Invoice No.</strong></td>
<td><strong>Invoice Date</strong></td>
<td><strong>Product Code</strong></td>
<td><strong>Product Category</strong></td>
<td><strong>Product Sub Category</strong></td>
<td><strong>Brand</strong></td>
<td><strong>Product Name</strong></td>
<td><strong>Require Qty</strong></td>
<td><strong>Dispatch qty</strong></td>
<td><strong>Price</strong></td>
<td><strong>Value</strong></td>
<td><strong>Status</strong></td>
<td><strong>Aging</strong></td>
<td><strong>TAT</strong></td>
<td><strong>Remark</strong></td>
<td><strong>Remark 2</strong></td>
<td><strong>Delivery Address</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
$other=explode("~",getLocationDetails($row_loc['po_to'],"name,city,state,addrs,phone",$link1));
$location=explode("~",getLocationDetails($row_loc['po_from'],"name,city,state,phone",$link1));
$product_name=explode("~",getProductDetails($row_loc['prod_code'],"productname,productcolor,eol,productcategory,productsubcat,brand",$link1));
$value=$row_loc['po_price']*$row_loc['req_qty'];
//// create by
$crtby = explode("~",getAnyDetails($row_loc['create_by'],"name,reporting_manager","username","admin_users",$link1));
if($crtby[1]){
	$manager = getAnyDetails($crtby[1],"name","username","admin_users",$link1);
}else{
	$manager = "";
}
?>
<tr>
<td align="left"><?=$i?></td>

<td align="left"><?=$row_loc['po_from']?></td>
<td align="left"><?=$location[0]?></td>
<td align="left"><?=$location[1]?></td>
<td align="left"><?=$location[2]?></td>
<td align="right"><?=$location[3]?></td>

<td align="left"><?=$row_loc['po_to']?></td>
<td align="left"><?=$other[0]?></td>
<td align="left"><?=$other[1]?></td>
<td align="left"><?=$other[2]?></td>
<td align="left"><?=$other[3]?></td>
<td align="right"><?=$other[4]?></td>

<td align="left"><?=$row_loc['po_no']?></td>
<td align="left"><?=$row_loc['requested_date']?></td>
<td align="left"><?=$row_loc['sale_type']?></td>
<td align="left"><?=$crtby[0].",".$row_loc['create_by']?></td>
<td align="left"><?=$row_loc['entry_date']?></td>
<td align="left"><?=$row_loc['sales_person']?></td>
<td align="left"><?=$row_loc['sales_executive']?></td>
<td align="left"><?=$manager.",".$crtby[1]?></td>
<td align="left"><?=$row_loc['dispatch_challan']?></td>
<td align="left"><?=$row_loc['challan_date']?></td>
<td align="left"><?=$row_loc['prod_code']?></td>
<td align="left"><?=getAnyDetails($product_name[3],"cat_name","catid","product_cat_master",$link1);?></td>
<td align="left"><?=getAnyDetails($product_name[4],"prod_sub_cat","psubcatid","product_sub_category",$link1);?></td>
<td align="left"><?=getAnyDetails($product_name[5],"make","id","make_master",$link1);?></td>
<td align="left"><?=$product_name[0]?></td>
<td align="right"><?=$row_loc['req_qty']?></td>
<td align="right"><?=$row_loc['qty']?></td>
<td align="right"><?=$row_loc['po_price']?></td>
<td align="right"><?=$value?></td>
<td align="left"><?=$row_loc['status']?></td>
<td align="right"><?php if($row_loc['challan_date']=="0000-00-00"){ echo daysDifference($today,$row_loc['requested_date']);}?></td>
<td align="right"><?php if($row_loc['challan_date']!="0000-00-00"){ echo daysDifference($row_loc['challan_date'],$row_loc['requested_date']);}?></td>
<td align="left"><?=$row_loc['remark']?></td>
<td align="left"><?php if($row_loc['temp_no']=="1"){ echo "New Order";}?></td>
<td align="left"><?=cleanData($row_loc['delivery_address'])?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>
