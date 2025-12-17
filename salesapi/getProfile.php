<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';
$db = new DB_Functions();
//// validate parameter
$userid = $jwtf->validateParameter('uid',$_REQUEST["uid"],STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$userid);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$a = array();
		$b = array();
		$res_profile = $db->checkUesr($userid);
		if ($res_profile != false){
			$row = mysqli_fetch_array($res_profile);
			//// get API URL
			$url = $db->getAPIURL();
			$b["usercode"] = $row["username"];
			$b["locationCode"] = $row["owner_code"];
			$b["username"] = $row["name"];
			$b["userphone"] = $row["phone"];
			$b["useremail"] = $row["emailid"];
			$b["useraddress"] = $row["address"];
			$b["usertype"] = $row["utype"];
			$b["userapplogout"] = $row["app_logout"];
			if($row["status"]=="active"){
				$b["userloginstatus"] = 1;
			}else{
				$b["userloginstatus"] = 0;
			}
			$b["userExpDayLimit"] = 5;
			//$b["userprofilepic"] = str_replace("/salesapi","/",$url)."".$row["profile_img_path"];
			if($row["profile_img_path"]){
				$b["userprofilepic"] = $url."".$row["profile_img_path"];
			}else{
				$b["userprofilepic"] = $url."profilepic/defaultprofile.png";
			}
			array_push($a,$b);
			$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);    
		}			
	}else{
		$decode_resp;
	}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>