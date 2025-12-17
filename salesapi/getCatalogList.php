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
//// validate parameter
$user_id = $jwtf->validateParameter('UserId',$uid,STRING);
$user_code = $jwtf->validateParameter('UserCode',$ucode,STRING);
try{
	////// get JWT token
	$token = $jwtf->getBearerToken();
	///// validate token
	$decode_resp = $jwtf->decodeJWT($token,$user_id);
	if($decode_resp == "SUCCESS_RESPONSE"){
		$res_catloglist = $get->getCatalog($user_code,"");
		if ($res_catloglist != false){
			$a = array();
			$b = array();
			$c = array();
			$d = array();
			$pc = array();
			$pc_atch = array();
			$psc = array();
			$psc_img = array();
			while($row = mysqli_fetch_array($res_catloglist)){
				$pc[$row['productid']] = $row['product_category'];
				$pc_atch[$row['productid']] = $row['attachment'];
				
				$psc[$row['productid']][$row['psubcatid']] = $row['prod_sub_cat'];
				$psc_img[$row['productid']][$row['psubcatid']] = $row['icon_img'];
			}
			foreach($pc as $pcid => $pcname){
				$d = array();
				$b["productCat"] = $pcname;
				$b["attachment"] = ATTACHMENT_URL.$pc_atch[$pcid];
				foreach($psc[$pcid] as $pscid => $pscname){
					$c["itemName"] = $pscname;
					$c["itemImg"] = ATTACHMENT_URL.$psc_img[$pcid][$pscid];
					array_push($d,$c);	
				}
				$b["productSubCat"] = $d;
				array_push($a,$b);
			}
			$c = array("userid" => $user_id, "usercode" => $user_code, "cataloglist" => $a);
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