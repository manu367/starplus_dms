<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions.php';
$get = new GET_Functions();
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$_REQUEST["userid"],STRING);
$user_code = $jwtf->validateParameter('UserCode',$_REQUEST["usercode"],STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_tasktype = $get->getTaskType();
		if ($res_tasktype != false){
			$a = array();
			$b = array();
			while($row = mysqli_fetch_array($res_tasktype)){
				$b["tasktypeid"] = $row["id"];
				$b["tasktype"] = $row["task_name"];
				array_push($a,$b);
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "tasktypearray" => $a);
			$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$c);
		}			
	}else{
		$decode_resp;
	}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>