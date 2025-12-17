<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
$state=$_REQUEST['state']; 
$a = array();  
$b = array();
$users = $db->getCity($state);
if(mysqli_num_rows($users)>0){
	while($row = mysqli_fetch_array($users)){ 
	
	$b["state"]=$row["state"];
	$b["cityid"] = $row["id"];
	$b["city"] = $row["city"];
	array_push($a,$b);
	}
}else{
	$b["status"]=0;
	$b["error_code"]=201;
}

echo json_encode($a);    
?>