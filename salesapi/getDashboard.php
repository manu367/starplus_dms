<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions.php';
$get = new GET_Functions();

/*$reflection_class = new ReflectionClass($get);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($get);*/

//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$_REQUEST["userid"],STRING);
$user_code = $jwtf->validateParameter('UserCode',$_REQUEST["usercode"],STRING);
///////validate filter parameter
$sel_month = $jwtf->validateParameter('SelMonth',$_REQUEST["selmonth"],STRING);
$sel_year = $jwtf->validateParameter('SelYear',$_REQUEST["selyear"],STRING);
///// make date filter string
$date_filter = $sel_year."-".str_pad($sel_month,2,0,STR_PAD_LEFT);
//mysqli_query($conn,"INSERT INTO api_json SET api_name='DASHBOARD', api_nature='REQUEST', request_json='".$_REQUEST["userid"]."-".$_REQUEST["usercode"]."-".$_REQUEST["selmonth"]."-".$_REQUEST["selyear"]."', response_json='', entry_by='".$user_code."', entry_date='".date("Y-m-d H:i:s")."'");
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		/////// get Dealer/Customer count
		$res_dealercount = $get->getDealerCount($user_id,$user_code,$date_filter);
		$row_dealercount = mysqli_fetch_array($res_dealercount);
		/////// get Dealer/Customer visits count
		$res_dealervisitcount = $get->getDealerVisitCount($user_id,$user_code,"",$date_filter);
		$row_dealervisitcount = mysqli_fetch_array($res_dealervisitcount);
		/////// get sales order count
		$res_socount = $get->getSOCount($user_id,$user_code,$date_filter);
		$row_socount = mysqli_fetch_array($res_socount);
		/////// get leave count
		$res_leavecount = $get->getLeaveCount($user_id,$user_code,$date_filter);
		$row_leavecount = mysqli_fetch_array($res_leavecount);
		/////// get new visits count
		$res_newdealervisitcount = $get->getDealerVisitCount($user_id,$user_code,"New",$date_filter);
		$row_newdealervisitcount = mysqli_fetch_array($res_newdealervisitcount);
		/////// get sales order amount
		$res_soamtcount = $get->getSOCount($user_id,$user_code,$date_filter);
		$row_soamtcount = mysqli_fetch_array($res_soamtcount);
		/////// get hot lead count
		$res_hotleadcount = $get->getLeadCount($user_id,$user_code,$date_filter,"'36'");
		$row_hotleadcount = mysqli_fetch_array($res_hotleadcount);
		/////// get open lead count
		$res_openleadcount = $get->getLeadCount($user_id,$user_code,$date_filter,"'7','14','18','26','27','28','29','30','31','32','33','34','35','36','37','38'");
		$row_openleadcount = mysqli_fetch_array($res_openleadcount);
		/////// get invoiced count
		$res_invcount = $get->getInvCount($user_id,$user_code,$date_filter);
		$row_invcount = mysqli_fetch_array($res_invcount);
		/////////
		$a = array();
		$b = array();			
		$b["dealercount"] = empty($row_dealercount[0])?"0":$row_dealercount[0];
		$b["visitcount"] = empty($row_dealervisitcount[0])?"0":$row_dealervisitcount[0];
		$b["ordercount"] = empty($row_socount[0])?"0":$row_socount[0];
		$b["leavecount"] = empty($row_leavecount[0])?"0":$row_leavecount[0];
		$b["newvisitcount"] = empty($row_newdealervisitcount[0])?"0":$row_newdealervisitcount[0];
		$b["orderamount"] = empty($row_soamtcount[1])?"0":$row_soamtcount[1];
		$b["hotleadcount"] = empty($row_hotleadcount[0])?"0":$row_hotleadcount[0];
		$b["openleadcount"] = empty($row_openleadcount[0])?"0":$row_openleadcount[0];
		$b["invoicedcount"] = empty($row_invcount[0])?"0":$row_invcount[0];
		array_push($a,$b);
		///// for target vs acheivement
		////// get last 12 month from current
		$last_12month = $get->getLast12Months();
		////// make last 12 month data
		$ym_array = array();
		for($l=0; $l<count($last_12month[0]); $l++){
			////////
			$res_tarach = $get->getTargetAcheivement($user_id,$user_code,$last_12month[0][$l]);
			$row_tarach = mysqli_fetch_array($res_tarach);
			////
			//$ym_array[$last_12month[0][$l]]["yearmonth"] = $last_12month[1][$l];
			//$ym_array[$last_12month[0][$l]]["target"] = empty($row_tarach[0])?"0":$row_tarach[0];
			//$ym_array[$last_12month[0][$l]]["acheivement"] = empty($row_tarach[1])?"0":$row_tarach[1];
			$ym_array[$l]["yearmonth"] = $last_12month[1][$l];
			$ym_array[$l]["target"] = empty($row_tarach[0])?"0":$row_tarach[0];
			$ym_array[$l]["acheivement"] = empty($row_tarach[1])?"0":$row_tarach[1];
		}
		
		$c = array("userid" => $user_id, "usercode" => $user_code, "selmonth" => $sel_month, "selyear" => $sel_year, "countarray" => $a, "targetacheivement" => $ym_array);
		$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$c);
		
	}else{
		$decode_resp;
	}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>