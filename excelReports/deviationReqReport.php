<?php
print("\n");
print("\n");
////// filters value/////
$task_type = base64_decode($_REQUEST['taskType']);
$assign_to = base64_decode($_REQUEST['assignTo']);
$fromdate = base64_decode($_REQUEST['fromDate']);
$todate = base64_decode($_REQUEST['toDate']);
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	
}else{
	$team = getTeamMembers($_SESSION['userid'],$link1);
	if($team){
		$team = $team.",'".$_SESSION['userid']."'"; 
	}else{
		$team = "'".$_SESSION['userid']."'"; 
	}
}
//////End filters value///// 
?>
<table width="100%" border="1" class="table table-bordered">
  <thead>
  <tr style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
    <th width="5%">S.No.</th>
    <th width="7%">User Name</th>
    <th width="5%">Employee Id</th>
    <th width="12%">Scheduled Date</th>
    <th width="13%">Scheduled Visit</th>
    <th width="8%">Change Visit</th>
    <th width="10%">Request Raised On</th>
    <th width="20%">Request Remark</th>
    <th width="20%">Approval Status</th>
  </tr>
  </thead>
  <tbody>
  <?php
  	$filter_str = 1;
	if($fromdate !=''){
		$filter_str	.= " and DATE(entry_date) >= '".$fromdate."'";
	}
	if($todate !=''){
		$filter_str	.= " and DATE(entry_date) <= '".$todate."'";
	}
	if($task_type){
		$filter_str	.= " and task_type = '".$task_type."'";
	}
	/*if($assign_to){
		$filter_str	.= " and entry_by = '".$assign_to."'";
	}*/
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	if($assign_to){
		$team2 = getTeamMembers($assign_to,$link1);
		if($team2){
			$team2 = $team2.",'".$assign_to."'"; 
		}else{
			$team2 = "'".$assign_to."'"; 
		}
		$filter_str	.= " AND entry_by IN (".$team2.")";
	}else{
		$filter_str	.= " ";
	}
}else{
	if($assign_to){
		$team3 = getTeamMembers($assign_to,$link1);
		if($team3){
			$team3 = $team2.",'".$assign_to."'"; 
		}else{
			$team3 = "'".$assign_to."'"; 
		}
		$filter_str	.= " AND entry_by IN (".$team3.")";
	}else{
		$filter_str	.= " AND entry_by IN (".$team.")";
	}
}
	$i = 1;
	//if($_SESSION["userid"]=="admin"){
		$sql1 = "SELECT * FROM deviation_request WHERE ".$filter_str." order by entry_date DESC";
	//}else{
		//$sql1 = "SELECT * FROM deviation_request WHERE ".$filter_str." AND entry_by IN (SELECT username FROM admin_users WHERE reporting_manager='".$_SESSION["userid"]."') AND app_status='Pending For Approval' order by entry_date DESC";
	//}
	
	$rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	if(mysqli_num_rows($rs1)>0){
	while($row1=mysqli_fetch_assoc($rs1)) { 
		$username = mysqli_fetch_assoc(mysqli_query($link1, "SELECT name,oth_empid FROM admin_users WHERE username='".$row1['entry_by']."'"));
		$schdate = mysqli_fetch_assoc(mysqli_query($link1,"SELECT plan_date FROM pjp_data WHERE id='".$row1["pjp_id"]."'"));
	?>
  <tr>
    <td align="center"><?=$i?></td>
    <td align=""><?= $username['name']." | ".$row1['entry_by'];?></td>
    <td align=""><?=$username['oth_empid'];?></td>
   <td><?php echo $schdate['plan_date']?></td>
    <td><?php echo $row1['sch_visit']?></td>
    <td><?php echo $row1['change_visit']?></td>
    <td><?php echo $row1['entry_date']?></td>
    <td><?php echo $row1['remark']?></td>
    <td><?php echo $row1['app_status']?></td>
  </tr>
  <?php
		$i++;
    }
  ?>
  
  <?php 
  }else{
  ?>
  <tr>
    <td colspan="9" align="center">No data found</td>
  </tr>
  <?php
  }
  ?>
  </tbody>
</table>