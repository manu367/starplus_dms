<?php
$time_zone=time() + 0;	
date_default_timezone_set ("Asia/Calcutta");
$a = array();     
$b = array();    
       
$today=date("Y-m-d");
$time=date("H:i:s");

$b["server_date"] = $today;
$b["server_time"] = $time; 

array_push($a,$b);         
      
echo json_encode($a);     

?>