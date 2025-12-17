<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions.php';
$get = new GET_Functions();
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$_REQUEST["userid"],STRING);
$user_code = $jwtf->validateParameter('UserCode',$_REQUEST["usercode"],STRING);
//$visit_city = $jwtf->validateParameter('VisitCity',$_REQUEST["vcity"],STRING);
$visit_city = rtrim($_REQUEST["vcity"]," ");
$visit_city = ltrim($visit_city," ");
$pjp_id = $_REQUEST["taskkey"];
try{
	////// get JWT token
	//$token = $jwtf->getBearerToken();
	///// validate token
	//$decode_resp = $jwtf->decodeJWT($token,$user_id);
	//if($decode_resp == "SUCCESS_RESPONSE"){
	if(1==1){
	//	$res_dealerlist = $get->getDealerList($user_code,$visit_city);
		
			$a = array();
			$b = array();
		   $b["activityname"] = "Select Type";
			$b["activitycode"] = "0";
		
			array_push($a,$b);
			$b["activityname"] = "BTL Activity";
			$b["activitycode"] = "1";
		
			array_push($a,$b);
			$b["activityname"] = "Meeting";
			$b["activitycode"] = "2";
				array_push($a,$b);
			$c = array("userid" => $user_id, "usercode" => $user_code,  "activitylist" => $a);
			$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$c);
					
	}else{
		$decode_resp;
	}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>