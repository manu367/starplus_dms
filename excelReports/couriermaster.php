<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$courierstate=base64_decode($_REQUEST[courierstate]);
$couriercity=base64_decode($_REQUEST[couriercity]);
$courierstatus=base64_decode($_REQUEST[status]);

## selected  Status
if($courierstatus!=""){
	$status="status='".$courierstatus."'";
}else{
	$status="1";
}
## selected city
if($couriercity!=""){
	$city="city='".$couriercity."'";
}else{
	$city="1";
}
## selected state
if($courierstate!=""){
	$state="state='".$courierstate."'";
}else{
	$state="1";
}
//////End filters value/////

$sql=mysqli_query($link1,"Select * from diesl_master where $status and $state and $city ")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Courier Name</strong></td>
<td><strong>Contact Person</strong></td>
<td><strong>Email Id</strong></td>
<td><strong>Contact No.</strong></td>
<td><strong>Address</strong></td>
<td><strong>State</strong></td>
<td><strong>City</strong></td>
<td><strong>Status</strong></td>
<td><strong>Rate</strong></td>
<td><strong>TAT</strong></td>
<td><strong>Vehicle Type</strong></td>
<td><strong>Weight</strong></td>

</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['couriername']?></td>
<td align="left"><?=$row_loc['contact_person']?></td>
<td align="left"><?=($row_loc['email'])?></td>
<td align="right"><?=$row_loc['phone']?></td>
<td align="left"><?=$row_loc['addrs']?></td>
<td align="left"><?=$row_loc['state']?></td>
<td align="left"><?=$row_loc['city']?></td>
<td align="left"><?=$row_loc['status']?></td>
<td align="left"><?=$row_loc['rate']?></td>
<td align="left"><?=$row_loc['tat']?></td>
<td align="left"><?=$row_loc['vehicletype']?></td>
<td align="left"><?=$row_loc['weight']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>