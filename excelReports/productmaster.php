<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$brand = base64_decode($_REQUEST['brand']);
$product_cat = base64_decode($_REQUEST['product_cat']);
$product_sub_cat = base64_decode($_REQUEST['product_sub_cat']);
$product = base64_decode($_REQUEST['product']);

## selected brand
if($brand!=""){
	$pro_brand="brand='".$brand."'";
}else{
	$pro_brand="1";
}
## selected product cat
if($product_cat!=""){
	$pc = "productid='".$product_cat."'";
}else{
	$pc = "1";
}
## selected product sub cat
if($product_sub_cat!=""){
	$psc = "productsubcat='".$product_sub_cat."'";
}else{
	$psc = "1";
}
## selected product
if($product!=""){
	$product="productcode='".$product."'";
}else{
	$product="1";
}

//////End filters value/////
$sql=mysqli_query($link1,"SELECT * FROM product_master WHERE ".$pro_brand." AND ".$psc." AND ".$product." ORDER BY productname")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>System Generated Code</strong></td>
<td><strong>Product Name</strong></td>
<td><strong>Product Color</strong></td>
<td><strong>Product Description</strong></td>
<td><strong>Product Category</strong></td>
<td><strong>Product Sub Category</strong></td>
<td><strong>HSN Code</strong></td>
<td><strong>Brand</strong></td>
<td><strong>Model</strong></td>
<td><strong>IsSerialized</strong></td>
<td><strong>Division</strong></td>
<td><strong>Gross Weight</strong></td>
<td><strong>Net Weight</strong></td>
<td><strong>Scrap Weight</strong></td>
<td><strong>Pro Rata</strong></td>
<td><strong>Battery Rating</strong></td>
<td><strong>Warranty Days</strong></td>
<td><strong>Permissible Storage Period</strong></td>
<td><strong>Status</strong></td>
<td><strong>Created Date</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left" bgcolor="#FFFF99"><?=$row_loc['productcode']?></td>
<td align="left"><?=$row_loc['productname']?></td>
<td align="left"><?=$row_loc['productcolor']?></td>
<td align="left"><?=$row_loc['productdesc']?></td>
<td align="left"><?php echo getAnyDetails($row_loc['productcategory'],"cat_name","catid" ,"product_cat_master"  ,$link1);?></td>
<td align="left"><?php echo getAnyDetails($row_loc['productsubcat'],"prod_sub_cat","psubcatid" ,"product_sub_category"  ,$link1);?></td>
<td align="left"><?=$row_loc['hsn_code']?></td>
<td align="left"><?php echo getAnyDetails($row_loc['brand'],"make","id" ,"make_master"  ,$link1);?></td>
<td align="left"><?=$row_loc['model_name']?></td>
<td align="left"><?=$row_loc['is_serialize']?></td>
<td align="left"><?=$row_loc['division']?></td>
<td align="left"><?=$row_loc['weight']?></td>
<td align="left"><?=$row_loc['net_weight']?></td>
<td align="left"><?=$row_loc['scrap_weight']?></td>
<td align="left"><?=$row_loc['pro_rata']?></td>
<td align="left"><?=$row_loc['battery_rating']?></td>
<td align="left"><?=$row_loc['warranty_days']?></td>
<td align="left"><?=$row_loc['grace_period']?></td>
<td align="left"><?=$row_loc['status']?></td>
<td align="left"><?=$row_loc['createdate']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>
