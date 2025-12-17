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
/////Collection parameter
$fdfor = $data->feedbackfor;
$fdtype = $data->feedbacktype;
$fdtitle = $data->feedbacktitle;
$fdmsg = $data->feedbackmsg;
$fdrate = $data->feedbackrating;
$contact_no = $data->contactno;
$party_name = $data->partyname;
$docencdstr = $data->docencodestr;
$docnm = $data->docname;
$lat = $data->latitude;
$long = $data->longitude;
$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$task_id = $jwtf->validateParameter('taskId',$tskid,STRING);
////////
$feedback_for = $jwtf->validateParameter('FeedbackFor',$fdfor,STRING);
$feedback_type = $jwtf->validateParameter('FeedbackType',$fdtype,STRING);
$feedback_title = $jwtf->validateParameter('FeedbackTitle',$fdtitle,STRING);
$feedback_msg = $jwtf->validateParameter('FeedbackMsg',$fdmsg,STRING);
$feedback_rate = $jwtf->validateParameter('FeedbackRating',$fdrate,INTEGER);
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
		$resp = $pst->updateUserActivity($user_code,"Feedback","Update",$lati,$longi,$user_id,$trackaddrs,$trackdistc);
		if($resp){
			$upd_feedback = explode("~",$pst->updateFeedback($user_id,$user_code,$feedback_for,$feedback_type,$feedback_title,$feedback_msg,$feedback_rate,$task_id,$lati,$longi,$doc_encodestr,$doc_name,$trackaddrs,$contact_no,$party_name));
			if($upd_feedback[0] == "1"){
				$a = array("userid" => $user_id, "usercode" => $user_code, "feedbackfor" => $feedback_for, "feedbacktype" => $feedback_type,"feedbacktitle" => $feedback_title, "sysrefno" => $upd_feedback[1]);
				$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
			}else{
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,$upd_feedback[1]);
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