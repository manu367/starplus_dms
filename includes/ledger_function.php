<?php
/////// function parameter sequence
//// 1. location code on which trasaction is being execute
//// 2. document no. which is being execute
//// 3. document date which is being execute
//// 4. Voucher Type . It means Purchase(1)/Sale(2)/Credit Note(3)/Debit Note(4)/Payment(5)/Receipt(6)
//// 5. Voucher For . It means Purchase/Sale/Credit Note/Debit Note/Payment/Receipt
//// 6. Tax Percentage and its tax amount array which are applicable of selected transaction
//// 7. Each line of item total value array with its tax percentage
//// 8. TCS % if applicable
//// 9. TCS Amount if applicable
//// 10. Round Off value
//// 11. GST Type either it will IGST or CGST/SGST
//// 12. All ledger name which are related to current transaction
//// 13. Account group name
//// 14. Account head name
//// 15. DB connection link
//// 16. transaction flag for commmit/rollback
////// function to store ledger transaction for tally purpose
function storeLedgerTransaction($locationcode,$docno,$docdate,$vchtype,$vchfor,$arr_tax,$arr_val,$tcs_per,$tcs_amt,$round_off,$gst_type,$arr_ldg_name,$ac_group,$ac_head,$link1,$flag){/*
	$datetime = date("Y-m-d H:i:s");
	//for any error msg
	$msg = "";
	/////// start ledger entry for tally purpose ///// written by shekhar on 12 july 2022
	/////// retrive ledger name from its array
	$igst_ldg_name = $arr_ldg_name["igstldgname"];///Input IGST @ 
	$cgst_ldg_name = $arr_ldg_name["cgstldgname"];///Input CGST @ 
	$sgst_ldg_name = $arr_ldg_name["sgstldgname"];///Input SGST @ 
	//////
	$igst_doc_ldg_name = $arr_ldg_name["igstdocldgname"];///Central Purchase @ 
	$cgst_doc_ldg_name = $arr_ldg_name["cgstdocldgname"];///Local Purchase @ 
	$sgst_doc_ldg_name = $arr_ldg_name["sgstdocldgname"];///Local Purchase @ 
	//////
	$tcs_ldg_name = $arr_ldg_name["tcsldgname"];///TCS on Purchase
	$roundoff_ldg_name = $arr_ldg_name["roundoffldgname"];////Rounded Off
	/////// 1. entry for voucher from voucher master with extension concatination
	/////get voucher extension name
	$vch_ext_name = getLocExtName($locationcode,"Voucher",$vchtype,$link1);
	/////get ledger extension name
	$ldg_ext_name = getLocExtName($locationcode,"Ledger",$vchtype,$link1);
	////// get voucher name
	$vch_det = explode("~",getVoucherName($vchfor,$link1));
	///// make voucher name for tally
	$voucher_name = $vch_ext_name." ".$vch_det[1];
	/////// 2. entry for purchse ledger
	$arr_ledger = array();
	$arr_ledger_val = array();
	$arr_ledger_type = array();
	if($gst_type == "IGST"){
		/////GST ledger
		foreach($arr_tax as $gstper => $gstamt){
			if($vchfor=="Delivery Challan" || $vchfor=="Receipt Note"){
			
			}else{

				$arr_ledger[] = $igst_ldg_name." ".round($gstper)."%";	
				$arr_ledger_val[] = $gstamt;
				$arr_ledger_type[] = "IGST";
			}
		}
		/////Purchase ledger
		foreach($arr_val as $gstper => $val){
			if($vchfor=="Delivery Challan" || $vchfor=="Receipt Note"){
				$arr_ledger[] = $igst_doc_ldg_name;
			}else{
				$arr_ledger[] = $igst_doc_ldg_name." ".round($gstper)."%";
			}
			$arr_ledger_val[] = $val;
			$arr_ledger_type[] = "ACCOUNT";
			if($vchfor=="Delivery Challan" || $vchfor=="Receipt Note"){
				break;
			}
		}
	}else{
		foreach($arr_tax as $gstper => $gstamt){
			if($vchfor=="Delivery Challan" || $vchfor=="Receipt Note"){
			
			}else{
				$divper = $gstper/2;
				if($gstper=="5.00" || $gstper==5.00){
					$arr_ledger[] = $cgst_ldg_name." ".number_format($divper,'1','.','')."%";
				}else{
					$arr_ledger[] = $cgst_ldg_name." ".round($divper)."%";
				}
				$arr_ledger_val[] = ($gstamt/2);
				$arr_ledger_type[] = "CGST";
				if($gstper=="5.00" || $gstper==5.00){
					$arr_ledger[] = $sgst_ldg_name." ".number_format($divper,'1','.','')."%";
				}else{
					$arr_ledger[] = $sgst_ldg_name." ".round($divper)."%";
				}
				$arr_ledger_val[] = ($gstamt/2);
				$arr_ledger_type[] = "SGST";
			}
		}
		foreach($arr_val as $gstper => $val){
			if($vchfor=="Delivery Challan" || $vchfor=="Receipt Note"){
				$arr_ledger[] = $cgst_doc_ldg_name;
				$arr_ledger_type[] = "ACCOUNT";
			}else{
				
				$arr_ledger[] = $cgst_doc_ldg_name." ".round($gstper)."%";
				
				$arr_ledger_val[] = $val;
				$arr_ledger_type[] = "ACCOUNT";
			}
			if($vchfor=="Delivery Challan" || $vchfor=="Receipt Note"){
				break;
			}
		}
	}
	/////// 3. entry for GST ledger
	////// get voucher id
	for($j=0; $j<count($arr_ledger); $j++){
		///// check ledger is exist or not
		if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM account_ledger WHERE ledger_name='".$arr_ledger[$j]."' AND status='Active'"))==0){
			//////insert in account ledger
			/////get account group
			$res_acgp = mysqli_query($link1,"SELECT id FROM account_group_master WHERE group_name='".$ac_group."' AND status='Active'");
			$row_acgp = mysqli_fetch_assoc($res_acgp);
			/////get account head
			$res_achd = mysqli_query($link1,"SELECT id FROM account_head_master WHERE head_name='".$ac_head."' AND group_id='".$row_acgp["id"]."' AND status='Active'");
			$row_achd = mysqli_fetch_assoc($res_achd);
			/////insert into account ledger
			$res_aclg = mysqli_query($link1,"INSERT INTO account_ledger SET ledger_name='".$arr_ledger[$j]."', ac_head_id='".$row_achd["id"]."', ac_head_name='".$ac_head."',ac_group_id='".$row_acgp["id"]."', ac_group_name='".$ac_group."', status='Active', entry_date='".$datetime."', entry_by='".$_SESSION["userid"]."'");
		}
		////// get ledger details
		$res_ldg_det = mysqli_query($link1,"SELECT * FROM account_ledger WHERE ledger_name='".$arr_ledger[$j]."' AND status='Active'");
		$row_ldg_det = mysqli_fetch_assoc($res_ldg_det);
		/////////
		$make_ledger = $ldg_ext_name." ".$arr_ledger[$j];
		$res_loc_ledger = mysqli_query($link1,"INSERT INTO location_ledger SET transaction_no ='".$docno."', transaction_date='".$docdate."', location_code ='".$locationcode."', voucher_id ='".$vch_det[0]."', voucher_name='".$voucher_name."', ledger_id ='".$row_ldg_det["id"]."', ledger_name='".$make_ledger."', ledger_value='".$arr_ledger_val[$j]."',ledger_type='".$arr_ledger_type[$j]."', ac_head_id ='".$row_ldg_det["ac_head_id"]."', ac_head_name='".$row_ldg_det["ac_head_name"]."', ac_group_id ='".$row_ldg_det["ac_group_id"]."', ac_group_name='".$row_ldg_det["ac_group_name"]."', entry_by='".$_SESSION["userid"]."'");
		############# check if query is not executed
		if(!$res_loc_ledger) {
			$flag = false;
			$msg = "Location Ledger1: " . mysqli_error($link1) . ".";
		}
	}
	/////// 4. entry for TCS ledger if applicable
	if($tcs_per){
		$tcs_ldg = $tcs_ldg_name." ".$tcs_per."%";
		$tcs_ldgamt = $tcs_amt;
		///// check ledger is exist or not
		if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM account_ledger WHERE ledger_name='".$tcs_ldg."' AND status='Active'"))==0){
			//////insert in account ledger
			/////get account group
			$res_acgp = mysqli_query($link1,"SELECT id FROM account_group_master WHERE group_name='".$ac_group."' AND status='Active'");
			$row_acgp = mysqli_fetch_assoc($res_acgp);
			/////get account head
			$res_achd = mysqli_query($link1,"SELECT id FROM account_head_master WHERE head_name='".$ac_head."' AND group_id='".$row_acgp["id"]."' AND status='Active'");
			$row_achd = mysqli_fetch_assoc($res_achd);
			/////insert into account ledger
			$res_aclg = mysqli_query($link1,"INSERT INTO account_ledger SET ledger_name='".$tcs_ldg."', ac_head_id='".$row_achd["id"]."', ac_head_name='".$ac_head."',ac_group_id='".$row_acgp["id"]."', ac_group_name='".$ac_group."', status='Active', entry_date='".$datetime."', entry_by='".$_SESSION["userid"]."'");
		}
		////// get ledger details
		$res_ldg_det = mysqli_query($link1,"SELECT * FROM account_ledger WHERE ledger_name='".$tcs_ldg."' AND status='Active'");
		$row_ldg_det = mysqli_fetch_assoc($res_ldg_det);
		//////
		$res_loc_ledger = mysqli_query($link1,"INSERT INTO location_ledger SET transaction_no ='".$docno."', transaction_date='".$docdate."', location_code ='".$locationcode."', voucher_id ='".$vch_det[0]."', voucher_name='".$voucher_name."', ledger_id ='".$row_ldg_det["id"]."', ledger_name='".$tcs_ldg."', ledger_value='".$tcs_ldgamt."',ledger_type='TCS', ac_head_id ='".$row_ldg_det["ac_head_id"]."', ac_head_name='".$row_ldg_det["ac_head_name"]."', ac_group_id ='".$row_ldg_det["ac_group_id"]."', ac_group_name='".$row_ldg_det["ac_group_name"]."', entry_by='".$_SESSION["userid"]."'");
		############# check if query is not executed
		if(!$res_loc_ledger) {
			$flag = false;
			$msg = "Location Ledger2: " . mysqli_error($link1) . ".";
		}
	}
	/////// 5. entry for round off ledger
	if($round_off!=0.00 && $round_off!=0){
		$ro_ldg = $roundoff_ldg_name;
		$ro_ldgamt = $round_off;
		///// check ledger is exist or not
		if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM account_ledger WHERE ledger_name='".$ro_ldg."' AND status='Active'"))==0){
			//////insert in account ledger
			/////get account group
			$res_acgp = mysqli_query($link1,"SELECT id FROM account_group_master WHERE group_name='".$ac_group."' AND status='Active'");
			$row_acgp = mysqli_fetch_assoc($res_acgp);
			/////get account head
			$res_achd = mysqli_query($link1,"SELECT id FROM account_head_master WHERE head_name='".$ac_head."' AND group_id='".$row_acgp["id"]."' AND status='Active'");
			$row_achd = mysqli_fetch_assoc($res_achd);
			/////insert into account ledger
			$res_aclg = mysqli_query($link1,"INSERT INTO account_ledger SET ledger_name='".$ro_ldg."', ac_head_id='".$row_achd["id"]."', ac_head_name='".$ac_head."',ac_group_id='".$row_acgp["id"]."', ac_group_name='".$ac_group."', status='Active', entry_date='".$datetime."', entry_by='".$_SESSION["userid"]."'");
		}
		////// get ledger details
		$res_ldg_det = mysqli_query($link1,"SELECT * FROM account_ledger WHERE ledger_name='".$ro_ldg."' AND status='Active'");
		$row_ldg_det = mysqli_fetch_assoc($res_ldg_det);
		//////
		$res_loc_ledger = mysqli_query($link1,"INSERT INTO location_ledger SET transaction_no ='".$docno."', transaction_date='".$docdate."', location_code ='".$locationcode."', voucher_id ='".$vch_det[0]."', voucher_name='".$voucher_name."', ledger_id ='".$row_ldg_det["id"]."', ledger_name='".$ro_ldg."', ledger_value='".$ro_ldgamt."',ledger_type='ROUND OFF', ac_head_id ='".$row_ldg_det["ac_head_id"]."', ac_head_name='".$row_ldg_det["ac_head_name"]."', ac_group_id ='".$row_ldg_det["ac_group_id"]."', ac_group_name='".$row_ldg_det["ac_group_name"]."', entry_by='".$_SESSION["userid"]."'");
		############# check if query is not executed
		if(!$res_loc_ledger) {
			$flag = false;
			$msg = "Location Ledger3: " . mysqli_error($link1) . ".";
		}
	}
	return $flag."~".$msg;
	/////// end ledger entry for tally purpose ///// written by shekhar on 12 july 2022	
*/
return "1~";
}
/////// function to get extension name written by shekhar on 13 jul 2022
function getLocExtName($loccode,$ledger_voucher,$extension_for,$link1){
	$sql = mysqli_query($link1,"SELECT extension_name FROM ledger_voucher_extension WHERE location_code='".$loccode."' AND ledger_voucher='".$ledger_voucher."' AND extension_for='".$extension_for."' AND status='Active'"); 
	$row = mysqli_fetch_assoc($sql);
	return $row['extension_name'];
}
/////// function to get voucher name written by shekhar on 13 jul 2022
function getVoucherName($voucher_for,$link1){
	$sql = mysqli_query($link1,"SELECT id,voucher_type FROM voucher_master WHERE voucher_for='".$voucher_for."' AND status='Active'"); 
	$row = mysqli_fetch_assoc($sql);
	return $row['id']."~".$row['voucher_type'];
}