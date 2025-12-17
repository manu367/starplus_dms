<?php
include('constant.php');
require "vendor/autoload.php";
use \Firebase\JWT\JWT;
//include('header.php');
class JWT_Functions{
	private $db;
	private $link;
	private $dt_format;
	function __construct(){
		include_once './config/dbconnect.php';
		$this->db = new DatabaseService();
		$this->link = $this->db->getConnection();
		///////////////////
		$this->dt_format = new DateTime("now", new DateTimeZone('Asia/Calcutta')); //first argument "must" be a string
		$this->dt_format->setTimestamp(time()); //adjust the object to correct timestamp
	}
	function __destruct() {
	}
	public function generateJWT($userid,$pwd){
		////// get JSON data
		$uid = $userid;
		////// check user in db
		$res_user = mysqli_query($this->link,"SELECT uid,username,password,emailid FROM admin_users WHERE username = '".$uid."'");
		$num = mysqli_num_rows($res_user);
		if($num > 0){
			///// fetch selected user data
			$row_user = mysqli_fetch_assoc($res_user);
			///// check password
			if($pwd == $row_user["password"]){
				$uemail = $row_user["emailid"];
				$uphone = $row_user["username"];
				$uname = $row_user["name"];
				$secret_key = SECURTY_KEY;
				$issuer_claim = "localhost"; // this can be the servername
				//$audience_claim = "THE_AUDIENCE";
				$issuedat_claim = time(); // issued at
				$notbefore_claim = $issuedat_claim + 0; //not before in seconds
				$expire_claim = $issuedat_claim + 300; // expire time in seconds
				$token = array(
				"iss" => $issuer_claim,   ////A string containing the name or identifier of the issuer application. Can be a domain name and can be used to discard tokens from other applications.
				//"aud" => $audience_claim,
				"iat" => $issuedat_claim,  /////timestamp of token issuing
				"nbf" => $notbefore_claim, ////Timestamp of when the token should start being considered valid. Should be equal to or greater than iat. In this case, the token will begin to be valid after 10 seconds after being issued
				"exp" => $expire_claim,  ////Timestamp of when the token should stop to be valid. Needs to be greater than iat and nbf. In our example, the token will expire after 60 seconds of being issued.
				"data" => array(
					"uid" => $uphone,
					"uname" => $uname,
					"email" => $uemail
			));
			http_response_code(200);	
			$jwt = JWT::encode($token, $secret_key);
			//$jwt = "jkgjgkjg";
			return json_encode(
				array(
					"message" => "Token generated.",
					"jwt" => $jwt,
					"uid" => $uphone,
					"expireAt" => $expire_claim
				));
			}else{
				http_response_code(401);
				return json_encode(array("message" => "Incorrect password", "userid" => $uid));
			}
		}else{
			http_response_code(402);
			return json_encode(array("message" => "User does not exist", "userid" => $uid));
		}

	}
	public function decodeJWT($token,$userid){
		try{
			$decoded = JWT::decode($token, SECURTY_KEY, array('HS256'));
			$getuid = $decoded->data->uid;
			if($getuid != $userid){				
				//return "USER_NOT_FOUND";
				return $this->throwError(USER_NOT_FOUND,'Invalid Token');
			}else{
				return "SUCCESS_RESPONSE";
				//return $this->throwError(SUCCESS_RESPONSE,'Success');
			}
		}catch(Exception $e){
			//echo json_encode(array("message" => ACESS_TOKEN_ERROR, "status" => 0, "userid" => "admin"));
			//return "ACCESS_TOKEN_ERROR";
			//return $this->throwError(ACCESS_TOKEN_ERROR,$e->getMessage());	
			return $this->throwError(ACCESS_TOKEN_ERROR,"Token tempered");
		}
	}
	public function validateParameter($paraMeter,$value,$dataType,$required = true){	
		if($required == true && (empty($value) && $value!=0)){
			$this->throwError(VALIDATE_PARAMETER_REQUIRED,$paraMeter.' is required');
		}
		switch($dataType){	
			case BOOLEAN:
			if(!is_bool($value)){
				$this->throwError(VALIDATE_PARAMETER_DATATYPE,'Data type is not valid for '.$paraMeter);
			}
			break;
			case INTEGER:
			if(!is_numeric($value)){
				$this->throwError(VALIDATE_PARAMETER_DATATYPE,'Data type is not valid for '.$paraMeter);
			}
			break;
			case STRING:
			if(!is_string($value)){
				$this->throwError(VALIDATE_PARAMETER_DATATYPE,'Data type is not valid for '.$paraMeter);
			}
			break;
			default :
			break;
		}
		return $value;
	}
	public function throwError($code,$message){
		header("Content-Type:application/json");
		$error = json_encode(["response"=>["code"=>$code,"message"=>$message]]);
		echo $error;
		exit;

	}
	////// check authorization header
	public function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
			
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
			
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
		
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
           
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
			
        }
		
        return $headers;
    }
	public function getBearerToken() {
		 $headers = $this->getAuthorizationHeader();
		// HEADER: Get the access token from the header
		if (!empty($headers)) {
			if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
				return $matches[1];
			}
		}
		//$this->throwError(AUTHORIZATION_HEADER_NOT_FOUND,"Access Token not found");
	}
	public function returnResponse($code,$pager,$message){
		header("Content-Type:application/json");
		if(!is_array($pager)){
			$response = json_encode(["response"=>["code"=>$code,"message"=>$message]]);
		}else{
			$response = json_encode(["response"=>["code"=>$code,"pager"=>$pager,"message"=>$message]]);
		}
		echo $response;
		exit;
	}
}
?>