<?php
print("\n");
print("\n");
////// filters value/////
$user_id = base64_decode($_REQUEST['user_id']);
$fromdate = base64_decode($_REQUEST['fromDate']);
$todate = base64_decode($_REQUEST['toDate']);
//////End filters value/////
$res_usr = mysqli_query($link1, "SELECT name,oth_empid,designationid FROM admin_users WHERE username='".$user_id."'");
$row_usr = mysqli_fetch_assoc($res_usr);
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
      <td height="25" colspan="15" align="center">Travel Expense Claim (TEC) Form <?=$fromdate?> To <?=$todate?></td>
    </tr>
    <tr align="left" style="font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
      <td height="25" colspan="15" align="center">DOMESTIC / OVERSEAS TOUR-CUM-LOCAL CONVEYANCE EXPENSE STATEMENT</td>
    </tr>
    <tr align="left" style="font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
      <td height="25" colspan="5" align="left"><strong>Name: </strong><?=$row_usr["name"]?></td>
      <td height="25" colspan="4" align="left"><strong>Designation: </strong><?=$row_usr["designationid"]?></td>
      <td height="25" colspan="3" align="left"><strong>Emp Code: </strong><?=$row_usr["oth_empid"]?></td>
      <td height="25" colspan="3" align="left"><strong>Brand: </strong></td>
    </tr>
    <tr align="left" style="font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
      <td height="25" colspan="15" align="center">Details Of Expenses</td>
    </tr>
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>Date</strong></td>
        <th>From</th>
        <th>Time</th>
        <th>To</th>
        <th>Time</th>
        <th>Purpose Of Travelling</th>
        <th>No. Of Visit</th>
        <th>Visit Result</th>
        <th>Mode Of Travel</th>
        <th>Food Expense</th>
        <th>Logistic Expense</th>
        <th>Travel Expense</th>
        <th>Non-Travel Expense</th>
        <th>Other Expense</th>
        <th>Hotel Expense</th>
        <th>Total Expense</th>
        <th>Approved Amount</th>
        <th>Status</th>
    </tr>
    <?php
    $i = 1;
	$sqldata="SELECT * FROM ta_da WHERE expense_date BETWEEN '".$fromdate."' AND '".$todate."' AND userid='".$user_id."'";
	$sql = mysqli_query($link1, $sqldata);
    while ($row = mysqli_fetch_assoc($sql)){
	//// get address
	$address = mysqli_fetch_assoc(mysqli_query($link1,"SELECT address FROM user_track WHERE latitude='".$row["latitude"]."' AND longitude='".$row["longitude"]."'"));
    ?>
        <tr>
            <td align="left"><?=$row["entry_date"];?></td>
            <td><?=$address["address"];?></td>
            <td><?=$row['entry_time'];?></td>
            <td><?=$address["address"];?></td>
            <td><?=$row['entry_time'];?></td>
            <td><?=$row['remark'];?></td>
            <td>1</td>
            <td>&nbsp;</td> 
            <td><?=$row['travel_mode'];?></td>
            <td><?=$row['food_exp'];?></td>
            <td><?=$row['courier_exp'];?></td>
            <td><?=$row['localconv_exp'];?></td>
            <td><?=$row['mobile_exp'];?></td>
            <td><?=$row['other_exp'];?></td>
            <td><?=$row['hotel_exp'];?></td>
            <td><?=($row['food_exp']+$row['courier_exp']+$row['localconv_exp']+$row['mobile_exp']+$row['other_exp']+$row['hotel_exp']);?></td>
            <td><?=$row['approved_amt']?></td>
            <td><?=$row['status']?></td>
        </tr>
        <?php
        $i+=1;
    }
    ?>
</table>
