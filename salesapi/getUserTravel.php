<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions.php';
$get = new GET_Functions();
////// get JSON data
$data = json_decode(file_get_contents("php://input"));
$uid = $data->userid;
$ucode = $data->usercode;
$fdate = $data->fromdate;
$tdate = $data->todate;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$from_date = $jwtf->validateParameter('FromDate',$fdate,STRING);
$to_date = $jwtf->validateParameter('ToDate',$tdate,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_attend = $get->getUserTravel($user_code,$from_date,$to_date);
		if ($res_attend != false){
			$row = mysqli_fetch_array($res_attend);
			$a = array("userid" => $user_id, "usercode" => $user_code, "intime" => $row["in_datetime"], "outtime" => $row["out_datetime"], "statusin" => $row["status_in"], "statusout" => $row["status_out"], "inlatitude" => $row["latitude_in"], "inlongitude" => $row["longitude_in"], "outlatitude" => $row["latitude_out"], "outlongitude" => $row["longitude_out"], "inaddress" => $row["address_in"], "outaddress" => $row["address_out"], "inimage" => ATTACHMENT_URL."travelimg/".substr($row["insert_date"],0,7)."/".$row["Image_in"], "outimage" => ATTACHMENT_URL."travelimg/".substr($row["insert_date"],0,7)."/".$row["Image_out"]);
			$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);    
		}			
	}else{
		$decode_resp;
	}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>