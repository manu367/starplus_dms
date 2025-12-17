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
$tkno = $data->ticketno;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$ticket_no = $jwtf->validateParameter('TicketNo',$tkno,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_tickethist = $get->getTicketHist($user_code,$ticket_no);
		if ($res_tickethist != false){
			$a = array();
			$b = array();
			while($row = mysqli_fetch_array($res_tickethist)){
				$b["ticketNo"] = $row["ticket_no"];
				$b["activity"] = $row["activity_name"];
				$b["status"] = $row["status"];
				$b["remark"] = $row["remark"];
				$b["entryBy"] = $row["entry_by"];
				$b["entryDate"] = $row["entry_date"];
				///// check if image is available
				if($row["attachment"]){
					$attach_img = ATTACHMENT_URL."ticketimg/".substr($row["entry_date"],0,7)."/".$row["attachment"];
				}else{
					$attach_img = null;
				}
				$b["attachment"] = $attach_img;
				array_push($a,$b);
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "ticketno" => $ticket_no, "tickethist" => $a);
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