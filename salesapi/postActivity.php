<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
include_once 'db_functions.php'; 
$db = new DB_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'post_functions.php';
$pst = new POST_Functions();
////// get JSON data
$data = json_decode(file_get_contents("php://input"));
$uid = $data->userid;
$ucode = $data->usercode;

$acttype = $data->activitytype;
$ptyname = $data->partyname;
$ptycode = $data->partycode;
$rmk = $data->remark;

$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;
/////// start by shekhar on 08 oct 2022
$time_zone=time() + 0;	
date_default_timezone_set ("Asia/Calcutta");
$datetime = date("Y-m-d H:i:s");
/////// end by shekhar on 08 oct 2022
/////Attendance In parameter
$in_latitude = $data->inLatitude;
$in_longitude = $data->inLongitude;

//$in_datetime = $data->inDateTime;  //// this is from app
$in_datetime = $datetime;    //// this is from server

$in_status = $data->inStatus;
$in_address = $data->inAddress;
$in_image = $data->inImage;
$in_image_name = $data->inImageName;
/////Attendance Out parameter
$out_latitude = $data->outLatitude;
$out_longitude = $data->outLongitude;

//$out_datetime = $data->outDateTime; //// this is from app
$out_datetime = $datetime;   //// this is from server

$out_status = $data->outStatus;
$out_address = $data->outAddress;
$out_image = $data->outImage;
$out_image_name = $data->outImageName;
//// validate parameter
$userId = $jwtf->validateParameter('UserId',$uid,STRING);
$userCode = $jwtf->validateParameter('UserCode',$ucode,STRING);

$activityType = $jwtf->validateParameter('ActivityType',$acttype,STRING);
$partyName = $jwtf->validateParameter('PartyName',$ptyname,STRING);
$partyCode = $jwtf->validateParameter('PartyCode',$ptycode,STRING);
$remark = $jwtf->validateParameter('Remark',$rmk,STRING);

if($in_status){
	$inLatitude = $jwtf->validateParameter('InLatitude',$in_latitude,STRING);
	$inLongitude = $jwtf->validateParameter('InLongitude',$in_longitude,STRING);
	$inDatetime = $jwtf->validateParameter('InDateTime',$in_datetime,STRING);
	$inStatus = $jwtf->validateParameter('InStatus',$in_status,STRING);
	$inAddress = $jwtf->validateParameter('InAddress',$in_address,STRING);
	$inImage = $jwtf->validateParameter('InImage',$in_image,STRING);
	$inImageName = $jwtf->validateParameter('InImageName',$in_image_name,STRING);
	
	$lati = $inLatitude;
	$longi = $inLongitude;
}else{
	$inLatitude = $in_latitude;
	$inLongitude = $in_longitude;
	$inDatetime = $in_datetime;
	$inStatus = $in_status;
	$inAddress = $in_address;
	$inImage = $in_image;
	$inImageName = $in_image_name;
}
if($out_status){
	$outLatitude = $jwtf->validateParameter('OutLatitude',$out_latitude,STRING);
	$outLongitude = $jwtf->validateParameter('OutLongitude',$out_longitude,STRING);
	$outDatetime = $jwtf->validateParameter('OutDateTime',$out_datetime,STRING);
	$outStatus = $jwtf->validateParameter('OutStatus',$out_status,STRING);
	$outAddress = $jwtf->validateParameter('OutAddress',$out_address,STRING);
	$outImage = $jwtf->validateParameter('OutImage',$out_image,STRING);
	$outImageName = $jwtf->validateParameter('OutImageName',$out_image_name,STRING);
	
	$lati = $outLatitude;
	$longi = $outLongitude;
}else{
	$outLatitude = $out_latitude;
	$outLongitude = $out_longitude;
	$outDatetime = $out_datetime;
	$outStatus = $out_status;
	$outAddress = $out_address;
	$outImage = $out_image;
	$outImageName = $out_image_name;

}
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$userId);
	if($decode_resp == "SUCCESS_RESPONSE"){
		////// check user is active or not
		$resu = $db->checkUesr($userId);
		$rowu = mysqli_fetch_array($resu);
		if($rowu["status"]=="active"){
			////// track user activity
			$resp = $pst->updateUserActivity($userCode,"User Attendance","Update-".$inStatus."".$outStatus,$lati,$longi,$userId,$trackaddrs,$trackdistc);
			if($resp){
				$upd_act = explode("~",$pst->updateTaskActivity($userId,$userCode,$activityType,$partyName,$partyCode,$remark,$inLatitude,$inLongitude,$inDatetime,$inStatus,$inAddress,$inImage,$inImageName,$outLatitude,$outLongitude,$outDatetime,$outStatus,$outAddress,$outImage,$outImageName));
				if($upd_act[0] == "1"){
					//////////
					$a = array("username" => $userId, "useremail" => $userCode);
					$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
				}else{
					$jwtf->returnResponse(FAILED_RESPONSE,$pager,$upd_act[1]);
				}
			}else{
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Something went wrong");
			}
		}else{
			$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Userid is deactive");
		}
	}else{
		$decode_resp;
	}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>