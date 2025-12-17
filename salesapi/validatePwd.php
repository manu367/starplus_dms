<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
include_once 'db_functions.php'; 
$db = new DB_Functions(); 
///// get userid
$json = $_POST["passValidateJSON"];
if (get_magic_quotes_gpc()){ 
	$json = stripslashes($json); 
}
//Decode JSON into an Array 
$data = json_decode($json);
$a = array();
$final_array = array();
//validate password entered by user if ok then give error msg
$uid2 = $data->userId;
$resp = $db-> passValidation($data->userId, $data->deviceId, $data->imei, $data->pwd);
if($resp == "success"){
	$res = $db->checkUesr($uid2);
	$row = mysqli_fetch_array($res);
	if($row["status"]=="active"){
	///// get JWT token
	$jwtresp = json_decode($jwtf->generateJWT($uid2));
	$jwt = $jwtresp->jwt;
	$expire_claim = $jwtresp->expireAt;

	
	$final_array["message"] = "Successfully login.";
	$final_array["status"] = 1;
	$final_array["userId"] = $row["phone"];
	$final_array["userCode"] = $row["username"];
	$final_array["locationCode"] = $row["owner_code"];
	$final_array["userName"] = $row["name"]; 
	$final_array["userType"] = $row["utype"];
	$final_array["userContact"] = $row["phone"];
	$final_array["userEmail"] = $row["emailid"];
	$final_array["userExpDayLimit"] = 5;
	$final_array["jwt"] = $jwt;
	$final_array["expireAt"] = $expire_claim;

	////// App logout flag is not set to be 0
	$upd_logoutflag = $db->updateLogoutFlag($row["phone"],$row["username"]);

	echo json_encode($final_array);
	}else{
		$final_array["message"] = "Userid is deactivated.";
		$final_array["status"] = 0;
		echo json_encode($final_array);
	}
}else{
	$final_array["message"] = $resp;
	$final_array["status"] = 0;
	echo json_encode($final_array);
}
?>