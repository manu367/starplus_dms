<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$from_date=base64_decode($_REQUEST['fdate']);
$to_date=base64_decode($_REQUEST['tdate']);
$from_loc=base64_decode($_REQUEST['loc']);
$vendordetail=base64_decode($_REQUEST['ven']);;
//$product=base64_decode($_REQUEST[pro]);
$alllocation=getAccessLocation($_SESSION['userid'],$link1);
/*if($product=='' )
{
	$product_code='1';
}

else
{
	$product_code="(prod_code='".$product."') ";
}

*/
if($from_loc=='' )
{
	$from_party="to_location in (".$alllocation.")";
}

else
{
	$from_party="(to_location='".$from_loc."') ";
}
if($vendordetail=='' )
{
	$to_party='1';
}

else
{
	$to_party="(from_location='".$vendordetail."') ";
}



if($from_date=='' || $to_date=='')
{
	$sql_date='1';
}

else
{
	$sql_date="(entry_date>='".$from_date."' and entry_date<='".$to_date."')";
}

//////End filters value/////

$sql=mysqli_query($link1,"SELECT * FROM billing_master WHERE ".$from_party." AND ".$to_party." AND ".$sql_date." AND type IN ('CLP','LP','DIRECT SALE RETURN','GRN','STN')")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>GRN No.</strong></td>
<td><strong>GRN Date</strong></td>
<td><strong>Type</strong></td>
<td><strong>Document Type</strong></td>
<td><strong>VPO No.</strong></td>
<td><strong>Vendor Invoice Number</strong></td>
<td><strong>Vendor Invoice Date</strong></td>
<td><strong>Vendor Name</strong></td>
<td><strong>Vendor Code</strong></td>
<td><strong>Location Name</strong></td>
<td><strong>Location Code</strong></td>
<td><strong>Go-down</strong></td>
<td><strong>Sub Total</strong></td>
<td><strong>Cgst Amt</strong></td>
<td><strong>Sgst Amt</strong></td>
<td><strong>Igst Amt</strong></td>
<td><strong>Total Value</strong></td>
<td><strong>Round Off</strong></td>
<td><strong>TCS(%)</strong></td>
<td><strong>TCS Amt</strong></td>
<td><strong>TDS(194Q)</strong></td>
<td><strong>Status</strong></td>
<td><strong>Remark</strong></td>
<td><strong>Serial Attach</strong></td>
<td><strong>Dispatch Address</strong></td>
<td><strong>Delivery Address</strong></td>
<td><strong>Receive By</strong></td>
<td><strong>Receive Date</strong></td>
<td><strong>Receive Remark</strong></td>

</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
	$location=explode("~",getLocationDetails($row_loc['to_location'],"name,city,state",$link1));
	/// sub location
	if($row_loc["receive_sub_location"]){
		$subloc=getLocationDetails($row_loc['receive_sub_location'],"name,city,state",$link1);
		$explodevalf=explode("~",$subloc);
		if($explodevalf[0]){ $sublocname=$explodevalf[0]; }else{ $sublocname=getAnyDetails($row_loc['receive_sub_location'],"sub_location_name","sub_location","sub_location_master",$link1);}  
	}else{
		$subloc=getLocationDetails($row_loc['sub_location'],"name,city,state",$link1);
		$explodevalf=explode("~",$subloc);
		if($explodevalf[0]){ $sublocname=$explodevalf[0]; }else{ $sublocname=getAnyDetails($row_loc['sub_location'],"sub_location_name","sub_location","sub_location_master",$link1);}  
	}   
 ?>
<tr>
<td align="center"><?=$i?></td>
<td align="left"><?=$row_loc['challan_no']?></td>
<td align="left"><?=$row_loc['sale_date']?></td>
<td align="left"><?=$row_loc['type']?></td>
<td align="left"><?=$row_loc['document_type']?></td>
<td align="left"><?=$row_loc['ref_no']?></td>
<td align="left"><?=$row_loc['inv_ref_no']?></td>
<td align="left"><?=$row_loc['po_inv_date']?></td>
<td align="left"><?=getAnyParty($row_loc['from_location'],$link1)?></td>
<td align="left"><?=$row_loc['from_location']?></td>
<td align="left"><?=$location[0].",".$location[1].",".$location[2]?></td>
<td align="left"><?=$row_loc['to_location']?></td>
<td align="left"><?php if($sublocname){ echo $sublocname;}else{ echo $locdet[0].",".$locdet[1].",".$locdet[2];}?></td>
<td align="right"><?=$row_loc['basic_cost']?></td>
<td align="right"><?=$row_loc['total_cgst_amt']?></td>
<td align="right"><?=$row_loc['total_sgst_amt']?></td>
<td align="right"><?=$row_loc['total_igst_amt']?></td>
<td align="right"><?=$row_loc['total_cost']?></td>
<td align="right"><?=$row_loc['round_off']?></td>
<td align="right"><?=$row_loc['tcs_per']?></td>
<td align="right"><?=$row_loc['tcs_amt']?></td>
<td align="right"><?=$row_loc['tds']?></td>
<td align="left"><?=$row_loc['status']?></td>
<td align="left"><?=$row_loc['billing_rmk']?></td>
<td align="center"><?=$row_loc['imei_attach']?></td>

<td align="left"><?=$row_loc['disp_addrs']?></td>
<td align="left"><?=$row_loc['deliv_addrs']?></td>
<td align="left"><?=getAdminDetails($row_loc['receive_by'],"name",$link1)?></td>
<td align="left"><?=$row_loc['receive_date']?></td>
<td align="left"><?=$row_loc['receive_remark']?></td>

</tr>
<?php
$i+=1;		
}
?>
</table>