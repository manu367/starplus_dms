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
$dlcode = $data->dealercode;
$prtcode = $data->partcode;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$dealer_code = $jwtf->validateParameter('DealerCode',$dlcode,STRING);
$part_code = $jwtf->validateParameter('PartCode',$prtcode,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_stocklist = $get->getStockList($dealer_code,$part_code);
		if ($res_stocklist != false){
			$a = array();
			$b = array();
			while($row = mysqli_fetch_array($res_stocklist)){
				$b["dealerCode"] = $row["asc_code"];
				$b["prodCode"] = $row["partcode"];
				$b["prodOkQty"] = $row["okqty"];
				$b["prodDamageQty"] = $row["broken"];
				$b["prodMissingQty"] = $row["missing"];
				$b["prodUom"] = $row["uom"];
				////// get product details
				$res_prod = $get->getProductDetail($row["partcode"],"productname,productcategory,productsubcat,productcolor,brand,hsn_code");
				$row_prod = mysqli_fetch_array($res_prod);
				$b["prodName"] = $row_prod["productname"];
				$b["prodColor"] = $row_prod["productcolor"];
				$b["prodHSN"] = $row_prod["hsn_code"];
				///// get product cat and subcat
				$res_psc = $get->getCatName($row_prod["productsubcat"]);
				$row_psc = mysqli_fetch_array($res_psc);
				$b["prodCat"] = $row_psc["product_category"];
				$b["prodSubCat"] = $row_psc["prod_sub_cat"];
				///// get brand
				$res_brand = $get->getBrandName($row_prod["brand"]);
				$row_brand = mysqli_fetch_array($res_brand);
				$b["prodBrand"] = $row_brand["make"];
				array_push($a,$b);
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "stocklist" => $a);
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