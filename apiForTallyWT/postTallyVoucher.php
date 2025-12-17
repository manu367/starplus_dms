<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'post_functions.php';
$pst = new PST_Functions();
////// get JSON data
$respdata = json_decode(file_get_contents("php://input"),true);
/////json decode
$data_arr = $respdata["data"];
$pend_vch = array();
$a = array();
$b = array();
for($i=0; $i<count($data_arr); $i++){
	$vch_no = $data_arr[$i]["Doc_No"];
	$vch_date = $data_arr[$i]["Voucher_Date"];
	$vch_type = $data_arr[$i]["Voucher_Name"];
	$vch_name = $data_arr[$i]["Voucher_Type"];
	$rmk = "API POST";
	if($vch_no=="" || $vch_type==""){
		//$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Voucher No. and Voucher Type should not be blank");
		$pend_vch["Doc_No"] = $vch_no;
		$pend_vch["Resp_Msg"] = "Voucher No. or Voucher Type is blank";
		array_push($b,$pend_vch);
	}else{
		$resp_vch = $pst->postTallyResponse($vch_name,$vch_no,$vch_type,$vch_date,$rmk);
		if($resp_vch == "1"){
			$pend_vch["Doc_No"] = $vch_no;
			$pend_vch["Resp_Msg"] = "Success";
			array_push($b,$pend_vch);
		}else{
			$pend_vch["Doc_No"] = $vch_no;
			$pend_vch["Resp_Msg"] = "Not Processed";	
			array_push($b,$pend_vch);
			//$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);
		}
	}
}
echo json_encode(array("response"=>$b));
?>