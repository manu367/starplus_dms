<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$fdate = $_REQUEST['fdate'];
$tdate = $_REQUEST['tdate'];
$po_from = base64_decode($_REQUEST['po_from']);
$brand = base64_decode($_REQUEST['brand']);
$product_cat = base64_decode($_REQUEST['product_cat']);
$product_subcat = base64_decode($_REQUEST['product_subcat']);
$product = base64_decode($_REQUEST['product']);
$stock_type = base64_decode($_REQUEST['stock_type']);


//$locstr=getAccessLocation($_SESSION['userid'],$link1);
if($po_from=='' )
{
	//$from_party="from_party in (".$locstr.")";
	//$from_party="(from_party in (".$locstr.") or to_party in (".$locstr."))";
	$from_party = "owner_code='".$po_from."'";
}
else
{
	//$from_party="(from_party='".$po_from."') ";
	//$from_party="(from_party='".$po_from."' or to_party='".$po_from."') ";
	$from_party = "owner_code='".$po_from."'";
}
if($fdate=='' || $tdate=='')
{
	$fromdate = date("Y-m-01");
	$todate = $today;
	$sql_date="(reference_date>='".$fromdate."' and sale_date<='".$todate."')";
}
else
{
	$sql_date="(reference_date>='".$fdate."' and reference_date<='".$tdate."')";
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
	$product_code = " partcode in (select productcode from product_master where ".$prd_cat." and ".$prd_subcat." and ".$prd_brand.")";
}
else{
	$product_code="(partcode='".$product."') ";
}
if($stock_type != ''){
	$stktype = " stock_type='".$stock_type."'";
}else{
	$stktype = " 1";
}
//////End filters value/////
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
	<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
  		<td>S.No</td>
        <td>From Location</td>
        <td>From Location Code</td>
        <td>To Location</td>
        <td>To Location Code</td>
        <td>Product Category</td>
        <td>Product Sub Category</td>
        <td>Brand</td>
        <td>Product Name</td>
        <td>Product Code</td>
        <td>Opening Stock</td>
        <td>In</td>
        <td>Out</td>
        <td>Closing Stock</td>
        <td>System Ref. No.</td>
        <td>Stock Type</td>
        <td>Transfer Type</td>
        <td>Stock Transfer</td>
        <td>Movement Date</td>
        <td>Movement Time</td>
	</tr>
	<?php
	$j=1;
	$sql_sl = "SELECT * FROM stock_ledger WHERE ".$from_party." AND ".$sql_date." AND ".$product_code." AND ".$stktype;
	$res_sl = mysqli_query($link1, $sql_sl);
	while($row = mysqli_fetch_assoc($res_sl)){
		/////// get full part desc //////////////
		$part_info =  explode("~",getAnyDetails($row["partcode"],"productname,brand,productcategory,productsubcat","productcode","product_master",$link1));		
		/////// get in out qty  ///////////	 
		$in_qty = 0;
		$out_qty = 0;
	
		if($row['stock_transfer']=='IN'){ $in_qty = $row['qty'];}
		if($row['stock_transfer']=='OUT'){ $out_qty = $row['qty'];}
		
		//////////////////
		if($row['stock_transfer']=='IN'){ $in = $row['qty']; $out=0; } else{ $in = 0;  $out = $row['qty']; }
		if($j==1){ $open = 0; }else{}
		$closing = $open+$in-$out;
	?>
    <tr>
        <td><?=$j?></td>
        <td><?php echo getAnyParty($row['from_party'],$link1);?></td>
        <td><?=$row['from_party']?></td>
        <td><?php echo getAnyParty($row['to_party'],$link1);?></td>
        <td><?=$row['to_party']?></td>
        <td><?=getAnyDetails($part_info[2],"cat_name","catid","product_cat_master",$link1);?></td>
        <td><?=getAnyDetails($part_info[3],"prod_sub_cat","psubcatid","product_sub_category",$link1);?></td>
        <td><?=getAnyDetails($part_info[1],"make","id","make_master",$link1);?></td>
        <td><?=$part_info[0];?></td>
        <td><?=$row["partcode"];?></td>
        <td align="right"><?=$open;?></td>
        <td align="right"><?=$in_qty;?></td>
        <td align="right"><?=$out_qty;?></td>
        <td align="right"><?=$closing;?></td>
        <td><?=$row["reference_no"];?></td>
        <td><?=$row["stock_type"];?></td>
        <td><?=$row["type_of_transfer"];?></td>
        <td><?=$row["stock_transfer"];?></td>
        <td align="center"><?=dt_format($row["create_date"]);?></td>
        <td align="center"><?=$row["create_time"];?></td>
    </tr>
	<?php
		$j++;
		$open=$closing;
	}
	?>
</table>
