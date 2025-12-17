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
$dcode = $data->dealercode;
$amt = $data->amount;
$paymode = $data->paymentmode;
$transno = $data->transactionno;
$transdate = $data->transactiondate;
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

$dealer_code = $jwtf->validateParameter('DealerCode',$dcode,STRING);
$amount = $jwtf->validateParameter('Amount',$amt,STRING);
$payment_mode = $jwtf->validateParameter('PaymentMode',$paymode,STRING);
$transaction_no = $jwtf->validateParameter('TransactionNo',$transno,STRING);
$transaction_date = $jwtf->validateParameter('TransactionDate',$transdate,STRING);
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
		$resp = $pst->updateUserActivity($user_code,"Collection","Update",$lati,$longi,$user_id,$trackaddrs,$trackdistc);
		if($resp){
			$upd_collection = explode("~",$pst->updateCollection($user_id,$user_code,$dealer_code,$amount,$payment_mode,$transaction_no,$transaction_date,$doc_encodestr,$doc_name,$task_id,$lati,$longi,$trackaddrs));
			if($upd_collection[0] == "1"){
				$a = array("userid" => $user_id, "usercode" => $user_code, "transactionno" => $transaction_no, "dealercode" => $dealer_code, "sysrefno" => "COLXYZ0001");
				$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
			}else{
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,$upd_collection[1]);
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