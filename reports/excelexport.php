<?php 
require_once("../config/config.php"); 
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".base64_decode($_REQUEST['rname'])."_".$today."_".$currtime .".xls");
header("Pragma: no-cache");
header("Expires: 0");
print("\n");
echo "\t \t ".base64_decode($_REQUEST['rheader'])." REPORT ON ".$today."\t";
print("\n");
include "../excelReports/".base64_decode($_REQUEST['rname']).".php";
?>