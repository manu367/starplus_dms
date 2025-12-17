<?php
require_once("../config/config.php");
@extract($_POST);
if($_POST){
	if ($_POST['upd']=='Process'){
		///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
		$messageIdent = md5($_POST['upd'] . $inv);
		//and check it against the stored value:
		$sessionMessageIdent = isset($_SESSION['msgVendRtn'])?$_SESSION['msgVendRtn']:'';
		if($messageIdent!=$sessionMessageIdent){//if its different:
			//save the session var:
			$_SESSION['msgVendRtn'] = $messageIdent;
  			if($tot_qty!='' && $tot_qty!=0){
				///// initialize variable
				mysqli_autocommit($link1, false);
				$flag = true;
				$err_msg = "";
				////// get master details
				$res_invmaster = mysqli_query($link1,"SELECT * FROM billing_master WHERE challan_no='".$inv."' AND from_location='".$partycode."' AND to_location='".$parentcode."' AND sub_location='".$stockfrom."'");
				if(mysqli_num_rows($res_invmaster)>0){
					$row_invmaster = mysqli_fetch_assoc($res_invmaster);
					/////// make debit note against this
					$res_dnt = mysqli_query($link1, "SELECT dn_str, dn_counter FROM document_counter WHERE location_code='" . $parentcode . "'");
					if(mysqli_num_rows($res_dnt)>0){
						$row_dnt = mysqli_fetch_array($res_dnt);
						$invdnt = $row_dnt['dn_counter'] + 1;
						$pad = $invdnt;
						$mobiCode = $row_dnt['dn_str'].$pad;
						//// Make System generated ref no.//////
						$res_po = mysqli_query($link1,"SELECT MAX(dc_temp) AS no FROM billing_master where from_location = '".$parentcode."' and type='VRN'");
						$row_po = mysqli_fetch_array($res_po);
						$c_nos = $row_po['no']+1;
						$doc_no = "VRN/".$fy."/".$parentcode."/".$c_nos;
						/////
						if ($delivery_address) {
							$deli_addrs = $delivery_address;
						} else {
							$deli_addrs = $row_invmaster["deliv_addrs"];
						}
						///// Insert Master Data
						$query1 = "INSERT INTO billing_master SET 
						from_location='" . $parentcode . "',
						to_location='" . $partycode . "',
						sub_location='".$stockfrom."',
						from_gst_no='".$row_invmaster["from_gst_no"]."',
						from_partyname='".$row_invmaster["from_partyname"]."',
						party_name='".$row_invmaster["party_name"]."',
						to_gst_no='".$row_invmaster["to_gst_no"]."',
						challan_no='" . $doc_no . "',
						dc_temp = '".$c_nos."',
						ref_no = '".$row_invmaster["challan_no"]."',
						ref_date = '".$row_invmaster["sale_date"]."',
						sale_date='" . $today . "',
						entry_date='" . $today . "',
						entry_time='" . $currtime . "',
						entry_by='" . $_SESSION['userid'] . "',
						status='Pending',
						type='VRN',
						document_type='".$row_invmaster["document_type"]."',
						basic_cost='" . $sub_total . "',
						total_sgst_amt='".$total_sgstamt."',
						total_cgst_amt='".$total_cgstamt."',
						total_igst_amt='".$total_igstamt."',
						tax_cost='" . $tax_total . "',
						total_cost='" . $grand_total . "',
						bill_from='" . $parentcode . "',
						bill_topty='" . $partycode . "',
						from_addrs='" . $row_invmaster["from_addrs"] . "',
						disp_addrs='" . $row_invmaster["disp_addrs"] . "',
						to_addrs='" . $row_invmaster["to_addrs"] . "',
						deliv_addrs='" . $deli_addrs . "',
						billing_rmk='" . $remark . "',
						from_state='".$row_invmaster["from_state"]."',
						to_state='".$row_invmaster["to_state"]."',
						from_city='".$row_invmaster["from_city"]."',
						to_city='".$row_invmaster["to_city"]."',
						from_pincode='".$row_invmaster["from_pincode"]."',
						to_pincode='".$row_invmaster["to_pincode"]."',
						from_phone='".$row_invmaster["from_phone"]."',
						to_phone='".$row_invmaster["to_phone"]."',
						from_email='".$row_invmaster["from_email"]."',
						to_email='".$row_invmaster["to_email"]."',
						round_off='".$round_off."',
						tds = '".$tds_194q."',
						tcs_per='".$tcs_per."',
						tcs_amt='".$tcs_amt."',
						ledger_name='".$ledgername."'";
						$result1 = mysqli_query($link1, $query1);
						//// check if query is not executed
						if (!$result1) {
							$flag = false;
							$err_msg = "Error Code1:" . mysqli_error($link1) . ".";
						}
						//////////////create debit note
						$sql_dn= "INSERT INTO debit_note SET 
						cust_id='".$partycode."',
						location_id='".$parentcode."',
						sub_location='".$stockfrom."',
						entered_ref_no='".$row_invmaster["challan_no"]."',
						entered_ref_date='".$row_invmaster["sale_date"]."',
						ref_no='".$mobiCode."',
						sys_ref_temp_no='".$pad."',
						challan_no='".$doc_no."',
						create_by='".$_SESSION['userid']."',
						remark='".$remark."',
						create_date='".$today."',
						amount='".$final_total."',
						status='Pending For Approval',
						create_ip='".$ip."',
						basic_amt = '".$sub_total."',
						round_off='".$round_off."',
						tds = '".$tds_194q."',
						tcs_per='".$tcs_per."',
						tcs_amt='".$tcs_amt."',
						sgst_amt='".$total_sgstamt."',
						cgst_amt='".$total_cgstamt."',
						igst_amt='".$total_igstamt."',
						description='VENDOR RETURN',
						
						from_gst_no='".$row_invmaster["from_gst_no"]."',
						from_partyname='".$row_invmaster["from_partyname"]."',
						party_name='".$row_invmaster["party_name"]."',
						to_gst_no='".$row_invmaster["to_gst_no"]."',
						
						bill_from='" . $parentcode . "',
						bill_topty='" . $partycode . "',
						from_addrs='" . $row_invmaster["from_addrs"] . "',
						disp_addrs='" . $row_invmaster["disp_addrs"] . "',
						to_addrs='" . $row_invmaster["to_addrs"] . "',
						deliv_addrs='" . $deli_addrs . "',
						from_state='".$row_invmaster["from_state"]."',
						to_state='".$row_invmaster["to_state"]."',
						from_city='".$row_invmaster["from_city"]."',
						to_city='".$row_invmaster["to_city"]."',
						from_pincode='".$row_invmaster["from_pincode"]."',
						to_pincode='".$row_invmaster["to_pincode"]."',
						from_phone='".$row_invmaster["from_phone"]."',
						to_phone='".$row_invmaster["to_phone"]."',
						from_email='".$row_invmaster["from_email"]."',
						to_email='".$row_invmaster["to_email"]."'";
						$db_add= mysqli_query($link1,$sql_dn);
						//// check if query is not executed
						if (!$db_add) {
							 $flag = false;
							 $err_msg = "Error details1.1: " . mysqli_error($link1) . ".";
						}
						$resultdc = mysqli_query($link1, "UPDATE document_counter SET dn_counter=dn_counter+1, update_by='" . $_SESSION['userid'] . "',updatedate='" . $datetime . "' WHERE location_code='" . $parentcode . "'");
						//// check if query is not executed
						if (!$resultdc) {
							$flag = false;
							$err_msg = "Error Code1.2: ".mysqli_error($link1);
						}
						///// Insert in item data by picking each data row one by one
						foreach($prod_code as $k=>$val)
						{   
							// checking row value of product and qty should not be blank
							$getokstk = getCurrentStockNew($parentcode,$stockfrom, $val, "okqty", $link1);
							$getdmgstk = getCurrentStockNew($parentcode,$stockfrom, $val, "broken", $link1);
							$getmisstk = getCurrentStockNew($parentcode,$stockfrom, $val, "missing", $link1);
							//// check stock should be available ////
							if($getokstk < $ok_qty[$k] || $getdmgstk < $dmg_qty[$k] || $getmisstk < $mis_qty[$k]){ 
							   $flag = false;
							   $err_msg = "Error Code1.3: Stock is not available for ".$val;
							}
							else{}
							// checking row value of product and qty should not be blank
							$totalqty = 0;
							if($prod_code[$k]!='' && (($ok_qty[$k]!='' && $ok_qty[$k]!=0) || ($dmg_qty[$k]!='' && $dmg_qty[$k]!=0) || ($mis_qty[$k]!='' && $mis_qty[$k]!=0)) ) {
								$totalqty = $ok_qty[$k] + $dmg_qty[$k] + $mis_qty[$k];
								/////////// insert data
								$query2 = "INSERT INTO billing_model_data SET 
								from_location='" . $parentcode . "',
								prod_code='" . $val . "',
								qty='" . $totalqty . "',
								okqty='" . $ok_qty[$k] . "',
								damageqty='".$dmg_qty[$k]."',
								missingqty='".$mis_qty[$k]."',
								mrp='" . $mrp[$k] . "',
								price='" . $price[$k] . "',
								hold_price='" . $holdRate[$k] . "',
								value='" . $linetotal[$k] . "',
								totalvalue='" . $total_amt[$k] . "',
								challan_no='" . $doc_no . "' ,
								sale_date='" . $today . "',
								entry_date='" . $today . "' ,
								sgst_per='".$sgst_per[$k]."' ,
								sgst_amt='".$sgst_amt[$k]."',
								igst_per='".$igst_per[$k]."' ,
								igst_amt='".$igst_amt[$k]."',
								cgst_per='".$cgst_per[$k]."' ,
								cgst_amt='".$cgst_amt[$k]."'";
								$result2 = mysqli_query($link1, $query2);
								//// check if query is not executed
								if (!$result2) {
								   $flag = false;
								   $err_msg =  "Error details2: " . mysqli_error($link1) . ".";
								}
								//// update stock of from loaction
								$result3 = mysqli_query($link1, "UPDATE stock_status SET okqty = okqty-'".$ok_qty[$k]."', broken = broken-'".$dmg_qty[$k]."', missing = missing-'".$mis_qty[$k]."', updatedate='".$datetime."' WHERE asc_code='".$parentcode."' AND partcode='".$val."' AND sub_location='".$stockfrom."'");
								//// check if query is not executed
								if (!$result3) {
								   $flag = false;
								   $err_msg =  "Error details3: " . mysqli_error($link1) . ".";
								}
								if($ok_qty[$k]!="" && $ok_qty[$k]!=0 && $ok_qty[$k]!=0.00){
									$flag=stockLedger($doc_no,$today,$val,$parentcode,$partycode,$parentcode,"OUT","OK","Vendor Return",$ok_qty[$k],$price[$k],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
								}
								if($dmg_qty[$k]!="" && $dmg_qty[$k]!=0 && $dmg_qty[$k]!=0.00){
									$flag=stockLedger($doc_no,$today,$val,$parentcode,$partycode,$parentcode,"OUT","DAMAGE","Vendor Return",$dmg_qty[$k],$price[$k],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
								}
								if($mis_qty[$k]!="" && $mis_qty[$k]!=0 && $mis_qty[$k]!=0.00){
									$flag=stockLedger($doc_no,$today,$val,$parentcode,$partycode,$parentcode,"OUT","MISSING","Vendor Return",$mis_qty[$k],$price[$k],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
								}
								/////// debit note data
								$query4 = "INSERT INTO debit_note_data SET prod_code='".$val."',req_qty='".$totalqty."' , price='".$price[$k]."', value='".$linetotal[$k]."' , discount='', totalvalue='".$total_amt[$k]."',ref_no='".$mobiCode."',entry_date='".$today."' ,sgst_per='".$sgst_per[$k]."' ,sgst_amt='".$sgst_amt[$k]."',igst_per='".$igst_per[$k]."' ,igst_amt='".$igst_amt[$k]."',cgst_per='".$cgst_per[$k]."' ,cgst_amt='".$cgst_amt[$k]."'";			
								$result4 = mysqli_query($link1, $query4);
								//// check if query is not executed
								if (!$result4) {
									$flag = false;
									$err_msg = "Error details3.1: " . mysqli_error($link1) . ".";
								}
							}// close if loop of checking row value of product and qty should not be blank
							else{
								
							}
						}/// close for loop
						$upd = mysqli_query($link1,"UPDATE current_cr_status SET cr_abl=cr_abl-'".$grand_total."',total_cr_limit=total_cr_limit-'".$grand_total."', last_updated='".$today."' WHERE parent_code='".$parentcode."' AND asc_code='".$partycode."'");
					   ############# check if query is not executed
						if (!$upd) {
							$flag = false;
							$err_msg = "Error details4: " . mysqli_error($link1) . ".";
						}
						////// maintain party ledger////
						$flag = partyLedger($partycode,$parentcode,$doc_no,$today,$today,$currtime,$_SESSION['userid'],"VRN",$grand_total,"DR",$link1,$flag);
						////// insert in activity table////
						$flag = dailyActivity($_SESSION['userid'],$doc_no,"VRN","ADD",$ip,$link1,$flag);
						$flag = dailyActivity($_SESSION['userid'],$mobiCode,"DEBIT NOTE","ADD",$ip,$link1,$flag);
						if ($flag) {
							mysqli_commit($link1);
							$msg = "Purchase Return is successfully placed with ref. no. ".$doc_no;
						} else {
							mysqli_rollback($link1);
							$msg = "Request could not be processed. Please try again.".$err_msg;
						} 
						mysqli_close($link1);
					}
					else{
						$msg = "Request could not be processed . Debit note series not found.".$err_msg;
					}
				}
				else{
	 				$msg = "Request could not be processed . Invoice details not found.".$err_msg;
				}
			}else{
	 			$msg = "Request could not be processed . Please dispatch some qty.".$err_msg;
			}
		}else {
			//you've sent this already!
			$msg="You have saved this already ";
			$cflag = "warning";
			$cmsg = "Warning";
		}	
     	///// move to parent page
		header("location:vendor_return.php?msg=".$msg."".$pagenav);
   		exit;
	}
}

$res_inv = mysqli_query($link1,"SELECT * FROM billing_master WHERE challan_no='".$_REQUEST['inv_no']."' AND from_location='".$_REQUEST['po_to']."' AND to_location='".$_REQUEST['po_from']."' AND sub_location='".$_REQUEST['stock_from']."'"); 
$row_inv = mysqli_fetch_assoc($res_inv);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=siteTitle?></title>
<script src="../js/jquery.min.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="../css/bootstrap-select.min.css">
<script src="../js/bootstrap-select.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#frm2").validate();
});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
<script type="text/javascript" >
/////////// function to get available stock of ho
function getAvlStk(indx){
	var productCode=document.getElementById("prod_code["+indx+"]").value;
   	var locationCode=$('#po_from').val();
	var aclocationCode = $('#stock_from').val();
   	$.ajax({
    	type:'post',
  		url:'../includes/getAzaxFields.php',
  		data:{getAllTypeLocStk:productCode,loccode:locationCode,godown:aclocationCode,indxx:indx},
  		success:function(data){ 
   			var getdata=data.split("~"); 
         	document.getElementById("avl_okstock["+getdata[1]+"]").value=getdata[0];
			document.getElementById("avl_dmgstock["+getdata[1]+"]").value=getdata[1];
			document.getElementById("avl_misstock["+getdata[1]+"]").value=getdata[2];
     	}
   	});
   	//rowTotal(indx);
 }
