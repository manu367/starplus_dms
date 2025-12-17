<?php 
require_once("../includes/serial_logic_function.php");
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
	$imeiloc="a.owner_code='".$location."'";
}else{
	$locstr=getAccessLocation($_SESSION['userid'],$link1);
	$imeiloc="a.owner_code in (".$locstr.")";
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
	$part_code = " a.prod_code='".$partcode."'";
}else{
	$part_code = " 1";
}
//////End filters value/////
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Location Code</strong></td>
<!--<td><strong>Location Name</strong></td>
<td><strong>City</strong></td>
<td><strong>State</strong></td>-->
<td><strong>Product Code</strong></td>
<!--<td><strong>Product Description</strong></td>-->
<td><strong>Model</strong></td>
<!--<td><strong>Product Category</strong></td>
<td><strong>Product Sub Category</strong></td>
<td><strong>Brand</strong></td>-->
<td><strong>Serial No.</strong></td>
<td><strong>Stock Type</strong></td>
<td><strong>Qty</strong></td>
</tr>
<?php
$i=1;
$sql=mysqli_query($link1,"Select a.owner_code, a.prod_code, a.imei1, a.stock_type, a.import_date  from billing_imei_data a, product_master b WHERE a.prod_code=b.productcode AND ".$imeiloc." AND ".$pcat_s." AND ".$pscat_s." AND ".$brd_s." AND ".$part_code)or die("er1".mysqli_error($link1));
while($row_loc = mysqli_fetch_array($sql)){
$qty = 0 ;
	$chek_owner=mysqli_fetch_assoc(mysqli_query($link1,"select owner_code,doc_no,prod_code from billing_imei_data where imei1='".$row_loc['imei1']."' order by id desc"));
	if($chek_owner["prod_code"]==$row_loc["prod_code"]){
	$chek_rcvin=mysqli_fetch_assoc(mysqli_query($link1,"select status from billing_master where challan_no='".$chek_owner['doc_no']."'"));
	  if($chek_rcvin['status']==""){
	  	$chek_postatus=mysqli_fetch_assoc(mysqli_query($link1,"select status from vendor_order_master where po_no='".$chek_owner['doc_no']."'"));
		if($chek_postatus['status']==""){
			$chek_rcvin2=mysqli_fetch_assoc(mysqli_query($link1,"select status from opening_stock_master where doc_no='".$chek_owner['doc_no']."'"));
			if($chek_rcvin2['status']==""){
				$chek_rcvin3=mysqli_fetch_assoc(mysqli_query($link1,"select status from stockconvert_master where doc_no='".$chek_owner['doc_no']."'"));
				if($chek_rcvin3['status']=="Processed"){
					$checkstatus = "Received";
				}else{
					$checkstatus = $chek_rcvin3['status'];
				}
			}else{
				$checkstatus=$chek_rcvin2['status'];
			}
		}else{
			$checkstatus = $chek_postatus['status'];
		}
		
	  }else{
		 $checkstatus=$chek_rcvin['status'];
	  }
	if($chek_owner['owner_code']==$row_loc['owner_code'] && $checkstatus=="Received"){
	//$locdet=explode("~",getLocationDetails($row_loc['owner_code'],"name,city,state,id_type",$link1));
	$proddet=explode("~",getProductDetails($row_loc['prod_code'],"productname,productcolor,model_name,productcategory,productsubcat,brand",$link1));
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['owner_code']?></td>
<?php /*?><td align="left"><?=$locdet[0]?></td>
<td align="left"><?=$locdet[1]?></td>
<td align="left"><?=$locdet[2]?></td><?php */?>
<td align="left"><?=$row_loc['prod_code']?></td>
<?php /*?><td align="left"><?=$proddet[0]?></td><?php */?>
<td align="left"><?=$proddet[2]?></td>
<?php /*?><td align="left"><?=getAnyDetails($proddet[3],"cat_name","catid","product_cat_master",$link1);?></td>
<td align="left"><?=getAnyDetails($proddet[4],"prod_sub_cat","psubcatid","product_sub_category",$link1);?></td>
<td align="left"><?=getAnyDetails($proddet[5],"make","id","make_master",$link1);?></td><?php */?>
<td align="left"><?=$row_loc['imei1']?></td>
<td align="left"><?=$row_loc['stock_type']?></td>
<td align="left"><?php echo $qty = 1; ?></td>
</tr>
<?php
$i+=1;
}
	}
}
?>
</table>
