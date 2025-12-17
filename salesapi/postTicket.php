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
$loccode = $data->locationcode;
$custname = $data->customername;
$custtype = $data->customertype;
$custphone = $data->customerphone;
$custaddress = $data->customeraddress;
$custcity = $data->customercity;
$custstate = $data->customerstate;
$custpincode = $data->customerpincode;
$problm = $data->problemreport;
$prod = $data->product;
$mod = $data->model;
$rmk = $data->remark;
/////Posted max 3 images
$encdstr1 = $data->encodestr1;
$imgname1 = $data->docname1;
$encdstr2 = $data->encodestr2;
$imgname2 = $data->docname2;
$encdstr3 = $data->encodestr3;
$imgname3 = $data->docname3;

$lat = $data->latitude;
$long = $data->longitude;
$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$location_code = $jwtf->validateParameter('LocationCode',$loccode,STRING);
$customer_name = $jwtf->validateParameter('CustomerName',$custname,STRING);
$customer_type = $jwtf->validateParameter('CustomerType',$custtype,STRING);
$customer_phone = $jwtf->validateParameter('CustomerPhone',$custphone,STRING);
$customer_address = $jwtf->validateParameter('CustomerAddress',$custaddress,STRING);
$customer_city = $jwtf->validateParameter('CustomerCity',$custcity,STRING);
$customer_state = $jwtf->validateParameter('CustomerState',$custstate,STRING);
$customer_pincode = $jwtf->validateParameter('CustomerPincode',$custpincode,STRING);
$problem_report = $jwtf->validateParameter('ProblemReport',$problm,STRING);
$product_name = $jwtf->validateParameter('ProductName',$prod,STRING);
$model_name = $jwtf->validateParameter('ModelName',$mod,STRING);
$remarkk = $jwtf->validateParameter('Remark',$rmk,STRING);

$img1_encodestr = $jwtf->validateParameter('ImgEncodeStr1',$encdstr1,STRING);
$img1_name = $jwtf->validateParameter('ImgName1',$imgname1,STRING);
$img2_encodestr = $jwtf->validateParameter('ImgEncodeStr2',$encdstr2,STRING);
$img2_name = $jwtf->validateParameter('ImgName2',$imgname2,STRING);
$img3_encodestr = $jwtf->validateParameter('ImgEncodeStr3',$encdstr3,STRING);
$img3_name = $jwtf->validateParameter('ImgName3',$imgname3,STRING);

$lati = $jwtf->validateParameter('latitude',$lat,STRING);
$longi = $jwtf->validateParameter('longitude',$long,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		////// track user activity
		$resp = $pst->updateUserActivity($user_code,"Ticket","Add",$lati,$longi,$user_id,$trackaddrs,$trackdistc);
		if($resp){
			$add_ticket = explode("~",$pst->updateTicket($user_id,$user_code,$location_code,$customer_name,$customer_type,$customer_phone,$customer_address,$customer_city,$customer_state,$customer_pincode,$problem_report,$product_name,$model_name,$remarkk,$img1_encodestr,$img1_name,$img2_encodestr,$img2_name,$img3_encodestr,$img3_name,$lati,$longi));
			if($add_ticket[0] == "1"){
				$a = array("userid" => $user_id, "usercode" => $user_code, "locationcode" => $location_code, "ticketno" => $add_ticket[1]);
				$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
			}else{
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,$add_ticket[1]);
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