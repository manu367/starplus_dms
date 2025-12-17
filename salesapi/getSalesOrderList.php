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
$fdate = $data->fromdate;
$tdate = $data->todate;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$from_date = $jwtf->validateParameter('FromDate',$fdate,STRING);
$to_date = $jwtf->validateParameter('ToDate',$tdate,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_solist = $get->getSalesOrderList($user_code,$from_date,$to_date,"");
		if ($res_solist != false){
			$a = array();
			$b = array();
			while($row = mysqli_fetch_array($res_solist)){
				$b["refId"] = $row["id"];
				$b["refNo"] = $row["po_no"];
				$b["refDate"] = $row["entry_date"];
				$b["refTime"] = $row["entry_time"];
				$b["soValue"] = $row["po_value"];
				$b["soDiscount"] = $row["discount"];
				//////// get to location name
				$res_toloc = $get->getLocationName2($row["po_to"]);
				$row_toloc = mysqli_fetch_array($res_toloc);
				$b["soTo"] = $row_toloc["name"];
				//////// get from location name
				$res_fromloc = $get->getLocationName2($row["po_from"]);
				$row_fromloc = mysqli_fetch_array($res_fromloc);
				$b["soFrom"] = $row_fromloc["name"];
				$b["soStatus"] = $row["status"];
				$b["soRemark"] = $row["remark"];
				array_push($a,$b);
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "solist" => $a);
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