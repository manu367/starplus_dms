<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions.php';
$get = new GET_Functions();
////// get JSON data
$data = json_decode(file_get_contents("php://input"));
$uid = $data->userid;
$ucode = $data->usercode;
$fdate = $data->fromdate;
$tdate = $data->todate;
$tskid = $data->taskid;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$from_date = $jwtf->validateParameter('FromDate',$fdate,STRING);
$to_date = $jwtf->validateParameter('ToDate',$tdate,STRING);
$task_id = $jwtf->validateParameter('taskId',$tskid,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_fblist = $get->getFeedbackList($user_code,$from_date,$to_date,$task_id);
		if ($res_fblist != false){
			$a = array();
			$b = array();
			while($row = mysqli_fetch_array($res_fblist)){
				$expl_fb = explode("~",$row["request"]);
				$b["refId"] = $row["id"];
				$b["taskId"] = $row["pjp_id"];
				$b["fbFor"] = $row["module"];
				$b["fbTitle"] = $expl_fb[0];
				$b["fbMsg"] = $expl_fb[1];
				$b["fbRate"] = $expl_fb[2];
				$b["fbType"] = $row["problem"]; 
				$b["sysRefNo"] = $row["query"];
				$b["entrydate"] = $row["entry_date"]." ".$row["entry_time"];
				array_push($a,$b);
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "feedbacklist" => $a);
			$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$c);
		}else{
			$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Something went wrong");
		}			
	}else{
		$decode_resp;
	}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>