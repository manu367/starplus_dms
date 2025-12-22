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

$sql = "
SELECT 
    ind.*,
    au.name AS tech_name,
    au.phone AS tech_mobile,
    au.username AS tech_id,
    ind.mobile_no AS cust_mobile,
    ind.email AS cust_email,
    ind.status AS ind_status
FROM installation_data ind
LEFT JOIN admin_users au ON ind.userid = au.username
$where
ORDER BY ind.installation_date DESC
";

$result = mysqli_query($link1, $sql);
?>

<table width="100%" border="1">
    <tr>
        <th>Sr No</th>

        <th>Technician ID</th>
        <th>Technician Name</th>
        <th>Technician Mobile</th>

        <th>Customer Name</th>
        <th>Customer Mobile</th>
        <th>Customer Email</th>
        <th>Customer Address</th>
        <th>Customer City</th>
        <th>Customer State</th>

        <th>Approved By</th>
        <th>Approved Date</th>
        <th>Approved Time</th>

        <th>Installation Date</th>
        <th>Product</th>
        <th>Serial No</th>
        <th>Document Number</th>
        <th>Invoice</th>
        <th>Status</th>
    </tr>

    <?php
    $sno = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $sno++;
        ?>
        <tr>
            <td><?= $sno ?></td>

            <td><?= $row['tech_id'] ?></td>
            <td><?= $row['tech_name'] ?></td>
            <td><?= $row['tech_mobile'] ?></td>

            <td><?= $row['customer_Name'] ?></td>
            <td><?= $row['cust_mobile'] ?></td>
            <td><?= $row['cust_email'] ?></td>
            <td><?= $row['address'] ?></td>
            <td><?= $row['city'] ?></td>
            <td><?= $row['state'] ?></td>

            <td><?= $row['approve_by'] ?? '-' ?></td>
            <td><?= $row['approved_date'] ?? '-' ?></td>
            <td><?= $row['approved_time'] ?? '-' ?></td>

            <td><?= $row['installation_date'] ?></td>
            <td><?= $row['product_code'] ?></td>
            <td><?= $row['serial_no'] ?></td>
            <td><?= $row['document_no'] ?? '-' ?></td>
            <td><?= $row['invoice_no'] ?? '-' ?></td>
            <td><?= $row['ind_status'] ?></td>
        </tr>
    <?php } ?>
</table>
