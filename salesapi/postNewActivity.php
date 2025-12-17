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
$ptyname = $data->partyname;
$ptycontact = $data->contactno;
$rmk = $data->remark;
$acttype = $data->activitytype;
$docencdstr = $data->docencodestr;
$docnm = $data->docname;
$lat = $data->latitude;
$long = $data->longitude;
$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;
$activityAction = $data->activityaction;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
////////
$party_name = $jwtf->validateParameter('PartyName',$ptyname,STRING);
$party_contact = $jwtf->validateParameter('PartyContact',$ptycontact,STRING);
$remark = $jwtf->validateParameter('Remark',$rmk,STRING);
$activity_type = $jwtf->validateParameter('ActivityType',$acttype,STRING);
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
		$resp = $pst->updateUserActivity($user_code,"Activity","Add",$lati,$longi,$user_id,$trackaddrs,$trackdistc);
		if($resp){
			$upd_act = explode("~",$pst->updateActivity($user_id,$user_code,$party_name,$party_contact,$remark,$activity_type,$lati,$longi,$trackaddrs,$doc_encodestr,$doc_name,$activityAction));
			if($upd_act[0] == "1"){
				$a = array("userid" => $user_id, "usercode" => $user_code, "partyname" => $party_name, "partycontact" => $party_contact, "activitytype" => $activity_type, "sysrefno" => $upd_act[1]);
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