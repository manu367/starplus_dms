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

$locstr = getAccessLocation($_SESSION['userid'],$link1);
if($location=='' )
{  
	$loc_code="location_code in (".$locstr.")";
}

else
{
	$loc_code="location_code='".$location."' ";
}

if($from_date=='' || $to_date=='')
{
	$sql_date='1';
}

else
{
	$sql_date="(requested_date>='".$from_date."' and requested_date<='".$to_date."')";
}

//////End filters value/////
//echo"Select * from opening_stock_master where $sql_date  ";
$sql=mysqli_query($link1,"Select * from opening_stock_master where $sql_date and $loc_code")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Location Code</strong></td>
<td><strong>Location Name</strong></td>
<td><strong>Doc Number</strong></td>
<td><strong>Entry By</strong></td>
<td><strong>Requested Date</strong></td>
<td><strong>Entry Date</strong></td>
<td><strong>Status</strong></td>
<td><strong>Stock Value</strong></td>
<td><strong>Remark</strong></td>
<td><strong>Cancel By</strong></td>
<td><strong>Cancel Remark</strong></td>
<td><strong>Cancel Step</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
    $locdet=explode("~",getLocationDetails($row_loc['location_code'],"name",$link1));
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['location_code']?></td>
<td align="left"><?=$locdet[0]?></td>
<td align="left"><?=$row_loc['doc_no']?></td>
<td align="left"><?=$row_loc['create_by']?></td>
<td align="center"><?=($row_loc['requested_date'])?></td>
<td align="center"><?=($row_loc['entry_date'])?></td>
<td align="left"><?=$row_loc['status']?></td>
<td align="left"><?=$row_loc['stock_value']?></td>
<td align="left"><?=$row_loc['remark']?></td>
<td align="left"><?=$row_loc['cancel_by']?></td>
<td align="left"><?=$row_loc['cancel_rmk']?></td>
<td align="left"><?=$row_loc['cancel_step']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>