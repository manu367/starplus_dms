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
$actId = $data->id;
$sysRefNo = $data->sysRefNo;
$remark = $data->remark;
$status = $data->status;
$docencdstr = $data->docencodestr;
$docnm = $data->docname;

$lat = $data->latitude;
$long = $data->longitude;
$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
////////
$act_id = $jwtf->validateParameter('ActivityId',$actId,STRING);
$system_refno = $jwtf->validateParameter('SysRefNo',$sysRefNo,STRING);
$remark = $jwtf->validateParameter('Remark',$remark,STRING);
$status = $jwtf->validateParameter('Status',$status,STRING);
$doc_encodestr = $jwtf->validateParameter('DocEncodeStr',$docencdstr,STRING);
$doc_name = $jwtf->validateParameter('DocName',$docnm,STRING);

$lati = $jwtf->validateParameter('latitude',$lat,STRING);
$longi = $jwtf->validateParameter('longitude',$long,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		////// track user activity
		$resp = $pst->updateUserActivity($user_code,"Activity","Update",$lati,$longi,$user_id,$trackaddrs,$trackdistc);
		if($resp){
			$upd_act = explode("~",$pst->updateActivityStatus($user_id,$user_code,$act_id,$system_refno,$remark,$status,$doc_encodestr,$doc_name,$lati,$longi,$trackaddrs));
			if($upd_act[0] == "1"){
				$a = array("userid" => $user_id, "usercode" => $user_code, "id" => $act_id, "status" => $status, "sysrefno" => $upd_act[1]);
				$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
			}else{
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,$upd_act[1]);
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