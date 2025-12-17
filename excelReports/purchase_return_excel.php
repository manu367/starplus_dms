<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$from_date = base64_decode($_REQUEST['fdate']);
$to_date = base64_decode($_REQUEST['tdate']);
$from_loc = base64_decode($_REQUEST['floc']);
$to_loc = base64_decode($_REQUEST['tloc']);

$locstr = getAccessLocation($_SESSION['userid'],$link1);
////// product category

if($from_loc==''){  
	$from_party="a.from_location in (".$locstr.")";
}else{
	$from_party="(a.from_location='".$from_loc."') ";
}

if($to_loc==''){
	$to_party="b.to_location in (".$locstr.")";
}else{
	$to_party="(b.to_location='".$to_loc."') ";
}

if($from_date=='' || $to_date==''){
	$sql_date='1';
}else{
	$sql_date="(b.sale_date>='".$from_date."' and b.sale_date<='".$to_date."')";
}

//////End filters value/////
$sql=mysqli_query($link1,"Select * from billing_model_data a inner join billing_master b on a.challan_no=b.challan_no where $from_party and $to_party  and $sql_date ")or die("er1".mysqli_error($link1));
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
<td><strong>Discount</strong></td>
<td><strong>IGST%</strong></td>
<td><strong>IGST AMT</strong></td>
<td><strong>CGST%</strong></td>
<td><strong>CGST AMT</strong></td>
<td><strong>SGST%</strong></td>
<td><strong>SGST AMT</strong></td>
<td><strong>Value</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
$product=explode("~",getProductDetails($row_loc['prod_code'],"productname,productdesc",$link1));
$f_location=explode("~",getLocationDetails($row_loc['from_location'],"name,city,state",$link1));
$t_location=explode("~",getLocationDetails($row_loc['to_location'],"name,city,state",$link1));
$admin_detail=explode("~",getAdminDetails($row_loc['entry_by'],"name",$link1));
 ?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$f_location[0].",".$f_location[1].",".$f_location[2]?></td>
<td align="left"><?=$row_loc['from_location']?></td>
<td align="left"><?=$t_location[0].",".$t_location[1].",".$t_location[2]?></td>
<td align="left"><?=$row_loc['to_location']?></td>
<td align="left"><?=$row_loc['challan_no']?></td>
<td align="center"><?=$row_loc['sale_date']?></td>
<td align="left"><?=$admin_detail[0]?></td>
<td align="left"><?=$product[0]?></td>
<td align="left"><?=$product[1]?></td>
<td align="right"><?=$row_loc['qty']?></td>
<td align="right"><?=$row_loc['okqty']?></td>
<td align="right"><?=$row_loc['damageqty']?></td>
<td align="right"><?=$row_loc['missingqty']?></td>
<td align="right"><?=$row_loc['price']?></td>
<td align="right"><?=$row_loc['discount']?></td>
<td align="right"><?=$row_loc['igst_per']?></td>
<td align="right"><?=$row_loc['igst_amt']?></td>
<td align="right"><?=$row_loc['cgst_per']?></td>
<td align="right"><?=$row_loc['cgst_amt']?></td>
<td align="right"><?=$row_loc['sgst_per']?></td>
<td align="right"><?=$row_loc['sgst_amt']?></td>
<td align="right"><?=$row_loc['totalvalue']?></td>

</tr>
<?php
$i+=1;		
}
?>
</table>