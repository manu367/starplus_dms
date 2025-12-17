<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$fdate = base64_decode($_REQUEST['fdate']);
$tdate = base64_decode($_REQUEST['tdate']);
$assign_to = base64_decode($_REQUEST['assign_to']);
$task_type = base64_decode($_REQUEST['task_type']);
$filter_str = 1;
if($fdate !=''){
	$filter_str	.= " and DATE(entry_date) >= '".$fdate."'";
}
if($tdate !=''){
	$filter_str	.= " and DATE(entry_date) <= '".$tdate."'";
}
if($task_type){
	$filter_str	.= " and task = '".$task_type."'";
}
if($assign_to){
	$filter_str	.= " and assigned_user = '".$assign_to."'";
}

//////End filters value/////
$sql=mysqli_query($link1,"SELECT * FROM pjp_data WHERE ".$filter_str." ORDER BY assigned_user,entry_date")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>System Ref. No.</strong></td>
<td><strong>Scheduled Date</strong></td>
<td><strong>Entry By</strong></td>
<td><strong>Plan Name</strong></td>
<td><strong>Plan Date</strong></td>
<td><strong>Assigned To</strong></td>
<td><strong>Visit Area</strong></td>
<td><strong>Task Target Count</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['document_no']?></td>
<td align="left"><?=$row_loc['entry_date']?></td>
<td align="left"><?=getAdminDetails($row_loc['entry_by'],"name",$link1).",".$row_loc['entry_by']?></td>
<td align="left"><?=$row_loc['task']?></td>
<td align="left"><?=$row_loc['plan_date']?></td>
<td align="left"><?=getAdminDetails($row_loc['assigned_user'],"name",$link1).",".$row_loc['assigned_user']?></td>
<td align="left"><?=$row_loc['visit_area']?></td>
<td align="left"><?=$row_loc['task_count']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>
