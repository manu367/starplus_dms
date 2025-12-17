<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$location=base64_decode($_REQUEST['loc']);
$product_cat=base64_decode($_REQUEST['product_cat']);
$product_subcat=base64_decode($_REQUEST['product_subcat']);
$brand=base64_decode($_REQUEST['brand']);
$partcode=base64_decode($_REQUEST['partcode']);
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
//////End filters value/////

$sql=mysqli_query($link1,"Select a.asc_code, a.sub_location, a.partcode, a.okqty, a.broken, a.missing FROM stock_status a, product_master b WHERE a.partcode=b.productcode AND ".$loc." AND ".$pcat_s." AND ".$pscat_s." AND ".$brd_s." AND ".$part_code)or die("er1".mysqli_error($link1));

?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td rowspan="2"><strong>S.No.</strong></td>
<td rowspan="2"><strong>Location Name</strong></td>
<td rowspan="2"><strong>City</strong></td>
<td rowspan="2"><strong>State</strong></td>
<td rowspan="2"><strong>Sub Location</strong></td>
<td rowspan="2"><strong>Product Code</strong></td>
<td rowspan="2"><strong>Product Category</strong></td>
<td rowspan="2"><strong>Product Sub Category</strong></td>
<td rowspan="2"><strong>Brand</strong></td>
<td rowspan="2"><strong>Product Description</strong></td>
<td rowspan="2"><strong>Product Nature</strong></td>
<td height="25" colspan="3" align="center" style="background-color:#99FFFF;color:#000000"><strong>0 - 30 days</strong></td>
<td colspan="3" align="center" style="background-color:#FFFF99;color:#000000"><strong>31 - 90 days</strong></td>
<td colspan="3" align="center" style="background-color:#FF9999;color:#000000"><strong>Above 90 days</strong></td>
<td rowspan="2"><strong>Total Qty</strong></td>
<td rowspan="2"><strong>Price</strong></td>
<td rowspan="2"><strong>Value</strong></td>
</tr>
<tr align="center" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
  <td height="25" style="background-color:#99FFFF;color:#000000"><strong>OK</strong></td>
  <td style="background-color:#99FFFF;color:#000000"><strong>DM</strong></td>
  <td style="background-color:#99FFFF;color:#000000"><strong>MS</strong></td>
  <td style="background-color:#FFFF99;color:#000000"><strong>OK</strong></td>
  <td style="background-color:#FFFF99;color:#000000"><strong>DM</strong></td>
  <td style="background-color:#FFFF99;color:#000000"><strong>MS</strong></td>
  <td style="background-color:#FF9999;color:#000000"><strong>OK</strong></td>
  <td style="background-color:#FF9999;color:#000000"><strong>DM</strong></td>
  <td style="background-color:#FF9999;color:#000000"><strong>MS</strong></td>
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
	$prd_det = getProductDetails($row_loc['partcode'],"productname,productcolor,eol,productcategory,productsubcat,brand",$link1);
	$prd = explode("~",$prd_det);
	$fifo_qty = explode("~",getFIFOStock($row_loc['asc_code'],$row_loc['partcode'],$row_loc['okqty'],$row_loc['broken'],$row_loc['missing'],$link1));
	$price = mysqli_fetch_assoc(mysqli_query($link1,"SELECT price FROM price_master where state='".$locdet[2]."' and location_type='".$locdet[3]."' and product_code='".$row_loc['partcode']."' and status='active'"));
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$locdet[0]?></td>
<td align="left"><?=$locdet[1]?></td>
<td align="left"><?=$locdet[2]?></td>
<td align="left"><?php if($sublocname){ echo $sublocname;}else{ echo $locdet[0].",".$locdet[1].",".$locdet[2];}?></td>
<td align="left"><?=$row_loc['partcode']?></td>
<td align="left"><?=getAnyDetails($prd[3],"cat_name","catid","product_cat_master",$link1);?></td>
<td align="left"><?=getAnyDetails($prd[4],"prod_sub_cat","psubcatid","product_sub_category",$link1);?></td>
<td align="left"><?=getAnyDetails($prd[5],"make","id","make_master",$link1);?></td>
<td align="left"><?=$prd[0].",".$prd[1]?></td>
<td align="left"><?php 
			  ///// check product is EOL or not
			  if($prd[2]!='0000-00-00'){
				  if($prd[2] < $today){
					  $prd_natute = "EOL";
				  }
			  }else{
				  /// check product is slow/average/fast moving
				  if($fifo_qty[0] > $fifo_qty[1] && $fifo_qty[0] > $fifo_qty[2]){
					  $prd_natute = "Fast Moving";
				  }else if($fifo_qty[2] < $fifo_qty[0] && $fifo_qty[2] < $fifo_qty[1]){
					  $prd_natute = "Slow Moving";
				  }else{
					  $prd_natute = "Average Moving";
				  }
			  }
			  echo $prd_natute;?></td>
<td align="right" style="background-color:#99FFFF;color:#000000"><?php echo $fifo_qty[0];?></td>
<td align="right" style="background-color:#99FFFF;color:#000000"><?php echo $fifo_qty[3];?></td>
<td align="right" style="background-color:#99FFFF;color:#000000"><?php echo $fifo_qty[6];?></td>
<td align="right" style="background-color:#FFFF99;color:#000000"><?php echo $fifo_qty[1];?></td>
<td align="right" style="background-color:#FFFF99;color:#000000"><?php echo $fifo_qty[4];?></td>
<td align="right" style="background-color:#FFFF99;color:#000000"><?php echo $fifo_qty[7];?></td>
<td align="right" style="background-color:#FF9999;color:#000000"><?php echo $fifo_qty[2];?></td>
<td align="right" style="background-color:#FF9999;color:#000000"><?php echo $fifo_qty[5];?></td>
<td align="right" style="background-color:#FF9999;color:#000000"><?php echo $fifo_qty[8];?></td>
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
