<?php
require_once("../config/dbconnect.php");
////// get only selected data on the typing basis written by shekhar on 17 oct 2022
if($_POST['requestFor']=="customer"){
	if(!isset($_POST['searchCust'])){ 
  		$fetchData = mysqli_query($link1,"SELECT a.location_id, b.name, b.city, b.state, b.id_type FROM access_location a, asc_master b WHERE a.uid='".$_POST['userid']."' AND a.status='Y' AND a.location_id=b.asc_code AND b.id_type IN ('DL','DS','RT') AND b.status='Active' ORDER BY b.name LIMIT 10");
	}else{ 
	  	$search = $_POST['searchCust'];
		$fetchData = mysqli_query($link1,"SELECT a.location_id, b.name, b.city, b.state, b.id_type FROM access_location a, asc_master b WHERE a.uid='".$_POST['userid']."' AND a.status='Y' AND a.location_id=b.asc_code AND b.id_type IN ('DL','DS','RT')  AND b.status='Active' AND (b.name like '%".$search."%' OR b.asc_code like '%".$search."%') ORDER BY b.name LIMIT 10");
	}
	$data = array();
	while ($row = mysqli_fetch_array($fetchData)) {    
  		$data[] = array("id"=>$row['location_id'], "text"=>$row['name']." | ".$row['city']." | ".$row['state']." | ".$row['location_id']);
	}
	echo json_encode($data);
}

////// get only selected data on the typing basis written by shekhar on 17 oct 2022
if($_POST['requestFor']=="product"){
	if(!isset($_POST['searchProd'])){ 
  		$fetchData = mysqli_query($link1,"SELECT productcode,productname,model_name FROM product_master WHERE status='Active' ORDER BY productname LIMIT 10");
	}else{ 
	  	$search = $_POST['searchProd'];
		$fetchData = mysqli_query($link1,"SELECT productcode,productname,model_name FROM product_master WHERE status='Active' AND (productname like '%".$search."%' OR model_name like '%".$search."%') ORDER BY productname LIMIT 10");
	}
	$data = array();
	while ($row = mysqli_fetch_array($fetchData)) {    
  		$data[] = array("id"=>$row['productcode'], "text"=>$row['productname']." | ".$row['model_name']." | ".$row['productcode']);
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
?>