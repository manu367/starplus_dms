<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions.php';
$get = new GET_Functions();
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$_REQUEST["userid"],STRING);
$user_code = $jwtf->validateParameter('UserCode',$_REQUEST["usercode"],STRING);
$from_date = $jwtf->validateParameter('FromDate',$_REQUEST["fromdate"],STRING);
$to_date = $jwtf->validateParameter('ToDate',$_REQUEST["todate"],STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_timeline = $get->getUserTimeLine($user_id,$user_code,$from_date,$to_date);
		if ($res_timeline != false){
			$a = array();
			$b = array();
			$j = array();
			$old_date = "";
			$new_date = "";
			///// make count of each date
			while($row_cnt = mysqli_fetch_array($res_timeline)){
				$j[$row_cnt["entry_date"]] += 1; 
			}
			mysqli_data_seek($res_timeline, 0);
			while($row = mysqli_fetch_array($res_timeline)){
				$old_date = $row["entry_date"];
				if($old_date!=$new_date){
					$k = $j[$old_date];
				}
				$b["id"] = $k;
				$b["taskname"] = $row["task_name"];
				$b["taskaction"] = $row["task_action"];
				$b["latitude"] = $row["latitude"];
				$b["longitude"] = $row["longitude"];
				//$b["updatedate"] = $get->getISTfromUTC($row["update_date"]);
				$b["updatedate"] = $row["update_date"];
				$a[$row["entry_date"]][] = $b;
				$new_date = $row["entry_date"];
				$k--;
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "timeline" => $a);
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