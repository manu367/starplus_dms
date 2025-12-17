<?php
require_once("dbconnect_cansaledms.php");
////// get only selected data on the typing basis written by shekhar on 17 oct 2022
if($_POST['requestFor']=="customer"){
	if(!isset($_POST['searchCust'])){ 
  		$fetchData = mysqli_query($link1,"SELECT a.location_id, b.name, b.city, b.state, b.id_type FROM access_location a, asc_master b WHERE a.uid='".$_POST['userid']."' AND a.status='Y' AND a.location_id=b.asc_code AND b.id_type IN ('DL','DS','RT') AND b.status='Active' ORDER BY b.name LIMIT 10");
	}else{ 
	  	$search = $_POST['searchCust'];
		$fetchData = mysqli_query($link1,"SELECT a.location_id, b.name, b.city, b.state, b.id_type FROM access_location a, asc_master b WHERE a.uid='".$_POST['userid']."' AND a.status='Y' AND a.location_id=b.asc_code AND b.id_type IN ('DL','DS','RT','SR')  AND b.status='Active' AND (b.name like '%".$search."%' OR b.asc_code like '%".$search."%') ORDER BY b.name LIMIT 10");
	}
	$data = array();
	while ($row = mysqli_fetch_array($fetchData)) {    
  		$data[] = array("id"=>$row['location_id'], "text"=>$row['name']." | ".$row['city']." | ".$row['state']." | ".$row['location_id']);
	}
	echo json_encode($data);
}

////// get only selected data on the typing basis written by shekhar on 17 oct 2022
if($_POST['requestFor']=="product"){
	///get access product
	$acc_psc = getAccessProduct($_POST['userid'],$link1);
	///get access brand
	$acc_brd = getAccessBrand($_POST['userid'],$link1);
	if(!isset($_POST['searchProd'])){ 
  		$fetchData = mysqli_query($link1,"SELECT productcode,productname,model_name FROM product_master WHERE status='Active' AND productsubcat IN (".$acc_psc.") AND brand IN (".$acc_brd.") ORDER BY productname LIMIT 10");
	}else{ 
	  	$search = $_POST['searchProd'];
		$fetchData = mysqli_query($link1,"SELECT productcode,productname,model_name FROM product_master WHERE status='Active' AND productsubcat IN (".$acc_psc.") AND brand IN (".$acc_brd.") AND (productname like '%".$search."%' OR model_name like '%".$search."%') ORDER BY productname LIMIT 10");
	}
	$data = array();
	while ($row = mysqli_fetch_array($fetchData)) {    
  		$data[] = array("id"=>$row['productcode'], "text"=>$row['productname']." | ".$row['model_name']." | ".$row['productcode']);
	}
	echo json_encode($data);
}
////// get only selected data on the typing basis written by shekhar on 16 feb 2023 for combo PO
if($_POST['requestFor']=="comboproduct"){
	///get access product
	//$acc_psc = getAccessProduct($_POST['userid'],$link1);
	///get access brand
	//$acc_brd = getAccessBrand($_POST['userid'],$link1);
	if(!isset($_POST['searchProd'])){ 
  		$fetchData = mysqli_query($link1,"SELECT bom_modelcode,bom_modelname FROM combo_master WHERE status='1' GROUP BY bom_modelcode LIMIT 10");
	}else{ 
	  	$search = $_POST['searchProd'];
		$fetchData = mysqli_query($link1,"SELECT bom_modelcode,bom_modelname FROM combo_master WHERE status='1' AND (bom_modelname like '%".$search."%' OR bom_modelcode like '%".$search."%') GROUP BY bom_modelcode LIMIT 10");
	}
	$data = array();
	while ($row = mysqli_fetch_array($fetchData)) {    
  		$data[] = array("id"=>$row['bom_modelcode'], "text"=>$row['bom_modelname']." | ".$row['bom_modelcode']);
	}
	echo json_encode($data);
}
////// get parent location data on the typing basis written by shekhar on 31 oct 2022
if($_POST['requestFor']=="parent"){
	if($_POST['loctypstr']=="DL"){ $strr = "id_type='DS' AND state='".$_POST['locstate']."'"; }else if($_POST['loctypstr']=="DS"){ $strr = "id_type='HO'";}else{ $strr = "user_level<'".$_POST['loctype']."'";}
	if(!isset($_POST['searchParent'])){ 
  		$fetchData = mysqli_query($link1,"SELECT asc_code,name,city,state FROM asc_master WHERE ".$strr." AND status='Active' ORDER BY name LIMIT 10");
	}else{ 
	  	$search = $_POST['searchParent'];
		$fetchData = mysqli_query($link1,"SELECT asc_code,name,city,state FROM asc_master WHERE ".$strr." AND status='Active' AND (name like '%".$search."%' OR asc_code like '%".$search."%') ORDER BY name LIMIT 10");
	}
	$data = array();
	while ($row = mysqli_fetch_array($fetchData)) {    
  		$data[] = array("id"=>$row['asc_code'], "text"=>$row['name']." | ".$row['city']." | ".$row['state']." | ".$row['asc_code']);
	}
	echo json_encode($data);
}
//// get access product written by shekhar on 12 oct 2022
function getAccessProduct($userid,$link1){
	$product_str="";
	$res_product=mysqli_query($link1,"SELECT prod_subcatid FROM mapped_productcat WHERE userid = '".$userid."' AND status='Y'")or die(mysqli_error($link1));
	if(mysqli_num_rows($res_product)>0){
		while($row_product=mysqli_fetch_assoc($res_product)){
		   if($product_str==""){
			   $product_str.="'".$row_product['prod_subcatid']."'";
		   }else{
			   $product_str.=",'".$row_product['prod_subcatid']."'";
			  
		   }
		}
	}else{
		$product_str="''";
	}
	return $product_str;
}
//// get access brand written by shekhar on 12 oct 2022
function getAccessBrand($userid,$link1){
	$brand_str="";
	$res_brand=mysqli_query($link1,"SELECT brand FROM mapped_brand WHERE userid = '".$userid."' AND status='Y'")or die(mysqli_error($link1));
	if(mysqli_num_rows($res_brand)>0){
		while($row_brand=mysqli_fetch_assoc($res_brand)){
		   if($brand_str==""){
			   $brand_str.="'".$row_brand['brand']."'";
		   }else{
			   $brand_str.=",'".$row_brand['brand']."'";
		   }
		}
	}else{
		$brand_str="''";
	}
	return $brand_str;
}
?>



