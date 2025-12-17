<?php 
$db_user = 'skm_db_usr';
$db_pass = '%0e5O7qm6';
$db_host = 'localhost';
$db = "sukam_cansale_dms";
$link1 = mysqli_connect($db_host, $db_user, $db_pass,$db) or die("Unable to connect to MySQL");
//$selected = mysqli_select_db("candms",$link1) or die("Could not select DB");
/*			##############################	TIME Diffrence US to INDIA		####################*/
$time_zone=time() + 0;	
date_default_timezone_set ("Asia/Calcutta");
/*			##############################	TIME Diffrence US to INDIA		####################*/
/*echo "<script type='text/javascript' src='../js/block.js'></script>";*/
?>