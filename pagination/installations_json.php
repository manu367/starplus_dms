<?php
require_once("../config/config.php");
header("Content-Type: application/json; charset=UTF-8");
$method=$_SERVER["REQUEST_METHOD"];
if($method=="GET"){
    $sql = "SELECT ind.* , au.name FROM installation_data ind LEFT JOIN admin_users au ON ind.userid= au.username";
    $result = mysqli_query($link1, $sql);
    $requestData= $_REQUEST;

    if (!$result) {
        echo json_encode([
            "status" => false,
            "message" => "Database query failed",
            "error" => mysqli_error($link1)
        ]);
        exit;
    }

// Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

// Final JSON response
    echo json_encode([
        "status" => true,
        "count"  => count($data),
        "data"   => $data
    ], JSON_PRETTY_PRINT);
}
?>
