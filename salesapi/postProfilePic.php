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
////this must be base64 encoded string
$uprofilepic = $data->userprofilepic;
/////
$uprofilepicname = $data->userprofilepicname;
$lat = $data->latitude;
$long = $data->longitude;
$trackaddrs = $data->trackaddress;
$trackdistc = $data->trackdistance;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$user_profilepic = $jwtf->validateParameter('UserProfilePic',$uprofilepic,STRING);
$user_profilepicname = $jwtf->validateParameter('UserProfilePicName',$uprofilepicname,STRING);
//$user_profilepicname = $userprofilepicname;
$lati = $jwtf->validateParameter('latitude',$lat,STRING);
$longi = $jwtf->validateParameter('longitude',$long,STRING);
/////
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		////// track user activity
		$resp = $pst->updateUserActivity($user_code,"User Profile Pic","Update",$lati,$longi,$user_id,$trackaddrs,$trackdistc);		
		if($resp){
			///check directory
			$pp = "profilepic";
			if (!is_dir($pp)) {
				mkdir($pp, 0777, 'R');
			}
			$base = $user_profilepic;
			// Get file name posted from Android App
			$filename = $user_profilepicname;
			// Decode Image//
			if($base!=null){
				$binary=base64_decode($base);
			}
			else{
				$binary=null;
			}
    		header('Content-Type: bitmap; charset=utf-8');
			$file_nm = $pp.'/'.$filename;
    		// Images will be saved under 'www/imgupload/uplodedimages' folder   
			$file = fopen($file_nm, 'wb');
    		// Create File
			if($binary!=null){
				fwrite($file, $binary);
				fclose($file);
				///// make column array
				$user_arr = array("profile_img_name" => $filename, "profile_img_path" => $file_nm);
				$upd_profile = explode("~",$pst->updateUserProfile($user_id,$user_arr));
				if($upd_profile[0] == "1"){
					$a = array("dpurl" => ATTACHMENT_URL."".$pp."/".$filename, "userid" => $user_id);
					$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
				}else{
					$jwtf->returnResponse(FAILED_RESPONSE,$pager,$upd_profile[1]);
				}
			}else{
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Pic not uploaded");
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