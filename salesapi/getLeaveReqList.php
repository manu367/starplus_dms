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
$statuss = $data->status;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$from_date = $jwtf->validateParameter('FromDate',$fdate,STRING);
$to_date = $jwtf->validateParameter('ToDate',$tdate,STRING);
$status = $jwtf->validateParameter('Status',$statuss,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_leavelist = $get->getLeaveRequestList($user_code,$from_date,$to_date,$status,"");
		if ($res_leavelist != false){
			$a = array();
			$b = array();
			while($row = mysqli_fetch_array($res_leavelist)){
				$b["id"] = $row["id"];
				$b["sysRefNo"] = "REQ".$row["id"];
				$b["leaveType"] = $row["leave_type"];
				$b["fromDate"] = $row["from_date"];
				$b["toDate"] = $row["to_date"];
				$b["duration"] = $row["leave_duration"];
				$b["reason"] = $row["purpose"];
				$b["description"] = $row["description"];
				$b["status"] = $row["status"];
				$b["createDate"] = $row["entry_date"];
				$b["createTime"] = $row["entry_time"];
				$b["approveBy"] = $row["approve_by"];
				$b["approveDate"] = $row["approve_date"];
				array_push($a,$b);
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "leaverequestlist" => $a);
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