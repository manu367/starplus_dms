<?php
//////// function for displaying error message
function errorMsg($errorcode){
	if($errorcode=="1") {
		$msg = "User Name Or Password Wrong! Please Try Again!";
	} else if($errorcode=="2") {
		$msg = "Session Expired! Please Login Again!";
	} else if($errorcode=="3") {
		$msg = "You have Successfuly Logged Out";
	} else if($errorcode=="4") {
		$msg = "Access denied . You are not authorized to access this directory.";
	} else if($errorcode=="5") {
		$msg = "Server is under maintenance. Please try after sometime.";
	} else if($errorcode=="6") {
		$msg = "OTP is Expired. Please Send OTP Again!";
	} else if($errorcode=="7") {
		$msg = "OTP is mismatched. Please enter valid OTP !";
	} else if($errorcode=="8") {
		$msg = "OTP is already used. Please Send OTP Again!";
	} else if($errorcode=="9") {
		$msg = "OTP is Expired. Please Send OTP Again!";
	} else{ 
	    $msg = $errorcode;
	}
	return $msg;
}
//////////// Function2 to release the purchase qty from current stock ////////
function releaseStock($locationcode,$sublocation,$prodcode,$okqty,$damageqty,$missingqty,$link1,$errorflag){
	$flag=$errorflag;
	$query="UPDATE stock_status set okqty=okqty-'".$okqty."',broken=broken-'".$damageqty."',missing=missing-'".$missingqty."' where asc_code='".$locationcode."' and partcode='".$prodcode."' and sub_location='".$sublocation."'";
	$result=mysqli_query($link1,$query);
	//// check if query is not executed
    if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
	}
	return $flag;
}
//////// function to get discount type description /////
function getDiscountType($discountcode){
	if($discountcode=="PD"){
		$returnstr="Productwise Discount";
	}else if($discountcode=="TD"){
		$returnstr="Total Discount";
	}else{
		$returnstr=$discountcode;
	}
	return $returnstr;
}
//////// function to get discount type description /////
function getTaxType($taxcode){
	if($taxcode=="PT"){
		$returnstr="Productwise Tax";
	}else if($taxcode=="TT"){
		$returnstr="Total Tax";
	}else{
		$returnstr=$taxcode;
	}
	return $returnstr;
}
/////////// function to clean data///////
function cleanData($instr) {
$str=trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($instr))))));
return $str;
}
//////// set currency format /////
function currencyFormat($amttt){
	return number_format($amttt,'2','.','');
}
function currencyFormat2($amttt){
	return number_format($amttt,'2','.','');
}
///////////////// Daily Transaction history 
function transactionHistory($ref_no,$ref_date,$entry_date,$entry_by,$location_code,$party_code,$transaction_type,$action_taken,$amount,$crdr,$ac_id,$ac_type,$link1){
	mysqli_query($link1,"INSERT INTO day_book_entries set ref_no='$ref_no', ref_date='$ref_date', entry_date='$entry_date', entry_by='$entry_by', location_code='$location_code', party_code='$party_code', transaction_type='$transaction_type', action_taken='$action_taken', amount='$amount', cr_dr='$crdr', ac_id='$ac_id', ac_type='$ac_type'") or die("Error in saving T.H.".mysql_error());
}
///////////////  date format like DD-MM-YYYY ////////////////
function dt_format($dt_sel){
  return substr($dt_sel,8,2)."-".substr($dt_sel,5,2)."-".substr($dt_sel,0,4);
}
function dttime_format($dt_sel){
  return substr($dt_sel,8,2)."-".substr($dt_sel,5,2)."-".substr($dt_sel,0,4)." ".substr($dt_sel,11,8);
}
///////////////  date format like MM/DD/YYYY ////////////////
function dt_format2($dt_sel){
  return substr($dt_sel,5,2)."/".substr($dt_sel,8,2)."/".substr($dt_sel,0,4);
}
////////// function to calculate day difference between two dates //////////
function daysDifference($endDate, $beginDate){
	$date_parts1=explode("-", $beginDate); $date_parts2=explode("-", $endDate);
	$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
	$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
	return $end_date - $start_date;
}
//////// function to capture daily activities /////
function dailyActivity($uid,$refno,$activityType,$actionTaken,$systemIp,$link1,$errorflag){
	$todayDate=date("Y-m-d");
	$todayTime=date("H:i:s");
	///////////////// for backdate entry written by shekhar on 29 mar 23
	/*$backdate = "2023-03-31";
	$arrbypassdate = array("2023-04-01","2023-04-02");
	if(in_array($todayDate, $arrbypassdate))
	{
		$todayDate = $backdate;
	}*/
	$flag=$errorflag;
	$query="INSERT INTO daily_activities set userid='".$uid."',ref_no='".$refno."',activity_type='".$activityType."',action_taken='".$actionTaken."',update_date='".$todayDate."',update_time='".$todayTime."',system_ip='".$systemIp."'";
	$result=mysqli_query($link1,$query);
	//// check if query is not executed
    if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
	}
	return $flag;
}
//////////// Function to hold the purchase qty in current stock ////////
function holdStockQty($locationcode,$prodcode,$reqqty,$link1,$errorflag){
	$flag=$errorflag;
	$query="UPDATE stock_status set hold_okqty=hold_okqty+'".$reqqty."' where asc_code='".$locationcode."' and partcode='".$prodcode."'";
	$result=mysqli_query($link1,$query);
	//// check if query is not executed
    if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
	}
	return $flag;
}
//////////// Function to release the purchase qty from current stock ////////
function releaseStockQty($locationcode,$prodcode,$reqqty,$link1,$errorflag){
	$flag=$errorflag;
	$query="UPDATE stock_status set hold_okqty=hold_okqty-'".$reqqty."' where asc_code='".$locationcode."' and partcode='".$prodcode."'";
	$result=mysqli_query($link1,$query);
	//// check if query is not executed
    if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
	}
	return $flag;
}
//////////// Function to release the stock qty from current stock written by shekhar on 29 JUL 22 ////////
function releaseStockQtyNew($locationcode,$prodcode,$reqqty,$link1,$errorflag){
	$flag=$errorflag;
	$query="UPDATE stock_status set hold_okqty=hold_okqty-'".$reqqty."' where asc_code='".$locationcode."' and partcode='".$prodcode."'";
	$result=mysqli_query($link1,$query);
	//// check if query is not executed
    if (!$result) {
	     $flag = false;
         $msg = "Error detailsRS: " . mysqli_error($link1) . ".";
	}
	return $flag."~".$msg;
}
//////////// Function to get current stock ////////
function getCurrentStock($locationcode,$prodcode,$fieldval,$link1){
   // echo "SELECT ".$fieldval." from stock_status where asc_code='".$locationcode."' and partcode='".$prodcode."'";die();
	$query="SELECT ".$fieldval." from stock_status where asc_code='".$locationcode."' and partcode='".$prodcode."'";
	$result=mysqli_query($link1,$query);
	if(mysqli_num_rows($result)>0){
		$rowstock = mysqli_fetch_array($result);
		return $rowstock[0];
	}else{
		return 0;
	}
	
}
//////////// Function to get current stock ////////
function getCurrentStockNew($locationcode,$godown,$prodcode,$fieldval,$link1){
   // echo "SELECT ".$fieldval." from stock_status where asc_code='".$locationcode."' and partcode='".$prodcode."'";die();
	$query="SELECT ".$fieldval." from stock_status where asc_code='".$locationcode."' and sub_location='".$godown."' and partcode='".$prodcode."'";
	$result=mysqli_query($link1,$query);
	if(mysqli_num_rows($result)>0){
		$rowstock = mysqli_fetch_array($result);
		return $rowstock[0];
	}else{
		return 0;
	}
	
}
//////// function to get credit balance of party /////
function getCRBAL($partyid,$parentid,$link1){
  if($partyid!='' && $parentid!=''){
	$res_crbal=mysqli_query($link1,"SELECT total_cr_limit FROM current_cr_status where parent_code='".$parentid."' and asc_code='".$partyid."'");
	$row_crbal=mysqli_fetch_assoc($res_crbal);
	if($row_crbal['total_cr_limit']){
	    return $row_crbal['total_cr_limit'];
	}else{
		return "0.00";
	}
  }else{
	return "0.00";
  }
}
/////// function to get admin user details
function getAdminDetails($adminid,$fields,$link1){
   $explodee=explode(",",$fields);
   $user_details=mysqli_fetch_array(mysqli_query($link1,"select $fields from admin_users where username='$adminid'"));
   $rtn_str="";
   for($k=0;$k < count($explodee);$k++){
       if($rtn_str==""){
          $rtn_str.=$user_details[$k];
	   }
       else{
          $rtn_str.="~".$user_details[$k];
	   }
   }
   return $rtn_str;
}
/////// function to get Location  details
function getLocationDetails($locid,$fields,$link1){
   $explodee=explode(",",$fields);
   $user_details=mysqli_fetch_array(mysqli_query($link1,"select $fields from asc_master where asc_code='$locid'"));
   $rtn_str="";
   for($k=0;$k < count($explodee);$k++){
       if($rtn_str==""){
          $rtn_str.=$user_details[$k];
	   }
       else{
          $rtn_str.="~".$user_details[$k];
	   }
   }
   return $rtn_str;
}
/////// function to get Location  details
function getVendorDetails($vendorid,$fields,$link1){
   $explodee=explode(",",$fields);
   $user_details=mysqli_fetch_array(mysqli_query($link1,"select $fields from vendor_master where id='$vendorid'"));
   $rtn_str="";
   for($k=0;$k < count($explodee);$k++){
       if($rtn_str==""){
          $rtn_str.=$user_details[$k];
	   }
       else{
          $rtn_str.="~".$user_details[$k];
	   }
   }
   return $rtn_str;
}
/////// function to get Retail customers details
function getCustomerDetails($customerid,$fields,$link1){
   $explodee=explode(",",$fields);
   $user_details=mysqli_fetch_array(mysqli_query($link1,"select $fields from customer_master where customerid='$customerid'"));
   $rtn_str="";
   for($k=0;$k < count($explodee);$k++){
       if($rtn_str==""){
          $rtn_str.=$user_details[$k];
	   }
       else{
          $rtn_str.="~".$user_details[$k];
	   }
   }
   return $rtn_str;
}
/////// function to get Product details
function getProductDetails($productid,$fields,$link1){
   $explodee=explode(",",$fields);
   $prod_details=mysqli_fetch_array(mysqli_query($link1,"select $fields from product_master where productcode='$productid'"));
   $rtn_str="";
   for($k=0;$k < count($explodee);$k++){
       if($rtn_str==""){
          $rtn_str.=$prod_details[$k];
	   }
       else{
          $rtn_str.="~".$prod_details[$k];
	   }
   }
   return $rtn_str;
}
//////// function to get location type /////
function getLocationType($locationtype,$link1){
	$query="SELECT locationname FROM location_type where locationtype='".$locationtype."' or seq_id='".$locationtype."'";
	$result=mysqli_query($link1,$query);
	$row=mysqli_fetch_assoc($result);
	return $row['locationname'];
}
/////// get location ///////
function getGroupName($groupid,$link1){
	$group_name=mysqli_fetch_array(mysqli_query($link1,"select group_name from group_master where group_id='".$groupid."'"));
	return $group_name['group_name'];
}
///////////////////////////
/////// get location ///////
function getParentLocation($childid,$link1){
	$parent_str="";
	$res_parent=mysqli_query($link1,"select uid from mapped_master where mapped_code='".$childid."'")or die(mysqli_error($link1));
	while($row_parent=mysqli_fetch_array($res_parent)){
		///get loction name
	   $locationname=str_replace("~",",",getLocationDetails($row_parent['uid'],"name,city,state",$link1));
	   if($parent_str==""){
		   $parent_str.=$locationname."(".$row_parent['uid'].")";
	   }else{
		   $parent_str.="<br/>".$locationname."(".$row_parent['uid'].")";
	   }
	}
	return $parent_str;
}
///////////////////////////
//// get access location ////
function getAccessLocation($userid,$link1){
	$loction_str="";
	$res_parent=mysqli_query($link1,"select location_id from access_location where uid='".$userid."' and status='Y'")or die(mysqli_error($link1));
	if(mysqli_num_rows($res_parent)>0){
	while($row_parent=mysqli_fetch_assoc($res_parent)){
	   if($loction_str==""){
		   $loction_str.="'".$row_parent['location_id']."'";
	   }else{
		   $loction_str.=",'".$row_parent['location_id']."'";
	   }
	}
	}else{
		$loction_str="''";
	}
	return $loction_str;
}
//// get access state ////
function getAccessState($userid,$link1){
	$state_str="";
	$res_state=mysqli_query($link1,"select state from access_state where uid='".$userid."' and status='Y'")or die(mysqli_error($link1));
	if(mysqli_num_rows($res_state)>0){
	while($row_state=mysqli_fetch_assoc($res_state)){
	   if($state_str==""){
		   $state_str.="'".$row_state['state']."'";
	   }else{
		   $state_str.=",'".$row_state['state']."'";
	   }
	}
	}else{
		$state_str="''";
	}
	return $state_str;
}
////// get excel and cancel process id //
function getExlCnclProcessid($processname,$link1){
	$res_processid=mysqli_query($link1,"select id from excel_cancel_rights where transaction_type='".$processname."' and status='A'")or die(mysqli_error($link1));
	$row_processid=mysqli_fetch_assoc($res_processid);
	if($row_processid['id']){
	    return $row_processid['id'];
	}else{
		return 0;
	}
}
//// get access Excel Export rights ////
function getExcelRight($userid,$processid,$link1){
	$excelRightFlag=0;
	$res_exl=mysqli_query($link1,"select sno from excel_export_right where process_id='".$processid."' and user_id='".$userid."' and status='Y'")or die(mysqli_error($link1));
	if(mysqli_num_rows($res_exl)>0){
       $excelRightFlag=1;
	}else{
	   $excelRightFlag=0;
	}
	return $excelRightFlag;
}
//// get access Cancel rights ////
function getCancelRight($userid,$processid,$link1){
	$cancelRightFlag=0;
	$res_cancel=mysqli_query($link1,"select id from access_cancel_rights where cancel_type='".$processid."' and uid='".$userid."' and status='Y'")or die(mysqli_error($link1));
	if(mysqli_num_rows($res_cancel)>0){
       $cancelRightFlag=1;
	}else{
	   $cancelRightFlag=0;
	}
	return $cancelRightFlag;
}
////
//////// function to capture approval activities /////
function approvalActivity($refno,$refdate,$reqtype,$uid,$actionTaken,$actiondate,$actiontime,$actionrmk,$systemIp,$link1,$errorflag){
	$flag=$errorflag;
	$query="INSERT INTO approval_activities set ref_no='".$refno."',ref_date='".$refdate."',req_type='".$reqtype."',action_by='".$uid."',action_taken='".$actionTaken."',action_date='".$actiondate."',action_time='".$actiontime."',action_remark='".$actionrmk."',action_ip='".$systemIp."'";
	$result=mysqli_query($link1,$query);
	//// check if query is not executed
    if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
	}
	return $flag;
}
//////////////// FUnction for insert into store stock for Stock Leadger/////////////////
function stockLedger($inv_no,$inv_date,$itemcode,$from_party,$to_party,$ownercode,$stock_transfer,$stock_type,$type_name,$qty,$price,$create_by,$createdate,$createtime,$ip,$link1,$errorflag){
	$flag=$errorflag;
    $result=mysqli_query($link1,"insert into stock_ledger set reference_no='".$inv_no."',reference_date='".$inv_date."',partcode='".$itemcode."',from_party='".$from_party."', to_party='".$to_party."',owner_code='".$ownercode."',stock_transfer='".$stock_transfer."',stock_type='".$stock_type."',type_of_transfer='".$type_name."',qty='".$qty."',rate='".$price."',create_by='".$create_by."',create_date='".$createdate."',create_time='".$createtime."',ip='".$ip."'");
	//// check if query is not executed
    if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
	}
	return $flag;
}
//////////////End of Store Stock Function///////////////////////////////
//////////////// FUnction for insert into store amount for Party Leadger/////////////////
function partyLedger($fromloc,$toloc,$docno,$docdate,$entrydate,$entrytime,$entryby,$doctype,$amount,$crdr,$link1,$errorflag){
	$flag=$errorflag;
    $result=mysqli_query($link1,"insert into party_ledger set location_code='".$fromloc."',cust_id='".$toloc."',doc_no='".$docno."',doc_date='".$docdate."', entry_date='".$entrydate."',entry_time='".$entrytime."',entry_by='".$entryby."',doc_type='".$doctype."',amount='".$amount."',cr_dr='".$crdr."'");
	//// check if query is not executed
    if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
	}
	return $flag;
}
////function for Logistic name
function getLogistic($logistic_code,$link1){
 $sql=mysqli_query($link1,"select couriername,city,state from diesl_master where couriercode='".$logistic_code."'");
 $row=mysqli_fetch_assoc($sql);
 return $row['couriername'].",".$row['city'].",".$row['state'];
}
function getProductName($product_id,$link1){
 $sql=mysqli_query($link1,"select productname from product_master where id='".$product_id."'");
 $row=mysqli_fetch_assoc($sql);
 return $row['productname'];
}
//////////////End of store amount for Party Leadger FUnction///////////////////////////////
////function for getting price 
function getProductPrice($product_code,$locationtype,$locationstate,$link1){
 $sql=mysqli_query($link1,"select price,mrp from price_master where state='".$locationstate."' and location_type='".$locationtype."' and product_code='".$product_code."' and status='active'");
 $row=mysqli_fetch_assoc($sql);
 return $row['price']."~".$row['mrp'];
}
############ Number convert into words ##############
function number_to_words($number){
  if ($number > 999999999){
      throw new Exception("Number is out of range");
  }
	$Cn = floor($number / 10000000); /* Crore () */
	$number -= $Cn * 10000000;
	//$Gn = floor($number / 1000000); /* Millions (giga) */
	//$number -= $Gn * 1000000;
	$ln = floor($number / 100000); /* Lakh () */
	$number -= $ln * 100000;
	
	$kn = floor($number / 1000); /* Thousands (kilo) */
	$number -= $kn * 1000;
	$Hn = floor($number / 100); /* Hundreds (hecto) */
	$number -= $Hn * 100;
	$Dn = floor($number / 10); /* Tens (deca) */
	$n = $number % 10; /* Ones */
	$cn = round(($number-floor($number))*100); /* Cents */
	$result = "";
    if ($Cn) { $result .= (empty($result) ? "" : " ") . number_to_words($Cn) . " Crore"; }
    /*if ($Gn){ $result .= number_to_words($Gn) . " Million"; }*/
    if ($ln){ $result .= (empty($result) ? "" : " ") . number_to_words($ln) . " Lakh"; }
    if ($kn){ $result .= (empty($result) ? "" : " ") . number_to_words($kn) . " Thousand"; }
    if ($Hn){ $result .= (empty($result) ? "" : " ") . number_to_words($Hn) . " Hundred"; }
	$ones = array("", "One", "Two", "Three", "Four", "Five", "Six","Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen","Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen","Nineteen");
	$tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty","Seventy", "Eigthy", "Ninety");

    if ($Dn || $n){
       if (!empty($result)){ $result .= " ";}
       if ($Dn < 2){ $result .= $ones[$Dn * 10 + $n];}
       else{ 
	      $result .= $tens[$Dn];
          if ($n){ $result .= "-" . $ones[$n];}
	   }
	}
    if ($cn){
       if (!empty($result)){ $result .= ' and ';}
       $title = $cn==1 ? 'paisa': 'paise';
       $result .= strtolower(number_to_words($cn)).' '.$title;
	}
    if (empty($result)){ $result = "zero"; }
    return $result;
}
////function for product name @payal
function getProduct($product_code,$link1){
 $sql=mysqli_query($link1,"select productname from product_master where productcode='".$product_code."'");
 $row=mysqli_fetch_assoc($sql);
 return $row['productname'];
}
/////// get usertypename  @payal ///////
function gettypeName($refid,$link1){
 $type_name=mysqli_fetch_array(mysqli_query($link1,"select typename from usertype_master where refid='".$refid."'"));
 return $type_name['typename'];
}

