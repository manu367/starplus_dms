<?php
//require_once("config/dbconnect.php"); 
$apikey = "AIzaSyCi-i2A-i3LsZP6F4ngwkK5YsRGot9EEFQ";
$time_zone=time() + 0;	
date_default_timezone_set ("Asia/Calcutta");
$today = date("Y-m-d");
function getFullAddress($lat,$long,$apikey){
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$long.'&key='.$apikey,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	));
	
	$response = curl_exec($curl);
	
	curl_close($curl);
	$resp = json_decode($response,true);
	$address = $resp["results"][0]["formatted_address"];
	return $address;
}
////// get blank address from user track table
$i = 0;
$res_usrtck = mysqli_query($link1,"SELECT id,userid,latitude,longitude FROM user_track WHERE address='' AND latitude!='' AND longitude!='' AND entry_date LIKE '".$today."'");
if(mysqli_num_rows($res_usrtck)>0){
	while($row_usrtck = mysqli_fetch_assoc($res_usrtck)){
		$addres = "";
		$url = "";
		//// check in gapi table
		$res_chk = mysqli_query($link1,"SELECT response_data FROM gapi_address_request WHERE latitude='".$row_usrtck["latitude"]."' AND longitude='".$row_usrtck["longitude"]."'");
		if(mysqli_num_rows($res_chk)>0){
			$row_chk = mysqli_fetch_assoc($res_chk);
			$addres = $row_chk["response_data"];
		}else{
			///getaddress
			$addres = getFullAddress($row_usrtck["latitude"],$row_usrtck["longitude"],$apikey);
		}
		$res_upd = mysqli_query($link1,"UPDATE user_track SET address='".$addres."' WHERE id = '".$row_usrtck["id"]."'");
		
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$row_usrtck["latitude"].','.$row_usrtck["longitude"].'&key=AIzaSyD_e0ruO5-kbEWig_tz6xMYExypn9K_XNU';
		
		$res_req = mysqli_query($link1,"INSERT INTO gapi_address_request SET userid='".$row_usrtck["userid"]."', emp_id='".$row_usrtck["userid"]."', latitude='".$row_usrtck["latitude"]."', longitude='".$row_usrtck["longitude"]."', request_date='".$today."', api_name='geocode', request_data='".$url."',response_data='".$addres."',entry_by='CRON'");
		$i++;
	}
}
echo $i." records are updated";