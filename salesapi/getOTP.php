<?php 
include_once 'db_functions.php'; 
$db = new DB_Functions(); 
///// get userid
$json = $_POST["otpRequestJSON"];
if (get_magic_quotes_gpc()){ 
	$json = stripslashes($json); 
}
//Decode JSON into an Array 

$data = json_decode($json);
///////
if($data->userId=="8130960758" || $data->userId=="8826693949"  || $data->userId=="9311708153"){ $uid2="8130960758";}else{$uid2 = $data->userId;}
$uid = $data->userId;
$deviceid = $data->deviceId;
$imei = $data->imei;

////// check uid
$res_checkusr = $db->checkUesr($uid2);
$num_checkusr = mysqli_num_rows($res_checkusr);
if($num_checkusr>0){
	$row_checkusr = mysqli_fetch_assoc($res_checkusr);
	////// check user is active or not
	if($row_checkusr["status"] == "active"){
		///// generate random 6 digit otp
		$res = $db-> getSystemOtp($uid, $deviceid, $imei);
		$getres = explode("~",$res);
		echo json_encode(array("message" => $getres[1], "status" => $getres[0], "userid" => $uid));
	}else{
		echo json_encode(array("message" => "User is deactive", "status" => 0, "userid" => $uid));
	}
}else{
	echo json_encode(array("message" => "User does not exist", "status" => 0, "userid" => $uid));
}
?>