<?php
//require_once("config/dbconnect.php");
$s = 0;
$today = date("Y-m-d");
$datetime = date("Y-m-d H:i:s");
$res_asp = mysqli_query($link1,"SELECT sno,asc_code,state,id_type FROM asc_master WHERE id_type IN ('DL','DS') AND status='Active'");
while($row = mysqli_fetch_assoc($res_asp)){
	//add script when a new dealer created then it should be auto assigned for all user of same state.requirement raised by Ravinder(EASTMAN) and developed by shekhar on 18 oct 2022//////
	if($row["id_type"]=="DL" || $row["id_type"]=="DS"){
		//// pick all users which are having same state rights
		$res_same_state = mysqli_query($link1,"SELECT uid FROM access_state WHERE state = '".$row["state"]."' AND status ='Y'");
		while($row_same_state = mysqli_fetch_assoc($res_same_state)){
			/// check user is activated or not
			if(mysqli_num_rows(mysqli_query($link1,"SELECT uid FROM admin_users WHERE username='".$row_same_state["uid"]."' AND `status` LIKE 'Active'"))>0){
				if(mysqli_num_rows(mysqli_query($link1,"SELECT id_type FROM access_location WHERE uid='".$row_same_state["uid"]."' AND id_type='".$row["id_type"]."' AND status='Y'"))>0){
					///// check if already exist
					if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM access_location WHERE uid='".$row_same_state["uid"]."' AND location_id='".$row["asc_code"]."' AND state='".$row["state"]."' AND id_type='".$row["id_type"]."'"))>0){
						$res10 = mysqli_query($link1,"UPDATE access_location set status='Y' WHERE uid='".$row_same_state["uid"]."' AND location_id='".$row["asc_code"]."' AND state='".$row["state"]."' AND id_type='".$row["id_type"]."'");
					}else{
						$res10 = mysqli_query($link1,"insert into access_location set uid='".$row_same_state["uid"]."',location_id='".$row["asc_code"]."',state='".$row["state"]."',id_type='".$row["id_type"]."',status='Y'");
					}
					//// check if query is not executed
					if (!$res10) {
						$flag = false;
						$err_msg = "Error Code10:".mysqli_error($link1);
					}
				}
			}
		}
	}
	//end add script when a new dealer created then it should be auto assigned for all user of same state.requirement raised by Ravinder(EASTMAN) and developed by shekhar on 18 oct 2022//////
	$s++;
}
echo $s."  ".$err_msg;