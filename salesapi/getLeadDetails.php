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
$leadid = $data->leadid;
$sysrefno = $data->sysrefno;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$lead_id = $jwtf->validateParameter('LeadId',$leadid,STRING);
$system_refno = $jwtf->validateParameter('SystemRefNo',$sysrefno,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_leadlist = $get->getLeadList($user_code,"","",$system_refno);
		if ($res_leadlist != false){
			$row = mysqli_fetch_array($res_leadlist);
			///// get lead status
			$res_leadstatus_main = $get->getLeadStatus($row["status"]);
			$row_leadstatus_main = mysqli_fetch_array($res_leadstatus_main);
			///// check if any attachment
			if($row["vcard_url"]){
				$attach_lead = ATTACHMENT_URL."leadimg/".substr($row["tdate"],0,7)."/".$row["vcard_url"];
			}else{
				$attach_lead = null;
			}
			$a = array();
			$b = array();
			$res_leadhist = $get->getLeadHistory($user_code,$system_refno);
			while($row_leadhist = mysqli_fetch_array($res_leadhist)){
				$b["id"] = $row_leadhist["id"];
				$b["internalNote"] = $row_leadhist["internal_note"];
				$b["clientNote"] = $row_leadhist["client_note"];
				$b["remark"] = $row_leadhist["remark"];
				$b["activity"] = $row_leadhist["activity"];
				$b["updateDate"] = $row_leadhist["tdate"];
				///// get lead status
				$res_leadstatus = $get->getLeadStatus($row_leadhist["status"]);
				$row_leadstatus = mysqli_fetch_array($res_leadstatus);
				$b["status"] = $row_leadstatus["status_name"];
				array_push($a,$b);
			}
			$c = array("userid" => $user_id, "usercode" => $user_code,"leadId" => $row["lid"], "sysRefNo" => $row["reference"], "partyName" => $row["partyid"], "partyState" => $row["party_state"], "partyCity" => $row["party_city"], "partyContact" => $row["party_contact"], "partyEmail" => $row["party_email"], "priority" => $row["priority"], "createDate" => $row["tdate"], "createTime" => $row["create_time"], "status" => $row_leadstatus_main["status_name"], "leadattachment" => $attach_lead, "leadhistory" => $a);
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