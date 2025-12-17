<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$from_date = base64_decode($_REQUEST['fdate']);
$to_date = base64_decode($_REQUEST['tdate']);
$from_loc = base64_decode($_REQUEST['floc']);
$to_loc = base64_decode($_REQUEST['tloc']);
$typ = base64_decode($_REQUEST['type']);

$locstr = getAccessLocation($_SESSION['userid'],$link1);

if($from_loc==''){ 
	if($typ=="PR"){ 
		$from_party="b.po_from in (".$locstr.")";
	}else{
		$from_party= " 1 ";
	}
}else{
	$from_party="(b.po_from='".$from_loc."') ";
}

if($to_loc==''){
	if($typ=="PR"){
		$to_party="b.po_to in (".$locstr.")";
	}else{
		$to_party=" 1 ";
	}
}else{
	$to_party="(b.po_to='".$to_loc."') ";
}

if($from_date=='' || $to_date==''){
	$sql_date='1';
}else{
	$sql_date="(b.requested_date>='".$from_date."' and b.requested_date<='".$to_date."')";
}

//////End filters value/////
$sql=mysqli_query($link1,"Select * from vendor_order_data a inner join vendor_order_master b on a.po_no=b.po_no where $from_party and $to_party  and $sql_date and b.req_type ='VRN' ")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>From Location</strong></td>
<td><strong>From Location Code</strong></td>
<td><strong>To Location</strong></td>
<td><strong>To Location Code</strong></td>
<td><strong>Invoice No.</strong></td>
<td><strong>Invoice Date</strong></td>
<td><strong>Entry By</strong></td>
<td><strong>Product Code</strong></td>
<td><strong>Product Description</strong></td>
<td><strong>Bill Qty</strong></td>
<td><strong>Receive OkQty</strong></td>
<td><strong>Receive DamageQty</strong></td>
<td><strong>Receive MissingQty</strong></td>
<td><strong>Price</strong></td>
<td><strong>SGST Per.</strong></td>
<td><strong>SGST AMT.</strong></td>
<td><strong>CGST Per.</strong></td>
<td><strong>CGST AMT.</strong></td>
<td><strong>IGST Per.</strong></td>
<td><strong>IGST AMT.</strong></td>
<td><strong>Tax %</strong></td>
<td><strong>Tax Amt.</strong></td>
<td><strong>Value</strong></td>
<td><strong>UOM</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
$product=explode("~",getProductDetails($row_loc['prod_code'],"productname,productdesc",$link1));
if($typ=="PR"){
	$f_location=explode("~",getLocationDetails($row_loc['po_from'],"name,city,state",$link1));
}else{
	$f_location=explode("~",getAnyDetails($row_loc['po_from'],"name,city,state","id","vendor_master",$link1));
}
$t_location=explode("~",getLocationDetails($row_loc['po_to'],"name,city,state",$link1));
$admin_detail=explode("~",getAdminDetails($row_loc['create_by'],"name",$link1));
 ?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$f_location[0].",".$f_location[1].",".$f_location[2]?></td>
<td align="left"><?=$row_loc['po_from']?></td>
<td align="left"><?=$t_location[0].",".$t_location[1].",".$t_location[2]?></td>
<td align="left"><?=$row_loc['po_to']?></td>
<td align="left"><?=$row_loc['po_no']?></td>
<td align="center"><?=$row_loc['invoice_date']?></td>
<td align="left"><?=$admin_detail[0]?></td>
<td align="left"><?=$product[0]?></td>
<td align="left"><?=$product[1]?></td>
<td align="right"><?=$row_loc['req_qty']?></td>
<td align="right"><?=$row_loc['okqty']?></td>
<td align="right"><?=$row_loc['damageqty']?></td>
<td align="right"><?=$row_loc['missingqty']?></td>
<td align="right"><?=$row_loc['po_price']?></td>
<td align="right"><?=$row_loc['sgst_per']?></td>
<td align="right"><?=$row_loc['sgst_amt']?></td>
<td align="right"><?=$row_loc['cgst_per']?></td>
<td align="right"><?=$row_loc['cgst_amt']?></td>
<td align="right"><?=$row_loc['igst_per']?></td>
<td align="right"><?=$row_loc['igst_amt']?></td>
<td align="right"><?=$row_loc['taxper']?></td>
<td align="left"><?=$row_loc['taxamt']?></td>
<td align="right"><?=$row_loc['totalval']?></td>
<td align="left"><?=$row_loc['uom']?></td>

</tr>
<?php
$i+=1;		
}
?>
</table>