/*
 * request parameter @field,@link1
 * return hsn code 
 * Hsn function code 
 */
function getHsnCode($field,$link1){
 $hsn_code=mysqli_fetch_array(mysqli_query($link1,"select hsn_code from product_master where productcode='".$field."'"));
 return $hsn_code['hsn_code'];
}
function getAnyDetails($keyid,$fields,$lookupname,$tbname,$link1){
	///// check no. of column
	$chk_keyword = substr_count($fields, ',');
        
	if($chk_keyword > 0){
		$explodee = explode(",",$fields);
                
		$tb_details = mysqli_fetch_array(mysqli_query($link1,"select ".$fields." from ".$tbname." where ".$lookupname." = '".$keyid."'"));
		$rtn_str = "";
		for($k=0;$k < count($explodee);$k++){
			if($rtn_str==""){
				$rtn_str.= $tb_details[$k];
			}
			else{
				$rtn_str.= "~".$tb_details[$k];
			}
		}
	}
	else{
		$tb_details = mysqli_fetch_array(mysqli_query($link1,"select ".$fields." from ".$tbname." where ".$lookupname." = '".$keyid."'"));
		$rtn_str = $tb_details[$fields];
	}
	return $rtn_str;
}
function getPRD($productid,$fields,$link1){
   $explodee=explode(",",$fields);
   $prod_details=mysqli_fetch_array(mysqli_query($link1,"select ".$fields." from product_master where id='".$productid."'"));
   $rtn_str="";
   for($k=0;$k < count($explodee);$k++){
       if($rtn_str==""){
          $rtn_str.=$prod_details[$k];
	   }
       else{
          $rtn_str.="~".$prod_details[$k];
	   }
   }
   return $rtn_str;
}
/////// this function is devoloped to get FIFO stock of a product and written by shekhar on 27 march 2019
function getFIFOStock($location,$partcode,$current_ok = 0,$current_dm = 0,$current_ms = 0, $link1){
	$today = date("Y-m-d");
	$slab_15_ok = 0;	$slab_30_ok = 0;	$slab_30p_ok = 0;
	$slab_15_dm = 0;	$slab_30_dm = 0;	$slab_30p_dm = 0;
	$slab_15_ms = 0;	$slab_30_ms = 0;	$slab_30p_ms = 0;

	/*//////pick each transaction of requested location for requested part from stock ledger
	$res = mysqli_query($link1,"SELECT stock_type, SUM(CASE WHEN stock_transfer='IN' THEN qty ELSE 0 END) as sumofinqty,SUM(CASE WHEN stock_transfer='OUT' THEN qty ELSE 0 END) as sumofoutqty FROM stock_ledger where partcode = '".$partcode."' and owner_code = '".$location."' group by stock_type");
	while($row = mysqli_fetch_assoc($res)){
		if($row["stock_type"] == "OK"){
			$current_ok = $row["sumofinqty"] - $row["sumofoutqty"];
		}else if($row["stock_type"] == "DAMAGE"){
			$current_dm = $row["sumofinqty"] - $row["sumofoutqty"];
		}else if($row["stock_type"] == "MISSING"){
			$current_ms = $row["sumofinqty"] - $row["sumofoutqty"];
		}else{
		}
	}////end while loop*/
	/// define 3 stock type array
	$array_ok = array();
	$array_dm = array();
	$array_ms = array();
	////// now we have to pick every IN qty trasaction order by descending and make array of stock type qty associate with reference date
	 $res2 = mysqli_query($link1,"SELECT reference_date, stock_type, qty FROM stock_ledger where partcode = '".$partcode."' and owner_code = '".$location."' and stock_transfer='IN' order by reference_date desc");
	while($row2 = mysqli_fetch_assoc($res2)){
		///// check stock type
		if($row2["stock_type"] == "OK"){
			$array_ok[$row2["reference_date"]] += $row2["qty"];
		}else if($row2["stock_type"] == "DAMAGE"){
			$array_dm[$row2["reference_date"]] += $row2["qty"];
		}else{
			$array_ms[$row2["reference_date"]] += $row2["qty"];
		}
	}///end while loop
	
	////// now we have to compare each stock type array with current stock to get stock aging
	foreach($array_ok as $ok_refdate => $okqty){
		$daysdiff = daysDifference($today , $ok_refdate);
		/// check current stock qty is equal or less then array ref date qty
		if($okqty >= $current_ok){
			if($daysdiff >= 0 && $daysdiff < 31){
				$slab_15_ok += $current_ok;
			}else if($daysdiff > 30 && $daysdiff < 91){
				$slab_30_ok += $current_ok;
			}else{
				$slab_30p_ok += $current_ok;
			}
			break;
		}else{
			if($daysdiff >= 0 && $daysdiff < 31){
				$slab_15_ok += $okqty;
			}else if($daysdiff > 30 && $daysdiff < 91){
				$slab_30_ok += $okqty;
			}else{
				$slab_30p_ok += $okqty;
			}
			$current_ok = $current_ok - $okqty;
		}
	}//// end ok foreach loop

	foreach($array_dm as $dm_refdate => $dmqty){
		$daysdiff = daysDifference($today , $dm_refdate);
		/// check current stock qty is equal or less then array ref date qty
		if($dmqty >= $current_dm){
			if($daysdiff >= 0 && $daysdiff < 31){
				$slab_15_dm += $current_dm;
			}else if($daysdiff > 30 && $daysdiff < 91){
				$slab_30_dm += $current_dm;
			}else{
				$slab_30p_dm += $current_dm;
			}
			break;
		}else{
			if($daysdiff >= 0 && $daysdiff < 31){
				$slab_15_dm += $dmqty;
			}else if($daysdiff > 30 && $daysdiff < 91){
				$slab_30_dm += $dmqty;
			}else{
				$slab_30p_dm += $dmqty;
			}
			$current_dm = $current_dm - $dmqty;
		}
	}//// end dm foreach loop

	foreach($array_ms as $ms_refdate => $msqty){
		$daysdiff = daysDifference($today , $ms_refdate);
		/// check current stock qty is equal or less then array ref date qty
		if($msqty >= $current_ms){
			if($daysdiff >= 0 && $daysdiff < 31){
				$slab_15_ms += $current_ms;
			}else if($daysdiff > 30 && $daysdiff < 91){
				$slab_30_ms += $current_ms;
			}else{
				$slab_30p_ms += $current_ms;
			}
			break;
		}else{
			if($daysdiff >= 0 && $daysdiff < 31){
				$slab_15_ms += $msqty;
			}else if($daysdiff > 30 && $daysdiff < 91){
				$slab_30_ms += $msqty;
			}else{
				$slab_30p_ms += $msqty;
			}
			$current_ms = $current_ms - $msqty;
		}
	}//// end ms foreach loop
	return $slab_15_ok."~".$slab_30_ok."~".$slab_30p_ok."~".$slab_15_dm."~".$slab_30_dm."~".$slab_30p_dm."~".$slab_15_ms."~".$slab_30_ms."~".$slab_30p_ms;
}
function get_status($status_id,$link1)
 {
	 $status=mysqli_query($link1,"select status_name from sf_status_master where id='".$status_id."'") or die(mysqli_error($link1));
	 $srow=mysqli_fetch_assoc($status);
	 $status_type=$srow['status_name'];
	 return $status_type;
 }
 
 function get_priority($priority,$link1)
 {
	 $priority=mysqli_query($link1,"select priority from priority_master where id='".$priority."'") or die(mysqli_error($link1));
	 $srow=mysqli_fetch_assoc($priority);
	 $priority=$srow['priority'];
	 return $priority;
 }
 ///// written by shekhar on 19 feb 2019