//////////////////chk enter qty should not exceed by avl qty ///////
function  check_qty(ind){	
	var avl = "req["+ind+"]";
  	var req1 = "req_qty["+ind+"]";
	var availble = document.getElementById(avl).value;
  	var requested = document.getElementById(req1).value;
  	if(parseFloat(requested) > parseFloat(availble)){  
    	alert("Entered Qty is more than Invoice Qty.");
     	document.getElementById(req1).value="0";
	  	return false;
  	}
  	rowTotal(ind);
}
 /////// calculate line total /////////////
function rowTotal(ind){
  	var req="req["+ind+"]";
  	var ent_qty="req_qty["+ind+"]";
  	var ent_rate="price["+ind+"]";
	var entOkQty="ok_qty["+ind+"]";
	var entDmgQty="dmg_qty["+ind+"]";
	var entMisQty="mis_qty["+ind+"]";
	var availableOkQty="avl_okstock["+ind+"]";
	var availableDmgQty="avl_dmgstock["+ind+"]";
	var availableMisQty="avl_misstock["+ind+"]";
  	var prodCodeField="prod_code["+ind+"]";	
	var rowsgstper = "sgst_per" + "[" + ind + "]";
	var rowcgstper = "cgst_per" + "[" + ind + "]";
	var rowsgstamount = "sgst_amt" + "[" + ind + "]";
	var rowcgstamount = "cgst_amt" + "[" + ind + "]";
	var rowigstper = "igst_per" + "[" + ind + "]";
	var rowigstamount = "igst_amt" + "[" + ind + "]";
	var totalvalField = "total_amt" + "[" + ind + "]";
	/////  check if entered qty is somthing
 	if(document.getElementById(ent_qty).value){ 
		var qty=document.getElementById(ent_qty).value;
 	}else{ 
		var qty=0;
	}
	if(document.getElementById(entOkQty).value){ 
		var ent_okqty=document.getElementById(entOkQty).value;
 	}else{ 
		var ent_okqty=0;
	}
	if(document.getElementById(entDmgQty).value){ 
		var ent_dmgqty=document.getElementById(entDmgQty).value;
 	}else{ 
		var ent_dmgqty=0;
	}
	if(document.getElementById(entMisQty).value){ 
		var ent_misqty=document.getElementById(entMisQty).value;
 	}else{ 
		var ent_misqty=0;
	}
 	/////  check if entered price is somthing
  	if(document.getElementById(ent_rate).value){ 
		var price=document.getElementById(ent_rate).value;
	}else{ 
		var price=0.00;
	}
	var sgstper = 0.00;
	var cgstper = 0.00;
	var igstper = 0.00;
	 //  check if sgst per
	if (document.getElementById(rowsgstper).value) {
		var sgstper = document.getElementById(rowsgstper).value;
	} else {
		var sgstper = 0.00;
	}
	// check if cgst per
	if (document.getElementById(rowcgstper).value) {
		var cgstper = document.getElementById(rowcgstper).value;
	} else {
		var cgstper = 0.00;
	}
	// check if igst per
	if (document.getElementById(rowigstper).value) {
		var igstper = (document.getElementById(rowigstper).value);
	} else {
		var igstper = 0.00;
	}
  	////// check entered qty should be available
  	if(parseFloat(ent_okqty) <= parseFloat(document.getElementById(availableOkQty).value) && parseFloat(ent_dmgqty) <= parseFloat(document.getElementById(availableDmgQty).value)  && parseFloat(ent_misqty) <= parseFloat(document.getElementById(availableMisQty).value)){
	
		var total = (parseFloat(ent_okqty) + parseFloat(ent_dmgqty) + parseFloat(ent_misqty))*parseFloat(price);
     	var totalcost = parseFloat(total);
     	var var3 = "linetotal["+ind+"]";
     	document.getElementById(var3).value = formatCurrency(total);
		var sgst_amt = ((totalcost * sgstper) / 100);
		var cgst_amt = ((totalcost * cgstper) / 100);
		var igst_amt = ((totalcost * igstper) / 100);
		//// calculate row wise discount                
		document.getElementById(rowsgstamount).value = formatCurrency(sgst_amt);
		document.getElementById(rowcgstamount).value = formatCurrency(cgst_amt);
		document.getElementById(rowigstamount).value = formatCurrency(igst_amt);
		var tot = parseFloat(totalcost) + parseFloat(sgst_amt) + parseFloat(cgst_amt) + parseFloat(igst_amt);
		document.getElementById(totalvalField).value = formatCurrency(parseFloat(tot));
     	calculatetotal();
  	}
  	else{
	 	alert("Stock is  not Available");
	  	//document.getElementById(ent_qty).value="";
		document.getElementById(entOkQty).value="";
		document.getElementById(entDmgQty).value="";
		document.getElementById(entMisQty).value="";
	  	//document.getElementById(availableQty).value="";
	  	//document.getElementById(ent_rate).value="";
	  	//document.getElementById(hold_rate).value="";
	 	// document.getElementById(prodCodeField).value="";
	  	//document.getElementById(prodmrpField).value="";
	 	// document.getElementById(prodCodeField).focus();
  	}
}
////// calculate final value of form /////
function calculatetotal(){
	var rowno = document.getElementById("rowno").value;
	var sum_okqty = 0;
	var sum_dmgqty = 0;
	var sum_misqty = 0;
	var sum_total = 0.00;
	var sum_taxable = 0.00;
	var sum_sgst = 0.00;
	var sum_cgst = 0.00;
	var sum_igst = 0.00;
	var sum = 0.00;
	for (var i = 0; i < rowno; i++) {
		var temp_okqty = "ok_qty" + "[" + i + "]";
		var temp_dmgqty = "dmg_qty" + "[" + i + "]";
		var temp_misqty = "mis_qty" + "[" + i + "]";  
		var temp_total = "linetotal" + "[" + i + "]";
		var temp_sgst = "sgst_amt" + "[" + i + "]";					          
		var temp_cgst = "cgst_amt" + "[" + i + "]";
		var temp_igst = "igst_amt" + "[" + i + "]";
		var total_amt = "total_amt" + "[" + i + "]";
		var totalamtvar = 0.00;
		var total = 0.00;
		var  total_taxable = 0.00;
		var  total_sgst = 0.00;
		var  total_cgst = 0.00;
		var  total_igst = 0.00;              
		///// check if line qty is something
		if (document.getElementById(temp_okqty).value) {
			totokqty = document.getElementById(temp_okqty).value;
		} else {
			totokqty = 0;
		}
		if (document.getElementById(temp_dmgqty).value) {
			totdmgqty = document.getElementById(temp_dmgqty).value;
		} else {
			totdmgqty = 0;
		}
		if (document.getElementById(temp_misqty).value) {
			totmisqty = document.getElementById(temp_misqty).value;
		} else {
			totmisqty = 0;
		}  
		///// check if line taxaable amount is something
		if (document.getElementById(temp_total).value) {
			total_taxable = document.getElementById(temp_total).value;
		} else {
			total_taxable = 0.00;
		}

		if (document.getElementById(temp_sgst).value) {
			total_sgst = document.getElementById(temp_sgst).value;
		} else {
			total_sgst = 0.00;
		}
		if (document.getElementById(temp_cgst).value) {
			total_cgst = document.getElementById(temp_cgst).value;
		} else {
			total_cgst = 0.00;
		}

		if (document.getElementById(temp_igst).value) {
			total_igst = document.getElementById(temp_igst).value;
		} else {
			total_igst = 0.00;
		}
		///// check if line total amount is something
		if (document.getElementById(total_amt).value) {
			total = document.getElementById(total_amt).value;
		} else {
			total = 0.00;
		}
		sum_okqty += parseFloat(totokqty);
		sum_dmgqty += parseFloat(totdmgqty);
		sum_misqty += parseFloat(totmisqty);
		sum_total += parseFloat(total);
		sum_taxable += parseFloat(total_taxable);//// total taxable amt
		sum_sgst += parseFloat(total_sgst);
		sum_cgst += parseFloat(total_cgst);
		sum_igst += parseFloat(total_igst);
		//sum += parseFloat(total);
	}/// close for loop
	document.getElementById("tot_qty").value = sum_okqty+sum_dmgqty+sum_misqty;
	document.getElementById("sub_total").value = formatCurrency(sum_taxable);//// total taxable amt
	document.getElementById("tax_total").value = formatCurrency(sum_sgst+sum_cgst+sum_igst);
	document.getElementById("total_sgstamt").value = formatCurrency(sum_sgst);
	document.getElementById("total_cgstamt").value = formatCurrency(sum_cgst);
	document.getElementById("total_igstamt").value = formatCurrency(sum_igst);
	document.getElementById("grand_total").value = formatCurrency(parseFloat(sum_total));
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
////////////////////////////////////////////End //////////////////////////////////////////////
</script>
</head>
<body onKeyPress="return keyPressed(event);">
	<div class="container-fluid">
  		<div class="row content">
			<?php 
            include("../includes/leftnav2.php");
            ?>
    		<div class="col-sm-9">
      			<h2 align="center"><i class="fa fa-reply-all fa-lg"></i> Add New Vendor Return</h2>
      			<div class="form-group"  id="page-wrap" style="margin-left:10px;">
          			<form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          				<div class="form-group">
            				<div class="col-md-10"><label class="col-md-3 control-label">Purhase Return From<span style="color:#F00">*</span></label>
              					<div class="col-md-9">
                                <select name="po_from" id="po_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                                    <option value="" selected="selected">Please Select </option>
                                    <?php 
                                    $sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y' and id_type IN ('HO','BR')";
                                    $res_chl=mysqli_query($link1,$sql_chl);
                                    while($result_chl=mysqli_fetch_array($res_chl)){
                                          $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
                                          //if($party_det['id_type']=='HO'){
                                          ?>
                                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['po_from'])echo "selected";?> >
                                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                                    </option>
                                    <?php
                                          //}
                                    }
                                    ?>
                                </select>
              					</div>
            				</div>
          				</div>
          				<div class="form-group">
            				<div class="col-md-10"><label class="col-md-3 control-label">Purhase Return To<span style="color:#F00">*</span></label>
              					<div class="col-md-9">
                 				<select name="po_to" id="po_to" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                                 <option value="" selected="selected">Please Select </option>
                                    <?php 
                                    $sql_parent="select * from vendor_master where status='active' and id!=''";
                                    $res_parent=mysqli_query($link1,$sql_parent);
                                    while($result_parent=mysqli_fetch_array($res_parent)){
                                          ?>
                                    <option data-tokens="<?=$result_parent['name']." | ".$result_parent['id']?>" value="<?=$result_parent['id']?>" <?php if($result_parent['id']==$_REQUEST['po_to'])echo "selected";?> >
                                       <?=$result_parent['name']." | ".$result_parent['city']." | ".$result_parent['state']." | ".$result_parent['country']?>
                                    </option>
                                    <?php
                                    }
                                    ?>
                                </select>
              					</div>
            				</div>
          				</div>
                        <div class="form-group">
                                <div class="col-md-10"><label class="col-md-3 control-label">Cost Centre(Godown)<span style="color:#F00">*</span></label>
                                    <div class="col-md-9">
                                        <select name="stock_from" id="stock_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                                            <option value="" selected="selected">Please Select </option>
                                             <?php                                 
											$smfm_sql = "SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code='".$_REQUEST['po_from']."'";
											$smfm_res = mysqli_query($link1,$smfm_sql);
											while($smfm_row = mysqli_fetch_array($smfm_res)){
											?>
											<option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['stock_from'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
											<?php
											}
											?>
											<?php                                 
											$smf_sql = "SELECT sub_location, sub_location_name FROM sub_location_master WHERE main_location='".$_REQUEST['po_from']."' AND status='Active'";
											$smf_res = mysqli_query($link1,$smf_sql);
											while($smf_row = mysqli_fetch_array($smf_res)){
											?>
											<option value="<?=$smf_row['sub_location']?>" <?php if($smf_row['sub_location']==$_REQUEST['stock_from'])echo "selected";?>><?=$smf_row['sub_location_name']." | ".$smf_row['sub_location']?></option>
											<?php
											}
											?>
                                        </select>

                                    </div>
                                </div>
                            </div>
                            <?php if($_REQUEST['po_to']!='' && $_REQUEST['po_from']!=''){?>
          				<div class="form-group">
		  <div class="col-md-10">
              <label class="col-md-3 control-label">GRN No.</label>
              <div class="col-md-9">
			 <select name="inv_no" id="inv_no" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                 <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_parent="SELECT challan_no,ref_no,inv_ref_no FROM billing_master WHERE from_location='".$_REQUEST['po_to']."' AND to_location='".$_REQUEST['po_from']."' AND sub_location='".$_REQUEST['stock_from']."' AND type='GRN' AND status='Received'";
					$res_parent=mysqli_query($link1,$sql_parent);
					while($result_parent=mysqli_fetch_array($res_parent)){
	                      ?>
                    <option value="<?=$result_parent['challan_no']?>"<?php if($result_parent['challan_no']==$_REQUEST['inv_no'])echo "selected";?>>
                       <?=$result_parent['challan_no']." | ".$result_parent['ref_no']." | ".$result_parent['inv_ref_no']?>
                    </option>
                     <?php
					}
                    ?>
                 </select>
              </div>
          </div>
        </div>
        <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Document Type</label>
                <div class="col-md-9">
                	<input type="text" class="form-control" name="doc_type" value="<?=$row_inv["document_type"]?>" readonly/>
                </div>
             </div>
          </div>
          <?php }?>
         </form>
         <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
          <div class="form-group"  id="page-wrap" style="margin-left:10px;">
		   <?php if($_REQUEST['inv_no']!=''){ ?> 
          <table width="100%" id="myTable" class="table table-bordered table-hover">
            <thead>
              <tr class="<?=$tableheadcolor?>" >
                <th>Product</th>
                <th>Inv. Qty</th>
                <th>OK Qty</th>
                <th>Damage Qty</th>
                <th>Missing Qty</th>
                <th>Price</th>
                <th>Value</th>
                <th>SGST %</th>
                <th>SGST Amt</th>
                <th>CGST %</th>
                <th>CGST Amt</th>
                <th>IGST %</th>
                <th>IGST Amt</th>
                <th>Total Amt</th>
                </tr>
            </thead>
            <tbody>
			<?php
			$i=0;
			$total_invqty=0;
			$total_okqty=0;
			$total_dmgqty=0;
			$total_misqty=0;
			$sql=mysqli_query($link1,"SELECT * FROM billing_model_data WHERE challan_no='".$_REQUEST['inv_no']."'");
			while($row=mysqli_fetch_assoc($sql)){
				
			?>
              <tr>
				<td align="left">
                <?php echo getProduct($row['prod_code'],$link1);?>
                <input type="hidden" name="req[<?php echo $i;?>]" id="req[<?php echo $i;?>]" value="<?php echo $row['qty'];?>"/>				
				<input type="hidden" name="prod_code[<?php echo $i;?>]" id="prod_code[<?php echo $i;?>]" class="form-control" value="<?php echo $row['prod_code'];?>"/>
                <input type="hidden" name="prod_cat[<?php echo $i;?>]" id="prod_cat[<?php echo $i;?>]" class="form-control" value="<?php echo $row['prod_cat'];?>"/>
                <input type="hidden" name="mrp[<?php echo $i;?>]" id="mrp[<?php echo $i;?>]" class="form-control" value="<?php echo $row['mrp'];?>"/>
                <input type="hidden" name="holdRate[<?php echo $i;?>]" id="holdRate[<?php echo $i;?>]" class="form-control" value="<?php echo $row['hold_price'];?>"/>
                </td>
				<td align="left">
                <input type="text" style="width:80px;text-align:right" name="req_qty[<?php echo $i;?>]" id="req_qty[<?php echo $i;?>]" onKeyUp="check_qty('<?php echo $i;?>');getAvlStk('<?php echo $i;?>');rowTotal('<?php echo $i;?>');" class="form-control digits" value="<?php echo round($row['qty']);?>" readonly/>
                </td>
                <td align="left">
                <input type="hidden" name="avl_okstock[<?php echo $i;?>]" id="avl_okstock[<?php echo $i;?>]" value="<?php echo getCurrentStockNew($_REQUEST['po_from'],$_REQUEST['stock_from'],$row['prod_code'],'okqty', $link1);?>"/>
                <input type="text" style="width:80px;text-align:right" name="ok_qty[<?php echo $i;?>]" id="ok_qty[<?php echo $i;?>]" onKeyUp="check_qty('<?php echo $i;?>');getAvlStk('<?php echo $i;?>');rowTotal('<?php echo $i;?>');" class="form-control digits" value="<?php echo round($row['okqty']);?>"/></td>
			    <td align="left">
                <input type="hidden" name="avl_dmgstock[<?php echo $i;?>]" id="avl_dmgstock[<?php echo $i;?>]" value="<?php echo getCurrentStockNew($_REQUEST['po_from'],$_REQUEST['stock_from'],$row['prod_code'],'broken', $link1);?>"/>
                <input type="text" style="width:80px;text-align:right" name="dmg_qty[<?php echo $i;?>]" id="dmg_qty[<?php echo $i;?>]" onKeyUp="check_qty('<?php echo $i;?>');getAvlStk('<?php echo $i;?>');rowTotal('<?php echo $i;?>');" class="form-control digits" value="<?php echo round($row['damageqty']);?>"/></td>
			    <td align="left">
                <input type="hidden" name="avl_misstock[<?php echo $i;?>]" id="avl_misstock[<?php echo $i;?>]" value="<?php echo getCurrentStockNew($_REQUEST['po_from'],$_REQUEST['stock_from'],$row['prod_code'],'missing', $link1);?>"/>
                <input type="text" style="width:80px;text-align:right" name="mis_qty[<?php echo $i;?>]" id="mis_qty[<?php echo $i;?>]" onKeyUp="check_qty('<?php echo $i;?>');getAvlStk('<?php echo $i;?>');rowTotal('<?php echo $i;?>');" class="form-control digits" value="<?php echo round($row['missingqty']);?>"/></td>
				<td align="left">
                <input type="text" style="width:80px;text-align:right" name="price[<?php echo $i;?>]" id="price[<?php echo $i;?>]" class="form-control" value="<?php echo $row['price'];?>" onKeyUp="rowTotal('<?php echo $i;?>');"/>
                </td>
				<td align="left">
                <input type="text" style="width:100px;text-align:right" name="linetotal[<?php echo $i;?>]" id="linetotal[<?php echo $i;?>]" class="form-control" value="<?php echo $row['value'];?>" readonly/>
                </td>
			    <td align="left"><input type="text" style="width:80px;text-align:right" name="sgst_per[<?php echo $i;?>]" id="sgst_per[<?php echo $i;?>]" class="form-control" value="<?php echo $row['sgst_per'];?>" readonly/></td>
			    <td align="left"><input type="text" style="width:100px;text-align:right" name="sgst_amt[<?php echo $i;?>]" id="sgst_amt[<?php echo $i;?>]" class="form-control" value="<?php echo $row['sgst_amt'];?>" readonly/></td>
			    <td align="left"><input type="text" style="width:80px;text-align:right" name="cgst_per[<?php echo $i;?>]" id="cgst_per[<?php echo $i;?>]" class="form-control" value="<?php echo $row['cgst_per'];?>" readonly/></td>
			    <td align="left"><input type="text" style="width:100px;text-align:right" name="cgst_amt[<?php echo $i;?>]" id="cgst_amt[<?php echo $i;?>]" class="form-control" value="<?php echo $row['cgst_amt'];?>" readonly/></td>
			    <td align="left"><input type="text" style="width:80px;text-align:right" name="igst_per[<?php echo $i;?>]" id="igst_per[<?php echo $i;?>]" class="form-control" value="<?php echo $row['igst_per'];?>" readonly/></td>
			    <td align="left"><input type="text" style="width:100px;text-align:right" name="igst_amt[<?php echo $i;?>]" id="igst_amt[<?php echo $i;?>]" class="form-control" value="<?php echo $row['igst_amt'];?>" readonly/></td>
			    <td align="left"><input type="text" style="width:120px;text-align:right" name="total_amt[<?php echo $i;?>]" id="total_amt[<?php echo $i;?>]" class="form-control" value="<?php echo $row['totalvalue'];?>" readonly/></td>
			    </tr>
			  <?php 
				$total_invqty += $row['qty'];
				$total_okqty += $row['okqty'];
				$total_dmgqty += $row['damageqty'];
				$total_misqty += $row['missingqty'];
				$i++;   
				}
			}
			?>
            </tbody>
          </table>
          </div>
          <div class="panel panel-info table-responsive">
		  <div class="panel-heading">Amount Info</div>
		  <div class="panel-body">
			<table class="table table-bordered" width="100%">
				<tbody>          
				   <tr>
					 <td width="25%"><label class="control-label">Total Qty</label></td>
					 <td width="25%"><input type="text" name="tot_qty" id="tot_qty" class="form-control" value="<?=$total_invqty;?>" style="width:150px;text-align:right" readonly/><input type="hidden" name="rowno" id="rowno" value="<?=$i?>"/></td>
					 <td width="25%"><label class="control-label">Sub Total</label></td>
					 <td width="25%"><input type="text" name="sub_total" id="sub_total" class="form-control" value="<?=$row_inv["basic_cost"]?>" style="width:150px;text-align:right" readonly/></td>
				   </tr>
				   <tr>
					 <td><label class="control-label">Tax Total</label></td>
					 <td><input type="text" name="tax_total" id="tax_total" class="form-control" value="<?=$row_inv['total_sgst_amt']+$row_inv['total_cgst_amt']+$row_inv['total_igst_amt'];?>" style="width:150px;text-align:right" readonly/>
                     <input type="hidden" name="total_sgstamt" id="total_sgstamt" class="form-control" readonly style="width:200px;text-align:right" value="<?=$row_inv['total_sgst_amt']?>"/>
                    <input type="hidden" name="total_cgstamt" id="total_cgstamt" class="form-control" readonly style="width:200px;text-align:right" value="<?=$row_inv['total_cgst_amt']?>"/>
                    <input type="hidden" name="total_igstamt" id="total_igstamt" class="form-control" readonly style="width:200px;text-align:right" value="<?=$row_inv['total_igst_amt']?>"/>
                     </td>
					 <td><label class="control-label">Grand Total</label></td>
					 <td><input type="text" name="grand_total" id="grand_total" class="form-control" value="<?=$row_inv["total_cost"];?>" style="width:150px;text-align:right" readonly/></td>
				   </tr>
				   <tr>
					 <td><label class="control-label">TCS</label></td>
					 <td>
                     	<select name="tcs_per" id="tcs_per" class="form-control" onChange="calculatetotal();">
                     		<option value="">--Please Select--</option>
                            <option value="0.1"<?php if($row_inv["tcs_per"]=="0.1"){ echo "selected";}?>>0.1 %</option>
                     	</select></td>
					 <td><label class="control-label">TCS Amount</label></td>
					 <td><input type="text" name="tcs_amt" id="tcs_amt" class="form-control" value="<?=$row_inv["tcs_amt"]?>" style="width:150px;text-align:right" readonly/></td>
				   </tr>
                   <tr>
					 <td><label class="control-label">Round Off</label></td>
					 <td><input type="text" name="round_off" id="round_off" class="form-control" value="<?=$row_inv["round_off"]?>" style="width:150px;text-align:right" readonly/></td>
					 <td><label class="control-label">Final Total</label></td>
					 <td><input type="text" name="final_total" id="final_total" class="form-control" value="<?=$row_inv["round_off"]+$row_inv["total_cost"]?>" style="width:150px;text-align:right" readonly/></td>
				   </tr>
                   <tr>
					 <td><label class="control-label"><!--Freight & Cartage--></label></td>
					 <td><input type="hidden" name="freight" id="freight" class="form-control number" value="0.00" style="width:150px;text-align:right"/></td>
					 <td><label class="control-label">TDS(194Q)</label></td>
					 <td><input type="text" name="tds_194q" id="tds_194q" class="form-control number" value="<?=$row_inv["tds"]?>" style="width:150px;text-align:right"/></td>
				   </tr>
                   <tr>
					 <td><label class="control-label">Delivery Address</label></td>
					 <td><textarea name="delivery_address" id="delivery_address" class="form-control required addressfield"  value=""style="resize:vertical" required><?=$row_inv["disp_addrs"]?></textarea></td>
					 <td><label class="control-label">Remark</label></td>
					 <td><textarea name="remark" id="remark" class="form-control addressfield" style="resize:vertical"></textarea></td>
				   </tr>
                   <tr>
                   	<td colspan="4" align="center">
                        <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Process" title="Process this purchase return" <?php if($_POST["upd"]=="Process"){?> disabled<?php }?>>
                        <input type="hidden" name="parentcode" id="parentcode" value="<?=$_REQUEST['po_from']?>"/>
                        <input type="hidden" name="partycode" id="partycode" value="<?=$_REQUEST['po_to']?>"/>
                        <input type="hidden" name="stockfrom" id="stockfrom" value="<?= $_REQUEST['stock_from'] ?>"/>
                        <input type="hidden" name="inv" id="inv" value="<?=$_REQUEST['inv_no']?>"/>
                        <input type="hidden" class="form-control" name="ledgername" value="<?=$row_inv["ledger_name"]?>"/>
                        <input type="hidden" name="doctype" id="doctype" value="<?=$row_inv["document_type"]?>"/>
                        <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='vendor_return.php?<?=$pagenav?>'">
                    </td>
                   </tr>
                   </tbody>
                   </table>
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
<?php if($_REQUEST['po_to']=='' || $_REQUEST['po_from']=='' || $_REQUEST['stock_from']=='' || $_REQUEST['inv_no']==''){ ?>
<script>
$("#frm2").find("input[type='submit']:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
</script>
<?php } ?>