<?php 
print("\n");
print("\n");
//// extract all encoded variables
$from_date=base64_decode($_REQUEST['fdate']);
$to_date=base64_decode($_REQUEST['tdate']);
$userid=base64_decode($_REQUEST['userid']);
$psc=base64_decode($_REQUEST['psc']);
$sale_type=base64_decode($_REQUEST['sale_type']);
if($sale_type){
	if($sale_type=="P"){
		$saletype = " AND a.sale_type LIKE 'PRIMARY' AND b.status IN ('Processed') AND dispatch_challan!=''";
	}else if($sale_type=="S"){
		$saletype = " AND a.sale_type LIKE 'SECONDARY' AND b.status IN ('Approved','Processed')";
	}else{
		$saletype = " AND ((a.sale_type LIKE 'PRIMARY' AND b.status IN ('Processed') AND dispatch_challan!='') OR (a.sale_type LIKE 'SECONDARY' AND b.status IN ('Approved','Processed')))";
	}
}else{
	$saletype = " AND ((a.sale_type LIKE 'PRIMARY' AND b.status IN ('Processed') AND dispatch_challan!='') OR (a.sale_type LIKE 'SECONDARY' AND b.status IN ('Approved','Processed')))";
}
///// get team members
$team = getTeamMembers($userid,$link1);
if($team){
	$team .= $team.",'".$userid."'"; 
}else{
	$team .= "'".$userid."'"; 
}
/////
$sql=mysqli_query($link1,"SELECT a.prod_code, a.req_qty, b.* FROM purchase_order_data a, purchase_order_master b WHERE a.prod_cat!='C' AND a.po_no = b.po_no ".$saletype." AND b.create_by IN (".$team.") AND b.entry_date >= '".$from_date."' AND b.entry_date <= '".$to_date."' AND a.prod_code IN (SELECT productcode FROM product_master WHERE productsubcat IN (SELECT psubcatid FROM product_sub_category WHERE prod_sub_cat='".$psc."'))")or die("er1".mysqli_error($link1));
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
<td><strong>Total Value</strong></td>
<td><strong>Status</strong></td>
<td><strong>Product Description</strong></td>
<td><strong>Model Name</strong></td>
<td><strong>Product Code</strong></td>
<td><strong>PO Qty</strong></td>
<td><strong>Remark</strong></td>
<td><strong>Remark 2</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
$other=explode("~",getLocationDetails($row_loc['po_to'],"name,city,state,addrs,phone",$link1));
$location=explode("~",getLocationDetails($row_loc['po_from'],"name,city,state,phone",$link1));
//// create by
$crtby = explode("~",getAnyDetails($row_loc['create_by'],"name,reporting_manager","username","admin_users",$link1));
if($crtby[1]){
	$manager = getAnyDetails($crtby[1],"name","username","admin_users",$link1);
}else{
	$manager = "";
}
$product_name=explode("~",getProductDetails($row_loc['prod_code'],"productname,model_name",$link1));
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
<td align="right"><?=$row_loc['po_value']?></td>
<td align="left"><?=$row_loc['status']?></td>
<td align="left"><?=$product_name[0]?></td>
<td align="left"><?=$product_name[1]?></td>
<td align="left"><?=$row_loc['prod_code']?></td>
<td align="left"><?=$row_loc['req_qty']?></td>
<td align="left"><?=$row_loc['remark']?></td>
<td align="left"><?php if($row_loc['temp_no']=="1"){ echo "New Order";}?></td>
</tr>
<?php
$i+=1;		
}
$sql_combo=mysqli_query($link1,"SELECT a.prod_code, (a.req_qty*c.bom_qty) AS req_qty, b.* FROM purchase_order_data a, purchase_order_master b,combo_master c WHERE a.prod_cat='C' AND a.po_no = b.po_no ".$saletype." AND b.create_by IN (".$team.") AND b.entry_date >= '".$from_date."' AND b.entry_date <= '".$to_date."' AND a.prod_code=c.bom_modelcode AND c.status='1' AND c.bom_partcode IN (SELECT productcode FROM product_master WHERE productsubcat IN (SELECT psubcatid FROM product_sub_category WHERE prod_sub_cat='".$psc."'))")or die("er1".mysqli_error($link1));
$i=1;
while($row_loc_combo = mysqli_fetch_array($sql_combo)){
$other=explode("~",getLocationDetails($row_loc_combo['po_to'],"name,city,state,addrs,phone",$link1));
$location=explode("~",getLocationDetails($row_loc_combo['po_from'],"name,city,state,phone",$link1));
//// create by
$crtby = explode("~",getAnyDetails($row_loc_combo['create_by'],"name,reporting_manager","username","admin_users",$link1));
if($crtby[1]){
	$manager = getAnyDetails($crtby[1],"name","username","admin_users",$link1);
}else{
	$manager = "";
}
$data = getAnyDetails($row_loc_combo['prod_code'],"bom_modelname,bom_model,bom_hsn","bom_modelcode","combo_master",$link1); 
$d = explode('~', $data); 
?>
<tr>
<td align="left"><?=$i?></td>

<td align="left"><?=$row_loc_combo['po_from']?></td>
<td align="left"><?=$location[0]?></td>
<td align="left"><?=$location[1]?></td>
<td align="left"><?=$location[2]?></td>
<td align="right"><?=$location[3]?></td>

<td align="left"><?=$row_loc_combo['po_to']?></td>
<td align="left"><?=$other[0]?></td>
<td align="left"><?=$other[1]?></td>
<td align="left"><?=$other[2]?></td>
<td align="left"><?=$other[3]?></td>
<td align="right"><?=$other[4]?></td>

<td align="left"><?=$row_loc_combo['po_no']?></td>
<td align="left"><?=$row_loc_combo['requested_date']?></td>
<td align="left"><?=$row_loc_combo['sale_type']?></td>
<td align="left"><?=$crtby[0].",".$row_loc_combo['create_by']?></td>
<td align="left"><?=$row_loc_combo['entry_date']?></td>
<td align="left"><?=$row_loc_combo['sales_person']?></td>
<td align="left"><?=$row_loc_combo['sales_executive']?></td>
<td align="left"><?=$manager.",".$crtby[1]?></td>
<td align="left"><?=$row_loc_combo['dispatch_challan']?></td>
<td align="left"><?=$row_loc_combo['challan_date']?></td>
<td align="right"><?=$row_loc_combo['po_value']?></td>
<td align="left"><?=$row_loc_combo['status']?></td>
<td align="left"><?=$d[0]?></td>
<td align="left"><?=$d[1]?></td>
<td align="left"><?=$row_loc_combo['prod_code']?></td>
<td align="left"><?=$row_loc_combo['req_qty']?></td>
<td align="left"><?=$row_loc_combo['remark']?></td>
<td align="left"><?php if($row_loc_combo['temp_no']=="1"){ echo "New Order";}?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>
