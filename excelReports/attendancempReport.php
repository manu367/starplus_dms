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

$user_id = base64_decode($_REQUEST['user_id']);
$fromdate = base64_decode($_REQUEST['fromDate']);
$todate = base64_decode($_REQUEST['toDate']);

//////End filters value/////

$sqldata = "Select * from okwu_attendence where action_type = 'WEB' ";
if ($user_id != '') {
    $sqldata.=" and user_id = '" . $user_id . "'";
}
if ($fromdate != '' or $todate != '') {
    $sqldata.=" and insert_date BETWEEN '" . $fromdate . "' and '" . $todate . "'";
}
$sqldata.=" order by id desc";

$sql = mysqli_query($link1, $sqldata);
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>S.No.</strong></td>
        <th>Employee Name</th>       
        <th>Date</th>
        <th>IN Time</th>        
        <th>OUT Time</th>
       
    </tr>
    <?php
    $i = 1;
    while ($row = mysqli_fetch_assoc($sql)) {
        $username = mysqli_fetch_assoc(mysqli_query($link1, "Select name from admin_users where username='" . $row['user_id'] . "'"));
        ?>
        <tr>
            <td align="left"><?=$i;?></td>
            <td><?=$username['name']; ?></td>
            <td><?=dt_format1($row['insert_date']); ?></td>
            <td><?=time_format($row['in_datetime']); ?></td>            
            <td><?=time_format($row['out_datetime']); ?></td>

        </tr>
        <?php
        $i+=1;
    }
    ?>
</table>
