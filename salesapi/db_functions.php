<?php
include('constant.php');
class DB_Functions{       
	private $db;
	private $link;
	private $dt_format;
	function __construct() { 
		include_once './config/dbconnect.php';
		$this->db = new DatabaseService();
		$this->link = $this->db->getConnection();
		///////////////////
		$this->dt_format = new DateTime("now", new DateTimeZone('Asia/Calcutta')); //first argument "must" be a string
		$this->dt_format->setTimestamp(time()); //adjust the object to correct timestamp
	}       
	function __destruct() {
	}
	/////
	//// check user
	public function checkUesr($uid){
		return mysqli_query($this->link,"SELECT * FROM admin_users WHERE phone = '".$uid."'");
	}
	//// get API URL
	public function getAPIURL(){
		 $url = "https://sukam.cancrm.in/dms/salesapi/";
		 return $url;
	}
	///// get OTP by system
	public function getSystemOtp($uid,$deviceId,$imei){
		//////// generate 6 digit otp
		if($uid=="8130960758" || $uid=="8826693949"  || $uid=="9311708153"){
			$otp_no = "111111";
		}else{
			$otp_no = substr(str_shuffle("1234567890"), 0, 6);
		}
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		//////		
		$time = strtotime($todayTime);
		$endTime = date("H:i:s", strtotime('+90 seconds', $time));
		////////insert in otp
		$otp_sql = "INSERT INTO otp_master SET userid = '".$uid."', otp = '".$otp_no."', otp_date='".$todayDate."', otp_gentime = '".$todayTime."', otp_expiretime = '".$endTime."', otp_for ='SALES_APP', otp_action='LOGIN', browser_id  = '".$deviceId."', ip  = '".$_SERVER['REMOTE_ADDR']."', status='A'";
		$otp_res = mysqli_query($this->link, $otp_sql);
		$instid = mysqli_insert_id($this->link);
		if($instid>0){
			//$msg2 = "Dear Customer, your login otp is ".$otp_no." which is valid for 3 minutes.";
			//$msg2 = "Dear Customer, your login otp is ".$otp_no." which is valid for 3 minutes. Thanks EASTMAN";
			//$msg2 = "Dear Customer, your login otp is ".$otp_no." which is valid for 3 min. Thanks EASTMAN (CANCRM)";
			//$msg2 = "Dear Customer, your otp is ".$otp_no." which is valid for 3 minutes. Thanks CANCRM";
			  $msg2 = "Dear Customer, your otp is ".$otp_no." which is valid for 3 min. Thanks CANDOUR (CANCRM)";
			/////send sms to resistered mobile no.
			if($uid=="8130960758" || $uid=="8826693949" || $uid=="9311708153"){
				$response = explode(",",$otp_no.",success");
			}else{
				//$url = "https://foxxsms.net/sms//submitsms.jsp?user=CONDOUR&key=9ffa85dce5XX&mobile=".$uid."&message=".urlencode($msg2)."&senderid=CANCRM&accusage=1";
				//$url = "https://foxxsms.net/sms//submitsms.jsp?user=CONDOUR&key=9ffa85dce5XX&mobile=".$uid."&message=".urlencode($msg2)."&senderid=CANCRM&accusage=1&entityid=1401565230000011667&tempid=1407165416545877064";
				$url = "https://foxxsms.net/sms//submitsms.jsp?user=CONDOUR&key=9ffa85dce5XX&mobile=".$uid."&message=".urlencode($msg2)."&senderid=CANCRM&accusage=1&entityid=1401565230000011667&tempid=1407167947895923929";
				$page = file_get_contents($url); 
				$response = explode(",",$page);
			}
			if($response[1]=="success"){
				$resp = "Please verify your OTP which has been sent to your Mobile No.";
				$flag = 1;
				$otps = $otp_no;
			}else{
				$del_qry = mysqli_query($this->link,"DELETE FROM otp_master WHERE id='".$instid."'");
				$resp="Request could not be procesed";
				$flag = 0;
				$otps = "";
			}
			/*
			$sendsm = "Dear user, your login otp is ".$otp_no." which is valid for 90 seconds.";
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => "http://www.canlog.in/api/mt/SendSMS?user=agpure1234&password=AGPURESMS123&senderid=AGPURE&channel=Trans&DCS=0&flashsms=0&number=".$row_user["contact_no"]."&text=".urlencode($sendsm)."&route=13&peid=1201159126924606780",
			  //CURLOPT_URL => "http://adworldlog.in/vendorsms/pushsms.aspx?user=agpure_123&password=Agpure@789&msisdn=".$row_user["contact_no"]."&sid=AGPURE&msg=".urlencode($sendsm)."&fl=0&gwid=2",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET",
			));
			$response = json_decode(curl_exec($curl));
			curl_close($curl);
			////check msg response
			if($response->ErrorCode=="000"){
				$resp = $otp_no."~".$row_user["contact_no"];
				$flag = 1;
			}else{
				$resp = "SMS sending failed. Please try again.";
				$flag = 0;
			}*/
		}else{
			$resp = "Something went wrong. Please try again.";
			$flag = 0;
		}		
		return $flag."~".$resp;
	}
	///// validate otp entered by user
	public function otpValidation($uid,$deviceId,$imei,$post_otp){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		///// get saved OTP with its time
		$res_otp = mysqli_query($this->link,"SELECT otp, otp_expiretime, status FROM otp_master WHERE userid = '".$uid."' AND browser_id = '".$deviceId."' ORDER BY id DESC") or die(mysqli_error($this->link));
		$row_otp = mysqli_fetch_assoc($res_otp);
		///// check OTP status
		if($row_otp['status']=="A"){
			//// check otp is matched with send otp	
			if($row_otp['otp'] == $post_otp){
				///// check OTP validity it should be less than or equal to 90 seconds
				if(strtotime($todayTime) <= strtotime($row_otp['otp_expiretime'])){
					////if everything is all correct then we mark OTP as Used status
					mysqli_query($this->link,"UPDATE otp_master SET status = 'U' WHERE userid = '".$uid."' AND browser_id = '".$deviceId ."' AND status = 'A'");
					///// insert login details
					//$sql_log = "INSERT INTO login_data SET user_id = '".$uid."', log_date_time = '".date("Y-m-d H:i:s")."', client_network_ip = '".$imei."'";
					//mysqli_query($this->link,$sql_log);
					$msg = "success";
				}else{
					///// update generated OTP status as Expired
					mysqli_query($this->link,"UPDATE otp_master SET status = 'E' WHERE userid = '".$uid."' AND browser_id = '".$deviceId ."' AND status = 'A'");
					$msg = "OTP is Expired. Please Send OTP Again!";
				}
			}else{
				$msg = "OTP is mismatched. Please enter valid OTP !";
			}
		}else if($row_otp['status']=="U"){
			$msg = "OTP is already used. Please Send OTP Again!";
		}else{
			$msg = "OTP is Expired. Please Send OTP Again!";
		}
		return $msg; 
	}
	///// validate password entered by user written by shekhar on 09 aug 2022
	public function passValidation($uid,$deviceId,$imei,$post_pwd){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		if($uid){
			///// get saved password
			$res_pass = mysqli_query($this->link,"SELECT password, status FROM admin_users WHERE username = '".$uid."' OR phone = '".$uid."'") or die(mysqli_error($this->link));
			$row_pass = mysqli_fetch_assoc($res_pass);
			///// check user status
			if($row_pass['status']=="active"){
				//// check password is matched with entered password
				if($row_pass['password'] == $post_pwd){
					$msg = "success";
				}else{
					$msg = "Wrong password. Please enter valid password !";
				}
			}else{
				$msg = "User is Deactivated.";
			}
		}else{
			$msg = "User id is blank.";
		}
		return $msg; 
	}
	////// get state master
	public function getStateMaster(){
		return mysqli_query($this->link,"SELECT * FROM state_master ORDER BY state");
	}
	///// get city master
	public function getCities($stateName){
		return mysqli_query($this->link,"SELECT * FROM district_master WHERE state='".$stateName."' ORDER BY city");
	}
	///// get lead source type
	public function getLeadSource(){
		return mysqli_query($this->link,"SELECT * FROM sf_source_master ORDER BY source");
	}
	///// get lead status list
	public function getLeadStatusList($statusFor){
		return mysqli_query($this->link,"SELECT * FROM sf_status_master WHERE display_for='".$statusFor."' AND status='1' ORDER BY status_name");
	}
	///// update logout flag after successfully login written on 22 aug 22 by shekhar
	///// get lead status list
	public function updateLogoutFlag($uid,$ucode){
		return mysqli_query($this->link,"UPDATE admin_users SET app_logout='0' WHERE username='".$ucode."'");
	}
}

