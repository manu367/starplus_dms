<?php 
include_once 'db_functions.php'; 
$db = new DB_Functions(); 
//// clone
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);

///// get userid
$uid = $_REQUEST["userid"];
$ucode = $_REQUEST["usercode"];
if($uid!="" && $ucode!=""){
	$a = array();
	$final_array = array();
	///// get left tab on right basis
	$res_acctab = mysqli_query($conn,"SELECT tabid FROM access_app_tab WHERE userid = '".$ucode."' AND status='1'");
	while($row_acctab = mysqli_fetch_assoc($res_acctab)){
		//// get tab details
		$res_tab = mysqli_query($conn,"SELECT * FROM app_tab_master WHERE status='1' AND tabid='".$row_acctab['tabid']."'");
		$row_tab = mysqli_fetch_assoc($res_tab);
		
		$imglink =  "require('".$row_tab['subtabicon']."')";
		$app_leftnav = array("nav" => $row_tab['maintabname'], "routeName" => $row_tab['filename'], "title" => $row_tab['subtabname'], "imagelink" => $imglink);
		array_push($a,$app_leftnav);	
	}
	$final_array["status"] = 1;
	$final_array["tabArray"] = $a;
	echo json_encode($final_array);
}else{
	$final_array["message"] = "Userid details not received";
	$final_array["status"] = 0;
	echo json_encode($final_array);
}
?>