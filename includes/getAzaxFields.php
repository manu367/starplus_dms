<?php
//require_once("../config/dbconnect.php");
require_once("../security/dbh.php");
$today = date('Y-m-d');
session_start();
//////////////////  Get city by selecting state	dropdown
if ($_POST['circle']) {
    echo "<select name='locationstate' id='locationstate' class='form-control required' onchange='get_citydiv();' required><option value=''>--Please Select--</option>";
    $state_query = "select distinct(state) from state_master where zone='" . $_POST['circle'] . "' order by state";
    $state_res = mysqli_query($link1, $state_query);
    while ($row_res = mysqli_fetch_array($state_res)) {
        echo "<option value='" . $row_res['state'] . "'>";
        echo $row_res['state'] . "</option>";
    }
    echo "</select>";
}
//////////////////  Get cgst,igst, sgst per///////////////////
if ($_POST['productinfo']) {
    $prodname = mysqli_fetch_assoc(mysqli_query($link1, "select productname,hsn_code from product_master where productcode='" . $_POST['productinfo'] . "'"));	
	$gst = mysqli_fetch_assoc(mysqli_query($link1, "select sgst,cgst,igst from tax_hsn_master where hsn_code='" . $prodname['hsn_code'] . "'"));
 $stk_query = "SELECT price,mrp from price_master where product_code='" . $_POST['productinfo'] . "' and state='" . $_POST['fromstate'] . "' and location_type='" .$_POST['idtype'] . "'";
    $stk_res = mysqli_query($link1, $stk_query);
    $stk_row = mysqli_fetch_array($stk_res);
	if ($stk_row['price']) {
        $price = $stk_row['price'] ;
    } else {
        $price = "0.00";
    }      

   //////  reward point logic ////////////////////////////////////////////
	    $reward_det = mysqli_query($link1 , " select reward_point , reward_startDate , reward_endDate from product_master where productcode = '".$_POST['productinfo']."' ");
											if(mysqli_num_rows($reward_det)>0){
											 $res_data  = mysqli_fetch_array($reward_det);
											 if($res_data['reward_endDate'] >= $today){
											     $str = 'Y';
												 $msg = "You earned Reward Point";
												 $msg2 = "for product";
												 $response .= "<tr><td>" .$msg." ".$res_data['reward_point']." ".$msg2." ".$prodDescrip. "</td></tr>";
											   }
											
											 }
											 else {
											    $str = 'N';
												$response = '';
											   }

      echo  $gst['sgst'] . "~" . $gst['cgst'] . "~" . $gst['igst']. "~".$price."~".$str."~".$res_data['reward_point']."~".$response;
}

//////////////////  Get city by selecting state	dropdown
if ($_POST['state']) {
    echo "<select  name='locationcity' id='locationcity' class='form-control required' required><option value=''>--Please Select--</option>";
    $city_query = "SELECT distinct city FROM district_master where state='" . $_POST['state'] . "' order by city";
    $city_res = mysqli_query($link1, $city_query);
    while ($row_city = mysqli_fetch_array($city_res)) {
        echo "<option value='" . $row_city['city'] . "'>";
        echo $row_city['city'] . "</option>";
    }
    echo "<option value='Others'>Others</option>";
    echo "</select>";
}
//// for multi line written on 02 JUN 2023 by shekhar
if ($_POST['statemultiline']) {
    echo "<select  name='locationcity[".$_POST['indxx']."]' id='locationcity[".$_POST['indxx']."]' class='form-control required' required><option value=''>--Please Select--</option>";
    $city_query = "SELECT distinct city FROM district_master where state='" . $_POST['statemultiline'] . "' order by city";
    $city_res = mysqli_query($link1, $city_query);
    while ($row_city = mysqli_fetch_array($city_res)) {
        echo "<option value='" . $row_city['city'] . "'>";
        echo $row_city['city'] . "</option>";
    }
    echo "<option value='Others'>Others</option>";
    echo "</select>";
}
//////////////////  Get Parent Location by selecting location type dropdown
if ($_POST['loctype']) {
	if($_POST['loctypstr']=="DL"){ $strr = "id_type='DS' AND state='".$_POST['locstate']."'"; }else if($_POST['loctypstr']=="DS" || $_POST['loctypstr']=="SR" || $_POST['loctypstr']=="RT"){ $strr = "id_type='HO'";}else{ $strr = "user_level<'".$_POST['loctype']."'";}
    echo "<select  name='parentid' id='parentid' class='form-control required' required data-live-search='true'><option value=''>--Please Select--</option>";
    $parent_query = "SELECT uid,name,city,state FROM asc_master where user_level<'" . $_POST['loctype'] . "' order by id_type,name";
	//$parent_query = "SELECT uid,name,city,state FROM asc_master where ".$strr." order by id_type,name";
    $parent_res = mysqli_query($link1, $parent_query);
    while ($row_parent = mysqli_fetch_array($parent_res)) {
        echo "<option value='" . $row_parent['uid'] . "'>";
        echo $row_parent['name'] . "," . $row_parent['city'] . "," . $row_parent['state'] . "</option>";
    }
    echo "<option value='NONE'>NONE</option>";
    //if($_POST['loctype']==1){ echo "<option value='NONE'>NONE</option>";}
    echo "</select>";
}
//////////////////  Get available stock of product for selected location
if ($_POST['locstk']) {
	if($_POST['godown']){
		$stk_query = "SELECT " . $_POST['stktype'] . " FROM stock_status where asc_code='" . $_POST['loccode'] . "' AND sub_location='".$_POST['godown']."' and partcode='" . $_POST['locstk'] . "'";
	}else{
    	$stk_query = "SELECT " . $_POST['stktype'] . " FROM stock_status where asc_code='" . $_POST['loccode'] . "' and partcode='" . $_POST['locstk'] . "'";
	}
    $stk_res = mysqli_query($link1, $stk_query);
    $prodname = mysqli_fetch_assoc(mysqli_query($link1, "select productname,hsn_code from product_master where productcode='" . $_POST['locstk'] . "'"));
    $loc_state = mysqli_fetch_assoc(mysqli_query($link1, "select state from asc_master where asc_code='" . $_POST['loccode'] . "'"));
    $ven_state = mysqli_fetch_assoc(mysqli_query($link1, "select state from vendor_master where id='" . $_POST['vendorCode'] . "'"));
	if($ven_state['state']==""){ $ven_state = mysqli_fetch_assoc(mysqli_query($link1, "select state from asc_master where asc_code='" . $_POST['vendorCode'] . "'"));}
    $gst = mysqli_fetch_assoc(mysqli_query($link1, "select sgst,cgst,igst from tax_hsn_master where hsn_code='" . $prodname['hsn_code'] . "'"));
    $stk_row = mysqli_fetch_array($stk_res);
    if( $loc_state['state'] == $ven_state['state']){
        $sgst_per = $gst['sgst'];
        $cgst_per = $gst['cgst'];
        $igst_per = '0';
    }else{
        $sgst_per = 0;
        $cgst_per = 0;
        $igst_per = $gst['igst'];
    }
    echo $stk_row[0] . "~" . $_POST['indxx']."~" . $sgst_per . "~" . $cgst_per . "~" . $igst_per."~" .$_POST['qty']."~" .$_POST['locnindex']."~".$_POST['prodtindex'];
}

