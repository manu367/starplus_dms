<?php
class Accounts{

    // return false | seconds
    function waitingTime($link1){

        $resp = false;
        $sql = "SELECT result, timestamp FROM login_attemps WHERE user_ip = '".$_SERVER['REMOTE_ADDR']."' ORDER BY sr_no DESC LIMIT 3";
        $res = mysqli_query($link1, $sql);
        if($res){
            if(mysqli_num_rows($res) == 3){

                $res_fail_c = 0;
                $tt = [];
                while($row = mysqli_fetch_assoc($res)){
                    if($row["result"] == 0){
                        $res_fail_c++;
                    }
                    $tt[] = $row["timestamp"];
                }
				
                if($res_fail_c == 3){

                    $diff = $tt[0] - $tt[2];
                    if($diff < 300){ // 300 sec (5 min)
                        //$now = time();
                        $waiting_time = ($tt[0] + 300) - time(); // sec remains to allow login
                        if($waiting_time > 1){
                            $resp = $waiting_time;
                        }
                        else{
                            $resp = 1;
                        }
                    }
                    else{
                        $resp = 1;
                    }
                }
                else{
                    $resp = 1;
                }
            }
            else{
                $resp = 1;
            }
        }
        return $resp;
    }

    // return: array
    function securityCheck($link1){

        $resp = [ "status"=>"failed", "msg"=>"" ];
        /// user agent validation
        if($_SERVER['HTTP_USER_AGENT']){

            $wt = $this->waitingTime($link1);
            //exit(var_dump($wt));
            if($wt == 1){
                $resp = [ "status"=>"success", "msg"=>"All good!" ];
            }
            elseif($wt > 1){
                $resp["msg"] = "Please try again after ".$wt." sec!";
            }
            else{
                $resp["msg"] = "Error occurred : Try again later! [ref: SLA]";
            }
        }
        else{
            $resp["msg"] = "Suspecious Browser!";
        }
        return $resp;
    }

    // return: true | false
    function isLoggedin($link1){

        $resp = false;
        //$sql = "SELECT * FROM login_active WHERE session='".session_id()."'";
        $sql = "SELECT * FROM login_active la JOIN login_history lh ON (la.history_ref = lh.sr_no) WHERE la.session='".session_id()."' LIMIT 1";
        $res = mysqli_query($link1, $sql);
        if($res){
            if(mysqli_num_rows($res) > 0){

                $row = mysqli_fetch_assoc($res);
                if($_SERVER['HTTP_USER_AGENT'] == $row["user_agent"] && $_SERVER['REMOTE_ADDR'] == $row["login_ip"]){

                    if(isset($_SESSION["userid"]) && strlen($_SESSION["userid"]) > 0){
                        $resp = true;
                    }
                }
                else{
                    $_SESSION["logres"] = [ "status"=>"failed", "msg"=>"Unauthorized Access!" ];
                }                
            }
            elseif(isset($_SESSION["userid"]) && strlen($_SESSION["userid"]) > 0){
                
                if(isset($_COOKIE[session_name()])){
                    unset($_COOKIE[session_name()]); 
                    setcookie(session_name(), null, -1, '/');
                }
                unset($_SESSION);
                session_destroy();
                session_start();
                $_SESSION["logres"] = [ "status"=>"failed", "msg"=>"Session Expired!" ];
            }else{
				$resp = true;
			}
        }else{
			$resp = true;
		}
        return $resp;
    }

    // return: true | false
    function addSession($link1, $ref){

        $resp = false;
        $sql = "INSERT INTO login_active
        SET
        session = '".session_id()."',
        started_at = '".time()."',
        history_ref = '".$ref."'";
        $res = mysqli_query($link1, $sql);
        if($res){
            if(mysqli_affected_rows($link1) > 0){
                $resp = true;
            }
        }
        return $resp;
    }

    // return: true | false
    function removeSession($link1, $sid){

        $resp = false;
        $sql = "DELETE FROM login_active
        WHERE
        session = '".$sid."'";
        $res = mysqli_query($link1, $sql);
        if($res){
            if(mysqli_affected_rows($link1) > 0){
                $resp = true;
            }
        }
        return $resp;
    }

    // return: true | false
    function recordAttamp($link1, $result){

        $resp = false;
        $sql = "INSERT INTO login_attemps
        SET
        user_agent = '".$_SERVER['HTTP_USER_AGENT']."',
        user_ip = '".$_SERVER['REMOTE_ADDR']."',
        result = '".$result."',
        timestamp = '".time()."'";
        $res = mysqli_query($link1, $sql);
        if($res){
            if(mysqli_affected_rows($link1) > 0){
                $resp = true;
            }
        }
        return $resp;
    }

