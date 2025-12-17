<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions.php';
$get = new GET_Functions();
////// get JSON data
//$data = json_decode(file_get_contents("php://input"));
//$ak = $data->accessKey;
//$req_date = $data->reqDate;
$ak = $_REQUEST['accessKey'];
$req_date = $_REQUEST['reqDate'];
//// validate parameter
$access_key = $jwtf->validateParameter('Access Key',$ak,STRING);
$reqdate = $jwtf->validateParameter('Requested Date',$req_date,STRING);
//try{
	////// get JWT token
	///$token = $jwtf->getBearerToken();
	///// validate token
	//$decode_resp = $jwtf->decodeJWT($token,$user_id);
	//if($decode_resp == "SUCCESS_RESPONSE"){
	if($_SERVER['REQUEST_METHOD'] === 'GET') {
     // The request is using the POST method
		if($access_key==ACCESS_KEY){
			if($reqdate==""){
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Requested Date should not be blank");
			}else{
				////// get sales order list
				$res_solist = $get->getSalesOrderList2("",$reqdate,$reqdate,"");
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
						$b["soToCode"] = $row["po_to"];
						//////// get from location name
						$res_fromloc = $get->getLocationName2($row["po_from"]);
						$row_fromloc = mysqli_fetch_array($res_fromloc);
						$b["soFrom"] = $row_fromloc["name"];
						$b["soFromCode"] = $row["po_from"];
						$b["soStatus"] = $row["status"];
						$b["soRemark"] = $row["remark"];
						///// getting item details
						//////for order data
						$c = array();
						$d = array();
						$res_sodata = $get->getSalesOrderData($user_code,$row["po_no"]);
						if(mysqli_num_rows($res_sodata)>0){
							while($row_sodata = mysqli_fetch_array($res_sodata)){
								$d["prodCode"] = $row_sodata["prod_code"];
								$d["soQty"] = $row_sodata["req_qty"];
								$d["soPrice"] = $row_sodata["po_price"];
								$d["soDiscount"] = $row_sodata["discount"];
								$d["soValue"] = $row_sodata["totalval"];
								$d["soUom"] = $row_sodata["uom"];
								////// get product details
								$res_prod = $get->getProductDetail($row_sodata["prod_code"],"productname,productcategory,productsubcat,brand");
								$row_prod = mysqli_fetch_array($res_prod);
								$d["prodName"] = $row_prod["productname"];
								///// get product cat and subcat
								$res_psc = $get->getCatName($row_prod["productsubcat"]);
								$row_psc = mysqli_fetch_array($res_psc);
								$d["prodCat"] = $row_psc["product_category"];
								$d["prodSubCat"] = $row_psc["prod_sub_cat"];
								///// get brand
								$res_brand = $get->getBrandName($row_prod["brand"]);
								$row_brand = mysqli_fetch_array($res_brand);
								$d["prodBrand"] = $row_brand["make"];
								array_push($c,$d);
							}
						}else{
							///// no history found
						}
						$b["soItems"] = $c;
						array_push($a,$b);
					}
					$e = array("solist" => $a);
					$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$e);
				}else{
					$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Something went wrong");
				}	
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
//}catch(Exception $e){
	//$jwtf->throwError(JWT_PROCESSING_ERROR,$e->getMessage());
//}
?>