function getProcessStatus($sid,$link1){
	$res = mysqli_query($link1,"select status_name from sf_process_status where id='".$sid."'");
	$row = mysqli_fetch_assoc($res);
	if($row['status_name']){
		return $row['status_name'];
	}else{
		return $sid;
	}
}
///// to get lead source name
 function get_leadsource($id,$link1)
 {
	$sql=mysqli_query($link1,"select * from sf_source_master where id='".$id."'"); 
	$row=mysqli_fetch_assoc($sql);
	return $row['source'];
 }
 function get_communication($id,$link1)
 {
	$sql=mysqli_query($link1,"select * from sf_tbl_comm_type where id='".$id."'"); 
	$row=mysqli_fetch_assoc($sql);
	return $row['comm_type'];
 }
 function set_history($party_id,$status_id, $trans_no, $trans_type,$upd_by,$link1)
 {
 	mysqli_query($link1,"insert into sf_status_history set party_id='".$party_id."', status_id='".$status_id."', trans_no='".$trans_no."', trans_type='".$trans_type."',update_by='".$upd_by."'") or die(mysqli_error($link1));
 }
/////// function to get Make details
function getMakeDetails($makeid,$fields,$link1){
   $explodee=explode(",",$fields);
   $prod_details=mysqli_fetch_array(mysqli_query($link1,"select $fields from make_master where id='$makeid'"));
   $rtn_str="";
   for($k=0;$k < count($explodee);$k++){
       if($rtn_str==""){
          $rtn_str.=$prod_details[$k];
	   }
       else{
          $rtn_str.="~".$prod_details[$k];
	   }
   }
   return $rtn_str;
}
/////// function to get product category details
function getProductCategoryDetails($prodid,$fields,$link1){
   $explodee=explode(",",$fields);
   $prod_details=mysqli_fetch_array(mysqli_query($link1,"select $fields from product_cat_master where catid='$prodid'"));
   $rtn_str="";
   for($k=0;$k < count($explodee);$k++){
       if($rtn_str==""){
          $rtn_str.=$prod_details[$k];
	   }
       else{
          $rtn_str.="~".$prod_details[$k];
	   }
   }
   return $rtn_str;
}
/////// function to get product sub category details
function getProductSubCategoryDetails($prodsubid,$fields,$link1){
   $explodee=explode(",",$fields);
   $prod_details=mysqli_fetch_array(mysqli_query($link1,"select $fields from product_sub_category where psubcatid='$prodsubid'"));
   $rtn_str="";
   for($k=0;$k < count($explodee);$k++){
       if($rtn_str==""){
          $rtn_str.=$prod_details[$k];
	   }
       else{
          $rtn_str.="~".$prod_details[$k];
	   }
   }
   return $rtn_str;
}
///// this function is create for storing every sale price for the fifo purpose , develop by shekhar on 16 nov 2019
function storeFifoSale($itemCode,$itemName,$fromLocCode,$fromLocName,$toLocCode,$toLocName,$saleQty,$salePrice,$transactionNo){
	$todayDate = date("Y-m-d");
	$todayTime = date("H:i:s");
	$flag = $errorflag;
	$query = "INSERT INTO fifo_sale_price SET item_code='".$itemCode."', item_name='".$itemName."', from_loccode='".$fromLocCode."', from_locname='".$fromLocName."', to_loccode='".$toLocCode."', to_locname='".$toLocName."', sale_qty='".$saleQty."', sale_price='".$salePrice."', transaction_no='".$transactionNo."', entry_date='".$todayDate."', entry_time='".$todayTime."'";
	$result = mysqli_query($link1,$query);
	//// check if query is not executed
    if (!$result) {
	     $flag = false;
         //echo "Error details: " . mysqli_error($link1) . ".";
	}
	return $flag;
}
///// this function is create for storing every purchase price for the fifo purpose , develop by shekhar on 16 nov 2019
function storeFifoPurchase($itemCode,$itemName,$fromLocCode,$fromLocName,$toLocCode,$toLocName,$purQty,$purPrice,$transactionNo){
	$todayDate = date("Y-m-d");
	$todayTime = date("H:i:s");
	$flag = $errorflag;
	$query = "INSERT INTO fifo_purchase_price SET item_code='".$itemCode."', item_name='".$itemName."', from_loccode='".$fromLocCode."', from_locname='".$fromLocName."', to_loccode='".$toLocCode."', to_locname='".$toLocName."', purchase_qty='".$purQty."', purchase_price='".$purPrice."', transaction_no='".$transactionNo."', entry_date='".$todayDate."', entry_time='".$todayTime."'";
	$result = mysqli_query($link1,$query);
	//// check if query is not executed
    if (!$result) {
	     $flag = false;
         //echo "Error details: " . mysqli_error($link1) . ".";
	}
	return $flag;
}

