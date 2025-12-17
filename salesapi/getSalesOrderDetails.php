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
$refno = $data->refno;
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
$ref_no = $jwtf->validateParameter('RefNo',$refno,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_somaster = $get->getSalesOrderList($user_code,"","",$ref_no);
		if ($res_somaster != false){
			$row = mysqli_fetch_array($res_somaster);
			//////// get to location name
			$res_toloc = $get->getLocationName2($row["po_to"]);
			$row_toloc = mysqli_fetch_array($res_toloc);
			//////// get from location name
			$res_fromloc = $get->getLocationName2($row["po_from"]);
			$row_fromloc = mysqli_fetch_array($res_fromloc);
			//////for order data
			$a = array();
			$b = array();
			$res_sodata = $get->getSalesOrderData($user_code,$ref_no);
			if(mysqli_num_rows($res_sodata)>0){
				while($row_sodata = mysqli_fetch_array($res_sodata)){
					$b["prodCode"] = $row_sodata["prod_code"];
					$b["soQty"] = $row_sodata["req_qty"];
					$b["soPrice"] = $row_sodata["po_price"];
					$b["soDiscount"] = $row_sodata["discount"];
					$b["soValue"] = $row_sodata["totalval"];
					$b["soUom"] = $row_sodata["uom"];
					////// get product details
				/*	$res_prod = $get->getProductDetail($row_sodata["prod_code"],"productname,productcategory,productsubcat,brand");
					$row_prod = mysqli_fetch_array($res_prod);
					$b["prodName"] = $row_prod["productname"];
					///// get product cat and subcat
					$res_psc = $get->getCatName($row_prod["productsubcat"]);
					$row_psc = mysqli_fetch_array($res_psc);
					$b["prodCat"] = $row_psc["product_category"];
					$b["prodSubCat"] = $row_psc["prod_sub_cat"];
					///// get brand
					$res_brand = $get->getBrandName($row_prod["brand"]);
					$row_brand = mysqli_fetch_array($res_brand);
					$b["prodBrand"] = $row_brand["make"];
					*/
					// upadted by ravi-02-03-2023 start
					
					if($row_sodata["warranty"]=='COMBO'){
						$res_prod = $get->getComboDetail($row_sodata["prod_code"],"bom_modelcode,bom_modelname");
						$row_prod = mysqli_fetch_array($res_prod);
						$b["prodName"] = $row_prod["bom_modelname"];
						$b["prodCat"]="";
						$b["prodSubCat"]="";
					$b["prodBrand"] = "";
						}else{
							$res_prod = $get->getProductDetail($row_sodata["prod_code"],"productname,productcategory,productsubcat,brand");	
							$row_prod = mysqli_fetch_array($res_prod);
							$b["prodName"] = $row_prod["productname"];
							$res_psc = $get->getCatName($row_prod["productsubcat"]);
								
					///// get product cat and subcat
					
					$row_psc = mysqli_fetch_array($res_psc);
					$b["prodCat"] = $row_psc["product_category"];
					$b["prodSubCat"] = $row_psc["prod_sub_cat"];
					///// get brand
							$res_brand = $get->getBrandName($row_prod["brand"]);
					$row_brand = mysqli_fetch_array($res_brand);
					$b["prodBrand"] = $row_brand["make"];
					}
						// upadted by ravi-02-03-2023 end
					array_push($a,$b);
				}
			}else{
				///// no history found
			}
			///// get invoice details
			if($row["dispatch_challan"]){
				$inv_status = $get->getAnyDetails($row["dispatch_challan"],"status","challan_no","billing_master");
			}else{
				$inv_status = "NA";
			}
			///////////////
			$c = array("userid" => $user_id, "usercode" => $user_code, "refId" => $row["id"], "refNo" => $row["po_no"], "refDate" => $row["entry_date"], "refTime" => $row["entry_time"], "soValue" => $row["po_value"], "soTo" => $row_toloc["name"], "soFrom" => $row_fromloc["name"], "soStatus" => $row["status"], "invStatus" => $inv_status, "sodataarray" => $a);
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