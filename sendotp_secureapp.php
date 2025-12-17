<?php
/////// get bearer token
function getBearerToken($mobileno,$deviceid){
	$arr = array("mobile_no"=>$mobileno,"device_id"=>$deviceid);
	$post_data = json_encode($arr);
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'http://prd.cansale.in/secureapp/getBearerToken.php',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS => $post_data,
	  CURLOPT_HTTPHEADER => array(
		'Content-Type: application/json'
	  ),
	));
	
	$response = curl_exec($curl);
	
	curl_close($curl);
	return $response;
}
function sendOtpToSecureApp($mobileno,$emailid,$deviceid,$projectcode,$lat,$lng,$address,$token,$otp,$otp_date,$gen_time,$exp_time){
	$arr = array("mobile_no"=>$mobileno,"email_id"=>$emailid,"device_id"=>$deviceid,"project_code"=>$projectcode,"otp"=>$otp,"otpdate"=>$otp_date,"gentime"=>base64_encode($gen_time),"exptime"=>base64_encode($exp_time),"latitude"=>$lat,"longitude"=>$lng,"address"=>$address);
	$post_data = json_encode($arr);
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'http://prd.cansale.in/secureapp/postProjectOTP.php',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS => $post_data,
	  CURLOPT_HTTPHEADER => array(
		'Content-Type: application/json',
		'Authorization: Bearer '.$token
	  ),
	));
	
    $response = curl_exec($curl);
	
	curl_close($curl);
	return $response;
}
