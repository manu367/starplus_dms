<?php
print("\n");
print("\n");
////// filters value/////
$fromdate=base64_decode($_REQUEST['fdate']);
$todate=base64_decode($_REQUEST['tdate']);
$userid=base64_decode($_REQUEST['userid']);
$psc=base64_decode($_REQUEST['psc']);
//////End filters value///// 
///// get team members
$team = getTeamMembers($userid,$link1);
if($team){
	$team .= $team.",'".$userid."'"; 
}else{
	$team .= "'".$userid."'"; 
}
?>
<table width="100%" border="1" class="table table-bordered">
  <thead>
  <tr style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
    <th width="5%">S.No.</th>
    <th width="7%">User Name</th>
    <th width="5%">Employee Id</th>
    <th width="12%">Visit Date</th>
    <th width="13%">Visit City</th>
    <th width="8%">Scheduled Visit</th>
    <th width="8%">Change Visit</th>
    <th width="8%">Deviation App By</th>
    <th width="8%">Deviation App Date</th>
    <th width="8%">Deviation App Status</th>
    <th width="8%">Deviation App Remark</th>
    <th width="8%">Dealer Type</th>
    <th width="10%">Dealer Name</th>
    <th width="10%">Dealer City</th>
    <th width="10%">Dealer State</th>
    <th width="10%">Dealer Code</th>
    <th width="20%">Geo Addres</th>
    <th width="20%">Remark</th>
  </tr>
  </thead>
  <tbody>
  <?php
	$i = 1;
	$res_dv = mysqli_query($link1,"SELECT * FROM dealer_visit WHERE userid IN (".$team.") AND visit_date >= '".$fromdate."' AND visit_date <= '".$todate."'")or die("ER1 ".mysqli_error($link1));
	if(mysqli_num_rows($res_dv)>0){
	while($row_dv = mysqli_fetch_array($res_dv)){
		$cordinate ="'".$row_dv["latitude"].", ".$row_dv["longitude"]."'";
		$center_loc = $row_dv["latitude"].", ".$row_dv["longitude"];
		$username = mysqli_fetch_assoc(mysqli_query($link1, "SELECT name,oth_empid FROM admin_users WHERE username='".$row_dv['userid']."'"));
		$dealer_det = explode("~",getAnyDetails($row_dv["party_code"],"name,city,state","asc_code","asc_master",$link1));
		/////PJP details
		if($row_dv["dealer_type"]=="Old"){
			$res_pjp = mysqli_query($link1, "SELECT * FROM pjp_data WHERE id='".$row_dv['pjp_id']."'");
			$row_pjp = mysqli_fetch_assoc($res_pjp);
		}else{
			$res_pjp = mysqli_query($link1, "SELECT * FROM pjp_data WHERE plan_date='".$row_dv['visit_date']."' AND task='Dealer Visit' AND assigned_user='".$row_dv['userid']."'");
			$row_pjp = mysqli_fetch_assoc($res_pjp);
		}
		/////deviation details
		$res_devi = mysqli_query($link1, "SELECT * FROM deviation_request WHERE pjp_id='".$row_pjp['id']."'");
		$row_devi = mysqli_fetch_assoc($res_devi);
	?>
  <tr>
    <td align="center"><?=$i?></td>
    <td align=""><?= $username['name']." | ".$row_dv['userid'];?></td>
    <td align=""><?=$username['oth_empid'];?></td>
    <td align="center"><?=$row_dv["update_time"]?></td>
    <td><?=$row_dv["visit_city"]?></td>
    <td><?php if($row_devi["sch_visit"]){ echo $row_devi["sch_visit"];}else{echo $row_pjp["visit_area"];}?></td>
    <td><?=$row_devi["change_visit"]?></td>
    <td><?=getAdminDetails($row_devi["app_by"],"name,oth_empid,username",$link1)?></td>
    <td><?=$row_devi["app_date"]?></td>
    <td><?=$row_devi["app_status"]?></td>
    <td><?=$row_devi["app_remark"]?></td>
    <td><?=$row_dv["dealer_type"]?></td>
    <td><?=$dealer_det[0]?></td>
    <td><?=$dealer_det[1]?></td>
    <td><?=$dealer_det[2]?></td>
    <td><?=$row_dv["party_code"]?></td>
    <td><?php if($row_dv["address"]){ echo $row_dv["address"];}else{ 
	$row_usrtrck = mysqli_fetch_assoc(mysqli_query($link1,"SELECT address FROM user_track WHERE latitude LIKE '".$row_dv["latitude"]."' AND longitude='".$row_dv["longitude"]."' AND address!=''")); echo $row_usrtrck["address"];}?></td>
    <td><?=$row_dv["remark"]?></td>
  </tr>
  <?php
		$i++;
    }
  ?>
  
  <?php 
  }else{
  ?>
  <tr>
    <td colspan="18" align="center">No data found</td>
  </tr>
  <?php
  }
  ?>
  </tbody>
</table>
