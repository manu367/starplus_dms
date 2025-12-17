<?php 
print("\n");
print("\n");
$selyear = base64_decode($_REQUEST['selyear']);
$selmonth = base64_decode($_REQUEST['selmonth']);
$user_id = base64_decode($_REQUEST['user_id']);
$filter_str = "";
if($selyear !=''){
	$filter_str	.= " AND year = '".$selyear."'";
}
if($selmonth !=''){
	$mnth = date("m", strtotime($selmonth."-".$selyear));
	$filter_str	.= " AND month = '".$mnth."'";
}
if($_SESSION['userid']=="admin"){
	if($user_id){
		$team2 = getTeamMembers($user_id,$link1);
		if($team2){
			$team2 = $team2.",'".$user_id."'"; 
		}else{
			$team2 = "'".$user_id."'"; 
		}
		$filter_str	.= " AND user_id IN (".$team2.")";
	}else{
		$filter_str	.= " ";
	}
}else{
	if($user_id){
		$team3 = getTeamMembers($user_id,$link1);
		if($team3){
			$team3 = $team2.",'".$user_id."'"; 
		}else{
			$team3 = "'".$user_id."'"; 
		}
		$filter_str	.= " AND user_id IN (".$team3.")";
	}else{
		$filter_str	.= " AND user_id IN (".$team.")";
	}
}
$sql=mysqli_query($link1,"SELECT * FROM dealer_target WHERE 1 ".$filter_str." ")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Target No</strong></td>
<td><strong>Party Name</strong></td>
<td><strong>Product Code</strong></td>
<td><strong>Target Value</strong></td>
<td><strong>Month</strong></td>
<td><strong>Year</strong></td>
<td><strong>Emp ID</strong></td>
<td><strong>User ID</strong></td>
<td><strong>Remark</strong></td>
<td><strong>Status</strong></td>
<td><strong>Entry By</strong></td>
<td><strong>Entry Date</strong></td>

</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
	$mnthh = "";	
	if($row['month'] == '01'){ $mnthh = "JAN"; }
	else if($row_loc['month'] == '02'){ $mnthh = "FEB"; }
	else if($row_loc['month'] == '03'){ $mnthh = "MAR"; }
	else if($row_loc['month'] == '04'){ $mnthh = "APR"; }
	else if($row_loc['month'] == '05'){ $mnthh = "MAY"; }
	else if($row_loc['month'] == '06'){ $mnthh = "JUN"; }
	else if($row_loc['month'] == '07'){ $mnthh = "JUL"; }
	else if($row_loc['month'] == '08'){ $mnthh = "AUG"; }
	else if($row_loc['month'] == '09'){ $mnthh = "SEP"; }
	else if($row_loc['month'] == '10'){ $mnthh = "OCT"; }
	else if($row_loc['month'] == '11'){ $mnthh = "NOV"; }
	else if($row_loc['month'] == '12'){ $mnthh = "DEC"; }
	else{ $mnthh = $row_loc['month'];}
 ?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['target_no']?></td>
<td><?=getAnyDetails($row_loc["party_code"],"name,city,state","asc_code","asc_master",$link1)?></td>
<td align="left"><?=$row_loc['prod_code']?></td>
<td align="left"><?=$row_loc['target_val']?></td>
<td align="left"><?=$mnthh?></td>
<td align="left"><?=$row_loc['year']?></td>
<td align="left"><?=$row_loc['emp_id']?></td>
<td><?=getAdminDetails($row_loc['user_id'],"name,username",$link1)?></td>
<td align="left"><?=$row_loc['remark']?></td>
<td align="left"><?=$row_loc['status']?></td>
<td align="left"><?=$row_loc['entry_by']?></td>
<td align="left"><?=$row_loc['entry_date']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>