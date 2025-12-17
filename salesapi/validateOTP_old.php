<?php 
include('constant.php');
require "vendor/autoload.php";
use \Firebase\JWT\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'db_functions.php'; 
$db = new DB_Functions(); 
///// get userid
$json = $_POST["otpValidateJSON"];
if (get_magic_quotes_gpc()){ 
	$json = stripslashes($json); 
}
//Decode JSON into an Array 
$data = json_decode($json);
$a = array();
//Util arrays to create response JSON 
/*$a = array();
$b = array();
$c = array();
$arr_tab = array();*/
$final_array = array();
//validate OTP entered by user if ok then give error msg
$resp = $db-> otpValidation($data->userId, $data->deviceId, $data->imei, $data->otp);
if($resp == "success"){
	$res = $db->checkUesr($data->userId);
	$row = mysqli_fetch_array($res);
	//////// generate JWT	
	$uid = $data->userId;
	$password = $row["password"];
	
	//$secret_key = "Cspl@#$%432";
	$secret_key = SECURTY_KEY;
	$issuer_claim = "CANSALEDMS"; // this can be the servername
	//$audience_claim = "THE_AUDIENCE";
	$issuedat_claim = time(); // issued at
	$notbefore_claim = $issuedat_claim + 10; //not before in seconds
	$expire_claim = $issuedat_claim + 300; // expire time in seconds
	$token = array(
            "iss" => $issuer_claim,   ////A string containing the name or identifier of the issuer application. Can be a domain name and can be used to discard tokens from other applications.
            //"aud" => $audience_claim,
            "iat" => $issuedat_claim,  /////timestamp of token issuing
            "nbf" => $notbefore_claim, ////Timestamp of when the token should start being considered valid. Should be equal to or greater than iat. In this case, the token will begin to be valid after 10 seconds after being issued
            "exp" => $expire_claim,  ////Timestamp of when the token should stop to be valid. Needs to be greater than iat and nbf. In our example, the token will expire after 60 seconds of being issued.
            "data" => array(
                "uid" => $uid,
                "uname" => $uname,
                "email" => $uemail
        ));
	http_response_code(200);
    $jwt = JWT::encode($token, $secret_key);	
	/*$res_tab = $db-> getTabRights($row["login_id"]);
	while($row_set = mysqli_fetch_assoc($res_tab)){
		$arr_tab[$row_set['tabid']] = $row_set['status_id'];
	}*/
	////// pick main tab name
	/*$res_tabn = $db-> getMainTab();
	while($row_tabn = mysqli_fetch_array($res_tabn)){
		////// pick sub tab name
		$res_subtabn = $db-> getSubTab($row_tabn['maintabname']);
		while($row_subtabn = mysqli_fetch_array($res_subtabn)){
			if($arr_tab[$row_subtabn['tabid']] == 1 && $row_subtabn['app_filename']!=""){
				$c["mainTab"] = $row_tabn['maintabname'];
				$c["subTabId"] = $row_subtabn['tabid'];
				$c["subTabName"] = $row_subtabn['subtabname'];
				$c["subTabFile"] = $row_subtabn['app_filename'];
				array_push($b,$c);
				$final_array["option_list"] = $b;
			}
		}
	}*/
	////// get left tab
	
	$arr_tab = array("TabName" => "Category1", "TabFileName" => "cat1", "TabIcon" => "tabicon1", "SubTabArray" =>"");
	array_push($a,$arr_tab);
	$arr_tab = array("TabName" => "Category2", "TabFileName" => "", "TabIcon" => "tabicon2", "SubTabArray" => array(array("SubTabName" => "My Task", "SubTabFileName" => "mytask", "SubTabIcon" => "subtabicon1"), array("SubTabName" => "Sales Order", "SubTabFileName" => "saleorder", "SubTabIcon" => "subtabicon2"), array("SubTabName" => "Attendance", "SubTabFileName" => "attendance", "SubTabIcon" => "subtabicon3"), array("SubTabName" => "TA/DA", "SubTabFileName" => "ta_da", "SubTabIcon" => "subtabicon4")));
	array_push($a,$arr_tab);
	
	$final_array["message"] = "Successfully login.";
	$final_array["status"] = 1;
	$final_array["userId"] = $row["username"];
	$final_array["userName"] = $row["name"]; 
	$final_array["userType"] = $row["utype"];
	$final_array["userContact"] = $row["phone"];
	$final_array["userEmail"] = $row["emailid"];
	$final_array["jwt"] = $jwt;
	$final_array["expireAt"] = $expire_claim;
	$final_array["tabArray"] = $a;

	echo json_encode($final_array);
}else{
	$final_array["message"] = $resp;
	$final_array["status"] = 0;
	echo json_encode($final_array);
}
?>