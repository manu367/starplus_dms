<?php
//// ERROR & WARNINGS ////////////////////////
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL); //0:DISABLE, E_ALL: ENABLE
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
require_once("includes/common_function.php");
/// check if session is already there then same account should be open
session_start();
if($_SESSION['userid']){
      header("Location:admin/home2.php");
      exit;
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="UTF-8">
 <link rel="shortcut icon" href="img/titleimg.png" type="image/png">
 <title>Distributor Management System (DMS)</title>
 <!-- applying css -->
 <link href="css/css_index.css" rel="stylesheet" type="text/css"/>
 <link rel="stylesheet" href="css/bootstrap.min.css">
 <!-- applying Javascript -->
 <script src="js/prefixfree.min.js"></script>
 <script src="js/jquery.min.js"></script>
 <script src="js/bootstrap.min.js"></script>
 <script type="text/javascript">
	<!--
		if (top.location!= self.location) {
			top.location = self.location.href
		}
	//-->
 </script>
</head>
<body>
<div id="login">
  <h2 align="center">Sign in to continue to DMS</h2>
  <form id="login_form" name="login_form" method="post" action="verify.php">
    <div class="row">
      <div class="col-sm-12" align="center"><img src="img/logo.png"></div>
    </div>
    <div class="row">
      <div class="col-sm-12" align="center">&nbsp;</div>
    </div>
    <div class="row">
      <div class="col-sm-12" align="center">&nbsp;</div>
    </div>
    <div class="row">
      <div class="col-sm-4"><label>USER ID<strong><span style="color:red">*</span></strong></label></div>
      <div class="col-sm-8"><input type="text" placeholder="User Id" name="userid"  id="userid" required></div>
    </div>
    <div class="row">
      <div class="col-sm-4">&nbsp;</div>
      <div class="col-sm-8">&nbsp;</div>
    </div>
    <div class="row">
      <div class="col-sm-4"><label>PASSWORD<strong><span style="color:red">*</span></strong></label></div>
      <div class="col-sm-8"><input type="Password" placeholder="Password" name="pwd"   id="pwd" required></div>
    </div><br/>
    <div class="row">
      <div class="col-sm-12"><button id="sign_in_button"><span class="button_text">Sign In</span></button></div>
    </div>
    <div class="row">
      <div class="col-sm-12" style="color:#FF0000"><?php if($_REQUEST['msg']){ echo errorMsg($_REQUEST['msg']);}?></div>
    </div>
  </form>
    <div class="row">
      <div class="col-sm-4">&nbsp;</div>
      <div class="col-sm-8">&nbsp;</div>
    </div>
  <div class="row">
      <div class="col-sm-12" align="center">Copyright © 2023. All Rights Reserved. Powered By : <a href="http://www.candoursoft.com/" target="_blank">CANDOUR SOFTWARE</a></div>
   </div>
</div>
</body>
</html>