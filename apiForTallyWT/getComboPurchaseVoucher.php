<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions.php';
$get = new GET_Functions();
////// get JSON data
/*$data = json_decode(file_get_contents("php://input"));
$uid = $data->userid;
$docno = $data->grnno;
$fdate = $data->fromdate;
$tdate = $data->todate;
*/
$uid = $_REQUEST["userid"];
$docno = $_REQUEST["grnno"];
$fdate = $_REQUEST["fromdate"];
$tdate = $_REQUEST["todate"];
//// validate parameter
$user_id = $jwtf->validateParameter('Userid',$uid,STRING);
$doc_no = $jwtf->validateParameter('GrnNo.',$docno,STRING);
$from_date = $jwtf->validateParameter('FromDate',$fdate,STRING);
$to_date = $jwtf->validateParameter('ToDate',$tdate,STRING);
try{
	////// get JWT token
	//$token = $jwtf->getBearerToken();
	///// validate token
	//$decode_resp = $jwtf->decodeJWT($token,$user_id);
	//if($decode_resp == "SUCCESS_RESPONSE"){
		if($doc_no=="" && $from_date==""){
			$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Date range or GRN no. should not be blank");
		}else{
			$resp_pv = $get->getComboPurchaseVoucher($doc_no,$from_date,$to_date);
			if (is_array($resp_pv)){
				$a = array("purchaseVoucherJson" => $resp_pv);
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