//////////////////  Get available stock of product for selected location
if ($_POST['locstknew']) {
	if($_POST['godown']){
		$stk_query = "SELECT " . $_POST['stktype'] . " FROM stock_status where asc_code='" . $_POST['fromloc'] . "' AND sub_location='".$_POST['godown']."' and partcode='" . $_POST['locstknew'] . "'";
	}else{
    	$stk_query = "SELECT " . $_POST['stktype'] . " FROM stock_status where asc_code='" . $_POST['fromloc'] . "' and partcode='" . $_POST['locstknew'] . "'";
	}
    $stk_res = mysqli_query($link1, $stk_query);
    $prodname = mysqli_fetch_assoc(mysqli_query($link1, "select productname,hsn_code from product_master where productcode='" . $_POST['locstknew'] . "'"));
    $loc_state = mysqli_fetch_assoc(mysqli_query($link1, "select state from asc_master where asc_code='" . $_POST['loccode'] . "'"));
    $ven_state = mysqli_fetch_assoc(mysqli_query($link1, "select state from asc_master where asc_code='" . $_POST['fromloc'] . "'"));
    $gst = mysqli_fetch_assoc(mysqli_query($link1, "select sgst,cgst,igst from tax_hsn_master where hsn_code='" . $prodname['hsn_code'] . "'"));
    $stk_row = mysqli_fetch_array($stk_res);
    if( $loc_state['state'] == $ven_state['state']){
        $sgst_per = $gst['sgst'];
        $cgst_per = $gst['cgst'];
        $igst_per = '0';
    }else{
        $sgst_per = 0;
        $cgst_per = 0;
        $igst_per = $gst['igst'];
    }
    echo $stk_row[0] . "~" . $_POST['indxx']."~" . $sgst_per . "~" . $cgst_per . "~" . $igst_per;
}



