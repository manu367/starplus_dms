<?php
require_once("../config/config.php");
require_once("../includes/ledger_function.php");
///// get parent location details
$parentloc = getLocationDetails($_REQUEST["po_to"],"addrs,disp_addrs,name,city,state,gstin_no,pincode,email,phone",$link1);
$parentlocdet = explode("~",$parentloc);
///// get child location details
$childloc = getLocationDetails($_REQUEST["po_from"],"addrs,disp_addrs,name,city,state,gstin_no,pincode,email,phone",$link1);
$childlocdet = explode("~",$childloc); 
@extract($_POST);
////// case 2. if we want to Add new user
if($_POST) {
	if($_POST['upd'] == 'Save') { 
		///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
		$messageIdent = md5($_POST['upd'].$parentcode.$partycode);
		//and check it against the stored value:
		$sessionMessageIdent = isset($_SESSION['messageIdentDSR'])?$_SESSION['messageIdentDSR']:'';
		if($messageIdent!=$sessionMessageIdent){//if its different:
		//save the session var:
		$_SESSION['messageIdentDSR'] = $messageIdent;
		//// check post invoice no. is already entered
		if (mysqli_num_rows(mysqli_query($link1,"SELECT id FROM billing_master WHERE inv_ref_no='".$invoiceno."' AND from_location='".$partycode."' AND status!='Cancelled'"))>0) {
			$cflag="danger";
			$cmsg="Failed";
			$msg = "Request could not be processed. Please try again. You have entered duplicate invoice number.";
			header("location:direct_return.php?msg=".$msg."".$pagenav);
			exit;
		}
		//// Make System generated Invoice no.//////
		$res_cnt=mysqli_query($link1,"SELECT prn_str, prn_counter FROM document_counter WHERE location_code='".$parentcode."'");
		if(mysqli_num_rows($res_cnt)){
			$row_cnt = mysqli_fetch_array($res_cnt);
			$invcnt = $row_cnt['prn_counter']+1;
			$pad = str_pad($invcnt,4,0,STR_PAD_LEFT);
			$invno = $row_cnt['prn_str'].$pad;
			/////// start transaction
			mysqli_autocommit($link1, false);
			$flag = true;
			$err_msg="";
					
			///// get parent location details
			$parentloc = getLocationDetails($parentcode,"addrs,disp_addrs,name,city,state,gstin_no,pincode,email,phone",$link1);
			$parentlocdet = explode("~",$parentloc);
			///// get child location details
			$childloc = getLocationDetails($partycode,"addrs,disp_addrs,name,city,state,gstin_no,pincode,email,phone",$link1);
			$childlocdet = explode("~",$childloc); 
			if($delivery_address){$deli_addrs=$delivery_address;}else{$deli_addrs=$parentlocdet[0];}	
			///// explode billto
			$expl_fromaddress = explode("~", $stockfromaddress);
			
			$query1= "INSERT INTO billing_master SET 
			from_location='".$partycode."',
			to_location='".$parentcode."',
			sub_location='".$stockin."',
			challan_no='".$invno."',
			to_gst_no='".$parentlocdet[5]."',
			party_name='".$parentlocdet[2]."',
			from_partyname='".$childlocdet[2]."',
			from_gst_no='".$expl_fromaddress[4]."',
			inv_ref_no='".$invoiceno."',
			po_inv_date='".$invoicedate."',
			sale_date='".$today."',
			entry_date='".$today."',
			entry_time='".$currtime."',
			type='DIRECT SALE RETURN',
			document_type='".$doctype."',
			discountfor='".$discountfor."',
			taxfor='".$taxfor."',
			status='Received',
			entry_by='".$_SESSION['userid']."',
			sale_person='".$se."',
			basic_cost='".$sub_total."',
			discount_amt='".$total_discount."',
			tax_cost='',
			total_cost='".$final_total."',
			round_off='" . $round_off . "',
			tcs_per='".$tcs_per."',
			tcs_amt='".$tcs_amt."',
			tax_type='',tax_header='',tax='',
			bill_from='".$expl_fromaddress[0]."',
			bill_topty='".$parentcode."',
			
			from_addrs='".$expl_fromaddress[1]."',
			disp_addrs='".$expl_fromaddress[1]."',
			
			to_addrs='".$parentlocdet[0]."',
			deliv_addrs='".$deli_addrs."',
			billing_rmk='".$remark."',
			
			to_state='".$parentlocdet[4]."',
			from_state='".$expl_fromaddress[3]."',
			to_city='".$parentlocdet[3]."',
			from_city='".$expl_fromaddress[2]."',
			to_pincode='".$parentlocdet[6]."',
			from_pincode='".$expl_fromaddress[5]."',
			to_phone='".$parentlocdet[8]."',
			from_phone='".$childlocdet[8]."',
			to_email='".$parentlocdet[7]."',
			from_email='".$childlocdet[7]."',
			total_cgst_amt = '".$tot_cgst_amt."',
			total_sgst_amt = '".$tot_sgst_amt."',
			total_igst_amt = '".$tot_igst_amt."',
			ledger_name='".$ledgername."',
			receive_date='".$today."',
			receive_time='".$currtime."',
			receive_by='".$_SESSION["userid"]."',
			receive_remark='".$remark."',
			receive_ip='".$ip."'";
			$result1 = mysqli_query($link1,$query1);
			//// check if query is not executed
			if(!$result1) {
				$flag = false;
				$err_msg = "Error Code1:".mysqli_error($link1).".";
			}
			/// update invoice counter /////
			$result2 = mysqli_query($link1,"UPDATE document_counter SET prn_counter = prn_counter+1, update_by='".$_SESSION['userid']."',updatedate='".$datetime."' WHERE location_code='".$parentcode."'");
			//// check if query is not executed
			if(!$result2) {
				$flag = false;
				$err_msg = "Error Code2:".mysqli_error($link1).".";
			}
			///// entry for credit note  srn_str
			/*$query_code = "SELECT MAX(sys_ref_temp_no) FROM credit_note WHERE location_id='".$parentcode."'";
			$result_code = mysqli_query($link1,$query_code);
			$arr_result2 = mysqli_fetch_array($result_code);
			$code_id = $arr_result2[0];
			$pad =++$code_id;
			$mobiCode = "CR/".$parentcode."/".$pad;*/
			$res_cnt = mysqli_query($link1, "SELECT srn_str, srn_counter FROM document_counter WHERE location_code='" . $parentcode . "'");
			$row_cnt = mysqli_fetch_array($res_cnt);
			$invcnt = $row_cnt['srn_counter'] + 1;
			//$pad = str_pad($invcnt, 4, 0, STR_PAD_LEFT);
			$pad = $invcnt;
			$mobiCode = $row_cnt['srn_str'].$pad;
			
			$sql_cn= "INSERT INTO credit_note SET cust_id='".$partycode."',location_id='".$parentcode."',sub_location='".$stockin."',entered_ref_no='".$invoiceno."',entered_ref_date='".$invoicedate."',ref_no='".$mobiCode."',sys_ref_temp_no='".$pad."',challan_no='".$invno."',create_by='".$_SESSION['userid']."',remark='".$remark."',create_date='".$today."',amount='".$final_total."',status='Pending For Approval',create_ip='".$ip."' ,basic_amt = '".$sub_total."' , discount_type = '' , discount = '".$total_discount."',round_off='".$round_off."',tcs_per='".$tcs_per."', tcs_amt='".$tcs_amt."',sgst_amt='".$tot_sgst_amt."',cgst_amt='".$tot_cgst_amt."',igst_amt='".$tot_igst_amt."',tax_cost='',description='DIRECT SALE RETURN',to_gst_no='".$parentlocdet[5]."',party_name='".$parentlocdet[2]."',from_partyname='".$childlocdet[2]."',from_gst_no='".$expl_fromaddress[4]."',bill_from='".$expl_fromaddress[0]."',bill_topty='".$parentcode."',from_addrs='".$childlocdet[3]."',disp_addrs='".$expl_fromaddress[1]."',to_addrs='".$parentlocdet[3]."',deliv_addrs='".$deli_addrs."',to_state='".$parentlocdet[4]."',from_state='".$expl_fromaddress[3]."',to_city='".$parentlocdet[3]."',from_city='".$expl_fromaddress[2]."',to_pincode='".$parentlocdet[6]."',from_pincode='".$expl_fromaddress[5]."',to_phone='".$parentlocdet[8]."',from_phone='".$childlocdet[8]."',to_email='".$parentlocdet[7]."',from_email='".$childlocdet[7]."'";
			$db_add= mysqli_query($link1,$sql_cn);
			//// check if query is not executed
			if (!$db_add) {
				 $flag = false;
				 $err_msg = "Error details1: " . mysqli_error($link1) . ".";
			}
			$resultdc = mysqli_query($link1, "UPDATE document_counter SET srn_counter=srn_counter+1,update_by='" . $_SESSION['userid'] . "',updatedate='" . $datetime . "' WHERE location_code='" . $parentcode . "'");
			//// check if query is not executed
			if (!$resultdc) {
				$flag = false;
				$err_msg = "Error Code1.1: ".mysqli_error($link1);
			}
			/*$arr_taxx = array();
			$arr_val = array();
			$gst_type = "";*/
        	///// Insert in item data by picking each data row one by one
        	foreach ($prod_code as $k => $val) {
            	// checking row value of product and qty should not be blank
            	if ($prod_code[$k] != '' && $req_qty[$k] != '' && $req_qty[$k] != 0) {
					/*if($sgst_per[$k]!="" && $sgst_per[$k]!="0.00"  && $sgst_per[$k]!="0"){
						$gstper = round($sgst_per[$k] + $cgst_per[$k]);
						$arr_taxx[$gstper] += $sgst_amt[$k] + $cgst_amt[$k];
						$arr_val[$gstper] += $value[$k];
						$gst_type = "SGST-CGST";
					}else{
						$gstper = round($igst_per[$k]);					
						$arr_taxx[$gstper] += $igst_amt[$k];
						$arr_val[$gstper] += $value[$k];
						$gst_type = "IGST";
					}*/
                	/////////// insert data					
					$query3 = "INSERT INTO billing_model_data SET from_location='".$partycode."',prod_code='".$val."',prod_cat='".$prod_cat[$k]."',qty='".$req_qty[$k]."',crdr_qty = crdr_qty+'".$req_qty[$k]."', mrp='".$mrp[$k]."', price='".$price[$k]."', value='".$value[$k]."',tax_name='',tax_per='',tax_amt='',discount='".$rowdiscount[$k]."', totalvalue='".$linetotal[$k]."',challan_no='".$invno."',sale_date='".$today."',entry_date='".$today."' ,sgst_per='".$sgst_per[$k]."' ,sgst_amt='".$sgst_amt[$k]."',igst_per='".$igst_per[$k]."' ,igst_amt='".$igst_amt[$k]."',cgst_per='".$cgst_per[$k]."' ,cgst_amt='".$cgst_amt[$k]."'";					
                	$result3 = mysqli_query($link1, $query3);
                	//// check if query is not executed
                	if(!$result3) {
                    	$flag = false;
                    	$err_msg = "Error details3:".mysqli_error($link1).".";
                	}
					//// update stock of return to godown loaction
		 			if(mysqli_num_rows(mysqli_query($link1,"SELECT sno FROM stock_status WHERE asc_code='".$parentcode."' AND partcode='".$val."' AND sub_location='".$stockin."'"))>0){
			 			$result_stock = mysqli_query($link1,"UPDATE stock_status SET qty = qty+'".$req_qty[$k]."', okqty = okqty+'".$req_qty[$k]."', updatedate='".$datetime."' WHERE asc_code='".$parentcode."' AND partcode='".$val."' AND sub_location='".$stockin."'");
			 		}
			 		else{
			  			$result_stock = mysqli_query($link1,"INSERT INTO stock_status SET qty = qty+'".$req_qty[$k]."', okqty=okqty+'".$req_qty[$k]."', updatedate='".$datetime."', asc_code='".$parentcode."', partcode='".$val."', sub_location='".$stockin."'");
			   		}
					//// check if query is not executed
					if (!$result_stock) {
						$flag = false;
						$err_msg = "Error Code3.1:".mysqli_error($link1).".";
					}
					///// update stock ledger table
				   	$flag = stockLedger($invno, $today, $val, $partycode, $stockin, $stockin, "IN", "OK", "DIRECT SALE RETURN", $req_qty[$k], $price[$k], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
					/////// credit note data
					$query2 = "INSERT INTO credit_note_data SET prod_code='".$val."',req_qty='".$req_qty[$k]."' , price='".$price[$k]."', value='".$value[$k]."' , discount='".$rowdiscount[$k]."', totalvalue='".$linetotal[$k]."',ref_no='".$mobiCode."',entry_date='".$today."' ,sgst_per='".$sgst_per[$k]."' ,sgst_amt='".$sgst_amt[$k]."',igst_per='".$igst_per[$k]."' ,igst_amt='".$igst_amt[$k]."',cgst_per='".$cgst_per[$k]."' ,cgst_amt='".$cgst_amt[$k]."'";			
					$result1 = mysqli_query($link1, $query2);
					//// check if query is not executed
					if (!$result1) {
						$flag = false;
						$err_msg = "Error details2: " . mysqli_error($link1) . ".";
					}
            	}// close if loop of checking row value of product and qty should not be blank
        	}// close for loop
        	////// insert in activity table////
			$flag = dailyActivity($_SESSION['userid'],$invno,"DIRECT SALE RETURN","ADD",$ip,$link1,$flag);
			$flag = dailyActivity($_SESSION['userid'],$mobiCode,"CREDIT NOTE","ADD",$ip,$link1,$flag);
			/////// make account ledger entry for location
			/////// start ledger entry for tally purpose ///// written by shekhar on 03 JAN 2023
			///// make ledger array which are need to be process
			/*if($_REQUEST['doctype'] == 'DC'){
				$hedid = "7";
				$hed = "Delivery Challan";
				$arr_ldg_name = array(
				"igstldgname" => "IGST @",
				"cgstldgname" => "CGST @",
				"sgstldgname" => "SGST @",
				"igstdocldgname" => $ledgername,
				"cgstdocldgname" => $ledgername,
				"sgstdocldgname" => $ledgername,
				"tcsldgname" => "TCS on Sale @",
				"roundoffldgname" => "Rounded Off"
				);
			}else{
				$hedid = "3";
				$hed = "Credit Note";
				$arr_ldg_name = array(
				"igstldgname" => "IGST @",
				"cgstldgname" => "CGST @",
				"sgstldgname" => "SGST @",
				"igstdocldgname" => "Central Sale @",
				"cgstdocldgname" => "GST Sales @",
				"sgstdocldgname" => "GST Sales @",
				"tcsldgname" => "TCS on Sale @",
				"roundoffldgname" => "Rounded Off"
				);
			}*/
			/////// function parameter sequence
			//// 1. location code on which trasaction is being execute
			//// 2. document no. which is being execute
			//// 3. document date which is being execute
			//// 4. Voucher Type . It means Purchase(1)/Sale(2)/Credit Note(3)/Debit Note(4)/Payment(5)/Receipt(6)/Delivery Challan(7)/Receipt Note(8)
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
			
			/*$resp = explode("~",storeLedgerTransaction($parentcode,$invno,$today,$hedid,$hed,$arr_taxx,$arr_val,$tcs_per,$tcs_amt,$round_off,$gst_type,$arr_ldg_name,"GST Sales","GST Sales Account",$link1,$flag));
			$flag = $resp[0];
			if($err_msg==""){
				$err_msg = $resp[1];
			}*/
			/////// end ledger entry for tally purpose ///// written by shekhar on 03 JAN 2023
			///// check both master and data query are successfully executed
			if ($flag) {
				mysqli_commit($link1);
				$msg = "Sale Return is successfully placed with ref. no. ".$invno;
			} else {
				mysqli_rollback($link1);
				$msg = "Request could not be processed. Please try again.".$err_msg;
			}
			mysqli_close($link1);
		}else{
			$msg = "Request could not be processed invoice series not found. Please try again.";
		}
		}else {
	//you've sent this already!
	$msg="You have saved this already ";
	$cflag = "warning";
	$cmsg = "Warning";
}
		///// move to parent page
		header("location:direct_return.php?msg=" . $msg . "" . $pagenav);
		exit;
    }
}
$addressfrom = explode("~",$_REQUEST["po_from_address"]);
//print_r($addressfrom);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= siteTitle ?></title>
        <script src="../js/jquery-1.10.1.min.js"></script>
        <link href="../css/font-awesome.min.css" rel="stylesheet">
        <link href="../css/abc.css" rel="stylesheet">
        <script src="../js/bootstrap.min.js"></script>
        <link href="../css/abc2.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/bootstrap-select.min.css">
        <script src="../js/bootstrap-select.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $("#frm2").validate();
            });
            // When the document is ready
            $(document).ready(function() {
                $('#invoicedate').datepicker({
                    format: "yyyy-mm-dd",
					endDate: "<?=$today?>",
                    todayHighlight: true,
                    autoclose: true
                });
            });
        </script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/common_js.js"></script>
        <link rel="stylesheet" href="../css/datepicker.css">
        <script src="../js/bootstrap-datepicker.js"></script>
        <script type="text/javascript">
            // function to get available stock of ho
            function getAvlStk(indx) {
                var productCode = document.getElementById("prod_code[" + indx + "]").value;
                var partyCode = $('#po_from').val();
                var  locationCode= $("#po_to").val();
                var stocktype = "okqty";
                $.ajax({
                    type: 'post',
                    url: '../includes/getAzaxFields.php',
                    data: {locstk: productCode, loccode: locationCode, vendorCode: partyCode, stktype: stocktype, indxx: indx},
                    success: function(data) {
					//alert(data);
                        var getdata = data.split("~");
                        document.getElementById("avl_stock[" + indx + "]").value = getdata[0];
                        document.getElementById("sgst_per[" + indx + "]").value = getdata[2];
                        document.getElementById("cgst_per[" + indx + "]").value = getdata[3];
                        document.getElementById("igst_per[" + indx + "]").value = getdata[4];
						rowTotal(indx);
                    }
                });
            }
            $(document).ready(function() {
                $("#add_row").click(function() {
                    var numi = document.getElementById('rowno');
                    var itm = "prod_code[" + numi.value + "]";
                    var qTy = "req_qty[" + numi.value + "]";
                    var preno = document.getElementById('rowno').value;
                    var num = (document.getElementById("rowno").value - 1) + 2;
                    if ((document.getElementById(itm).value != "" && document.getElementById(qTy).value != "" && document.getElementById(qTy).value != "0") || ($("#addr" + numi.value + ":visible").length == 0)) {
                        numi.value = num;
                        var r = '<tr id="addr' + num + '"><td><span id="pdtid' + num + '"><select class="form-control selectpicker" data-live-search="true" name="prod_code[' + num + ']" id="prod_code[' + num + ']" onchange="getAvlStk(' + num + ');checkDuplicate(' + num + ',this.value);" required><option value="">--None--</option><?php $model_query = "select productcode,productname,productcolor from product_master where status='active'";$check1 = mysqli_query($link1, $model_query);while ($br = mysqli_fetch_array($check1)) {?><option  value="<?php echo $br['productcode']; ?>"><?= $br['productname'] . ' | ' . $br['productcolor'] . ' | ' . $br['productcode']; ?></option><?php } ?></select></span></td><td><input type="text" name="req_qty[' + num + ']" id="req_qty[' + num + ']" onblur=rowTotal(' + num + ');myFunction(this.value,' + num + ',"req_qty"); class="digits form-control" value="0" onkeypress="return onlyNumbers(this.value);" style="text-align: right;padding: 4px;"/></td><td><input  name="price[' + num + ']" id="price[' + num + ']" type="text" onkeypress="return onlyFloatNum(this.value)" value="0.00" class="form-control" onblur="rowTotal(' + num + ');" style="text-align: right;padding: 4px;"></td><td><input type="text" name="value[' + num + ']" id="value[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="sgst_per[' + num + ']" id="sgst_per[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="sgst_amt[' + num + ']" id="sgst_amt[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="cgst_per[' + num + ']" id="cgst_per[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="cgst_amt[' + num + ']" id="cgst_amt[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="igst_per[' + num + ']" id="igst_per[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="igst_amt[' + num + ']" id="igst_amt[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 5px;"/></td><td><input type="text" class="form-control" name="linetotal[' + num + ']" id="linetotal[' + num + ']" autocomplete="off" readonly style="width:85px;text-align: right;padding: 4px;"><input type="hidden" class="form-control" name="avl_stock[' + num + ']" id="avl_stock[' + num + ']"  autocomplete="off"  style="width:50px;text-align: right" readonly value="0"><input name="mrp[' + num + ']" id="mrp[' + num + ']" type="hidden"/><input name="holdRate[' + num + ']" id="holdRate[' + num + ']" type="hidden"/></div><div style="display:inline-block;float:right"><i class="fa fa-close fa-lg" onClick="deleteRow(' + num + ');"></i></div></td></tr>';
                        $('#itemsTable1').append(r);
						makeSelect();
                    }
                });
            });
			function makeSelect(){
				$('.selectpicker').selectpicker({
					liveSearch: true,
					showSubtext: true
				});
			  }
            ////// delete product row///////////
            function deleteRow(ind) {
                var id = "addr" + ind;
                var itemid = "prod_code" + "[" + ind + "]";
                var qtyid = "req_qty" + "[" + ind + "]";
                var rateid = "price" + "[" + ind + "]";
                var lineTotal = "linetotal[" + ind + "]";
                var mrpid = "mrp" + "[" + ind + "]";
                var holdRateid = "holdRate" + "[" + ind + "]";
                var abl_qtyid = "avl_stock" + "[" + ind + "]";
                // hide fieldset \\
                document.getElementById(id).style.display = "none";
                // Reset Value\\
                // Blank the Values \\
                document.getElementById(itemid).value = "";
                document.getElementById(lineTotal).value = "0.00";
                document.getElementById(qtyid).value = "0.00";
                document.getElementById(rateid).value = "0.00";
                document.getElementById(mrpid).value = "0.00";
                document.getElementById(holdRateid).value = "0.00";
                document.getElementById(abl_qtyid).value = "0.00";
                rowTotal(ind);
            }
            /////// calculate line total /////////////
            function rowTotal(ind) {                
                var ent_qty = "req_qty[" + ind + "]";
                var ent_rate = "price[" + ind + "]";
               // var hold_rate = "holdRate[" + ind + "]";
                var availableQty = "avl_stock[" + ind + "]";
               // var prodCodeField = "prod_code[" + ind + "]";
                //var prodmrpField = "mrp[" + ind + "]";
                var sgst_per = "sgst_per[" + ind + "]";
                var sgst_amt = "sgst_amt[" + ind + "]";
                var cgst_per = "cgst_per[" + ind + "]";
                var cgst_amt = "cgst_amt[" + ind + "]";
                var igst_per = "igst_per[" + ind + "]";
                var igst_amt = "igst_amt[" + ind + "]";
                var value = "value[" + ind + "]";
                var var3 = "linetotal[" + ind + "]";
               // var holdRate = document.getElementById(hold_rate).value;
                ////// check if entered qty is something
                if (document.getElementById(ent_qty).value) {
                    var qty = document.getElementById(ent_qty).value;
                } else {
                    var qty = 0;
                }
                /////  check if entered price is somthing
                if (document.getElementById(ent_rate).value) {
                    var price = document.getElementById(ent_rate).value;
                } else {
                    var price = 0.00;
                }
				<?php if($_REQUEST["doc_type"]=="DC"){ ?>
					var sgst = 0.00;
					var cgst = 0.00;
					var igst = 0.00;
				<?php }else{?>
                /////  check if entered sgst per is somthing
                if (document.getElementById(sgst_per).value) {
                    var sgst = document.getElementById(sgst_per).value;
                } else {
                    var sgst = 0.00;
                }
                /////  check if entered cgst per is somthing
                if (document.getElementById(cgst_per).value) {
                    var cgst = document.getElementById(cgst_per).value;
                } else {
                    var cgst = 0.00;
                }
                /////  check if entered igst per is somthing
                if (document.getElementById(igst_per).value) {
                    var igst = document.getElementById(igst_per).value;
                } else {
                    var igst = 0.00;
                }
               <?php }?>
			   var gstrate = parseFloat(sgst) + parseFloat(cgst) + parseFloat(igst);
			    var gstamt = parseFloat(document.getElementById(sgst_amt).value) + parseFloat(document.getElementById(cgst_amt).value) + parseFloat(document.getElementById(igst_amt).value);
				//alert("<?=$addressfrom[3]."==".$parentlocdet[4]?>");
				<?php 
				if($_REQUEST["po_from_address"]){
				if($addressfrom[3]==$parentlocdet[4]){ ?>
					var cgst = parseFloat(gstrate/2,2);
					var sgst = parseFloat(gstrate/2,2);
					var igst = 0.00;
					var sgstamt = parseFloat(gstamt/2,2);
					var cgstamt = parseFloat(gstamt/2,2);
					var igstamt = 0.00;
				<?php }else{?>
					var cgst = 0.00;
					var sgst = 0.00;
					var igst = parseFloat(gstrate,2);
					var sgstamt = 0.00;
					var cgstamt = 0.00;
					var igstamt = parseFloat(gstamt,2);
				<?php }?>
				document.getElementById(sgst_per).value = sgst;
				document.getElementById(cgst_per).value = cgst;
				document.getElementById(igst_per).value = igst;
				document.getElementById(sgst_amt).value = formatCurrency(sgstamt);
				document.getElementById(cgst_amt).value = formatCurrency(cgstamt);
				document.getElementById(igst_amt).value = formatCurrency(igstamt);
				<?php }?>
               // if (parseFloat(qty)) {
					var total = parseFloat(qty) * parseFloat(price);                
					var sgst_amt1 = total * sgst/100;
					var cgst_amt1 = total * cgst/100;
					var igst_amt1 = total * igst/100;
					var linetotal = parseFloat(total + sgst_amt1 + cgst_amt1 + igst_amt1);
					document.getElementById(value).value = formatCurrency(total);
					document.getElementById(sgst_amt).value = formatCurrency(sgst_amt1);
					document.getElementById(cgst_amt).value = formatCurrency(cgst_amt1);
					document.getElementById(igst_amt).value = formatCurrency(igst_amt1);         
					document.getElementById(var3).value = formatCurrency(linetotal);
					calculatetotal();
                //}
                //else {
                   // alert("Please Enter Qty.");
                   // document.getElementById(ent_qty).value = "";
                   // document.getElementById(ent_rate).value = document.getElementById(ent_rate).value;
                //}
            }
            
            ////// calculate final value of form /////
            
            function calculatetotal() {
                var rowno = (document.getElementById("rowno").value);
                var sum_qty = 0;
                var sum_subtotal = 0.00;
				var sum_total = 0.00;
                var sum_sgst_total = 0.00;
                var sum_cgst_total = 0.00;
                var sum_igst_total = 0.00;
                for (var i = 0; i <= rowno; i++) {
                    var temp_qty = "req_qty[" + i + "]";
					var temp_val = "value[" + i + "]";
                    var temp_total = "linetotal[" + i + "]";
                    var sgst_amt = "sgst_amt[" + i + "]";
                    var cgst_amt = "cgst_amt[" + i + "]";
                    var igst_amt = "igst_amt[" + i + "]";
                    var totalamtvar = 0.00;
					var valamtvar = 0.00;
                    var totalamtsgst = 0.00;
                    var totalamtcgst= 0.00;
                    var totalamtigst= 0.00;
                    
                    ///// check if line total value is something
                    
                    if (document.getElementById(temp_total).value) {
                        totalamtvar = document.getElementById(temp_total).value;
                    } else {
                        totalamtvar = 0.00;
                    }
					if (document.getElementById(temp_val).value) {
                        valamtvar = document.getElementById(temp_val).value;
                    } else {
                        valamtvar = 0.00;
                    }
                    if (document.getElementById(sgst_amt).value) {
                        totalamtsgst = document.getElementById(sgst_amt).value;
                    } else {
                        totalamtsgst = 0.00;
                    }
                    if (document.getElementById(cgst_amt).value) {
                        totalamtcgst = document.getElementById(cgst_amt).value;
                    } else {
                        totalamtcgst = 0.00;
                    }
                    if (document.getElementById(igst_amt).value) {
                        totalamtigst = document.getElementById(igst_amt).value;
                    } else {
                        totalamtigst = 0.00;
                    }
                    sum_qty += parseFloat(document.getElementById(temp_qty).value);
					sum_subtotal += parseFloat(valamtvar);
                    sum_total += parseFloat(totalamtvar);
                    sum_sgst_total +=parseFloat(totalamtsgst);
                    sum_cgst_total +=parseFloat(totalamtcgst);
                    sum_igst_total +=parseFloat(totalamtigst);

                    }
                    /// close for loop
                    var round_off = parseFloat(parseFloat(Math.round(sum_total)) - parseFloat(sum_total)).toFixed(2);
                    document.getElementById("total_qty").value = sum_qty;
                    document.getElementById("sub_total").value = formatCurrency(sum_subtotal);
					document.getElementById("grand_total").value = formatCurrency(sum_total);
                    document.getElementById("round_off").value = formatCurrency(round_off);
                    document.getElementById("final_total").value = formatCurrency(Math.round(sum_total));
                    document.getElementById("tot_sgst_amt").value = formatCurrency(sum_sgst_total);
                    document.getElementById("tot_cgst_amt").value = formatCurrency(sum_cgst_total);
                    document.getElementById("tot_igst_amt").value = formatCurrency(sum_igst_total);
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
        </script>
		<script type="text/javascript">
            ///// function for checking duplicate Product value
            function checkDuplicate(fldIndx1, enteredsno) { 		 
			 document.getElementById("upd").disabled = false;
                if (enteredsno != '') {
                    var check2 = "prod_code[" + fldIndx1 + "]";
                    var flag = 1;
                    for (var i = 0; i <= fldIndx1; i++) {
                        var check1 = "prod_code[" + i + "]";
                        if (fldIndx1 != i && document.getElementById(check2).value != '' && document.getElementById(check1).value != '') {
                            if ((document.getElementById(check2).value == document.getElementById(check1).value)) {
                                alert("Duplicate Product Selection.");
                                document.getElementById(check2).value = '';
                                document.getElementById(check2).style.backgroundColor = "#F66";
                                flag *= 0;
                            }
                            else {
                                document.getElementById(check2).style.backgroundColor = "#FFFFFF";
                                flag *= 1;
                                ///do nothing
                            }
                        }
                    }//// close for loop
                    if (flag == 0) {
                        return false;
                    } else {
                        return true;
                    }
                }
				
            }
		</script>
    </head>
    <body onKeyPress="return keyPressed(event);">
        <div class="container-fluid">
            <div class="row content">
                <?php
                include("../includes/leftnav2.php");
                ?>
                <div class="col-sm-9">
                    <h2 align="center"><i class="fa fa-reply-all"></i> Add New Direct Return</h2>
                    <div class="form-group"  id="page-wrap" style="margin-left:10px;">
                        <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
                            <div class="form-group">
                                <div class="col-md-10"><label class="col-md-5 control-label">Return From<span style="color:#F00">*</span></label>
                                    <div class="col-md-7">
                                        <select name="po_from" id="po_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                                            <option value="" selected="selected">Please Select </option>
                                            <?php
                                            $sql_parent = "select name , city, state,asc_code from asc_master where status='Active' AND id_type IN ('DS') ORDER BY name";
                                            $res_parent = mysqli_query($link1, $sql_parent);
                                            while($party_det = mysqli_fetch_array($res_parent)) {
                                                ?>
                                                <option value="<?= $party_det['asc_code']?>" <?php if ($party_det['asc_code'] == $_REQUEST['po_from']) echo "selected"; ?> >
                                                    <?= $party_det['name'] . " | " . $party_det['city'] . " | " . $party_det['state'] . " | " . $party_det['asc_code'] ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10"><label class="col-md-5 control-label">Return From Address<span style="color:#F00">*</span></label>
                                    <div class="col-md-7">
										<select name="po_from_address" id="po_from_address" required class="form-control required" data-live-search="true" onChange="document.frm1.submit();">
                                            <option value="<?=$childlocdet[2]."~".$childlocdet[0]."~".$childlocdet[3]."~".$childlocdet[4]."~".$childlocdet[5]."~".$childlocdet[6]?>"<?php if($childlocdet[2]."~".$childlocdet[0]."~".$childlocdet[3]."~".$childlocdet[4]."~".$childlocdet[5]."~".$childlocdet[6]==$_REQUEST['po_from_address']){ echo "selected";}?>>Default</option>
                                            <?php
                                            $sql_billto = "SELECT * FROM delivery_address_master WHERE location_code='".$_REQUEST['po_from']."' AND status='Active'";
                                            $res_billto = mysqli_query($link1, $sql_billto);
                                            while($row_billto = mysqli_fetch_array($res_billto)) {
											?>
                                            <option value="<?=$row_billto['party_name']."~".$row_billto['address']."~".$row_billto['city']."~".$row_billto['state']."~".$row_billto['gstin']."~".$row_billto['pincode']?>" <?php if($row_billto['party_name']."~".$row_billto['address']."~".$row_billto['city']."~".$row_billto['state']."~".$row_billto['gstin']."~".$row_billto['pincode']==$_REQUEST['po_from_address']){ echo "selected";}?>><?=$row_billto['party_name']." | ".$row_billto['address']." | ".$row_billto['city']." | ".$row_billto['state']?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10"><label class="col-md-5 control-label">Return To<span style="color:#F00">*</span></label>
                                    <div class="col-md-7">
                                        <select name="po_to" id="po_to" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                                            <option value="" selected="selected">Please Select </option>
                                            <?php
                                            $sql_chl = "select * from access_location where uid='$_SESSION[userid]' and status='Y' AND id_type IN ('HO','BR')";
                                            $res_chl = mysqli_query($link1, $sql_chl);
                                            while ($result_chl = mysqli_fetch_array($res_chl)) {
                                                $party_det = mysqli_fetch_array(mysqli_query($link1, "select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]' AND id_type IN ('HO','BR')"));
                                                
                                                    ?>
                                                    <option data-tokens="<?= $party_det['name'] . " | " . $result_chl['location_id'] ?>" value="<?= $result_chl['location_id'] ?>" <?php if ($result_chl['location_id'] == $_REQUEST['po_to']) echo "selected"; ?> >
                                                        <?= $party_det['name'] . " | " . $party_det['city'] . " | " . $party_det['state'] . " | " . $result_chl['location_id'] ?>
                                                    </option>
                                                    <?php
                                                
                                            }
                                            ?>                                           
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10"><label class="col-md-5 control-label">Cost Centre(Godown)<span style="color:#F00">*</span></label>
                                    <div class="col-md-7">
                                        <select name="stock_in" id="stock_in" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                                            <option value="" selected="selected">Please Select </option>
                                             <?php                                 
											$smfm_sql = "SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code='".$_REQUEST['po_to']."'";
											$smfm_res = mysqli_query($link1,$smfm_sql);
											while($smfm_row = mysqli_fetch_array($smfm_res)){
											?>
											<option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['stock_in'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
											<?php
											}
											?>
											<?php                                 
											$smf_sql = "SELECT sub_location, sub_location_name FROM sub_location_master WHERE main_location='".$_REQUEST['po_to']."' AND status='Active'";
											$smf_res = mysqli_query($link1,$smf_sql);
											while($smf_row = mysqli_fetch_array($smf_res)){
											?>
											<option value="<?=$smf_row['sub_location']?>" <?php if($smf_row['sub_location']==$_REQUEST['stock_in'])echo "selected";?>><?=$smf_row['sub_location_name']." | ".$smf_row['sub_location']?></option>
											<?php
											}
											?>
                                        </select>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10"><label class="col-md-5 control-label">Document Type<span style="color:#F00">*</span></label>
                                    <div class="col-md-7">
										<select name="doc_type" id="doc_type" class="form-control required" onChange="document.frm1.submit();">
											<option value=""<?php if($_REQUEST["doc_type"]==""){ echo "selected";}?>>Invoice</option>
                                            <?php /*?><option value="DC"<?php if($_REQUEST["doc_type"]=="DC"){ echo "selected";}?>>Delivery Challan</option><?php */?>
                                     	</select>
                                    </div>
                                </div>
                            </div>
                        
                            <?php if($_REQUEST["doc_type"]=="DC"){ ?>
                            <div class="form-group">
                                <div class="col-md-10"><label class="col-md-5 control-label">Ledger Name<span style="color:#F00">*</span></label>
                                    <div class="col-md-7">
										<select name="ledger_name" id="ledger_name" class="form-control required" onChange="document.frm1.submit();">
                                        	<option value=""<?php if($_REQUEST["ledger_name"]==""){ echo "selected";}?>>--Please Select--</option>
											<option value="Warranty Purchase"<?php if($_REQUEST["ledger_name"]=="Warranty Purchase"){ echo "selected";}?>>Warranty Purchase</option>
                                            <option value="Sample & Testing Purchase"<?php if($_REQUEST["ledger_name"]=="Sample & Testing Purchase"){ echo "selected";}?>>Sample & Testing Purchase</option>
                                            <option value="Branch Purchase Within State GST"<?php if($_REQUEST["ledger_name"]=="Branch Purchase Within State GST"){ echo "selected";}?>>Branch Purchase Within State GST</option>
                                            <option value="POP Material Purchase"<?php if($_REQUEST["ledger_name"]=="POP Material Purchase"){ echo "selected";}?>>POP Material Purchase</option>
                                            <option value="Business Promotion Purchase"<?php if($_REQUEST["ledger_name"]=="Business Promotion Purchase"){ echo "selected";}?>>Business Promotion Purchase</option>
                                            <option value="Purchase Other"<?php if($_REQUEST["ledger_name"]=="Purchase Other"){ echo "selected";}?>>Purchase Other</option>
                                     	</select>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                        </form>
                        <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
                            <div class="form-group">
                                <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
                                    <thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th data-class="expand" class="col-md-2" style="font-size:13px;">Product</th>
                                            <th style="font-size:13px;text-align: center;padding: 5px;">Qty</th>
                                            <th data-hide="phone" class="col-md-1" style="font-size:13px;text-align: right;padding: 5px;">Price <?php if ($_REQUEST['currency_type'] == "USD") { ?>(<i class="fa fa-usd" aria-hidden="true"></i>)<?php } else { ?>(<i class="fa fa-inr" aria-hidden="true"></i>)<?php } ?></th>
                                            <th data-hide="phone" style="font-size:13px;padding: 5px;text-align: center">Value</th>
                                            <th data-hide="phone" style="font-size:13px;padding: 5px;text-align: center">SGST<br>(%)</th>
                                            <th data-hide="phone" class="col-md-1" style="font-size:13px;padding: 5px;text-align: center">SGST Amt</th>
                                            <th data-hide="phone" style="font-size:13px;padding: 5px;text-align: center">CGST<br>(%)</th>
                                            <th data-hide="phone" class="col-md-1" style="font-size:13px;padding: 5px;text-align: center">CGST Amt</th>
                                            <th data-hide="phone" style="font-size:13px;padding: 5px;text-align: center">IGST<br>(%)</th>
                                            <th data-hide="phone" class="col-md-1" style="font-size:13px;padding: 5px;text-align: center">IGST Amt</th>
                                            <th data-hide="phone,tablet" class="col-md-1" style="font-size:13px;text-align: center;padding: 5px;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id='addr0'>
                                            <td class="col-md-2">
                                                <span id="pdtid0">
                                                    <select name="prod_code[0]" id="prod_code[0]" class="form-control selectpicker" required data-live-search="true" onChange="getAvlStk(0);checkDuplicate(0, this.value);">
                                                        <option value="">--None--</option>
                                                        <?php
                                                        $model_query = "select productcode,productname,productcolor from product_master where status='active'";
                                                        $check1 = mysqli_query($link1, $model_query);
                                                        while ($br = mysqli_fetch_array($check1)) {
                                                            ?>
                                                            <option value="<?php echo $br['productcode']; ?>"><?= $br['productname'] . ' | ' . $br['productcolor'] . ' | ' . $br['productcode']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </span>
                                            </td>
                                            <td width="6%"><input type="text" class="form-control digits"  name="req_qty[0]" id="req_qty[0]"  autocomplete="off" required onBlur="myFunction(this.value, 0, 'req_qty'); rowTotal(0);" onKeyPress="return onlyNumbers(this.value);" style="text-align: right;padding: 4px;" value="0"></td>
                                            <td class="col-md-1"><input type="text" class="form-control" name="price[0]" id="price[0]" value="0.00" onBlur="rowTotal(0);" autocomplete="off" onKeyPress="return onlyFloatNum(this.value);" required style="text-align: right;padding: 3px;"></td>                                            
                                            <td width="10%"><input type="text" name="value[0]" id="value[0]" class="form-control" value="0.00" readonly style="text-align: right;padding: 2px;"/></td>
                                            <td width="6%"><input type="text" name="sgst_per[0]" id="sgst_per[0]" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td>
                                            <td width="10%"><input type="text" name="sgst_amt[0]" id="sgst_amt[0]" class="form-control" value="0.00" readonly style="text-align: right;padding: 2px;"/></td>
                                            <td width="6%"><input type="text" name="cgst_per[0]" id="cgst_per[0]" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td>
                                            <td width="10%"><input type="text" name="cgst_amt[0]" id="cgst_amt[0]" class="form-control" value="0.00" readonly style="text-align: right;padding: 2px;"/></td>
                                            <td width="6%"><input type="text" name="igst_per[0]" id="igst_per[0]" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td>
                                            <td width="10%"><input type="text" name="igst_amt[0]" id="igst_amt[0]" class="form-control" value="0.00" readonly style="text-align: right;padding: 2px;"/></td> 
                                            <td class="col-md-1"><input type="text" class="form-control" name="linetotal[0]" id="linetotal[0]" autocomplete="off" readonly style="width:85px;text-align: right;padding: 4px;"><input type="hidden" class="form-control" name="avl_stock[0]" id="avl_stock[0]"  autocomplete="off" style="width:50px;text-align: right;padding: 4px;" value="0" readonly>
                                                <input name="mrp[0]" id="mrp[0]" type="hidden"/>
                                                <input name="holdRate[0]" id="holdRate[0]" type="hidden"/></td>
                                        </tr>
                                    </tbody>
                                    <tfoot id='productfooter' style="z-index:-9999;">
                                        <tr class="0">
                                            <td colspan="12" style="font-size:13px;">
                                                <a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a>
                                                <input type="hidden" name="rowno" id="rowno" value="0"/>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10">
                                    <label class="col-md-3 control-label">Total Qty</label>
                                    <div class="col-md-3">
                                        <input type="text" name="total_qty" id="total_qty" class="form-control" value="0" readonly/>
                                    </div>
                                    <label class="col-md-3 control-label">Sub Total</label>
                                    <div class="col-md-3">
                                        <input type="text" name="sub_total" id="sub_total" class="form-control" value="0.00" style="text-align:right" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10">
                                    <label class="col-md-3 control-label">Invoice No.<span style="color:#F00">*</span></label>
                                    <div class="col-md-3">
                                       <input type="text" name="invoiceno" id="invoiceno" class="form-control required" required/>
                                    </div>
                                    <label class="col-md-3 control-label">Total SGST Amt</label>
                                    <div class="col-md-3">
                                        <input type="text" name="tot_sgst_amt" id="tot_sgst_amt" class="form-control" value="0.00" style="text-align:right" readonly/>
                                    </div>
                                </div>
                            </div>
							<div class="form-group">
                                <div class="col-md-10">
                                    <label class="col-md-3 control-label">Invoice Date <span style="color:#F00">*</span></label>
                                    <div class="col-md-3">
                                    	<input type="text" class="form-control span2 required" name="invoicedate"  id="invoicedate" required>
                                    </div>
                                    <label class="col-md-3 control-label">Total CGST Amt</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control span2" name="tot_cgst_amt"  id="tot_cgst_amt" value="0.00" style="text-align:right" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10">
                                    <label class="col-md-3 control-label">&nbsp;</label>
                                    <div class="col-md-3">
                                        &nbsp;
                                    </div>
                                    <label class="col-md-3 control-label">Total IGST Amt</label>
                                    <div class="col-md-3">
                                       <input type="text" name="tot_igst_amt" id="tot_igst_amt" class="form-control" value="0.00" style="text-align:right" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10">
                                    <label class="col-md-3 control-label">&nbsp;</label>                                  
                                    <div class="col-md-3">
                                        &nbsp;
                                    </div>                                  
                                   <label class="col-md-3 control-label">Grand Total</label>
                                    <div class="col-md-3">
                                        <input type="text" name="grand_total" id="grand_total" class="form-control" value="0.00" style="text-align:right" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10">
                                    <label class="col-md-3 control-label">TCS %</label>
                                    <div class="col-md-3">
                                    	<select name="tcs_per" id="tcs_per" class="form-control" onChange="calculatetotal();">
                                            <option value="">--Please Select--</option>
                                            <option value="0.1">0.1 %</option>
                                        </select>
                                    </div>
                                    <label class="col-md-3 control-label">TCS Amount</label>
                                    <div class="col-md-3">
                                        <input type="text" name="tcs_amt" id="tcs_amt" class="form-control" value="0.00" style="text-align:right" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10">
                                    <label class="col-md-3 control-label">Round Off</label>
                                    <div class="col-md-3">
                                    	<input type="text" name="round_off" id="round_off" class="form-control" value="<?=$roundoff?>" style="text-align:right" readonly/>
                                    </div>
                                    <label class="col-md-3 control-label">Final Total</label>
                                    <div class="col-md-3">
                                        <input type="text" name="final_total" id="final_total" class="form-control" value="<?=$roundoff+$grand_total?>" style="text-align:right" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10">
                                    <label class="col-md-3 control-label">Delivery Address<span style="color:#F00">*</span></label>
                                    <div class="col-md-3">
                                        <textarea name="delivery_address" id="delivery_address" class="form-control required" style="resize:none" required><?=$parentlocdet[0]?></textarea>
                                    </div>
                                    <label class="col-md-3 control-label">Remark</label>
                                    <div class="col-md-3">
                                        <textarea name="remark" id="remark" class="form-control" style="resize:none"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10">
                                    <label class="col-md-3 control-label">Sales Executive</label>
                                    <div class="col-md-3">
                                        <select name="se" id="se" class="form-control selectpicker" data-live-search="true" style="width:200px;">
                                        <option value="" <?php if($_REQUEST['sales_executive'] == "") { echo "selected"; } ?> >Please Select </option>
                                        <?php 
                                        $sql_se="select name, username, utype,oth_empid from admin_users where utype in ('2','3','4','5','6','7') and status='Active'";
                                        $res_se=mysqli_query($link1,$sql_se);
                                        while($result_se=mysqli_fetch_array($res_se)){
                                        ?>
                                        <option value="<?=$result_se['username']?>" <?php if($result_se['username']==$_REQUEST['sales_executive']) { echo "selected"; } ?> >
                                        <?=$result_se['name']." | ".$result_se['username']." | ".$result_se['oth_empid']?>
                                        </option>
                                    <?php
                                    }
                                    ?>
                                    </select>
                                    </div>
                                    <label class="col-md-3 control-label">&nbsp;</label>
                                    <div class="col-md-3">
                                  
                                    </div>
                                </div>
                            </div>
                         
                            <div class="form-group">
                                <div class="col-md-12" align="center">
                                    <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Save" title="Save This PO" <?php if ($_POST['upd']=='Save'){ echo "disabled";}?>>
                                    <input type="hidden" name="parentcode" id="parentcode" value="<?= $_REQUEST['po_to'] ?>"/>
                                    <input type="hidden" name="partycode" id="partycode" value="<?= $_REQUEST['po_from'] ?>"/>
                                    <input type="hidden" name="stockfromaddress" id="stockfromaddress" value="<?=$_REQUEST['po_from_address']?>"/>
                                    <input type="hidden" name="stockin" id="stockin" value="<?= $_REQUEST['stock_in'] ?>"/>
                                    <input type="hidden" name="doctype" id="doctype" value="<?= $_REQUEST['doc_type'] ?>"/>
                                    <input type="hidden" name="ledgername" id="ledgername" value="<?= $_REQUEST['ledger_name'] ?>"/>
                                    <a title="Back" type="button" class="btn btn-primary" onClick="window.location.href = 'direct_return.php?<?= $pagenav ?>'">Back</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include("../includes/footer.php");
        include("../includes/connection_close.php");
        ?>
    </body>
</html>
<?php if ($_REQUEST['po_to'] == '' || $_REQUEST['po_from'] == '' || $_REQUEST['stock_in'] == '') { ?>
    <script>
        $("#frm2").find("input:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
    </script>
<?php } ?>