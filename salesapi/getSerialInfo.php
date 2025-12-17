<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions.php';
$get = new GET_Functions();
////// get JSON data
$data = json_decode(file_get_contents("php://input"));
//$uid = $data->userid;
//$ucode = $data->usercode;
$sno = $data->serialno;
$ak = $data->accessKey;
//// validate parameter
//$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
//$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$serial_no = $jwtf->validateParameter('SerialNo',$sno,STRING,true);
$access_key = $jwtf->validateParameter('Access Key',$ak,STRING,true);
try{
	////// get JWT token
	//$token = $jwtf->getBearerToken();
	///// validate token
	//$decode_resp = $jwtf->decodeJWT($token,$user_id);
	//if($decode_resp == "SUCCESS_RESPONSE"){
	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		if($access_key==ACCESS_KEY){
			$res_serial = $get->getSerialInfo($serial_no);
			if ($res_serial != false){
				$a = array();
				$b = array();
				$c = array();
				while($row_serial = mysqli_fetch_array($res_serial)){
					$b['locationCode'] = $row_serial['owner_code'];
					$b['locationName'] = $row_serial['owner_code'];
					$b['prodCode'] = $row_serial['prod_code'];
					/////get product info
					$res_prodinfo = $get->getProductDetail($row_serial['prod_code'],"productname,model_name,productcategory,productsubcat,hsn_code,brand");
					$row_prodinfo = mysqli_fetch_array($res_prodinfo);
					$b['prodName'] = $row_prodinfo['productname'];
					/////////////cat info
					$res_pscinfo = $get->getCatName($row_prodinfo['productsubcat']);
					$row_pscinfo = mysqli_fetch_array($res_pscinfo);
					$b['productCat'] = $row_pscinfo['product_category'];
					$b['productCatId'] = $row_prodinfo['productcategory'];
					$b['productSubCat'] = $row_pscinfo['prod_sub_cat'];
					$b['productSubCatId'] = $row_prodinfo['productsubcat'];
					/////brand name
					$res_brand = $get->getBrandName($row_prodinfo['brand']);
					$row_brand = mysqli_fetch_array($res_brand);
					$b['brand'] = $row_brand['make'];
					$b['brandId'] = $row_prodinfo['brand'];
					$b['model'] = $row_prodinfo['model_name'];
					
					$b['refNo'] = $row_serial['doc_no'];
					$b['refDate'] = $row_serial['transaction_date'];
					$b['importDate'] = $row_serial['import_date'];
					//$res_mod = $get->getmodelDetail($row_serial['prod_code'],"wp");
					//$rowmod = mysqli_fetch_array($res_mod);
					//print_r($rowmod);
					array_push($a,$b);
				}
				$c = array("serialinfo" => $a);
				$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$c);
			}else{
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Something went wrong");
			}
		}else{
			$jwtf->returnResponse(ACCESS_TOKEN_ERROR,$pager,"Invalid Access Key");
		}
	}else{
		$jwtf->returnResponse(REQUEST_METHOD_NOT_VALID,$pager,"Method Not Allowed");
	}	
	//}else{
		//$decode_resp;
	//}
}catch(Exception $e){
	$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
}
?>