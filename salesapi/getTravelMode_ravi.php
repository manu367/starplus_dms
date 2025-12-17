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
			$b["modeId"] = "1";
			$b["travelMode"] = "By Train";
			array_push($a,$b);
			$b["modeId"] = "2";
			$b["travelMode"] = "By Air";
			array_push($a,$b);
			$b["modeId"] = "3";
			$b["travelMode"] = "By Bus";
			array_push($a,$b);
			$b["modeId"] = "4";
			$b["travelMode"] = "By Other";
			array_push($a,$b);
			//}
		//	$c = array("userid" => $user_id, "usercode" => $user_code, "travelmodearray" => $a);
		
		
		//updated by ravi- these  values need to make  thru logic or table and give with respose
			$usrlimit = array();
			$usrlimit_a = array();
			$usrlimit_a["food"]="1000";
			$usrlimit_a["foodmsg"]="Your are entring more than allowed amount 1000, please re-check Food";
			//array_push($usrlimit,$usrlimit_a);
			$usrlimit_a["logistic"]="1100";
			$usrlimit_a["logisticmsg"]="Your are entring more than allowed amount 1100, please re-check"."Logistic";
			//array_push($usrlimit,$usrlimit_a);
			$usrlimit_a["travel"]="1200";
			$usrlimit_a["travelmsg"]="Your are entring more than allowed amount 1200, please re-check"."travel";
			//array_push($usrlimit,$usrlimit_a);
			$usrlimit_a["nontravel"]="1300";
			$usrlimit_a["nontravelmsg"]="Your are entring more than allowed amount 1300, please re-check"."nontravel";
			//array_push($usrlimit,$usrlimit_a);
			//	$usrlimit_a["logistic"]="1000";
			//	array_push($usrlimit,$usrlimit_a);
			$usrlimit_a["oth"]="1400";
			$usrlimit_a["othmsg"]="Your are entring more then allowed amount 1400, please re-check"."oth";
			//array_push($usrlimit,$usrlimit_a);
			$usrlimit_a["hotel"]="1500";
			$usrlimit_a["hotelmsg"]="Your are entring more then allowed amount 1500, please re-check"."hotel";
			array_push($usrlimit,$usrlimit_a);
			
			//}
			$c = array("userid" => $user_id, "usercode" => $user_code, "travelmodearray" => $a,"userlimit" => $usrlimit);
		
		
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