//////////////////  Check duplicate document code for selected location
if ($_POST['doccode']) {
    $doccode_query = "SELECT id from document_counter where financial_year='" . $_POST['fcyear'] . "' and doc_code='" . strtoupper($_POST['doccode']) . "'";
    $doccode_res = mysqli_query($link1, $doccode_query);
    $doccode_row = mysqli_num_rows($doccode_res);
    echo $doccode_row;
}
//////////////////  Check duplicate Product code
if ($_POST['pcode']) {
    $prodcode_query = "SELECT id from product_master where productcode='" . $_POST['pcode'] . "'";
    $prodcode_res = mysqli_query($link1, $prodcode_query);
    $prodcode_row = mysqli_num_rows($prodcode_res);
    echo $prodcode_row;
}
//////////////////  Get price of product 
if ($_POST['product']) {
    $stk_query = "SELECT price,mrp from price_master where product_code='" . $_POST['product'] . "' and state='" . $_POST['locstate'] . "' and location_type='" . $_POST['lctype'] . "'";
    $stk_res = mysqli_query($link1, $stk_query);
    $stk_row = mysqli_fetch_array($stk_res);
    if ($stk_row['price']) {
        echo $stk_row['price'] . "~" . $stk_row['mrp'];
    } else {
        echo "0.00~0.00";
    }
}
//////////////////  Get product details by searching IMEI 
if ($_POST['prodimei']) {
    //// check imei is in stock or not
  $imei_query = "SELECT owner_code,prod_code from billing_imei_data where imei1='" . $_POST['prodimei'] . "' or imei2='" . $_POST['prodimei'] . "' order by id desc";
    $imei_res = mysqli_query($link1, $imei_query);
	$imei_row = mysqli_fetch_array($imei_res);
  if( $_POST['loccode'] == rtrim($imei_row['owner_code'], " " )){
       $stkflag = "Y";
       $prodname = mysqli_fetch_assoc(mysqli_query($link1, "select productname,hsn_code from product_master where productcode='" . $imei_row['prod_code'] . "'"));
       $prodDescrip = $prodname['productname'];
    } else {
       $stkflag = "N";
       $prodDescrip = "Not in stock";
    }
    ////// check price of product
    if ($imei_row['prod_code']) {
        $stk_query = "SELECT price,mrp from price_master where product_code='" . $imei_row['prod_code'] . "' and state='" . $_POST['locstate'] . "' and location_type='" . $_POST['lctype'] . "'";
        $stk_res = mysqli_query($link1, $stk_query);
        $stk_row = mysqli_fetch_array($stk_res);
        if ($stk_row['price']) {
            $pricflag = $stk_row['price'] . "~" . $stk_row['mrp'];
        } else {
            $pricflag = "0.00~0.00";
        }
    } else {
        $pricflag = "0.00~0.00";
    }

   if($prodDescrip) {
	//////  reward point logic ////////////////////////////////////////////
	    $reward_det = mysqli_query($link1 , " select reward_point , reward_startDate , reward_endDate from product_master where productcode = '".$imei_row['prod_code']."' ");
											if(mysqli_num_rows($reward_det)>0){
											 $res_data  = mysqli_fetch_array($reward_det);
											 if($res_data['reward_endDate'] >= $today){
											     $str = 'Y';
												 $msg = "You earned Reward Point";
												 $msg2 = "for product";
												 $response .= "<tr><td>" .$msg." ".$res_data['reward_point']." ".$msg2." ".$prodDescrip. "</td></tr>";
											   }
											
											 }
											 else {
											    $str = 'N';
												$response = '';
											   }
	
	 }
	
          ///////check coupon code is available  on product or not /////////////////////////////
						       $product_det = mysqli_fetch_array(mysqli_query($link1 , "select id , productcategory, productsubcat from product_master where productcode = '".$imei_row['prod_code']."' "));      
							  
		                         ///// check coupon code is available on productid of product/////////////////////////////
							    $coupon_det = mysqli_query($link1 , "select coupon_code from coupon_mapping where  status = 'Y' and  find_in_set('".$product_det['id']."',productid) <> 0     ");	
			                     if(mysqli_num_rows($coupon_det) >0)  {
								
								  $coupon_str = '';
								   while($res = mysqli_fetch_array($coupon_det))
								   {
								     if($coupon_str == ''){
								    $coupon_str.= $res['coupon_code'];
									 }
									 else {
									 $coupon_str.="','".$res['coupon_code'];
									   }
								    }
								   }
								   else {
									  //////  check coupon code availabel on product subcategory 
									   $coupon_subcatdet = mysqli_query($link1 , " select coupon_code from coupon_mapping where  status = 'Y' and  find_in_set('".$product_det['productsubcat']."',prod_subcat) <> 0   ");	
									    if(mysqli_num_rows($coupon_subcatdet) >0){
										$coupon_str = '';
										   while($res = mysqli_fetch_array($coupon_subcatdet))
										   {
											 if($coupon_str == ''){
											 $coupon_str.= $res['coupon_code'];
											 }
											 else {
											 $coupon_str.="','".$res['coupon_code'];
											   }
											}
										}
										else {
										 $coupon_prodcatdet = mysqli_query($link1 , " select coupon_code from coupon_mapping where  status = 'Y' and find_in_set('".$product_det['productcategory']."',prod_cat) <> 0   ");
										
										 if(mysqli_num_rows($coupon_prodcatdet) >0){
										 $coupon_str = '';
										   while($res = mysqli_fetch_array($coupon_prodcatdet))
										   {
											 if($coupon_str == ''){
											 $coupon_str.= $res['coupon_code'];
											 }
											 else {
											 $coupon_str.="','".$res['coupon_code'];
											   }
											}
										
										    }		
										}
									}	
									 	 
	    ///////  check validity ////////////////////////////////////////////////////////////////////

		$coupon_data = mysqli_fetch_array(mysqli_query($link1 , "select coupon_code , max(amount) as amt from coupon_master  where valid_to >= $today and  coupon_code in ( '$coupon_str')  and  amount = (select max(amount) from coupon_master where coupon_code in (  '$coupon_str') )"));
		if($coupon_data['coupon_code'] != ''){
		 $coupon_check = $coupon_data['coupon_code'];
		 $coupon_amt = $coupon_data['amt'];
		 }else {
		 $coupon_check = '';	
		 $coupon_amt = '';	 
		  }

    $state = mysqli_fetch_assoc(mysqli_query($link1, "select state from asc_master where asc_code='" . $_POST['loccode'] . "'"));
    $gst = mysqli_fetch_assoc(mysqli_query($link1, "select sgst,cgst,igst from tax_hsn_master where hsn_code='" . $prodname['hsn_code'] . "'"));
    echo $pricflag . "~" . $stkflag . "~" . $imei_row['prod_code'] . "~" . $prodDescrip . "~" . $gst['sgst'] . "~" . $gst['cgst'] . "~" . $gst['igst'] . "~" . $state['state']."~".$str."~".$res_data['reward_point']."~".$response."~".$coupon_check."~".$coupon_amt;
	}
