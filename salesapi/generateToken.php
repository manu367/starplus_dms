<?php
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
////// get JSON data
$data = json_decode(file_get_contents("php://input"));
$uid = $data->userid;
//// validate parameter
$userid = $jwtf->validateParameter('uid',$uid,STRING);
try{
	echo $jwtf->generateJWT($uid);
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>