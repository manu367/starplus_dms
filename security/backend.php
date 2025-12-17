<?php
//// ERROR & WARNINGS ////////////////////////
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(0); //0:DISABLE, E_ALL: ENABLE
///// SESSION COOKIE //////////////////////////
$maxlifetime = 60*60;
$secure = true;
$httponly = true;
$samesite = 'lax';
$domain = ''; //$_SERVER['HTTP_HOST'];
$path = '/';
if(PHP_VERSION_ID < 70300){
    session_set_cookie_params($maxlifetime, ''.$path.'; samesite='.$samesite, $domain, $secure, $httponly);
}
else{
    session_set_cookie_params([
        'lifetime' => $maxlifetime,
        'path' => $path,
        'domain' => $domain,
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => $samesite
    ]);
}

session_start();

date_default_timezone_set('Asia/Kolkata'); //(GMT +5:30)
//$root = "https://starplus.cancrm.in";
$root = "/star_plus/starplus_dms";
//////////////////////////////////////////////

/// db connection
include_once("dbh.php");
/// class with account/session related functions
include_once("accounts.php");
$acc = new Accounts();
/// general functions
include_once("general.php");
 
/// is user logged in? (only for secure pages)
$page_type = (isset($page_type))?$page_type:'secure';


if($page_type != "insecure"){

    if(!$acc->isLoggedin($link1)){

		//exit(header('Location:'.$root.'/hsilindx.php'));
		exit(header('Location:'.$root));
    }

    if(!isset($_SESSION["userid"])){

        //exit(header('Location:'.$root.'/hsilindx.php'));
		exit(header('Location:'.$root));
    }
}
//echo $page_type;

/// filter all incoming requests
if(isset($_REQUEST['start'])){
	
}else{
	if(isset($_REQUEST) && $_REQUEST){
		$_REQUEST = requestFilter($link1, $_REQUEST);
	}
}
if(isset($_GET) && $_GET){
    $_GET = requestFilter($link1, $_GET);
}
if(isset($_POST) && $_POST){
    $_POST = requestFilter($link1, $_POST);
}

/// following things are required for bug fix
if(isset($_REQUEST["offset"])){
	$_REQUEST["offset"] = ($_REQUEST["offset"])?$_REQUEST["offset"]:0;
}