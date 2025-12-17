<?php
print("\n");
print("\n");
//// date function
function dt_format1($dt_sel)
{
 return substr($dt_sel,8,2)."-".substr($dt_sel,5,2)."-".substr($dt_sel,0,4);
}
//// time function
function time_format($t_sel)
{
 return  substr($t_sel,11,2).''.substr($t_sel,13,3).':'.substr($t_sel,17,3);
}
////// filters value/////
$username = base64_decode($_REQUEST['user_id']);
$fromdate = base64_decode($_REQUEST['fromDate']);
$todate = base64_decode($_REQUEST['toDate']);
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
		$user_id = " AND b.username IN (".$team2.")";
	}else{
		$user_id = " ";
	}
}else{
	if($username){
		$team3 = getTeamMembers($username,$link1);
		if($team3){
			$team3 = $team3.",'".$username."'"; 
		}else{
			$team3 = "'".$username."'"; 
		}
		$user_id = " AND b.username IN (".$team3.")";
	}else{
		$user_id = " AND b.username IN (".$team.")";
	}
}
//////End filters value/////
$sqldata = "SELECT a.*, b.name, b.oth_empid, b.designationid, b.department, b.subdepartment FROM user_travel_plan a, admin_users b WHERE 1=1 AND a.user_id=b.username ".$subdeptqry." ".$deptqry." ".$user_id;
if ($fromdate != '' || $todate != '') {
    $sqldata.=" and a.insert_date BETWEEN '" . $fromdate . "' and '" . $todate . "'";
}
$sqldata.=" order by a.id desc";
$sql = mysqli_query($link1, $sqldata);
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>S.No.</strong></td>
        <th>Employee Name</th>
        <th>Employee Id</th>
        <th>User Id</th>
        <th>Designation</th>
        <th>Department</th>
        <th>Sub-Department</th>
        <th>In Status</th>
        <th>Date</th>
        <th>IN Time</th>
        <th>IN Address</th>
        <th>OUT Status</th>
        <th>OUT Time</th>
        <th>OUT Address</th>
    </tr>
    <?php
    $i = 1;
    while ($row = mysqli_fetch_assoc($sql)) {
        $username = mysqli_fetch_assoc(mysqli_query($link1, "Select name,oth_empid from admin_users where username='" . $row['user_id'] . "'"));
        ?>
        <tr>
            <td align="left"><?=$i;?></td>
            <td><?=$username['name']; ?></td>
            <td width='5%'><?=$username['oth_empid']; ?></td>
            <td width='5%'><?=$row['user_id']?></td>
            <td><?=getAnyDetails($row["designationid"],"designame","designationid","hrms_designation_master",$link1)?></td>
            <td><?=getAnyDetails($row["department"],"dname","departmentid","hrms_department_master",$link1)?></td>
            <td><?=getAnyDetails($row["subdepartment"],"subdept","subdeptid","hrms_subdepartment_master",$link1)?></td>
            <td width='5%'><?=$row['status_in']; ?></td>
            <td><?=dt_format1($row['in_datetime']); ?></td>
            <td><?=time_format($row['in_datetime']); ?></td>
            <td><?=$row['address_in']; ?></td> 
            <td width='5%'><?=$row['status_out']; ?></td>
            <td><?=time_format($row['out_datetime']); ?></td>
            <td><?=$row['address_out']; ?></td>
        </tr>
        <?php
        $i+=1;
    }
    ?>
</table>
