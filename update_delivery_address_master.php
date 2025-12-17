<?php
//require_once("config/dbconnect.php");
$res_dam = mysqli_query($link1,"SELECT * FROM delivery_address_master WHERE location_code=''");
while($row_dam = mysqli_fetch_assoc($res_dam)){
	////select state
	$res_asc = mysqli_query($link1,"SELECT asc_code FROM asc_master WHERE name = '".$row_dam['party_name']."'");
	$row_asc = mysqli_fetch_assoc($res_asc);
	if($row_asc['asc_code']){
		////// count max no. of location in selected state
	   $query_code="SELECT COUNT(id) as qa FROM delivery_address_master WHERE location_code='".$row_asc['asc_code']."'";
	   $result_code=mysqli_query($link1,$query_code)or die("ER2".mysqli_error($link1));
	   $arr_result2=mysqli_fetch_array($result_code);
	   $code_id=$arr_result2[0];
	   /// make 3 digit padding
	   $pad=str_pad(++$code_id,2,"0",STR_PAD_LEFT);
	   //// make logic of location code
	   $newlocationcode=strtoupper($row_asc['asc_code'])."-AD".$pad;
		///// insert in price master
		mysqli_query($link1,"UPDATE delivery_address_master SET location_code='".$row_asc['asc_code']."' ,address_code='".$newlocationcode."' WHERE id='".$row_dam["id"]."'");
	}
}
?>