<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'post_functions.php';
$pst = new POST_Functions();
////// get JSON data
$data = json_decode(file_get_contents("php://input"));
$uid = $data->userid;
$ucode = $data->usercode;
/////Collection parameter
$tskid = $data->taskid;
$schvisit = $data->scheduledvisit;
$chgvisit = $data->changevisit;
$remak = $data->remark;
$lat = $data->latitude;
$long = $data->longitude;
$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);

$task_id = $jwtf->validateParameter('TaskId',$tskid,STRING);
$scheduled_visit = $jwtf->validateParameter('ScheduledVisit',$schvisit,STRING);
$change_visit = $jwtf->validateParameter('ChangeVisit',$chgvisit,STRING);
$remark = $jwtf->validateParameter('Remark',$remak,STRING);

$lati = $jwtf->validateParameter('latitude',$lat,STRING);
$longi = $jwtf->validateParameter('longitude',$long,STRING);

try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		////// track user activity
		$resp = $pst->updateUserActivity($user_code,"Deviation","Request",$lati,$longi,$user_id,$trackaddrs,$trackdistc);
		if($resp){
			$upd_deviation = explode("~",$pst->updateDeviation($user_id,$user_code,$task_id,$scheduled_visit,$change_visit,$remark,$lati,$longi,$trackaddrs));
			if($upd_deviation[0] == "1"){
				$a = array("userid" => $user_id, "usercode" => $user_code, "taskid" => $transaction_no, "msg" => "Deviation request is successfully raised.", "status" => "Deviation is pending for approval");
				$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
			}else{
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,$upd_deviation[1]);
			}
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