    // return: refid | false
    function recordLogin($link1, $userid){

        $resp = false;
        $sql = "INSERT INTO login_history
        SET
        user_id = '".$userid."',
        session = '".session_id()."',
        login_time = '".date("Y-m-d H:i:s")."',
        login_ip = '".$_SERVER['REMOTE_ADDR']."',
        user_agent = '".$_SERVER['HTTP_USER_AGENT']."'";
        $res = mysqli_query($link1, $sql);
        if($res){
            if(mysqli_affected_rows($link1) > 0){
                $resp = mysqli_insert_id($link1);
            }
        }
        return $resp;
    }

    // return: array
    function doLogin($link1, $user, $pass){

        $resp = [ "status"=>"failed", "msg"=>"" ];
        // security check
        $security_check = $this->securityCheck($link1);
        if($security_check["status"] == "success"){
           
            /// admin & user verification
            $sql = "SELECT * FROM admin_users WHERE username LIKE '".$user."' AND status = 'active' LIMIT 1";
            $res = mysqli_query($link1, $sql);
            if($res){				
                if(mysqli_num_rows($res) > 0){
				
					$data = mysqli_fetch_assoc($res);
					$twostepverify = $data['additional_otp_login'];
					/// check if there is 2 step verification with OTP
					if($twostepverify == "Y" && $_SESSION["otp"] != "verified"){
						if($data['phone']!="" && strlen($data['phone']) == "10"){
							$resp = [ "status"=>"otpverification", "mobileno"=>$data['phone'], "type" => "admin" ];
						}else{
							$resp["msg"] = "Invalid Mobile no.";
						}
						$rA = true;
					}else{
                    if(md5($data["password"]) == $pass && $pass!=''){
						$rA = $this->recordAttamp($link1, 1);
						if($rA){
                            session_regenerate_id();
                            $ref = $this->recordLogin($link1, $data["username"]);
                            if($ref){
                                if($this->addSession($link1, $ref)){

                                    //if($data["type"] == "Admin"){
										$_SESSION['userid']=$data['username'];
										$_SESSION['owner_code']=$data['owner_code'];
										$_SESSION['uname']=$data['name'];
										$_SESSION['utype']=$data['utype'];
										$_SESSION['user_level']=$data['user_level'];
										$_SESSION['userlevel']=$data['userlevel'];
										$_SESSION['userdesig']=$data['designationid'];
										$_SESSION['userdepart']=$data['department'];
										$_SESSION['uid']=$data['uid'];
										$_SESSION['state']=$data['state'];
 
                                        $resp = [ "status"=>"success", "msg"=>"login success", "type" => "admin" ];
                                    //}
                                    //else{
                                       // $resp["msg"] = "Error occurred : Unknown user!";
                                    //}
                                }
                                else{
                                    $resp["msg"] = "Error occurred : Try again later! [ref: ILACT]";
                                }                            
                            }
                            else{
                                $resp["msg"] = "Error occurred : Try again later! [ref: ILH]";
                            }
                        }
						else{
                            $resp["msg"] = "Error occurred : Try again later! [ref: ILA]";
                        }					
					}
					else{
						$resp["msg"] = "Invalid Username/Password";
					}
					}				
				}
				else{
					$resp["msg"] = "Invalid Username/Password";
				}
            }
            else{
                $resp["msg"] = "Error occurred : Try again later! [ref: SAU]";
            }

            if(!$rA){
               $s = $this->recordAttamp($link1, 0);
            }
        }
        else{
            $resp["msg"] = $security_check["msg"];
        }
        return $resp;
    }

    // return: true | false
    function doLogout($link1, $uid){

        $resp = false;
        if($this->removeSession($link1, session_id())){
            
            if(isset($_COOKIE[session_name()])){
                unset($_COOKIE[session_name()]); 
                setcookie(session_name(), null, -1, '/');
            }
            unset($_SESSION);
            session_destroy();
            $resp = true;
        }
        return $resp;
    }
	//// function to check user limit, expiry date and assigned module to the application written by shekhar on 06 feb 2024
	function appSetting($link1){
		$today = date("Y-m-d");
		$resp = [ "status"=>"failed", "msg"=>"" ];
		$sql = "SELECT * FROM app_config WHERE 1";
		$res = mysqli_query($link1,$sql);
		$row = mysqli_fetch_array($res);
        if(strtotime($row['expiry_date']) < strtotime($today) || $row['status'] != '1' ){
			$resp["msg"] = "Your Services has been exprired or deactivated, please contact to your Application Admin.";
		}else{
			$resp = [ "status"=>"success", "msg"=>"checked app success", "type" => "admin" ];
		}
		return $resp;
	}
}
?>