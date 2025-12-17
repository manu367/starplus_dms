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
$tdid = $data->tadaid;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$tada_id = $jwtf->validateParameter('TaDaId',$tdid,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_tadadet = $get->getTaDaList($user_code,"","","",$tada_id);
		if ($res_tadadet != false){
			$row = mysqli_fetch_array($res_tadadet);
			///// check each expense attachment
			if($row["food_exp_img"]){
				$attach_food = ATTACHMENT_URL."tadaimg/".substr($row["entry_date"],0,7)."/".$row["food_exp_img"];
			}else{
				$attach_food = null;
			}
			if($row["courier_exp_img"]){
				$attach_courier = ATTACHMENT_URL."tadaimg/".substr($row["entry_date"],0,7)."/".$row["courier_exp_img"];
			}else{
				$attach_courier = null;
			}
			if($row["localconv_exp_img"]){
				$attach_local = ATTACHMENT_URL."tadaimg/".substr($row["entry_date"],0,7)."/".$row["localconv_exp_img"];
			}else{
				$attach_local = null;
			}
			if($row["mobile_exp_img"]){
				$attach_mobile = ATTACHMENT_URL."tadaimg/".substr($row["entry_date"],0,7)."/".$row["mobile_exp_img"];
			}else{
				$attach_mobile = null;
			}
			if($row["other_exp_img"]){
				$attach_other = ATTACHMENT_URL."tadaimg/".substr($row["entry_date"],0,7)."/".$row["other_exp_img"];
			}else{
				$attach_other = null;
			}
			if($row["hotel_exp_img"]){
				$attach_hotel = ATTACHMENT_URL."tadaimg/".substr($row["entry_date"],0,7)."/".$row["hotel_exp_img"];
			}else{
				$attach_hotel = null;
			}
			///// get approval details
			$get_app_det = explode("~",$get->getAnyDetails($row["system_ref_no"],"action_by,action_date,action_time,action_remark","ref_no","approval_activities"));
			if($get_app_det[0]){
				$app_by = $get->getAnyDetails($get_app_det[0],"name","username","admin_users");
				$app_date = $get_app_det[1]." ".$get_app_det[2];
				$app_rmk = $get_app_det[3];
			}else{
				$app_by = "";
				$app_date = "";
				$app_rmk = "";
			}
			///////////////
			$c = array("userid" => $user_id, "usercode" => $user_code, "refId" => $row["id"], "refNo" => $row["system_ref_no"], "expDate" => $row["expense_date"], "refDate" => $row["entry_date"], "refTime" => $row["entry_time"], "totalAmt" => $row["total_amt"], "appAmt" => $row["approved_amt"], "appBy" => $app_by, "appDate" => $app_date, "appRemark" => $app_rmk, "foodExpAmt" => $row["food_exp"], "foodExpImg" => $attach_food, "courierExpAmt" => $row["courier_exp"], "courierExpImg" => $attach_courier, "localExpAmt" => $row["localconv_exp"], "localExpImg" => $attach_local, "mobileExpAmt" => $row["mobile_exp"], "mobileExpImg" => $attach_mobile, "otherExpAmt" => $row["other_exp"], "otherExpImg" => $attach_other, "hotelExpAmt" => $row["hotel_exp"], "hotelExpImg" => $attach_hotel, "status" => $row["status"], "acceptAction" => $row["accept_action"]);
			$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$c);
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