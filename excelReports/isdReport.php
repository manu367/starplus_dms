<?php
print("\n");
print("\n");

////// filters value/////
//// extract all encoded variables
function dt_format1($dt_sel) {

    return substr($dt_sel, 8, 2) . "-" . substr($dt_sel, 5, 2) . "-" . substr($dt_sel, 0, 4);
}

$fromdate = base64_decode($_REQUEST['fromDate']);
$todate = base64_decode($_REQUEST['toDate']);

//////End filters value/////

$sqldata = "Select * from sale_data where 1=1";
if ($fromdate != '' or $todate != '') {
    $sqldata.=" and sync_date BETWEEN '" . $fromdate . "' and '" . $todate . "'";
}
$sqldata.=" order by id desc";

$sql = mysqli_query($link1, $sqldata);
?>
<style>
    .text{
        mso-number-format:"\@";/*force text*/
    }
</style>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>S.No.</strong></td>
        <th>ISP ID</th>
        <th>ISP Name</th>
        <th><?=$imeitag?></th>        
        <th>Customer Name</th>
        <th>Location</th>
        <th>Contact No.</th>
        <th>State</th>
        <th>City</th>                                    
        <th>Entry Date</th>
    </tr>
    <?php
    $i = 1;
    while ($row = mysqli_fetch_assoc($sql)) {
        $sql1 = mysqli_query($link1, "select owner_code from billing_imei_data where imei1= '" . $row['imei'] . "' group by imei1 order by id desc")or die(mysql_error());
        $location = mysqli_fetch_assoc($sql1);
        $sql2 = mysqli_query($link1, "select name from asc_master where asc_code = '" . $location['owner_code'] . "'")or die(mysql_error());
        $lname = mysqli_fetch_assoc($sql2);
        $sql3 = mysqli_query($link1, "select name from admin_users where username = '" . $row['user_name'] . "'")or die(mysql_error());
        $name = mysqli_fetch_assoc($sql3);
        ?>
        <tr>
            <td align="left"><?= $i ?></td>
            <td><?= $row['user_name'] ?></td>
            <td><?= $name['name'] ?></td>
            <td class="text"><?= $row['imei']; ?></td>
            <td><?= $row['cust_name']; ?></td> 
            <td><?=$lname['name']?></td>
            <td width='15%'><?= $row['contact_no']; ?></td>
            <td><?= $row['state']; ?></td>
            <td><?= $row['city']; ?></td>
            <td><?php echo date_format(date_create($row['sync_date']), "d-m-Y"); ?></td>
        </tr>
    </tr>
    <?php
    $i+=1;
}
?>
</table>
