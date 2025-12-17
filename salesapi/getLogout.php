<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
include_once 'db_functions.php'; 
$db = new DB_Functions(); 
///// get userid
/*$json = $_POST["passValidateJSON"];
if (get_magic_quotes_gpc()){ 
	$json = stripslashes($json); 
}
//Decode JSON into an Array 
$data = json_decode($json);
*/
$a = array();
$final_array = array();
//validate password entered by user if ok then give error msg
$uid2 = $_REQUEST['uid'];
//$resp = $db-> passValidation($data->userId, $data->deviceId, $data->imei, $data->pwd);
//if($resp == "success"){
	$res = $db->checkUesr($uid2);
	$row = mysqli_fetch_array($res);
	
	$final_array["status"] = $row['app_logout'];
	

	echo json_encode($final_array);

?>