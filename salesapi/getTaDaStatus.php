<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
//include_once 'db_functions.php';
//$db = new DB_Functions();
//// validate parameter
$userid = $jwtf->validateParameter('uid',$_REQUEST["uid"],STRING);
try{
	////// get JWT token
	//$token = $jwtf->getBearerToken();
	///// validate token
	//$decode_resp = $jwtf->decodeJWT($token,$userid);
	//if($decode_resp == "SUCCESS_RESPONSE"){
		$a = array();
		$b = array();
		//$res_state = $db->getStateMaster();
		//if ($res_state != false){
			//while ($row = mysqli_fetch_array($res_state)) 
			
				$b["status"] = "Pending";
				array_push($a,$b);
				$b["status"] = "Approved";
				array_push($a,$b);
				$b["status"] = "Approved with deduction";
				array_push($a,$b);
				$b["status"] = "Rejected";
				array_push($a,$b);
				$b["status"] = "Hold";
				array_push($a,$b);
				$b["status"] = "Esclate to HOD";
				array_push($a,$b);         
			//}         
			$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);    
		//}			
	//}else{
		//$decode_resp;
	//}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>