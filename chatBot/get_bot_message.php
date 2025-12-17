<?php
date_default_timezone_set('Asia/Kolkata');
include('database.inc.php');
session_start();
$browserid = session_id();
$txt=mysqli_real_escape_string($con,$_POST['txt']);
$bws=mysqli_real_escape_string($con,$_POST['bwsid']);
if($browserid==$bws){
	//// if press 0 then move to level 1
	if($txt==0 || $txt==10){
		$lvel = 0;
	}else{
		$lvl=mysqli_fetch_assoc(mysqli_query($con,"SELECT level FROM message WHERE browserid='$bws' ORDER BY id DESC"));
		if($lvl){ $lvel = $lvl["level"];}else{ $lvel = 0;}
	}
	$sql="select level,reply from chatbot_hints where question like '%$txt%' and level='$lvel'";
	$res=mysqli_query($con,$sql);
	if(mysqli_num_rows($res)>0){
		$row=mysqli_fetch_assoc($res);
		$html=$row['reply'];
		//if($txt==0 || $txt==10){
			//$levl=0;
		//}else{
			$levl=$row['level']+1;
		//}
	}else{
		$html='Sorry not be able to understand you.<br/>Type "0" or "hi", To exit type "10"';
		//$levl=$lvel;
		$levl=0;
	}
}else{
	//$html="Sorry not be able to understand you.";
	$html="Dear User, your session has ended as we have not received any input from you, to start chat again please type hi to us";
	$levl=0;
}
$added_on=date('Y-m-d H:i:s');
mysqli_query($con,"insert into message(level,message,added_on,type,browserid) values('$levl','$txt','$added_on','user','$bws')");
$added_on=date('Y-m-d H:i:s');
mysqli_query($con,"insert into message(level,message,added_on,type,browserid) values('$levl','$html','$added_on','bot','$bws')");
echo $html;
?>