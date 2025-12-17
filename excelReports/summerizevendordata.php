<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$from_date=base64_decode($_REQUEST[fdate]);
$to_date=base64_decode($_REQUEST[tdate]);
$from_loc=base64_decode($_REQUEST[loc]);
$vendordetail=base64_decode($_REQUEST[ven]);
$product=base64_decode($_REQUEST[pro]);
$alllocation=getAccessLocation($_SESSION[userid],$link1);
if($product=='' )
{
	$product_code='1';
}

else
{
	$product_code="(a.prod_code='".$product."') ";
}

if($from_loc=='' )
{
	$from_party="b.po_from in (".$alllocation.")";
}

else
{
	$from_party="(b.po_from='".$from_loc."') ";
}
if($vendordetail=='' )
{
	$to_party='1';
}

else
{
	$to_party="(b.po_to='".$vendordetail."') ";
}



if($from_date=='' || $to_date=='')
{
	$sql_date='1';
}

else
{
	$sql_date="(b.requested_date>='".$from_date."' and b.requested_date<='".$to_date."')";
}

//////End filters value/////

$sql=mysqli_query($link1,"Select * from vendor_order_data a inner join vendor_order_master b on a.po_no=b.po_no where $from_party and $to_party  and $sql_date and $product_code   ")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Type</strong></td>
<td><strong>Document No.</strong></td>
<td><strong>Document Date</strong></td>
<td><strong>Vendor Name</strong></td>
<td><strong>Vendor Code</strong></td>
<td><strong>Location Name</strong></td>
<td><strong>Location Code</strong></td>
<td><strong>Go-Down</strong></td>
<td><strong>Sub Total</strong></td>
<td><strong>CGST Amt</strong></td>
<td><strong>SGST Amt</strong></td>
<td><strong>IGST Amt</strong></td>
<td><strong>Total Amt</strong></td>
<td><strong>Status</strong></td>
<td><strong>Vendor Invoice Number</strong></td>
<td><strong>Receive Date</strong></td>
<td><strong>Receive By</strong></td>
<td><strong>Receive Remark</strong></td>
<td><strong>Delivery Address</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
	$location=explode("~",getLocationDetails($row_loc['po_from'],"name,city,state",$link1));
	$product_name=explode("~",getProductDetails($row_loc['prod_code'],"productname",$link1));
	$admin_detail=explode("~",getAdminDetails($row_loc['receive_by'],"name",$link1));
	/// sub location
    $subloc=getLocationDetails($row_loc['sub_location'],"name,city,state",$link1);
    $explodevalf=explode("~",$subloc);
    if($explodevalf[0]){ $sublocname=$explodevalf[0]; }else{ $sublocname=getAnyDetails($row_loc['sub_location'],"sub_location_name","sub_location","sub_location_master",$link1);}
?>
<tr>
<td align="center"><?=$i?></td>
<td align="left"><?=$row_loc['req_type']?></td>
<td align="left"><?=$row_loc['po_no']?></td>
<td align="center"><?=$row_loc['requested_date']?></td>
<td align="left"><?=getAnyParty($row_loc['po_to'],$link1)?></td>
<td align="left"><?=$row_loc['po_to']?></td>
<td align="left"><?=$location[0].",".$location[1].",".$location[2]?></td>
<td align="left"><?=$row_loc['po_from']?></td>
<td align="left"><?php if($sublocname){ echo $sublocname;}else{ echo $locdet[0].",".$locdet[1].",".$locdet[2];}?></td>
<td align="right"><?=$row_loc['po_value']?></td>
<td align="right"><?=$row_loc['total_cgst_amt']?></td>
<td align="right"><?=$row_loc['total_sgst_amt']?></td>
<td align="right"><?=$row_loc['total_igst_amt']?></td>
<td align="right"><?=$row_loc['grand_total']?></td>
<td align="left"><?=$row_loc['status']?></td>
<td align="left"><?=$row_loc['invoice_no']?></td>
<td align="center"><?=$row_loc['receive_date']?></td>
<td align="left"><?=$admin_detail[0]?></td>
<td align="left"><?=$row_loc['receive_remark']?></td>
<td align="left"><?=$row_loc['delivery_address']?></td>

</tr>
<?php
$i+=1;		
}
?>
</table>