function cellColor($cells,$color){

    global $objPHPExcel;



    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(

        'type' => PHPExcel_Style_Fill::FILL_SOLID,

        'startcolor' => array(

             'rgb' => $color

        )

    ));

}
/// functiom to get any party details written by shekhar on 21 feb 2022///
function getAnyParty($code,$link1){
	$str="";
	$rs=mysqli_query($link1,"select customername,city,state,customerid  from customer_master  where customerid='$code'")or die(mysqli_error($link1));
	$rs_vend=mysqli_query($link1,"select name,city,state  from vendor_master  where id='$code'")or die(mysqli_error($link1));
	$rs_loc=mysqli_query($link1,"select name,city,state  from asc_master  where asc_code='$code'")or die(mysqli_error($link1));
	$rs_subloc=mysqli_query($link1,"select sub_location_name,sub_location_type from sub_location_master where sub_location='$code'")or die(mysqli_error($link1));
	if(mysqli_num_rows($rs)>0){
		$br=mysqli_fetch_array($rs);
		$str.=$br[0].",".$br[1].",".$br[2].",".$br[3];
	}
	elseif(mysqli_num_rows($rs_vend)>0){
		$br=mysqli_fetch_array($rs_vend);
		$str.=$br[0].",".$br[1].",".$br[2].",".$code;
	}
	elseif(mysqli_num_rows($rs_loc)>0){
		$br=mysqli_fetch_array($rs_loc);
		$str.=$br[0].",".$br[1].",".$br[2].",".$code;
	}
	elseif(mysqli_num_rows($rs_subloc)>0){
		$br=mysqli_fetch_array($rs_subloc);
		$str.=$br[0]." (".$br[1].")";
	}
	else{
		$rs_ac=mysqli_query($link1,"select name,username from admin_users where username='$code'")or die(mysqli_error($link1));
		$br=mysqli_fetch_array($rs_ac);
		$str.=$br[0].",".$br[1];
	}
	return $str;
}
/////// function to get voucher type written by shekhar on 11 jul 2022
function getVoucherType($link1){
	$a = array();
	$res_vchtyp = mysqli_query($link1,"SELECT id, type_name FROM voucher_type WHERE status='Active'");
	while($row_vchtyp = mysqli_fetch_assoc($res_vchtyp)){
		$a[] = $row_vchtyp["id"]."~".$row_vchtyp["type_name"];
	}
	return $a;
}
///// function to check 17 digit of serial no. and alphanumeric strig written by shekhar on 03 AUG 2022
/*function strFilter($str='', $minl=0, $maxl=0){
    $resp = false;
    $str = trim($str);
    // filter reg-ex
    $str = preg_replace("/[^a-zA-Z0-9]+/", "", $str);
    $min_l = ($minl)?(int)$minl:0;
    $max_l = ($maxl)?(int)$maxl:0;
    if($min_l >= 0 && $max_l >= 0){
        if(strlen($str) >= $min_l){
			if($max_l === 0){
				$resp = $str;
			}
			if(strlen($str) <= $max_l){
				$resp = $str;
			}
		}		
    }
    return $resp;
}*/
////// function to get hours : minutes : seconds from seconds
function getHoursMinSec($seconds){
	$H = floor($seconds / 3600);
	$i = ($seconds / 60) % 60;
	$s = $seconds % 60;
	return sprintf("%02d:%02d:%02d", $H, $i, $s);
}
///// get indian time from UTC developed by shekhar on 26 aug 2022
function getISTfromUTC($utc_datetime){
	// create a $dt object with the UTC timezone
	$dt = new DateTime($utc_datetime, new DateTimeZone('UTC'));
	// change the timezone of the object without changing its time
	$dt->setTimezone(new DateTimeZone('Asia/Calcutta'));
	// format the datetime
	return $dt->format('Y-m-d H:i:s');
}
////// function to get hierarchy written by shekhar on 07 sep 2022
function getHierarchy($id, $link1){
	$resp = [];
	$subsql = mysqli_query($link1,"SELECT username,reporting_manager FROM admin_users WHERE reporting_manager = '".$id."' AND status='Active'");
	while($row = mysqli_fetch_array($subsql)){
		if($row["reporting_manager"] == $id){
			$childs = getHierarchy($row["username"], $link1);
			$resp[] = [ $row["username"] => $childs];
		}
  	}
	return $resp;
}
////// function to get hierarchy in str written by shekhar on 14 oct 2022
function getHierarchyStr($id, $link1, $str){
	$subsql = mysqli_query($link1,"SELECT username,reporting_manager FROM admin_users WHERE reporting_manager = '".$id."' AND status='Active'");
	while($row = mysqli_fetch_array($subsql)){
		if($row["reporting_manager"] == $id){
			$str = getHierarchyStr($row["username"], $link1, $str);
			$str .= ($str)?",".$row["username"]:$row["username"];
		}
  	}
	return $str;
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
//// get access state code which is used in location code string written on 20 oct 2022 by shekhar ////
function getAccessStateCode($userid,$link1){
	$statecode_str="";
	$res_state=mysqli_query($link1,"select state from access_state where uid='".$userid."' and status='Y'")or die(mysqli_error($link1));
	if(mysqli_num_rows($res_state)>0){
	while($row_state=mysqli_fetch_assoc($res_state)){
		///// get state code
		$statec = mysqli_fetch_assoc(mysqli_query($link1,"SELECT code FROM state_master WHERE state='".$row_state['state']."'"));
	   if($statecode_str==""){
		   $statecode_str.="'".$statec['code']."'";
	   }else{
		   $statecode_str.=",'".$statec['code']."'";
	   }
	}
	}else{
		$statecode_str="''";
	}
	return $statecode_str;
}
//// get access role written by shekhar on 28 oct 2022 ////
function getAccessRole($userid,$link1){
	$role_str="";
	$res_role=mysqli_query($link1,"select role_id from access_role where uid='".$userid."' and status='Y'")or die(mysqli_error($link1));
	if(mysqli_num_rows($res_role)>0){
	while($row_role=mysqli_fetch_assoc($res_role)){
	   if($role_str==""){
		   $role_str.="'".$row_role['role_id']."'";
	   }else{
		   $role_str.=",'".$row_role['role_id']."'";
	   }
	}
	}else{
		$role_str="''";
	}
	return $role_str;
}
//////// function for displaying stock type by shekhar on 27 dec 2022
function getStockTypeName($str){
	if($str=="okqty") {
		$msg = "OK";
	} else if($str=="broken") {
		$msg = "DAMAGE";
	} else if($str=="missing") {
		$msg = "MISSING";
	} else{ 
	    $msg = $str;
	}
	return $msg;
}
//////// function to capture master change history /////
function updateMasterHistory($uid,$refno,$activityType,$remark,$systemIp,$link1,$errorflag){
	$todayDate=date("Y-m-d");
	$todayTime=date("H:i:s");
	$flag=$errorflag;
	$query="INSERT INTO master_change_history SET ref_no='".$refno."',change_done='".$activityType."',remark='".$remark."',update_by='".$uid."',update_date='".$todayDate." ".$todayTime."',update_ip='".$systemIp."'";
	$result=mysqli_query($link1,$query);
	//// check if query is not executed
    if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
	}
	return $flag;
}
/////// filter string (like ' & " ) written by shekhar on 12 jan 2023
function strFilter2($str='', $minl=0, $maxl=0){
    $resp = false;
    $str = trim($str);
    // filter reg-ex
    $str = preg_replace("/[^a-zA-Z0-9\s\/@_.,:!#&()+\-?]+/", "", $str);
    $min_l = ($minl)?(int)$minl:0;
    $max_l = ($maxl)?(int)$maxl:0;
    if($min_l >= 0 && $max_l >= 0){
        if(strlen($str) >= $min_l){
			if($max_l === 0){
				$resp = $str;
			}
			if(strlen($str) <= $max_l){
				$resp = $str;
			}
		}		
    }
    return $resp;
}

