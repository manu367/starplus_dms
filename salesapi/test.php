<?php
include_once './config/dbconnect.php';
$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();
$res = mysqli_query($conn,"SELECT * FROM admin_users");
$row = mysqli_fetch_array($res);
print_r($row);