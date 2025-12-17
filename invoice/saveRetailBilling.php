<?php
require_once("../config/config.php");
@extract($_POST);
////// if we want to save data
if ($_POST) {
    if ($_POST['upd'] == 'Process') {
		$bill_from = base64_decode($parentcode);
		$bill_to = base64_decode($partycode);
		$arr_serial = unserialize(base64_decode($_POST['arr_serial']));
        ///// make a string token
        $messageIdent = md5($_POST['upd'] . $bill_from . $bill_to);
        //and check it against the stored value:
        $sessionMessageIdent = isset($_SESSION['retailBill']) ? $_SESSION['retailBill'] : '';
        if ($messageIdent != $sessionMessageIdent) {//if its different:          
            //save the session var:
            $_SESSION['retailBill'] = $messageIdent;
            //// initialize transaction parameters
            $flag = true;
            mysqli_autocommit($link1, false);
            $err_msg = "";
			if($total_qty!='' && $total_qty!=0){
				//// Make System generated Invoice no.//////
				$res_cnt = mysqli_query($link1,"SELECT inv_str,inv_counter FROM document_counter WHERE location_code='".$bill_from."'")or die(mysqli_error($link1));
				if(mysqli_num_rows($res_cnt)){
					$row_cnt = mysqli_fetch_array($res_cnt);
					$invcnt = $row_cnt['inv_counter']+1;
					$pad = str_pad($invcnt,4,0,STR_PAD_LEFT);
					$invno = $row_cnt['inv_str'].$pad;
					///// get parent location details
					$parentloc=getLocationDetails($bill_from,"addrs,disp_addrs,name,city,state,gstin_no,pincode,email,phone",$link1);
					$parentlocdet=explode("~",$parentloc);	
					///// get customer details details
	   				$childloc = getCustomerDetails($bill_to,"customerid,address,customername,city,state,gstin,pincode,emailid,contactno",$link1);
	   				$childlocdet = explode("~",$childloc);
					if($deliveryAddress){$deli_addrs = $deliveryAddress;}else{$deli_addrs = $childlocdet[1];}
					///// Insert Master Data
					$query1 = "INSERT INTO billing_master set from_location='".$bill_from."', to_location='".$bill_to."',from_gst_no='".$parentlocdet[5]."', from_partyname='".$parentlocdet[2]."', party_name='".$childlocdet[2]."', to_gst_no='".$childlocdet[5]."', challan_no='".$invno."', sale_date='".$today."', entry_date='".$today."', entry_time='".$currtime."', entry_by='".$_SESSION['userid']."', type='RETAIL', document_type='INVOICE',basic_cost='".$subTotal."',discount_amt='".$total_discount."',round_off='".$roundOff."',tax_cost='".$totalTax."',total_cost='".$grandTotal."',bill_from='".$bill_from."',bill_topty='".$bill_to."',from_addrs='".$parentlocdet[0]."',disp_addrs='".$parentlocdet[1]."',to_addrs='".$childlocdet[1]."',deliv_addrs='".$deli_addrs."',billing_rmk='".$remark."',po_no='FRONT_SCAN', status='Dispatched', dc_date='".$today."',dc_time='".$currtime."',file_name='SCANNED',imei_attach='Y',from_state='".$parentlocdet[4]."', to_state='".$childlocdet[4]."', from_city='".$parentlocdet[3]."', to_city='".$childlocdet[3]."', from_pincode='".$parentlocdet[6]."', to_pincode='".$childlocdet[6]."', from_phone='".$parentlocdet[8]."', to_phone='".$childlocdet[8]."', from_email='".$parentlocdet[7]."', to_email='".$childlocdet[7]."'";
					$result = mysqli_query($link1,$query1);
					//// check if query is not executed
					if (!$result) {
						 $flag = false;
						 $err_msg = "Error Code1:".mysqli_error($link1);
					}
					/// update invoice counter /////
					$result = mysqli_query($link1,"UPDATE document_counter SET inv_counter = inv_counter+1,update_by='".$_SESSION['userid']."',updatedate='".$datetime."' WHERE location_code='".$bill_from."'");
					//// check if query is not executed
					if (!$result) {
						 $flag = false;
						 $err_msg = "Error Code2:".mysqli_error($link1);
					}
					//print_r($arr_serial);
					////// attach serial no.
					foreach($arr_serial as $product => $serialnos){
						$serial_str = explode(",",$serialnos);
						for($k=0; $k<count($serial_str); $k++){
							/// check imei is already bill or not
						   	$res_imei = mysqli_query($link1,"SELECT owner_code,imei1,imei2 FROM billing_imei_data WHERE imei1='".$serial_str[$k]."' or imei2='".$serial_str[$k]."' ORDER BY id DESC");
						   	$checkimei = mysqli_fetch_assoc($res_imei);
						   	if(mysqli_num_rows($res_imei) >0 && $checkimei['owner_code']==$bill_from){						
								//////////////insert in billing imei data////////////////////////
						 		$result = mysqli_query($link1,"INSERT INTO billing_imei_data  SET from_location='".$bill_from."',to_location='".$bill_to."',owner_code='".$bill_to."',prod_code='".$product."' ,doc_no='".$invno."',imei1='".$checkimei['imei1']."',imei2='".$checkimei['imei2']."'");
								//// check if query is not executed
							   if (!$result) {
								   $flag = false;
								   $err_msg = "Error Code3.1:". mysqli_error($link1) . ".";
							   }
							}else{
								$flag = false;
								$err_msg = "Error Code3.2: Serial nos. not in stock";
							}
						}
					}
					////// insert data value
					for($i = 1; $i < $count; $i++){
						$rowprodcut = "prodcode" . $i;
						$rowqty = "bill_qty" . $i;
						$rowprice = "rowprice" . $i;
						$rowvalue = "value" . $i;
						$rowtotal = "rowsubtotal" . $i;
						$rowsgstPer = "sgstper" . $i;
						$rowsgstAmt = "sgstamt" . $i;
						$rowcgstPer = "cgstper" . $i;
						$rowcgstAmt = "cgstamt" . $i;
						$rowigstPer = "igstper" . $i;
						$rowigstAmt = "igstamt" . $i;
						$rowdisc = "rowdiscount" . $i;
						$rowtotalval = "total_val" . $i;
						$rowschemecode = "sch_cd" . $i;
						$reward_point =  "reward_point".$i;
					    $couponcode  =  "coupon_codename".$i;
						$couponcodeamt  =  "couponamount".$i;
				
						//find the scheme name
						if($rowschemecode != ""){
							$rrr = mysqli_fetch_assoc(mysqli_query($link1,"SELECT scheme_name FROM scheme_master WHERE scheme_code = '".$_POST[$rowschemecode]."' "));
							$rowschemename = $rrr['scheme_name'];
						}else{
							$rowschemename = "";
						}
						//checking row value of product and qty should not be blank
						$getstk = getCurrentStock($bill_from, $_POST[$rowprodcut], "okqty", $link1);
						//// check stock should be available ////
						if ($getstk < $_POST[$rowqty]) {
							$flag = false;
							$err_msg = "Error Code3: Stock is not available";
						} else {
							
						}
						if ($_POST[$rowprodcut] != '' && $_POST[$rowqty] != '' && $_POST[$rowqty] != 0) {
							/////////// insert data
							//$splitrowtax = explode("~", $_POST[$rowtaxtype]);
							$query2 = "INSERT INTO billing_model_data SET from_location='" . $bill_from . "',challan_no='" . $invno . "', prod_code='" . $_POST[$rowprodcut] . "',prod_cat='" . $po_datarow['prod_cat'] . "', qty='" . $_POST[$rowqty] . "', mrp='" . $_POST[$rowmrp] . "', price='" . $_POST[$rowprice] . "', hold_price='" . $_POST[$rowholdrate] . "', value='" . $_POST[$rowtotal] . "',tax_name='" . $splitrowtax[1] . "',tax_per='" . $splitrowtax[0] . "',tax_amt='" . $_POST[$rowtaxamt] . "',discount='" . $_POST[$rowdisc] . "',sgst_per='" . $_POST[$rowsgstPer] . "',sgst_amt='" . $_POST[$rowsgstAmt] . "',cgst_per='" . $_POST[$rowcgstPer] . "',cgst_amt='" . $_POST[$rowcgstAmt] . "',igst_per='" . $_POST[$rowigstPer] . "',igst_amt='" . $_POST[$rowigstAmt] . "', totalvalue='" . $_POST[$rowtotalval] . "',sale_date='" . $today . "',entry_date='" . $today . "', scheme_name = '".$rowschemename."', scheme_code = '".$_POST[$rowschemecode]."', coupon_code = '".$_POST[$couponcode]."' , coupon_amt = '".$_POST[$couponcodeamt]."'  ";
							
							$result2 = mysqli_query($link1, $query2);
							//// check if query is not executed
							if (!$result2) {
								$flag = false;
								$err_msg = "Error Code4:".mysqli_error($link1);
							}
							//// update stock of from loaction
							
						   $result_2 = mysqli_query($link1, "UPDATE stock_status SET okqty=okqty-'" . $_POST[$rowqty] . "',updatedate='" . $datetime . "' WHERE asc_code='" . $bill_from . "' AND sub_location='" . $bill_from . "' AND partcode='" . $_POST[$rowprodcut] . "'");
							//// check if query is not executed
							if (!$result_2) {
								$flag = false;
								$err_msg = "Error Code5:".mysqli_error($link1);
							}
							
							////// entry in Customer Reward Ledger /////////////////////////////
		
						 if(($_POST[$reward_point] != '') && ($_POST[$reward_point] >0)){
						  ///// entry in customer reward ledger //////////////////////
						   mysqli_query($link1 , " insert into customer_reward_ledger set customer_id = '".$bill_to."' , ref_no = '".$invno."' ,rewards = '".$_POST[$reward_point]."' , cr_dr_type = 'CR' , remark = 'EARNED' , entry_date = '".$today."' , entry_by = '".$_SESSION['userid']."' , ip = '".$ip."' , product_code = '".$_POST[$rowprodcut]."'  ");
						 
						  ///////////////////////////////////////////////////////////////////////////
						   }
							
							
							
							///// update stock ledger table
							$flag = stockLedger($invno, $today, $_POST[$rowprodcut], $bill_from, $bill_to, $bill_from, "OUT", "OK", "Retail Invoice", $_POST[$rowqty], $_POST[$rowprice], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
						}// close if loop of checking row value of product and qty should not be blank
					}
					///////  Entry for the scheme start //////////////
					$s = $_POST['count'];
					$t = $_POST['norow'];
					if($_POST['norow']>0){
						for($l=$s; $l<($s+$t); $l++){
							//checking row value of product and qty should not be blank
							$getstk1 = getCurrentStock($bill_from, $_POST['schprd'.$l], "okqty", $link1);
							//// check stock should be available ////
							if ($getstk1 < $_POST['schqty'.$l]) {
								$flag = false;
								$err_msg = "Error Code3.1: Stock is not available";
							} else {
								
							}
							if(($_POST['schqty'.$l] != "") && ($_POST['schprd'.$l] != "")){
								/////////// insert data
								$query2_1 = "INSERT INTO billing_model_data SET from_location='" . $bill_from . "',challan_no='" . $invno . "', prod_code='" . $_POST['schprd'.$l] . "',prod_cat='', qty='" . $_POST['schqty'.$l] . "', mrp='0.00', price='0.00', hold_price='0.00', value='0.00',tax_name='',tax_per= '0.00',tax_amt='0.00',discount='0.00',sgst_per='0.00',sgst_amt='0.00',cgst_per='0.00',cgst_amt='0.00',igst_per='0.00',igst_amt='0.00', totalvalue='0.00',sale_date='" . $today . "',entry_date='" . $today . "', scheme_name = 'FOC', scheme_code = ''";
							
								$result2_1 = mysqli_query($link1, $query2_1);
								//// check if query is not executed
								if (!$result2_1) {
									$flag = false;
									$err_msg = "Error Code4.1:".mysqli_error($link1);
								}
								//// update stock of from loaction
						   		$result2_2 = mysqli_query($link1, "UPDATE stock_status SET okqty=okqty-'" . $_POST['schqty'.$l] . "',updatedate='" . $datetime . "' WHERE asc_code='" . $bill_from . "' AND partcode='" . $_POST['schprd'.$l] . "'");
								//// check if query is not executed
								if (!$result2_2) {
									$flag = false;
									$err_msg = "Error Code5.1:".mysqli_error($link1);
								}
								///// update stock ledger table
						   		$flag = stockLedger($invno, $today, $_POST['schprd'.$l], $bill_from, $bill_to, $bill_from, "OUT", "OK", "Retail Invoice", $_POST['schqty'.$l], "0.00", $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
								/// check imei is already bill or not
							   	$res_imei = mysqli_query($link1,"SELECT owner_code,prod_code,imei1,imei2 FROM billing_imei_data WHERE imei1='".$_POST['schserial'.$l]."' or imei2='".$_POST['schserial'.$l]."' ORDER BY id DESC");
						   		$checkimei = mysqli_fetch_assoc($res_imei);
						   		if(mysqli_num_rows($res_imei) >0 && $checkimei['owner_code']==$bill_from){						
									//////////////insert in billing imei data////////////////////////
						 			$result = mysqli_query($link1,"INSERT INTO billing_imei_data  SET from_location='".$bill_from."',to_location='".$bill_to."',owner_code='".$bill_to."',prod_code='".$checkimei['prod_code']."' ,doc_no='".$invno."',imei1='".$checkimei['imei1']."',imei2='".$checkimei['imei2']."'");
									//// check if query is not executed
								   if (!$result) {
									   $flag = false;
									   $err_msg = "Error Code3.11:". mysqli_error($link1) . ".";
								   }
								}else{
									$flag = false;
									$err_msg = "Error Code3.12: Scheme Serial nos. not in stock";
								}
							}
						}///////  Entry for the scheme stop //////////////
					}
					////// maintain party ledger////
					$flag = partyLedger($bill_from,$bill_to,$invno,$today,$today,$currtime,$_SESSION['userid'],"RETAIL INVOICE",$grandTotal,"DR",$link1,$flag);
					////// insert in activity table////
					$flag = dailyActivity($_SESSION['userid'],$invno,"RETAIL INVOICE","ADD",$ip,$link1,$flag);
					///// check both master and data query are successfully executed
					if ($flag) {
						mysqli_commit($link1);
						$msg = "Invoice is successfully created with ref. no. ".$invno;
						$cflag = "success";
						$cmsg = "Success";
					} else {
						mysqli_rollback($link1);
						$msg = "Request could not be processed ".$err_msg.". Please try again.";
						$cflag = "danger";
						$cmsg = "Failed";
					} 
					mysqli_close($link1);
				}else{
					$msg = "Request could not be processed . Invoice string not found.";
					$cflag = "danger";
					$cmsg = "Failed";
				}
			}else{
				$msg = "Request could not be processed . Please bill atleast 1 qty.";
				$cflag = "danger";
            	$cmsg = "Failed";
			}
		}else {
            //you've sent this already!
            $msg = "Re-submission is not allowed";
            $cflag = "warning";
            $cmsg = "Warning";
        }
        ///// move to parent page
        header("location:retailbillinglist.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
        exit;
	}else{
		echo "You are not authorized to access this directly";
	}
}
?>