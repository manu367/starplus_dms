<?php
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'post_functions.php';
$pst = new POST_Functions();

$reflection_class = new ReflectionClass($pst);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($pst);
//print_r($conn);
//mysqli_query($conn,"INSERT INTO api_json SET api_name='TA DA', api_nature='REQUEST', request_json='".$data."', response_json='', entry_by='".$ucode."', entry_date='".date("Y-m-d H:i:s")."'");