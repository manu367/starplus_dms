<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
$app_version=$_REQUEST['app_version'];
$uid=$_REQUEST['userName'];
$pwd=$_REQUEST['pwd']; 
$a = array();  
$b = array();
if($app_version == "1.1" ){
$users = $db->getUserDetails($uid,$pwd);
if(mysqli_num_rows($users)>0){
   $row = mysqli_fetch_array($users);
   
  if($row["profile_img_name"]!=''){
   $path="https://starplus.cancrm.in/".$row['profile_img_path']; 
   $img_url=$path.$row["profile_img_path"];
	  }
  // $username = preg_replace('/[^A-Za-z0-9]/', "", $row["name"]); 
   $usercode = preg_replace('/[^A-Za-z0-9]/', "", $row["username"]); 
   //$address = preg_replace('/[^A-Za-z0-9]/', "", $row["address"]); 
   $usertype_name=$db->getAnyDetails($row["utype"],"typename","id","usertype_master");
   
	$b["username"] = $row["name"];
	$b["usercode"]=$usercode;
	$b["password"] = $row["password"];
	$b["mobile_no"] = $row["phone"];
	$b["alternate_contact"]=$row["alternate_contact"];
    
	$b["emailId"]= $row["emailid"];
	$b["type"] = $usertype_name;
	$b["pincode"] =$row["pincode"];
	$b["city"] =$row["city"]; 
	$b["state"] = $row["state"];
	$b["address"] = $row["address"];
	$b["profilePic"] = $row["profile_img_name"];
	
	$b["status"]=$row["status"];
	$b["error_code"]=200;
	$b["device_token"]= $row["device_token"];
	$b["img_profile"]= $img_url;
	$b["referral_code"] = $row["referralCode"];
}else{
	$b["status"]=0;
	$b["error_code"]=201;
	$b["error_msg"]="Username or Password Mis-match";
}
//array_push($a,$b);
}else{
$b["status"]="App Version Error";
//array_push($a,$b);
}
echo json_encode($b);    
?>