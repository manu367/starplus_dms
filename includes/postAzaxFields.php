<?php
require_once("../config/dbconnect.php");
require_once("../includes/globalvariables.php");
session_start();
//////////////////  save payment from SAP
if($_POST['tabEditSave']) {
	///// update report master
	$query = "UPDATE report_master SET header_id='".$_POST['mntbidd']."',header='".$_POST['mntbb']."',name='".$_POST['sbtbb']."',file_name='".$_POST['fntnn']."',icon_img='".$_POST['iconn']."',status='".$_POST['stass']."',module_name='".$_POST['moduu']."' WHERE id='".$_POST['tabid']."'";
	$result = mysqli_query($link1,$query);
	if($result){	
		echo "1~Tab is successfully updated";
	}else{
		echo "0~Something went wrong";
	}
}
?>