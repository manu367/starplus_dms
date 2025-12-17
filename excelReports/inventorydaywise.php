<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$stockdate=base64_decode($_REQUEST['stockdate']);
$location=base64_decode($_REQUEST['loc']);
$product_cat=base64_decode($_REQUEST['product_cat']);
$product_subcat=base64_decode($_REQUEST['product_subcat']);
$brand=base64_decode($_REQUEST['brand']);
$partcode=base64_decode($_REQUEST['partcode']);
$go_down=base64_decode($_REQUEST['godown']);
## selected location
if($location!=""){
	$loc="a.asc_code='".$location."'";
}else{
	$locstr=getAccessLocation($_SESSION['userid'],$link1);
	$loc="a.asc_code in (".$locstr.")";
}
## selected Product Category
if($product_cat!=""){
	$pcat_s= " b.productcategory='".$product_cat."'";
}else{
	$pcat_s = " 1";
}
## selected Product Sub Category
if($product_subcat!=""){
	$pscat_s = " b.productsubcat='".$product_subcat."'";
}else{
	$pscat_s = " 1";
}
## selected brand
if($brand!=""){
	$brd_s = " b.brand='".$brand."'";
}else{
	$brd_s = " 1";
}
## selected product id
if($partcode!=""){
	$part_code = " b.productcode='".$partcode."'";
}else{
	$part_code = " 1";
}
if($go_down!=""){
	$godown = " a.sub_location='".$go_down."'";
}else{
	$godown = " 1";
}
//////End filters value/////
$seldate = date('Y-m-d', strtotime("+1 day", strtotime($stockdate)));
if($stockdate!="" && $stockdate!=$today){
	$sql=mysqli_query($link1,"Select a.asc_code, a.sub_location, a.partcode, a.okqty, a.broken, a.missing FROM `stock_status".$seldate."` a, product_master b WHERE a.partcode=b.productcode AND ".$loc." AND ".$pcat_s." AND ".$pscat_s." AND ".$brd_s." AND ".$part_code." AND ".$godown)or die("er1".mysqli_error($link1));
}else{
	$sql=mysqli_query($link1,"Select a.asc_code, a.sub_location, a.partcode, a.okqty, a.broken, a.missing FROM `stock_status` a, product_master b WHERE a.partcode=b.productcode AND ".$loc." AND ".$pcat_s." AND ".$pscat_s." AND ".$brd_s." AND ".$part_code." AND ".$godown)or die("er1".mysqli_error($link1));
}
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Location Code</strong></td>
<td><strong>Location Name</strong></td>
<td><strong>City</strong></td>
<td><strong>State</strong></td>
<td><strong>Sub Location</strong></td>
<td><strong>Product Code</strong></td>
<td><strong>Product Description</strong></td>
<td><strong>Model</strong></td>
<td><strong>Product Category</strong></td>
<td><strong>Product Sub Category</strong></td>
<td><strong>Brand Name</strong></td>
<td><strong>Ok</strong></td>
<td><strong>Damage</strong></td>
<td><strong>Missing</strong></td>
<td><strong>Total</strong></td>
<td><strong>Price</strong></td>
<td><strong>Value</strong></td>
</tr>
<?php
$i=1;
$new_loc = "";
$old_loc = "";
while($row_loc = mysqli_fetch_array($sql)){
	$new_loc = $row_loc['asc_code'];
	if($old_loc != $new_loc){
		$locdet=explode("~",getLocationDetails($row_loc['asc_code'],"name,city,state,id_type",$link1));
	}
	/// sub location
    $subloc=getLocationDetails($row_loc['sub_location'],"name,city,state",$link1);
    $explodevalf=explode("~",$subloc);
    if($explodevalf[0]){ $sublocname=$explodevalf[0]; }else{ $sublocname=getAnyDetails($row_loc['sub_location'],"sub_location_name","sub_location","sub_location_master",$link1);}
	$proddet=explode("~",getProductDetails($row_loc['partcode'],"productname,productcolor,model_name,productcategory,productsubcat,brand",$link1));
	$price = mysqli_fetch_assoc(mysqli_query($link1,"SELECT price FROM price_master where state='".$locdet[2]."' and location_type='".$locdet[3]."' and product_code='".$row_loc['partcode']."' and status='active'"));
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['asc_code']?></td>
<td align="left"><?=$locdet[0]?></td>
<td align="left"><?=$locdet[1]?></td>
<td align="left"><?=$locdet[2]?></td>
<td align="left"><?php if($sublocname){ echo $sublocname;}else{ echo $locdet[0].",".$locdet[1].",".$locdet[2];}?></td>
<td align="left"><?=$row_loc['partcode']?></td>
<td align="left"><?=$proddet[0]?></td>
<td align="left"><?=$proddet[2]?></td>
<td align="left"><?=getAnyDetails($proddet[3],"cat_name","catid","product_cat_master",$link1);?></td>
<td align="left"><?=getAnyDetails($proddet[4],"prod_sub_cat","psubcatid","product_sub_category",$link1);?></td>
<td align="left"><?=getAnyDetails($proddet[5],"make","id","make_master",$link1);?></td>
<td align="left"><?=$row_loc['okqty']?></td>
<td align="left"><?=$row_loc['broken']?></td>
<td align="left"><?=$row_loc['missing']?></td>
<td align="right"><?php echo $total = $row_loc['okqty']+$row_loc['broken']+$row_loc['missing'];?></td>
<td align="right"><?=number_format($price["price"],'2','.','')?></td>
<td align="right"><?=number_format($price["price"]*$total,'2','.','')?></td>
</tr>
<?php
$old_loc = $row_loc['asc_code'];
$i+=1;		
}
?>
</table>
