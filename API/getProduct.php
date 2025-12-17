<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
 
$a = array();  
$b = array();
$users = $db->getProduct();
if(mysqli_num_rows($users)>0){
	while($row = mysqli_fetch_array($users)){ 
	$b["productid"] = $row["id"];
	$b["product_code"]=$row["code"];
	array_push($a,$b);
	}
}else{
	$b["status"]=0;
	$b["error_code"]=201;
}

echo json_encode($a);    
?>