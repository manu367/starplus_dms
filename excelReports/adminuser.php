<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$utype=base64_decode($_REQUEST[u_type]);
$ustatus=base64_decode($_REQUEST[status]);
$accessLocation=getAccessLocation($_SESSION['userid'],$link1);
## selected state
if($accessLocation!=""){
	$loc_code="owner_code IN (".$accessLocation.")";
}else{
	$loc_code="1";
}
## selected  Status
if($ustatus!=""){
	$status="status='".$ustatus."'";
}else{
	$status="1";
}
## selected user type
if($utype!=""){
	$utypename="utype='".$utype."'";
}else{
	$utypename="1";
}
//////End filters value/////
if($_SESSION['utype']==1){
$sql=mysqli_query($link1,"Select * from admin_users where $status and $utypename ")or die("er1".mysqli_error($link1));
}
else {

$sql=mysqli_query($link1,"Select * from admin_users where $loc_code and  $status and $utypename ")or die("er1".mysqli_error($link1));
}
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>User Id</strong></td>
<td><strong>Emp Id</strong></td>
<td><strong>User Name</strong></td>
<td><strong>User Type</strong></td>
<td><strong>Reporting Manager</strong></td>
<td><strong>Contact No.</strong></td>
<td><strong>Email Id</strong></td>
<td><strong>Designation</strong></td>
<td><strong>Department</strong></td>
<td><strong>Sub Department</strong></td>
<td><strong>State</strong></td>
<td><strong>Base Location</strong></td>
<td><strong>Status</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
	$rpmng = mysqli_fetch_assoc(mysqli_query($link1, "Select name,oth_empid from admin_users where username='" . $row_loc['reporting_manager'] . "'"));
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['username']?></td>
<td align="left"><?=$row_loc['oth_empid']?></td>
<td align="left"><?=$row_loc['name']?></td>
<td align="left"><?=gettypeName($row_loc['utype'],$link1)?></td>
<td align="left"><?=$rpmng["name"]." (".$row_loc['reporting_manager'].")"?></td>
<td align="right"><?=$row_loc['phone']?></td>
<td align="left"><?=$row_loc['emailid']?></td>
<td align="left"><?=getAnyDetails($row_loc['designationid'],"designame","designationid","hrms_designation_master",$link1);?></td>
<td align="left"><?=getAnyDetails($row_loc['department'],"dname","departmentid","hrms_department_master",$link1);?></td>
<td align="left"><?=getAnyDetails($row_loc['subdepartment'],"subdept","subdeptid","hrms_subdepartment_master",$link1);?></td>
<td align="left"><?=$row_loc['state']?></td>
<td align="left"><?=$row_loc['city']?></td>
<td align="left"><?=$row_loc['status']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>
