<?php 
include('constant.php');
require "vendor/autoload.php";
use \Firebase\JWT\JWT;
include('header.php');
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';
$db = new DB_Functions();
//// validate parameter
$userid = $db->validateParameter('uid',$_REQUEST["uid"],STRING);
try{
	////// get JWT token
	$token = $db->getBearerToken();
	///// validate token
	try{
		$decoded = JWT::decode($token, SECURTY_KEY, array('HS256'));
		$getuid = $decoded->data->uid;
		if($getuid != $userid){				
			$db->throwError(USER_NOT_FOUND,'Invalid Token');
		}else{
			$a = array();
			$b = array();
			$res_state = $db->getStateMaster();
			if ($res_state != false){
				while ($row = mysqli_fetch_array($res_state)) 
				{                     
					$b["sno"] = $row["sno"];
					$b["zone"] = $row["zone"];
					$b["state"] = $row["state"];
					$b["gststatecode"] = $row["statecode"];
					$b["code"] = $row["code"];
					$b["country"] = $row["country"];
					array_push($a,$b);         
				}         
				$db->returnResponse(SUCCESS_RESPONSE,$pager,$a);    
			}
		}
	}catch(Exception $e){
		//echo json_encode(array("message" => ACESS_TOKEN_ERROR, "status" => 0, "userid" => "admin"));
		$db->throwError(ACCESS_TOKEN_ERROR,$e->getMessage());	
	}
}catch(Exception $e){
	$this->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>