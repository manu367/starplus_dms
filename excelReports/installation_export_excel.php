<?php
require_once("../config/config.php");

// 🔒 Excel headers
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=installation_report_".date("Y-m-d").".xls");
header("Pragma: no-cache");
header("Expires: 0");

$where = " WHERE 1=1 ";

// 🔹 From Date
if (!empty($_REQUEST['fdate'])) {
    $fromdate = $_REQUEST['fdate'];
    $where .= " AND ind.installation_date >= '$fromdate'";
}

// 🔹 To Date
if (!empty($_REQUEST['tdate'])) {
    $todate = $_REQUEST['tdate'];
    $where .= " AND ind.installation_date <= '$todate'";
}

// 🔹 User
if (!empty($_REQUEST['user_id'])) {
    $userid = $_REQUEST['user_id'];
    $where .= " AND ind.userid = '$userid'";
}

// 🔹 Status
if (!empty($_REQUEST['status'])) {
    $status = $_REQUEST['status'];
    $where .= " AND ind.status = '$status'";
}
//var_dump($_REQUEST['status']);exit();
$sql = "
SELECT ind.*, au.*, ind.status as ind_status
FROM installation_data ind
LEFT JOIN admin_users au ON ind.userid = au.username
$where
ORDER BY ind.installation_date DESC
";

$result = mysqli_query($link1, $sql);
?>

<table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
    <tr>
        <th>Sr No</th>
        <th>Technican ID</th>
        <th>Technican Name</th>
        <th>Status</th>
        <th>Installation Date</th>
        <th>Product</th>
        <th>Serial No</th>
        <th>Customer Name</th>
        <th>Cus. Mobile Number</th>
        <th>Cus. Email</th>
        <th>Cus. Address</th>
        <th>Cus. City</th>
        <th>Cus. State</th>
    </tr>

    <?php
    $sno = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $sno++;
        ?>
        <tr>
            <td><?= $sno ?></td>
            <td><?= $row['userid'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['ind_status'] ?></td>
            <td><?= $row['installation_date'] ?></td>
            <td><?= $row['product_code'] ?></td>
            <td><?= $row['serial_no'] ?></td>
            <td><?= $row['customer_Name'] ?></td>
            <td><?= $row['mobile_no'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['address'] ?></td>
            <td><?= $row['city'] ?></td>
            <td><?= $row['state'] ?></td>
        </tr>
    <?php } ?>
</table>
