<?php
require_once("../config/config.php");
require_once("../includes/ledger_function.php");
$foc_flag = 0;
$docid = base64_decode($_REQUEST['id']);
$po_sql = "SELECT * FROM purchase_order_master where po_no='" . $docid . "'";
$po_res = mysqli_query($link1, $po_sql);
$po_row = mysqli_fetch_assoc($po_res);
$numRow = mysqli_num_rows($po_res);
///// get parent location details
$parentloc = getLocationDetails($po_row['po_to'], "name,city,state,addrs,disp_addrs,margin,gstin_no,pincode,email,phone", $link1);
$parentlocdet = explode("~", $parentloc);
///// get child location details
$childloc = getLocationDetails($po_row['po_from'], "name,city,state,addrs,disp_addrs,gstin_no,pincode,email,phone,tcs_applicable,tcs_per", $link1);
$childlocdet = explode("~", $childloc);
@extract($_POST);


////// if we hit process button
if ($_POST) {
	if ($_POST['upd'] == 'Process') {
        if ($total_qty != '' && $total_qty != 0) {
			$stock_from = base64_decode($stockfrom);
			if($stock_from){
            //// Make System generated Invoice no.//////
			if($_REQUEST['doctype'] == 'DC'){
            	$res_cnt = mysqli_query($link1, "select stn_str,stn_counter from document_counter where location_code='" . $po_row['po_to'] . "'");
			}else{
				$res_cnt = mysqli_query($link1, "select inv_str,inv_counter from document_counter where location_code='" . $po_row['po_to'] . "'");
			}
            if (mysqli_num_rows($res_cnt)) {
                $row_cnt = mysqli_fetch_array($res_cnt);
				if($_REQUEST['doctype'] == 'DC'){
					$invcnt = $row_cnt['stn_counter'] + 1;
                	$pad = str_pad($invcnt, 3, 0, STR_PAD_LEFT);
                	$invno = $row_cnt['stn_str'] . $pad;
				}else{
                	$invcnt = $row_cnt['inv_counter'] + 1;
                	$pad = str_pad($invcnt, 4, 0, STR_PAD_LEFT);
                	$invno = $row_cnt['inv_str'] . $pad;
				}
                mysqli_autocommit($link1, false);
                $flag = true;
                $err_msg = "";
                ///// Insert Master Data
                $splitcompltetax = explode("~", $complete_tax);
                //// explode shipto
				$expl_ship = explode("~", $ship_to);
				if($expl_ship[0]){
					$shiptodet = explode("~",getAnyDetails($expl_ship[0],"party_name,address,city,state,pincode,gstin","address_code","delivery_address_master",$link1));
					$deli_addrs = $shiptodet[1];
				}else if ($delivery_address) {
                    $deli_addrs = $delivery_address;
                } else {
                    $deli_addrs = $childlocdet[4];
                }
				if($_REQUEST['doctype'] == 'DC'){
					$doctype =  "Delivery Challan";
					$invoicetype = "Delivery Challan";
				}else {
					$doctype =  "INVOICE";
					$invoicetype = "CORPORATE INVOICE";
				}
								
                 $query1 = "INSERT INTO billing_master set from_location='" . $po_row['po_to'] . "',to_location='" . $po_row['po_from'] . "',sub_location='".$stock_from."',from_gst_no='".$parentlocdet[6]."', from_partyname='".$parentlocdet[0]."', party_name='".$childlocdet[0]."', to_gst_no='".$childlocdet[5]."' ,challan_no='" . $invno . "',po_no='" . $po_row['po_no'] . "',ref_no='" . $ref_no . "',sale_date='" . $today . "',entry_date='" . $today . "',entry_time='" . $currtime . "',type='CORPORATE',document_type='".$doctype."',discountfor='PD',taxfor='" . $taxfor . "',status='Pending',entry_by='" . $_SESSION['userid'] . "',basic_cost='" . $sub_total . "',discount_amt='" . $total_discount . "',tax_cost='" . $tax_amount . "',total_sgst_amt='" . $total_sgst . "',total_cgst_amt='" . $total_cgst . "',total_igst_amt='" . $total_igst . "',total_cost='" . $grand_total . "',tax_type='" . $splitcompltetax[1] . "',tax_header='" . $splitcompltetax[2] . "',tax='" . $splitcompltetax[0] . "',bill_from='" . $po_row['po_to'] . "',bill_topty='" . $po_row['po_from'] . "',from_addrs='" . $parentlocdet[3] . "',disp_addrs='" . $parentlocdet[4] . "',to_addrs='" . $childlocdet[3] . "',deliv_addrs='" . $deli_addrs . "',billing_rmk='" . $remark . "' , margin = '".$margin_amt."',from_state='".$parentlocdet[2]."', to_state='".$childlocdet[2]."', from_city='".$parentlocdet[1]."', to_city='".$childlocdet[1]."', from_pincode='".$parentlocdet[7]."', to_pincode='".$childlocdet[6]."', from_phone='".$parentlocdet[9]."', to_phone='".$childlocdet[8]."', from_email='".$parentlocdet[8]."', to_email='".$childlocdet[7]."',round_off='".$round_off."',tcs_per='".$tcs_per."', tcs_amt='".$tcs_amt."',payment_term='".$payment_term."',ship_to='".$shiptodet[0]."',ship_to_gstin='".$shiptodet[5]."',ship_to_city='".$shiptodet[2]."',ship_to_state='".$shiptodet[3]."',ship_to_pincode='".$shiptodet[4]."',ledger_name='".$ledgername."'";	
				 	
                $result = mysqli_query($link1, $query1);
                //// check if query is not executed
                if (!$result) {
                    $flag = false;
                    $err_msg = "Error Code1: ".mysqli_error($link1);
                }
                /// update invoice counter /////
				if($_REQUEST['doctype'] == 'DC'){
					$result = mysqli_query($link1, "update document_counter set stn_counter=stn_counter+1,update_by='" . $_SESSION['userid'] . "',updatedate='" . $datetime . "' where location_code='" . $po_row['po_to'] . "'");
					//// check if query is not executed
					if (!$result) {
						$flag = false;
						$err_msg = "Error Code2: ".mysqli_error($link1);
					}
				}else{
					$result = mysqli_query($link1, "update document_counter set inv_counter=inv_counter+1,update_by='" . $_SESSION['userid'] . "',updatedate='" . $datetime . "' where location_code='" . $po_row['po_to'] . "'");
					//// check if query is not executed
					if (!$result) {
						$flag = false;
						$err_msg = "Error Code2: ".mysqli_error($link1);
					}
				}
				$arr_taxx = array();
				$arr_val = array();
				$gst_type = "";
                ////// pick purchase order details
                $po_datares = mysqli_query($link1, "select * from purchase_order_data where po_no='" . $po_row['po_no'] . "'");
                while ($po_datarow = mysqli_fetch_array($po_datares)) {
                    $rowprodcut = "prodcode" . $po_datarow['id'];
                    $rowqty = "bill_qty" . $po_datarow['id'];
                    $rowprice = "price" . $po_datarow['id'];
                    $rowvalue = "value" . $po_datarow['id'];
                    $rowtotal = "linetotal" . $po_datarow['id'];
                    $rowmrp = "mrp" . $po_datarow['id'];
                    $rowholdrate = "holdRate" . $po_datarow['id'];
                    $rowsgstPer = "sgst_per" . $po_datarow['id'];
                    $rowsgstAmt = "sgst_amt" . $po_datarow['id'];
                    $rowcgstPer = "cgst_per" . $po_datarow['id'];
                    $rowcgstAmt = "cgst_amt" . $po_datarow['id'];
                    $rowigstPer = "igst_per" . $po_datarow['id'];
                    $rowigstAmt = "igst_amt" . $po_datarow['id'];
                    $rowdisc = "rowdiscount" . $po_datarow['id'];
                    $rowtotalval = "total_val" . $po_datarow['id'];
					$rowschemecode = "sch_cd" . $po_datarow['id'];
					//find the scheme name
					if($rowschemecode != ""){
						$rrr = mysqli_fetch_assoc(mysqli_query($link1,"SELECT scheme_name FROM scheme_master WHERE scheme_code = '".$_POST[$rowschemecode]."' "));
						$rowschemename = $rrr['scheme_name'];
					}else{
						$rowschemename = "";
					}
                    //checking row value of product and qty should not be blank
                    $getstk = getCurrentStockNew($po_row['po_to'], $stock_from, $_POST[$rowprodcut], "okqty", $link1);
                    //// check stock should be available ////
                    if ($getstk < $_POST[$rowqty]) {
                        $flag = false;
                        $err_msg = "Error Code3: Stock is not available";
                    } else {
                        
                    }
                    if ($_POST[$rowprodcut] != '' && $_POST[$rowqty] != '' && $_POST[$rowqty] != 0) {
						if($_POST[$rowsgstPer]!="" && $_POST[$rowsgstPer]!="0.00"  && $_POST[$rowsgstPer]!="0"){
							$gstper = $_POST[$rowsgstPer] + $_POST[$rowcgstPer];					
							$arr_taxx[$gstper] += $_POST[$rowsgstAmt] + $_POST[$rowcgstAmt];
							$arr_val[$gstper] += $_POST[$rowvalue];
							$gst_type = "SGST-CGST";
						}else{
							$gstper = $_POST[$rowigstPer];					
							$arr_taxx[$gstper] += $_POST[$rowigstAmt];
							$arr_val[$gstper] += $_POST[$rowvalue];
							$gst_type = "IGST";
						}
                        /////////// insert data
                        $splitrowtax = explode("~", $_POST[$rowtaxtype]);
                        $query2 = "insert into billing_model_data set from_location='" . $po_row['po_to'] . "',challan_no='" . $invno . "', prod_code='" . $_POST[$rowprodcut] . "',prod_cat='" . $po_datarow['prod_cat'] . "', qty='" . $_POST[$rowqty] . "', mrp='" . $_POST[$rowmrp] . "', price='" . $_POST[$rowprice] . "', hold_price='" . $_POST[$rowholdrate] . "', value='" . $_POST[$rowvalue] . "',tax_name='" . $splitrowtax[1] . "',tax_per='" . $splitrowtax[0] . "',tax_amt='" . $_POST[$rowtaxamt] . "',discount='" . $_POST[$rowdisc] . "',sgst_per='" . $_POST[$rowsgstPer] . "',sgst_amt='" . $_POST[$rowsgstAmt] . "',cgst_per='" . $_POST[$rowcgstPer] . "',cgst_amt='" . $_POST[$rowcgstAmt] . "',igst_per='" . $_POST[$rowigstPer] . "',igst_amt='" . $_POST[$rowigstAmt] . "', totalvalue='" . $_POST[$rowtotalval] . "',sale_date='" . $today . "',entry_date='" . $today . "', scheme_name = '".$rowschemename."', scheme_code = '".$_POST[$rowschemecode]."' ";
						
                        $result2 = mysqli_query($link1, $query2);
                        //// check if query is not executed
                        if (!$result2) {
                            $flag = false;
                            $err_msg = "Error Code4: ".mysqli_error($link1);
                        }
                        //// update stock of from loaction
						
                       $result_2 = mysqli_query($link1, "update stock_status set okqty=okqty-'" . $_POST[$rowqty] . "',updatedate='" . $datetime . "' where asc_code='" . $po_row['po_to'] . "' and sub_location='" . $stock_from . "' and partcode='" . $_POST[$rowprodcut] . "'");
                        //// check if query is not executed
                        if (!$result_2) {
                            $flag = false;
                            $err_msg = "Error Code5:";
                        }
                        ///// update stock ledger table
                        $flag = stockLedger($invno, $today, $_POST[$rowprodcut], $stock_from, $po_row['po_from'], $stock_from, "OUT", "OK", $invoicetype, $_POST[$rowqty], $_POST[$rowprice], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
                        ////// release the PO qty in stock ////
                        $flag = releaseStockQty($po_row['po_to'], $_POST[$rowprodcut], $po_row['req_qty'], $link1, $flag);
						
                    }// close if loop of checking row value of product and qty should not be blank
                    //// update details in po table
					
                   $result_3 = mysqli_query($link1, "update purchase_order_data set qty=qty+'" . $_POST[$rowqty] . "' where id='" . $po_datarow['id'] . "'");
                    //// check if query is not executed
                    if (!$result_3) {
                        $flag = false;
                        $err_msg = "Error Code6: ".mysqli_error($link1);
                    }
                }/// close while loop
				
				///////  Entry for the scheme start //////////////
				$s = $_REQUEST['count'];
				$t = $_REQUEST['norow'];
				if($_REQUEST['norow']>0){
					for($l=$s; $l<($s+$t); $l++){
						if(($_REQUEST['schqty'.$l] != "") && ($_REQUEST['schprd'.$l] != "")){
                        /////////// insert data
                        $query2_1 = "insert into billing_model_data set from_location='" . $po_row['po_to'] . "',challan_no='" . $invno . "', prod_code='" . $_REQUEST['schprd'.$l] . "',prod_cat='', qty='" . $_REQUEST['schqty'.$l] . "', mrp='0.00', price='0.00', hold_price='0.00', value='0.00',tax_name='',tax_per= '0.00',tax_amt='0.00',discount='0.00',sgst_per='0.00',sgst_amt='0.00',cgst_per='0.00',cgst_amt='0.00',igst_per='0.00',igst_amt='0.00', totalvalue='0.00',sale_date='" . $today . "',entry_date='" . $today . "', scheme_name = 'FOC', scheme_code = '' ";
						
                        $result2_1 = mysqli_query($link1, $query2_1);
                        //// check if query is not executed
                        if (!$result2_1) {
                            $flag = false;
                            $err_msg = "Error Code4.1: ".mysqli_error($link1);
                        }
                        //// update stock of from loaction
						
                       $result2_2 = mysqli_query($link1, "update stock_status set okqty=okqty-'" . $_REQUEST['schqty'.$l] . "',updatedate='" . $datetime . "' where asc_code='" . $po_row['po_to'] . "' and partcode='" . $_REQUEST['schprd'.$l] . "'");
                        //// check if query is not executed
                        if (!$result2_2) {
                            $flag = false;
                            $err_msg = "Error Code5.1: ".mysqli_error($link1);
                        }
                        ///// update stock ledger table
                       $flag = stockLedger($invno, $today, $_REQUEST['schprd'.$l], $po_row['po_to'], $po_row['po_from'], $po_row['po_to'], "OUT", "OK", $invoicetype, $_REQUEST['schqty'.$l], $_POST[$rowprice], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
                        ////// release the PO qty in stock ////
                      	$flag = releaseStockQty($po_row['po_to'], $_REQUEST['schprd'.$l], $_REQUEST['schqty'.$l], $link1, $flag);
						
                    
					}
					}
				}
				///////  Entry for the scheme stop //////////////
				
				
                ///// update invoice and date in po master details
							
              $result = mysqli_query($link1, "update purchase_order_master set status='Processed',dispatch_challan='" . $invno . "',challan_date='" . $today . "' where po_no='" . $po_row['po_no'] . "'");
                //// check if query is not executed
                if (!$result) {
                    $flag = false;
                    $err_msg = "Error Code7: ".mysqli_error($link1);
                }
                //// update cr bal of child location
				
               $result = mysqli_query($link1, "update current_cr_status set cr_abl=cr_abl-'" . $grand_total . "',total_cr_limit=total_cr_limit-'" . $grand_total . "', last_updated='" . $datetime . "' where parent_code='" . $po_row['po_to'] . "' and asc_code='" . $po_row['po_from'] . "'");
                //// check if query is not executed
                if (!$result) {
                    $flag = false;
                    $err_msg = "Error Code8: ".mysqli_error($link1);
                }
                ////// maintain party ledger////
             	$flag = partyLedger($po_row['po_to'], $po_row['po_from'], $invno, $today, $today, $currtime, $_SESSION['userid'], $invoicetype, $grand_total, "DR", $link1, $flag);				
				//////////////
				
				if($_POST['margin_amt'] != '0' || $_POST['margin_amt'] != '0.00') {
				 ////// maintain party ledger for margin////
                $flag = partyLedger($po_row['po_to'], $po_row['po_from'], $invno, $today, $today, $currtime, $_SESSION['userid'], "Margin Against Invoice", $grand_total, "CR", $link1, $flag);	
			  }	
			  ///////////////////////////////////////////////////	
				
				
                ////// insert in activity table////
                $flag = dailyActivity($_SESSION['userid'], $invno, $invoicetype, "CREATE", $ip, $link1, $flag);
				/////// make account ledger entry for location
				/////// start ledger entry for tally purpose ///// written by shekhar on 19 july 2022
				///// make ledger array which are need to be process
				/*$arr_ldg_name = array(
				"igstldgname" => "IGST @",
				"cgstldgname" => "CGST @",
				"sgstldgname" => "SGST @",
				"igstdocldgname" => "Central Sale @",
				"cgstdocldgname" => "GST Sales @",
				"sgstdocldgname" => "GST Sales @",
				"tcsldgname" => "TCS on Sale @",
				"roundoffldgname" => "Rounded Off"
				);*/
				if($_REQUEST['doctype'] == 'DC'){
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
					$hedid = "2";
					$hed = "Sale";
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
				}
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
				/*if($_REQUEST['doctype'] == 'DC'){
					$hedid = "7";
					$hed = "Delivery Challan";
				}else{
					$hedid = "2";
					$hed = "Sale";
				}*/
				$resp = explode("~",storeLedgerTransaction($po_row['po_to'],$invno,$today,$hedid,$hed,$arr_taxx,$arr_val,$tcs_per,$tcs_amt,$round_off,$gst_type,$arr_ldg_name,"GST Sales","GST Sales Account",$link1,$flag));
				$flag = $resp[0];
				if($err_msg==""){
					$err_msg = $resp[1];
				}
				/////// end ledger entry for tally purpose ///// written by shekhar on 12 july 2022
                ///// check both master and data query are successfully executed
                if ($flag) {
                    mysqli_commit($link1);
                    $msg = "Invoice is successfully created with ref. no. " . $invno;
                } else {
                    mysqli_rollback($link1);
                    $msg = "Request could not be processed " . $err_msg . ". Please try again.";
                }
                mysqli_close($link1);
            } else {
                $msg = "Request could not be processed invoice series not found. Please try again.";
            }
			}else{
				$msg = "Request could not be processed . Please select cost center.";
			}
        } else {
            $msg = "Request could not be processed . Please dispatch some qty.";
        }
        ///// move to parent page
        header("location:corporateInvoice.php?msg=" . $msg . "" . $pagenav);
       exit;
    }else{}
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= siteTitle ?></title>
        <script src="../js/jquery.min.js"></script>
        <link href="../css/font-awesome.min.css" rel="stylesheet">
        <link href="../css/abc.css" rel="stylesheet">
        <script src="../js/bootstrap.min.js"></script>
        <link href="../css/abc2.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/bootstrap.min.css">

        <script type="text/javascript">
            $(document).ready(function() {
                $("#frm2").validate();
            });
        </script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/common_js.js"></script>
        <script type="text/javascript">
////// function for open model to see the purchase
	function checkScheme(refid, indx, val, prdDtl){
		var aplQty = document.getElementById("bill_qty"+indx).value;
		$.get('scheme_applicable.php?pk=' + refid + '&indx_no=' + indx + '&val_amt=' + val + '&val_qty=' + aplQty + '&prd_Dtl=' + prdDtl, function(html){
			 $('#viewModal .modal-body').html(html);
			 $('#viewModal').modal({
				show: true,
				backdrop:"static"
			});
		 });
		 $("#viewModal #tile_name").html("<i class='fa fa-tags'></i> Scheme Applicable");
	}
/////// Scheme selection  /////////	
	function getSchemeAmut(){
		var valAmt = document.getElementById("valAmt").value;
		var valINDNo = document.getElementById("valINDNo").value;
		var valPrd = document.getElementById("valPrd").value;
		var prdDtlVal = document.getElementById("prdDtlVal").value;
		var schemeApplicable = document.querySelector('input[name="scheme_applicable"]:checked').value;
		
		$.ajax({
		type:'post',
		url:'../includes/getAzaxFields.php',
		data:{schemeInfo:valAmt,valINDNo:valINDNo,valPrd:valPrd,prdDtlVal:prdDtlVal,schemeApplicable:schemeApplicable},
		success:function(data){
		var getValue = data.split("~");
		getDesc(getValue);
		}
		});
	}
	
////// delete scheme row///////////
	function removeROW(obj,indx,qtt){  	
		var spltid = obj.id.split("_"); 
		var id = spltid[1];
		var cnt = ($('#tbl_scheam tr').length);
		$('#'+id+'').remove();
		var tq = 0;
		if(id == (cnt -1)){
		//alert("hi");
		}else{
			for(var i = id; i < cnt-1; i++){
				var current_indx = parseInt(i) + 1;
				var new_indx = parseInt(current_indx) - 1;
				///// change id again
				$('#'+current_indx+'').prop('id', new_indx);
				$('#schqty'+current_indx+'').prop('id', 'schqty'+new_indx);
				$('#schprd'+current_indx+'').prop('id', 'schprd'+new_indx);
				$('#canicn_'+current_indx+'').prop('id', 'canicn_'+new_indx);
			}
		}
		document.getElementById("scheme"+indx).style.display = "block";
		num1 = document.getElementById("norow").value;
		if(parseInt(document.getElementById("norow").value)>0){
			document.getElementById("norow").value = parseInt(parseInt(num1)-1);	
		}else{
			document.getElementById("norow").value = 0;	
		}
		getToQty();
	}
/////////////////////////////////////////////
/////// used for discount calculation ///////	
	function getDesc(val){	
			document.getElementById("sch_cd"+val[3]).value = val[6];
			if(val[1] == 1){
				num1 = document.getElementById("norow").value;
				document.getElementById("norow").value = parseInt(parseInt(num1)+1);
				
				var list = $("#tbl_scheam");
				var nextid = ($('#tbl_scheam tr').length);
				
				$.each(list, function(i) {
								
				var r='<tr id="'+nextid+'"><td>'+val[5]+'</td><td> 0.00 </td><td><input type="text"  name="schqty'+nextid+'"  id="schqty'+nextid+'" style="text-align:right;" class="form-control" value="'+val[0]+'" /><input type="hidden"  name="schprd'+nextid+'"  id="schprd'+nextid+'" value="'+val[4]+'" /></td><td><input type="text" class="form-control" value="0.00" /></td><td><input type="text" class="form-control" value="0.00" /></td>	<td><input type="text" class="form-control" value="0.00" /></td><td><input type="text" class="form-control"  value="0.00" /></td><td><input type="text" class="form-control" value="0.00" /></td>	<td><input type="text" class="form-control"  value="0.00" /></td><td><input type="text" class="form-control"  value="0.00"/></td><td><input type="text" class="form-control" value="0.00" /></td>	<td><input type="text" class="form-control" value="0" /></td><td><input type="text" class="form-control"  value="0" /></td><td><input type="text" class="form-control" value="0.00" /></td>	<td>FOC     <i style="color:red;" id="canicn_'+nextid+'" class="fa fa-close" onclick="removeROW(this,'+val[3]+','+val[0]+')" ></i> </td></tr>';
				
				$(list).append(r);
				document.getElementById("scheme"+val[3]).style.display = "none";
				if(parseFloat(document.getElementById("rowdiscount"+val[3]).value)!="" || parseFloat(document.getElementById("rowdiscount"+val[3]).value)!= "0.00" ){
					document.getElementById("rowdiscount"+val[3]).value = "0.00";
				}
								
				}); ////// end of each
				getToQty();
			}else{
				document.getElementById("rowdiscount"+val[3]).value = parseInt(val[0]);
				getToQty();
			}	
			rowTotal(val[3]);		
	}
	
	function getToQty(){
		var no_row = parseInt(document.getElementById("norow").value);
		var count_vl = parseInt(document.getElementById("count").value);
		var schVal = 0;
		if(no_row>0){
			for(var p= count_vl; p < (count_vl + no_row); p++){
				schVal += parseInt(document.getElementById("schqty"+p).value);
			}
		}
		document.getElementById("total_qty1").value = schVal;
		if((parseInt(document.getElementById("total_qty1").value)>0) && parseInt(document.getElementById("norow").value)>0){
			document.getElementById("total_qty").value = parseInt(parseInt(document.getElementById("total_qty1").value)+parseInt(document.getElementById("qtyto").value));
	    }else{
			document.getElementById("total_qty").value = parseInt(document.getElementById("qtyto").value);
		}
		
	}
		
	function getChallan(val){
	 var i =  document.getElementById("count").value;
	if(val == 'DC'){
	var sum_qty = 0.00
	for(var j=1 ; j<i ;j++){
	//	var qty = 0.00;
		var value = 0.00;
		//var total = 0.00
	document.getElementById("igst_per" + j + "").value = 0.00;
	document.getElementById("igst_amt" + j + "").value = 0.00;
	document.getElementById("sgst_per" + j + "").value = 0.00;
	document.getElementById("sgst_amt" + j + "").value = 0.00;
	document.getElementById("cgst_per" + j + "").value = 0.00;
	document.getElementById("cgst_amt" + j + "").value = 0.00;
	document.getElementById("rowdiscount" + j + "").value = 0.00;
	value = document.getElementById("value" + j + "").value;
	document.getElementById("rowdiscount_val" + j + "").value = value;
	// qty = document.getElementById("bill_qty" + j + "").value ;
	
	// total = qty * value;
	document.getElementById("total_val" + j + "").value = value;
	//var sum+= total	;
	sum_qty += parseFloat(value);
	}
	document.getElementById("grand_total").value = sum_qty;
	document.getElementById("total_igst").value = 0.00;
	document.getElementById("total_sgst").value = 0.00;
	document.getElementById("total_cgst").value = 0.00;
	document.getElementById("total_discount").value = 0.00;
	document.getElementById("doctype").value = "DC";
	
	}
      else {
	  location.reload();
	
	 } 	

	
	
	
	}
		
            /////// calculate line total /////////////
            function rowTotal(ind) {

                var ent_qty = "bill_qty" + ind + "";
                var ent_rate = "price" + ind + "";
                var ent_value = "value" + ind + "";
                var hold_rate = "holdRate" + ind + "";
                var po_price = "poprice" + ind + "";
                var availableQty = "avl_stock" + ind + "";
                var prodmrpField = "mrp" + ind + "";
                var discountField = "rowdiscount" + ind + "";
                var ent_rowdisval = "rowdiscount_val" + ind + "";
                var sgst_per = "sgst_per" + ind + "";
                var cgst_per = "cgst_per" + ind + "";
                var igst_per = "igst_per" + ind + "";
                var sgst_amt = "sgst_amt" + ind + "";
                var cgst_amt = "cgst_amt" + ind + "";
                var igst_amt = "igst_amt" + ind + "";
                var rowtax = "taxType" + ind + "";
                var totalvalField = "total_val" + ind + "";
                var holdRate = document.getElementById(hold_rate).value;
				
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
                /////  check if entered value is somthing
                if (document.getElementById(ent_value).value) {
                    var value = document.getElementById(ent_value).value;
                } else {
                    var value = 0.00;
                }
                ///// check if discount value is something
                if (document.getElementById(discountField).value) {
                    var dicountval = document.getElementById(discountField).value;
                } else {
                    var dicountval = 0.00;
                }
                /////  check if entered price is somthing
                if (document.getElementById(sgst_per).value) {
                    var sgst = document.getElementById(sgst_per).value;
                } else {
                    var sgst = 0.00;
                }
                /////  check if entered price is somthing
                if (document.getElementById(cgst_per).value) {
                    var cgst = document.getElementById(cgst_per).value;
                } else {
                    var cgst = 0.00;
                }
                ///// check if discount value is something
                if (document.getElementById(igst_per).value) {
                    var igst = document.getElementById(igst_per).value;
                } else {
                    var igst = 0.00;
                }

                ///// check if row wise tax is something
                //  if(document.getElementById(rowtax).value){ var expldtax=(document.getElementById(rowtax).value).split("~"); var rowtaxval=expldtax[0];}else{ var rowtaxval=0.00; }
                ////// check entered qty should be available
                if (parseFloat(qty) <= parseFloat(document.getElementById(availableQty).value)) {
                    if (parseFloat(price) >= parseFloat(dicountval)) {
                        var value = parseFloat(qty) * parseFloat(price);
                        var discost = parseFloat(value) -  (parseFloat(qty) * parseFloat(dicountval));
                        var sgst_amt1 = discost * sgst / 100;
                        var cgst_amt1 = discost * cgst / 100;
                        var igst_amt1 = discost * igst / 100;
                        var linetot = parseFloat(discost + sgst_amt1 + cgst_amt1 + igst_amt1);
                        //var var3="linetotal"+ind+"";    
                        //document.getElementById(var3).value=formatCurrency(total);
                        document.getElementById(ent_value).value = formatCurrency(value);
                        document.getElementById(ent_rowdisval).value = formatCurrency(discost);
                        document.getElementById(sgst_amt).value = formatCurrency(sgst_amt1);
                        document.getElementById(cgst_amt).value = formatCurrency(cgst_amt1);
                        document.getElementById(igst_amt).value = formatCurrency(igst_amt1);
                        document.getElementById(totalvalField).value = formatCurrency(linetot);
                        ///// calculate row wise tax
                        // var taxamount=(parseFloat(totalcost)*parseFloat(rowtaxval))/100;
                        // document.getElementById("rowtaxamount"+ind).value=formatCurrency(taxamount);
                        ///// line total
                        // document.getElementById(totalvalField).value=formatCurrency(parseFloat(totalcost)+parseFloat(taxamount));
                        calculatetotal();
					
                    } else {
                        alert("Discount is exceeding from price");
                        var total = parseFloat(qty) * parseFloat(price);
                        var var3 = "linetotal" + ind + "";
                        document.getElementById(var3).value = formatCurrency(total);
                        document.getElementById(discountField).value = "0.00";
                        document.getElementById(totalvalField).value = formatCurrency(total);
                        calculatetotal();
                    }
                } else if (parseFloat(document.getElementById(availableQty).value) == '0.00') {
                    alert("Stock is not Available");
                    document.getElementById(ent_qty).value = "";
                    //document.getElementById(availableQty).value="";
                    document.getElementById(ent_rate).value = document.getElementById(po_price).value;
                }
                else {
                    alert("Stock is not Available");
                    document.getElementById(ent_qty).value = "";
                    //document.getElementById(availableQty).value="";
                    document.getElementById(ent_rate).value = document.getElementById(po_price).value;
                }
            }
            ////// calculate final value of form /////
            function calculatetotal() {			
                var rowno = (document.getElementById("rowno").value);
                var sum_qty = 0;
                var sum_subtot = 0;
                var sum_discount = 0;
                var sum_grandtot = 0;
                var temp_value = 0.00;
                var sum_discount = 0.00;
                var temp_total_val = 0.00;
				
				var sum_cgstamt = 0.00;
				var sum_sgstamt = 0.00;
				var sum_igstamt = 0.00;
				
                for (var i = 1; i < rowno; i++) {
                    var temp_qty = "bill_qty" + i + "";
                    var temp_value = "value" + i + "";
                    var temp_discount = "rowdiscount" + i + "";
                    var temp_total_val = "total_val" + i + "";
                    var discountvar = 0.00;
                    var totalamt = 0.00;
                    var totalvalue = 0.00;
                    var totqty = 0;
                    ///// check if discount value is something
                    if (document.getElementById(temp_discount).value) {
                        discountvar = document.getElementById(temp_discount).value;
                    } else {
                        discountvar = 0.00;
                    }
                    ///// check if line total value is something
                    if (document.getElementById(temp_value).value) {
                        totalvalue = document.getElementById(temp_value).value;
                    } else {
                        totalvalue = 0.00;
                    }
					////// calculate sub total with discount value
					var valAfterDisc = ((parseInt(document.getElementById("bill_qty"+i).value) * parseFloat(document.getElementById("price"+i).value)) - (parseInt(document.getElementById("bill_qty"+i).value) * parseFloat(discountvar)));
					var cgstamount = (parseFloat(document.getElementById("cgst_per"+i).value) * parseFloat(valAfterDisc))/100;
					var sgstamount = (parseFloat(document.getElementById("sgst_per"+i).value) * parseFloat(valAfterDisc))/100;
					var igstamount = (parseFloat(document.getElementById("igst_per"+i).value) * parseFloat(valAfterDisc))/100; 
					
                    ///// check if line qty is something
                    if (document.getElementById(temp_qty).value) {
                        totqty = document.getElementById(temp_qty).value;
                    } else {
                        totqty = 0;
                    }
                    ///// check if line tax value is something
                    if (document.getElementById(temp_total_val).value) {
                        totalamt = document.getElementById(temp_total_val).value;
                    } else {
                        totalamt = 0.00;
                    }

                    sum_qty += parseFloat(totqty);
                    sum_subtot += parseFloat(totalvalue);
					sum_discount += (parseFloat(discountvar) * parseInt(document.getElementById("bill_qty"+i).value));
					
					sum_cgstamt += parseFloat(cgstamount);
					sum_sgstamt += parseFloat(sgstamount);
					sum_igstamt += parseFloat(igstamount);
		
                    sum_grandtot += parseFloat(totalamt);

                }/// close for loop
				document.getElementById("qtyto").value = (sum_qty);
   				document.getElementById("total_qty").value = (sum_qty);
			    getToQty();///// calculate total qty
				
                document.getElementById("sub_total").value = formatCurrency(sum_subtot);
                document.getElementById("total_discount").value = formatCurrency(sum_discount);
				document.getElementById("total_sgst").value = formatCurrency(sum_sgstamt);
				document.getElementById("total_cgst").value = formatCurrency(sum_cgstamt);
				document.getElementById("total_igst").value = formatCurrency(sum_igstamt);
                document.getElementById("grand_total").value = formatCurrency(sum_grandtot);
				////// check if TCS is applicable or not
				var tcs = document.getElementById("tcs_per").value;
				if(tcs){
					var ft = (sum_grandtot * parseFloat(tcs))/100;
					document.getElementById("tcs_amt").value=(ft).toFixed(2);
					var ftwro = (sum_grandtot+ft).toFixed(2);
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
					var ftwro = sum_grandtot.toFixed(2);
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
            ///// check total tax of all selling product
            function check_total_tax() {
                if (document.getElementById("complete_tax").value) {
                    var splittax = (document.getElementById("complete_tax").value).split("~");
                    var completeTax = splittax[0];
                } else {
                    var completeTax = 0.00;
                }
                var calculateTax = (parseFloat(completeTax) * (parseFloat(document.getElementById("sub_total").value) - parseFloat(document.getElementById("total_discount").value))) / 100;
                document.getElementById("tax_amount").value = formatCurrency(calculateTax);
                document.getElementById("grand_total").value = formatCurrency(parseFloat(document.getElementById("sub_total").value) - parseFloat(document.getElementById("total_discount").value) + parseFloat(calculateTax));
            }
			function getShipToInfo(val){
				var shipto = val.split("~");
				$("#shipto_gstin").val(shipto[3]);
				$("#shipto_city").val(shipto[2]);
				$("#delivery_address").val(shipto[1]);
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
                    <h2 align="center"><i class="fa fa-user-circle-o"></i> Sale Invoicing against PO</h2><br/>
                    <div class="panel-group">
                        <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
                            <div class="panel panel-info table-responsive">
                                <div class="panel-heading heading1">Party Information</div>
                                <div class="panel-body">
                                    <table class="table table-bordered" width="100%">
                                        <tbody>
                                            <tr>
                                            	<td width="20%"><label class="control-label">Purchase Order From</label></td>
                                                <td width="30%"><?php echo $childlocdet[0]."<br/>".$childlocdet[3]."<br/>".$childlocdet[1].",".$childlocdet[2]."-".$childlocdet[6]."<br/>".$childlocdet[7].",".$childlocdet[8]."<br/>".$childlocdet[5]; ?></td>
                                                <td width="20%"><label class="control-label">Purchase Order To</label></td>
                                                <td width="30%"><?php echo $parentlocdet[0]."<br/>".$parentlocdet[3]."<br/>".$parentlocdet[1].",".$parentlocdet[2]."-".$parentlocdet[7]."<br/>".$parentlocdet[8].",".$parentlocdet[9]."<br/>".$parentlocdet[6]; ?></td>
                                                
                                            </tr>
                                            <tr>
                                                <td><label class="control-label">Purchase Order No.</label></td>
                                                <td><?php echo $po_row['po_no']; ?></td>
                                                <td><label class="control-label">Purchase Order Date</label></td>
                                                <td><?php echo $po_row['requested_date']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><label class="control-label">Entry By</label></td>
                                                <td><?php echo getAdminDetails($po_row['create_by'], "name", $link1); ?></td>
                                                <td><label class="control-label">Status</label></td>
                                                <td><?php echo $po_row['status']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><label class="control-label">Discount Type</label></td>
                                                <td><?php echo getDiscountType($po_row['discount_type']); ?></td>
                                            
                                                <td><label class="control-label">Document Type</label></td>
              								  	<td><select name="doc_type" id="doc_type" class="form-control" onChange="getChallan(this.value);">
											 <option value="" >Invoice</option>
                     							<option value="DC"<?php if($_REQUEST['doc_type']=="DC"){ echo "selected";}?>>Delivery Challan</option>
                    								</select></td>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label class="control-label">Cost Centre(Godown)<span style="color:#F00">*</span></label></td>
              								  	<td><select name="stock_from" id="stock_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                                            <option value="" selected="selected">Please Select </option>
                                             <?php                                 
											$smfm_sql = "SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code='".$po_row['po_to']."'";
											$smfm_res = mysqli_query($link1,$smfm_sql);
											while($smfm_row = mysqli_fetch_array($smfm_res)){
											?>
											<option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['stock_from'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
											<?php
											}
											?>
											<?php                                 
											$smf_sql = "SELECT sub_location, sub_location_name FROM sub_location_master WHERE main_location='".$po_row['po_to']."' AND status='Active'";
											$smf_res = mysqli_query($link1,$smf_sql);
											while($smf_row = mysqli_fetch_array($smf_res)){
											?>
											<option value="<?=$smf_row['sub_location']?>" <?php if($smf_row['sub_location']==$_REQUEST['stock_from'])echo "selected";?>><?=$smf_row['sub_location_name']." | ".$smf_row['sub_location']?></option>
											<?php
											}
											?>
                                        </select></td>
                                                </td>
                                                <td id="lg1"><label class="control-label">Ledger Name</label></td>
                                                <td><select name="ledger_name" id="ledger_name" class="form-control" onChange="document.frm1.submit();" <?php if($_REQUEST['doc_type']=="DC"){}else{?> disabled<?php }?>>
                                        	<option value=""<?php if($_REQUEST["ledger_name"]==""){ echo "selected";}?>>--Please Select--</option>
											<option value="Advance Warranty Replacement"<?php if($_REQUEST["ledger_name"]=="Advance Warranty Replacement"){ echo "selected";}?>>Advance Warranty Replacement</option>
											<option value="Warranty Replacement"<?php if($_REQUEST["ledger_name"]=="Warranty Replacement"){ echo "selected";}?>>Warranty Replacement</option>
                                            <option value="Sample & Testing Purpose"<?php if($_REQUEST["ledger_name"]=="Sample & Testing Purpose"){ echo "selected";}?>>Sample & Testing Purpose</option>
                                            <option value="Stock Transfer Within State GST"<?php if($_REQUEST["ledger_name"]=="Stock Transfer Within State GST"){ echo "selected";}?>>Stock Transfer Within State GST</option>
                                            <option value="POP Material"<?php if($_REQUEST["ledger_name"]=="POP Material"){ echo "selected";}?>>POP Material</option>
                                            <option value="Business Promotion"<?php if($_REQUEST["ledger_name"]=="Business Promotion"){ echo "selected";}?>>Business Promotion</option>
                                     	</select></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><!--close panel body-->
                            </div><!--close panel-->
                            <br><br>
                        </form>
                        <form id="frm2" name="frm2" class="form-horizontal" action="" method="post"><br>
                            <div class="panel panel-info table-responsive">
                                <div class="panel-heading heading1" style="width: 111%;">Items Information</div>
                                <div class="panel-body">
                                    <table class="table table-bordered" width="100%" id="tbl_scheam">
                                        <thead>
                                            <tr class="<?=$tableheadcolor?>">
                                                <!---<th style="text-align:center" width="5%">#</th>--------->
                                                <th style="text-align:center" width="15%">Product</th>
                                                <th style="text-align:center" width="10%">Req. Qty</th>
                                                <th style="text-align:center" width="10%">Bill Qty</th>
                                                <th style="text-align:center" width="10%">Price</th>
                                                <th style="text-align:center" width="10%">Value</th>
                                                <th style="text-align:center" width="10%">Discount/Unit</th>
                                                <th style="text-align:center" width="10%">After Discount Value</th>
                                                <th style="text-align:center" width="10%">SGST<br>(%)</th>
                                                <th style="text-align:center" width="10%">SGST <br>Amt</th>
                                                <th style="text-align:center" width="10%">CGST<br>(%)</th>
                                                <th style="text-align:center" width="10%">CGST <br>Amt</th>
                                                <th style="text-align:center" width="10%">IGST<br>(%)</th>
                                                <th style="text-align:center" width="10%">IGST <br>Amt</th>
                                                <th style="text-align:center" width="10%">Total</th>
                                                <th style="text-align:center" width="10%">Scheme</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            $totqty = 0;
                                            $sgst_per = 0;
                                            $cgst_per = 0;
                                            $igst_per = 0;
                                            $sgst_amt = 0;
                                            $cgst_amt = 0;
                                            $igst_amt = 0;
                                            $totsgst = 0;
                                            $totcgst = 0;
                                            $totigst = 0;
                                            $totdiscount = 0;
                                            $subtotal = 0;
                                            $grandtot = 0;
                                            $podata_sql = "SELECT * FROM purchase_order_data where po_no='" . $docid . "'";
                                            $podata_res = mysqli_query($link1, $podata_sql);
                                            while ($podata_row = mysqli_fetch_assoc($podata_res)) {
												
                                                //// get current available stock
                                                $hsn_code = mysqli_fetch_assoc(mysqli_query($link1, "select hsn_code,productname,productcolor from product_master where productcode='" . $podata_row['prod_code'] . "'"));
                                                $gst_data = mysqli_fetch_assoc(mysqli_query($link1, "select sgst,cgst,igst from tax_hsn_master where hsn_code='" . $hsn_code['hsn_code'] . "'"));
                                              
                                                if ($childlocdet['2'] == $parentlocdet['2']) {
                                                    $sgst_per = $gst_data['sgst'];
                                                    $cgst_per = $gst_data['cgst'];
                                                    $sgst_amt = number_format((($podata_row['totalval'] * $sgst_per) / 100), '2', '.', '');
                                                    $cgst_amt = number_format((($podata_row['totalval'] * $cgst_per) / 100), '2', '.', '');
                                                    $linetotal = $podata_row['totalval'] + $sgst_amt + $cgst_amt;
                                                } else {
                                                    $igst_per = $gst_data['igst'];
                                                    $igst_amt = number_format((($podata_row['totalval'] * $igst_per) / 100), '2', '.', '');
                                                    $linetotal = $podata_row['totalval'] + $igst_amt;
                                                }
                                                $discount_val = number_format(($podata_row['po_value'] - ($podata_row['req_qty'] * $podata_row['discount'])), '2', '.', '');
                                                $avlstock = getCurrentStockNew($po_row['po_to'], $_REQUEST['stock_from'], $podata_row['prod_code'], "okqty", $link1);
                                                ?>
                                                <tr>
                                                    <!----<td><?= $i ?></td>---->
                                                    <td><?=$hsn_code['productname'].' | '.$hsn_code['productcolor'].' | '.$podata_row['prod_code']?><input type="hidden" name="prodcode<?= $podata_row['id'] ?>" id="prodcode<?= $i ?>" value="<?= $podata_row['prod_code'] ?>"></td>
                                                    <td style="text-align:right">
                                                        <input type="hidden" name="avl_stock<?= $podata_row['id'] ?>" id="avl_stock<?= $i ?>" value="<?= $avlstock ?>">
                                                        <input name="mrp<?= $podata_row['id'] ?>" id="mrp<?= $i ?>" type="hidden" value="<?= $podata_row['mrp'] ?>"/>
                                                        <input name="holdRate<?= $podata_row['id'] ?>" id="holdRate<?= $i ?>" type="hidden" value="<?= $podata_row['hold_price'] ?>"/>
                                                        <input type="hidden" name="req_qty<?= $podata_row['id'] ?>" id="req_qty<?= $i ?>" value="<?= $podata_row['req_qty'] ?>">
                                                        <?= $podata_row['req_qty'] ?>
                                                    </td>
                                                    <td><input type="text" class="form-control digits" name="bill_qty<?= $podata_row['id'] ?>" id="bill_qty<?= $i ?>" value="<?=(int)$podata_row['req_qty']?>" autocomplete="off" required onBlur="myFunction(this.value, 'none', 'bill_qty<?= $i ?>');rowTotal('<?= $i ?>');" onKeyPress="return onlyNumbers(this.value);" style="width:45px;text-align:right;padding: 5px;"></td>
                                                    <td><input type="text" class="form-control" name="price<?= $podata_row['id'] ?>" id="price<?= $i ?>" onBlur="rowTotal('<?= $i ?>');" autocomplete="off" onKeyPress="return onlyFloatNum(this.value);" required value="<?= $podata_row['po_price'] ?>" style="width:72px;text-align:right;padding: 5px;"><input name="poprice<?= $podata_row['id'] ?>" id="poprice<?= $i ?>" type="hidden" value="<?= $podata_row['po_price'] ?>"/></td>
                                                    <td><input type="text" class="form-control" name="value<?= $podata_row['id'] ?>" id="value<?= $i ?>" autocomplete="off" readonly value="<?= $podata_row['po_value'] ?>" style="width:72px;text-align:right;padding: 5px;"></td>
                                                    <td><input type="text" class="form-control" name="rowdiscount<?= $podata_row['id'] ?>" id="rowdiscount<?= $i ?>" onKeyPress="return onlyFloatNum(this.value);" autocomplete="off"  onblur="rowTotal('<?= $i ?>');" value="<?= $podata_row['discount'] ?>" style="width:72px;text-align:right;padding: 5px;"></td>
                                                    <td><input type="text" class="form-control" name="rowdiscount_val<?= $podata_row['id'] ?>" id="rowdiscount_val<?= $i ?>" autocomplete="off" readonly value="<?= $discount_val ?>" style="width:72px;text-align:right;padding: 5px;"></td>
                                                    <td><input type="text" class="form-control" name="sgst_per<?= $podata_row['id'] ?>" id="sgst_per<?= $i ?>" readonly value="<?= $sgst_per ?>" style="width:50px;text-align:right;padding: 5px;"></td>
                                                    <td><input type="text" class="form-control" name="sgst_amt<?= $podata_row['id'] ?>" id="sgst_amt<?= $i ?>" readonly value="<?= $sgst_amt ?>" style="width:65px;text-align:right;padding: 5px;"></td>
                                                    <td><input type="text" class="form-control" name="cgst_per<?= $podata_row['id'] ?>" id="cgst_per<?= $i ?>" readonly value="<?= $cgst_per ?>" style="width:50px;text-align:right;padding: 5px;"></td>
                                                    <td><input type="text" class="form-control" name="cgst_amt<?= $podata_row['id'] ?>" id="cgst_amt<?= $i ?>" readonly value="<?= $cgst_amt ?>" style="width:65px;text-align:right;padding: 5px;"></td>
                                                    <td><input type="text" class="form-control" name="igst_per<?= $podata_row['id'] ?>" id="igst_per<?= $i ?>" readonly value="<?= $igst_per ?>" style="width:50px;text-align:right;padding: 5px;"></td>
                                                    <td><input type="text" class="form-control" name="igst_amt<?= $podata_row['id'] ?>" id="igst_amt<?= $i ?>" readonly value="<?= $igst_amt ?>" style="width:65px;text-align:right;padding: 5px;"></td>
                                                    <td><input type="text" class="form-control" name="total_val<?= $podata_row['id'] ?>" id="total_val<?= $i ?>" autocomplete="off" readonly value="<?= number_format($linetotal, '2', '.', '') ?>" style="width:75px;text-align:right;padding: 5px;"></td>
                                                    <td align="center">
                                                    <a href="#" onClick="checkScheme('<?=$podata_row['prod_code']?>','<?=$i?>','<?=$podata_row['po_value']?>','<?=$hsn_code['productname'].' | '.$hsn_code['productcolor'].' | '.$podata_row['prod_code']?>');" id="scheme<?=$i?>" title='Applicable Schemes'><i class='fa fa-tags fa-lg'></i></a>
                                                    <input type="hidden"  name="sch_cd<?= $podata_row['id'] ?>"  id="sch_cd<?=$i?>" value="" /></td>
                                                </tr>
                                                <?php
                                                $totqty+=$podata_row['req_qty'];
                                                $subtotal+=$podata_row['po_value'];                                                
                                                $grandtot+=$linetotal;
                                                $totsgst+=$sgst_amt;
                                                $totcgst+=$cgst_amt;
                                                $totigst+=$igst_amt;
                                                $totdiscount+=($podata_row['discount']*$podata_row['req_qty']);
                                                $i++;
                                            }
											$grand_total=$grandtot; 
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
                                                                                       
                                                                                       
											<input type="hidden"  id="count" name="count" value="<?=$i;?>">
                                            <input type="hidden"  id="norow" name="norow" value="0">
                                            <input type="hidden"  id="qtyto" name="qtyto" value="<?=$totqty?>">
                                                                                       
                                        </tbody>
                                    </table>
                                </div><!--close panel body-->
                            </div><!--close panel-->
                            <br><br>
                            <div class="panel panel-info table-responsive">
                                <div class="panel-heading heading1">Amount Information</div>
                                <div class="panel-body">
                                    <table class="table table-bordered" width="100%">
                                        <tbody>
                                            <tr>
                                                <td width="20%"><label class="control-label">Total Qty</label></td>
                                                <td width="30%"><input type="text" name="total_qty" id="total_qty" class="form-control" value="<?=$totqty;?>" readonly style="width:200px;"/> <input type="hidden" name="total_qty1" id="total_qty1" value="0" /> </td>
                                                <td width="20%"><label class="control-label">Sub Total</label></td>
                                                <td width="30%"><input type="text" name="sub_total" id="sub_total" class="form-control" value="<?= currencyFormat($subtotal) ?>" readonly style="width:200px;text-align:right"/></td>
                                            </tr>
                                            <tr>
                                                <td rowspan="2"><label class="control-label">Delivery Address</label></td>
                                                <td rowspan="2"><textarea name="delivery_address" id="delivery_address" class="form-control required" rows="5"  style="resize:none; width:200px" required><?php echo $po_row['delivery_address']; ?></textarea></td>
                                                <td><label class="control-label">Discount</label></td>
                                                <td><input type="text" name="total_discount" id="total_discount" class="form-control" value="<?=$totdiscount?>" style="width:200px;text-align:right" onKeyPress="return onlyFloatNum(this.value);" onKeyUp="check_total_tax();" readonly/></td>
                                            </tr>
                                            <tr>
                                              <td><label class="control-label">Total SGST</label></td>
                                              <td><input type="text" name="total_sgst" id="total_sgst" class="form-control" value="<?= $totsgst ?>" readonly style="width:200px;text-align:right"/></td>
                                            </tr>
                                            <tr>
                                              <td height="56"><label class="control-label">Ship To</label></td>
                                              <td>
												  <select name="ship_to" id="ship_to" <?php if(substr($po_row['po_from'],0,4)!="EASR"){?>required<?php }?> class="form-control selectpicker <?php if(substr($po_row['po_from'],0,4)!="EASR"){?>required<?php }?>" data-live-search="true" style="width:200px;" onChange="getShipToInfo(this.value);">
												  
                                            <option value="" selected="selected">Please Select </option>
                                            <?php
                                            $sql_shipto = "SELECT * FROM delivery_address_master WHERE location_code='".$po_row['po_from']."' AND status='Active'";
                                            $res_shipto = mysqli_query($link1, $sql_shipto);
                                            while($row_shipto = mysqli_fetch_array($res_shipto)) {
											?>
                                            <option value="<?=$row_shipto['address_code']."~".$row_shipto['address']."~".$row_shipto['city']."~".$row_shipto['gstin']?>"><?=$row_shipto['party_name']." | ".$row_shipto['address']?></option>
                                            <?php
                                            }
                                            ?>
                                        </select></td>
                                              <td><label class="control-label">Total CGST</label></td>
                                              <td><input type="text" name="total_cgst" id="total_cgst" class="form-control" value="<?= $totcgst ?>" readonly style="width:200px;text-align:right"/></td>
                                            </tr>
                                            <tr>
                                              <td><label class="control-label">Ship To GSTIN</label></td>
                                              <td><input type="text" name="shipto_gstin" id="shipto_gstin" class="form-control alphanumeric" minlength="15" maxlength="15" style="width:200px;"></td>
                                              <td><label class="control-label">Total IGST</label></td>
                                              <td><input type="text" name="total_igst" id="total_igst" class="form-control" value="<?= $totigst ?>" readonly style="width:200px;text-align:right"/></td>
                                            </tr>
                                            <tr>
                                              <td><label class="control-label">Ship To City</label></td>
                                              <td><input type="text" name="shipto_city" id="shipto_city" class="form-control mastername" style="width:200px;"></td>
                                                <td><label class="control-label">Grand Total</label></td>
                                                <td><input type="text" name="grand_total" id="grand_total" class="form-control" value="<?php echo currencyFormat($grandtot); ?>" readonly style="width:200px;text-align:right"/></td>
                                            </tr>
                                            <tr>
					 <td><label class="control-label">TCS</label></td>
					 <td>
                     	<select name="tcs_per" id="tcs_per" class="form-control" onChange="calculatetotal();" style="width:200px">
                        	<option value="">--Please Select--</option>
							<?php if($childlocdet[9]=="Y"){ ?>
                            <option value="<?=$childlocdet[10]?>"><?=$childlocdet[10]?> %</option>
                            <?php }else{?>
                            <option value="0.1">0.1 %</option>
                            <?php }?>
                     	</select></td>
					 <td><label class="control-label">TCS Amount</label></td>
					 <td><input type="text" name="tcs_amt" id="tcs_amt" class="form-control" value="0.00" style="width:200px;text-align:right" readonly/></td>
				   </tr>
                   <tr>
					 <td><label class="control-label">Round Off</label></td>
					 <td><input type="text" name="round_off" id="round_off" class="form-control" value="<?=$roundoff?>" style="width:200px;text-align:right" readonly/></td>
					 <td><label class="control-label">Final Total</label></td>
					 <td><input type="text" name="final_total" id="final_total" class="form-control" value="<?=$roundoff+$grandtot?>" style="width:200px;text-align:right" readonly/></td>
				   </tr>
                   <tr>
                     <td><label class="control-label">Payment Term</label></td>
                     <td><textarea name="payment_term" id="payment_term" class="form-control required" rows="3"  style="resize:none; width:200px" required><?php echo $po_row['payment_status']; ?></textarea></td>
                     <td><label class="control-label">Remark</label></td>
                     <td><textarea name="remark" id="remark" class="form-control" style="resize:none;width:200px" rows="5" ></textarea></td>
                   </tr>
                                            <tr>
                                                <td colspan="4" align="center"><input type="submit" class="btn <?=$btncolor?>" name="upd" id="upd" value="Process" title="Make Invoice">
                                                    <input type="hidden" name="doctype" id="doctype" value="<?= $_REQUEST['doc_type'] ?>"/>
                                                    <input type="hidden" name="ledgername" id="ledgername" value="<?= $_REQUEST['ledger_name'] ?>"/>
                                                    <input type="hidden" name="stockfrom" id="stockfrom" value="<?= base64_encode($_REQUEST['stock_from'])?>"/>
                                                    <input type="hidden" name="id" id="id" value="<?= $_REQUEST['id'] ?>"/>
                                                    <input type="hidden" name="rowno" id="rowno" value="<?= $i ?>"/>
                                                    <input type="hidden" name="disc_amt" id="disc_amt" value="<?=$totdisc."~".$row_Nom."~".$res_schm['scheme_given_type'];?>"/>
                                                    <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href = 'corporateInvoice.php?<?= $pagenav ?>'"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                              </div><!--close panel body-->
                            </div><!--close panel-->
                            <br><br>
                        </form>
                    </div><!--close panel group-->
                </div><!--close col-sm-9-->
            </div><!--close row content-->
        </div><!--close container-fluid-->
        <br>
        <form id="frm3" name="frm3" class="form-horizontal" action="" method="post">
            <div class="modal modalTH fade" id="viewModal" role="dialog">
                <div class="modal-dialog modal-dialogTH modal-lg">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h2 class="modal-title" align="center" id="tile_name"></h2>
                        </div>
                        <div class="modal-body modal-bodyTH">
                            <!-- here dynamic task details will show -->
                        </div>
                        <div  class="modal-footer">
                         <input type="button" class="btn <?=$btncolor?>" name="savebtn" id="savebtn" value="Apply" title="Apply Scheme" onClick="getSchemeAmut()" data-dismiss="modal" >
                        <button type="button" id="btnCancel" class="btn <?=$btncolor?>" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        include("../includes/footer.php");
        include("../includes/connection_close.php");
        ?>
    </body>
</html>

