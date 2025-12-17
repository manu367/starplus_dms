<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';
$db = new DB_Functions();
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$_REQUEST["userid"],STRING);
$user_code = $jwtf->validateParameter('UserCode',$_REQUEST["usercode"],STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$a = array();
		$b = array();
		$arr_banner = array("slider1.png","slider2.png","slider3.png","slider4.png");
		if (!empty($arr_banner)){
			/////
			for($k=0;$k<count($arr_banner);$k++){
				$b["bannerImg"] = $arr_banner[$k];
				$b["bannerUrl"] = ATTACHMENT_URL."bannerimg/".$arr_banner[$k];
				array_push($a,$b);
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "bannerarray" => $a);
			$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$c);    
		}else{
			$jwtf->returnResponse(FAILED_RESPONSE,$pager,"No Banner found");
		}
	}else{
		$decode_resp;
	}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>