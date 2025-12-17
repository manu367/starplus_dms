<?php
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
////// get JSON data
$data = json_decode(file_get_contents("php://input"));
$uid = $data->userid;
$pwd = $data->pwd;
//// validate parameter
$userid = $jwtf->validateParameter('Userid',$uid,STRING);
$passw = $jwtf->validateParameter('Password',$pwd,STRING);
try{
	echo $jwtf->generateJWT($uid,$passw);
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>