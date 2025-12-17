<style type='text/css'>
.text{mso-number-format:"\@";}
</style>
<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$from_date=base64_decode($_REQUEST[fdate]);
$to_date=base64_decode($_REQUEST[tdate]);

if($from_date=='' || $to_date==''){
	$sql_date='1';
}else{
	$sql_date="(upload_date>='".$from_date."' and upload_date<='".$to_date."')";
}

//////End filters value/////

$sql=mysqli_query($link1,"Select * from imei_activation where  $sql_date ")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Product Category</strong></td>
<td><strong>Product Sub-category</strong></td>
<td><strong>Brand</strong></td>
<td><strong>Product Code</strong></td>
<td><strong>Product Name</strong></td>
<td><strong><?=$imeitag?>-1</strong></td>
<td><strong><?=$imeitag?>-2</strong></td>
<td><strong>Operator</strong></td>
<td><strong>State</strong></td>
<td><strong>File Name</strong></td>
<td><strong>Activation Date</strong></td>
<td><strong>Entry Date</strong></td>
<td><strong>Entry By</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
$product_name=explode("~",getProductDetails($row_loc['productcode'],"productname",$link1));
$admin_detail=explode("~",getAdminDetails($row_loc['update_by'],"name",$link1));
$make_detail=explode("~",getMakeDetails($row_loc['brand'],"make",$link1));
$prd_cat_detail=explode("~",getProductCategoryDetails($row_loc['product_cat'],"cat_name",$link1));
$prd_sub_cat_detail=explode("~",getProductSubCategoryDetails($row_loc['product_sub_cat'],"prod_sub_cat",$link1));
?>
<tr>
<td align="center"><?=$i?></td>
<td align="left"><?=$prd_cat_detail[0]?></td>
<td align="left"><?=$prd_sub_cat_detail[0]?></td>
<td align="left"><?=$make_detail[0]?></td>
<td align="left"><?=$row_loc['productcode']?></td>
<td align="left"><?=$product_name[0]?></td>
<td class="text" align="right"><?=cleanData($row_loc['imei1'])?></td>
<td class="text" align="left"><?=cleanData($row_loc['imei2'])?></td>
<td align="left"><?=$row_loc['operator']?></td>
<td align="left"><?=$row_loc['state']?></td>
<td align="right"><?=$row_loc['file_name']?></td>
<td align="center"><?=dt_format($row_loc['sale_date'])?></td>
<td align="center"><?=dt_format($row_loc['upload_date'])?></td>
<td align="right"><?=$admin_detail[0]?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>