<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$from_date=base64_decode($_REQUEST['fdate']);
$to_date=base64_decode($_REQUEST['tdate']);
$from_loc=base64_decode($_REQUEST['floc']);
$to_loc=base64_decode($_REQUEST['tloc']);
//$product_cat = base64_decode($_REQUEST['product_cat']);
//$product_subcat = base64_decode($_REQUEST['product_subcat']);
//$product = base64_decode($_REQUEST['pro']);
$locstr=getAccessLocation($_SESSION['userid'],$link1);
////// product category
/*if($product_cat == ""){
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

*/
if($from_loc=='' )
{  
	$from_party="";
}

else
{
	$from_party=" and (bid.from_location='".$from_loc."') ";
}
if($to_loc=='' )
{
	$to_party="and bid.to_location in (".$locstr.")";
}

else
{
	$to_party="and (bid.to_location='".$to_loc."') ";
}



if($from_date=='' || $to_date=='')
{
	$sql_date='1';
}

else
{
	$sql_date="(bm.entry_date>='".$from_date."' and bm.entry_date<='".$to_date."')";
}

//////End filters value/////
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>GRN No.</strong></td>
<td><strong>GRN Date</strong></td>
<td><strong>Type</strong></td>
<td><strong>Document Type</strong></td>
<td><strong>VPO No.</strong></td>
<td><strong>Vendor Invoice Number</strong></td>
<td><strong>Vendor Invoice Date</strong></td>
<td><strong>Vendor Name</strong></td>
<td><strong>Vendor Code</strong></td>
<td><strong>Location Name</strong></td>
<td><strong>Location Code</strong></td>
<td><strong>Go-down</strong></td>
<td><strong>Product Code</strong></td>
<td><strong>Product Name</strong></td>
<td><strong>Model</strong></td>
<td><strong>Product Category</strong></td>
<td><strong>Product SubCategory</strong></td>
<td><strong>Brand</strong></td>
<td><strong>Serial No.</strong></td>
<td><strong>Stock Type</strong></td>
</tr>
<?php
$i=1;
$sql=mysqli_query($link1,"SELECT bid.*, bm.entry_date ,bm.to_location, bm.from_location,bm.type ,bm.ref_no ,bm.inv_ref_no FROM billing_imei_data bid , billing_master bm WHERE ".$sql_date." AND bid.doc_no = bm.challan_no AND bm.type IN ('CLP','LP','DIRECT SALE RETURN','GRN','STN') ".$from_party." ".$to_party."")or die("er1".mysqli_error($link1));
while($row_loc = mysqli_fetch_array($sql)){
	$location=explode("~",getLocationDetails($row_loc['to_location'],"name,city,state",$link1));
	$product_name=explode("~",getProductDetails($row_loc['prod_code'],"productname,model_name,productcategory,productsubcat,brand",$link1));
	/// sub location
	if($row_loc["receive_sub_location"]){
		$subloc=getLocationDetails($row_loc['receive_sub_location'],"name,city,state",$link1);
		$explodevalf=explode("~",$subloc);
		if($explodevalf[0]){ $sublocname=$explodevalf[0]; }else{ $sublocname=getAnyDetails($row_loc['receive_sub_location'],"sub_location_name","sub_location","sub_location_master",$link1);}  
	}else{
		$subloc=getLocationDetails($row_loc['sub_location'],"name,city,state",$link1);
		$explodevalf=explode("~",$subloc);
		if($explodevalf[0]){ $sublocname=$explodevalf[0]; }else{ $sublocname=getAnyDetails($row_loc['sub_location'],"sub_location_name","sub_location","sub_location_master",$link1);}  
	}
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['challan_no']?></td>
<td align="left"><?=$row_loc['sale_date']?></td>
<td align="left"><?=$row_loc['type']?></td>
<td align="left"><?=$row_loc['document_type']?></td>
<td align="left"><?=$row_loc['ref_no']?></td>
<td align="left"><?=$row_loc['inv_ref_no']?></td>
<td align="left"><?=$row_loc['po_inv_date']?></td>
<td align="left"><?=getAnyParty($row_loc['from_location'],$link1)?></td>
<td align="left"><?=$row_loc['from_location']?></td>
<td align="left"><?=$location[0].",".$location[1].",".$location[2]?></td>
<td align="left"><?=$row_loc['to_location']?></td>
<td align="left"><?php if($sublocname){ echo $sublocname;}else{ echo $locdet[0].",".$locdet[1].",".$locdet[2];}?></td>
<td align="left"><?=$row_loc['prod_code']?></td>
<td align="left"><?=$product_name[0]?></td>
<td align="left"><?=$product_name[1]?></td>
<td align="left"><?=getAnyDetails($product_name[2],"cat_name","catid","product_cat_master",$link1);?></td>
<td align="left"><?=getAnyDetails($product_name[3],"prod_sub_cat","psubcatid","product_sub_category",$link1);?></td>
<td align="left"><?=getAnyDetails($product_name[4],"make","id","make_master",$link1);?></td>
<td align="left"><?=$row_loc['imei1']?></td>
<td align="left"><?=$row_loc['imei2']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>

