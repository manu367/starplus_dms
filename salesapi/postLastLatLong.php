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
$lat = $data->latitude;
$long = $data->longitude;
$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$lati = $jwtf->validateParameter('latitude',$lat,STRING);
$longi = $jwtf->validateParameter('longitude',$long,STRING);
//try{
	////// get JWT token
	//$token = $jwtf->getBearerToken();
	///// validate token
	//$decode_resp = $jwtf->decodeJWT($token,$user_id);
	//if($decode_resp == "SUCCESS_RESPONSE"){
		////record lat long after a time interval	
		$resp = explode("~",$pst->saveLatLong($user_id,$user_code,$lati,$longi,$trackaddrs,$trackdistc));
		if($resp[0] == "1"){
			$a = array("userid" => $user_id, "usercode" => $user_code);
			$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
		}else{
			$jwtf->returnResponse(FAILED_RESPONSE,$pager,$add_tada[1]);
		}
	//}else{
		//$decode_resp;
	//}
//}catch(Exception $e){
	//$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
//}
?>