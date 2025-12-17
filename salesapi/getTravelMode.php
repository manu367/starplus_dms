<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions.php';
$get = new GET_Functions();
//////////////
$reflection_class = new ReflectionClass($get);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($get);
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
			///// get user band
			$usrband = $get->getAnyDetails($user_code,"band","username","admin_users");
			/////updated by shekhar
			$food_limit = "0";
			$foodclassid = "5";
			
			$travel_limit = "0";
			$travelclassid = "5";
			
			$hotel_limit = "0";
			$hotelclassid = "5";
			
			if($usrband=="1" || $usrband=="2"){
				$food_limit = "0";
				$travel_limit = "0";
				$hotel_limit = "0";
			}else{
				$row_foodlimit = mysqli_fetch_assoc(mysqli_query($conn,"SELECT exp_limit FROM expense_limit_master WHERE band='".$usrband."' AND exp_type='FOOD' AND class_type IN ('".$foodclassid."')"));
				$row_travellimit = mysqli_fetch_assoc(mysqli_query($conn,"SELECT exp_limit FROM expense_limit_master WHERE band='".$usrband."' AND exp_type='TRAVEL' AND class_type IN ('".$travelclassid."')"));
				$row_hotellimit = mysqli_fetch_assoc(mysqli_query($conn,"SELECT exp_limit FROM expense_limit_master WHERE band='".$usrband."' AND exp_type='HOTEL' AND class_type IN ('".$hotelclassid."')"));
				$food_limit = $row_foodlimit["exp_limit"];
				$travel_limit = $row_travellimit["exp_limit"];
				$hotel_limit = $row_hotellimit["exp_limit"];
			}
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
			$usrlimit_a["food"] = $food_limit;
			$usrlimit_a["foodmsg"]="Your are entring more than allowed amount ".$food_limit.", please re-check Food";
			//array_push($usrlimit,$usrlimit_a);
			$usrlimit_a["logistic"]="0";
			$usrlimit_a["logisticmsg"]="Your are entring more than allowed amount 0, please re-check"."Logistic";
			//array_push($usrlimit,$usrlimit_a);
			$usrlimit_a["travel"]=$travel_limit;
			$usrlimit_a["travelmsg"]="Your are entring more than allowed amount ".$travel_limit.", please re-check"."travel";
			//array_push($usrlimit,$usrlimit_a);
			$usrlimit_a["nontravel"]="0";
			$usrlimit_a["nontravelmsg"]="Your are entring more than allowed amount 0, please re-check"."nontravel";
			//array_push($usrlimit,$usrlimit_a);
			//	$usrlimit_a["logistic"]="1000";
			//	array_push($usrlimit,$usrlimit_a);
			$usrlimit_a["oth"]="0";
			$usrlimit_a["othmsg"]="Your are entring more then allowed amount 0, please re-check"."oth";
			//array_push($usrlimit,$usrlimit_a);
			$usrlimit_a["hotel"]=$hotel_limit;
			$usrlimit_a["hotelmsg"]="Your are entring more then allowed amount ".$hotel_limit.", please re-check"."hotel";
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