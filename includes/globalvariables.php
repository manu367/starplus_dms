<?php
//set_magic_quotes_runtime(0);
define("TIME_OUT_ONLINE", 1200);
define("TIME_OUT_IDLE", 120);
define("DISPLAY_ERRORS",TRUE);
// Admin
define("siteTitle", "Distributor Management System");
define("BRANDNAME","Star Plus",true);
define("COMPANYNAME","Star Plus",true);
/////
$today=date("Y-m-d");
$todayt=date("Ymd");
$datetime=date("Y-m-d H:i:s");
$currtime=date("H:i:s");
$now=date("His");
$ip=$_SERVER['REMOTE_ADDR'];
$browserid=session_id();
$btncolor = " btn-primary";/// From here you can change yorr application all button css
$screenwidth = "col-sm-9";//// if you set nav bar as V then it should be 9 otherwise it should 12
$tableheadcolor = " btn-primary";/// From here you can change yorr application all table header color which are showing in listing
$imeitag = " Serial No. ";/// By using this bariable you can change imei with serial no. in whole application
///// page nav variable ///////////////////
$pagenav="&pid=".$_REQUEST['pid']."&hid=".$_REQUEST['hid'];
///// financial year /////////////
///// financial year /////////////
$curr_y=date("y");
$curr_m=date("m");
$pre_y=date("y")-1;
$next_y=date("y")+1;
if($curr_m>="04"){
	$fy=$curr_y."-".$next_y;
}else{
	$fy=$pre_y."-".$curr_y;
}

///////////////// for backdate entry written by shekhar on 29 mar 23
/*$backdate = "2023-03-31";
$backdatet = "20230331";
$backdatetime = "2023-03-31 ".date("H:i:s");
$arrbypassdate = array("2023-04-01","2023-04-02");
if(in_array($today, $arrbypassdate))
{
	$today = $backdate;
	$todayt = $backdatet;
	$datetime = $backdatetime;
}*/
?>