<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$from_date = base64_decode($_REQUEST['fdate']);
$to_date = base64_decode($_REQUEST['tdate']);
$from_loc = base64_decode($_REQUEST['floc']);
$to_loc = base64_decode($_REQUEST['tloc']);
$product_cat = base64_decode($_REQUEST['product_cat']);
$product_subcat = base64_decode($_REQUEST['product_subcat']);
$product = base64_decode($_REQUEST['pro']);
$status = base64_decode($_REQUEST['status']);
$scheme = base64_decode($_REQUEST['scheme']);
$locstr = getAccessLocation($_SESSION['userid'],$link1);
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
if($product =='' ){
	$product_code = " a.prod_code in (select productcode from product_master where ".$prd_cat." and ".$prd_subcat.")";
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


if($scheme=='' )
{
	$scheme_name ="";
}

else
{
	$scheme_name=" and a.scheme_name='".$scheme."' ";
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
	if($status == "Pending For Serial"){
		$sts = " AND a.imei_attach='' AND a.prod_code IN (SELECT productcode FROM product_master WHERE is_serialize='Y')";
	}else{
		$sts = " AND b.status='".$status."'";
	}
}else{
	$sts = "";
}

//////End filters value/////
$sql = mysqli_query($link1,"SELECT * FROM billing_model_data a INNER JOIN billing_master b ON a.challan_no=b.challan_no WHERE ".$from_party." AND ".$to_party." AND ".$sql_date." AND ".$product_code." ".$scheme_name." ".$sts."")or die("er1".mysqli_error($link1));
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
<td><strong>Entry By</strong></td>
<td><strong>Status</strong></td>
<td><strong>Product Code</strong></td>
<td><strong>Product Description</strong></td>
<td><strong>Gross Weight</strong></td>
<td><strong>Net Weight</strong></td>
<td><strong>Scrap Weight</strong></td>
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
<td><strong>Scheme Name</strong></td>
<td><strong>Invoice Remark</strong></td>
<td><strong>Logistic Name</strong></td>
<td><strong>Docket Number</strong></td>
<td><strong>Logistic Person</strong></td>
<td><strong>Contact No.</strong></td>
<td><strong>Carrier No.</strong></td>
<td><strong>Dispatch Date</strong></td>
<td><strong>Dispatch Remark</strong></td>
<td><strong>Transport Mode</strong></td>
<td><strong>Dispatch Address</strong></td>
<td><strong>Delivery Address</strong></td>
<td><strong>Received By</strong></td>
<td><strong>Received Date</strong></td>
<td><strong>Received Remark</strong></td>
<td><strong>Dispatch TAT</strong></td>
<td><strong>Receive/In-Transit TAT</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
$product=explode("~",getProductDetails($row_loc['prod_code'],"productcode,productdesc,weight,net_weight,scrap_weight",$link1));
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
<td align="left"><?=$admin_detail[0]?></td>
<td align="left"><?=$row_loc['status']?></td>
<td align="left"><?=$product[0]?></td>
<td align="left"><?=$product[1]?></td>
<td align="right"><?=$product['2']?></td>
<td align="right"><?=$product['3']?></td>
<td align="right"><?=$product['4']?></td>
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
<td align="right"><?=$row_loc['scheme_name']?></td>
<td align="left"><?=$row_loc['billing_rmk']?></td>
<td align="left"><?=getLogistic($row_loc['diesel_code'],$link1)?></td>
<td align="left"><?=$row_loc['docket_no']?></td>
<td align="left"><?=$row_loc['logistic_person']?></td>
<td align="left"><?=$row_loc['logistic_contact']?></td>
<td align="left"><?=$row_loc['vehical_no']?></td>
<td align="left"><?=$row_loc['dc_date']?></td>
<td align="left"><?=$row_loc['disp_rmk']?></td>
<td align="left"><?=$row_loc['trnas_mode']?></td>
<td align="left"><?=$row_loc['disp_addrs']?></td>
<td align="left"><?=$row_loc['deliv_addrs']?></td>
<td align="left"><?=getAdminDetails($row_loc['receive_by'],"name",$link1)?></td>
<td align="left"><?=$row_loc['receive_date']?></td>
<td align="left"><?=$row_loc['receive_remark']?></td>
<td align="right"><?php if($row_loc['dc_date']!="0000-00-00"){ $dcdate = $row_loc['dc_date'];}else{ $dcdate = $today;} echo $rec_tat = daysDifference($dcdate,$row_loc['sale_date']);?></td>
<td align="right"><?php if($row_loc['receive_date']!="0000-00-00"){ $rdate = $row_loc['receive_date'];}else{ $rdate = $today;} echo $trans_tat = daysDifference($rdate,$row_loc['dc_date']);?></td>


</tr>
<?php
$i+=1;		
}
?>
</table>