<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$from_date=base64_decode($_REQUEST['fdate']);
$to_date=base64_decode($_REQUEST['tdate']);
$from_loc=base64_decode($_REQUEST['floc']);
$to_loc=base64_decode($_REQUEST['tloc']);
$docType = base64_decode($_REQUEST['docType']);
$product_cat = base64_decode($_REQUEST['product_cat']);
$product_subcat = base64_decode($_REQUEST['product_subcat']);
$brand = base64_decode($_REQUEST['brand']);
$status = base64_decode($_REQUEST['status']);
$product = base64_decode($_REQUEST['pro']);
$locstr=getAccessLocation($_SESSION['userid'],$link1);
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

if($from_loc=='' )
{  
	$from_party="a.from_location in (".$locstr.")";
}

else
{
	$from_party="(a.from_location='".$from_loc."') ";
}
if($to_loc=='' )
{
	//$to_party="b.to_location in (".$locstr.")";
	$to_party="1";
}

else
{
	$to_party="(b.to_location='".$to_loc."') ";
}

if($docType=='' )
{
	$doc_type="1";
}

else
{
	$doc_type="(b.document_type='".$docType."') ";
}

if($from_date=='' || $to_date=='')
{
	$sql_date='1';
}

else
{
	$sql_date="(b.sale_date>='".$from_date."' and b.sale_date<='".$to_date."')";
}

if($status){
	$sts = " AND b.status='".$status."'";
}else{
	$sts = "";
}
//////End filters value/////
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>From Location</strong></td>
<td><strong>From Location Code</strong></td>
<td><strong>To Location</strong></td>
<td><strong>To Location Code</strong></td>
<td><strong>Document Type</strong></td>
<td><strong>Invoice No.</strong></td>
<td><strong>Invoice Date</strong></td>
<td><strong>Status</strong></td>
<td><strong>Entry By</strong></td>
<td><strong>Product Code</strong></td>
<td><strong>Product Description</strong></td>
<td><strong><?=$imeitag?>1</strong></td>
<td><strong><?=$imeitag?>2</strong></td>
</tr>
<?php
$i=1;
	//echo "Select * from billing_imei_data a inner join billing_master b on a.doc_no=b.challan_no where $from_party and $to_party and $doc_type and $sql_date and $product_code";
$sql=mysqli_query($link1,"Select * from billing_imei_data a inner join billing_master b on a.doc_no=b.challan_no where $from_party and $to_party and $doc_type and $sql_date and $product_code $sts")or die("er1".mysqli_error($link1));
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
<td align="left"><?=$row_loc['document_type']?></td>
<td align="left"><?=$row_loc['challan_no']?></td>
<td align="center"><?=$row_loc['sale_date']?></td>
<td align="left"><?=$row_loc['status']?></td>
<td align="left"><?=$admin_detail[0]?></td>
<td align="left"><?=$product[0]?></td>
<td align="left"><?=$product[1]?></td>
<td align="left"><?=$row_loc['imei1']?></td>
<td align="left"><?=$row_loc['imei2']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>