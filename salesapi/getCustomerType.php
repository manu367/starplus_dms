<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions.php';
$get = new GET_Functions();
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$_REQUEST["userid"],STRING);
$user_code = $jwtf->validateParameter('UserCode',$_REQUEST["usercode"],STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		//$res_paymode = $get->getPaymentMode();
		//if ($res_paymode != false){
			$a = array();
			$b = array();
			//while($row = mysqli_fetch_array($res_paymode)){
			$b["typeId"] = "1";
			$b["typeName"] = "Corporate";
			array_push($a,$b);
			$b["typeId"] = "2";
			$b["typeName"] = "Retail";
			array_push($a,$b);
			//}
			$c = array("userid" => $user_id, "usercode" => $user_code, "customertypearray" => $a);
			$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$c);
		//}else{
			//$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Something went wrong");
		//}			
	}else{
		$decode_resp;
	}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>