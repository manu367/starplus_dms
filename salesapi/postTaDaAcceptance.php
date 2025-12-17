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
$expno = $data->expenseNo;
$accptaction = $data->acceptAction;

$lat = $data->latitude;
$long = $data->longitude;
$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$expense_no = $jwtf->validateParameter('ExpenseNo',$expno,STRING);
$accept_action = $jwtf->validateParameter('AcceptAction',$accptaction,STRING);

$lati = $jwtf->validateParameter('latitude',$lat,STRING);
$longi = $jwtf->validateParameter('longitude',$long,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		if($expense_no){
			////// track user activity
			$resp = $pst->updateUserActivity($user_code,"TADA","Accept",$lati,$longi,$expense_no,$trackaddrs,$trackdistc);
			if($resp){
				$upd_tada = explode("~",$pst->updateTaDaAccept($expense_no,$accept_action));
				if($upd_tada[0] == "1"){
					$a = array("userid" => $user_id, "usercode" => $user_code, "expenseNo" => $upd_tada[1], "msg" => "Deduction is accepted");
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
?>