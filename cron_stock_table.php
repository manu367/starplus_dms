<?php
//require_once("config/dbconnect.php");
$today=date("Y-m-d");

$user_exist_table='stock_status';
$user_new_table="`stock_status".$today."`";

$create=mysqli_query($link1,"CREATE TABLE $user_new_table LIKE $user_exist_table")or die("err-1".mysqli_error($link1));
$insertdata=mysqli_query($link1,"INSERT INTO $user_new_table SELECT * FROM $user_exist_table")or die("err-2".mysqli_error($link1));
?>