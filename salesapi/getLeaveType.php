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
		//$res_feedbacktype = $get->getFeedbackType();
		//if ($res_feedbacktype != false){
			$a = array();
			$b = array();
			//while($row = mysqli_fetch_array($res_feedbacktype)){
				$b["leavetypeid"] = "1";
				$b["leavetype"] = "Planned Leave";
				array_push($a,$b);
				$b["leavetypeid"] = "2";
				$b["leavetype"] = "Sick Leave";
				array_push($a,$b);
				$b["leavetypeid"] = "3";
				$b["leavetype"] = "Casual Leave";
				array_push($a,$b);
				$b["leavetypeid"] = "4";
				$b["leavetype"] = "Earn Leave";
				array_push($a,$b);
				$b["leavetypeid"] = "5";
				$b["leavetype"] = "Other Leave";
				array_push($a,$b);
			//}
			$c = array("userid" => $user_id, "usercode" => $user_code, "leavetypearray" => $a);
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