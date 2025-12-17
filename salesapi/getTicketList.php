<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions.php';
$get = new GET_Functions();
////// get JSON data
$data = json_decode(file_get_contents("php://input"));
$uid = $data->userid;
$ucode = $data->usercode;
$lcode = $data->locationcode;
$fdate = $data->fromdate;
$tdate = $data->todate;
$tktno = $data->ticketno;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$location_code = $jwtf->validateParameter('LocationCode',$lcode,STRING);
$from_date = $jwtf->validateParameter('FromDate',$fdate,STRING);
$to_date = $jwtf->validateParameter('ToDate',$tdate,STRING);
$ticket_no = $jwtf->validateParameter('TicketNo',$tktno,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_ticketlist = $get->getTicketList($user_code,$location_code,$from_date,$to_date,$ticket_no);
		if ($res_ticketlist != false){
			$a = array();
			$b = array();
			while($row = mysqli_fetch_array($res_ticketlist)){
				$b["ticketId"] = $row["id"];
				$b["ticketNo"] = $row["ticket_no"];
				$b["ticketDate"] = $row["ticket_date"];
				$b["customerType"] = $row["customer_type"];
				$b["customerName"] = $row["customer_name"];
				$b["customerPhone"] = $row["customer_phone"];
				$b["customerAddress"] = $row["customer_address"];
				$b["customerPhone"] = $row["customer_phone"];
				$b["customerCity"] = $row["customer_city"];
				$b["customerState"] = $row["customer_state"];
				$b["customerPincode"] = $row["customer_pincode"];
				$b["problemReport"] = $row["problem_report"];
				$b["product"] = $row["product"];
				$b["model"] = $row["model"];
				$b["ticketStatus"] = $row["status"];
				$b["remark"] = $row["remark"];
				$b["locationCode"] = $row["location_code"];
				///// check if image is available
				if($row["attach_img1"]){
					$attach_img1 = ATTACHMENT_URL."ticketimg/".substr($row["ticket_date"],0,7)."/".$row["attach_img1"];
				}else{
					$attach_img1 = null;
				}
				if($row["attach_img2"]){
					$attach_img2 = ATTACHMENT_URL."ticketimg/".substr($row["ticket_date"],0,7)."/".$row["attach_img2"];
				}else{
					$attach_img2 = null;
				}
				if($row["attach_img3"]){
					$attach_img3 = ATTACHMENT_URL."ticketimg/".substr($row["ticket_date"],0,7)."/".$row["attach_img3"];
				}else{
					$attach_img3 = null;
				}
				$b["img1"] = $attach_img1;
				$b["img2"] = $attach_img2;
				$b["img3"] = $attach_img3;
				array_push($a,$b);
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "ticketlist" => $a);
			$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$c);
		}else{
			$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Something went wrong");
		}			
	}else{
		$decode_resp;
	}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>