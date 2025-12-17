<?php 
require_once("../config/config.php");
////// google api key
$apikey = "AIzaSyCi-i2A-i3LsZP6F4ngwkK5YsRGot9EEFQ";
$travelmode = "driving";
$req_date = base64_decode($_REQUEST["travel_date"]);
$req_user = base64_decode($_REQUEST["id"]);
////////////////
$origin = "";
$destination = "";
$waypoints = "";
$edate = $req_date;
$uid = $req_user;
$eid = $req_user;
///// check api is already called or not for same user on specific date
if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM gapi_request WHERE userid='".$uid."' AND request_date='".$edate."'"))==0){
	/// intialize array
	$lat_arr = array();
	$lng_arr = array();
	//////get user track details
	$res_usertrack = mysqli_query($link1,"SELECT latitude,longitude FROM user_track WHERE userid='".$uid."' AND entry_date='".$edate."' ORDER BY id ASC");
	while($row_usertrack = mysqli_fetch_assoc($res_usertrack)){
		$lat_arr[] = $row_usertrack["latitude"];
		$lng_arr[] = $row_usertrack["longitude"];
	}
	///// make origin
	$origin = $lat_arr[0].",".$lng_arr[0];
	///// make destination
	$destination = end($lat_arr).",".end($lng_arr);
	///// make waypoints
	for($j=1;$j<count($lat_arr);$j++){
		if($waypoints==""){
			$waypoints = $lat_arr[$j].",".$lng_arr[$j];
		}else{
			$waypoints .= "|".$lat_arr[$j].",".$lng_arr[$j];
		}
	}
	///// make requested URL
	$url = "https://maps.googleapis.com/maps/api/directions/json?origin=".$origin."&destination=".$destination."&waypoints=".$waypoints."&mode=".$travelmode."&key=".$apikey."&sensor=false";
	//////save request
	$res_req = mysqli_query($link1,"INSERT INTO gapi_request SET userid='".$uid."', emp_id='".$eid."', request_date='".$edate."', api_name='directions', request_data='".$url."',entry_by='".$_SESSION["userid"]."'");
	///start cURL
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$response = curl_exec($ch);
	curl_close($ch);
	$response_a = json_decode($response, true);
	//echo "<pre>";
	//print_r($response_a);
	$arr_wayps =$response_a["geocoded_waypoints"];
	//$arr_wayps =$response_a['routes'][0]["waypoint_order"];
	//echo "<br/>";
	$no_of_wayps = count($arr_wayps);
	//echo "<br/>";
	$dist = "";
	$from_addrs = "";
	$to_addrs = "";
	$resp_lat1 = "";
	$resp_lng1 = "";
	$resp_lat2 = "";
	$resp_lng2 = "";
	for($i=0;$i<($no_of_wayps-1);$i++){
		$from_addrs = $response_a['routes'][0]['legs'][$i]['start_address'];
		$to_addrs = $response_a['routes'][0]['legs'][$i]['end_address'];
		$resp_lat1 = $response_a['routes'][0]['legs'][$i]['start_location']['lat'];
		$resp_lng1 = $response_a['routes'][0]['legs'][$i]['start_location']['lng'];
		$resp_lat2 = $response_a['routes'][0]['legs'][$i]['end_location']['lat'];
		$resp_lng2 = $response_a['routes'][0]['legs'][$i]['end_location']['lng'];
		$dist = $response_a['routes'][0]['legs'][$i]['distance']['text'];
		$dist_in_mtr = $response_a['routes'][0]['legs'][$i]['distance']['value'];
		////// start saving response
		$res_api = mysqli_query($link1,"INSERT INTO google_api_response SET userid='".$uid."', emp_id='".$eid."', entry_date='".$edate."', api_name='directions', respdata1='".$from_addrs."', respdata2='".$to_addrs."', respdata3='".$dist."', distance='".$dist_in_mtr."', entry_by='".$_SESSION["userid"]."', latitude='".$resp_lat1."', longitude='".$resp_lng1."', latitude2='".$resp_lat2."', longitude2='".$resp_lng2."'");
		//echo "<br/>";
	}
}
header("Location:track_total_distance.php?isp_name=".$req_user."&fdate=".$req_date."&tdate=".$req_date."".$pagenav);
exit;
?>
