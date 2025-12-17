<?php 
/**  * Creates fault detail data as JSON  */ 
$a = array();
$b = array();
$b["ver_code"] = '1.0';
$b["ver_name"] = '1.1';
array_push($a,$b);
echo json_encode($a);
?>