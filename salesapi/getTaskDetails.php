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
$tskid = $data->taskid;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$task_id = $jwtf->validateParameter('TaskId',$tskid,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_taskdet = $get->getTaskList($user_code,"","",$task_id);
		if ($res_taskdet != false){
			$row = mysqli_fetch_array($res_taskdet);
			$a = array();
			$b = array();
			$res_taskhist = $get->getTaskHistory($user_code,$task_id);
			if(mysqli_num_rows($res_taskhist)>0){
				while($row_taskhist = mysqli_fetch_array($res_taskhist)){
					$b["taskName"] = $row_taskhist["task_name"];
					$b["taskDate"] = $row_taskhist["task_date"];
					$b["taskStatus"] = $row_taskhist["task_status"];
					$b["taskAction"] = $row_taskhist["action_perform"];
					$b["taskRemark"] = $row_taskhist["remark"];
					$b["taskActionDate"] = $row_taskhist["entry_date"];
					array_push($a,$b);
				}
			}else{
				///// no history found
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "taskName" => $row["task"], "taskId" => $row["id"], "refNo" => $row["document_no"], "taskDate" => $row["plan_date"], "taskVisitArea" => $row["visit_area"], "taskTarget" => $row["task_count"], "taskEntryDate" => $row["entry_date"], "taskhistory" => $a);
			$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$c);
		}			
	}else{
		$decode_resp;
	}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>