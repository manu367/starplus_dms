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
$refno = $data->expenseNo;

$lat = $data->latitude;
$long = $data->longitude;
$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$reference_no = $jwtf->validateParameter('RefNo',$refno,STRING);


$lati = $jwtf->validateParameter('latitude',$lat,STRING);
$longi = $jwtf->validateParameter('longitude',$long,STRING);
if($lati!="" && $longi!=""){
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		if($reference_no){
			////// track user activity
			$resp = $pst->updateUserActivity($user_code,"TADA","Cancel",$lati,$longi,$reference_no,$trackaddrs,$trackdistc);
			if($resp){
				$upd_tada = explode("~",$pst->cancelTaDa($reference_no));
				if($upd_tada[0] == "1"){
					$a = array("userid" => $user_id, "usercode" => $user_code, "expenseNo" => $upd_tada[1], "msg" => "Expense is cancelled");
					$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
				}else{
					$jwtf->returnResponse(FAILED_RESPONSE,$pager,$upd_tada[1]);
				}
			}else{
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Something went wrong");
			}
		}else{
			$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Ref. No. is blank");
		}
	}else{
		$decode_resp;
	}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
}else{
	$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Location missing");
}
?>