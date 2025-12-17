<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions.php';
$get = new GET_Functions();
////// get JSON data
/*$data = json_decode(file_get_contents("php://input"));
$uid = $data->userid;
$payrefno = $data->refno;
$fdate = $data->fromdate;
$tdate = $data->todate;*/
$uid = $_REQUEST["userid"];
$payrefno = $_REQUEST["refno"];
$fdate = $_REQUEST["fromdate"];
$tdate = $_REQUEST["todate"];
//// validate parameter
$user_id = $jwtf->validateParameter('Userid',$uid,STRING);
$pay_refno = $jwtf->validateParameter('PaymentRefNo.',$payrefno,STRING);
$from_date = $jwtf->validateParameter('FromDate',$fdate,STRING);
$to_date = $jwtf->validateParameter('ToDate',$tdate,STRING);
try{
	////// get JWT token
	//$token = $jwtf->getBearerToken();
	///// validate token
	//$decode_resp = $jwtf->decodeJWT($token,$user_id);
	//if($decode_resp == "SUCCESS_RESPONSE"){
		if($pay_refno=="" && $from_date==""){
			$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Date range or payment ref. no. should not be blank");
		}else{
			$resp_payref = $get->getPaymentVoucher($pay_refno,$from_date,$to_date);
			if (is_array($resp_payref)){
				$a = array("paymentVoucherJson" => $resp_payref);
				$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);    
			}
		}		
	/*}else{
		$decode_resp;
	}*/
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>