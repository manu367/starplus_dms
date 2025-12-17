<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$from_date=base64_decode($_REQUEST['fdate']);
$to_date=base64_decode($_REQUEST['tdate']);
$location=base64_decode($_REQUEST['loc']);
$product_cat = base64_decode($_REQUEST['product_cat']);
$product_subcat = base64_decode($_REQUEST['product_subcat']);
$product = base64_decode($_REQUEST['pro']);
$brand = base64_decode($_REQUEST['brand']);
$locstr = getAccessLocation($_SESSION['userid'],$link1);
if($location=='' )
{  
	$loc_code="b.location_code in (".$locstr.")";
}

else
{
	$loc_code="b.location_code='".$location."' ";
}

////// product category
if($product_cat == ""){
	$prd_cat = "1";
}else{
	$prd_cat = "productcategory = '".$product_cat."'";
}
if($product_subcat == ""){
	$prd_subcat = "1";
}else{
	$prd_subcat = "productsubcat = '".$product_subcat."'";
}
if($brand == ""){
	$prd_brand = "1";
}else{
	$prd_brand = "brand = '".$brand."'";
}
if($product =='' ){
	$product_code = " a.prod_code in (select productcode from product_master where ".$prd_cat." and ".$prd_subcat." and ".$prd_brand.")";
}
else{
	$product_code="(a.prod_code='".$product."') ";
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
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Location Code</strong></td>
<td><strong>Location Name</strong></td>
<td><strong>Doc Number</strong></td>
<td><strong>Entry By</strong></td>
<td><strong>Entry Date</strong></td>
<td><strong>Product Code</strong></td>
<td><strong>Product Description</strong></td>
<td><strong>Stock Type</strong></td>
<td><strong>Serial No</strong></td>
</tr>
<?php
$i=1;
$sql=mysqli_query($link1,"Select * from billing_imei_data a inner join opening_stock_master b on a.doc_no=b.doc_no where $sql_date and $product_code and $loc_code")or die("er1".mysqli_error($link1));
while($row_loc = mysqli_fetch_array($sql)){
    $product=explode("~",getProductDetails($row_loc['prod_code'],"productcode,productdesc",$link1));
    $locdet=explode("~",getLocationDetails($row_loc['location_code'],"name",$link1));
 ?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['location_code']?></td>
<td align="left"><?=$locdet[0]?></td>
<td align="left"><?=$row_loc['doc_no']?></td>
<td align="left"><?=$row_loc['create_by']?></td>
<td align="center"><?=($row_loc['entry_date'])?></td>
<td align="left"><?=$product[0]?></td>
<td align="left"><?=$product[1]?></td>
<td align="left"><?=$row_loc['stock_type']?></td>
<td align="left"><?=$row_loc['imei1']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>