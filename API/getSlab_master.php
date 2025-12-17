<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
header('Content-Type: application/json');
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn = $private_variable->getValue($db);

$response = array();
$slabs = array();

// Fetch only Active (A) slabs
$query = "SELECT slab, start, end, multipler, image FROM points_slab_master WHERE status = 'A' ORDER BY start ASC";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Use slab name (e.g., SILVER, GOLD) as key
        $key = strtoupper(trim($row['slab']));

        $slabs[$key] = array(
            "Start" => (float)$row['start'],
            "End" => (float)$row['end'],
            "Multiplier" => (float)$row['multipler'],
            "label" => ucfirst(strtolower($row['slab'])),
            "image" => $row['image']
        );
    }

    $response['SLABS'] = $slabs;
    $response['status'] = 1;
    $response['msg'] = 'Success';
} else {
    $response['status'] = 0;
    $response['msg'] = 'No active slabs found';
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
