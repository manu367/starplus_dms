<?php 
print("\n");
print("\n");
////// filters value/////
$locationstate=base64_decode($_REQUEST[locstate]);
$product=base64_decode($_REQUEST[product]);
$locationtype=base64_decode($_REQUEST[loctype]);

## selected state
if($locationstate!=""){
	$loc_state="state='".$locationstate."'";
}else{
	$loc_state="1";
}
## selected city
if($product!=""){
	$productprice="product_code='".$product."'";
}else{
	$productprice="1";
}
## selected location type
if($locationtype!=""){
	$loc_type="location_type='".$locationtype."'";
}else{
	$loc_type="1";
}


$sql=mysqli_query($link1,"Select * from price_master where $loc_state and $loc_type and $productprice ")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>State</strong></td>
<td><strong>Location Type</strong></td>
<td><strong>Product</strong></td>
<td><strong>Product Mrp</strong></td>
<td><strong>Product Price</strong></td>
<td><strong>Status</strong></td>

</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['state']?></td>
<td align="left"><?=getLocationType($row_loc['location_type'],$link1)?></td>
<td align="left"><?=getProduct($row_loc['product_code'],$link1)?></td>
<td align="left"><?=$row_loc['mrp']?></td>
<td align="left"><?=$row_loc['price']?></td>

<td align="left"><?=$row_loc['status']?></td>

</tr>
<?php
$i+=1;		
}
?>
</table>