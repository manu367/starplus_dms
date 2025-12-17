<?php
print("\n");
print("\n");
////// filters value/////
$from_date=base64_decode($_REQUEST['fdate']);
$to_date=base64_decode($_REQUEST['tdate']);
$userid=base64_decode($_REQUEST['userid']);
$psc=base64_decode($_REQUEST['psc']);
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
	$filter_str	.= " AND a.entry_date >= '".$from_date."'";
}
if($to_date !=''){
	$filter_str	.= " AND a.entry_date <= '".$to_date."'";
}

$filter_str	.= " AND a.entry_by IN (".$team.")";

//////End filters value/////

$sqldata = "SELECT a.*, b.name, b.oth_empid, b.designationid, b.department, b.subdepartment, b.oth_empid FROM query_master a, admin_users b WHERE 1=1 AND a.entry_by=b.username ".$subdeptqry." ".$deptqry." ".$filter_str."";

$sql = mysqli_query($link1, $sqldata);
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>S.No.</strong></td>
        <th>Feedback Type</th>
        <th>Feedback For</th>
        <th>Subject</th>
        <th>Feedback</th>
        <th>Party Name</th>
        <th>Contact No.</th>
        <th>Updated By</th>
        <th>Designation</th>
        <th>Department</th>
        <th>Sub-Department</th>
        <th>Employee Id</th>
        <th>User Id</th>
        <th>Updated Date</th>
        <th>Address</th>
    </tr>
    <?php
    $i = 1;
    while ($row = mysqli_fetch_assoc($sql)) {
        $expl = explode("~",$row["request"]);
        ?>
        <tr>
            <td align="left"><?=$i;?></td>
            <td><?=$row['problem'];?></td>
            <td width='5%'><?=$row['module'];?></td>
            <td width='5%'><?=$expl[0]?></td>
            <td width='5%'><?=$expl[1]; ?></td>
            <td><?=$row['party_name'];?></td>
            <td><?=$row['party_contact'];?></td>
            <td><?=$row['name'];?></td>
            <td><?=getAnyDetails($row["designationid"],"designame","designationid","hrms_designation_master",$link1)?></td>
            <td><?=getAnyDetails($row["department"],"dname","departmentid","hrms_department_master",$link1)?></td>
            <td><?=getAnyDetails($row["subdepartment"],"subdept","subdeptid","hrms_subdepartment_master",$link1)?></td>
            <td width='5%'><?=$row['oth_empid']; ?></td>
            <td width='5%'><?=$row['entry_by']?></td>
            <td><?=$row['entry_date'];?></td>
            <td><?=$row['address']; ?></td> 
        </tr>
        <?php
        $i+=1;
    }
    ?>
</table>
