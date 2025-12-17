<?php 
print("\n");
print("\n");
////// filters value/////
$location_state=base64_decode($_REQUEST['location_state']);
$product=base64_decode($_REQUEST['product']);
$location_type=base64_decode($_REQUEST['location_type']);
$status=base64_decode($_REQUEST['status']);
$accessState=getAccessState($_SESSION['userid'],$link1);
$filter = "";
## selected state
if($location_state!=""){
	$filter .=" AND state='".$location_state."'";
}
## selected city
if($product!=""){
	$filter .=" AND partcode='".$product."'";
}
## selected location type
if($location_type!=""){
	$filter .=" AND id_type='".$location_type."'";
}
## selected status
if($status!=""){
	$filter .=" AND status='".$status."'";
}

$sql=mysqli_query($link1,"SELECT * FROM reward_points_master WHERE state IN (".$accessState.") ".$filter)or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>State</strong></td>
<td><strong>Location Type</strong></td>
<td><strong>Product Name</strong></td>
<td><strong>Product Code</strong></td>
<td><strong>Reward Points</strong></td>
<td><strong>Status</strong></td>
<td><strong>Create By</strong></td>
<td><strong>Create On</strong></td>
<td><strong>Update By</strong></td>
<td><strong>Update On</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['state']?></td>
<td align="left"><?=getLocationType($row_loc['id_type'],$link1)?></td>
<td align="left"><?=getProduct($row_loc['partcode'],$link1)?></td>
<td align="left"><?=$row_loc['partcode']?></td>
<td align="left"><?=$row_loc['reward_point']?></td>
<td align="left"><?=$row_loc['status']?></td>
<td align="left"><?=$row_loc['create_by']?></td>
<td align="left"><?=$row_loc['create_on']?></td>
<td align="left"><?=$row_loc['update_by']?></td>
<td align="left"><?=$row_loc['update_on']?></td>

</tr>
<?php
$i+=1;		
}
?>
</table>