//////////////////  check Serial no. is available or not on 17 dec 2019 by shekhar
if ($_POST['checkSerialIsAvl']) {
	//// check imei is in stock or not
  	$imei_query = "SELECT owner_code,prod_code from billing_imei_data where imei1='" . $_POST['checkSerialIsAvl'] . "' or imei2='" . $_POST['checkSerialIsAvl'] . "' order by id desc";
    $imei_res = mysqli_query($link1, $imei_query);
	$imei_row = mysqli_fetch_array($imei_res);
  	if( $_POST['loccode'] == rtrim($imei_row['owner_code'], " " )){
    	$stkflag = "Y";
       	$prodname = mysqli_fetch_assoc(mysqli_query($link1, "select productname,hsn_code from product_master where productcode='" . $imei_row['prod_code'] . "'"));
       	$prodDescrip = $prodname['productname'];
    } else {
       	$stkflag = "N";
       	$prodDescrip = "Not in stock";
    }
	echo $stkflag . "~" . $imei_row['prod_code'] . "~" . $prodDescrip;
}
/// get product specification devlop by shekhar on 26 march 2019
if($_POST["prodSpecif"]){
	//// get product id behalf of product code
	$prdid = mysqli_fetch_assoc(mysqli_query($link1,"SELECT id,productcategory,warranty_days FROM product_master where productcode='".$_POST["prodSpecif"]."'"));
	$response = "<table class=table table-bordered width=100%><thead><tr><th>Specification</th><th>Description</th></tr></thead><tbody>";
	$sql = "SELECT parameter_id,parameter_details FROM pr_specification where product_id='".$prdid["id"]."'";
	$res = mysqli_query($link1, $sql);
	if(mysqli_num_rows($res)>0){
		while($row = mysqli_fetch_array($res)){
			///// get parameter name
			$pmn = mysqli_fetch_assoc(mysqli_query($link1,"SELECT parameter_name FROM pr_parameter_master where parameter_id='".$row["parameter_id"]."'"));
			$response .= "<tr><td>".$pmn["parameter_name"]."</td><td>".$row['parameter_details']."</td></tr>";
		}
	}else{
		$response .= "<tr><td colspan=2>No specification found for selected product</td></tr>";
	}
	$response .= "</tbody></table>";
	echo $response."~".$_POST['indxx']."~".$prdid["productcategory"]."~".$prdid["warranty_days"];
}
/// get product delivery date devlop by shekhar on 26 march 2019
if($_POST["prodDelivdate"]){
	$sql = "SELECT delivery_days FROM product_delivery_matrix where from_location='".$_POST["fromloc"]."' and to_location='".$_POST["toloc"]."' and productcategory='".$_POST["prodDelivdate"]."'";
	$res = mysqli_query($link1, $sql);
	if(mysqli_num_rows($res)>0){
		$row = mysqli_fetch_array($res);
		$deliv_date = date('Y-m-d', strtotime(date("Y-m-d"). ' + '.$row["delivery_days"].' days'));
	}else{
		$deliv_date = "";
	}
	echo $deliv_date."~".$_POST['indxx'];
}
///// check scanned IMEI is belong to requested location stock or not
///// developed by shekhar on 22 april 2019
if($_POST["scanimei"]){
	//echo "SELECT id,owner_code,prod_code,imei1 FROM billing_imei_data WHERE imei1 = '".$_POST["scanimei"]."' order by id desc".$_POST['reqmodel'];
	$res_imei = mysqli_query($link1,"SELECT id FROM billing_imei_data WHERE imei1 = '".$_POST["scanimei"]."'");
	if(mysqli_num_rows($res_imei)>0){
		echo "Serial nos. is already in stock~".$_POST["indx"]."~danger~fa-ban";
	}else{
		echo "Correct~".$_POST["indx"]."~success~fa-check-circle";
	}
}
///by shekhar on 20/03/23
if($_POST["scanimeireturn"]){
	$res_imei = mysqli_query($link1,"SELECT id,prod_code,owner_code FROM billing_imei_data WHERE imei1 = '".$_POST["scanimeireturn"]."' order by id desc");
	if(mysqli_num_rows($res_imei)>0){
		$row_imei = mysqli_fetch_assoc($res_imei);
		if($row_imei["prod_code"]==$_POST["reqmodel"]){
			if($row_imei["owner_code"]==$_POST["reqowner"]){
				echo "Correct~".$_POST["indx"]."~success~fa-check-circle";
			}
			else{
				echo "Ownership is invalid~".$_POST["indx"]."~danger~fa-ban";
			}
		}else{
			echo "Serial product code is not matched~".$_POST["indx"]."~danger~fa-ban";
		}
	}else{
		echo "Serial nos. is not in DB~".$_POST["indx"]."~danger~fa-ban";
	}
}
if($_POST["scanimeisale"]){
	//echo "SELECT id,owner_code,prod_code,imei1 FROM billing_imei_data WHERE imei1 = '".$_POST["scanimei"]."' order by id desc".$_POST['reqowner'];
	$res_imei = mysqli_query($link1,"SELECT id,owner_code,prod_code,imei1,stock_type FROM billing_imei_data WHERE imei1 = '".$_POST["scanimeisale"]."' order by id desc");
	if(mysqli_num_rows($res_imei)>0){
		$row_imei = mysqli_fetch_assoc($res_imei);
		///// check IMEI owner
		//echo $_POST['reqowner']." == ".$row_imei["owner_code"]."<br><br>";
		if($_POST['reqowner'] == $row_imei["owner_code"]){
			///// check IMEI belongs to requested model
			if($_POST['reqmodel'] == $row_imei["prod_code"]){
				///// check Serial stock type
				if($_POST['serialtype']==$row_imei["stock_type"]){
					echo "Correct~".$_POST["indx"]."~success~fa-check-circle";
				}else{
					echo "Serial stock type not found~".$_POST["indx"]."~warning~fa-exclamation";
				}
			}else{
				echo "Wrong Serial no. picked~".$_POST["indx"]."~warning~fa-exclamation";	
			}
		}else{
			echo "Not in stock~".$_POST["indx"]."~danger~fa-ban";
		}
	}else{
		echo "Not found in DB~".$_POST["indx"]."~danger~fa-ban";
	}
}
///// find the scheme benifite /////////////
if($_POST["schemeInfo"]){
	$foc_flag = 0;
	/////// Select Scheme details ////////
	$sql_schm = "SELECT scheme_based_on, scheme_given_type, scheme_given, scheme_based_type FROM scheme_master where   scheme_code = '".$_POST['schemeApplicable']."'   "; 
	$res_schm = mysqli_fetch_array(mysqli_query($link1,$sql_schm));
	
	if($res_schm['scheme_given_type'] == "Discount Amount"){
		$totdisc = $res_schm['scheme_given'];
		$foc_flag = 0;
	}else if($res_schm['scheme_given_type'] == "Discount Percentage"){
		$totdisc = (($_POST["schemeInfo"] * $res_schm['scheme_given'])/100);
		$foc_flag = 0;
	}else if($res_schm['scheme_given_type'] == "FOC Qty"){
		$totdisc = $res_schm['scheme_given'];
		$foc_flag = 1;
	}else{}
	if($foc_flag == 1){ $tot_disc = (int)$totdisc; }else{ $tot_disc = $totdisc; }
	echo $tot_disc."~".$foc_flag."~".$_POST['schemeInfo']."~".$_POST['valINDNo']."~".$_POST['valPrd']."~".$_POST['prdDtlVal']."~".$_POST['schemeApplicable'];
}
//////////////////  Get scheme	dropdown
if ($_POST['sbt']) {
	echo "<select name='scheme_given_type' id='scheme_given_type' class='form-control required' required onchange='show_INput(this.value);'><option value=''> -- Please Select -- </option>";
	if($_POST['sbt'] == "Total Amount"){
		echo "<option value='Discount Amount'>Discount Amount</option><option value='Discount Percentage'>Discount Percentage</option>";
	}else{
		echo "<option value='FOC Qty'>FOC Qty</option>";
	}
	echo "</select>";
}

