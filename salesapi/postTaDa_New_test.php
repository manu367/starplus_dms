<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'post_functions.php';
$pst = new POST_Functions();

$reflection_class = new ReflectionClass($pst);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($pst); 
////// get JSON data

$data = json_decode(utf8_decode(file_get_contents("php://input")));

$uid = $data->userid;
$ucode = $data->usercode;
$expdate = $data->expenseDate;
$refno = $data->refNo;
/////Posted TA DA parameter
$fdexpamt = $data->foodexpamt;
$fdexpencdstr = $data->foodexpencodestr;
$fdexpdocname = $data->foodexpdocname;

$coexpamt = $data->localexpamt;
$coexpencdstr = $data->localexpencodestr;
$coexpdocname = $data->localexpdocname;

$lcexpamt = $data->travelexpamt;
$lcexpencdstr = $data->travelexpencodestr;
$lcexpdocname = $data->travelexpdocname;
$lcexptravelmode = $data->travelmode;

$mbexpamt = $data->nontravelexpamt;
$mbexpencdstr = $data->nontravelexpencodestr;
$mbexpdocname = $data->nontravelexpdocname;

$otexpamt = $data->otherexpamt;
$otexpencdstr = $data->otherexpencodestr;
$otexpdocname = $data->otherexpdocname;

$htexpamt = $data->hotelexpamt;
$htexpencdstr = $data->hotelexpencodestr;
$htexpdocname = $data->hotelexpdocname;

$expremark = $data->remark;

$lat = $data->latitude;
$long = $data->longitude;
$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;

/////// add on parameter on 19 dec 2022 by shekhar on eastman request of TA/DA policy
$floc = $data->fromloc;
$tloc = $data->toloc;
$trvltyp = $data->traveltype;
$purp = $data->purpose;

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
$localexp_travelmode = $jwtf->validateParameter('TravelMode',$lcexptravelmode,STRING);

$mobileexp_amt = $jwtf->validateParameter('MobileExpAmt',$mbexpamt,STRING);
$mobileexp_encodestr = $jwtf->validateParameter('MobileExpEncodeStr',$mbexpencdstr,STRING);
$mobileexp_docname = $jwtf->validateParameter('MobileExpDocName',$mbexpdocname,STRING);

$otherexp_amt = $jwtf->validateParameter('OtherExpAmt',$otexpamt,STRING);
$otherexp_encodestr = $jwtf->validateParameter('OtherExpEncodeStr',$otexpencdstr,STRING);
$otherexp_docname = $jwtf->validateParameter('OtherExpDocName',$otexpdocname,STRING);

$hotelexp_amt = $jwtf->validateParameter('HotelExpAmt',$htexpamt,STRING);
$hotelexp_encodestr = $jwtf->validateParameter('HotelExpEncodeStr',$htexpencdstr,STRING);
$hotelexp_docname = $jwtf->validateParameter('HotelExpDocName',$htexpdocname,STRING);

/////// add on parameter on 19 dec 2022 by shekhar on eastman request of TA/DA policy
$from_loc = $jwtf->validateParameter('FromLocation',$floc,STRING);
$to_loc = $jwtf->validateParameter('ToLocation',$tloc,STRING);
$travel_type = $jwtf->validateParameter('TravelType',$trvltyp,STRING);
$purpos = $jwtf->validateParameter('Purpose',$purp,STRING);

////////
mysqli_query($conn,"INSERT INTO api_json SET api_name='TA DA', api_nature='REQUEST', request_json='".json_encode($data)."', response_json='', entry_by='".$ucode."', entry_date='".date("Y-m-d H:i:s")."'");

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
			$add_tada = explode("~",$pst->updateTaDaNew($user_id,$user_code,$foodexp_amt,$foodexp_encodestr,$foodexp_docname,$courierexp_amt,$courierexp_encodestr,$courierexp_docname,$localexp_amt,$localexp_encodestr,$localexp_docname,$mobileexp_amt,$mobileexp_encodestr,$mobileexp_docname,$otherexp_amt,$otherexp_encodestr,$otherexp_docname,$lati,$longi,$expense_date,$refno,$localexp_travelmode,$hotelexp_amt,$hotelexp_encodestr,$hotelexp_docname,$expremark,$from_loc,$to_loc,$travel_type,$purpos));
			if($add_tada[0] == "1"){
				$a = array("userid" => $user_id, "usercode" => $user_code, "foodexpamt" => $foodexp_amt, "courierexpamt" => $courierexp_amt, "localexpamt" => $localexp_amt, "mobileexpamt" => $mobileexp_amt, "otherexpamt" => $otherexp_amt, "hotelexpamt" => $hotelexp_amt, "sysrefno" => $add_tada[1], "expdate" => $expense_date);
				$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
			}else{
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,$add_tada[1]);
			}
		}else{
			$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Something went Wrong!");
		}
	}else{
		$decode_resp;
	}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>