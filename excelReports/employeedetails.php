<?php 
print("\n");
print("\n");
////// filters value/////
$user_id = base64_decode($_REQUEST['user_id']);

if ($user_id != '') {
    $emp.=" where loginid = '" . $user_id . "'";
}
else {
	$emp.=" ";
	
	}

$sql=mysqli_query($link1,"Select * from hrms_employe_master  $emp order by empid")or die("er1".mysqli_error($link1));

?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Employee Id</strong></td>
<td><strong>Employee Name</strong></td>
<td><strong>State</strong></td>
<td><strong>City</strong></td>
<td><strong>Email</strong></td>
<td><strong>Contact No.</strong></td>
<td><strong>Present Address</strong></td>
<td><strong>DOB</strong></td>
<td><strong>Employee Type</strong></td>
<td><strong>Job Type</strong></td>
<td><strong>Mapped Location</strong></td>
<td><strong>Department Name</strong></td>
<td><strong>Designation</strong></td>
<td><strong>Reporting Manager</strong></td>
<td><strong>Joining Date</strong></td>
<td><strong>Resign Date</strong></td>
<td><strong>Status</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
###########333 department name 
 $dname= mysqli_fetch_array(mysqli_query($link1,"select dname from hrms_department_master where departmentid = '".$row_loc['departmentid']."' "));	
 $dept= mysqli_fetch_array(mysqli_query($link1,"select designame from hrms_designation_master where designationid = '".$row_loc['designationid']."' "));	
  $manager= mysqli_fetch_array(mysqli_query($link1,"select empname from hrms_employe_master where loginid = '".$row_loc['managerid']."' "));
  $party_det = mysqli_fetch_array(mysqli_query($link1, "select name  from asc_master where asc_code='" . $row_loc['mapped_loc'] . "'"));

?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['loginid']?></td>
<td align="left"><?=$row_loc['empname']?></td>
<td align="left"><?=$row_loc['state']?></td>
<td align="right"><?=$row_loc['city']?></td>
<td align="left"><?=$row_loc['email']?></td>
<td align="left"><?=$row_loc['phone']?></td>

<td align="left"><?=$row_loc['address']?></td>

<td align="left"><?=$row_loc['date_of_birth']?></td>
<td align="left"><?php if($row_loc['utype'] == 'EMP'){echo "Employee";} elseif($row_loc['utype'] == 'MEMP'){echo "Manager";}?></td>
<td align="left"><?=$row_loc['emp_type']?></td>
<td align="left"><?=$party_det['name']?></td>
<td align="left"><?=$dname['dname']?></td>
<td align="left"><?=$dept['designame']?></td>
<td align="left"><?=$manager['empname']?></td>
<td align="left"><?=$row_loc['joining_date']?></td>
<td align="left"><?=$row_loc['resign_date']?></td>
<td align="left"><?=$row_loc['status']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>