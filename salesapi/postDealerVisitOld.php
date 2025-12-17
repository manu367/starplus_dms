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
$tskid = $data->taskid;
$dcode = $data->dealercode;
$visitaddrs = $data->visitaddress;
$visitrmk = $data->visitremark;
$lat = $data->latitude;
$long = $data->longitude;
$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$task_id = $jwtf->validateParameter('taskId',$tskid,STRING);
$dealer_code = $jwtf->validateParameter('DealerCode',$dcode,STRING);
$visit_address = $jwtf->validateParameter('VisitAddress',$visitaddrs,STRING);
$visit_remark = $jwtf->validateParameter('VisitRemark',$visitrmk,STRING);
$lati = $jwtf->validateParameter('latitude',$lat,STRING);
$longi = $jwtf->validateParameter('longitude',$long,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		if($dealer_code){
			////// track user activity
			$resp = $pst->updateUserActivity($user_code,"Dealer Visit Old","Update",$lati,$longi,$user_id,$trackaddrs,$trackdistc);
			if($resp){
				$upd_dealervisit = explode("~",$pst->updateDealerVisitOld($user_id,$user_code,$task_id,$dealer_code,$visit_address,$visit_remark,$lati,$longi,$trackaddrs));
				if($upd_dealervisit[0] == "1"){
					$a = array("userid" => $user_id, "usercode" => $user_code, "taskid" => $task_id, "dealercode" => $dealer_code);
					$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
				}else{
					$jwtf->returnResponse(FAILED_RESPONSE,$pager,$upd_dealervisit[1]);
				}
			}else{
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Something went wrong");
			}
		}else{
			$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Please select dealer first");
		}
	}else{
		$decode_resp;
	}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>