<?php
include("sendotp_secureapp.php");
$mobileno = "8130960758";
$emailid = "";
$deviceid = "1234567890";
$projectcode = "VOLCS001";
$otp="123456";
$otpdate="2024-11-11";
$gentime="12:51:00";
$exptime="12:52:30";
$lat="";
$lng="";
$address="";
////get bearer token
$resp1 = getBearerToken($mobileno,$deviceid);
$resp1 = json_decode($resp1);
$token = $resp1->response->message->token;
/// now send project otp
$resp2 = sendOtpToSecureApp($mobileno,$emailid,$deviceid,$projectcode,$lat,$lng,$address,$token,$otp,$otpdate,$gentime,$exptime);
$resp2 = json_decode($resp2);
//print_r($resp2);
echo $otp = $resp2->response->message->otp_info->otp;
?>
