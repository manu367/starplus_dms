<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables


$ustatus=base64_decode($_REQUEST[status]);

## selected  Status
if($ustatus!=""){
	$status="status='".$ustatus."'";
}else{
	$status="1";
}

//////End filters value/////
$sql=mysqli_query($link1,"Select * from coupon_master where  $status order by id ")or die("er1".mysqli_error($link1));

?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Coupon Code</strong></td>
<td><strong>Valid From</strong></td>
<td><strong>Valid To</strong></td>
<td><strong>Amount</strong></td>
<td><strong>Remark</strong></td>
<td><strong>Status</strong></td>
<td><strong>Create Date</strong></td>
<td><strong>Create By</strong></td>
<td><strong>Update Date & Time</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['coupon_code']?></td>
<td align="left"><?=$row_loc['valid_from']?></td>
<td align="left"><?=$row_loc['valid_to']?></td>
<td align="right"><?=$row_loc['amount']?></td>
<td align="right"><?=$row_loc['remark']?></td>
<td align="left"><?=$row_loc['status']?></td>
<td align="left"><?=$row_loc['create_date']?></td>
<td align="left"><?=getAdminDetails($row_loc['create_by'],"name",$link1)?></td>
<td align="left"><?=$row_loc['updatedate']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>