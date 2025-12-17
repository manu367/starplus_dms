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
$lvtype = $data->leavetype;
$lvfrom = $data->leavefromdate;
$lvto = $data->leavetodate;
$lvreason = $data->leavereason;
$lat = $data->latitude;
$long = $data->longitude;
$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
////////
$leave_type = $jwtf->validateParameter('LeaveType',$lvtype,STRING);
$leave_fromdate = $jwtf->validateParameter('LeaveFromDate',$lvfrom,STRING);
$leave_todate = $jwtf->validateParameter('LeaveToDate',$lvto,STRING);
$leave_reason = $jwtf->validateParameter('LeaveReason',$lvreason,STRING);

$lati = $jwtf->validateParameter('latitude',$lat,STRING);
$longi = $jwtf->validateParameter('longitude',$long,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		////// track user activity
		$resp = $pst->updateUserActivity($user_code,"Leave","Request",$lati,$longi,$user_id,$trackaddrs,$trackdistc);
		if($resp){
			$upd_leave = explode("~",$pst->leaveRequest($user_id,$user_code,$leave_type,$leave_fromdate,$leave_todate,$leave_reason,$leave_desc,$lati,$longi,$trackaddrs));
			if($upd_leave[0] == "1"){
				$a = array("userid" => $user_id, "usercode" => $user_code, "leavetype" => $leave_type, "leavefromdate" => $leave_fromdate,"leavetodate" => $leave_todate, "sysrefno" => $upd_leave[1]);
				$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
			}else{
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,$upd_leave[1]);
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