/*function sqlFilter($link, $string, $minl=0, $maxl=0){

	$filtered = strFilter2($string, $minl, $maxl);	
	//$filtered = $string;	
	if($filtered || $filtered === "0"){
		$resp = mysqli_real_escape_string($link, $filtered);
	}
	elseif($filtered === false){
		$resp = false;
	}
	else{
		$resp = '';
	}
	return $resp;
}*/

/*function requestFilter($link1, $data){

	$resp = [];
	if($data){
		foreach($data as $k_e_y => $r_e_q){
			if(is_array($r_e_q)){
				foreach($r_e_q as $key_b => $r){
					$r_e_q[$key_b] = sqlFilter($link1, $r);
				}
				$resp[$k_e_y] = $r_e_q;
			}
			else{
				$resp[$k_e_y] = sqlFilter($link1, $r_e_q);
			}
		}
	}
	return $resp;
}*/
//// get down the line all childs ////
function getTeamMembers($userid,$link1){
	$loction_str="";
	$res_parent=mysqli_query($link1,"SELECT child_id FROM relation_data WHERE user_id='".$userid."'")or die(mysqli_error($link1));
	if(mysqli_num_rows($res_parent)>0){
	while($row_parent=mysqli_fetch_assoc($res_parent)){
	   if($loction_str==""){
		   $loction_str.="'".$row_parent['child_id']."'";
	   }else{
		   $loction_str.=",'".$row_parent['child_id']."'";
	   }
	}
	}else{
		$loction_str="''";
	}
	return $loction_str;
}
///// get cancel rights
function getCancelRightNew($userid,$processid,$link1){
	$cancelRightFlag=0;
	$res_cancel=mysqli_query($link1,"select id from access_ops_rights where uid='".$userid."' and ops_id='".$processid."' and ops_name='CANCEL' and status='Y'")or die(mysqli_error($link1));
	if(mysqli_num_rows($res_cancel)>0){
       $cancelRightFlag=1;
	}else{
	   $cancelRightFlag=0;
	}
	return $cancelRightFlag;
}
//////////// Access Verification By shekhar (Mar 15, 2023) ////////////
function is_access_allowed_master($link1, $fun_id, $user)
{
	$resp = '';
	$sql = "SELECT * FROM access_report WHERE uid LIKE '".$user."' AND report_id LIKE '".$fun_id."' AND status LIKE 'Y'";
	$res = mysqli_query($link1, $sql);
	if($res)
	{	
		if(mysqli_num_rows($res) > 0)
		{
			$resp = true;
		}
		else
		{
			$resp = false;
		}
	}
	return $resp;
}
function access_check_master($link1, $fun_ids, $userid='')
{
	$resp = false;
	foreach($fun_ids as $fun_id)
	{
		$ac_ver = is_access_allowed_master($link1, $fun_id, $userid);
		if($ac_ver === '')
		{
			echo "Error Occurred, Try again!";
			exit;
		}
		elseif($ac_ver === true)
		{
			$resp = true;
			break;
		}
	}
	if($resp === false)
	{
		echo "CAUTION! Our system has detected you are trying to do an unauthorised activity. Please don't do this again otherwise your id will be block for next 365 days.";
		$req_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$res_log = mysqli_query($link1, "INSERT INTO log_unauth_act SET userid = '".$_SESSION["userid"]."', url = '".$req_link."', datetime = '".date("Y-m-d H:i:s")."', ip='".$_SERVER['REMOTE_ADDR']."', browser = '".$_SERVER['HTTP_USER_AGENT']."'");
	}
	return $resp;
}
function getAccessLocationType($userid,$link1){
	$loctiontyp_str="";
	$res_parent = mysqli_query($link1,"SELECT DISTINCT(id_type) FROM access_location WHERE uid='".$userid."' AND status='Y'")or die(mysqli_error($link1));
	if(mysqli_num_rows($res_parent)>0){
	while($row_parent=mysqli_fetch_assoc($res_parent)){
	   if($loctiontyp_str==""){
		   $loctiontyp_str.="'".$row_parent['id_type']."'";
	   }else{
		   $loctiontyp_str.=",'".$row_parent['id_type']."'";
	   }
	}
	}else{
		$loctiontyp_str="''";
	}
	return $loctiontyp_str;
}
//////////// Access Verification of pages By shekhar (OCT 26, 2023) ////////////
function is_access_allowed_v3($link1, $fun_id, $for, $user, $usertype)
{
	$resp = '';
	if($for == "u")
	{
		$sql = "SELECT * FROM access_function WHERE uid LIKE '".$user."' AND function_id LIKE '".$fun_id."' AND status LIKE 'Y'";
	}
	/*elseif($for == "l" && $usertype == NULL)
	{
		$sql = "SELECT * FROM access_report WHERE uid LIKE '".$user."' AND report_id LIKE '".$fun_id."' AND status LIKE 'Y'";
	}*/
	//elseif($for == "a" && $usertype == "Admin")
	elseif($for == "a")
	{
		$sql = "SELECT * FROM access_report WHERE uid LIKE '".$user."' AND report_id LIKE '".$fun_id."' AND status LIKE 'Y'";
	}
	else
	{
		return $resp;
	}	
	$res = mysqli_query($link1, $sql);
	if($res)
	{
		if(mysqli_num_rows($res) > 0)
		{
			$resp = true;
		}
		else
		{
			$resp = false;
		}
	}
	return $resp;
}
function access_check_v3($link1, $fun_arr, $userid='', $usertype)
{
	$resp = false;
	foreach($fun_arr as $for => $fun_ids)
	{
        foreach($fun_ids as $fun_id)
        {
            $ac_ver = is_access_allowed_v3($link1, $fun_id, $for, $userid, $usertype);
            if($ac_ver === true)
            {
                $resp = true;
                break;
            }
        }		
	}
	if($resp === false)
	{
		//echo '<div style="text-align:center;color:#ff4e4e;margin:60px 0px;"><h2>CAUTION!</h2><br>Our system has detected you are trying to do an unauthorised activity. Please don\'t do this again otherwise your id will be block for next 365 days.<br><br><span style="color:#756e6e;">User: '.$_SESSION["userid"].' | IP: '.$_SERVER['REMOTE_ADDR'].' | Activity Time: '.date("Y-m-d H:i:s").'</span></div>';
		echo '<div style="text-align:center;color:#ff4e4e;margin:60px 0px;"><h2>CAUTION!</h2><br>Our system has detected you are trying to do an unauthorised activity. Please don\'t do this again. your id is deactivate now so please contact to administration to activate your id.<br><br><span style="color:#756e6e;">User: '.$_SESSION["userid"].' | IP: '.$_SERVER['REMOTE_ADDR'].' | Activity Time: '.date("Y-m-d H:i:s").'</span></div>';
		$req_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$res_log = mysqli_query($link1, "INSERT INTO log_unauth_act SET userid = '".$_SESSION["userid"]."', url = '".$req_link."', datetime = '".date("Y-m-d H:i:s")."', ip='".$_SERVER['REMOTE_ADDR']."', browser = '".$_SERVER['HTTP_USER_AGENT']."'");
		///// deactivate the id
		$res_deact = mysqli_query($link1, "UPDATE admin_users SET status='deactive' WHERE username = '".$_SESSION["userid"]."'");
		$query="INSERT INTO daily_activities set userid='".$_SESSION["userid"]."',ref_no='".$_SESSION["userid"]."',activity_type='Unauthorised Page Access',action_taken='Deactive',update_date='".date("Y-m-d")."',update_time='".date("H:i:s")."',system_ip='".$_SERVER['REMOTE_ADDR']."'";
		$result=mysqli_query($link1,$query);
		session_destroy();
	}
	return $resp;
}
function timeDiff($time1, $time2, $precision = 6) {
    // If not numeric then convert texts to unix timestamps
    if (!is_int($time1)) {
      $time1 = strtotime($time1);
    }
    if (!is_int($time2)) {
      $time2 = strtotime($time2);
    }
    // If time1 is bigger than time2
    // Then swap time1 and time2
    if ($time1 > $time2) {
      $ttime = $time1;
      $time1 = $time2;
      $time2 = $ttime;
    }
    // Set up intervals and diffs arrays
    $intervals = array('year','month','day','hour','minute','second');
    $diffs = array();
    // Loop thru all intervals
    foreach ($intervals as $interval) {
      // Create temp time from time1 and interval
      $ttime = strtotime('+1 ' . $interval, $time1);
      // Set initial values
      $add = 1;
      $looped = 0;
      // Loop until temp time is smaller than time2
      while ($time2 >= $ttime) {
        // Create new temp time from time1 and interval
        $add++;
        $ttime = strtotime("+" . $add . " " . $interval, $time1);
        $looped++;
      }
      $time1 = strtotime("+" . $looped . " " . $interval, $time1);
      $diffs[$interval] = $looped;
    }
    
    $count = 0;
    $times = array();
    // Loop thru all diffs
    foreach ($diffs as $interval => $value) {
      // Break if we have needed precission
      if ($count >= $precision) {
        break;
      }
      // Add value and interval 
      // if value is bigger than 0
      if ($value > 0) {
        // Add s if value is not 1
        if ($value != 1) {
          $interval .= "s";
        }
        // Add value and interval to times array
        $times[] = $value . " " . $interval;
        $count++;
      }
    }
    // Return string with times
    return implode(", ", $times);
  }
/////// function to check allowed module written on 07 feb 2024 by shekhar
function getAccessModule($link1){
	$module_str="";
	$res_module = mysqli_query($link1,"SELECT module_name FROM app_config WHERE status='1' AND expiry_date >= '".date("Y-m-d")."'")or die(mysqli_error($link1));
	if(mysqli_num_rows($res_module)>0){
		$row_module = mysqli_fetch_assoc($res_module);
		$module_str = $row_module['module_name'];
	}else{
		$module_str="";
	}
	return $module_str;
}
?>