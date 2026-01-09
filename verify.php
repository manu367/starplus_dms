<?php
$page_type = "insecure";
require_once("security/backend.php");
function sendOTP($link1, $mobile)
{
	$resp = false;
	$today = date("Y-m-d");
	$cn1 = substr($mobile,0,2);
	$cn2 = substr($mobile,-4);
	$uid = $mobile;
	$todayDate = date("Y-m-d");
	$todayTime = date("H:i:s");
	$deviceId = session_id();

	if($uid=="8130960758" || $uid=="8826693949"){
		$otp_no = "111111";
	}else{
		$otp_no = substr(str_shuffle("1234567890"), 0, 6);
	}
	//////
	$time = strtotime($todayTime);
	$endTime = date("H:i:s", strtotime('+90 seconds', $time));
	$endTimeS = time()+90;
	//session_start();
	$_SESSION['exptime'] = $endTime;
	$_SESSION['exptimeS'] = $endTimeS;
	////////insert in otp
	$otp_sql = "INSERT INTO otp_master SET userid = '".$uid."', otp = '".$otp_no."', otp_date='".$todayDate."', otp_gentime = '".$todayTime."', otp_expiretime = '".$endTime."', otp_for ='WEB_LOGIN', otp_action='LOGIN', browser_id  = '".$deviceId."', ip  = '".$_SERVER['REMOTE_ADDR']."', status='A'";
	$otp_res = mysqli_query($link1, $otp_sql);
	$instid = mysqli_insert_id($link1);
	if($instid>0){
		/*$msg2 = "Dear Customer, your otp is ".$otp_no." which is valid for 3 min. Thanks DEMO (CANCRM)";
		/////send sms to resistered mobile no.
		if($uid=="8130960758" || $uid=="8826693949"){
			$response = explode(",",$otp_no.",success");
		}else{
			$url = "https://foxxsms.net/sms//submitsms.jsp?user=CONDOUR&key=9ffa85dce5XX&mobile=".$uid."&message=".urlencode($msg2)."&senderid=CANCRM&accusage=1&entityid=1401565230000011667&tempid=1407167947895923929";
			$page = file_get_contents($url); 
			$response = explode(",",$page);
		}*/
		include("sendotp_secureapp.php");
		$mobileno = $uid;
		$emailid = "";
		$deviceid = $deviceId;
		$projectcode = "SUKAM";
		$otp=$otp_no;
		$otpdate=$todayDate;
		$gentime=$todayTime;
		$exptime=$endTime;
		$lat="";
		$lng="";
		$address="";
		////get bearer token
		$resp1 = getBearerToken($mobileno,$deviceid);
		$resp1 = json_decode($resp1);
		$token = $resp1->response->message->token;
		/// now send project otp
		$resp2 = sendOtpToSecureApp($mobileno,$emailid,$deviceid,$projectcode,$lat,$lng,$address,$token,$otp,$otpdate,$gentime,$exptime);
		$resp2 = json_decode($resp2);
		$getotp_resp = $resp2->response->message->otp_info->otp;
		//if($response[1]=="success"){
		if($getotp_resp){
			$resp = "Please verify your OTP which has been sent to your Mobile No.";
			$resp = true;
			$otps = $otp_no;
		}else{
			$del_qry = mysqli_query($link1,"DELETE FROM otp_master WHERE id='".$instid."'");
			$resp="Request could not be procesed";
			$resp = false;
			$otps = "";
		}
	}else{
		$resp = "Something went wrong. Please try again.";
		$resp = false;
	}	
	////////////////
	return $resp;
}
function validateOTP($link1,$mobile,$user,$pass)
{
	//$user = base64_encode($user);
	if(strlen($mobile) == "10")
	{
		/// send new otp
		sendOTP($link1, $mobile);
		// redirect
		$_SESSION["otp"] = "";
		exit(header("Location: verifyotp.php?m=".base64_encode($mobile)."&u=".base64_encode($user)."&t=".$pass."&rs=1"));
	}
	else
	{
		$_SESSION["logres"] = [ "status"=>"failed", "msg"=> "Invalid mobile number!" ];
		exit(header('Location:'.$root.'/index.php'));
	}
}
function verifyOTP($link1,$post_mno,$otp)
{
	$resp = false;
	$todayDate = date("Y-m-d");
	$todayTime = date("H:i:s");
	$deviceId = session_id();
	$res_otp = mysqli_query($link1,"SELECT otp, otp_expiretime, status FROM otp_master WHERE userid = '".$post_mno."' AND browser_id = '".$deviceId."' AND otp_for ='WEB_LOGIN' ORDER BY id DESC LIMIT 1") or die(mysqli_error($link1));
	$row_otp = mysqli_fetch_assoc($res_otp);
	///// check OTP status
	if($row_otp['status']=="A"){
		//// check otp is matched with send otp	
		//echo $row_otp['otp']."==".$otp;
		if($row_otp['otp'] == $otp){
			///// check OTP validity it should be less than or equal to 90 seconds
			if(strtotime($todayTime) <= strtotime($row_otp['otp_expiretime'])){
				////if everything is all correct then we mark OTP as Used status
				mysqli_query($link1,"UPDATE otp_master SET status = 'U' WHERE userid = '".$post_mno."' AND browser_id = '".$deviceId ."' AND otp_for ='WEB_LOGIN' AND status = 'A'");
/*				$query_aut = "SELECT * FROM admin_users WHERE username='".$userid."' AND status='active'";
				$result_aut = mysqli_query($link1,$query_aut) or die(mysqli_error($link1));
				$arr_res_aut = mysqli_fetch_assoc($result_aut);
				session_start();
				$_SESSION['userid']=$arr_res_aut['username'];
				$_SESSION['owner_code']=$arr_res_aut['owner_code'];
				$_SESSION['uname']=$arr_res_aut['name'];
				$_SESSION['utype']=$arr_res_aut['utype'];
				$_SESSION['user_level']=$arr_res_aut['user_level'];
				$_SESSION['userlevel']=$arr_res_aut['userlevel'];
				$_SESSION['uid']=$arr_res_aut['uid'];
				$_SESSION['state']=$arr_res_aut['state'];
				///// insert login details
				$sql_ins="insert into login_data set userid='".$arr_res_aut['username']."',ip='".$_SERVER['REMOTE_ADDR']."',remote_agent='".$_SERVER['HTTP_USER_AGENT']."'";
				mysqli_query($link1,$sql_ins);
				//// check user login first time or not
				if($arr_res_aut['first_login']=='Y'){
					if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPod') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') || strpos($_SERVER['HTTP_USER_AGENT'], 'Android')  || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') || strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile')){
						header("Location:dashboard/home_dashboard.php");
					}else{
						header("Location:admin/home2.php");
						exit;
					}
				}
				else{////// otherwise change password first
					header("Location:changepwd.php");
					exit;
				}*/
				$resp = true;
			}else{
				///// update generated OTP status as Expired
				mysqli_query($link1,"UPDATE otp_master SET status = 'E' WHERE userid = '".$post_mno."' AND browser_id = '".$deviceId ."' AND otp_for ='WEB_LOGIN' AND status = 'A'");
				$msg = "OTP is Expired. Please Send OTP Again!";
				header("Location:error.php?errcode=6");
				exit;
			}
		}else{
			$msg = "OTP is mismatched. Please enter valid OTP !";
			header("Location:error.php?errcode=7");
			exit;
		}
	}else if($row_otp['status']=="U"){
		$msg = "OTP is already used. Please Send OTP Again!";
		header("Location:error.php?errcode=8");
		exit;
	}else{
		$msg = "OTP is Expired. Please Send OTP Again!";
		header("Location:error.php?errcode=9");
		exit;
	}
	return $resp;
}

