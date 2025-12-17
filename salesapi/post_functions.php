<?php
include('constant.php');
class POST_Functions{       
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
	///// capture user activity
	public function updateUserActivity($userid,$taskname,$taskaction,$latitude,$longitude,$refno,$trackadddress=0,$trackkm=0){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		return $res = mysqli_query($this->link,"INSERT INTO user_track SET userid='".$userid."', task_name='".$taskname."', task_action='".$taskaction."', ref_no='".$refno."', latitude='".$latitude."', longitude='".$longitude."', address='".$trackadddress."',travel_km='".$trackkm."', remote_address='".$_SERVER['REMOTE_ADDR']."',remote_agent='".$_SERVER['HTTP_USER_AGENT']."' , entry_date='".$todayDate."'");
	}
	//// update user profile
	public function updateUserProfile($uid,$updarray){
		///// update data string
		$upd_str = "";
		foreach($updarray as $key => $val){
			if($upd_str){
				$upd_str .= ",".$key." = '".$val."'";
			}else{
				$upd_str = "".$key." = '".$val."'";
			}
		}
		///// update user profile details
		$res1 = mysqli_query($this->link,"UPDATE admin_users SET ".$upd_str." WHERE phone = '".$uid."'");
		if(!$res1){
			$err_msg = "ER1 ".mysqli_error($this->link);
			return "0~".$err_msg;
		}else{
			return "1";
		}
	}
	////// update user attendance
	public function updateUserAttendance($userId,$userCode,$inLatitude,$inLongitude,$inDatetime,$inStatus,$inAddress,$inImage,$inImageName,$outLatitude,$outLongitude,$outDatetime,$outStatus,$outAddress,$outImage,$outImageName){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		///// check user is already punched attendance or not
		$user_attend = mysqli_query($this->link,"SELECT id,status_in,status_out FROM user_attendence WHERE user_id='".$userCode."' AND insert_date='".$todayDate."'");
		if(mysqli_num_rows($user_attend)==0 && $inStatus!=''){
			/// check attendance image directory
			///check directory
			$dirct = "attendanceimg/".date("Y-m");
			if (!is_dir($dirct)) {
				mkdir($dirct, 0777, 'R');
			}
			$base = $inImage;
			// Get file name posted from Android App
			$filename = $inImageName;
			// Decode Image//
			if($base!=null){
				$binary=base64_decode($base);
			}
			else{
				$binary=null;
			}
    		header('Content-Type: bitmap; charset=utf-8');
			$file_nm = $dirct.'/'.$filename;
    		// Images will be saved under 'www/imgupload/uplodedimages' folder   
			$file = fopen($file_nm, 'wb');
    		// Create File
			if($binary!=null){
				fwrite($file, $binary);
				$res1 = mysqli_query($this->link,"INSERT INTO user_attendence SET longitude_in='".$inLongitude."',latitude_in='".$inLatitude."',longitude_out='".$outLongitude."',latitude_out='".$outLatitude."',user_id='".$userCode."',status_in='".$inStatus."',in_datetime='".$inDatetime."',status_out='".$outStatus."',out_datetime='".$outDatetime."',address_in='".$inAddress."',address_out='".$outAddress."',insert_date='".$todayDate."',Image_in='".$inImageName."',Image_out='".$outImageName."'");
				if(!$res1){
					$err_msg = "ER1 ".mysqli_error($this->link);
					return "0~".$err_msg;
				}else{
					return "1";
				}
			}else{
				$err_msg = "In Time image not captured";
				return "0~".$err_msg;
			}
			fclose($file);
		////// out attendance punch	
		}else if(mysqli_num_rows($user_attend)>0){
			$row_attend = mysqli_fetch_array($user_attend);
			if($row_attend["status_in"]=="IN" && $inStatus!=''){
				$err_msg = "User already logged in";
				return "0~".$err_msg;
			}else if($row_attend["status_out"]==""){
				/// check attendance image directory
				///check directory
				$dirct = "attendanceimg/".date("Y-m");
				if (!is_dir($dirct)) {
					mkdir($dirct, 0777, 'R');
				}
				$base = $outImage;
				// Get file name posted from Android App
				$filename = $outImageName;
				// Decode Image//
				if($base!=null){
					$binary=base64_decode($base);
				}
				else{
					$binary=null;
				}
				header('Content-Type: bitmap; charset=utf-8');
				$file_nm = $dirct.'/'.$filename;
				// Images will be saved under 'www/imgupload/uplodedimages' folder   
				$file = fopen($file_nm, 'wb');
				// Create File
				if($binary!=null){
					fwrite($file, $binary);
					///// update user profile details
					$res1 = mysqli_query($this->link,"UPDATE user_attendence SET status_out='".$outStatus."',out_datetime='".$outDatetime."',address_out='".$outAddress."',longitude_out='".$outLongitude."',latitude_out='".$outLatitude."',Image_out='".$outImageName."' WHERE id='".$row_attend['id']."'");
					if(!$res1){
						$err_msg = "ER1 ".mysqli_error($this->link);
						return "0~".$err_msg;
					}else{
						return "1";
					}
				}else{
					$err_msg = "Out Time image not captured";
					return "0~".$err_msg;
				}
				fclose($file);
			}else{
				$err_msg = "User already logged out";
				return "0~".$err_msg;
			}
		}else if(mysqli_num_rows($user_attend)>0 && $row_attend["status_in"]!="" && $row_attend["status_out"]!=""){
			$err_msg = "User already marked their attendance";
			return "0~".$err_msg;
		}else{
			$err_msg = "Please login first";
			return "0~".$err_msg;
		}
	}
	////// update user travel plan
	public function updateUserTravel($userId,$userCode,$inLatitude,$inLongitude,$inDatetime,$inStatus,$inAddress,$inImage,$inImageName,$outLatitude,$outLongitude,$outDatetime,$outStatus,$outAddress,$outImage,$outImageName){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		///// check user is already punched attendance or not
		$user_attend = mysqli_query($this->link,"SELECT id,status_in,status_out FROM user_travel_plan WHERE user_id='".$userCode."' AND insert_date='".$todayDate."'");
		if(mysqli_num_rows($user_attend)==0 && $inStatus!=''){
			/// check attendance image directory
			///check directory
			$dirct = "travelimg/".date("Y-m");
			if (!is_dir($dirct)) {
				mkdir($dirct, 0777, 'R');
			}
			$base = $inImage;
			// Get file name posted from Android App
			$filename = $inImageName;
			// Decode Image//
			if($base!=null){
				$binary=base64_decode($base);
			}
			else{
				$binary=null;
			}
    		header('Content-Type: bitmap; charset=utf-8');
			$file_nm = $dirct.'/'.$filename;
    		// Images will be saved under 'www/imgupload/uplodedimages' folder   
			$file = fopen($file_nm, 'wb');
    		// Create File
			if($binary!=null){
				fwrite($file, $binary);
				$res1 = mysqli_query($this->link,"INSERT INTO user_travel_plan SET longitude_in='".$inLongitude."',latitude_in='".$inLatitude."',longitude_out='".$outLongitude."',latitude_out='".$outLatitude."',user_id='".$userCode."',status_in='".$inStatus."',in_datetime='".$inDatetime."',status_out='".$outStatus."',out_datetime='".$outDatetime."',address_in='".$inAddress."',address_out='".$outAddress."',insert_date='".$todayDate."',Image_in='".$inImageName."',Image_out='".$outImageName."'");
				if(!$res1){
					$err_msg = "ER1 ".mysqli_error($this->link);
					return "0~".$err_msg;
				}else{
					return "1";
				}
			}else{
				$err_msg = "In Time image not captured";
				return "0~".$err_msg;
			}
			fclose($file);
		////// out attendance punch	
		}else if(mysqli_num_rows($user_attend)>0){
			$row_attend = mysqli_fetch_array($user_attend);
			if($row_attend["status_in"]=="IN" && $inStatus!=''){
				$err_msg = "User already logged in";
				return "0~".$err_msg;
			}else if($row_attend["status_out"]==""){
				/// check attendance image directory
				///check directory
				$dirct = "travelimg/".date("Y-m");
				if (!is_dir($dirct)) {
					mkdir($dirct, 0777, 'R');
				}
				$base = $outImage;
				// Get file name posted from Android App
				$filename = $outImageName;
				// Decode Image//
				if($base!=null){
					$binary=base64_decode($base);
				}
				else{
					$binary=null;
				}
				header('Content-Type: bitmap; charset=utf-8');
				$file_nm = $dirct.'/'.$filename;
				// Images will be saved under 'www/imgupload/uplodedimages' folder   
				$file = fopen($file_nm, 'wb');
				// Create File
				if($binary!=null){
					fwrite($file, $binary);
					///// update user profile details
					$res1 = mysqli_query($this->link,"UPDATE user_travel_plan SET status_out='".$outStatus."',out_datetime='".$outDatetime."',address_out='".$outAddress."',longitude_out='".$outLongitude."',latitude_out='".$outLatitude."',Image_out='".$outImageName."' WHERE id='".$row_attend['id']."'");
					if(!$res1){
						$err_msg = "ER1 ".mysqli_error($this->link);
						return "0~".$err_msg;
					}else{
						return "1";
					}
				}else{
					$err_msg = "Out Time image not captured";
					return "0~".$err_msg;
				}
				fclose($file);
			}else{
				$err_msg = "User already logged out";
				return "0~".$err_msg;
			}
		}else if(mysqli_num_rows($user_attend)>0 && $row_attend["status_in"]!="" && $row_attend["status_out"]!=""){
			$err_msg = "User already marked their travel attendance";
			return "0~".$err_msg;
		}else{
			$err_msg = "Please login first";
			return "0~".$err_msg;
		}
	}
	/////// Existing dealer visit post
	public function updateDealerVisitOld($userId,$userCode,$taskId,$dealerCode,$visitAddress,$visitRemark,$latitude,$longitude,$trackaddress){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$flag = true;
		///// Insert Master Data
		$query1 = "INSERT INTO dealer_visit set userid='".$userCode."',party_code='".$dealerCode."', remark='".$visitRemark."',visit_date='".$todayDate."',visit_city='".$visitAddress."',dealer_type='Old',address='".$trackaddress."',latitude='".$latitude."',longitude='".$longitude."',pjp_id='".$taskId."',ip='".$_SERVER['REMOTE_ADDR']."'";
		$result1 = mysqli_query($this->link,$query1);
		//// check if query is not executed
		if (!$result1) {
			 $flag = false;
			 $err_msg = "ER1: " . mysqli_error($this->link) . ".";
		}
		///// if task id is not blank
		if($taskId){
			$result2 = mysqli_query($this->link,"UPDATE pjp_data SET task_acheive=task_acheive+1 WHERE id='".$taskId."'");
			//// check if query is not executed
			if (!$result2) {
				 $flag = false;
				 $err_msg = "ER2: " . mysqli_error($this->link) . ".";
			}
	   	}
	   	///// check if no error in execution
	   	if($flag){
			return "1";	
		}else{
			return "0~".$err_msg;
		}
	}
	//////collection post
	public function updateCollection($userId,$userCode,$dealerCode,$amount,$paymentMode,$transactionNo,$transactionDate,$docEncodeStr,$docName,$taskId,$latitude,$longitude,$trackaddress){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		if($docEncodeStr){
			///check directory
			$dirct = "collectionimg/".date("Y-m");
			if (!is_dir($dirct)) {
				mkdir($dirct, 0777, 'R');
			}
			$base = $docEncodeStr;
			// Get file name posted from Android App
			$filename = $docName;
			// Decode Image//
			if($base!=null){
				$binary=base64_decode($base);
			}
			else{
				$binary=null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_nm = $dirct.'/'.$filename;
			// Images will be saved under 'www/imgupload/uplodedimages' folder   
			$file = fopen($file_nm, 'wb');
			// Create File
			fwrite($file, $binary);
			fclose($file);
		}
		if($amount!="" && $dealerCode!=""){
			$res1 = mysqli_query($this->link,"INSERT INTO party_collection SET user_id='".$userCode."',party_code='".$dealerCode."',amount='".$amount."',pay_mode='".$paymentMode."',transaction_no='".$transactionNo."',transaction_date='".$transactionDate."',doc_name='".$docName."',pjp_id='".$taskId."',entry_by='".$userCode."',entry_date='".$todayDate." ".$todayTime."',address='".$trackaddress."',latitude='".$latitude."',longitude='".$longitude."'");
			//// check if query is not executed
			if (!$res1) {
				 $flag = false;
				 $err_msg = "ER1: " . mysqli_error($this->link) . ".";
			}
			if($taskId){
				$res2 = mysqli_query($this->link,"UPDATE pjp_data SET task_acheive=task_acheive+1 WHERE id='".$taskId."'");
				//// check if query is not executed
				if (!$res2) {
					 $flag = false;
					 $err_msg = "ER2: " . mysqli_error($this->link) . ".";
				}
			}
			return "1";
		}else{
			$err_msg = "Something went wrong";
			return "0~".$err_msg;
		}
	}
	//////Feedback post
	public function updateFeedback($userId,$userCode,$feedbackFor,$feedbackType,$feedbackTitle,$feedbackMsg,$feedbackRate,$taskId,$latitude,$longitude,$docEncodeStr,$docName,$trackaddress,$contactNo,$partyName){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$tody = $this->dt_format->format('Ymd');
		$flag = true;
		///////// get next system generated no.
		$query_code = "SELECT MAX(id) AS qc FROM query_master";
    	$result_code = mysqli_query($this->link,$query_code)or die("error2".mysqli_error($this->link));
    	$arr_result2 = mysqli_fetch_array($result_code);
    	$code_id = $arr_result2[0];
    	$pad = str_pad(++$code_id,3,"0",STR_PAD_LEFT);
		///// make system ref no.
    	$sysrefno="FB/".$tody."/".$pad;
		///check if any attachment is sent
		if($docEncodeStr){
			///check directory
			$dirct = "feedbackimg/".date("Y-m");
			if (!is_dir($dirct)) {
				mkdir($dirct, 0777, 'R');
			}
			$base = $docEncodeStr;
			// Get file name posted from Android App
			$filename = $docName;
			// Decode Image//
			if($base!=null){
				$binary=base64_decode($base);
			}
			else{
				$binary=null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_nm = $dirct.'/'.$filename;
			// Images will be saved under 'www/imgupload/uplodedimages' folder   
			$file = fopen($file_nm, 'wb');
			// Create File
			fwrite($file, $binary);
			fclose($file);
		}
		$res1 = mysqli_query($this->link,"INSERT INTO query_master SET query='".$sysrefno."',temp='".$code_id."',module='".$feedbackFor."',problem='".$feedbackType."',contact_no='".$userId."',request='".ucwords($feedbackTitle)."~".$feedbackMsg."~".$feedbackRate."',party_contact='".$contactNo."',party_name='".ucwords($partyName)."',attachment='".$filename."',status='pending',entry_date='".$todayDate."',entry_time='".$todayTime."',entry_by='".$userCode."',address='".$trackaddress."',latitude='".$latitude."',longitude='".$longitude."',pjp_id='".$taskId."'");
		//// check if query is not executed
		if (!$res1) {
			 $flag = false;
			 $err_msg = "ER1: " . mysqli_error($this->link) . ".";
		}
		if($taskId){
			$res2 = mysqli_query($this->link,"UPDATE pjp_data SET task_acheive=task_acheive+1 WHERE id='".$taskId."'");
			//// check if query is not executed
			if (!$res2) {
				 $flag = false;
				 $err_msg = "ER2: " . mysqli_error($this->link) . ".";
			}
		}
		if($flag){
			return "1~".$sysrefno;
		}else{
			//$err_msg = "Something went wrong";
			return "0~".$err_msg;
		}
	}
	///// add TADA by user
	public function updateTaDa($userId,$userCode,$foodexpAmt,$foodexpEncodestr,$foodexpDocname,$courierexpAmt,$courierexpEncodestr,$courierexpDocname,$localexpAmt,$localexpEncodestr,$localexpDocname,$mobileexpAmt,$mobileexpEncodestr,$mobileexpDocname,$otherexpAmt,$otherexpEncodestr,$otherexpDocname,$latitude,$longitude,$expDate,$refexpno){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$tody = $this->dt_format->format('Ymd');
		$flag = true;
		// make expense system generated ref no.
		$sql = "SELECT MAX(seq_no) AS qa FROM ta_da WHERE userid='".$userCode."'";
		$res = mysqli_query($this->link,$sql);
		$row = mysqli_fetch_assoc($res);
		$code_id = $row["qa"];
		$next_seq = $row["qa"] + 1;
		/// make 3 digit padding
		$pad = str_pad(++$code_id,4,"0",STR_PAD_LEFT);
		//// make logic of ref no.
		$refno = "EXP/".$tody."/".strtoupper($userCode)."/".$pad;
		/// calculate total exp amount
		$total_exp = $foodexpAmt + $courierexpAmt + $localexpAmt + $mobileexpAmt + $otherexpAmt;
		/////check if any attachment is sent
		if($foodexpEncodestr!='' || $courierexpEncodestr!='' || $localexpEncodestr!='' || $mobileexpEncodestr!='' || $otherexpEncodestr!=''){
			///check directory
			$dirct = "tadaimg/".date("Y-m");
			if (!is_dir($dirct)) {
				mkdir($dirct, 0777, 'R');
			}
			$base_food = $foodexpEncodestr;
			$base_courier = $courierexpEncodestr;
			$base_local = $localexpEncodestr;
			$base_mobile = $mobileexpEncodestr;
			$base_other = $otherexpEncodestr;
			// Get file name posted from Android App
			$filename_food = $foodexpDocname;
			$filename_courier = $courierexpDocname;
			$filename_local = $localexpDocname;
			$filename_mobile = $mobileexpDocname;
			$filename_other = $otherexpDocname;
			// Decode Food Image//
			if($base_food!=null){
				$binary_food = base64_decode($base_food);
			}
			else{
				$binary_food = null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_food = $dirct.'/'.$filename_food;
			// Images will be saved under 'tadaimg/Y-m/' folder   
			$filefood = fopen($file_food, 'wb');
			// Create File
			fwrite($filefood, $binary_food);
			fclose($filefood);
			// Decode Courier Image//
			if($base_courier!=null){
				$binary_courier = base64_decode($base_courier);
			}
			else{
				$binary_courier = null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_courier = $dirct.'/'.$filename_courier;
			// Images will be saved under 'tadaimg/Y-m/' folder   
			$filecourier = fopen($file_courier, 'wb');
			// Create File
			fwrite($filecourier, $binary_courier);
			fclose($filecourier);
			// Decode Local Image//
			if($base_local!=null){
				$binary_local = base64_decode($base_local);
			}
			else{
				$binary_local = null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_local = $dirct.'/'.$filename_local;
			// Images will be saved under 'tadaimg/Y-m/' folder   
			$filelocal = fopen($file_local, 'wb');
			// Create File
			fwrite($filelocal, $binary_local);
			fclose($filelocal);
			// Decode Mobile Image//
			if($base_mobile!=null){
				$binary_mobile = base64_decode($base_mobile);
			}
			else{
				$binary_mobile = null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_mobile = $dirct.'/'.$filename_mobile;
			// Images will be saved under 'tadaimg/Y-m/' folder   
			$filemobile = fopen($file_mobile, 'wb');
			// Create File
			fwrite($filemobile, $binary_mobile);
			fclose($filemobile);
			// Decode Other Image//
			if($base_other!=null){
				$binary_other = base64_decode($base_other);
			}
			else{
				$binary_other = null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_other = $dirct.'/'.$filename_other;
			// Images will be saved under 'tadaimg/Y-m/' folder   
			$fileother = fopen($file_other, 'wb');
			// Create File
			fwrite($fileother, $binary_other);
			fclose($fileother);
		}
		///////insert
		$query = "INSERT INTO ta_da set userid='".$userCode."',system_ref_no='".$refno."',seq_no='".$next_seq."',any_ref_no='".$refexpno."',food_exp='".$foodexpAmt."',food_exp_img='".$foodexpDocname."',courier_exp='".$courierexpAmt."',courier_exp_img='".$courierexpDocname."',localconv_exp='".$localexpAmt."',localconv_exp_img='".$localexpDocname."',mobile_exp='".$mobileexpAmt."',mobile_exp_img='".$mobileexpDocname."',other_exp='".$otherexpAmt."',other_exp_img='".$otherexpDocname."',expense_date='".$expDate."',entry_date='".$todayDate."',entry_time='".$todayTime."',entry_by='app_user',remark='app_user',total_amt='".$total_exp."',status='Pending', latitude = '".$latitude."', longitude = '".$longitude."', address = ''";
		$result = mysqli_query($this->link,$query);
		//// check if query is not executed
		if (!$result) {
			 $flag = false;
			 $err_msg = "ER1: " . mysqli_error($this->link) . ".";
		}
		if($flag){
			return "1~".$refno;
		}else{
			return "0~".$err_msg;
		}
	}
	///// add TADA New by user in this travel mode and hotel expenses was addedd
	public function updateTaDaNew($userId,$userCode,$foodexpAmt,$foodexpEncodestr,$foodexpDocname,$courierexpAmt,$courierexpEncodestr,$courierexpDocname,$localexpAmt,$localexpEncodestr,$localexpDocname,$mobileexpAmt,$mobileexpEncodestr,$mobileexpDocname,$otherexpAmt,$otherexpEncodestr,$otherexpDocname,$latitude,$longitude,$expDate,$refexpno,$localexpTravelmode,$hotelexpAmt,$hotelexpCity,$hotelexpEncodestr,$hotelexpDocname,$expRemark,$fromLoc,$toLoc,$travelType,$purpose){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$tody = $this->dt_format->format('Ymd');
		$flag = true;
		// make expense system generated ref no.
		$sql = "SELECT MAX(seq_no) AS qa FROM ta_da WHERE userid='".$userCode."'";
		$res = mysqli_query($this->link,$sql);
		$row = mysqli_fetch_assoc($res);
		$code_id = $row["qa"];
		$next_seq = $row["qa"] + 1;
		/// make 3 digit padding
		$pad = str_pad(++$code_id,4,"0",STR_PAD_LEFT);
		//// make logic of ref no.
		$refno = "EXP/".$tody."/".strtoupper($userCode)."/".$pad;
		/// calculate total exp amount
		$total_exp = $foodexpAmt + $courierexpAmt + $localexpAmt + $mobileexpAmt + $otherexpAmt + $hotelexpAmt;
		/////check if any attachment is sent
		if($foodexpEncodestr!='' || $courierexpEncodestr!='' || $localexpEncodestr!='' || $mobileexpEncodestr!='' || $otherexpEncodestr!='' || $hotelexpEncodestr!=''){
			///check directory
			$dirct = "tadaimg/".date("Y-m");
			if (!is_dir($dirct)) {
				mkdir($dirct, 0777, 'R');
			}
			$base_food = $foodexpEncodestr;
			$base_courier = $courierexpEncodestr;
			$base_local = $localexpEncodestr;
			$base_mobile = $mobileexpEncodestr;
			$base_other = $otherexpEncodestr;
			$base_hotel = $hotelexpEncodestr;
			// Get file name posted from Android App
			$filename_food = $foodexpDocname;
			$filename_courier = $courierexpDocname;
			$filename_local = $localexpDocname;
			$filename_mobile = $mobileexpDocname;
			$filename_other = $otherexpDocname;
			$filename_hotel = $hotelexpDocname;
			// Decode Food Image//
			if($base_food!=null){
				$binary_food = base64_decode($base_food);
			}
			else{
				$binary_food = null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_food = $dirct.'/'.$filename_food;
			// Images will be saved under 'tadaimg/Y-m/' folder   
			$filefood = fopen($file_food, 'wb');
			// Create File
			fwrite($filefood, $binary_food);
			fclose($filefood);
			// Decode Courier Image//
			if($base_courier!=null){
				$binary_courier = base64_decode($base_courier);
			}
			else{
				$binary_courier = null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_courier = $dirct.'/'.$filename_courier;
			// Images will be saved under 'tadaimg/Y-m/' folder   
			$filecourier = fopen($file_courier, 'wb');
			// Create File
			fwrite($filecourier, $binary_courier);
			fclose($filecourier);
			// Decode Local Image//
			if($base_local!=null){
				$binary_local = base64_decode($base_local);
			}
			else{
				$binary_local = null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_local = $dirct.'/'.$filename_local;
			// Images will be saved under 'tadaimg/Y-m/' folder   
			$filelocal = fopen($file_local, 'wb');
			// Create File
			fwrite($filelocal, $binary_local);
			fclose($filelocal);
			// Decode Mobile Image//
			if($base_mobile!=null){
				$binary_mobile = base64_decode($base_mobile);
			}
			else{
				$binary_mobile = null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_mobile = $dirct.'/'.$filename_mobile;
			// Images will be saved under 'tadaimg/Y-m/' folder   
			$filemobile = fopen($file_mobile, 'wb');
			// Create File
			fwrite($filemobile, $binary_mobile);
			fclose($filemobile);
			// Decode Other Image//
			if($base_other!=null){
				$binary_other = base64_decode($base_other);
			}
			else{
				$binary_other = null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_other = $dirct.'/'.$filename_other;
			// Images will be saved under 'tadaimg/Y-m/' folder   
			$fileother = fopen($file_other, 'wb');
			// Create File
			fwrite($fileother, $binary_other);
			fclose($fileother);
			// Decode Hotel Image//
			if($base_hotel!=null){
				$binary_hotel = base64_decode($base_hotel);
			}
			else{
				$binary_hotel = null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_hotel = $dirct.'/'.$filename_hotel;
			// Images will be saved under 'tadaimg/Y-m/' folder   
			$filehotel = fopen($file_hotel, 'wb');
			// Create File
			fwrite($filehotel, $binary_hotel);
			fclose($filehotel);
		}
		///////insert
		$query = "INSERT INTO ta_da set userid='".$userCode."',system_ref_no='".$refno."',seq_no='".$next_seq."',any_ref_no='".$refexpno."',food_exp='".$foodexpAmt."',food_exp_img='".$foodexpDocname."',courier_exp='".$courierexpAmt."',courier_exp_img='".$courierexpDocname."',localconv_exp='".$localexpAmt."',localconv_exp_img='".$localexpDocname."',travel_mode='".$localexpTravelmode."',mobile_exp='".$mobileexpAmt."',mobile_exp_img='".$mobileexpDocname."',other_exp='".$otherexpAmt."',other_exp_img='".$otherexpDocname."',hotel_exp='".$hotelexpAmt."',hotel_exp_img='".$hotelexpDocname."',hotel_city='".$hotelexpCity."',expense_date='".$expDate."',entry_date='".$todayDate."',entry_time='".$todayTime."',entry_by='app_user',remark='".$expRemark."',total_amt='".$total_exp."',status='Pending', latitude = '".$latitude."', longitude = '".$longitude."', address = '', from_location='".$fromLoc."', to_location='".$toLoc."', travel_type='".$travelType."', purpose='".$purpose."'";
		$result = mysqli_query($this->link,$query);
		//// check if query is not executed
		if (!$result) {
			 $flag = false;
			 $err_msg = "ER1: " . mysqli_error($this->link) . ".";
		}
		if($flag){
			return "1~".$refno;
		}else{
			return "0~".$err_msg;
		}
	}
	////// add new lead
	public function updateLead($userId,$userCode,$partyName,$partyAddress,$partyState,$partyCity,$partyContact,$partyEmail,$leadPurpose,$leadSource,$latitude,$longitude,$docEncodeStr,$docName){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$tody = $this->dt_format->format('Ymd');
		$priority = "Normal";
		$flag = true;
		//////// get max lead id
		$res_sysref = mysqli_query($this->link,"SELECT MAX(lid) AS cnt FROM sf_lead_master ORDER BY lid DESC");
		$row_sysref = mysqli_fetch_assoc($res_sysref);
		$next_no = $row_sysref['cnt']+1;
		$pad = str_pad($next_no,3,"0",STR_PAD_LEFT);  
		$reference = "LD".$pad;
		///check if any attachment is sent
		if($docEncodeStr){
			///check directory
			$dirct = "leadimg/".date("Y-m");
			if (!is_dir($dirct)) {
				mkdir($dirct, 0777, 'R');
			}
			$base = $docEncodeStr;
			// Get file name posted from Android App
			$filename = $docName;
			// Decode Image//
			if($base!=null){
				$binary=base64_decode($base);
			}
			else{
				$binary=null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_nm = $dirct.'/'.$filename;
			// Images will be saved under 'www/imgupload/uplodedimages' folder   
			$file = fopen($file_nm, 'wb');
			// Create File
			fwrite($file, $binary);
			fclose($file);
		}
		///// insert in lead master
		$res2 =	mysqli_query($this->link,"INSERT INTO sf_lead_master SET partyid='".$partyName."', party_address='".$partyAddress."', intial_remark='".$leadPurpose."', priority='".$priority."', vcard_url='".$filename."', reference='".$reference."', type='Lead', category='', tdate='".$todayDate."',create_time='".$todayTime."', status='7', ip='".$_SERVER['REMOTE_ADDR']."', sales_executive='".$userCode."', dept_id='".$dept."', party_state='".$partyState."', party_city='".$partyCity."',party_contact='".$partyContact."',party_email='".$partyEmail."', party_country='India', lead_source='".$leadSource."', create_location='', create_by='".$userCode."'");
		//// check if query is not executed
		if (!$res2) {
			 $flag = false;
			 $err_msg = "ER2: " . mysqli_error($this->link) . ".";
		}
		$leadid = mysqli_insert_id($this->link);
		///// insert in lead history
		$res1 =	mysqli_query($this->link,"INSERT INTO sf_lead_history SET lid ='".$leadid."', system_ref_no ='".$reference."', internal_note='', client_note='', remark='', ip='".$_SERVER['REMOTE_ADDR']."', tdate='".$todayDate." ".$todayTime."', activity='Lead Create', status='7'");
		//// check if query is not executed
		if (!$res1) {
			 $flag = false;
			 $err_msg = "ER1: " . mysqli_error($this->link) . ".";
		}
		if($flag){
			return "1~".$reference;
		}else{
			return "0~".$err_msg;
		}
	}
	///// update lead status
	public function updateLeadStatus($userId,$userCode,$leadId,$sysRefNo,$internalNote,$clientNote,$remark,$status,$latitude,$longitude){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$flag = true;
		///// insert in lead history
		$res1 =	mysqli_query($this->link,"INSERT INTO sf_lead_history SET lid ='".$leadId."', system_ref_no ='".$sysRefNo."', internal_note='".$internalNote."', client_note='".$clientNote."', remark='".$remark."', ip='".$_SERVER['REMOTE_ADDR']."', tdate='".$todayDate." ".$todayTime."', activity='Lead Status Update', status='".$status."'");
		//// check if query is not executed
		if (!$res1) {
			 $flag = false;
			 $err_msg = "ER1: " . mysqli_error($this->link) . ".";
		}
		///// update lead master
		$res2 =	mysqli_query($this->link,"UPDATE sf_lead_master SET status='7' WHERE lid='".$leadId."'");
		//// check if query is not executed
		if (!$res2) {
			 $flag = false;
			 $err_msg = "ER2: " . mysqli_error($this->link) . ".";
		}
		if($flag){
			return "1~".$sysRefNo;
		}else{
			return "0~".$err_msg;
		}
	}
	///// update user acceptance in TA/DA
	public function updateTaDaAccept($exp_no,$accept_action){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$flag = true;
		///// update tada
		$res1 =	mysqli_query($this->link,"UPDATE ta_da SET accept_action ='".$accept_action."', accept_date ='".$todayDate." ".$todayTime."' WHERE system_ref_no='".$exp_no."'");
		//// check if query is not executed
		if (!$res1) {
			 $flag = false;
			 $err_msg = "ER1: " . mysqli_error($this->link) . ".";
		}
		if($flag){
			return "1~".$exp_no;
		}else{
			return "0~".$err_msg;
		}
	}
	///// update deviation request for dealer visit
	public function updateDeviation($userId,$userCode,$taskId,$scheduledVisit,$changeVisit,$remark,$latitude,$longitude,$trackaddress){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$flag = true;
		///// update deviation
		$res1 =	mysqli_query($this->link,"INSERT INTO deviation_request set task_type = 'Dealer Visit' , sch_visit='".$scheduledVisit."',change_visit='".$changeVisit."', remark='".$remark."',entry_by='".$userCode."',entry_date='".$todayDate." ".$todayTime."',entry_ip='".$_SERVER['REMOTE_ADDR']."',app_status='Pending For Approval',pjp_id='".$taskId."'");
		//// check if query is not executed
		if (!$res1) {
			 $flag = false;
			 $err_msg = "ER1: " . mysqli_error($this->link) . ".";
		}
		if($flag){
			return "1~".$taskId;
		}else{
			return "0~".$err_msg;
		}
	}
	///////post attendance data to greytHR written by shekhar on 08 APR 2022
	public function updateGreytHrAttendance($usercode,$type,$punchtime){
		///////// get eastman employee code 
		$res_emp = mysqli_query($this->link,"SELECT oth_empid FROM admin_users WHERE username='".$usercode."'");
		$row_emp = mysqli_fetch_assoc($res_emp);
		$empcode = $row_emp["oth_empid"];
		$id = "4398033b-d66c-4a72-94f6-e6bc3b8aa1db";//API ID generated from greytHR in API details page
		//$swipes = file_get_contents("swipes.txt");//Batch of swipes, one swipe per line
		$mkpunchtime = $punchtime;
		$swipes = $mkpunchtime.",".$empcode.",Gurgaon,".$type;///// for IN type=1 and for OUT type = 0
		$private_key = file_get_contents("private-key.pem");
		$pkeyid = openssl_pkey_get_private($private_key);//Private Key generated from greytHR in API details page
		openssl_sign($swipes, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
	
		$data = array(
			"id" => $id,
			"swipes" => $swipes,
			"sign" => base64_encode($signature)
		);
		////start curl
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://eastmanglobal-corp.greythr.com/v2/attendance/asca/swipes",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => true,
			CURLOPT_NOBODY => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_HTTPHEADER => array(
				"X-Requested-With: XMLHttpRequest"
			)
		));
		$response = curl_exec($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		//Need to test for status 200(Ok) to make sure the request was successful
		$err = curl_error($curl);
		/////insert data in to response table
		if($type=="1"){$ty = "ATTENDANCE IN";}else if($type=="0"){$ty = "ATTENDANCE OUT";}else{$ty = "";}
		mysqli_query($this->link,"INSERT INTO greythr_api_data SET userid='".$usercode."', empcode='".$empcode."', requesttype='".$ty."', requestdata='".json_encode($data)."', response='".$response."',response_code='".$httpcode."'");
		curl_close($curl);
		return $httpcode."~".$err;
	}
	///// insert lat long on a time interval
	public function saveLatLong($userId,$userCode,$latitude,$longitude,$trackaddress,$trackdistance){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$flag = true;
		$res1 = mysqli_query($this->link,"INSERT INTO user_lat_long SET userid='".$userId."', latitude='".$latitude."', longitude='".$longitude."', address='".$trackaddress."',travel_km='".$trackdistance."', remote_address='".$_SERVER['REMOTE_ADDR']."',remote_agent='".$_SERVER['HTTP_USER_AGENT']."' , entry_date='".$todayDate."'");
		//// check if query is not executed
		if (!$res1) {
			 $flag = false;
			 $err_msg = "ER1: " . mysqli_error($this->link) . ".";
		}
		if($flag){
			return "1~".$userId;
		}else{
			return "0~".$err_msg;
		}
	}
	///// add ticket by DL/DS for customer
	public function updateTicket($userId,$userCode,$locationCode,$customerName,$customerType,$customerPhone,$customerAddress,$customerCity,$customerState,$customerPincode,$problemReport,$productName,$modelName,$remark,$img1Encodestr,$img1Name,$img2Encodestr,$img2Name,$img3Encodestr,$img3Name,$latitude,$longitude){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$tody = $this->dt_format->format('Ym');
		$yr = $this->dt_format->format('Y');
		$flag = true;
		// make system generated ticket no.
		$sql = "SELECT COUNT(id) AS qa FROM service_ticket_master WHERE location_code='".$locationCode."' AND YEAR(ticket_date)='".$yr."'";
		$res = mysqli_query($this->link,$sql);
		$row = mysqli_fetch_assoc($res);
		$code_id = $row["qa"];
		$next_seq = $row["qa"] + 1;
		/// make 3 digit padding
		$pad = str_pad(++$code_id,4,"0",STR_PAD_LEFT);
		//// make logic of ref no.
		$refno = "TC/".$tody."/".substr(strtoupper($locationCode),2)."/".$pad;
		/////check if any attachment is sent
		if($img1Encodestr!='' || $img2Encodestr!='' || $img3Encodestr!=''){
			///check directory
			$dirct = "ticketimg/".date("Y-m");
			if (!is_dir($dirct)) {
				mkdir($dirct, 0777, 'R');
			}
			$base_img1 = $img1Encodestr;
			$base_img2 = $img2Encodestr;
			$base_img3 = $img3Encodestr;
			// Get file name posted from Android App
			$filename_img1 = $img1Name;
			$filename_img2 = $img2Name;
			$filename_img3 = $img3Name;
			// Decode Image 1//
			if($base_img1!=null){
				$binary_img1 = base64_decode($base_img1);
			}
			else{
				$binary_img1 = null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_img1 = $dirct.'/'.$filename_img1;
			// Images will be saved under 'ticketimg/Y-m/' folder   
			$fileimg1 = fopen($file_img1, 'wb');
			// Create File
			fwrite($fileimg1, $binary_img1);
			fclose($fileimg1);
			// Decode Image 2//
			if($base_img2!=null){
				$binary_img2 = base64_decode($base_img2);
			}
			else{
				$binary_img2 = null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_img2 = $dirct.'/'.$filename_img2;
			// Images will be saved under 'ticketimg/Y-m/' folder   
			$fileimg2 = fopen($file_img2, 'wb');
			// Create File
			fwrite($fileimg2, $binary_img2);
			fclose($fileimg2);
			// Decode Image 3//
			if($base_img3!=null){
				$binary_img3 = base64_decode($base_img3);
			}
			else{
				$binary_img3 = null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_img3 = $dirct.'/'.$filename_img3;
			// Images will be saved under 'tadaimg/Y-m/' folder   
			$fileimg3 = fopen($file_img3, 'wb');
			// Create File
			fwrite($fileimg3, $binary_img3);
			fclose($fileimg3);
		}
		///////insert in ticket master
		$query = "INSERT INTO service_ticket_master SET ticket_no ='".$refno."',ticket_date='".$todayDate." ".$todayTime."',customer_type='".$customerType."',customer_name='".$customerName."',customer_phone='".$customerPhone."',customer_address='".$customerAddress."',customer_city='".$customerCity."',customer_state='".$customerState."',customer_pincode='".$customerPincode."',problem_report='".$problemReport."',product='".$productName."',model='".$modelName."',status='1',remark='".$remark."',attach_img1='".$img1Name."',attach_img2='".$img2Name."',attach_img3='".$img3Name."',location_code='".$locationCode."',entry_source='APP',create_by='".$userCode."',create_date='".$todayDate." ".$todayTime."'";
		$result = mysqli_query($this->link,$query);
		//// check if query is not executed
		if (!$result) {
			 $flag = false;
			 $err_msg = "ER1: " . mysqli_error($this->link) . ".";
		}
		///////insert in ticket history
		$query2 = "INSERT INTO service_ticket_history SET ticket_no ='".$refno."',location_code ='".$locationCode."',activity_name='Ticket Create',status='1',remark='".$remark."',attachment='',entry_by='".$userCode."',entry_date='".$todayDate." ".$todayTime."'";
		$result2 = mysqli_query($this->link,$query2);
		//// check if query is not executed
		if (!$result2) {
			 $flag = false;
			 $err_msg = "ER2: " . mysqli_error($this->link) . ".";
		}
		if($flag){
			return "1~".$refno;
		}else{
			return "0~".$err_msg;
		}
	}
	// function to Compress image written by shekhar on 23 june 2022
	public function compressImage($source, $destination, $quality) {
	  $info = getimagesize($source);
	  if ($info['mime'] == 'image/jpeg'){ 
		$image = imagecreatefromjpeg($source);
	  }elseif ($info['mime'] == 'image/jpg'){
		$image = imagecreatefromgif($source);
	  }elseif ($info['mime'] == 'image/gif'){ 
		$image = imagecreatefromgif($source);
	  } elseif ($info['mime'] == 'image/png'){
		$image = imagecreatefrompng($source);
	  }else{
	  }
	  $res = imagejpeg($image, $destination, $quality);
	  if($res){
		return 1;
	  }else{
		return 0;
	  }
	}
	////// update user activity on 03 feb 2023 by shekhar
	public function updateTaskActivity($userId,$userCode,$activityType,$partyName,$partyCode,$remark,$inLatitude,$inLongitude,$inDatetime,$inStatus,$inAddress,$inImage,$inImageName,$outLatitude,$outLongitude,$outDatetime,$outStatus,$outAddress,$outImage,$outImageName){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		///// check user is already punched attendance or not
		$user_act = mysqli_query($this->link,"SELECT id,status_in,status_out FROM user_activities WHERE user_id='".$userCode."' AND insert_date='".$todayDate."' AND status_out='' ORDER BY id DESC");
		if(mysqli_num_rows($user_act)==0 && $inStatus!=''){
			/// check attendance image directory
			///check directory
			$dirct = "activityimg/".date("Y-m");
			if (!is_dir($dirct)) {
				mkdir($dirct, 0777, 'R');
			}
			$base = $inImage;
			// Get file name posted from Android App
			$filename = $inImageName;
			// Decode Image//
			if($base!=null){
				$binary=base64_decode($base);
			}
			else{
				$binary=null;
			}
    		header('Content-Type: bitmap; charset=utf-8');
			$file_nm = $dirct.'/'.$filename;
    		// Images will be saved under 'www/imgupload/uplodedimages' folder   
			$file = fopen($file_nm, 'wb');
    		// Create File
			if($binary!=null){
				fwrite($file, $binary);
				$res1 = mysqli_query($this->link,"INSERT INTO user_activities SET activity_type='".$activityType."', party_name='".$partyName."', party_code='".$partyCode."', remark='".$remark."', longitude_in='".$inLongitude."',latitude_in='".$inLatitude."',longitude_out='".$outLongitude."',latitude_out='".$outLatitude."',user_id='".$userCode."',status_in='".$inStatus."',in_datetime='".$inDatetime."',status_out='".$outStatus."',out_datetime='".$outDatetime."',address_in='".$inAddress."',address_out='".$outAddress."',insert_date='".$todayDate."',Image_in='".$inImageName."',Image_out='".$outImageName."'");
				if(!$res1){
					$err_msg = "ER1 ".mysqli_error($this->link);
					return "0~".$err_msg;
				}else{
					return "1";
				}
			}else{
				$err_msg = "In Time image not captured";
				return "0~".$err_msg;
			}
			fclose($file);
		////// out attendance punch	
		}else if(mysqli_num_rows($user_act)>0){
			$row_act = mysqli_fetch_array($user_act);
			if($row_act["status_in"]=="IN" && $inStatus!=''){
				$err_msg = "User activity is already running";
				return "0~".$err_msg;
			}else if($row_act["status_out"]==""){
				/// check attendance image directory
				///check directory
				$dirct = "activityimg/".date("Y-m");
				if (!is_dir($dirct)) {
					mkdir($dirct, 0777, 'R');
				}
				$base = $outImage;
				// Get file name posted from Android App
				$filename = $outImageName;
				// Decode Image//
				if($base!=null){
					$binary=base64_decode($base);
				}
				else{
					$binary=null;
				}
				header('Content-Type: bitmap; charset=utf-8');
				$file_nm = $dirct.'/'.$filename;
				// Images will be saved under 'www/imgupload/uplodedimages' folder   
				$file = fopen($file_nm, 'wb');
				// Create File
				if($binary!=null){
					fwrite($file, $binary);
					///// update user profile details
					$res1 = mysqli_query($this->link,"UPDATE user_activities SET activity_type='".$activityType."', party_name='".$partyName."', party_code='".$partyCode."', remark='".$remark."',status_out='".$outStatus."',out_datetime='".$outDatetime."',address_out='".$outAddress."',longitude_out='".$outLongitude."',latitude_out='".$outLatitude."',Image_out='".$outImageName."' WHERE id='".$row_act['id']."'");
					if(!$res1){
						$err_msg = "ER1 ".mysqli_error($this->link);
						return "0~".$err_msg;
					}else{
						return "1";
					}
				}else{
					$err_msg = "Out Time image not captured";
					return "0~".$err_msg;
				}
				fclose($file);
			}else{
				$err_msg = "User activity is already over";
				return "0~".$err_msg;
			}
		}else if(mysqli_num_rows($user_act)>0 && $row_act["status_in"]!="" && $row_act["status_out"]!=""){
			$err_msg = "User activity is already over";
			return "0~".$err_msg;
		}else{
			$err_msg = "Please start activity first";
			return "0~".$err_msg;
		}
	}
	////// add new Activitty
	public function updateActivity($userId,$userCode,$partyName,$partyContact,$remark,$activityType,$latitude,$longitude,$address,$docEncodeStr,$docName,$activityAction){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$tody = $this->dt_format->format('Ymd');
		$flag = true;
		//////// get max lead id
		$res_sysref = mysqli_query($this->link,"SELECT COUNT(id) AS cnt FROM activity_master WHERE user_id ='".$userCode."'");
		$row_sysref = mysqli_fetch_assoc($res_sysref);
		$next_no = $row_sysref['cnt']+1;
		$pad = str_pad($next_no,5,"0",STR_PAD_LEFT);  
		$reference = "ACT/".$userCode."/".$pad;
		///check if any attachment is sent
		if($docEncodeStr){
			///check directory
			$dirct = "activityimg/".date("Y-m");
			if (!is_dir($dirct)) {
				mkdir($dirct, 0777, 'R');
			}
			$base = $docEncodeStr;
			// Get file name posted from Android App
			$filename = $docName;
			// Decode Image//
			if($base!=null){
				$binary=base64_decode($base);
			}
			else{
				$binary=null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_nm = $dirct.'/'.$filename;
			// Images will be saved under 'www/imgupload/uplodedimages' folder   
			$file = fopen($file_nm, 'wb');
			// Create File
			fwrite($file, $binary);
			fclose($file);
		}
		///// insert in lead master
		$res2 =	mysqli_query($this->link,"INSERT INTO activity_master SET ref_no ='".$reference."', activity_type='".$activityType."',activity_date ='".$todayDate."', user_id='".$userCode."', party_code='', party_name='".$partyName."', party_address='', party_city='', party_state='',party_contact='".$partyContact."', party_email='', intial_remark='".$remark."', initial_attach='".$filename."', status ='Start', entry_by ='".$userCode."', entry_date='".$todayDate." ".$todayTime."',entry_ip='".$_SERVER['REMOTE_ADDR']."',activity_action='".$activityAction."'");
		//// check if query is not executed
		if (!$res2) {
			 $flag = false;
			 $err_msg = "ER2: " . mysqli_error($this->link) . ".";
		}
		$leadid = mysqli_insert_id($this->link);
		///// insert in lead history
		$res1 =	mysqli_query($this->link,"INSERT INTO activity_history SET ref_no ='".$reference."', remark ='".$remark."', status='Start', attachment='".$filename."', entry_by='".$userCode."', entry_date='".$todayDate." ".$todayTime."', entry_ip='".$_SERVER['REMOTE_ADDR']."', latitude='".$latitude."', longitude='".$longitude."', address='".$address."'");
		//// check if query is not executed
		if (!$res1) {
			 $flag = false;
			 $err_msg = "ER1: " . mysqli_error($this->link) . ".";
		}
		if($flag){
			return "1~".$reference;
		}else{
			return "0~".$err_msg;
		}
	}
	///// update Activity status
	public function updateActivityStatus($userId,$userCode,$actId,$sysRefNo,$remark,$status,$docEncodeStr,$docName,$latitude,$longitude,$address){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$flag = true;
		///check if any attachment is sent
		if($docEncodeStr){
			///check directory
			$dirct = "activityimg/".date("Y-m");
			if (!is_dir($dirct)) {
				mkdir($dirct, 0777, 'R');
			}
			$base = $docEncodeStr;
			// Get file name posted from Android App
			$filename = $docName;
			// Decode Image//
			if($base!=null){
				$binary=base64_decode($base);
			}
			else{
				$binary=null;
			}
			header('Content-Type: bitmap; charset=utf-8');
			$file_nm = $dirct.'/'.$filename;
			// Images will be saved under 'www/imgupload/uplodedimages' folder   
			$file = fopen($file_nm, 'wb');
			// Create File
			fwrite($file, $binary);
			fclose($file);
		}
		///// insert in activity history
		$res1 =	mysqli_query($this->link,"INSERT INTO activity_history SET ref_no ='".$sysRefNo."', remark='".$remark."', status='".$status."', attachment='".$filename."', entry_by='".$userCode."', entry_date='".$todayDate." ".$todayTime."', entry_ip='".$_SERVER['REMOTE_ADDR']."', latitude='".$latitude."', longitude='".$longitude."', address='".$address."'");
		//// check if query is not executed
		if (!$res1) {
			 $flag = false;
			 $err_msg = "ER1: " . mysqli_error($this->link) . ".";
		}
		///// update lead master
		$res2 =	mysqli_query($this->link,"UPDATE activity_master SET status='".$status."', last_updateby ='".$userCode."', last_updatedate='".$todayDate." ".$todayTime."',last_updateip='".$_SERVER['REMOTE_ADDR']."' WHERE ref_no ='".$sysRefNo."'");
		//// check if query is not executed
		if (!$res2) {
			 $flag = false;
			 $err_msg = "ER2: " . mysqli_error($this->link) . ".";
		}
		if($flag){
			return "1~".$sysRefNo;
		}else{
			return "0~".$err_msg;
		}
	}
	
	///// update deviation status
	public function updateDeviationStatus($userId,$userCode,$actId,$sysRefNo,$remark,$status,$docEncodeStr,$docName,$latitude,$longitude,$address){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$flag = true;
	
	
		///// upadte in Deviation 

		$res1 =	mysqli_query($this->link,"UPDATE deviation_request set app_by = '".$userId."', app_date='".$todayDate."', app_status='".$status."', app_remark='".$remark."', app_ip='".$_SERVER['REMOTE_ADDR']."' where id='".$sysRefNo."' ");
		//// check if query is not executed
		if (!$res1) {
			 $flag = false;
			 $err_msg = "ER1: " . mysqli_error($this->link) . ".";
		}
		///// update lead master
		
		if($status=="Approved"){
			$row_v = mysqli_fetch_assoc(mysqli_query($this->link,"SELECT pjp_id,change_visit FROM deviation_request WHERE id='".$sysRefNo."'"));
		$res2 =	mysqli_query($this->link,"UPDATE pjp_data SET visit_area='".$row_v["change_visit"]."' WHERE id = '".$row_v["pjp_id"]."'");
		//// check if query is not executed
		if (!$res2) {
			 $flag = false;
			 $err_msg = "ER2: " . mysqli_error($this->link) . ".";
		}
		
		}
		
		
		if($flag){
			return "1~".$sysRefNo;
		}else{
			return "0~".$err_msg;
		}
	}
	///////leave request
	public function leaveRequest($userId,$userCode,$leaveType,$fromDate,$toDate,$reason,$description,$latitude,$longitude,$address){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$flag = true;
		if($toDate != $fromDate){
			$leave_time = ($this->daysDifference($toDate,$fromDate)+1)." Day";
		}else{
			$leave_time = "1 Day";
		}
		// insert all details of leave into leave request table //
		$sql = "INSERT INTO hrms_leave_request SET empid ='".$userCode."', leave_type='".$leaveType."', from_date = '".$fromDate."', to_date = '".$toDate."', leave_duration = '".$leave_time."',  purpose  = '".$reason ."',  description = '".$description."' ,status = '3' , entry_date  = '".$todayDate."',  entry_time  = '".$todayTime."' ";
		$res_leave =  mysqli_query($this->link,$sql);
		$sysRefNo = "REQ".mysqli_insert_id($this->link);
		/// check if query is execute or not//
		if(!$res_leave){
			$flag = false;
			$err_msg = "Error 2". mysqli_error($this->link) . ".";
		}
		if($flag){
			return "1~".$sysRefNo;
		}else{
			return "0~".$err_msg;
		}
	}
	////////// function to calculate day difference between two dates //////////
	public function daysDifference($endDate, $beginDate){
		$date_parts1=explode("-", $beginDate); $date_parts2=explode("-", $endDate);
		$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
		$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
		return $end_date - $start_date;
	}
	/////// check user task on same date from same location update by shekhar on 04 MAR 2025
	public function checkUserActivityLocation($userCode,$taskType,$taskLat,$taskLng){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		
		$result = mysqli_query($this->link,"SELECT id FROM user_track WHERE userid ='".$userCode."' AND task_name='".$taskType."' AND latitude='".$taskLat."' AND longitude='".$taskLng."' AND entry_date='".$todayDate."' ORDER BY id DESC");
		$numrow = mysqli_num_rows($result);
		return $numrow;	
	}
	///// update cancel status in TA/DA update by shekhar on 23 jun 2025
	public function cancelTaDa($exp_no){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$flag = true;
		///// check status of ta da should be pending only then we only can allow to cancel
		if(mysqli_num_rows(mysqli_query($this->link,"SELECT id FROM ta_da WHERE system_ref_no='".$exp_no."' AND status='Pending'"))>0){
			///// update tada
			$res1 =	mysqli_query($this->link,"UPDATE ta_da SET status ='Cancelled' WHERE system_ref_no='".$exp_no."'");
			//// check if query is not executed
			if (!$res1) {
				 $flag = false;
				 //$err_msg = "ER1: " . mysqli_error($this->link) . ".";
				$err_msg = "Execution Failed";
			}
		}else{
			$flag = false;
			$err_msg = "You can not cancel this TA/DA";	
		}
		if($flag){
			return "1~".$exp_no;
		}else{
			return "0~".$err_msg;
		}
	}
}