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
$leadId = $data->leadId;
$sysRefNo = $data->sysRefNo;
$internalNote = $data->internalNote;
$clientNote= $data->clientNote;
$remark = $data->remark;
$status = $data->status;
$lat = $data->latitude;
$long = $data->longitude;
$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
////////
$lead_id = $jwtf->validateParameter('LeadId',$leadId,STRING);
$system_refno = $jwtf->validateParameter('SysRefNo',$sysRefNo,STRING);
$internal_note = $jwtf->validateParameter('InternalNote',$internalNote,STRING);
$client_note = $jwtf->validateParameter('ClientNote',$clientNote,STRING);
$remark = $jwtf->validateParameter('Remark',$remark,STRING);
$status = $jwtf->validateParameter('Status',$status,STRING);

$lati = $jwtf->validateParameter('latitude',$lat,STRING);
$longi = $jwtf->validateParameter('longitude',$long,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		////// track user activity
		$resp = $pst->updateUserActivity($user_code,"Lead","Update",$lati,$longi,$user_id,$trackaddrs,$trackdistc);
		if($resp){
			$upd_lead = explode("~",$pst->updateLeadStatus($user_id,$user_code,$lead_id,$system_refno,$internal_note,$client_note,$remark,$status,$lati,$longi));
			if($upd_lead[0] == "1"){
				$a = array("userid" => $user_id, "usercode" => $user_code, "leadid" => $lead_id, "status" => $status, "sysrefno" => $upd_lead[1]);
				$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
			}else{
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,$upd_lead[1]);
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