///// check coupon code validity //////////////////////////////////////////////////////////////
if($_POST["checkCouponIsAvl"]){
	///////check coupon code is available  on product or not /////////////////////////////
						       $product_det = mysqli_fetch_array(mysqli_query($link1 , "select id , productcategory, productsubcat from product_master where productcode = '".$_POST['productcode']."' "));      
		                         ///// check coupon code is available on productid of product/////////////////////////////	 
							$coupon_det =    mysqli_query($link1 , "select coupon_code from coupon_mapping where coupon_code = '".$_POST["checkCouponIsAvl"]."' and status = 'Y' and   find_in_set('".$product_det['id']."',productid) <> 0      ");	
							if(mysqli_num_rows($coupon_det) >0){
							  $check_flag = 'Y';
							  }else {
							  $check_flag = '';
							  }
					
							
									  //////  check coupon code availabel on product subcategory 
									   $coupon_subcatdet =mysqli_query($link1 , " select coupon_code from coupon_mapping where  coupon_code = '".$_POST["checkCouponIsAvl"]."' and status = 'Y' and  find_in_set('".$product_det['productsubcat']."',prod_subcat) <> 0  ");	
									    if(mysqli_num_rows($coupon_subcatdet)> 0){
										 $check_flagsub = 'Y';
										  }
										  else {
										  $check_flagsub = '';
										  }
										  //// check coupon_code available on product category ////////////////
										   $coupon_prodcatdet = mysqli_query($link1 , " select coupon_code from coupon_mapping where coupon_code = '".$_POST["checkCouponIsAvl"]."' and status = 'Y' and   find_in_set('".$product_det['productcategory']."',prod_cat) <> 0   ");
										     if(mysqli_num_rows($coupon_prodcatdet) >0){
											 $check_flagprod = 'Y';
											 }
											 else {
											   $check_flagprod = '';
											 
											 }	
		if($check_flagprod == 'Y' || $check_flagsub == 'Y' || $check_flag == 'Y') {							
	    ///////  check validity ////////////////////////////////////////////////////////////////////
		$coupon_data = mysqli_fetch_array(mysqli_query($link1 , "select coupon_code , amount from coupon_master  where valid_to >= $today and  coupon_code = '".$_POST["checkCouponIsAvl"]."' "));
		if($coupon_data['coupon_code'] != ''){
		 $coupon_check = $coupon_data['coupon_code'];
		 $coupon_amt = $coupon_data['amount'];
		 }else {
		 $coupon_check = '';	
		 $coupon_amt = '';	 
		  }
		 } 
	 echo $coupon_check."~".$coupon_amt."~".$_POST['indexval'];
}
//////////////////  Get combo product for selected combo model written on 03 jan 2022 by shekhar
if ($_POST['combModel']) {
	$arr_comboprod = array();
	$total_prodqty = 0;
    $combo_qry = "SELECT bom_partcode,bom_qty FROM combo_master WHERE bom_modelcode='".$_POST['combModel']."' AND status='1'";
    $combo_res = mysqli_query($link1, $combo_qry);
	while($combo_row = mysqli_fetch_array($combo_res)){
		////// get product name
		$res_prd = mysqli_query($link1,"SELECT productname FROM product_master WHERE productcode='".$combo_row["bom_partcode"]."'");
		$row_prd = mysqli_fetch_assoc($res_prd);
		////// get product combo price
		$res_cmbprice = mysqli_query($link1,"SELECT combo_price FROM price_master WHERE product_code='".$combo_row["bom_partcode"]."' AND state='".$_POST['fromstate']."' AND location_type='".$_POST['idtype']."' AND status='active'");
		$row_cmbprice = mysqli_fetch_assoc($res_cmbprice);
		///// make qty as per combo model qty
		$total_prodqty = $combo_row["bom_qty"] * $_POST['combModelQty'];
		$arr_comboprod[] = $combo_row["bom_partcode"]."^".$total_prodqty."^".$row_cmbprice["combo_price"]."^".$row_prd["productname"];
	}
    echo json_encode($arr_comboprod) . "~" . $_POST['indxx'];
}
//////////////////  Get price and tax for selected combo model written on 03 jan 2022 by shekhar
if ($_POST['combomodelinfo']) {
	$res_gst = mysqli_query($link1,"SELECT sgst,cgst,igst FROM tax_hsn_master WHERE hsn_code='".$_POST['combomodelhsn']."'");
	$row_gst = mysqli_fetch_assoc($res_gst);
	////// get rate/price
    $res_price = mysqli_query($link1,"SELECT price,mrp FROM price_master WHERE product_code='".$_POST['combomodelinfo']."' AND state='".$_POST['fromstate']."' AND location_type='".$_POST['idtype']."'");
    $row_price = mysqli_fetch_array($res_price);
	if ($row_price['price']) {
        $price = $row_price['price'] ;
    } else {
        $price = "0.00";
    }      
    echo  $row_gst['sgst'] . "~" . $row_gst['cgst'] . "~" . $row_gst['igst']. "~".$price;
}
//////////////////  Get sub department by selecting department dropdown
if ($_POST['deptid']) {
    echo "<select name='subdepartment' id='subdepartment' class='required form-control' required><option value=''>--Please Select--</option>";
    $res_sdept=mysqli_query($link1,"select * from hrms_subdepartment_master where status='1' AND departmentid='".$_POST['deptid']."' order by department,subdept");
    while($row_sdept=mysqli_fetch_assoc($res_sdept)){
        echo "<option value='".$row_sdept['subdeptid']."'>";
        echo $row_sdept['subdept'] . "</option>";
    }
    echo "</select>";
}
////// get location stock written on 05 jul 2022 by shekhar for purpose of stock movement with in location
if($_POST['sm_mainloc']){
	$sm_qry = "SELECT ".$_POST['stktype']." FROM stock_status WHERE asc_code='".$_POST['sm_mainloc']."' AND sub_location='".$_POST['sm_subloc']."' AND partcode='".$_POST['sm_partcode']."'";
    $sm_res = mysqli_query($link1, $sm_qry);
	$sm_row = mysqli_fetch_array($sm_res);
	echo $sm_row[0]."~".$_POST['indxx'];
}
///// get ac head dropdown on basis of ac group written by shekhar on 08 jul 2022
if ($_POST['acgroup']) {
    echo "<select name='ac_head' id='ac_head' class='form-control'";
	if($_POST['op'] == 'add'){
	echo " required ";
	} else {
	echo " disabled";
	}
	echo "><option value=''>Please Select</option>";
    $res_achead=mysqli_query($link1,"select id, head_name from account_head_master where status='Active' AND group_id='".$_POST['acgroup']."'");
    while($row_achead=mysqli_fetch_array($res_achead)){
        echo "<option value='".$row_res['id']."~".$row_achead['head_name']."'>";
        echo $row_achead['head_name']."</option>";
    }
}
////// check serial no. against stock conversion serial upload written by shekhar on 06 sep 2022
if($_POST["scanserialstockconvert"]){
	$res_imei = mysqli_query($link1,"SELECT id,owner_code,prod_code,imei1,stock_type FROM billing_imei_data WHERE imei1 = '".$_POST["scanserialstockconvert"]."' ORDER BY id DESC");
	if(mysqli_num_rows($res_imei)>0){
		$row_imei = mysqli_fetch_assoc($res_imei);
		///// check Serial owner
		if($_POST['reqowner'] == $row_imei["owner_code"]){
			///// check Serial belongs to requested model
			if($_POST['reqmodel'] == $row_imei["prod_code"]){
				///// check Serial stock type
				if($_POST['serialtype']==$row_imei["stock_type"]){
					echo "Correct~".$_POST["indx"]."~success~fa-check-circle";
				}else{
					echo "Serial stock type not found~".$_POST["indx"]."~warning~fa-exclamation";
				}
			}else{
				echo "Wrong Serial picked~".$_POST["indx"]."~warning~fa-exclamation";
			}
		}else{
			echo "Not in stock~".$_POST["indx"]."~danger~fa-ban";
		}
	}else{
		echo "Not found in DB~".$_POST["indx"]."~danger~fa-ban";
	}
}
////// get only selected data on the typing basis written by shekhar on 30 sep 2022
if($_POST['requestFor']=="billto"){
	if(!isset($_POST['searchCust'])){ 
  		$fetchData = mysqli_query($link1,"SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code IN (SELECT mapped_code FROM mapped_master WHERE uid='" . $_REQUEST['po_from'] . "' AND status='Y') ORDER BY name LIMIT 10");
	}else{ 
	  	$search = $_POST['searchCust'];
		if(substr($_REQUEST['po_from'],0,4)=="DEHO" || substr($_REQUEST['po_from'],0,4)=="EABR"){
			$fetchData = mysqli_query($link1,"SELECT asc_code, name, city, state, id_type FROM asc_master WHERE status='Active' AND (name like '%".$search."%' OR asc_code like '%".$search."%') ORDER BY name LIMIT 10");
		}else{
			$fetchData = mysqli_query($link1,"SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code IN (SELECT mapped_code FROM mapped_master WHERE uid='" . $_REQUEST['po_from'] . "' AND status='Y') AND (name like '%".$search."%' OR asc_code like '%".$search."%') ORDER BY name LIMIT 10");
		}
	}
	$data = array();
	while ($row = mysqli_fetch_array($fetchData)) {
		if ($row['id_type'] != 'HO') {    
  			$data[] = array("id"=>$row['asc_code'], "text"=>$row['name']." | ".$row['city']." | ".$row['state']." | ".$row['asc_code']);
		}
	}
	echo json_encode($data);
}
////// get only selected data on the typing basis written by shekhar on 16 nov 2022 for all 
if($_POST['requestFor']=="allloc"){
	if(!isset($_POST['searchCust'])){ 
  		$fetchData = mysqli_query($link1,"SELECT a.location_id, b.name, b.city, b.state, b.id_type FROM access_location a, asc_master b WHERE a.uid='".$_POST['userid']."' AND a.status='Y' AND a.location_id=b.asc_code AND b.id_type IN ('DS','HO','BR') AND b.status='Active' ORDER BY b.name LIMIT 10");
	}else{ 
	  	$search = $_POST['searchCust'];
		$fetchData = mysqli_query($link1,"SELECT a.location_id, b.name, b.city, b.state, b.id_type FROM access_location a, asc_master b WHERE a.uid='".$_POST['userid']."' AND a.status='Y' AND a.location_id=b.asc_code AND b.id_type IN ('DS','HO','BR')  AND b.status='Active' AND (b.name like '%".$search."%' OR b.asc_code like '%".$search."%') ORDER BY b.name LIMIT 10");
	}
	$data = array();
	while ($row = mysqli_fetch_array($fetchData)) {    
  		$data[] = array("id"=>$row['location_id'], "text"=>$row['name']." | ".$row['city']." | ".$row['state']." | ".$row['location_id']);
	}
	echo json_encode($data);
}
/// get Combo Details devlop by shekhar on 13 jan 2023
if($_POST["comboProduct"]){
	$response = "<table class=table table-bordered width=100%><thead><tr><th>Product</th><th>Qty</th></tr></thead><tbody>";
	$sql = "SELECT bom_partcode,bom_qty,bom_unit FROM combo_master WHERE bom_modelcode='".$_POST["comboProduct"]."' AND status='1'";
	$res = mysqli_query($link1, $sql);
	if(mysqli_num_rows($res)>0){
		while($row = mysqli_fetch_array($res)){
			///// get parameter name
			$pmn = mysqli_fetch_assoc(mysqli_query($link1,"SELECT productname FROM product_master WHERE productcode='".$row["bom_partcode"]."'"));
			$response .= "<tr><td>".$pmn["productname"]."</td><td>".round($row['bom_qty'])." ".$row['bom_unit']."</td></tr>";
		}
	}else{
		$response .= "<tr><td colspan=2>No specification found for selected combo</td></tr>";
	}
	$response .= "</tbody></table>";
	echo $response."~".$_POST['indxx'];
}
//////////////////  Get available stock of product for selected location and godown with its all stock type written on 24 FEB 23 by shekhar
if ($_POST['getAllTypeLocStk']) {
	$sql_stock = "SELECT okqty, broken, missing FROM stock_status WHERE asc_code='" . $_POST['loccode'] . "' AND sub_location='".$_POST['godown']."' AND partcode='" . $_POST['getAllTypeLocStk'] . "'";
    $res_stock = mysqli_query($link1, $sql_stock);
	$row_stock = mysqli_fetch_array($res_stock);
    echo $row_stock[0] . "~" .$row_stock[1] . "~" .$row_stock[2] . "~" . $_POST['indxx'];
}
////// get only selected data on the typing basis written by shekhar on 12 apr 2023
if($_POST['requestFor']=="srchlocaion"){
	$data = array();
	/////// search from asc_master
	if(!isset($_POST['searchFromLoc'])){ 
  		$fetchData = mysqli_query($link1,"SELECT a.location_id, b.name, b.city, b.state, b.id_type FROM access_location a, asc_master b WHERE a.uid='".$_POST['userid']."' AND a.status='Y' AND a.location_id=b.asc_code AND b.id_type IN ('HO','BR','DS','DL','RT','SR') AND b.status='Active' ORDER BY b.name LIMIT 10");
	}else{ 
	  	$search = $_POST['searchFromLoc'];
		$fetchData = mysqli_query($link1,"SELECT a.location_id, b.name, b.city, b.state, b.id_type FROM access_location a, asc_master b WHERE a.uid='".$_POST['userid']."' AND a.status='Y' AND a.location_id=b.asc_code AND b.id_type IN ('HO','BR','DS','DL','RT','SR')  AND b.status='Active' AND (b.name like '%".$search."%' OR b.asc_code like '%".$search."%') ORDER BY b.name LIMIT 10");
	}
	while ($row = mysqli_fetch_array($fetchData)) {    
  		$data[] = array("id"=>$row['location_id'], "text"=>$row['name']." | ".$row['city']." | ".$row['state']." | ".$row['location_id']);
	}
	/////// search from vendor_master
	if(!isset($_POST['searchFromLoc'])){ 
  		$fetchData = mysqli_query($link1,"SELECT id, name, city, state FROM vendor_master WHERE status='Active' ORDER BY name LIMIT 10");
	}else{ 
	  	$search = $_POST['searchFromLoc'];
		$fetchData = mysqli_query($link1,"SELECT id, name, city, state FROM vendor_master WHERE status='Active' AND (name like '%".$search."%' OR id like '%".$search."%') ORDER BY name LIMIT 10");
	}
	while ($row = mysqli_fetch_array($fetchData)) {    
  		$data[] = array("id"=>$row['id'], "text"=>$row['name']." | ".$row['city']." | ".$row['state']." | ".$row['id']);
	}
	echo json_encode($data);
}
///// check FIFO written on 19 may 23 by shekhar
if($_POST['checkFIFOser']){
	////// check fifo serial is enabled
	if($_POST['fifoflag']=="Y"){
		$resp_fifo = getSerialFiFoCheck($_POST["scanserarr"],$_POST['reqmodel'],$_POST['reqowner'],$_POST['reqsubloc'],$link1);
		if($resp_fifo["code"]=="1"){
			$flag_fifo = 1;
		}else{
			$flag_fifo = 0;
		}
	}else{
		$flag_fifo = 1;
	}
	if($flag_fifo=="1"){
		echo "Correct~".$_POST["indx"]."~success~fa-check-circle";
	}else{
		if($flag_fifo=="0"){
			echo $resp_fifo["msg"]."~".$_POST["indx"]."~warning~fa-exclamation";
		}else{
			echo "Something went wrong~".$_POST["indx"]."~warning~fa-exclamation";
		}
	}
}
/////// check serial no. valid or not
/////// get serial no. validate with its partcode written by shekhar on 16 jun 2023
if($_POST['checkSerialValid']){
	$rtn_msg = "";
	$serial = $_POST['checkSerialValid'];
	//// check serial no. is in DB or not
	$res_indb = mysqli_query($link1,"SELECT prod_code,doc_no,transaction_date FROM billing_imei_data WHERE imei1='".$serial."' ORDER BY id DESC");
	if(mysqli_num_rows($res_indb)>0){
		$row_indb = mysqli_fetch_assoc($res_indb);
		$partcode = $row_indb['prod_code'];
		/////get product details
		$res_prod = mysqli_query($link1,"SELECT productname,model_name,productcategory,brand,productsubcat FROM product_master WHERE productcode='".$partcode."'");
		$row_prod = mysqli_fetch_assoc($res_prod);
		$rtn_msg = "1~".$_POST['indx']."~".$partcode."~".$row_prod['productname']."~".$row_prod['model_name']."~".$row_prod['productcategory']."~".$row_prod['brand']."~".$row_prod['productsubcat'];
	}else{
		$rtn_msg = "0~".$_POST['indx']."~Serial no. not found in DB";
	}
	echo $rtn_msg;
}
////
if($_POST['sessionstr']){
	session_start();
	$_SESSION['snos'] = str_replace(','.$_POST['sessionstr'], '', $_SESSION['snos']);
	$_SESSION['snos'] = str_replace($_POST['sessionstr'].',', '', $_SESSION['snos']);
	$_SESSION['snos'] = str_replace($_POST['sessionstr'], '', $_SESSION['snos']);
}
//// for multi line written on 29 DEC 2023 by shekhar
if ($_POST['prodmultiline']) {
    echo "<select  name='product_code[".$_POST['indxx']."]' id='product_code[".$_POST['indxx']."]' class='form-control required' required><option value=''>--Please Select--</option>";
    $prod_query = "SELECT productcode,productname FROM product_master WHERE productsubcat='".$_POST['prodmultiline']."' AND status='Active' ORDER BY productname";
    $prod_res = mysqli_query($link1, $prod_query);
    while ($row_prod = mysqli_fetch_array($prod_res)) {
        echo "<option value='".$row_prod['productcode'] . "'>";
        echo $row_prod['productname']."</option>";
    }
    echo "</select>";
}
?>