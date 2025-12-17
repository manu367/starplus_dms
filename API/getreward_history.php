<?php
include_once 'db_functions.php';     
$db = new DB_Functions();

// DB connection
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn = $private_variable->getValue($db);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

// GET userid
$userid = isset($_REQUEST['userid']) ? trim($_REQUEST['userid']) : '';

if ($userid == "") {
    echo json_encode([
        "status" => "error",
        "message" => "userid is required"
    ]);
    exit;
}

// GET USER WALLET BALANCE
$sql_wallet = mysqli_query($conn, 
    "SELECT reward FROM reward_wallet WHERE userid='".$userid."'"
);

if (mysqli_num_rows($sql_wallet) == 0) {
    echo json_encode([
        "status" => "error",
        "message" => "User not found"
    ]);
    exit;
}

$wallet_data = mysqli_fetch_assoc($sql_wallet);
$wallet_balance = $wallet_data['reward'];

#### Check Slab as per $wallet_balance

$sql_slab=mysqli_fetch_assoc(mysqli_query($conn,"select slab,image from points_slab_master where end <='".$wallet_balance."'  order by id DESC LIMIT 1"));
if($sql_slab['slab']!=''){
	$eng_slab=$sql_slab['slab'];
	$eng_slab_img=$sql_slab['image'];
}
else{
$eng_slab="SILVER";	
$eng_slab_img="silver.png";
}

// GET REWARD TRANSACTION HISTORY
$sql_trn = mysqli_query($conn, 
    "SELECT userid, entry_date, time AS entry_time, type AS trn_type, reward AS point,status 
     FROM reward_reedem_ledger 
     WHERE userid='".$userid."'
     ORDER BY entry_date DESC, time DESC"
);

$transaction_list = [];
while ($row = mysqli_fetch_assoc($sql_trn)) {
    $transaction_list[] = [
        "userid"     => $row['userid'],
        "entry_date" => $row['entry_date'],
        "entry_time" => $row['entry_time'],
        "trn_type"   => $row['trn_type'],
		"status"     => $row['status'],
        "point"      => (int)$row['point'],
		"point_value" => (int)($row['point']*25)
    ];
}

// FINAL RESPONSE
echo json_encode([
    "userid" => $userid,
    "wallet" => $wallet_balance,
	"slab" => $eng_slab,
	"slab_img" => $eng_slab_img,
    "transaction" => $transaction_list
]);
?>
