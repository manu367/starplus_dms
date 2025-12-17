<?php
print("\n");
print("\n");
////// filters value/////
$from_date=base64_decode($_REQUEST['fdate']);
$to_date=base64_decode($_REQUEST['tdate']);
$userid=base64_decode($_REQUEST['userid']);
$psc=base64_decode($_REQUEST['psc']);
$task_name=base64_decode($_REQUEST['rheader']);
///// get team members
$team = getTeamMembers($userid,$link1);
if($team){
	$team .= $team.",'".$userid."'"; 
}else{
	$team .= "'".$userid."'"; 
}
////// filters value/////
$filter_str = "";
if($from_date !=''){
	$filter_str	.= " AND activity_date >= '".$from_date."'";
}
if($to_date !=''){
	$filter_str	.= " AND activity_date <= '".$to_date."'";
}

$filter_str	.= " AND user_id IN (".$team.")";

//////End filters value/////

$sqldata = "SELECT * FROM activity_master WHERE activity_type='".$task_name."' ".$filter_str."";

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
        <th>Activity No.</th>
        <th>Activity Type</th>
        <th>Party Name</th>
        <th>Party Contact</th>
        <th>Initial Remark</th>
        <th>Status</th>
        <th>Activity Date</th>
        <th>Start Date/Time</th>
        <th>Start Geo Location</th>
        <th>End Date/Time</th>
        <th>End Geo Location</th>
    </tr>
    <?php
    $i = 1;
    while ($row = mysqli_fetch_assoc($sql)) {
		$empdet = explode("~",getAnyDetails($row["user_id"],"name,oth_empid,designationid,department,subdepartment","username","admin_users",$link1));
		///// for start
		$row_hist1 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT entry_date, address FROM activity_history WHERE ref_no LIKE '".$row["ref_no"]."' AND status='Start'"));
		//// for end
		$row_hist2 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT entry_date, address FROM activity_history WHERE ref_no LIKE '".$row["ref_no"]."' AND status='Complete'"));
        ?>
        <tr>
            <td align="left"><?=$i;?></td>
            <td><?=$empdet[0];?></td>
            <td><?=$empdet[1];?></td>
            <td><?=$row["user_id"];?></td>
            <td><?=getAnyDetails($empdet[2],"designame","designationid","hrms_designation_master",$link1)?></td>
            <td><?=getAnyDetails($empdet[3],"dname","departmentid","hrms_department_master",$link1)?></td>
            <td><?=getAnyDetails($empdet[4],"subdept","subdeptid","hrms_subdepartment_master",$link1)?></td>
            <td><?=$row["ref_no"];?></td>
            <td><?=$row["activity_type"]; ?></td>
            <td><?=$row['party_name'];?></td>
            <td><?=$row['party_contact'];?></td>
            <td><?=$row['intial_remark'];?></td>
            <td><?=$row['status'];?></td>
            <td><?=$row['activity_date'];?></td> 
            <td><?=$row_hist1['entry_date'];?></td>
            <td><?=$row_hist1['address'];?></td>
            <td><?=$row_hist2['entry_date'];?></td>
            <td><?=$row_hist2['address'];?></td> 
        </tr>
        <?php
        $i+=1;
    }
    ?>
</table>
