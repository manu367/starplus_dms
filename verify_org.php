<?php
$today = date("Y-m-j");
require_once("config/dbconnect.php");
$userid = mysqli_real_escape_string($link1,$_POST['userid']);
$password1 = mysqli_real_escape_string($link1,$_POST['pwd']);
$query_aut="Select * from admin_users where username='$userid' and status='active'";
$result_aut=mysqli_query($link1,$query_aut) or die(mysqli_error($link1));
$arr_res_aut=mysqli_fetch_assoc($result_aut);
if($arr_res_aut['password']==$password1){
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
	$sql_ins="insert into login_data set userid='".$userid."',ip='".$_SERVER['REMOTE_ADDR']."'";
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
	else {////// otherwise change password first
	     header("Location:changepwd.php");
		 exit;
	}
}
else{
  header("Location:error.php");
  exit;
}
?>