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
<td><strong>Location Name</strong></td>
<td><strong>Location Type</strong></td>
<td><strong>Mapped Group</strong></td>
<td><strong>Contact Person</strong></td>
<td><strong>Contact No.</strong></td>
<td><strong>Landline No.</strong></td>
<td><strong>Email Id</strong></td>
<td><strong>Communication Address</strong></td>
<td><strong>Dispatch/Delivery Address</strong></td>
<td><strong>Landmark</strong></td>
<td><strong>Pincode</strong></td>
<td><strong>TIN</strong></td>
<td><strong>PAN No.</strong></td>
<td><strong>CST No.</strong></td>
<td><strong>GST No.</strong></td>
<td><strong>Service Tax No.</strong></td>
<td><strong>Proprietor Type</strong></td>
<td><strong>TDS%</strong></td>
<td><strong>Bank Account Holder</strong></td>
<td><strong>Bank Account No.</strong></td>
<td><strong>Bank Name</strong></td>
<td><strong>Bank City</strong></td>
<td><strong>IFSC Code</strong></td>
<td><strong>Mapped Parent id</strong></td>
<td><strong>Status</strong></td>
<td><strong>Login Status</strong></td>
<td><strong>Remark</strong></td>
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
<td bgcolor="#FFFF99"><?=$row_loc['asc_code']?></td>
<td><?=$row_loc['name']?></td>
<td><?=$row_loc['id_type']?></td>
<td><?php if($row_loc['group_id']!=''){ echo getGroupName($row_loc['group_id'],$link1);}else{ }?></td>
<td><?=$row_loc['contact_person']?></td>
<td><?=$row_loc['phone']?></td>
<td><?=$row_loc['landline']?></td>
<td><?=$row_loc['email']?></td>
<td><?=cleanData($row_loc['addrs'])?></td>
<td><?=cleanData($row_loc['disp_addrs'])?></td>
<td><?=$row_loc['landmark']?></td>
<td><?=$row_loc['picode']?></td>
<td><?=$row_loc['vat_no']?></td>
<td><?=$row_loc['pan_no']?></td>
<td><?=$row_loc['cst_no']?></td>
<td><?=$row_loc['gstin_no']?></td>
<td><?=$row_loc['st_no']?></td>
<td><?=$row_loc['proprietor_type']?></td>
<td><?=$row_loc['tdsper']?></td>
<td><?=$row_loc['account_holder']?></td>
<td><?=$row_loc['account_no']?></td>
<td><?=$row_loc['bank_name']?></td>
<td><?=$row_loc['bank_city']?></td>
<td><?=$row_loc['ifsc_code']?></td>
<td><?=getParentLocation($row_loc['asc_code'],$link1)?></td>
<td><?=$row_loc['status']?></td>
<td><?=$row_loc['login_status']?></td>
<td><?=cleanData($row_loc['remark'])?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>