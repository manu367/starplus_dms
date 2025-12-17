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
$scheme = base64_decode($_REQUEST['scheme']);
$status = base64_decode($_REQUEST['status']);

$locstr=getAccessLocation($_SESSION['userid'],$link1);
if($from_loc=='' )
{
	$from_party="from_location in (".$locstr.")";
}

else
{
	$from_party="(from_location='".$from_loc."') ";
}

if($to_loc=='' )
{
	//$to_party="to_location in (".$locstr.")";
	$to_party="1";
}

else
{
	$to_party="(to_location='".$to_loc."') ";
}
if($docType=='' )
{
	$doc_type="1";
}

else
{
	$doc_type="document_type='".$docType."' ";
}



if($from_date=='' || $to_date=='')
{
	$sql_date='1';
}

else
{
	$sql_date="(sale_date>='".$from_date."' and sale_date<='".$to_date."')";
}

if($status){
	$sts = " AND status='".$status."'";
}else{
	$sts = "";
}

//////End filters value/////
//echo"Select * from billing_master where $from_party and $to_party and $sql_date  ";
if($status == "Pending For Serial"){
	$sql=mysqli_query($link1,"SELECT * FROM billing_master WHERE ".$from_party." AND ".$to_party." AND ".$doc_type." AND ".$sql_date." AND challan_no IN (SELECT challan_no FROM billing_model_data WHERE imei_attach='' AND prod_code IN (SELECT productcode FROM product_master WHERE is_serialize='Y'))")or die("er1".mysqli_error($link1));
}else{
	$sql=mysqli_query($link1,"SELECT * FROM billing_master WHERE ".$from_party." AND ".$to_party." AND ".$doc_type." AND ".$sql_date." ".$sts)or die("er1".mysqli_error($link1));
}
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
<td><strong>Invoice Type</strong></td>
<td><strong>Basic Amount</strong></td>
<td><strong>Discount Amount</strong></td>
<td><strong>Total IGST Amount</strong></td>
<td><strong>Total CGST Amount</strong></td>
<td><strong>Total SGST Amount</strong></td>
<td><strong>Total Amount</strong></td>
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
<td><strong>Scheme Name</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
$f_location=explode("~",getLocationDetails($row_loc['from_location'],"name,city,state",$link1));
$t_location=explode("~",getLocationDetails($row_loc['to_location'],"name,city,state",$link1));
///// fetch scheme name ////////////
if($scheme != ''){
$scheme = mysqli_fetch_array(mysqli_query($link1," select scheme_name from billing_model_data  where scheme_name = '".$scheme."' "));
}
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$f_location[0].",".$f_location[1].",".$f_location[2]?></td>
<td align="left"><?=$row_loc['from_location']?></td>
<td align="left"><?=$t_location[0].",".$t_location[1].",".$t_location[2]?></td>
<td align="left"><?=$row_loc['to_location']?></td>
<td align="left"><?=$row_loc['document_type']?></td>
<td align="left"><?=($row_loc['challan_no'])?></td>
<td align="center"><?=($row_loc['sale_date'])?></td>
<td align="left"><?=getAdminDetails($row_loc['entry_by'],"name",$link1)?></td>
<td align="left"><?=$row_loc['status']?></td>
<td align="left"><?=$row_loc['type']?></td>
<td align="right"><?=$row_loc['basic_cost']?></td>
<td align="right"><?=$row_loc['discount_amt']?></td>
<td align="right"><?=$row_loc['total_igst_amt']?></td>
<td align="right"><?=$row_loc['total_cgst_amt']?></td>
<td align="right"><?=$row_loc['total_sgst_amt']?></td>
<td align="right"><?=$row_loc['total_cost']?></td>
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
<td align="right"><?=$scheme['scheme_name']?></td>

</tr>
<?php
$i+=1;		
}
?>
</table>