<?php
print("\n");
print("\n");
////// filters value/////
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

$status = base64_decode($_REQUEST['status']);
$fromdate = base64_decode($_REQUEST['fromDate']);
$todate = base64_decode($_REQUEST['toDate']);

if($status)
 {
	 $newstatus= " and status = '" . $status . "'";
 }
 else {
	 $newstatus= "";
	 }
//////End filters value/////

$sqldata = "Select * from hrms_leave_request where entry_date BETWEEN '" . $fromdate . "' and '" . $todate . "' $newstatus  order by id desc";

$sql = mysqli_query($link1, $sqldata);
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>S.No.</strong></td>
        <th>Employee Name</th>       
        <th>Empid</th>
        <th>Leave Type</th>        
        <th>From Date</th>
        <th>To Date</th>
        <th>Leave Duration</th>
        <th>Purpose</th>
        <th>Description</th>
        <th>Status</th>
        <th>Approve By</th>
        <th>Approve Date </th>
        <th>Remark  </th>
       
    </tr>
    <?php
    $i = 1;
    while ($row = mysqli_fetch_assoc($sql)) {
		$empname = mysqli_fetch_assoc(mysqli_query($link1, "Select empname from hrms_employe_master where loginid  ='" . $row['empid'] . "'"));
        $username = mysqli_fetch_assoc(mysqli_query($link1, "Select name from admin_users where username='" . $row['approve_by'] . "'"));
        ?>
        <tr>
            <td align="left"><?=$i;?></td>
            <td><?=$empname['empname']; ?></td>
            <td><?=$row['empid']; ?></td>
            <td><?=$row['leave_type']; ?></td>
            <td><?=dt_format1($row['from_date']); ?></td>
            <td><?=dt_format1($row['to_date']); ?></td>
            <td><?=$row['leave_duration']; ?></td>  
            <td><?=$row['purpose']; ?></td> 
            <td><?=$row['description']; ?></td> 
            <td><?php if($row['status'] == '3') {echo "Pending for Approval";} else if($row['status'] == '4') {echo "Approved";} else if($row['status'] == '5') {echo "Reject";} else {echo $row['status'];}?></td> 
            <td><?=dt_format1($row['approve_date']); ?></td>         
             <td><?=$username['name']; ?></td>
              <td><?=$row['remark']; ?></td>

        </tr>
        <?php
        $i+=1;
    }
    ?>
</table>
