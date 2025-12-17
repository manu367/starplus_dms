<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$username=base64_decode($_REQUEST['user']);
$fromdate = base64_decode($_REQUEST['fdate']);
$todate = base64_decode($_REQUEST['tdate']);
$department = base64_decode($_REQUEST['department']);
$subdepartment = base64_decode($_REQUEST['subdepartment']);
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	
}else{
	$team = getTeamMembers($_SESSION['userid'],$link1);
	if($team){
		$team = $team.",'".$_SESSION['userid']."'"; 
	}else{
		$team = "'".$_SESSION['userid']."'"; 
	}
}
////// filters value/////
$filter_str = "";
if($fromdate !=''){
	$filter_str	.= " AND a.entry_date >= '".$fromdate."'";
}
if($todate !=''){
	$filter_str	.= " AND a.entry_date <= '".$todate."'";
}
/*if($username !=''){
	$filter_str	.= " AND a.userid = '".$username."'";
}*/
if($department){
	$deptqry = " AND b.department ='".$department."'";
}else{
	$deptqry = "";
}
if($subdepartment){
	$subdeptqry = " AND b.subdepartment ='".$subdepartment."'";
}else{
	$subdeptqry = "";
}
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	if($username){
		$team2 = getTeamMembers($username,$link1);
		if($team2){
			$team2 = $team2.",'".$username."'"; 
		}else{
			$team2 = "'".$username."'"; 
		}
		$filter_str .= " AND a.userid IN (".$team2.")";
	}else{
		$filter_str .= " ";
	}
}else{
	if($username){
		$team3 = getTeamMembers($username,$link1);
		if($team3){
			$team3 = $team3.",'".$username."'"; 
		}else{
			$team3 = "'".$username."'"; 
		}
		$filter_str .= " AND a.userid IN (".$team3.")";
	}else{
		$filter_str .= " AND a.userid IN (".$team.")";
	}
}
//////End filters value/////
$sql1 = "SELECT a.*, b.name, b.oth_empid, b.designationid, b.department, b.subdepartment FROM user_track a, admin_users b WHERE 1=1 AND a.userid=b.username ".$subdeptqry." ".$deptqry." ".$filter_str."";
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>User Id</strong></td>
<td><strong>User Name</strong></td>
<td><strong>Emp Id</strong></td>
<td><strong>Department</strong></td>
<td><strong>Sub-Department</strong></td>
<td><strong>Designation</strong></td>
<td><strong>Task Name</strong></td>
<td><strong>Task Action</strong></td>
<td><strong>Ref. No.</strong></td>
<td><strong>Latitude</strong></td>
<td><strong>Longitude</strong></td>
<td><strong>Location Address</strong></td>
<td><strong>Track Date</strong></td>
<td><strong>Update On</strong></td>
</tr>
<?php
$i=1;
$rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
while($row1=mysqli_fetch_assoc($rs1)) {
	if($row1['userid']!=""){
?>
<tr>
<td align="left"><?=$i?></td>
<td><?=$row1['userid']?></td>
<td><?=$row1['name']?></td>
<td><?=$row1['oth_empid']?></td>
<td><?=getAnyDetails($row1["department"],"dname","departmentid","hrms_department_master",$link1)?></td>
<td><?=getAnyDetails($row1["subdepartment"],"subdept","subdeptid","hrms_subdepartment_master",$link1)?></td>
<td><?=getAnyDetails($row1["designationid"],"designame","designationid","hrms_designation_master",$link1)?></td>
<td><?=$row1["task_name"]?></td>
<td><?=$row1["task_action"]?></td>
<td><?=$row1["ref_no"]?></td>
<td><?=$row1["latitude"]?></td>
<td><?=$row1["longitude"]?></td>
<td><?=$row1["address"]?></td>
<td><?=$row1["entry_date"]?></td>
<td><?=$row1["update_date"]?></td>
</tr>
<?php
	$i+=1;	
	}	
}
?>
</table>
