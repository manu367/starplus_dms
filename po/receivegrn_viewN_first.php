<?php
require_once("../config/config.php");
//////////////// decode challan number////////////////////////////////////////////////////////
$po_no = base64_decode($_REQUEST['id']);
////////////////////////////////////////// fetching datta from table/////////////////////////
$po_sql = "SELECT * FROM vendor_order_master WHERE po_no='".$po_no."'";
$po_res = mysqli_query($link1,$po_sql);
$po_row = mysqli_fetch_assoc($po_res);
///////// get location details
//$loc_det =  explode("~",getLocationDetails($po_row['po_from'],"id_type,state",$link1));
$loc_det =  explode("~",getLocationDetails($po_row['po_from'],"name,id_type,email,phone,addrs,disp_addrs,city,state,statecode,pincode,gstin_no",$link1));
//////// get vendor details
$vend_det = explode("~",getVendorDetails($po_row['po_to'],"name,city,state,address,phone,email,pincode,bill_address,gstin_no",$link1));
$msg="";
///// after hitting receive button ///
@extract($_POST);
	if($_POST){
		if ($_POST['upd']=='Receive'){
			mysqli_autocommit($link1, false);
			$flag = true;
			$error_msg = "";
			$status = "Received";
		   	#########  invocie attachment code ##############################
			if($_FILES['inv_attachment']['name']){
				$folder="grn_doc";
				$file_name = $_FILES['inv_attachment']['name'];
				$file_tmp = $_FILES['inv_attachment']['tmp_name'];
				$up = move_uploaded_file($file_tmp,"../".$folder."/".time().$file_name);
				$path1 = "../".$folder."/".time().$file_name;	
			}
			//// pick max count of grn
			$res_grncount = mysqli_query($link1,"SELECT grn_counter FROM document_counter WHERE location_code='".$_POST['po_from']."'");
			$row_grncount = mysqli_fetch_assoc($res_grncount);
			///// make grn sequence
			$nextgrnno = $row_grncount['grn_counter'] + 1;
			$grnno = "GRN"."".$_POST['po_from']."".str_pad($nextgrnno,4,0,STR_PAD_LEFT);
			//// first update the job count
			$updst = mysqli_query($link1,"UPDATE document_counter set grn_counter='".$nextgrnno."' where location_code='".$_POST['po_from']."'");
			//// check if query is not executed
			if (!$updst) {
				 $flag = false;
				 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
			}
			
			
				
			////// run data cycle of grn and get posted value of receive qty
			$tot_rcvqty=0;
			$arr_tax = array();
			$arr_val = array();
			$gst_type = "";
			$tot_igst = 0.00;
			$tot_sgst = 0.00;
			$tot_cgst = 0.00;
			$sql_sp="select * from vendor_order_data where po_no ='".$_POST['po_no']."'";
			$res_grnData=mysqli_query($link1,$sql_sp);
			while($row_grnData=mysqli_fetch_assoc($res_grnData)){
				$gstper = "";
				///// initialize posted variables
				$bill_qty = "inv_qty".$row_grnData['id'];
				//$req_qty="req_qty".$row_grnData['id'];			
				$bill_price = "price".$row_grnData['id'];
				$bill_value = "po_value".$row_grnData['id'];
				$bill_cgstper = "cgst_per".$row_grnData['id'];
				$bill_cgstamt = "cgst_amt".$row_grnData['id'];
				$bill_sgstper = "sgst_per".$row_grnData['id'];
				$bill_sgstamt = "sgst_amt".$row_grnData['id'];
				$bill_igstper = "igst_per".$row_grnData['id'];
				$bill_igstamt = "igst_amt".$row_grnData['id'];
				$bill_totval = "totalval".$row_grnData['id'];			
				//$bill_shipqty="shippedqty".$row_grnData['id'];
				$bill_okqty="ok_qty".$row_grnData['id'];
				$bill_damageqty="damage_qty".$row_grnData['id'];
				$bill_missqty="miss_qty".$row_grnData['id'];
				$bill_excessqty="excess".$row_grnData['id'];
				//$partcode="partcode".$row_grnData['id'];
				//$lotno="lot_no".$row_grnData['id'];
				///$fspprice="fsp_price".$row_grnData['id'];
				//$wid="wid".$row_grnData['id'];
				/////////				
				if($_POST[$bill_okqty]!=""){
					if($_POST[$bill_okqty] !=0 && $_POST[$bill_okqty] !="" && $_POST[$bill_okqty] !=0.00){	
						//// insert grn data 	
					   	$value = $_POST[$bill_okqty] *  $_POST[$bill_price];
					   	if($_POST[$bill_sgstper]){
							$sgst_amt= ($value *$_POST[$bill_sgstper])/100 ;
							$cgst_amt = ($value *$_POST[$bill_cgstper])/100 ;
							
							$gstper = $_POST[$bill_sgstper]+$_POST[$bill_cgstper];
							
							$arr_tax[$gstper] += $sgst_amt + $cgst_amt;
							$arr_val[$gstper] += $value;
							
							$gst_type = "SGST-CGST";
						} else {
							$igst_amt= ($value *$_POST[$bill_igstper])/100 ;
							
							$gstper = $_POST[$bill_igstper];
							
							$arr_tax[$gstper] += $igst_amt;
							$arr_val[$gstper] += $value;
							
							$gst_type = "IGST";
						}					   
						$totalval = $value +$igst_amt+$cgst_amt+$sgst_amt;
					   
						$req_ins2 = "insert into billing_model_data  set challan_no ='".$grnno."' , prod_code ='".$row_grnData["prod_code"]."',from_location='".$po_to."', qty='".$_POST[$bill_qty]."',okqty='".$_POST[$bill_okqty]."' ,damageqty='".$_POST[$bill_damageqty]."',missingqty='".$_POST[$bill_missqty]."',excess='".$_POST[$bill_excessqty]."' , price='".$_POST[$bill_price]."',value='".$value."',sgst_per='".$_POST[$bill_sgstper]."',sgst_amt='".$sgst_amt."',cgst_per='".$_POST[$bill_cgstper]."',cgst_amt='".$cgst_amt."' ,igst_per='".$_POST[$bill_igstper]."',igst_amt='".$igst_amt."' ,totalvalue ='".$totalval."' , entry_date = '".$today."' , sale_date = '".$today."'";
						$req_res2 = mysqli_query($link1,$req_ins2);
						//// check if query is not executed
						if (!$req_res2) {
							$flag = false;
						 	$error_msg = "Error details2: " . mysqli_error($link1) . ".";
						}
						///// sum of  ok, damge , missing qty and minus from from pending qty //////////////
					 	$sum = $_POST[$bill_okqty]+$_POST[$bill_missqty]+$_POST[$bill_damageqty];			   
						//// update supplier PO data 
					
						$upd_spd = mysqli_query($link1,"update vendor_order_data set pending_qty=pending_qty-'".$sum."' where id='".$row_grnData['id']."'");
						//// check if query is not executed
						$okqty=$_POST[$bill_okqty]+$_POST[$bill_excessqty];		
					
					
							//////////  update inventory  start  ////////////////////////////		
							if(mysqli_num_rows(mysqli_query($link1,"select partcode from stock_status where partcode='".$row_grnData["prod_code"]."' and asc_code='".$po_from."'"))>0){
								$result=mysqli_query($link1,"update stock_status set qty= qty+'".$okqty."' , okqty=okqty+'".$okqty."',broken=broken+'".$_POST[$bill_damageqty]."',missing=missing+'".$_POST[$bill_missqty]."',updatedate='".$datetime."' where partcode='".$row_grnData["prod_code"]."' and asc_code='".$po_from."' ");
							}
							else{
								//// if product is not exist then add in inventory
								$result=mysqli_query($link1,"insert into stock_status set asc_code='".$po_from."',partcode='".$row_grnData["prod_code"]."',qty = '".$okqty."' , okqty='".$okqty."',broken='".$_POST[$bill_damageqty]."',missing='".$_POST[$bill_missqty]."',updatedate='".$datetime."'");
							}
							//// check if query is not executed
							if (!$result) {
						 		$flag = false;
						 		$error_msg = "Error details4: " . mysqli_error($link1) . ".";
							}
							///////////  entry in stock ledger///////////////////////////////////////////////////////////
					 		if($okqty!=0 && $okqty!="" && $okqty!=0.00){					
								$flag=stockLedger($grnno,$today,$row_grnData["prod_code"],$po_to,$po_from,$po_from,"IN","OK","Receive Against GRN",$okqty,$_POST[$bill_price],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag); 
						
							}
							if($_POST[$bill_damageqty]!=0 && $_POST[$bill_damageqty]!="" && $_POST[$bill_damageqty]!=0.00){
								$flag=stockLedger($grnno,$today,$row_grnData["prod_code"],$po_to,$po_from,$po_from ,"IN","DAMAGE","Receive Against GRN",$_POST[$bill_damageqty],$_POST[$bill_price],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
							}
							if($_POST[$bill_missqty]!=0 && $_POST[$bill_missqty]!="" && $_POST[$bill_missqty]!=0.00){
					  			$flag=stockLedger($grnno,$today,$row_grnData["prod_code"],$po_to,$po_from,$po_from ,"IN","MISSING","Receive Against GRN",$_POST[$bill_missqty],$_POST[$bill_price],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
							}
				 		
						$tot_rcvqty+=$_POST[$bill_okqty];
					}
			 	}	
				$basic_cost+= $value;
				$grandtot+= $totalval;
				$toaxtot+= ($sgst_amt+$cgst_amt+$igst_amt);
				$tot_igst += $igst_amt;
				$tot_cgst += $cgst_amt;
				$tot_sgst += $sgst_amt;
			}//// close while loop
			/////////////////////////////// insert data into grn master  table///////////////////////////////////////////////
		 	$grn_master="insert into billing_master set from_location ='".$_POST['po_to']."', to_location='".$_POST['po_from']."',from_gst_no='".$vend_det[8]."', from_partyname='".$vend_det[0]."', party_name='".$loc_det[0]."', to_gst_no='".$loc_det[10]."' ,ref_no='".$_POST['po_no']."', receive_date='".$today."' ,receive_time='".$time."', entry_date ='".$today."' , status='".$status."' , challan_no='".$grnno."', basic_cost='".$basic_cost."', tax_cost='".$toaxtot."',total_sgst_amt='".$tot_sgst."',total_cgst_amt='".$tot_cgst."',total_igst_amt='".$tot_igst."',round_off='".$round_off."', total_cost='".$grandtot."',tcs_per='".$tcs_per."', tcs_amt='".$tcs_amt."' , inv_ref_no='".$_POST['postinv_no']."', receive_remark='".$_POST['rcv_rmk']."',type='GRN',document_type='INVOICE', sale_date = '".$today."' ,grn_doc = '".$path1."',from_state='".$vend_det[2]."', to_state='".$loc_det[7]."', from_city='".$vend_det[1]."', to_city='".$loc_det[6]."', from_pincode='".$vend_det[6]."', to_pincode='".$loc_det[9]."', from_phone='".$vend_det[4]."', to_phone='".$loc_det[3]."', from_email='".$vend_det[5]."', to_email='".$loc_det[2]."', from_addrs='".$vend_det[3]."', disp_addrs='".$vend_det[3]."', to_addrs='".$loc_det[4]."', deliv_addrs='".$po_row["delivery_address"]."'";
	
			$result=mysqli_query($link1,$grn_master);
			//// check if query is not executed
			if (!$result) {
				 $flag = false;
				 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
			}
		
			////// check  amnd update status///////////////		
			 $sel_pend_qty=mysqli_fetch_array(mysqli_query($link1,"select sum(pending_qty) as pend_qty from vendor_order_data where po_no='".$_POST['po_no']."'"));
			 if($sel_pend_qty['pend_qty']==0.00){ 
				 $res2 = mysqli_query($link1,"update vendor_order_master set status='Received'  where po_no='".$_POST['po_no']."'");       
			   } 
			
			////// insert in location account ledger 
		 $res_ac_ledger = mysqli_query($link1,"INSERT INTO party_ledger set location_code='".$po_from."',entry_date='".$today."', doc_type = 'GRN',doc_no='".$grnno."',doc_date='".$today."',cr_dr='CR',amount='".$_POST['grand_total']."' , cust_id = '".$po_to."' ");
			if(!$res_ac_ledger){
				$flag = false;
				$error_msg = "Error details8: " . mysqli_error($link1) . ".";
			}
		  		   
		
			################################################## Update credit limit of party
			if(mysqli_num_rows(mysqli_query($link1,"select id from current_cr_status where parent_code='".$_POST['po_from']."' and asc_code='".$_POST['po_to']."'"))>0){
					$upd = mysqli_query($link1,"update current_cr_status set cr_abl=cr_abl+'".$_POST['grand_total']."',total_cr_limit=total_cr_limit+'".$_POST['grand_total']."', last_updated='$today' where parent_code='".$_POST['po_from']."' and asc_code='".$_POST['po_to']."'");
				   ############# check if query is not executed
					if (!$upd) {
						$flag = false;
						echo "Error details11: " . mysqli_error($link1) . ".";
					}
			}else{
					$upd = mysqli_query($link1,"insert into current_cr_status set cr_abl=cr_abl+'".$_POST['grand_total']."',total_cr_limit=total_cr_limit+'".$_POST['grand_total']."', last_updated='$today', parent_code='".$_POST['po_from']."' , asc_code='".$_POST['po_to']."'");
				   ############# check if query is not executed
					if (!$upd) {
						$flag = false;
						echo "Error details11: " . mysqli_error($link1) . ".";
					}
			}
			////// insert in activity table////
			$flag= dailyActivity($_SESSION['userid'],$grnno,"GRN","RECEIVE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
			/////// start ledger entry for tally purpose ///// written by shekhar on 12 july 2022
			
			/////// 1. entry for voucher from voucher master with extension concatination
			/////get voucher extension name
			$vch_ext_name = getLocExtName($_POST['po_from'],"Voucher","1",$link1);
			/////get ledger extension name
			$ldg_ext_name = getLocExtName($_POST['po_from'],"Ledger","1",$link1);
			////// get voucher name
			$vch_det = explode("~",getVoucherName("Purchase",$link1));
			///// make voucher name for tally
			$voucher_name = $vch_ext_name." ".$vch_det[1];
			/////// 2. entry for purchse ledger
			$arr_ledger = array();
			$arr_ledger_val = array();
			if($gst_type == "IGST"){
				/////GST ledger
				foreach($arr_tax as $gstper => $gstamt){
					$arr_ledger[] = "Input IGST @ ".round($gstper)."%";
					$arr_ledger_val[] = $gstamt;
				}
				/////Purchase ledger
				foreach($arr_val as $gstper => $val){
					$arr_ledger[] = "Central Purchase @ ".round($gstper)."%";
					$arr_ledger_val[] = $val;
				}
			}else{
				foreach($arr_tax as $gstper => $gstamt){
					$arr_ledger[] = "Input CGST @ ".round($gstper/2)."%";
					$arr_ledger_val[] = ($gstamt/2);
					$arr_ledger[] = "Input SGST @ ".round($gstper/2)."%";
					$arr_ledger_val[] = ($gstamt/2);
				}
				foreach($arr_val as $gstper => $val){
					$arr_ledger[] = "Local Purchase @ ".round($gstper)."%";
					$arr_ledger_val[] = $val;
				}
			}
			/////// 3. entry for GST ledger
			////// get voucher id
			for($j=0; $j<count($arr_ledger); $j++){
				///// check ledger is exist or not
				if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM account_ledger WHERE ledger_name='".$arr_ledger[$j]."' AND status='Active'"))==0){
					//////insert in account ledger
					/////get account group
					$res_acgp = mysqli_query($link1,"SELECT id FROM account_group_master WHERE group_name='Direct Purchase' AND status='Active'");
					$row_acgp = mysqli_fetch_assoc($res_acgp);
					/////get account head
					$res_achd = mysqli_query($link1,"SELECT id FROM account_head_master WHERE head_name='Purchase Accounts' AND group_id='".$row_acgp["id"]."' AND status='Active'");
					$row_achd = mysqli_fetch_assoc($res_achd);
					/////insert into account ledger
					$res_aclg = mysqli_query($link1,"INSERT INTO account_ledger SET ledger_name='".$arr_ledger[$j]."', ac_head_id='".$row_achd["id"]."', ac_head_name='Purchase Accounts',ac_group_id='".$row_acgp["id"]."', ac_group_name='Direct Purchase', status='Active', entry_date='".$datetime."', entry_by='".$_SESSION["userid"]."'");
				}
				////// get ledger details
				$res_ldg_det = mysqli_query($link1,"SELECT * FROM account_ledger WHERE ledger_name='".$arr_ledger[$j]."' AND status='Active'");
				$row_ldg_det = mysqli_fetch_assoc($res_ldg_det);
				/////////
				$make_ledger = $ldg_ext_name." ".$arr_ledger[$j];
				$res_loc_ledger = mysqli_query($link1,"INSERT INTO location_ledger SET transaction_no ='".$grnno."', transaction_date='".$today."', location_code ='".$_POST['po_from']."', voucher_id ='".$vch_det[0]."', voucher_name='".$voucher_name."', ledger_id ='".$row_ldg_det["id"]."', ledger_name='".$make_ledger."', ledger_value='".$arr_ledger_val[$j]."', ac_head_id ='".$row_ldg_det["ac_head_id"]."', ac_head_name='".$row_ldg_det["ac_head_name"]."', ac_group_id ='".$row_ldg_det["ac_group_id"]."', ac_group_name='".$row_ldg_det["ac_group_name"]."', entry_by='".$_SESSION["userid"]."'");
			}
			/////// 4. entry for TCS ledger if applicable
			if($tcs_per){
				$tcs_ldg = "TCS on Purchase @ ".$tcs_per."%";
				$tcs_ldgamt = $tcs_amt;
				////// get ledger details
				$res_ldg_det = mysqli_query($link1,"SELECT * FROM account_ledger WHERE ledger_name='".$tcs_ldg."' AND status='Active'");
				$row_ldg_det = mysqli_fetch_assoc($res_ldg_det);
				//////
				$res_loc_ledger = mysqli_query($link1,"INSERT INTO location_ledger SET transaction_no ='".$grnno."', transaction_date='".$today."', location_code ='".$_POST['po_from']."', voucher_id ='".$vch_det[0]."', voucher_name='".$voucher_name."', ledger_id ='".$row_ldg_det["id"]."', ledger_name='".$tcs_ldg."', ledger_value='".$tcs_ldgamt."', ac_head_id ='".$row_ldg_det["ac_head_id"]."', ac_head_name='".$row_ldg_det["ac_head_name"]."', ac_group_id ='".$row_ldg_det["ac_group_id"]."', ac_group_name='".$row_ldg_det["ac_group_name"]."', entry_by='".$_SESSION["userid"]."'");
			}
			/////// 5. entry for round off ledger
			if($round_off!=0.00 && $round_off!=0){
				$ro_ldg = "Rounded Off";
				$ro_ldgamt = $round_off;
				////// get ledger details
				$res_ldg_det = mysqli_query($link1,"SELECT * FROM account_ledger WHERE ledger_name='".$ro_ldg."' AND status='Active'");
				$row_ldg_det = mysqli_fetch_assoc($res_ldg_det);
				//////
				$res_loc_ledger = mysqli_query($link1,"INSERT INTO location_ledger SET transaction_no ='".$grnno."', transaction_date='".$today."', location_code ='".$_POST['po_from']."', voucher_id ='".$vch_det[0]."', voucher_name='".$voucher_name."', ledger_id ='".$row_ldg_det["id"]."', ledger_name='".$ro_ldg."', ledger_value='".$ro_ldgamt."', ac_head_id ='".$row_ldg_det["ac_head_id"]."', ac_head_name='".$row_ldg_det["ac_head_name"]."', ac_group_id ='".$row_ldg_det["ac_group_id"]."', ac_group_name='".$row_ldg_det["ac_group_name"]."', entry_by='".$_SESSION["userid"]."'");
			}
			/////// end ledger entry for tally purpose ///// written by shekhar on 12 july 2022	
			///// check both master and data query are successfully executed
			if ($flag) {
				mysqli_commit($link1);
				$msg="Successfully Stock  Received  for ".$grnno;
				$cflag="success";
				$cmsg="Success";
			} else {
				mysqli_rollback($link1);
				$cflag="danger";
				$cmsg="Failed";
				$msg = "Request could not be processed. Please try again. ".$error_msg;
			}
			
			mysqli_close($link1);
			///// move to parent page
			  header("location:grnList.php?msg=".$msg."".$pagenav);
			  exit;
		}
	}
	?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=siteTitle?></title>
    <script src="../js/jquery.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/bootstrap.min.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
	<script type="text/javascript">
	$(document).ready(function(){
		$("#frm2").validate();
	});		
	
	</script>
	<script type="text/javascript">
	
	function checkPriceInvRcb(indx){
	var inv_qty_data = document.getElementById("inv_qty"+indx).value;
	var rcb_qty_data = document.getElementById("qty"+indx).value;
	if(parseInt(rcb_qty_data) < parseInt(inv_qty_data)){
		alert("Invoice qty cannot be greater then Pending Qty ");
		document.getElementById('inv_qty'+indx).value = rcb_qty_data;
	}else{
	
	}
}
	/////// calculate line total /////////////
	function rowTotal(ind){
		var ent_qty = "inv_qty"+ind+"";
		var ent_rate = "price"+ind+"";
		var cgst_per = "cgst_per"+ind+"";
		var sgst_per = "sgst_per"+ind+"";
		var igst_per = "igst_per"+ind+"";
		var cgstamt = "cgst_amt"+ind+"";
		var sgstamt = "sgst_amt"+ind+"";
		var igstamt = "igst_amt"+ind+"";
		var linetotal = "totalval"+ind+"";
		
		var vend_state = '<?=$vend_det[2]?>' ;
		var loc_state = '<?=$loc_det[7]?>';
		////// check if entered qty is something
		if(document.getElementById(ent_qty).value){ 
			var qty = document.getElementById(ent_qty).value;
		}else{ 
			var qty = 0;
		}
		
		/////  check if entered price is somthing
		if(document.getElementById(ent_rate).value){ 
			var price = document.getElementById(ent_rate).value;
		}else{ 
			var price = 0.00;
		}
		var total = parseFloat(qty) * parseFloat(price);
		var var3 = "po_value"+ind+"";
		document.getElementById(var3).value = total.toFixed(2);	

		if(vend_state == loc_state){
			var cgstamtval = ((total)*(document.getElementById(cgst_per).value))/100;
			document.getElementById(cgstamt).value=(cgstamtval).toFixed(2);
	 
			var sgstamtval = ((total)*(document.getElementById(sgst_per).value))/100;
			document.getElementById(sgstamt).value=(sgstamtval).toFixed(2);	 
			
			document.getElementById(linetotal).value = parseFloat(total+cgstamtval+sgstamtval).toFixed(2);
			recalculatetotal();
		}
		else {
			var igstamtval = ((total)*(document.getElementById(igst_per).value))/100;
			document.getElementById(igstamt).value=(igstamtval).toFixed(2);
			
			document.getElementById(linetotal).value = (total+igstamtval).toFixed(2);
			recalculatetotal();
		} 
		//recalculatetotal(); 
	}
	////// calculate final value of form /////
	function recalculatetotal(){
		var rowno = (document.getElementById("row_no").value);
		var sum_total = 0.00; 
		var sum_subtot = 0.00;
		var sum_cgsttot = 0.00;
		var sum_sgsttot = 0.00;
		var sum_igsttot = 0.00;
		var vend_state = '<?=$vend_det[2]?>' ;
		var loc_state = '<?=$loc_det[7]?>';
			for(var i=1; i<rowno; i++){
				var temp_qty = "inv_qty"+i+"";
				var temp_total = "totalval"+i+"";
				var temp_subtot = "po_value"+i+"";
				var temp_sgstamt = "sgst_amt"+i+"";
				var temp_cgstamt = "cgst_amt"+i+"";
				var temp_igstamt = "igst_amt"+i+"";
	
				var totalamtvar = 0.00;
				var totalsubtotal = 0.00;
				var cgsttotal = 0.00;
				var sgsttotal = 0.00;
				var igsttotal = 0.00;
				///// check if line total value is something
				if(document.getElementById(temp_total).value){ totalamtvar= document.getElementById(temp_total).value;}else{ totalamtvar=0.00;}
				if(document.getElementById(temp_subtot).value){ totalsubtotal= document.getElementById(temp_subtot).value;}else{ totalsubtotal=0.00;}
				if(vend_state == loc_state) {
					if(document.getElementById(temp_sgstamt).value){ sgsttotal= document.getElementById(temp_sgstamt).value;}else{ sgsttotal=0.00;}
				
					if(document.getElementById(temp_cgstamt).value){ cgsttotal= document.getElementById(temp_cgstamt).value;}else{ cgsttotal=0.00;}
					sum_cgsttot+=parseFloat(cgsttotal);
					sum_sgsttot+=parseFloat(sgsttotal);
				}
				else {
					if(document.getElementById(temp_igstamt).value){ igsttotal= document.getElementById(temp_igstamt).value;}else{ igsttotal=0.00;}
					sum_igsttot+=parseFloat(igsttotal);
				}
				sum_total+=parseFloat(totalamtvar);
				sum_subtot+=parseFloat(totalsubtotal);
			}/// close for loop
			document.getElementById("sub_total").value=(sum_subtot).toFixed(2);
			document.getElementById("tax_total").value=(sum_cgsttot+sum_sgsttot+sum_igsttot).toFixed(2);
			document.getElementById("grand_total").value=(sum_total).toFixed(2);
			////// check if TCS is applicable or not
			var tcs = document.getElementById("tcs_per").value;
			if(tcs){
				var ft = (sum_total * parseFloat(tcs))/100;
				document.getElementById("tcs_amt").value=(ft).toFixed(2);
				var ftwro = (sum_total+ft).toFixed(2);
				var decimals = ftwro - Math.floor(ftwro);
				var decimalPlaces = ftwro.toString().split('.')[1].length;
				decimals = decimals.toFixed(decimalPlaces);				
				if(decimals>=.50){
					var ro = parseFloat((1-decimals),2).toFixed(2);
					var roundoff = "+"+ro;
				}else if(decimals==.00){
					var roundoff = decimals;
				}else{
					var roundoff = "-"+decimals;
				}
				document.getElementById("round_off").value=roundoff;
				document.getElementById("final_total").value=parseFloat(roundoff)+parseFloat(ftwro);
			}else{
				var ftwro = sum_total.toFixed(2);
				var decimals = ftwro - Math.floor(ftwro);
				var decimalPlaces = ftwro.toString().split('.')[1].length;
				decimals = decimals.toFixed(decimalPlaces);				
				if(decimals>=.50){
					var ro = parseFloat((1-decimals),2).toFixed(2);
					var roundoff = "+"+ro;
				}else if(decimals==.00){
					var roundoff = decimals;
				}else{
					var roundoff = "-"+decimals;
				}
				document.getElementById("tcs_amt").value=0.00;
				document.getElementById("round_off").value=roundoff;
				document.getElementById("final_total").value=parseFloat(roundoff)+parseFloat(ftwro);
			}
		}
		
		
		function checkRecQty(a){
	  
			var holdqty = 0;
			var recvqty = 0;
			var poqty = 0;
			var missqty = 0;
			//// check hold qty
			if(document.getElementById("damage_qty"+a).value==""){ holdqty=0; }else{ holdqty=parseInt(document.getElementById("damage_qty"+a).value); }
			//// check missing qty
			if(document.getElementById("miss_qty"+a).value==""){ missqty=0; }else{ missqty=parseInt(document.getElementById("miss_qty"+a).value);}
			//// entered received qty
			if(document.getElementById("ok_qty"+a).value==""){ recvqty=0; }else{ recvqty=parseInt(document.getElementById("ok_qty"+a).value);}
			//// check enter poqty qty
			if(document.getElementById("inv_qty"+a).value==""){ poqty=0; }else{ poqty=parseInt(document.getElementById("inv_qty"+a).value); }
			
			if(poqty < (recvqty)){
				alert("Ok Qty  can not more than Invoice Qty!");
				document.getElementById("ok_qty"+a).value=poqty;
				document.getElementById("miss_qty"+a).value=0;
				document.getElementById("damage_qty"+a).value=0;
			}
			else{
				document.getElementById("miss_qty"+a).value=(poqty - recvqty);
				//document.getElementById("miss_qty"+a).focus();
				//document.getElementById("upd").disabled=true;
	        }
			
			//// entered shipped qty
			//if(document.getElementById("shippedqty"+a).value==""){ shipqty=0; }else{ shipqty=parseInt(document.getElementById("shippedqty"+a).value);}
			if(poqty < (recvqty + holdqty)){
				alert("Ok Qty & Damage Qty can not more than Invoice Qty!");
				document.getElementById("ok_qty"+a).value=poqty;
				document.getElementById("miss_qty"+a).value=0;
				document.getElementById("damage_qty"+a).value=0;
			}else{
				document.getElementById("miss_qty"+a).value=(poqty - (recvqty + holdqty));
				//document.getElementById("miss_qty"+a).focus();
				//document.getElementById("upd").disabled=true;
	        }
			
			/*if(poqty < (recvqty + missqty)){
				alert("Ok Qty & Missing Qty can not more than Invoice Qty!");
				document.getElementById("ok_qty"+a).value=poqty;
				document.getElementById("miss_qty"+a).value=0;
				document.getElementById("damage_qty"+a).value=0;
			}*/
			
			/*if(poqty < (recvqty + missqty+ holdqty)){
				alert("Ok Qty & Missing Qty & Damage Qty can not more than Invoice Qty!");
				document.getElementById("ok_qty"+a).value=poqty;
				document.getElementById("miss_qty"+a).value=0;
				document.getElementById("damage_qty"+a).value=0;
			}*/
			calculateTotal();
		}

		///// calculate total amount //
		function calculateTotal(){
			var maxrow = document.getElementById("row_no").value;
			var subtotal = 0.00;
			var taxtotal = 0.00;
			var grandtotal = 0.00;
			var qtytotal = 0;
			var newqty = 0;
			for(var i=1; i < maxrow; i++){
				if(document.getElementById("inv_qty"+i).value==""){ var shipqty=0; }else{ var shipqty=parseInt(document.getElementById("inv_qty"+i).value);}
				if(document.getElementById("ok_qty"+i).value==""){ var okqtynew=0; }else{ var okqtynew=parseInt(document.getElementById("ok_qty"+i).value);}
				
				if(document.getElementById("excess"+i).value==""){ var excessqty=0; }else{ var excessqty=parseInt(document.getElementById("excess"+i).value);}
				
				var totqty = shipqty + excessqty;
				
				qtytotal+= totqty;
			    
				var calsub = parseFloat(totqty) * parseFloat(document.getElementById("price"+i).value);
				subtotal+=  calsub;
				//var caltax = (calsub * parseFloat(document.getElementById("taxper"+i).value))/100;
				//taxtotal+=  caltax;
				grandtotal+= calsub;
				newqty+= okqtynew;
			}
			
			document.getElementById("tot_qty").value = qtytotal;
			//document.getElementById("sub_total").value = subtotal;
			//document.getElementById("tax_total").value = taxtotal;
			//document.getElementById("grand_total").value = grandtotal;
			document.getElementById("totok_qty").value = newqty;
		}
		function putdata(){			
			var invno = document.getElementById("invoice_no").value;
			document.getElementById("postinv_no").value = invno;
		}
		</script>
		<script type="text/javascript" src="../js/jquery.validate.js"></script>
		<!-- Include multiselect -->
	<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
    <link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
	</head>
	<body onLoad="putdata();">
	<div class="container-fluid">
		<div class="row content">
		<?php 
		include("../includes/leftnav2.php");
		?>
	  <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
		  <h2 align="center"><i class="fa fa-ship"></i> Receive GRN </h2><br/>
	   <div class="panel-group">
	   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
		<div class="panel panel-info">
			<div class="panel-heading">VPO Details</div>
			 <div class="panel-body">
			  <table class="table table-bordered" width="100%">
				<tbody>
				  <tr>
					<td width="20%"><label class="control-label">PO TO:</label></td>
					<td width="30%"><?php echo getVendorDetails($po_row["po_to"],"name",$link1)."(".$po_row['po_to'].")";?><input name="supplier" id="supplier" type="hidden" value="<?=$po_row['po_to']?>"/></td>
					<td width="20%"><label class="control-label">PO From</label></td>
					<td width="30%"><?php echo getLocationDetails($po_row["po_from"],"name",$link1)."(".$po_row['po_from'].")";?><input name="to_loc" id="to_loc" type="hidden" value="<?=$po_row['po_from']?>"/></td>
				  </tr>
				 
				  <tr>
					<td><label class="control-label">PO No.</label></td>
					<td><?php echo $po_row['po_no'];?><input name="po_no" id="po_no" type="hidden" value="<?=$po_row['po_no']?>"/></td>
					<td><label class="control-label">PO Date</label></td>
					<td><?php echo dt_format($po_row['entry_date']);?></td>
				  </tr>  
				  <tr>
					<td><label class="control-label">Status</label></td>
					<td><?php  echo $po_row["status"]?></td>
					<td><label class="control-label">Invoice Number</label></td>
					<td><input type="text" name="invoice_no" id="invoice_no" value="<?=$po_row['invoice_no']?>" class="form-control" onKeyUp="putdata();"/></td>
				  </tr>     
				</tbody>
			  </table>
			</div><!--close panel body-->
		</div><!--close panel-->	
		</form>
<form  method="post" id="frm1" name="frm1" enctype="multipart/form-data">
	<div class="panel panel-info table-responsive">
		<div class="panel-heading">Items Information</div>
		<div class="panel-body">
			<table class="table table-bordered" width="200%">
				<thead>
					<tr class="<?=$tableheadcolor?>">
				  		<td>S.No</td>
                        <td>Product</td>
                        <td>PO Qty</td>
                        <td>Pending Qty</td>
						<th>Invoice Qty</th>
                        <td>Purchase Price</td>
                        <td>SubTotal</td>
                        <?php  if($vend_det[2] == $loc_det[7]){?>
                        <td>CGST(%)</td>
                        <td>CGST Amount</td>
                        <td>SGST(%)</td>
                        <td>SGST Amount</td>
                        <?php } else {?>
                        <td>IGST(%)</td>
                        <td>IGST Amount</td>
                        <?php }?>
                        <td>Total Amount</td>
                        
                        <!--<td>Receive Qty</td>-->
                        <td>OK</td>
						<td>Damage</td>
						<td>Missing</td>
                        <td>Excess</td>
					</tr>
				</thead>
				<tbody>
				<?php
				$i = 1;
			    $data_sql = "SELECT * FROM vendor_order_data WHERE po_no='".$po_no."' AND pending_qty != 0.00 ";
				$data_res = mysqli_query($link1,$data_sql);
				while($data_row = mysqli_fetch_assoc($data_res)){
					$hsncode = mysqli_fetch_array(mysqli_query($link1,"select hsn_code,productname from product_master where productcode = '".$data_row['prod_code']."' "));
				    $taxdet = mysqli_fetch_array(mysqli_query($link1,"select igst,cgst,sgst from tax_hsn_master where hsn_code = '".$hsncode['hsn_code']."' "));
				 ?>
					<tr>
				  		<td><?=$i?></td>
				  		<td><?=$hsncode['productname'];?></td>
				  		<td><?=$data_row['req_qty'];?></td>	
                        <td><?=$data_row['pending_qty'];?><input name="qty<?=$data_row['id']?>" id="qty<?=$i?>" type="hidden" value="<?=$data_row['pending_qty']?>"/></td>
                        <td><input name="inv_qty<?=$data_row['id']?>" onBlur="checkPriceInvRcb(<?=$i?>);rowTotal('<?=$i?>');" id="inv_qty<?=$i?>" type="text" class="form-control number required" style="width:80px;text-align:right" required/></td>
                  						
				  		<td><input name="price<?=$data_row['id']?>" id="price<?=$i?>" type="text" value="<?=$data_row['po_price']?>" class="form-control" onBlur="rowTotal('<?=$i?>');" style="width:80px;text-align:right"/></td>
				  		<td><input name="po_value<?=$data_row['id']?>" id="po_value<?=$i?>" type="text" value="<?=$data_row['po_value']?>" class="form-control" style="width:100px;" readonly/></td> 
				  		<?php  if($vend_det[2] == $loc_det[7]){
							$cgstamtval =  ($taxdet['cgst']*$data_row['po_value'])/100;
							$sgstamtval =  ($taxdet['sgst']*$data_row['po_value'])/100;
							$totalval = $cgstamtval + $cgstamtval + $data_row['po_value'];
							?>
				  		<td><?=$taxdet['cgst'];?><input name="cgst_per<?=$data_row['id']?>" id="cgst_per<?=$i?>" type="hidden" value="<?=$taxdet['cgst']?>" class="form-control" readonly/></td>
				  		<td><input name="cgst_amt<?=$data_row['id']?>" id="cgst_amt<?=$i?>" type="text" value="<?=$cgstamtval?>" class="form-control" style="width:80px;" readonly/></td>
				  		<td><?=$taxdet['sgst'];?><input name="sgst_per<?=$data_row['id']?>" id="sgst_per<?=$i?>" type="hidden" value="<?=$taxdet['sgst']?>" class="form-control" readonly/></td>
				  		<td><input name="sgst_amt<?=$data_row['id']?>" id="sgst_amt<?=$i?>" type="text" value="<?=$sgstamtval?>" class="form-control" style="width:80px;" readonly/></td>
				   		<?php } else {
							  $igstamtval =  ($taxdet['igst']*$data_row['po_value'])/100;
							$totalval = $igstamtval + $data_row['po_value'];
							?>
				  		<td><?=$taxdet['igst'];?><input name="igst_per<?=$data_row['id']?>" id="igst_per<?=$i?>" type="hidden" value="<?=$taxdet['igst']?>" class="form-control" readonly/></td>
				  		<td><input name="igst_amt<?=$data_row['id']?>" id="igst_amt<?=$i?>" type="text" value="<?=$igstamtval?>" class="form-control" style="width:80px;" readonly/></td>
				  		<?php }?>
				  		<td><input name="totalval<?=$data_row['id']?>" id="totalval<?=$i?>" type="text" value="<?=$totalval?>" class="form-control" style="width:100px;" readonly/></td>
				  		
				  		<td><input type="text" class="digits form-control" style="width:80px;" name="ok_qty<?=$data_row['id']?>" id="ok_qty<?=$i?>"  autocomplete="off" onBlur="checkPriceInvRcb(<?=$i?>);" onKeyUp="checkRecQty('<?=$i?>');" value="<?=$data_row['qty'];?>"></td>
				  		<td><input type="text" class="digits form-control" style="width:80px;" name="damage_qty<?=$data_row[id]?>" id="damage_qty<?=$i?>"  autocomplete="off" required onBlur="checkRecQty('<?=$i?>')"; value="0" ></td>
                        <td><input type="text" class="digits form-control" style="width:80px;" name="miss_qty<?=$data_row[id]?>" id="miss_qty<?=$i?>"  autocomplete="off" value="0" readonly></td>
						<td><input name="excess<?=$data_row['id']?>" id="excess<?=$i?>" style="width:80px;"  class="digits form-control" type="text"  size="3" value="0" onKeyUp="checkRecQty('<?=$i?>');"/></td>
					</tr>
				<?php
					$total_qty+= $data_row['req_qty'];
					$sub_total+= $data_row['po_value'];
					$tax_total+= $data_row['tax_cost'];
					$grand_total+= $data_row['totalval'];
					$i++;
				}
				if(strpos($grand_total, ".") !== false){
					$expd_gt = explode(".",$grand_total);
					$checkval = ".".$expd_gt[1];
					if($checkval>=.50){
						$ro = 1-$checkval;
						$roundoff = "+".$ro;
					}else{
						$roundoff = "-".$checkval;
					}
				}else{
					$roundoff = 0.00;
				}
				?>
					<tr>
						<td colspan="10" align="right"><strong>Total Qty</strong></td>
						<td><input type="text" name="totok_qty" id="totok_qty"  class="form-control"></td>
                        <td colspan="3">&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</div><!--close panel body-->
	</div><!--close panel-->
	<div class="panel panel-info table-responsive">
		  <div class="panel-heading">Receive</div>
		  <div class="panel-body">
			<table class="table table-bordered" width="100%">
				<tbody>          
				   <tr>
					 <td width="25%"><label class="control-label">Total Qty</label></td>
					 <td width="25%"><input type="text" name="tot_qty" id="tot_qty" class="form-control" value="<?=$total_qty;?>" style="width:150px;text-align:right" readonly/></td>
					 <td width="25%"><label class="control-label">Sub Total</label></td>
					 <td width="25%"><input type="text" name="sub_total" id="sub_total" class="form-control" value="<?=$sub_total?>" style="width:150px;text-align:right" readonly/></td>
				   </tr>
				   <tr>
					 <td><label class="control-label">Tax Total</label></td>
					 <td><input type="text" name="tax_total" id="tax_total" class="form-control" value="<?=$po_row['total_sgst_amt']+$po_row['total_cgst_amt']+$po_row['total_igst_amt'];?>" style="width:150px;text-align:right" readonly/></td>
					 <td><label class="control-label">Grand Total</label></td>
					 <td><input type="text" name="grand_total" id="grand_total" class="form-control" value="<?=$grand_total;?>" style="width:150px;text-align:right" readonly/></td>
				   </tr>
				   <tr>
					 <td><label class="control-label">TCS</label></td>
					 <td>
                     	<select name="tcs_per" id="tcs_per" class="form-control" onChange="recalculatetotal();">
                     		<option value="">--Please Select--</option>
                            <option value="0.1">0.1 %</option>
                     	</select></td>
					 <td><label class="control-label">TCS Amount</label></td>
					 <td><input type="text" name="tcs_amt" id="tcs_amt" class="form-control" value="0.00" style="width:150px;text-align:right" readonly/></td>
				   </tr>
                   <tr>
					 <td><label class="control-label">Round Off</label></td>
					 <td><input type="text" name="round_off" id="round_off" class="form-control" value="<?=$roundoff?>" style="width:150px;text-align:right" readonly/></td>
					 <td><label class="control-label">Final Total</label></td>
					 <td><input type="text" name="final_total" id="final_total" class="form-control" value="<?=$roundoff+$grand_total?>" style="width:150px;text-align:right" readonly/></td>
				   </tr>
				   <tr>
				   <td><label class="control-label">Receive Remark <span style="color:#F00">*</span></label></td>
					 <td colspan="3">
					   <textarea  name="rcv_rmk" id="rcv_rmk"  class=" form-control required" style="width:500px; resize:vertical"  required /></textarea></td>
					  </tr>
					  <tr>
					   <td><label class="control-label">Invoice Attachment</label></td>
					 <td colspan="3">
					   <input type="file" class="form-control" name="inv_attachment" id="inv_attachment" accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf" style="width:500px;"/></td>
					  </tr>
				   <tr>
					 <td colspan="4" align="center">
					<input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Receive" title="Receive">&nbsp;
				  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='grnAgainstPO.php?<?=$pagenav?>'">
						 <input type="hidden" id="po_from" name="po_from" value="<?=$po_row['po_from']?>">
						 <input type="hidden" id="po_to" name="po_to" value="<?=$po_row['po_to']?>">
						 <input type="hidden" id="po_no" name="po_no" value="<?=$po_row['po_no']?>">
						 <input type="hidden" id="row_no" name="row_no" value="<?=$i?>">
			
						 <input type="hidden" id="postinv_no" name="postinv_no" value="<?=$po_row['invoice_no']?>">
					 </td>
					</tr>
				</tbody>
			  </table>
		  </div><!--close panel body-->
		</div><!--close panel-->
		</form>
		
	  </div><!--close panel group-->
	 </div><!--close col-sm-9-->
	</div><!--close row content-->
	</div><!--close container-fluid-->
	<?php
	include("../includes/footer.php");
	include("../includes/connection_close.php");
	?>