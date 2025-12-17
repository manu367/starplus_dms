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
$uname = $data->username;
$uemail = $data->useremail;
$uaddress = $data->useraddress;
$lat = $data->latitude;
$long = $data->longitude;
$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$userCode = $jwtf->validateParameter('UserCode',$ucode,STRING);
$user_name = $jwtf->validateParameter('UserName',$uname,STRING);
$user_email = $jwtf->validateParameter('UserEmail',$uemail,STRING);
$user_address = $jwtf->validateParameter('UserAddress',$uaddress,STRING);
$lati = $jwtf->validateParameter('latitude',$lat,STRING);
$longi = $jwtf->validateParameter('longitude',$long,STRING);
///// make column array
$user_arr = array("name" => $user_name, "emailid" => $user_email, "address" => $user_address);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		////// track user activity
		$resp = $pst->updateUserActivity($userCode,"User Profile","Update",$lati,$longi,$user_id,$trackaddrs,$trackdistc);
		if($resp){
			$upd_profile = explode("~",$pst->updateUserProfile($user_id,$user_arr));
			if($upd_profile[0] == "1"){
				$a = array("username" => $user_name, "useremail" => $user_email, "useraddress" => $user_address, "userphone" => $user_id);
				$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
			}else{
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,$upd_profile[1]);
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