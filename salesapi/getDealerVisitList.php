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
		$res_visitlist = $get->getDealerVisitList($user_code,$from_date,$to_date,$task_id);
		if ($res_visitlist != false){
			$a = array();
			$b = array();
			while($row = mysqli_fetch_array($res_visitlist)){
				$res_partydet = $get->getLocationName($row["party_code"]);
				$row_partydet = mysqli_fetch_assoc($res_partydet);
				$b["refId"] = $row["id"];
				$b["taskId"] = $row["pjp_id"];
				$b["partyCode"] = $row["party_code"];
				$b["partyName"] = $row_partydet["name"].",".$row_partydet["city"].",".$row_partydet["state"];
				$b["visitCity"] = $row["visit_city"];
				$b["address"] = $row["address"]; 
				$b["remark"] = $row["remark"];
				$b["updatedate"] = $row["update_time"];
				array_push($a,$b);
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "visitlist" => $a);
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