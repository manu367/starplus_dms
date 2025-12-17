<?php
print("\n");
print("\n");
////// filters value/////
$user_id = base64_decode($_REQUEST['user_id']);
$fromdate = base64_decode($_REQUEST['fromDate']);
$todate = base64_decode($_REQUEST['toDate']);
//////End filters value///// 
?>
<table width="100%" border="1" class="table table-bordered">
  <thead>
  <tr style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
    <th width="5%">S.No.</th>
    <th width="7%">User Name</th>
    <th width="5%">Employee Id</th>
    <th width="12%">Visit Date</th>
    <th width="13%">Visit City</th>
    <th width="8%">Dealer Type</th>
    <th width="10%">Dealer Name</th>
    <th width="10%">Dealer City</th>
    <th width="10%">Dealer State</th>
    <th width="10%">Dealer Code</th>
    <th width="20%">Dealer Addres</th>
    <th width="20%">Remark</th>
  </tr>
  </thead>
  <tbody>
  <?php
	$i = 1;
	if($user_id){ $uid = "userid ='".$user_id."'";}else{ $uid = "1";}
	$res_dv = mysqli_query($link1,"SELECT * FROM dealer_visit WHERE ".$uid." AND visit_date >= '".$fromdate."' AND visit_date <= '".$todate."' ORDER BY id DESC")or die("ER1 ".mysqli_error($link1));
	if(mysqli_num_rows($res_dv)>0){
	while($row_dv = mysqli_fetch_array($res_dv)){
		$cordinate ="'".$row_dv["latitude"].", ".$row_dv["longitude"]."'";
		$center_loc = $row_dv["latitude"].", ".$row_dv["longitude"];
		$username = mysqli_fetch_assoc(mysqli_query($link1, "SELECT name,oth_empid FROM admin_users WHERE username='".$row_dv['userid']."'"));
		$dealer_det = explode("~",getAnyDetails($row_dv["party_code"],"name,city,state","asc_code","asc_master",$link1));
	?>
  <tr>
    <td align="center"><?=$i?></td>
    <td align=""><?= $username['name']." | ".$row_dv['userid'];?></td>
    <td align=""><?=$username['oth_empid'];?></td>
    <td align="center"><?=$row_dv["visit_date"]?></td>
    <td><?=$row_dv["visit_city"]?></td>
    <td><?=$row_dv["dealer_type"]?></td>
    <td><?=$dealer_det[0]?></td>
    <td><?=$dealer_det[1]?></td>
    <td><?=$dealer_det[2]?></td>
    <td><?=$row_dv["party_code"]?></td>
    <td><?=$row_dv["address"]?></td>
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
    <td colspan="8" align="center">No data found</td>
  </tr>
  <?php
  }
  ?>
  </tbody>
</table>