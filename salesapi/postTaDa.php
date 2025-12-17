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
$expdate = $data->expenseDate;
$refno = $data->refNo;
/////Posted TA DA parameter
$fdexpamt = $data->foodexpamt;
$fdexpencdstr = $data->foodexpencodestr;
$fdexpdocname = $data->foodexpdocname;

$coexpamt = $data->courierexpamt;
$coexpencdstr = $data->courierexpencodestr;
$coexpdocname = $data->courierexpdocname;

$lcexpamt = $data->localexpamt;
$lcexpencdstr = $data->localexpencodestr;
$lcexpdocname = $data->localexpdocname;

$mbexpamt = $data->mobileexpamt;
$mbexpencdstr = $data->mobileexpencodestr;
$mbexpdocname = $data->mobileexpdocname;

$otexpamt = $data->otherexpamt;
$otexpencdstr = $data->otherexpencodestr;
$otexpdocname = $data->otherexpdocname;

$lat = $data->latitude;
$long = $data->longitude;
$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$expense_date = $jwtf->validateParameter('ExpenseDate',$expdate,STRING);
////////
$foodexp_amt = $jwtf->validateParameter('FoodExpAmt',$fdexpamt,STRING);
$foodexp_encodestr = $jwtf->validateParameter('FoodExpEncodeStr',$fdexpencdstr,STRING);
$foodexp_docname = $jwtf->validateParameter('FoodExpDocName',$fdexpdocname,STRING);

$courierexp_amt = $jwtf->validateParameter('CourierExpAmt',$coexpamt,STRING);
$courierexp_encodestr = $jwtf->validateParameter('CourierExpEncodeStr',$coexpencdstr,STRING);
$courierexp_docname = $jwtf->validateParameter('CourierExpDocName',$coexpdocname,STRING);

$localexp_amt = $jwtf->validateParameter('LocalExpAmt',$lcexpamt,STRING);
$localexp_encodestr = $jwtf->validateParameter('LocalExpEncodeStr',$lcexpencdstr,STRING);
$localexp_docname = $jwtf->validateParameter('LocalExpDocName',$lcexpdocname,STRING);

$mobileexp_amt = $jwtf->validateParameter('MobileExpAmt',$mbexpamt,STRING);
$mobileexp_encodestr = $jwtf->validateParameter('MobileExpEncodeStr',$mbexpencdstr,STRING);
$mobileexp_docname = $jwtf->validateParameter('MobileExpDocName',$mbexpdocname,STRING);

$otherexp_amt = $jwtf->validateParameter('OtherExpAmt',$otexpamt,STRING);
$otherexp_encodestr = $jwtf->validateParameter('OtherExpEncodeStr',$otexpencdstr,STRING);
$otherexp_docname = $jwtf->validateParameter('OtherExpDocName',$otexpdocname,STRING);


$lati = $jwtf->validateParameter('latitude',$lat,STRING);
$longi = $jwtf->validateParameter('longitude',$long,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		////// track user activity
		$resp = $pst->updateUserActivity($user_code,"TADA","Add",$lati,$longi,$user_id,$trackaddrs,$trackdistc);
		if($resp){
			$add_tada = explode("~",$pst->updateTaDa($user_id,$user_code,$foodexp_amt,$foodexp_encodestr,$foodexp_docname,$courierexp_amt,$courierexp_encodestr,$courierexp_docname,$localexp_amt,$localexp_encodestr,$localexp_docname,$mobileexp_amt,$mobileexp_encodestr,$mobileexp_docname,$otherexp_amt,$otherexp_encodestr,$otherexp_docname,$lati,$longi,$expense_date,$refno));
			if($add_tada[0] == "1"){
				$a = array("userid" => $user_id, "usercode" => $user_code, "foodexpamt" => $foodexp_amt, "courierexpamt" => $courierexp_amt, "localexpamt" => $localexp_amt, "mobileexpamt" => $mobileexp_amt, "otherexpamt" => $otherexp_amt, "sysrefno" => $add_tada[1], "expdate" => $expense_date);
				$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
			}else{
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,$add_tada[1]);
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