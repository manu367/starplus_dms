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
		$res_leadlist = $get->getLeadList($user_code,$from_date,$to_date,"");
		if ($res_leadlist != false){
			$a = array();
			$b = array();
			while($row = mysqli_fetch_array($res_leadlist)){
				$b["leadId"] = $row["lid"];
				$b["sysRefNo"] = $row["reference"];
				$b["partyName"] = $row["partyid"];
				$b["partyState"] = $row["party_state"];
				$b["partyCity"] = $row["party_city"];
				$b["partyContact"] = $row["party_contact"];
				$b["partyEmail"] = $row["party_email"];
				$b["priority"] = $row["priority"];
				$b["createDate"] = $row["tdate"];
				$b["createTime"] = $row["create_time"];
				///// get lead status
				$res_leadstatus = $get->getLeadStatus($row["status"]);
				$row_leadstatus = mysqli_fetch_array($res_leadstatus);
				$b["status"] = $row_leadstatus["status_name"];
				array_push($a,$b);
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "leadlist" => $a);
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