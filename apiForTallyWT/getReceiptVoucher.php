<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions.php';
$get = new GET_Functions();
////// get JSON data
/*$data = json_decode(file_get_contents("php://input"));
$uid = $data->userid;
$recptno = $data->receiptno;
$fdate = $data->fromdate;
$tdate = $data->todate;*/
$uid = $_REQUEST["userid"];
$recptno = $_REQUEST["receiptno"];
$fdate = $_REQUEST["fromdate"];
$tdate = $_REQUEST["todate"];
//// validate parameter
$user_id = $jwtf->validateParameter('Userid',$uid,STRING);
$receipt_no = $jwtf->validateParameter('ReceiptNo.',$recptno,STRING);
$from_date = $jwtf->validateParameter('FromDate',$fdate,STRING);
$to_date = $jwtf->validateParameter('ToDate',$tdate,STRING);
try{
	////// get JWT token
	//$token = $jwtf->getBearerToken();
	///// validate token
	//$decode_resp = $jwtf->decodeJWT($token,$user_id);
	//if($decode_resp == "SUCCESS_RESPONSE"){
		if($receipt_no=="" && $from_date==""){
			$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Date range or receipt no. should not be blank");
		}else{
			$resp_recpt = $get->getReceiptVoucher($receipt_no,$from_date,$to_date);
			if (is_array($resp_recpt)){
				$a = array("recptVoucherJson" => $resp_recpt);
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