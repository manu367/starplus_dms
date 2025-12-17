<?php
print("\n");
print("\n");
////// filters value/////
$from_date = base64_decode($_REQUEST['fdate']);
$to_date = base64_decode($_REQUEST['tdate']);
$location = base64_decode($_REQUEST['location_code']);
//////fetch data/////
$sql = "SELECT * FROM reward_points_ledger WHERE location_code='".$location."' AND transaction_date>='".$from_date."' AND transaction_date<='".$to_date."' ORDER BY id ASC";
$res = mysqli_query($link1, $sql);
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>S.No.</strong></td>
        <th>Transaction Date</th>
        <th>Transaction No.</th>
        <th>Product</th>
        <th>Type</th>
        <th>DR</th>
        <th>CR</th>
        <th>Update On</th>
    </tr>
    <?php
    $i = 1;
    while ($row = mysqli_fetch_assoc($res)) {
		$proddet=explode("~",getProductDetails($row['partcode'],"productname,model_name,productcode",$link1));
        ?>
        <tr>
            <td align="left"><?=$i;?></td>
            <td align="center"><?=$row['transaction_date']?></td>
            <td><?=$row['transaction_no'];?></td>
            <td><?=$proddet[0]." , ".$proddet[1]." (".$proddet[2].")";?></td>
            <td><?=$row['reward_type']?></td>
            <td style="text-align:right"><?=$row['dr_reward']?></td>
            <td style="text-align:right"><?=$row['cr_reward']?></td>
            <td align="center"><?=$row['update_on']; ?></td>
        </tr>
        <?php
        $i+=1;
    }
    ?>
</table>