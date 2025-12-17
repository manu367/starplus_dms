<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
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
	$dept = " AND department ='".$department."'";
}else{
	$dept = "";
}
if($subdepartment){
	$subdept = " AND subdepartment ='".$subdepartment."'";
}else{
	$subdept = "";
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
		$user_id = " AND username IN (".$team.")";
	}
}
//// make emplye array
$arr_emp = array();
$res_emp = mysqli_query($link1, "SELECT username, name, oth_empid, designationid, department, subdepartment FROM admin_users WHERE 1 ".$user_id." ".$dept." ".$subdept." ORDER BY name");
while($row_emp = mysqli_fetch_assoc($res_emp)){
	$arr_emp[$row_emp["username"]] = $row_emp["name"]."~".$row_emp["oth_empid"]."~".$row_emp["designationid"]."~".$row_emp["department"]."~".$row_emp["subdepartment"];
}
////////////////////////////
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<thead>
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<th width="10%">Employee Name</th>
<th width="10%">Employee Id</th>
<th>User Id</th>
<th>Designation</th>
<th>Department</th>
<th>Sub-Department</th>
<th width="10%">Date</th>
<th width="10%">Present</th>
<th width="20%">In Time (HH:MM:SS)</th>
<th width="20%">Out Time (HH:MM:SS)</th>
<th width="20%">Working Hours (HH:MM:SS)</th>
</tr>
</thead>
<tbody>
<?php
$flag = 0;
$d = daysDifference($todate,$fromdate);
////// loop for all employee
foreach($arr_emp as $usrid => $userdet){
	$expld_emp = explode("~",$userdet);
	////// loop for all days
	for($k=0; $k<=$d; $k++){
  		$make_date = date('Y-m-d', strtotime($fromdate. ' + '.$k.' days'));
  		///// get attendance report
  		$res = mysqli_query($link1,"SELECT user_id, in_datetime, out_datetime, insert_date FROM user_attendence WHERE user_id='".$usrid."' AND insert_date='".$make_date."'");
  		$num = mysqli_num_rows($res);
  		///// check present on selected date or not
  		if($num>0){
			$row = mysqli_fetch_assoc($res);
			$intime = substr($row["in_datetime"],11,8);
			$outtime = substr($row["out_datetime"],11,8);
			if($row["out_datetime"] == "0000-00-00 00:00:00"){
				if($row['insert_date']!=$today){
					$wkh = "Logout missing";
					$incls = "bg-danger";
				}else{
					$wkh = "";
					$incls = "bg-success";
				}
			}
			else{
				$wkh = getHoursMinSec(strtotime($row["out_datetime"])-strtotime($row["in_datetime"]));
				if($wkh < "08:50:00"){ $incls = "bg-danger";}else{ $incls = "bg-success";}
			}
			$status = "Present";
			$cls = "bg-success";
			$flag = 1;
		}else{
			$intime = "";
			$outtime = "";
			$status = "Absent";
			$wkh = "";
			$flag = 0;
		}
		if($flag==1){ 
		?>
		<tr class="<?=$cls?>">
            <td><?=$expld_emp[0]?></td>
            <td><?=$expld_emp[1]?></td>
            <td><?=$row["user_id"]?></td>
            <td><?=getAnyDetails($expld_emp[2],"designame","designationid","hrms_designation_master",$link1)?></td>
            <td><?=getAnyDetails($expld_emp[3],"dname","departmentid","hrms_department_master",$link1)?></td>
            <td><?=getAnyDetails($expld_emp[4],"subdept","subdeptid","hrms_subdepartment_master",$link1)?></td>
            <td><?=$make_date?></td>
            <td><?=$status?></td>
            <td><?=$intime?></td>
            <td><?=$outtime?></td>
            <td class="<?=$incls?>"><?=$wkh?></td>
		</tr>
		<?php 
		}else{
		?>
		<tr class="<?=$cls?>">
            <td><?=$expld_emp[0]?></td>
            <td><?=$usrid?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><?=$make_date?></td>
            <td align="left"><?=$status?></td>
            <td align="left">&nbsp;</td>
            <td align="left">&nbsp;</td>
            <td align="left"><?=$status?></td>
       	</tr>
	<?php 
		}
	}
}
?>
</tbody>
</table>