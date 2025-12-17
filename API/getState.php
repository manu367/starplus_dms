<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
 
$a = array();  
$b = array();
$users = $db->getState();
if(mysqli_num_rows($users)>0){
	while($row = mysqli_fetch_array($users)){ 
	$b["stateid"] = $row["sno"];
	$b["state"]=$row["state"];
	array_push($a,$b);
	}
}else{
	$b["status"]=0;
	$b["error_code"]=201;
}

echo json_encode($a);    
?>