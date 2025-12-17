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
		$res_actlist = $get->getActivityList($user_code,$from_date,$to_date,"");
		if ($res_actlist != false){
			$a = array();
			$b = array();
			while($row = mysqli_fetch_array($res_actlist)){
				$b["id"] = $row["id"];
				$b["sysRefNo"] = $row["ref_no"];
				$b["partyName"] = $row["party_name"];
				$b["partyContact"] = $row["party_contact"];
				$b["activityType"] = $row["activity_type"];
				$b["remark"] = $row["intial_remark"];
				$b["activityDate"] = $row["activity_date"];
				$b["status"] = $row["status"];
				if($row["status"]=="Complete"){
					$b["displayStatus"] = 1;
				}else{
					$b["displayStatus"] = 0;
				}
				$cd = explode(" ",$row["entry_date"]);
				$b["createDate"] = $cd[0];
				$b["createTime"] = $cd[1];
				array_push($a,$b);
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "activitylist" => $a);
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