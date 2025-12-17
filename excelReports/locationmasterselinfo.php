<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$locationstate=base64_decode($_REQUEST['locstate']);
$locationcity=base64_decode($_REQUEST['loccity']);
$locationtype=base64_decode($_REQUEST['loctype']);
$locationstatus=base64_decode($_REQUEST['locstatus']);
## selected state
if($locationstate!=""){
	$loc_state="state='".$locationstate."'";
}else{
	$loc_state="1";
}
## selected city
if($locationcity!=""){
	$loc_city="city='".$locationcity."'";
}else{
	$loc_city="1";
}
## selected location type
if($locationtype!=""){
	$loc_type="id_type='".$locationtype."'";
}else{
	$loc_type="1";
}
## selected location Status
if($locationstatus!=""){
	$loc_status="status='".$locationstatus."'";
}else{
	$loc_status="1";
}
//////End filters value/////
$sql_loc=mysqli_query($link1,"Select * from asc_master where $loc_state and $loc_city and $loc_type and $loc_status order by state, name asc")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Circle</strong></td>
<td><strong>State</strong></td>
<td><strong>City</strong></td>
<td><strong>Location Id</strong></td>
<td bgcolor="#FFCC66" style="color:#FF0000"><strong>SAP Code</strong></td>
<td><strong>Location Name</strong></td>
<td><strong>Location Type</strong></td>
<td><strong>Contact No.</strong></td>
<td><strong>Communication Address</strong></td>
<td><strong>GST No.</strong></td>
<td><strong>Status</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql_loc)){
?>
<tr>
<td><?=$i?></td>
<td><?=$row_loc['circle']?></td>
<td><?=$row_loc['state']?></td>
<td><?=$row_loc['city']?></td>
<td><?=$row_loc['asc_code']?></td>
<td><?=$row_loc['sap_code']?></td>
<td><?=$row_loc['name']?></td>
<td><?=$row_loc['id_type']?></td>
<td><?=$row_loc['phone']?></td>
<td><?=cleanData($row_loc['addrs'])?></td>
<td><?=$row_loc['gstin_no']?></td>
<td><?=$row_loc['status']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>
