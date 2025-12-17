<?php
require_once("security/dbh.php");
///// function to get random 3 digit document code
function isExist($link1, $fy, $ranstr){

	$indb = mysqli_num_rows(mysqli_query($link1,"SELECT id from document_counter where financial_year='".$fy."' and doc_code='".$ranstr."'"));
	if($indb > 0){
		return true;
	}else{
		return false;
	}
}
function generateRandomString($length,$fy,$link1){

	$x = true;
	while($x){
		$ranstr = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789"), 0, $length);
		$x = isExist($link1, $fy, $ranstr);
	}
	return $ranstr;
}
$s = 0;
$prefixdocstr = "25-26";
$parentid = "";
$today = date("Y-m-d");
$datetime = date("Y-m-d H:i:s");
$res_asp = mysqli_query($link1,"SELECT sno,name,group_id,state,id_type,user_level,email,phone,circle FROM asc_master WHERE asc_code=''");
while($row = mysqli_fetch_assoc($res_asp)){
	//$parentid = $row["group_id"];
	$parentid = "SUHODL001";
	///// get 3 charcter random string
	$docstr = generateRandomString(3,$prefixdocstr,$link1);
   ///// first of all we will check document string ///
   if(mysqli_num_rows(mysqli_query($link1,"SELECT id from document_counter where financial_year='".$prefixdocstr."' and doc_code='".$docstr."'"))==0){
   //// get state code from statemaster
   $res_state=mysqli_query($link1,"select code from state_master where state='".$row["state"]."'")or die("ER1".mysqli_error($link1));
   $row_state=mysqli_fetch_array($res_state);
   $statecode=$row_state['code'];
   /// explode location type value
   $expld_loctyp=explode("~",$locationtype);
   ////// count max no. of location in selected state
   $query_code="select MAX(code_id) as qa from asc_master where state='".$row["state"]."' and id_type='".$row["id_type"]."'";
   $result_code=mysqli_query($link1,$query_code)or die("ER2".mysqli_error($link1));
   $arr_result2=mysqli_fetch_array($result_code);
   $code_id=$arr_result2[0];
   /// make 3 digit padding
   $pad=str_pad(++$code_id,3,"0",STR_PAD_LEFT);
   //// make logic of location code
   $newlocationcode="SU".$row["id_type"].$statecode.$pad;
   ///// check generated id should not be in system
   if(mysqli_num_rows(mysqli_query($link1,"SELECT sno from asc_master where uid='".$newlocationcode."'"))==0){
   // insert all details of location //
  $sql="UPDATE asc_master set uid='".$newlocationcode."',pwd='".$newlocationcode."',asc_code='".$newlocationcode."',code_id='".$pad."',status='Active',login_status='Active',start_date='".$today."',update_date='".$datetime."' WHERE sno = '".$row["sno"]."'";
   mysqli_query($link1,$sql)or die("ER3".mysqli_error($link1));
   if($parentid){
   //insert into credit bal////////////////////////////
   mysqli_query($link1,"insert into current_cr_status set parent_code='".$parentid."', asc_code='".$newlocationcode."',cr_abl='0',cr_limit='0',total_cr_limit='0'")or die("ER4".mysqli_error($link1));
   ///insert into mapping table/////////////////////////
   mysqli_query($link1,"insert into mapped_master set uid='".$parentid."',mapped_code='".$newlocationcode."',status='Y',update_date='".$today."'")or die("ER5".mysqli_error($link1));
   }
   ///insert into document string table/////////////////////////
   $invstr="INV/".$prefixdocstr."/".$docstr."/";
   $stnstr="STN/".$prefixdocstr."/".$docstr."/";
   $prnstr="PRN/".$prefixdocstr."/".$docstr."/";
   $srnstr="SRN/".$prefixdocstr."/".$docstr."/";
   $rcvpaystr="RECP/".$prefixdocstr."/".$docstr."/";
   mysqli_query($link1,"insert into document_counter set location_code='".$newlocationcode."',financial_year='".$prefixdocstr."',doc_code='".$docstr."',inv_str='".$invstr."',stn_str='".$stnstr."',prn_str='".$prnstr."',srn_str='".$srnstr."',rcvpay_str='".$rcvpaystr."',create_on='".$today."'")or die("ER6".mysqli_error($link1));
   //////////////////////////////////////////////////////////////
   //// create a user corresponding to this location
	$query_code="select MAX(uid) as qc from admin_users";
    $result_code=mysqli_query($link1,$query_code)or die("error2".mysqli_error($link1));
    $arr_result2=mysqli_fetch_array($result_code);
    $code_id=$arr_result2[0];
    $pad=str_pad(++$code_id,3,"0",STR_PAD_LEFT);
    $admiCode="SUUSR".$row["id_type"].$statecode.$pad;
	$pwd=$admiCode."@321";
	//// insert in user table	
	$usr_add="INSERT INTO admin_users set username ='".$admiCode."',password ='".$pwd."',owner_code='".$newlocationcode."',user_level='".$row["user_level"]."',name= '".$row["name"]."',utype='8',phone='".$row["phone"]."',emailid= '".$row["email"]."',create_by='admin' ,status='active',createdate='".date("Y-m-d H:i:s")."'";
	   mysqli_query($link1,$usr_add);
	//// give auto basic permission ////
	mysqli_query($link1,"insert into access_region set uid='".$admiCode."',region='".$row["circle"]."',status='Y'")or die(mysqli_error($link1));
	mysqli_query($link1,"insert into access_state set uid='".$admiCode."',state='".$row["state"]."',status='Y'")or die(mysqli_error($link1));
	mysqli_query($link1,"insert into access_role set uid='".$admiCode."',role_id='".$row["id_type"]."',status='Y'")or die(mysqli_error($link1));
	mysqli_query($link1,"insert into access_location set uid='".$admiCode."',location_id='".$newlocationcode."',status='Y'")or die(mysqli_error($link1));
	   if($row["id_type"]=="DS" || $row["id_type"]=="DL"){
			$res11 = mysqli_query($link1,"INSERT INTO `access_function` (`id`, `uid`, `function_id`, `status`, `updatedate`) VALUES ('', '".$admiCode."', '43', 'Y', ''),('', '".$admiCode."', '2', 'Y', ''),('', '".$admiCode."', '10', 'Y', ''),('', '".$admiCode."', '6', 'Y', ''),('', '".$admiCode."', '130', 'Y', ''),('', '".$admiCode."', '121', 'Y', ''),('', '".$admiCode."', '44', 'Y', ''),('', '".$admiCode."', '154', 'Y', '')");
			//// check if query is not executed
			if (!$res11) {
			  $flag = false;
			  $err_msg = "Error Code11:".mysqli_error($link1);
			}
			$res12 = mysqli_query($link1,"INSERT INTO `mapped_brand` (`id`, `userid`, `brand`, `status`) VALUES ('', '".$admiCode."', '11', 'Y')");
			//// check if query is not executed
			if (!$res12) {
			  $flag = false;
			  $err_msg = "Error Code12:".mysqli_error($link1);
			}
			$res14 = mysqli_query($link1,"INSERT INTO `access_report` (`uid`, `report_id`, `status`) VALUES ('".$admiCode."', '12', 'Y'),('".$admiCode."', '13', 'Y'),('".$admiCode."', '15', 'Y'),('".$admiCode."', '90', 'Y'),('".$admiCode."', '1', 'Y'),('".$admiCode."', '5', 'Y'),('".$admiCode."', '6', 'Y'),('".$admiCode."', '20', 'Y'),('".$admiCode."', '82', 'Y')");
			//// check if query is not executed
			if (!$res14) {
			  $flag = false;
			  $err_msg = "Error Code14:".mysqli_error($link1);
			}
		  }
	//add script when a new dealer created then it should be auto assigned for all user of same state.requirement raised by Ravinder(EASTMAN) and developed by shekhar on 18 oct 2022//////
	/*if($row["id_type"]=="DL" || $row["id_type"]=="DS" || $row["id_type"]=="SR" || $row["id_type"]=="RT"){
		//// pick all users which are having same state rights
		$res_same_state = mysqli_query($link1,"SELECT uid FROM access_state WHERE state = '".$row["state"]."' AND status ='Y'");
		while($row_same_state = mysqli_fetch_assoc($res_same_state)){
			/// check user is activated or not
			if(mysqli_num_rows(mysqli_query($link1,"SELECT uid FROM admin_users WHERE username='".$row_same_state["uid"]."' AND `status` LIKE 'Active'"))>0){
				if(mysqli_num_rows(mysqli_query($link1,"SELECT id_type FROM access_location WHERE uid='".$row_same_state["uid"]."' AND id_type='".$row["id_type"]."' AND status='Y'"))>0){
					$res10 = mysqli_query($link1,"insert into access_location set uid='".$row_same_state["uid"]."',location_id='".$newlocationcode."',state='".$row["state"]."',id_type='".$row["id_type"]."',status='Y'");
					//// check if query is not executed
					if (!$res10) {
						$flag = false;
						$err_msg = "Error Code10:".mysqli_error($link1);
					}
				}
			}
		}
	}*/
	//end add script when a new dealer created then it should be auto assigned for all user of same state.requirement raised by Ravinder(EASTMAN) and developed by shekhar on 18 oct 2022//////
	
   ////// insert in activity table////
	//dailyActivity($_SESSION['userid'],$newlocationcode,"LOCATION","ADD",$ip,$link1,"");
	//dailyActivity($_SESSION['userid'],$admiCode,"ADMIN USER","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
	$s++;
	////// return message
	//$msg="You have successfully created a new location with ref. no. ".$newlocationcode." and location user id is ".$admiCode;
   }else{
	////// return message
	$msg="Something went wrong. Please try again.";
   }
   }else{
	////// return message
	$msg="Something went wrong like document code was already in DB. Please try again.";
   }
}
echo $s."  ".$msg;