<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';
$db = new DB_Functions();
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$_REQUEST["userid"],STRING);
$user_code = $jwtf->validateParameter('UserCode',$_REQUEST["usercode"],STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_leadsource = $db->getLeadSource();
		if ($res_leadsource != false){
			$a = array();
			$b = array();
			while($row = mysqli_fetch_array($res_leadsource)){
				$b["leadsourceid"] = $row["id"];
				$b["leadsource"] = $row["source"];
				array_push($a,$b);
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "leadsourcearray" => $a);
			$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$c);
		}			
	}else{
		$decode_resp;
	}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>