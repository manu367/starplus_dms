<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$ustatus=base64_decode($_REQUEST['status']);
## selected  Status
if($ustatus!=""){
	$status="status='".$ustatus."'";
}else{
	$status="1";
}

//////End filters value/////

$sql=mysqli_query($link1,"Select * from voucher_master where $status  ")or die("er1".mysqli_error($link1));

?>
<table width="50%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td width="5%"><strong>S.No.</strong></td>
<td  width="25%"><strong>Voucher Name</strong></td>
<td width="10%"><strong>Voucher Type</strong></td>
<td width="10%"><strong>Entry Date</strong></td>
<td width="10%"><strong>Status</strong></td>

</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['voucher_type']?></td>
<td align="left"><?=$row_loc['nature_of_voucher']?></td>
<td align="left"><?=$row_loc['update_date']?></td>
<td align="left"><?=$row_loc['status']?></td>

</tr>
<?php
$i+=1;		
}
?>
</table>