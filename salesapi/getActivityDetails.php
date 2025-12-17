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
$actid = $data->actid;
$sysrefno = $data->sysrefno;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$act_id = $jwtf->validateParameter('ActivityId',$actid,STRING);
$system_refno = $jwtf->validateParameter('SystemRefNo',$sysrefno,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_actlist = $get->getActivityList($user_code,"","",$system_refno);
		if ($res_actlist != false){
			$row = mysqli_fetch_array($res_actlist);
			$cd = explode(" ",$row["entry_date"]);
			///// check if any attachment
			if($row["initial_attach"]){
				$attach_main = ATTACHMENT_URL."activityimg/".substr($cd[0],0,7)."/".$row["initial_attach"];
			}else{
				$attach_main = null;
			}
			$a = array();
			$b = array();
			$res_acthist = $get->getActivityHistory($user_code,$system_refno);
			while($row_acthist = mysqli_fetch_array($res_acthist)){
				$b["id"] = $row_acthist["id"];
				$b["remark"] = $row_acthist["remark"];
				$b["status"] = $row_acthist["status"];
				if($row_acthist["attachment"]){
					$attach_hist = ATTACHMENT_URL."activityimg/".substr($cd[0],0,7)."/".$row_acthist["attachment"];
				}else{
					$attach_hist = null;
				}
				$b["attachment"] = $attach_hist;
				$b["entryBy"] = $row_acthist["entry_by"];
				$b["entryDate"] = $row_acthist["entry_date"];
				array_push($a,$b);
			}
			
			$c = array("userid" => $user_id, "usercode" => $user_code,"id" => $row["id"], "sysRefNo" => $row["ref_no"], "partyName" => $row["party_name"], "activityType" => $row["activity_type"], "remark" => $row["intial_remark"], "activityDate" => $row["activity_date"], "status" => $row["status"], "createDate" => $cd[0], "createTime" => $cd[1], "mainattachment" => $attach_main, "activityhistory" => $a);
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