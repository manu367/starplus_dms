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


$fromdate = base64_decode($_REQUEST['fromDate']);
$todate = base64_decode($_REQUEST['toDate']);
$status = base64_decode($_REQUEST['status']);

//////End filters value/////
if($status ==  'IR'){

 $sqldata = "Select * from hrms_request_icard where update_date BETWEEN '" . $fromdate . "' and '" . $todate . "'   order by sno desc";

$sql = mysqli_query($link1, $sqldata);
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>S.No.</strong></td>
        <th>Employee Name</th>       
        <th>Empid</th>
        <th>Manager</th>        
        <th>Designation</th>
        <th>Emergeny No.</th>
        <th>Status</th>
        <th>Remark</th>
    </tr>
    <?php
    $i = 1;
    while ($row = mysqli_fetch_assoc($sql)) {
		$empname = mysqli_fetch_assoc(mysqli_query($link1, "Select empname from hrms_employe_master where loginid  ='" . $row['emp_id'] . "'"));
        $managername = mysqli_fetch_assoc(mysqli_query($link1, "Select empname from hrms_employe_master where loginid='" . $row['mgr_id'] . "'"));
		$dsignation = mysqli_fetch_assoc(mysqli_query($link1, "Select designame from hrms_designation_master where designationid='" . $row['designation'] . "'"));
        ?>
        <tr>
            <td align="left"><?=$i;?></td>
            <td><?=$empname['empname']; ?></td>
            <td><?=$row['emp_id']; ?></td>
            <td><?=$managername['empname']; ?></td>
            <td><?=$dsignation['designame']; ?></td>
            <td><?=$row['emergency_no']; ?></td>
             <td><?=$row['status']; ?></td>
             <td><?=$row['remark']; ?></td>            
        </tr>
        <?php
        $i+=1;
    }

    ?>
</table>
<?php } else if($status ==  'VR'){ 
	
	$sqldata_vr = "Select * from hrms_request_vcard where update_date BETWEEN '" . $fromdate . "' and '" . $todate . "'   order by sno desc";

$sqlvr = mysqli_query($link1, $sqldata_vr);
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>S.No.</strong></td>
        <th>Employee Name</th>       
        <th>Empid</th>
        <th>Manager</th>        
        <th>Designation</th>
        <th>Mobile  No.</th>
        <th>Email</th>
        <th>Status</th>
        <th>Remark</th>
    </tr>
    <?php
    $j = 1;
    while ($rowvr = mysqli_fetch_assoc($sqlvr)) {
		$empname = mysqli_fetch_assoc(mysqli_query($link1, "Select empname from hrms_employe_master where loginid  ='" . $rowvr['emp_id'] . "'"));
        $managername = mysqli_fetch_assoc(mysqli_query($link1, "Select empname from hrms_employe_master where loginid='" . $rowvr['mgr_id'] . "'"));
		$dsignation = mysqli_fetch_assoc(mysqli_query($link1, "Select designame from hrms_designation_master where designationid='" . $rowvr['designation'] . "'"));
        ?>
        <tr>
            <td align="left"><?=$j;?></td>
            <td><?=$empname['empname']; ?></td>
            <td><?=$rowvr['emp_id']; ?></td>
            <td><?=$managername['empname']; ?></td>
            <td><?=$dsignation['designame']; ?></td>
             <td><?=$rowvr['mobile_no']; ?></td>
             <td><?=$rowvr['email']; ?></td>
             <td><?=$rowvr['status']; ?></td>
             <td><?=$rowvr['remark']; ?></td>            
        </tr>
        <?php
        $j+=1;
    }

    ?>
</table>
<?php } else if($status ==  'LR'){ 
$sqldata_lr = "Select * from hrms_request_loan where update_date BETWEEN '" . $fromdate . "' and '" . $todate . "'   order by sno desc";

$sqlr = mysqli_query($link1, $sqldata_lr);
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>S.No.</strong></td>
        <th>Employee Name</th>       
        <th>Empid</th>
        <th>Manager</th>        
        <th>Designation</th>
        <th>Date of Birth</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Requested Amount</th>
        <th>Approved Amount</th>
        <th>Status</th>
        <th>Remark</th>
    </tr>
    <?php
    $k = 1;
    while ($rowvlr = mysqli_fetch_assoc($sqlr)) {
		$empname = mysqli_fetch_assoc(mysqli_query($link1, "Select empname from hrms_employe_master where loginid  ='" . $rowvlr['emp_id'] . "'"));
        $managername = mysqli_fetch_assoc(mysqli_query($link1, "Select empname from hrms_employe_master where loginid='" . $rowvlr['mgr_id'] . "'"));
		$dsignation = mysqli_fetch_assoc(mysqli_query($link1, "Select designame from hrms_designation_master where designationid='" . $rowvlr['designation'] . "'"));
        ?>
        <tr>
            <td align="left"><?=$k;?></td>
            <td><?=$empname['empname']; ?></td>
            <td><?=$rowvlr['emp_id']; ?></td>
            <td><?=$managername['empname']; ?></td>
            <td><?=$dsignation['designame']; ?></td>
             <td><?=$rowvlr['doj']; ?></td>
             <td><?=$rowvlr['email']; ?></td>
             <td><?=$rowvlr['phone']; ?></td>
             <td><?=$rowvlr['requested_amt']; ?></td>
             <td><?=$rowvlr['approved_amt']; ?></td>
             <td><?=$rowvlr['status']; ?></td>
             <td><?=$rowvlr['remark']; ?></td>            
        </tr>
        <?php
        $k+=1;
    }

    ?>
</table>
<?php } else if($status ==  'TRC'){ 
$sqldata_trc = "Select * from hrms_travelling_details where tv_date BETWEEN '" . $fromdate . "' and '" . $todate . "'   order by id desc";

$sqltrc = mysqli_query($link1, $sqldata_trc);
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>S.No.</strong></td>
        <th>Employee Name</th>       
        <th>Empid</th>
        <th>Manager</th>        
        <th>Claim No.</th>
        <th>From Place</th>
        <th>To Place</th>
        <th>Distance</th>
        <th>Travel Mode</th>
        <th>Purpose</th>
        <th>Amount</th>
        <th>Status</th>
    </tr>
    <?php
    $m = 1;
    while ($rowvtrc = mysqli_fetch_assoc($sqltrc)) {
		$info = mysqli_fetch_assoc(mysqli_query($link1, "Select emp_id, mgr_id from hrms_request_master where request_no ='" . $rowvtrc['claim_id']. "'"));
		$empname = mysqli_fetch_assoc(mysqli_query($link1, "Select empname from hrms_employe_master where loginid  ='" . $info['emp_id'] . "'"));
        $managername = mysqli_fetch_assoc(mysqli_query($link1, "Select empname from hrms_employe_master where loginid='" . $info['mgr_id'] . "'"));
		
        ?>
        <tr>
            <td align="left"><?=$m;?></td>
            <td><?=$empname['empname']; ?></td>
            <td><?=$info['emp_id']; ?></td>
            <td><?=$managername['empname']; ?></td>
            <td><?=$rowvtrc['claim_id']; ?></td>
             <td><?=$rowvtrc['from_place']; ?></td>
             <td><?=$rowvtrc['to_place']; ?></td>
             <td><?=$rowvtrc['distance']; ?></td>
             <td><?=$rowvtrc['tv_mode']; ?></td>
             <td><?=$rowvtrc['purpose']; ?></td>
             <td><?=$rowvtrc['amt']; ?></td>
             <td><?=$rowvtrc['status']; ?></td>            
        </tr>
        <?php
        $m+=1;
    }

    ?>
</table>
<?php } else if($status ==  'LRC'){ 
$sqldata_lrc = "Select * from hrms_lodging_details where log_date BETWEEN '" . $fromdate . "' and '" . $todate . "'   order by id desc";

$sqlrc = mysqli_query($link1, $sqldata_lrc);
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>S.No.</strong></td>
        <th>Employee Name</th>       
        <th>Empid</th>
        <th>Manager</th>        
        <th>Claim No.</th>
        <th>Location</th>
        <th>Hotel Name</th>
        <th>Days</th>
        <th>Amount</th>
        <th>Status</th>
    </tr>
    <?php
    $n = 1;
    while ($rowvlrc = mysqli_fetch_assoc($sqlrc)) {
		$info1 = mysqli_fetch_assoc(mysqli_query($link1, "Select emp_id, mgr_id from hrms_request_master where request_no ='" . $rowvlrc['claim_id'] . "'"));
		$empname = mysqli_fetch_assoc(mysqli_query($link1, "Select empname from hrms_employe_master where loginid  ='" . $info1['emp_id'] . "'"));
        $managername = mysqli_fetch_assoc(mysqli_query($link1, "Select empname from hrms_employe_master where loginid='" . $info1['mgr_id'] . "'"));
		
        ?>
        <tr>
            <td align="left"><?=$n;?></td>
            <td><?=$empname['empname']; ?></td>
            <td><?=$info1['emp_id']; ?></td>
            <td><?=$managername['empname']; ?></td>
            <td><?=$rowvlrc['claim_id']; ?></td>
             <td><?=$rowvlrc['location']; ?></td>
             <td><?=$rowvlrc['hotel_name']; ?></td>
             <td><?=$rowvlrc['days']; ?></td>
           
             <td><?=$rowvlrc['amt']; ?></td>
             <td><?=$rowvlrc['status']; ?></td>            
        </tr>
        <?php
        $n+=1;
    }

    ?>
</table>
<?php } else { 
}