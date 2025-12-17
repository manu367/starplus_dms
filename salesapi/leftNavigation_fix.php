<?php 
///// get userid
$uid = $_REQUEST["userid"];
$ucode = $_REQUEST["usercode"];
if($uid!="" && $ucode!=""){
	$a = array();
	$final_array = array();
	////// get left tab
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "Home", "title" => "Home", "imagelink" => "require('../assets/home.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "MyTask", "title" => "MyTask", "imagelink" => "require('../assets/clipboard.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "myActivity", "title" => "Activity", "imagelink" => "require('../assets/immigration.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "deviationApproval", "title" => "Deviation Approval", "imagelink" => "require('../assets/report.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "Report", "title" => "Report", "imagelink" => "require('../assets/report.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "SalesOrder", "title" => "Sales Order", "imagelink" => "require('../assets/team.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "DealerVisitDirect", "title" => "Dealer Visit", "imagelink" => "require('../assets/team.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "DealerTask", "title" => "Target", "imagelink" => "require('../assets/stock-market.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "Lead", "title" => "Lead", "imagelink" => "require('../assets/leadup.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "Offer", "title" => "Offers", "imagelink" => "require('../assets/offer.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "Stock", "title" => "Stock", "imagelink" => "require('../assets/store.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "FeedBack", "title" => "Feedback", "imagelink" => "require('../assets/feedback.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "TADA", "title" => "TA/DA", "imagelink" => "require('../assets/donation.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "Attandance", "title" => "Attandance", "imagelink" => "require('../assets/immigration.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "Explore", "title" => "Explore", "imagelink" => "require('../assets/intexp.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "Network", "title" => "Network", "imagelink" => "require('../assets/networking.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "Dashboard", "title" => "Dashboard", "imagelink" => "require('../assets/monitor.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "Activity", "title" => "My Time Line", "imagelink" => "require('../assets/stock-market.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "Travel", "title" => "Travel", "imagelink" => "require('../assets/world.png')");
	array_push($a,$app_leftnav);
	$app_leftnav = array("nav" => "MainDrawer", "routeName" => "Profile", "title" => "Profile", "imagelink" => "require('../assets/add-user.png')");
	array_push($a,$app_leftnav);
	$final_array["status"] = 1;
	$final_array["tabArray"] = $a;
	echo json_encode($final_array);
}else{
	$final_array["message"] = "Userid details not received";
	$final_array["status"] = 0;
	echo json_encode($final_array);
}
?>