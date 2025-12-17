<?php
$page_type = "insecure";
require_once("security/backend.php");
if(!isset($_SESSION["userid"]))
{
	$user = $_POST['userid'];
	$pass = $_POST['pwd'];

	if($_SESSION["otp"]["otp"] == "verified")
	{
		$user = $_SESSION["otp"]["temp_user"];
		$pass = $_SESSION["otp"]["random"];
		unset($_SESSION["otp"]);
		$_SESSION["otp"] = "verified";
	}
	else
	{
		//$pass = hash("sha256", md5($pass));
	}
	
	$res = $acc->doLogin($link1, $user, $pass);
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
else
{
	//exit(header('Location:'.$root.'/index.php'));
	exit(header('Location:'.$root.'/index.php'));
}
?>