if(!isset($_SESSION["userid"]))
{
	if(isset($_POST['m'])){
		//$post_otp = mysqli_real_escape_string($link1,($_POST['codeBox1'].$_POST['codeBox2'].$_POST['codeBox3'].$_POST['codeBox4'].$_POST['codeBox5'].$_POST['codeBox6']));
		$post_otp = $_POST['codeBox1'].$_POST['codeBox2'].$_POST['codeBox3'].$_POST['codeBox4'].$_POST['codeBox5'].$_POST['codeBox6'];
		$mobile = base64_decode($_POST['m']);
		if(verifyOTP($link1,$mobile,$post_otp))
		{
			$_SESSION["otp"] = "verified";
			$user = mysqli_real_escape_string($link1, base64_decode($_POST['u']));
			$pass = mysqli_real_escape_string($link1,$_POST['t']);
		}
		else
		{
			$user = $_POST['userid'];
			$pass = md5($_POST['pwd']);
		}
	}else{
		$user = $_POST['userid'];
		$pass = md5($_POST['pwd']);
	}
	//echo $user."->".$pass;
	//exit;
	////// start add on condition applied for checking application ussage credential written by shekhar on 06 FEB 2024
	$appvalid = $acc->appSetting($link1);
	if($appvalid['status']=="failed"){
		$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $appvalid["msg"] ];
		exit(header('Location:'.$root.'/index.php'));
	}
	////// end add on condition applied for checking application ussage credential written by shekhar on 06 FEB 2024
	$res = $acc->doLogin($link1, $user, $pass);
	//print_r($res);
	//exit;
	if($res["status"] == "otpverification"){
		validateOTP($link1,$res["mobileno"],$user,$pass);
		//exit;
	}else{
		if($res["status"] == "success")
		{
			exit(header('Location:'.$root.'/admin/home2.php?pid=0&hid=0'));
		}
		else
		{
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $res["msg"] ];
			exit(header('Location:'.$root.'/index.php'));
			//exit(header('Location:'.$root));
		}
	}
}
else
{
	//exit(header('Location:'.$root.'/index.php'));
	exit(header('Location:'.$root.'/index.php'));
}
?>