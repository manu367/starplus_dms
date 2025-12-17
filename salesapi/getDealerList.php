<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions.php';
$get = new GET_Functions();
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$_REQUEST["userid"],STRING);
$user_code = $jwtf->validateParameter('UserCode',$_REQUEST["usercode"],STRING);
//$visit_city = $jwtf->validateParameter('VisitCity',$_REQUEST["vcity"],STRING);
$visit_city = rtrim($_REQUEST["vcity"]," ");
$visit_city = ltrim($visit_city," ");
$pjp_id = $_REQUEST["taskkey"];
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_dealerlist = $get->getDealerList($user_code,$visit_city);
		if ($res_dealerlist != false){
			$a = array();
			$b = array();
			while($row = mysqli_fetch_array($res_dealerlist)){
				$b["dealercode"] = $row["asc_code"];
				$b["dealername"] = $row["name"].",".$row["city"].",".$row["state"];
				$b["dealercity"] = $row["city"];
				$b["dealerstate"] = $row["state"];
				$b["dealercontactno"] = $row["phone"];
				$b["dealeremail"] = $row["email"];
				$b["createdate"] = $row["start_date"];
				$b["dealertype"] = $row["id_type"];
				array_push($a,$b);
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "visitcity" => $visit_city, "pjpid" => $pjp_id, "dealerlist" => $a);
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