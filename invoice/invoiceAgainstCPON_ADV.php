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
$parentloc = getLocationDetails($po_row['po_to'], "name,city,state,addrs,disp_addrs,margin,gstin_no,pincode,email,phone,id_type", $link1);
$parentlocdet = explode("~", $parentloc);
///// get child location details
$childloc = getLocationDetails($po_row['po_from'], "name,city,state,addrs,disp_addrs,gstin_no,pincode,email,phone,tcs_applicable,tcs_per,id_type", $link1);
$childlocdet = explode("~", $childloc);
@extract($_POST);
////// if we hit process button
if ($_POST) {
	if ($_POST['upd'] == 'Process') {
	///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
		$messageIdent = md5($_POST['upd'] . $po_row['po_no']);
		//and check it against the stored value:
		$sessionMessageIdent = isset($_SESSION['msgiacpo'])?$_SESSION['msgiacpo']:'';
		if($messageIdent!=$sessionMessageIdent){//if its different:
			//save the session var:
			$_SESSION['msgiacpo'] = $messageIdent;
    	if ($total_qty != '' && $total_qty != 0) {
			$stock_from = base64_decode($stockfrom);
			if($stock_from){
				if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM billing_master WHERE po_no='".$po_row['po_no']."'"))==0){
            	//// Make System generated Invoice/DC no.//////
				if($_REQUEST['doctype'] == 'DC'){
					$res_cnt = mysqli_query($link1, "SELECT stn_str,stn_counter FROM document_counter WHERE location_code='".$po_row['po_to']."'");
				}else{
					$res_cnt = mysqli_query($link1, "SELECT inv_str,inv_counter FROM document_counter WHERE location_code='".$po_row['po_to']."'");
				}
				///// check invoice /DC series should be there
            	if (mysqli_num_rows($res_cnt)){
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
					///// start transaction
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
					}else{
						$doctype =  "INVOICE";
						$invoicetype = "CORPORATE INVOICE";
					}
					///// explode billto
					$expl_billto = explode("~", $billto);
                 	$query1 = "INSERT INTO billing_master SET from_location='".$po_row['po_to']."', to_location='".$po_row['po_from']."', sub_location='".$stock_from."', from_gst_no='".$parentlocdet[6]."', from_partyname='".$parentlocdet[0]."', party_name='".$childlocdet[0]."', to_gst_no='".$expl_billto[4]."' ,challan_no='".$invno."', po_no='" . $po_row['po_no']."', ref_no='".$ref_no."', sale_date='".$today."', entry_date='".$today."', entry_time='".$currtime."', type='CORPORATE',billing_type='COMBO', document_type='".$doctype."', discountfor='PD', taxfor='".$taxfor."', status='Pending', entry_by='".$_SESSION['userid']."', basic_cost='".$sub_total."', discount_amt='".$total_discount."', tax_cost='".$tax_amount."', total_sgst_amt='".$total_sgst."', total_cgst_amt='".$total_cgst."', total_igst_amt='".$total_igst."', total_cost='".$grand_total."', tax_type='".$splitcompltetax[1]."', tax_header='".$splitcompltetax[2]."', tax='".$splitcompltetax[0]."', bill_from='".$po_row['po_to']."', bill_topty='".$expl_billto[0]."', from_addrs='".$parentlocdet[3]."', disp_addrs='".$parentlocdet[4]."', to_addrs='".$expl_billto[1]."', deliv_addrs='".$deli_addrs."', billing_rmk='".$remark."', margin = '".$margin_amt."',from_state='".$parentlocdet[2]."', to_state='".$expl_billto[3]."', from_city='".$parentlocdet[1]."', to_city='".$expl_billto[2]."', from_pincode='".$parentlocdet[7]."', to_pincode='".$expl_billto[5]."', from_phone='".$parentlocdet[9]."', to_phone='".$childlocdet[8]."', from_email='".$parentlocdet[8]."', to_email='".$childlocdet[7]."',round_off='".$round_off."',tcs_per='".$tcs_per."', tcs_amt='".$tcs_amt."',payment_term='".$payment_term."',ship_to='".$shiptodet[0]."',ship_to_gstin='".$shiptodet[5]."',ship_to_city='".$shiptodet[2]."',ship_to_state='".$shiptodet[3]."',ship_to_pincode='".$shiptodet[4]."',ledger_name='".$ledgername."',sale_person='".$po_row["sales_executive"]."'";
                	$result1 = mysqli_query($link1, $query1);
                	//// check if query is not executed
                	if (!$result1) {
                    	$flag = false;
                    	$err_msg = "Error Code1: ".mysqli_error($link1);
                	}
                	/// update invoice counter /////
					if($_REQUEST['doctype'] == 'DC'){
						$result2 = mysqli_query($link1,"UPDATE document_counter SET stn_counter=stn_counter+1,update_by='".$_SESSION['userid']."',updatedate='".$datetime."' WHERE location_code='".$po_row['po_to']."'");
						//// check if query is not executed
						if (!$result2) {
							$flag = false;
							$err_msg = "Error Code2: ".mysqli_error($link1);
						}
					}else{
						$result2 = mysqli_query($link1,"UPDATE document_counter SET inv_counter=inv_counter+1,update_by='".$_SESSION['userid']."',updatedate='".$datetime."' WHERE location_code='".$po_row['po_to']."'");
						//// check if query is not executed
						if (!$result2) {
							$flag = false;
							$err_msg = "Error Code2: ".mysqli_error($link1);
						}
					}
					$arr_taxx = array();
					$arr_val = array();
					$gst_type = "";
                	////// pick purchase order details
                	$po_datares = mysqli_query($link1, "SELECT * FROM purchase_order_data WHERE po_no='" . $po_row['po_no'] . "'");
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
						$rowcombo = "combo_model". $po_datarow['id'];
						$rowcomboprdcnt = "noofbomproduct". $po_datarow['id'];
						///// get combo model details
					$combo_det = explode("~",$_POST[$rowcombo]);
					///// get total no. of combo product at one line of item
					$numrow = $_POST[$rowcomboprdcnt];
					for($p=0; $p<$numrow; $p++){
						$comboprod = "combopart".$po_datarow['id']."_".$p;
						$comboprodqty = "combopartqty".$po_datarow['id']."_".$p;
						$comboprodprice = "combopartprice".$po_datarow['id']."_".$p;
						$getstk = getCurrentStockNew($po_row['po_to'], $stock_from, $_POST[$comboprod], "okqty", $link1);
						//// check stock should be available for each combo product ////
						if ($getstk < $_POST[$comboprodqty]) {
							$flag = false;
							$err_msg = "Error Code3: Stock is not available for ".$_POST[$comboprod];
						} else {
							
						}
						$linetotal = 0.00;
						$sgst_amt = 0.00;
						$cgst_amt = 0.00;
						$igst_amt = 0.00;
						$totalvalue = 0.00;
						// checking row value of combo product and qty should not be blank
                    	if ($_POST[$comboprod] != '' && $_POST[$comboprodqty] != '' && $_POST[$comboprodqty] != 0 && $_POST[$rowprodcut] != '' && $_POST[$rowqty] != '' && $_POST[$rowqty] != 0) {
							$linetotal = $_POST[$comboprodqty] * $_POST[$comboprodprice];
							$sgst_amt = round(($_POST[$rowsgstPer] * $linetotal)/100,2);
							$cgst_amt = round(($_POST[$rowcgstPer] * $linetotal)/100,2);
							$igst_amt = round(($_POST[$rowigstPer] * $linetotal)/100,2);
							$totalvalue = $linetotal + $sgst_amt + $cgst_amt + $igst_amt;
							
							/////////// insert data for products of each combo product
							$query_cp = "insert into billing_model_data set from_location='" . $po_row['po_to'] . "', prod_code='" . $_POST[$comboprod] . "', combo_code='".$combo_det[0]."', combo_name='".$combo_det[2]."', qty='" . $_POST[$comboprodqty] . "', okqty='" . $_POST[$comboprodqty] . "',mrp='" . $mrp[$k] . "', price='" . $_POST[$comboprodprice] . "', hold_price='" . $_POST[$comboprodprice] . "', value='" . $linetotal . "',tax_name='',tax_per='', tax_amt='',discount='', totalvalue='" . $totalvalue . "',challan_no='" . $invno . "' ,sale_date='" . $today . "',entry_date='" . $today . "' ,sgst_per='".$_POST[$rowsgstPer]."' ,sgst_amt='".$sgst_amt."',igst_per='".$_POST[$rowigstPer]."' ,igst_amt='".$igst_amt."',cgst_per='".$_POST[$rowcgstPer]."' ,cgst_amt='".$cgst_amt."'";
							$result_cp = mysqli_query($link1, $query_cp);
							//// check if query is not executed
							if (!$result_cp) {
								$flag = false;
								$err_msg = "Error Code4: Combo Product saving ".mysqli_error($link1);
							}
							//// update stock of from loaction
							$result3 = mysqli_query($link1, "update stock_status set okqty=okqty-'" . $_POST[$comboprodqty] . "',updatedate='" . $datetime . "' where asc_code='".$po_row['po_to']."' and sub_location='".$stock_from."' and partcode='" . $_POST[$comboprod] . "'");
							//// check if query is not executed
							if (!$result3) {
								$flag = false;
								$err_msg = "Error Code5: ".mysqli_error($link1);
							}
							///// update stock ledger table
							$flag = stockLedger($invno, $today, $_POST[$comboprod], $stock_from, $po_row['po_from'], $stock_from, "OUT", "OK", "Combo Invoice", $_POST[$comboprodqty], $_POST[$comboprodprice], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
						}	
					}/// close combo product for loop
						
						//find the scheme name
						if($rowschemecode != ""){
							$rrr = mysqli_fetch_assoc(mysqli_query($link1,"SELECT scheme_name FROM scheme_master WHERE scheme_code = '".$_POST[$rowschemecode]."' "));
							$rowschemename = $rrr['scheme_name'];
						}else{
							$rowschemename = "";
						}
                    	/*//checking row value of product and qty should not be blank
                    	$getstk = getCurrentStockNew($po_row['po_to'], $stock_from, $_POST[$rowprodcut], "okqty", $link1);
                    	//// check stock should be available ////
                    	if($getstk < $_POST[$rowqty]) {
                        	$flag = false;
                        	$err_msg = "Error Code2.1: Stock is not available for ".$_POST[$rowprodcut];
                    	}else{
                    	}*/
						if($_POST[$rowprodcut] != '' && $_POST[$rowqty] != '' && $_POST[$rowqty] != 0) {
							if($_POST[$rowsgstPer]!="" && $_POST[$rowsgstPer]!="0.00"  && $_POST[$rowsgstPer]!="0"){
								$gstper = round($_POST[$rowsgstPer] + $_POST[$rowcgstPer]);
								$arr_taxx[$gstper] += $_POST[$rowsgstAmt] + $_POST[$rowcgstAmt];
								$arr_val[$gstper] += $_POST[$rowvalue];
								$gst_type = "SGST-CGST";
							}else{
								$gstper = round($_POST[$rowigstPer]);					
								$arr_taxx[$gstper] += $_POST[$rowigstAmt];
								$arr_val[$gstper] += $_POST[$rowvalue];
								$gst_type = "IGST";
							}
							/////////// insert data
							$splitrowtax = explode("~", $_POST[$rowtaxtype]);
							$query3 = "INSERT INTO billing_model_data SET from_location='" . $po_row['po_to'] . "',challan_no='" . $invno . "', prod_code='" . $_POST[$rowprodcut] . "', combo_code='".$combo_det[0]."', combo_name='".$combo_det[2]."',prod_cat='C', qty='" . $_POST[$rowqty] . "', mrp='" . $_POST[$rowmrp] . "', price='" . $_POST[$rowprice] . "', hold_price='" . $_POST[$rowholdrate] . "', value='" . $_POST[$rowvalue] . "',tax_name='" . $splitrowtax[1] . "',tax_per='" . $splitrowtax[0] . "',tax_amt='" . $_POST[$rowtaxamt] . "',discount='" . $_POST[$rowdisc] . "',sgst_per='" . $_POST[$rowsgstPer] . "',sgst_amt='" . $_POST[$rowsgstAmt] . "',cgst_per='" . $_POST[$rowcgstPer] . "',cgst_amt='" . $_POST[$rowcgstAmt] . "',igst_per='" . $_POST[$rowigstPer] . "',igst_amt='" . $_POST[$rowigstAmt] . "', totalvalue='" . $_POST[$rowtotalval] . "',sale_date='" . $today . "',entry_date='" . $today . "', scheme_name = '".$rowschemename."', scheme_code = '".$_POST[$rowschemecode]."' ";
							$result3 = mysqli_query($link1, $query3);
							//// check if query is not executed
							if(!$result3) {
								$flag = false;
								$err_msg = "Error Code3: ".mysqli_error($link1);
							}
							//// update stock of from loaction
						   /*$result4 = mysqli_query($link1, "UPDATE stock_status SET okqty=okqty-'" . $_POST[$rowqty] . "',updatedate='" . $datetime . "' WHERE asc_code='" . $po_row['po_to'] . "' AND sub_location='" . $stock_from . "' AND partcode='" . $_POST[$rowprodcut] . "'");
							//// check if query is not executed
							if(!$result4) {
								$flag = false;
								$err_msg = "Error Code4: ".mysqli_error($link1);
							}*/
							///// update stock ledger table
							//$flag = stockLedger($invno, $today, $_POST[$rowprodcut], $stock_from, $po_row['po_from'], $stock_from, "OUT", "OK", $invoicetype, $_POST[$rowqty], $_POST[$rowprice], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
							////// release the PO qty in stock ////
							//$flag = releaseStockQty($po_row['po_to'], $_POST[$rowprodcut], $po_row['req_qty'], $link1, $flag);
						}// close if loop of checking row value of product and qty should not be blank
						//// update details in po table
						$result5 = mysqli_query($link1, "UPDATE purchase_order_data SET qty=qty+'".$_POST[$rowqty]."' WHERE id='".$po_datarow['id']."'");
						//// check if query is not executed
						if (!$result5) {
							$flag = false;
							$err_msg = "Error Code5: ".mysqli_error($link1);
						}
					}/// close while loop
					/////////Additional Details written by shekhar on 27 dec 2022///////////
					if(is_array($_POST['prod_code'])){
						$addprod_code = $_POST['prod_code'];
						$addbill_qty = $_POST['bill_qty'];
						$addprice = $_POST['price'];
						$addvalue = $_POST['value'];
						$addrowdiscount = $_POST['rowdiscount'];
						$addrowsubtotal = $_POST['rowsubtotal'];
						$addsgst_per = $_POST['sgst_per'];
						$addcgst_per = $_POST['cgst_per'];
						$addigst_per = $_POST['igst_per'];
						$addsgst_amt = $_POST['sgst_amt'];
						$addcgst_amt = $_POST['cgst_amt'];
						$addigst_amt = $_POST['igst_amt'];
						$addtotal_val = $_POST['total_val'];
						$len=count($_POST['prod_code']);
						if($len>0){
							for($i=0;$i<$len;$i++){			
								################ mysqli_ecape string function ##################################33
								if($addbill_qty[$i]!=""){					
									$getstk = getCurrentStockNew($po_row['po_to'], $stock_from, $addprod_code[$i], "okqty", $link1);
									if ($getstk < $addbill_qty[$i]) {
										$flag = false;
										$err_msg = "Error Code2.2: Stock is not available";
									} else {
										
									}
									if($addsgst_per[$i]!="" && $addsgst_per[$i]!="0.00"  && $addsgst_per[$i]!="0"){
										$gstper = round($addsgst_per[$i] + $addcgst_per[$i]);
										$arr_taxx[$gstper] += $addsgst_amt[$i] + $addcgst_amt[$i];
										$arr_val[$gstper] += $addvalue[$i];
										$gst_type = "SGST-CGST";
									}else{
										$gstper = round($addigst_per[$i]);					
										$arr_taxx[$gstper] += $addigst_amt[$i];
										$arr_val[$gstper] += $addvalue[$i];
										$gst_type = "IGST";
									}
									////insert additional details into billing data/////
									$result6 = mysqli_query($link1,"INSERT INTO billing_model_data SET from_location='" . $po_row['po_to'] . "',challan_no='" . $invno . "', prod_code='" . $addprod_code[$i]. "',prod_cat='', qty='" .$addbill_qty[$i]. "', mrp='', price='".$addprice[$i]."', hold_price='".$addprice[$i]."', value='".$addvalue[$i]."',tax_name='',tax_per='',tax_amt='',discount='".$addrowdiscount[$i]."',sgst_per='" . $addsgst_per[$i] . "',sgst_amt='" . $addsgst_amt[$i] . "',cgst_per='" . $addcgst_per[$i] . "',cgst_amt='" . $addcgst_amt[$i] . "',igst_per='" . $addigst_per[$i] . "',igst_amt='" . $addigst_amt[$i] . "', totalvalue='" . $addtotal_val[$i] . "',sale_date='" . $today . "',entry_date='" . $today . "', scheme_name = '', scheme_code = '',additional_product='FOC'");
									//// check if query is not executed
									if (!$result6) {
										 $flag = false;
										 $err_msg = "Error details6: " . mysqli_error($link1) . ".";
									}
									///// update inventory
									$result7 = mysqli_query($link1, "UPDATE stock_status SET okqty=okqty-'" . $addbill_qty[$i] . "',updatedate='" . $datetime . "' WHERE asc_code='" . $po_row['po_to'] . "' AND sub_location='" . $stock_from . "' AND partcode='" . $addprod_code[$i] . "'");
									//// check if query is not executed
									if(!$result7) {
										$flag = false;
										$err_msg = "Error details7: " . mysqli_error($link1) . ".";
									}
									//// stock ledger
									$flag = stockLedger($invno, $today, $addprod_code[$i], $stock_from, $po_row['po_from'], $stock_from, "OUT", "OK", $invoicetype, $addbill_qty[$i], $addprice[$i], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
								}//close if
							}//close for
						}//close if	
					}
					///////  Entry for the scheme start //////////////
					$s = $_REQUEST['count'];
					$t = $_REQUEST['norow'];
					if($_REQUEST['norow']>0){
						for($l=$s; $l<($s+$t); $l++){
							if(($_REQUEST['schqty'.$l] != "") && ($_REQUEST['schprd'.$l] != "")){
							/////////// insert data
							$query8 = "INSERT INTO billing_model_data SET from_location='" . $po_row['po_to'] . "',challan_no='" . $invno . "', prod_code='" . $_REQUEST['schprd'.$l] . "',prod_cat='', qty='" . $_REQUEST['schqty'.$l] . "', mrp='0.00', price='0.00', hold_price='0.00', value='0.00',tax_name='',tax_per= '0.00',tax_amt='0.00',discount='0.00',sgst_per='0.00',sgst_amt='0.00',cgst_per='0.00',cgst_amt='0.00',igst_per='0.00',igst_amt='0.00', totalvalue='0.00',sale_date='" . $today . "',entry_date='" . $today . "', scheme_name = 'FOC', scheme_code = '' ";
							$result8 = mysqli_query($link1, $query8);
							//// check if query is not executed
							if (!$result8) {
								$flag = false;
								$err_msg = "Error details8: " . mysqli_error($link1) . ".";
							}
							//// update stock of from loaction
						   $result9 = mysqli_query($link1, "UPDATE stock_status SET okqty=okqty-'" . $_REQUEST['schqty'.$l] . "',updatedate='" . $datetime . "' WHERE asc_code='" . $po_row['po_to'] . "' AND sub_location='" . $stock_from . "' AND partcode='" . $_REQUEST['schprd'.$l] . "'");
							//// check if query is not executed
							if (!$result9) {
								$flag = false;
								$err_msg = "Error details9: " . mysqli_error($link1) . ".";
							}
							///// update stock ledger table
							$flag = stockLedger($invno, $today, $_REQUEST['schprd'.$l], $po_row['po_to'], $po_row['po_from'], $po_row['po_to'], "OUT", "OK", $invoicetype, $_REQUEST['schqty'.$l], $_POST[$rowprice], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
							////// release the PO qty in stock ////
							//$flag = releaseStockQty($po_row['po_to'], $_REQUEST['schprd'.$l], $_REQUEST['schqty'.$l], $link1, $flag);
						}
					}
				}
				///////  Entry for the scheme stop //////////////
				///// update invoice and date in po master details
				$result10 = mysqli_query($link1, "UPDATE purchase_order_master SET status='Processed',dispatch_challan='" . $invno . "',challan_date='" . $today . "' WHERE po_no='" . $po_row['po_no']."'");
				//// check if query is not executed
				if(!$result10) {
					$flag = false;
					$err_msg = "Error details10: " . mysqli_error($link1) . ".";
				}
				//// update cr bal of child location
				$result11 = mysqli_query($link1, "UPDATE current_cr_status SET cr_abl=cr_abl-'" . $grand_total . "',total_cr_limit=total_cr_limit-'" . $grand_total . "', last_updated='" . $datetime . "' WHERE parent_code='" . $po_row['po_to'] . "' AND asc_code='" . $po_row['po_from'] . "'");
				//// check if query is not executed
				if (!$result11) {
					$flag = false;
					$err_msg = "Error Code11: ".mysqli_error($link1);
				}
				////// maintain party ledger////
				$flag = partyLedger($po_row['po_to'], $po_row['po_from'], $invno, $today, $today, $currtime, $_SESSION['userid'], $invoicetype, $grand_total, "DR", $link1, $flag);				
				//////////////				
				if($_POST['margin_amt'] != '0' || $_POST['margin_amt'] != '0.00') {
					////// maintain party ledger for margin////
					$flag = partyLedger($po_row['po_to'], $po_row['po_from'], $invno, $today, $today, $currtime, $_SESSION['userid'], "Margin Against Invoice", $grand_total, "CR", $link1, $flag);	
				}				
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
			$msg = "Request could not be processed . Invoice is already created against PO .".$po_row['po_no'];
		}
		}else{
			$msg = "Request could not be processed . Please select cost center.";
		}
	}else {
		$msg = "Request could not be processed . Please dispatch some qty.";
	}
	}else {
		//you've sent this already!
		$msg="You have saved this already ";
		$cflag = "warning";
		$cmsg = "Warning";
	}	
	///// move to parent page
	header("location:comboInvoice.php?msg=" . $msg . "" . $pagenav);
	exit;
}else{
}
}
////// extratct bill to if selected
$billto = explode("~",$_REQUEST["bill_to"]);
//print_r($billto);
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
<link rel="stylesheet" href="../css/bootstrap-select.min.css">
<script src="../js/bootstrap-select.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#frm1").validate();
	$("#frm2").validate();
});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
<script type="text/javascript">
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
			//var qty = 0.00;
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
	else{
	  	location.reload();
	}
}		
/////// calculate line total /////////////
function rowTotal(ind) {
	setTimeout(function () {
	//getComboProduct(ind);
	}, 3000);
	var ent_qty = "bill_qty" + ind + "";
	var ent_rate = "price" + ind + "";
	var ent_value = "value" + ind + "";
	var hold_rate = "holdRate" + ind + "";
	var po_price = "poprice" + ind + "";
	//var availableQty = "avl_stock" + ind + "";
	var nobomprod = "noofbomproduct" + ind + "";
	
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
	
	///// check stock of each combo
	var availableQty = 1;
	var bompcnt = parseInt(document.getElementById(nobomprod).value);
	//alert("bompcnt="+bompcnt);
	for(var k=0; k<bompcnt; k++){
		var selqty = parseInt(document.getElementById("combopartqty"+ind+"_"+k).value);
		var avlqty = parseInt(document.getElementById("avl_stock"+ind+"_"+k).value);
		//alert(selqty+">"+avlqty);
		if(selqty>avlqty){
			availableQty *= 0;
		}else{
			availableQty *= 1;
		}
	}
	
	////// check if entered qty is something
	if (document.getElementById(ent_qty).value) {
		var qty = document.getElementById(ent_qty).value;
	} else {
		var qty = 0;
	}
	//alert(qty);
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
	<?php if($_REQUEST["doc_type"]=="DC"){ ?>
		var sgst = 0.00;
		var cgst = 0.00;
		var igst = 0.00;
	<?php }else{?>
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
	<?php }?>
	var gstrate = parseFloat(sgst) + parseFloat(cgst) + parseFloat(igst);
	var gstamt = parseFloat(document.getElementById(sgst_amt).value) + parseFloat(document.getElementById(cgst_amt).value) + parseFloat(document.getElementById(igst_amt).value);
	<?php 
	if($_REQUEST["bill_to"]){
	if($billto[3]==$parentlocdet[2]){ ?>
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
	///// check if row wise tax is something
	//  if(document.getElementById(rowtax).value){ var expldtax=(document.getElementById(rowtax).value).split("~"); var rowtaxval=expldtax[0];}else{ var rowtaxval=0.00; }
	
	//alert(availableQty);
	////// check entered qty should be available
	//if(parseFloat(qty) <= parseFloat(document.getElementById(availableQty).value)) {
	var value = parseFloat(qty) * parseFloat(price);
	//var discost = parseFloat(value) -  (parseFloat(qty) * parseFloat(dicountval));
	var discost = parseFloat(value) -  parseFloat(dicountval);
	var sgst_amt1 = discost * sgst / 100;
	var cgst_amt1 = discost * cgst / 100;
	var igst_amt1 = discost * igst / 100;
	var linetot = parseFloat(discost + sgst_amt1 + cgst_amt1 + igst_amt1);
	if(availableQty==1 && k>0) {                    
		if (parseFloat(value) >= parseFloat(dicountval)) {
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
			var var3 = "value" + ind + "";
			//if(total!="" && total!="0"){ var t = total;}else{ var t = 0.00;}
			document.getElementById(var3).value = formatCurrency(total);
			document.getElementById(discountField).value = "0.00";
			document.getElementById(totalvalField).value = formatCurrency(ent_rowdisval);
			document.getElementById(totalvalField).value = formatCurrency(total);
			calculatetotal();
		}
	}
	else {
		alert("Stock is not Available");
		document.getElementById(ent_qty).value = "";
		//document.getElementById(availableQty).value="";
		document.getElementById(ent_rate).value = document.getElementById(po_price).value;
		//calculatetotal();
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
		var temp_price = "price" + i + "";
		var temp_value = "value" + i + "";
		var temp_discount = "rowdiscount" + i + "";
		var temp_discountval = "rowdiscount_val" + i + "";
		var temp_total_val = "total_val" + i + "";
		var discountvar = 0.00;
		var totalamt = 0.00;
		var totalvalue = 0.00;
		var totqty = 0;
		//document.getElementById(temp_value).value = parseInt(document.getElementById(temp_qty).value)*parseFloat(document.getElementById(temp_price).value,2);
		//document.getElementById(temp_discountval).value = parseFloat(document.getElementById(temp_value).value,2)-parseFloat(document.getElementById(temp_discount).value,2);
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
		//var valAfterDisc = ((parseInt(document.getElementById("bill_qty"+i).value) * parseFloat(document.getElementById("price"+i).value)) - (parseInt(document.getElementById("bill_qty"+i).value) * parseFloat(discountvar)));
		var valAfterDisc = ((parseInt(document.getElementById("bill_qty"+i).value) * parseFloat(document.getElementById("price"+i).value)) - parseFloat(discountvar));
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
		//sum_discount += (parseFloat(discountvar) * parseInt(document.getElementById("bill_qty"+i).value));
		sum_discount += parseFloat(discountvar);
		
		sum_cgstamt += parseFloat(cgstamount);
		sum_sgstamt += parseFloat(sgstamount);
		sum_igstamt += parseFloat(igstamount);

		sum_grandtot += parseFloat(totalamt);
	}/// close for loop
	document.getElementById("qtyto").value = (sum_qty);
	document.getElementById("total_qty_ext").value = (sum_qty);
	getToQty();///// calculate total qty
	
	document.getElementById("sub_total_ext").value = formatCurrency(sum_subtot);
	document.getElementById("total_discount_ext").value = formatCurrency(sum_discount);
	document.getElementById("total_sgst_ext").value = formatCurrency(sum_sgstamt);
	document.getElementById("total_cgst_ext").value = formatCurrency(sum_cgstamt);
	document.getElementById("total_igst_ext").value = formatCurrency(sum_igstamt);
	document.getElementById("grand_total_ext").value = formatCurrency(sum_grandtot);
	
	getFinalCal();
}
          
function getShipToInfo(val){
	var shipto = val.split("~");
	$("#shipto_gstin").val(shipto[3]);
	$("#shipto_city").val(shipto[2]);
	$("#delivery_address").val(shipto[1]);
}
function recount(){
	var rowno = (document.getElementById("rowno").value);
	for (var i = 1; i < rowno; i++) {
		rowTotal(i);
	}
}
$(document).ready(function() {
	$("#add_row").click(function() {
	   var ni = document.getElementById('newpartrow');
		var numi = document.getElementById('theValue');
		var num = (document.getElementById("theValue").value -1)+ 2;
		//alert(num);
		// condition for add next form only when previous for is filled \\
		numi.value = num;
		var divIdName = "newpartrow"+num+"";
		var nextnum = num+2;
		//alert(divIdName);
		var newdiv = document.createElement('span');
		newdiv.setAttribute("id",divIdName);
		
		newdiv.innerHTML = '<table class="table table-bordered" width="100%"><tr><td width="15%"><select class="form-control selectpicker" data-live-search="true" name="prod_code[' + num + ']" id="prod_code[' + num + ']" required  onChange="getAvlStk(' + num + '); get_price(' + num + ');" style="width:200px"><option value="">--None--</option><?php $model_query = "select productcode,productname,productcolor from product_master where status='Active'";$check1 = mysqli_query($link1, $model_query);while ($br = mysqli_fetch_array($check1)) {?><option value="<?php echo $br['productcode']; ?>"><?php echo $br['productname']." | ".$br['productcode']." | ".$br['productcolor']; ?></option><?php } ?></select></td><td style="text-align:right" width="10%"><input type="hidden" name="avl_stock[' + num + ']" id="avl_stock[' + num + ']" value="0"></td><td width="15%">Qty:<input type="text" class="form-control digits" name="bill_qty[' + num + ']" id="bill_qty[' + num + ']" value="0" autocomplete="off" required onBlur=rowTotalNew(' + num + '); style="width:45px;text-align:right;padding: 5px;"><span id="err_qty['+num+']" class="red_small"></span></td><td width="15%">Price:<input type="text" class="form-control number" name="price[' + num + ']" id="price[' + num + ']" onBlur="rowTotalNew(' + num + ');" autocomplete="off" required value="0.00" style="width:72px;text-align:right;padding: 5px;"></td><td width="15%">Value:<input type="text" class="form-control" name="value[' + num + ']" id="value[' + num + ']" autocomplete="off" readonly value="0.00" style="width:72px;text-align:right;padding: 5px;"></td><td width="10%">Discount:<input type="text" class="form-control number" name="rowdiscount[' + num + ']" id="rowdiscount[' + num + ']" autocomplete="off"  onblur="rowTotalNew(' + num + ');" value="0.00" style="width:72px;text-align:right;padding: 5px;"></td><td width="10%">After Discount:<input type="text" class="form-control" name="rowsubtotal[' + num + ']" id="rowsubtotal[' + num + ']" autocomplete="off" readonly value="0.00" style="width:72px;text-align:right;padding: 5px;"></td><td width="10%">SGST%:<input type="text" class="form-control" name="sgst_per[' + num + ']" id="sgst_per[' + num + ']" readonly value="0.00" style="width:50px;text-align:right;padding: 5px;"></td><td width="10%">SGST Amt:<input type="text" class="form-control" name="sgst_amt[' + num + ']" id="sgst_amt[' + num + ']" readonly value="0.00" style="width:65px;text-align:right;padding: 5px;"></td><td width="10%">CGST%:<input type="text" class="form-control" name="cgst_per[' + num + ']" id="cgst_per[' + num + ']" readonly value="0.00" style="width:50px;text-align:right;padding: 5px;"></td><td width="10%">CGST Amt:<input type="text" class="form-control" name="cgst_amt[' + num + ']" id="cgst_amt[' + num + ']" readonly value="0.00" style="width:65px;text-align:right;padding: 5px;"></td><td width="10%">IGST%:<input type="text" class="form-control" name="igst_per[' + num + ']" id="igst_per[' + num + ']" readonly value="0.00" style="width:50px;text-align:right;padding: 5px;"></td><td width="10%">IGST Amt:<input type="text" class="form-control" name="igst_amt[' + num + ']" id="igst_amt[' + num + ']" readonly value="0.00" style="width:65px;text-align:right;padding: 5px;"></td><td width="10%">Total:<input type="text" class="form-control" name="total_val[' + num + ']" id="total_val[' + num + ']" autocomplete="off" readonly value="0.00" style="width:75px;text-align:right;padding: 5px;"></td><td width="10%" align="center"><i class="fa fa-close fa-lg" onClick="deleteRow(' + num + ');"></i></td></tr></table>';
		ni.appendChild(newdiv);
		makeSelect();
	});
});
function makeSelect(){
	$('.selectpicker').selectpicker({
		liveSearch: true,
		showSubtext: true
	});
  }
function deleteRow(ind){  
 //$("#addr"+(indx)).html(''); 
 var id="newpartrow"+ind+"";
 var itemid="prod_code"+"["+ind+"]";
 var qtyid="bill_qty"+"["+ind+"]";
 var rateid="price"+"["+ind+"]";
 var totalid="total_val"+"["+ind+"]";
 var valueid="value"+"["+ind+"]";
 var rowdiscountid="rowdiscount"+"["+ind+"]";
 var rowsubtotalid="rowsubtotal"+"["+ind+"]";
 var sgstperid="sgst_per"+"["+ind+"]";
 var sgstamtid="sgst_amt"+"["+ind+"]";
 var cgstperid="cgst_per"+"["+ind+"]";
 var cgstamtid="cgst_amt"+"["+ind+"]";
 var igstperid="igst_per"+"["+ind+"]";
 var igstamtid="igst_amt"+"["+ind+"]";
 var abl_qtyid="avl_stock"+"["+ind+"]";
 // hide fieldset \\
 document.getElementById(id).style.display="none";
 // Reset Value\\
 // Blank the Values \\
 document.getElementById(itemid).value="";
 document.getElementById(qtyid).value="0.00";
 document.getElementById(rateid).value="0.00";
 document.getElementById(totalid).value="0.00";
 document.getElementById(valueid).value="0.00";
 document.getElementById(rowdiscountid).value="0.00";
 document.getElementById(rowsubtotalid).value="0.00";
 document.getElementById(sgstperid).value="0.00";
 document.getElementById(sgstamtid).value="0.00";
 document.getElementById(cgstperid).value="0.00";
 document.getElementById(cgstamtid).value="0.00";
 document.getElementById(igstperid).value="0.00";
 document.getElementById(igstamtid).value="0.00";
 document.getElementById(abl_qtyid).value="0.00";
 calculatetotal_addnew();
 }
  /////////// function to get available stock of ho
function getAvlStkComboP(indx) {
	var productCode = document.getElementById("combopart" + indx +"").value;
	var locationCode = "<?=$po_row['po_to']?>";
	var aclocationCode = "<?=$_REQUEST['stock_from']?>";
	var stocktype = "okqty";
	$.ajax({
		type: 'post',
		url: '../includes/getAzaxFields.php',
		data: {locstk: productCode, loccode: locationCode, godown:aclocationCode, stktype: stocktype, indxx: indx},
		success: function(data) {
		//alert(data);
			var getdata = data.split("~");
			if(getdata[0]){ var avl_stk = getdata[0];}else{var avl_stk = 0;}
			document.getElementById("avl_stock" + getdata[1] + "").value = avl_stk;
			var mainindx = getdata[1].split('_');
			//alert(mainindx[0]);
			rowTotal(mainindx[0]);
		}
	});
}
  /////////// function to get available stock of ho
function getAvlStk(indx) {
	var productCode = document.getElementById("prod_code[" + indx + "]").value;
	var locationCode = "<?=$po_row['po_to']?>";
	var aclocationCode = "<?=$_REQUEST['stock_from']?>";
	var stocktype = "okqty";
	$.ajax({
		type: 'post',
		url: '../includes/getAzaxFields.php',
		data: {locstk: productCode, loccode: locationCode, godown:aclocationCode, stktype: stocktype, indxx: indx},
		success: function(data) {
			var getdata = data.split("~");
			document.getElementById("avl_stock[" + getdata[1] + "]").value = getdata[0];
		}
	});
}
///// function to get price of product
function get_price(ind) {
	var productCode = document.getElementById("prod_code[" + ind + "]").value;
	var billingfrom = "<?=$po_row['po_to']?>";
	var billingto  =  "<?=$po_row['po_from']?>";		
	var tolocation = "<?=$billto[3]?>";
	var fromlocation = "<?=$parentlocdet[2]?>";   
	var fromidtype = "<?=$parentlocdet[10]?>";   
	$.ajax({
		type: 'post',
		url: '../includes/getAzaxFields.php',
		data: {productinfo: productCode, idtype: fromidtype, fromstate:fromlocation},
		success: function(data) {
			var splitprice = data.split("~");
			document.getElementById("price[" + ind + "]").value = splitprice[3];
			//document.getElementById("reward_info_chck[" + ind + "]").value = splitprice[4];
			//document.getElementById("reward_point[" + ind + "]").value = splitprice[5];
			//if((splitprice[4] == 'Y') && (splitprice[5] >0)){
			   //document.getElementById("prd_desc"+ind+"").innerHTML = "<a href='#' title='Reward Point' style='color:#FF0000;' data-toggle='popover' data-trigger='focus' data-content='"+splitprice[6]+"'><i class='fa fa-th'></i></a>";
			  //rePop();
			 //}
			 //else {
			  //}	
			 // alert(tolocation+"=="+fromlocation);
			if ((tolocation == fromlocation) ){ ///// for new customer //////////////////////////////////////////////////////////
				document.getElementById("sgst_per[" + ind + "]").value = splitprice[0];
				document.getElementById("cgst_per[" + ind + "]").value = splitprice[1];
				$("#igst_per[" + ind + "]").value = '0';
			}
			else {					
				$("#sgst_per[" + ind + "]").value = '0';
				$("#cgst_per[" + ind + "]").value = '0';
				document.getElementById("igst_per[" + ind + "]").value = splitprice[2];
			}
		}
	});
}
 /////// calculate line total /////////////
function rowTotalNew(ind) {
	//get_price(ind);
	var ent_qty = "bill_qty" + "[" + ind + "]";
	var ent_rate = "price" + "[" + ind + "]";
	var availableQty = "avl_stock" + "[" + ind + "]";
	var discountField = "rowdiscount" + "[" + ind + "]";
	var totalvalField = "total_val" + "[" + ind + "]";
	var st = "value" + "[" + ind + "]";
	var rst = "rowsubtotal" + "[" + ind + "]";
	//alert("<?=$parentlocdet[2]?>==<?=$billto[3]?>");
	<?php if($parentlocdet[2]==$billto[3]){ ?>
	var rowsgstper = "sgst_per" + "[" + ind + "]";
	var rowcgstper = "cgst_per" + "[" + ind + "]";
	var rowsgstamount = "sgst_amt" + "[" + ind + "]";
	var rowcgstamount = "cgst_amt" + "[" + ind + "]";
	<?php }else{ ?>
	var rowigstper = "igst_per" + "[" + ind + "]";
	var rowigstamount = "igst_amt" + "[" + ind + "]";
	<?php }?>
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
	///// check if discount value is something
	if (document.getElementById(discountField).value) {
		var dicountval = document.getElementById(discountField).value;		
	} else {
		var dicountval = 0.00;
	}         
	<?php if($parentlocdet[2]==$billto[3]){ ?>
	<?php if($_REQUEST["doc_type"]=="DC"){ ?>
		var sgstper = 0.00;
		var cgstper = 0.00;
	<?php }else{?>
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
	<?php }?>
	<?php } else{?>
	<?php if($_REQUEST["doc_type"]=="DC"){ ?>
		var igstper = 0.00;
	<?php } else{?>
	// check if igst per
	if (document.getElementById(rowigstper).value) {
		var igstper = (document.getElementById(rowigstper).value);
	} else {
		var igstper = 0.00;
	}
	<?php }?>
	<?php }?>
	////// check entered qty should be available
	if (parseFloat(qty) <= parseFloat(document.getElementById(availableQty).value)) {
		var total = parseFloat(qty) * parseFloat(price);				
		if (parseFloat(total) >= parseFloat(dicountval)) {
			var totalcost = (parseFloat(qty) * (parseFloat(price)) - parseFloat(dicountval));
			<?php if($parentlocdet[2]==$billto[3]){ ?>
			var sgst_amt = ((totalcost * sgstper) / 100);
			var cgst_amt = ((totalcost * cgstper) / 100);
			<?php }else{?>
			var igst_amt = ((totalcost * igstper) / 100);
			<?php }?>
			//// calculate row wise discount                
			document.getElementById(st).value = formatCurrency(total);
			document.getElementById(rst).value = formatCurrency(totalcost);
			<?php if($parentlocdet[2]==$billto[3]){ ?>
			document.getElementById(rowsgstamount).value = formatCurrency(sgst_amt);
			document.getElementById(rowcgstamount).value = formatCurrency(cgst_amt);
			var tot = parseFloat(totalcost) + parseFloat(sgst_amt) + parseFloat(cgst_amt);
			<?php }else{?>
			 document.getElementById(rowigstamount).value = formatCurrency(igst_amt);
			 var tot = parseFloat(totalcost) + parseFloat(igst_amt);
			<?php } ?>
			document.getElementById(totalvalField).value = formatCurrency(parseFloat(tot));
			calculatetotal_addnew();
		} else {
			alert("2 Discount is exceeding from price");
			var total = parseFloat(qty) * parseFloat(price);
			var var3 = "value" + "[" + ind + "]";
			document.getElementById(var3).value = formatCurrency(total);
			document.getElementById(discountField).value = "0.00";
			document.getElementById(totalvalField).value = formatCurrency(total);
			calculatetotal_addnew();
		}
	} else if (parseFloat(document.getElementById(availableQty).value) == '0.00') {
		document.getElementById(ent_qty).value = "0";
		alert("Stock is not Available.");
		//document.getElementById(availableQty).value="";
		document.getElementById(ent_rate).value = 0.00;
		calculatetotal_addnew();
	}
	else {
		document.getElementById(ent_qty).value = "0";
		alert("Stock is not Available..");
		//document.getElementById(availableQty).value="";
		document.getElementById(ent_rate).value = 0.00;
		calculatetotal_addnew();
	}
}
function calculatetotal_addnew(){
	var rowno=(document.getElementById("theValue").value);
	var sum_qty=0.00;
	var sum_value=0.00; 
	var sum_discount=0.00; 
	var sum_aftdisval=0.00;
	var sum_sgstamt=0.00;
	var sum_cgstamt=0.00;
	var sum_igstamt=0.00;
	var sum_totval=0.00;
	for(var ind=0;ind<=rowno;ind++){
		var ent_qty = "bill_qty" + "[" + ind + "]";
		var ent_rate = "price" + "[" + ind + "]";
		var st = "value" + "[" + ind + "]";
		var discountField = "rowdiscount" + "[" + ind + "]";
		var rst = "rowsubtotal" + "[" + ind + "]";
		<?php if($parentlocdet[2]==$billto[3]){ ?>
		var rowsgstper = "sgst_per" + "[" + ind + "]";
		var rowcgstper = "cgst_per" + "[" + ind + "]";
		var rowsgstamount = "sgst_amt" + "[" + ind + "]";
		var rowcgstamount = "cgst_amt" + "[" + ind + "]";
		<?php }else{ ?>
		var rowigstper = "igst_per" + "[" + ind + "]";
		var rowigstamount = "igst_amt" + "[" + ind + "]";
		<?php }?>
		var totalvalField = "total_val" + "[" + ind + "]";
		
		sum_qty+=parseFloat(document.getElementById(ent_qty).value);
		sum_value+=parseFloat(document.getElementById(st).value);
		sum_discount+=parseFloat(document.getElementById(discountField).value);
		sum_aftdisval+=parseFloat(document.getElementById(rst).value);
		<?php if($parentlocdet[2]==$billto[3]){ ?>
		sum_sgstamt+=parseFloat(document.getElementById(rowsgstamount).value);
		sum_cgstamt+=parseFloat(document.getElementById(rowcgstamount).value);
		<?php }else{ ?>
		sum_igstamt+=parseFloat(document.getElementById(rowigstamount).value);
		<?php }?>
		sum_totval+=parseFloat(document.getElementById(totalvalField).value);
	}
	document.getElementById("total_qty_add").value = sum_qty;
	document.getElementById("sub_total_add").value = sum_value;
	document.getElementById("total_discount_add").value = sum_discount;
	<?php if($parentlocdet[2]==$billto[3]){ ?>
	document.getElementById("total_sgst_add").value = sum_sgstamt;
	document.getElementById("total_cgst_add").value = sum_cgstamt;
	<?php }else{ ?>
	document.getElementById("total_igst_add").value = sum_igstamt;
	<?php }?>
	document.getElementById("grand_total_add").value = sum_totval;
	getFinalCal();
}
function getFinalCal(){
	var f_total_qty = parseInt(document.getElementById("total_qty_ext").value) + parseFloat(document.getElementById("total_qty_add").value,2);
	var f_sub_total = parseFloat(document.getElementById("sub_total_ext").value,2) + parseFloat(document.getElementById("sub_total_add").value,2);
	var f_discount = parseFloat(document.getElementById("total_discount_ext").value,2) + parseFloat(document.getElementById("total_discount_add").value,2);
	var f_sgstamt = parseFloat(document.getElementById("total_sgst_ext").value,2) + parseFloat(document.getElementById("total_sgst_add").value,2);
	var f_cgstamt = parseFloat(document.getElementById("total_cgst_ext").value,2) + parseFloat(document.getElementById("total_cgst_add").value,2);
	var f_igstamt = parseFloat(document.getElementById("total_igst_ext").value,2) + parseFloat(document.getElementById("total_igst_add").value,2);
	var f_total = parseFloat(document.getElementById("grand_total_ext").value,2) + parseFloat(document.getElementById("grand_total_add").value,2);
	document.getElementById("total_qty").value = f_total_qty;
	document.getElementById("sub_total").value = f_sub_total;
	document.getElementById("total_discount").value = f_discount;
	document.getElementById("total_sgst").value = f_sgstamt;
	document.getElementById("total_cgst").value = f_cgstamt;
	document.getElementById("total_igst").value = f_igstamt;
	document.getElementById("grand_total").value = f_total;
	////// check if TCS is applicable or not
	var tcs = document.getElementById("tcs_per").value;
	var sum_grandtot = f_total;
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
function getComboProduct(indx) {
var comboModel = document.getElementById("prodcode" + indx + "").value;
var comboModelQty = document.getElementById("bill_qty" + indx + "").value;
if(comboModelQty){
}else{
	comboModelQty = 0;
}
var locationCode = "<?=$po_row['po_to']?>";
var tolocation = "<?=$childlocdet[2]?>";
var fromlocation = "<?=$parentlocdet[2]?>";   
var fromidtype = "<?=$childlocdet[11]?>";
$.ajax({
	type: 'post',
	url: '../includes/getAzaxFields.php',
	data: {combModel: comboModel, combModelQty: comboModelQty, loccode: locationCode, idtype: fromidtype, fromstate:fromlocation, indxx: indx},
	success: function(data) {
		//// split response
		var getdata = data.split("~");
		// Display the array elements
		var comboProd = JSON.parse(getdata[0]);
		var totprice = 0.00;
		for(var i = 0; i < comboProd.length; i++){
			var newindx = getdata[1]+"_"+i;
			///// split product and its combo qty
			var combopart = comboProd[i].split("^");
			<?php /*?>var productlist = '<select class="form-control" data-live-search="true" name="combopart'+newindx+'" id="combopart'+newindx+'" required onchange=getAvlStk("'+newindx+'","'+getdata[1]+'");><option value="">--None--</option><?php $model_query = "SELECT productcode,productname,productcolor FROM product_master WHERE status='Active'";$check1 = mysqli_query($link1, $model_query);while ($br = mysqli_fetch_array($check1)) {?><option data-tokens="<?php echo $br['productname']." | ".$br['productcode']; ?>" value="<?php echo $br['productcode'];?>"><?php echo $br['productname']." | ".$br['productcode']; ?></option><?php } ?></select>';<?php */?>
			//var productqty = '<input name="combopartqty'+newindx+'" id="combopartqty'+newindx+'" type="text" class="form-control" value="'+combopart[1]+'" readonly/>';
			//var productprice = '<input name="combopartprice'+newindx+'" id="combopartprice'+newindx+'" type="text" class="form-control" value="'+combopart[2]+'" style="text-align:right" readonly/>';
			document.getElementById("combopartqty"+newindx).value = combopart[1];
			document.getElementById("combopartprice"+newindx).value = combopart[2];
			//alert(combopart[1]);
			//$("#combo_prodlist"+getdata[1]+"").append(productlist);
			//$("#combo_prodqty"+getdata[1]+"").append(productqty);
			//$("#combo_prodprice"+getdata[1]+"").append(productprice);
			//document.getElementById("combopart"+newindx).value = combopart[0];
			//list_prod[combopart[0]] = parseInt(combopart[1]);
			totprice += parseFloat(combopart[2]*combopart[1]);
		}
		//document.getElementById("price"+getdata[1]+"").value = totprice;
		//document.getElementById("noofbomproduct"+getdata[1]+"").value = comboProd.length;
		//get_price(getdata[1]);
		//console.log(list_prod);
		//document.getElementById("combo_prodlist"+getdata[1]+"").innerHTML = productlist;
	}
});
}
        </script>
    </head>
    <body onKeyPress="return keyPressed(event);" onLoad="recount()">
        <div class="container-fluid">
            <div class="row content">
                <?php
                include("../includes/leftnav2.php");
                ?>
                <div class="col-sm-9">
                    <h2 align="center"><i class="fa fa-user-circle-o"></i> Sale Invoicing against Combo PO (Advance)</h2><br/>
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
              								  	<td><select name="doc_type" id="doc_type" class="form-control" onChange="document.frm1.submit();">
											 <option value="">Invoice</option>
                     							<option value="DC"<?php if($_REQUEST['doc_type']=="DC"){ echo "selected";}?>>Delivery Challan</option>
                    								</select></td>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label class="control-label">Cost Centre(Godown)<span style="color:#F00">*</span></label></td>
              								  	<td><select name="stock_from" id="stock_from" required class="form-control required" data-live-search="true" onChange="document.frm1.submit();">
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
                                                <td><select name="ledger_name" id="ledger_name" class="form-control <?php if($_REQUEST['doc_type']=="DC"){ echo "required";}?>" <?php if($_REQUEST['doc_type']=="DC"){ echo "required";}?> onChange="document.frm1.submit();" <?php if($_REQUEST['doc_type']=="DC"){}else{?> disabled<?php }?>>
                                        	<option value=""<?php if($_REQUEST["ledger_name"]==""){ echo "selected";}?>>--Please Select--</option>
											<option value="Advance Warranty Replacement"<?php if($_REQUEST["ledger_name"]=="Advance Warranty Replacement"){ echo "selected";}?>>Advance Warranty Replacement</option>
											<option value="Warranty Replacement"<?php if($_REQUEST["ledger_name"]=="Warranty Replacement"){ echo "selected";}?>>Warranty Replacement</option>
                                            <option value="Sample & Testing Purpose"<?php if($_REQUEST["ledger_name"]=="Sample & Testing Purpose"){ echo "selected";}?>>Sample & Testing Purpose</option>
                                            <option value="Stock Transfer Within State GST"<?php if($_REQUEST["ledger_name"]=="Stock Transfer Within State GST"){ echo "selected";}?>>Stock Transfer Within State GST</option>
                                            <option value="POP Material"<?php if($_REQUEST["ledger_name"]=="POP Material"){ echo "selected";}?>>POP Material</option>
                                            <option value="Business Promotion"<?php if($_REQUEST["ledger_name"]=="Business Promotion"){ echo "selected";}?>>Business Promotion</option>
                                     	</select></td>
                                            </tr>
                                            <tr>
                                                <td><label class="control-label">Bill To</label></td>
                                                <td>
                                       <select name="bill_to" id="bill_to" required class="form-control required" data-live-search="true" onChange="document.frm1.submit();">
                                            <option value="<?=$childlocdet[0]."~".$childlocdet[3]."~".$childlocdet[1]."~".$childlocdet[2]."~".$childlocdet[5]."~".$childlocdet[6]?>"<?php if($childlocdet[0]."~".$childlocdet[3]."~".$childlocdet[1]."~".$childlocdet[2]."~".$childlocdet[5]."~".$childlocdet[6]==$_REQUEST['bill_to']){ echo "selected";}?>>Default</option>
                                            <?php
                                            $sql_billto = "SELECT * FROM delivery_address_master WHERE location_code='".$po_row['po_from']."' AND status='Active'";
                                            $res_billto = mysqli_query($link1, $sql_billto);
                                            while($row_billto = mysqli_fetch_array($res_billto)) {
											?>
                                            <option value="<?=$row_billto['party_name']."~".$row_billto['address']."~".$row_billto['city']."~".$row_billto['state']."~".$row_billto['gstin']."~".$row_billto['pincode']?>" <?php if($row_billto['party_name']."~".$row_billto['address']."~".$row_billto['city']."~".$row_billto['state']."~".$row_billto['gstin']."~".$row_billto['pincode']==$_REQUEST['bill_to']){ echo "selected";}?>><?=$row_billto['party_name']." | ".$row_billto['address']." | ".$row_billto['city']." | ".$row_billto['state']?></option>
                                            <?php
                                            }
                                            ?>
                                        </select></td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><!--close panel body-->
                            </div><!--close panel-->
                            <br><br>
                        </form>
<form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading heading1" style="width: 111%;">Items Information</div>
        <div class="panel-body">
            <table class="table table-bordered" width="100%" id="tbl_scheam">
            <thead>
                <tr class="<?=$tableheadcolor?>">
                    <th style="text-align:center" width="15%">Product</th>
                    <th style="text-align:center" width="10%">Req. Qty</th>
                    <th style="text-align:center" width="10%">Bill Qty</th>
                    <th style="text-align:center" width="10%">Price</th>
                    <th style="text-align:center" width="10%">Value</th>
                    <th style="text-align:center" width="10%">Discount</th>
                    <th style="text-align:center" width="10%">After Discount Value</th>
                    <th style="text-align:center" width="10%">SGST<br>(%)</th>
                    <th style="text-align:center" width="10%">SGST <br>Amt</th>
                    <th style="text-align:center" width="10%">CGST<br>(%)</th>
                    <th style="text-align:center" width="10%">CGST <br>Amt</th>
                    <th style="text-align:center" width="10%">IGST<br>(%)</th>
                    <th style="text-align:center" width="10%">IGST <br>Amt</th>
                    <th style="text-align:center" width="10%">Total</th>
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
                $podata_sql = "SELECT * FROM purchase_order_data WHERE po_no='" . $docid . "'";
                $podata_res = mysqli_query($link1, $podata_sql);
                while($podata_row = mysqli_fetch_assoc($podata_res)){												
					//// get current available stock
					$hsn_code = mysqli_fetch_assoc(mysqli_query($link1,"SELECT bom_hsn,bom_modelname FROM combo_master WHERE bom_modelcode='".$podata_row['prod_code']."'"));
					$gst_data = mysqli_fetch_assoc(mysqli_query($link1,"SELECT sgst,cgst,igst FROM tax_hsn_master WHERE hsn_code='".$hsn_code['bom_hsn']."'"));
					if($childlocdet['2'] == $parentlocdet['2']) {
						$sgst_per = $gst_data['sgst'];
						$cgst_per = $gst_data['cgst'];
						$sgst_amt = number_format((($podata_row['totalval'] * $sgst_per) / 100), '2', '.', '');
						$cgst_amt = number_format((($podata_row['totalval'] * $cgst_per) / 100), '2', '.', '');
						$linetotal = $podata_row['totalval'] + $sgst_amt + $cgst_amt;
					}else{
						$igst_per = $gst_data['igst'];
						$igst_amt = number_format((($podata_row['totalval'] * $igst_per) / 100), '2', '.', '');
						$linetotal = $podata_row['totalval'] + $igst_amt;
					}
					$discount_val = number_format(($podata_row['po_value'] - ($podata_row['discount'])), '2', '.', '');
				?>
                <tr>
                	<td><?=$hsn_code['bom_modelname'].' | '.$podata_row['prod_code']?>
                    	<input type="hidden" name="prodcode<?=$podata_row['id']?>" id="prodcode<?=$i?>" value="<?=$podata_row['prod_code']?>"/>
                        <input type="hidden" name="combo_model<?=$podata_row['id']?>" id="combo_model<?=$i?>" value="<?php echo $podata_row['prod_code']."~".$hsn_code['bom_hsn']."~".$hsn_code['bom_modelname']; ?>"/>
						<?php 
						$p = 0;
						$arr_bomqty = array();
						$arr_bompric = array();
						$arr_avlstk = array();
                        $combo_qry = "SELECT bom_partcode,bom_qty FROM combo_master WHERE bom_modelcode='".$podata_row['prod_code']."' AND status='1'";
                        $combo_res = mysqli_query($link1, $combo_qry);
                        while($combo_row = mysqli_fetch_array($combo_res)){
                            ////// get product combo price
                            $res_cmbprice = mysqli_query($link1,"SELECT combo_price FROM price_master WHERE product_code='".$combo_row["bom_partcode"]."' AND state='".$parentlocdet[2]."' AND location_type='".$childlocdet[11]."' AND status='active'");
                            $row_cmbprice = mysqli_fetch_assoc($res_cmbprice);
                            ///// make qty as per combo model qty
                            $total_prodqty = $combo_row["bom_qty"] * $podata_row['req_qty'];
							$arr_bomqty[]=$total_prodqty;
							$arr_bompric[]=$row_cmbprice["combo_price"];
							///// check available stock  to combo products
							$avlstock = getCurrentStockNew($po_row['po_to'], $_REQUEST['stock_from'], $combo_row["bom_partcode"], "okqty", $link1);
							$arr_avlstk[] = $avlstock;
                        ?>
                       	<select class="form-control" data-live-search="true" name="combopart<?=$podata_row['id']?>_<?=$p?>" id="combopart<?=$i?>_<?=$p?>" required onchange='getAvlStkComboP("<?=$i?>_<?=$p?>","");'>
							<option value="">--None--</option>
							<?php 
							$model_query = "SELECT productname,model_name,productcode FROM product_master WHERE status='Active'";
							$check1 = mysqli_query($link1, $model_query);
							while ($br = mysqli_fetch_array($check1)){
							?>
                            <option value="<?php echo $br['productcode'];?>"<?php if($br['productcode']==$combo_row["bom_partcode"]){ echo "selected";}?>><?php echo $br['productname']." | ".$br['model_name']." | ".$br['productcode']; ?></option>
							<?php } ?>
                  		</select>                        
					<?php $p++;}?>
                    	<input type="hidden" name="noofbomproduct<?=$podata_row['id']?>" id="noofbomproduct<?=$i?>" value="<?=$p?>">
                   	</td>
                   	<td style="text-align:right"><?=$podata_row['req_qty']?>
                        <input name="mrp<?=$podata_row['id']?>" id="mrp<?= $i ?>" type="hidden" value="<?=$podata_row['mrp']?>"/>
                        <input name="holdRate<?= $podata_row['id'] ?>" id="holdRate<?= $i ?>" type="hidden" value="<?=$podata_row['hold_price']?>"/>
                        <input type="hidden" name="req_qty<?= $podata_row['id']?>" id="req_qty<?= $i ?>" value="<?=$podata_row['req_qty']?>"/>
                    </td>
                    <td>
                    	<input type="text" class="form-control digits" name="bill_qty<?=$podata_row['id']?>" id="bill_qty<?=$i?>" value="<?=(int)$podata_row['req_qty']?>" autocomplete="off" required onBlur="rowTotal('<?=$i?>');" onKeyUp="getComboProduct('<?=$i?>');" style="width:45px;text-align:right;padding: 5px;"/>
                        <?php 
						for($j = 0; $j<count($arr_bomqty); $j++){
						?>
                        <input name="combopartqty<?=$podata_row['id']?>_<?=$j?>" id="combopartqty<?=$i?>_<?=$j?>" type="text" class="form-control" value="<?=$arr_bomqty[$j]?>" readonly/>
                        <input type="hidden" name="avl_stock<?=$podata_row['id'];?>_<?=$j?>" id="avl_stock<?=$i?>_<?=$j?>" value="<?=$arr_avlstk[$j]?>"/>
                        <?php 
						}
						?>
                   	</td>
                    <td>
                    	<input type="text" class="form-control number" name="price<?=$podata_row['id']?>" id="price<?=$i?>" onKeyUp="rowTotal('<?=$i?>');" autocomplete="off" required value="<?= $podata_row['po_price'] ?>" style="width:72px;text-align:right;padding: 5px;"/>
                        <input name="poprice<?= $podata_row['id'] ?>" id="poprice<?=$i?>" type="hidden" value="<?=$podata_row['po_price']?>"/>
                        <?php 
						for($k = 0; $k<count($arr_bompric); $k++){
						?>
                        <input name="combopartprice<?=$podata_row['id']?>_<?=$k?>" id="combopartprice<?=$i?>_<?=$k?>" type="text" class="form-control" value="<?=$arr_bompric[$k]?>" style="text-align:right" readonly/>
                        <?php 
						}
						?>
                    </td>
                    <td>
                    	<input type="text" class="form-control" name="value<?=$podata_row['id']?>" id="value<?=$i?>" autocomplete="off" readonly value="<?= $podata_row['po_value'] ?>" style="width:72px;text-align:right;padding: 5px;">
                   	</td>
                    <td>
                    	<input type="text" class="form-control number" name="rowdiscount<?=$podata_row['id']?>" id="rowdiscount<?=$i?>" autocomplete="off" onKeyUp="rowTotal('<?= $i ?>');" value="<?=$podata_row['discount']?>" style="width:72px;text-align:right;padding: 5px;"/>
                   	</td>
                    <td>
                    	<input type="text" class="form-control" name="rowdiscount_val<?=$podata_row['id']?>" id="rowdiscount_val<?=$i?>" autocomplete="off" readonly value="<?=$discount_val?>" style="width:72px;text-align:right;padding: 5px;"/>
                    </td>
                    <td>
                    	<input type="text" class="form-control" name="sgst_per<?= $podata_row['id'] ?>" id="sgst_per<?= $i ?>" readonly value="<?= $sgst_per ?>" style="width:50px;text-align:right;padding: 5px;"/>
                   	</td>
                    <td>
                    	<input type="text" class="form-control" name="sgst_amt<?= $podata_row['id'] ?>" id="sgst_amt<?= $i ?>" readonly value="<?= $sgst_amt ?>" style="width:65px;text-align:right;padding: 5px;"/>
                    </td>
                    <td>
                    	<input type="text" class="form-control" name="cgst_per<?= $podata_row['id'] ?>" id="cgst_per<?= $i ?>" readonly value="<?= $cgst_per ?>" style="width:50px;text-align:right;padding: 5px;"/>
                    </td>
                    <td>
                    	<input type="text" class="form-control" name="cgst_amt<?= $podata_row['id'] ?>" id="cgst_amt<?= $i ?>" readonly value="<?= $cgst_amt ?>" style="width:65px;text-align:right;padding: 5px;"/>
                    </td>
                    <td>
                    	<input type="text" class="form-control" name="igst_per<?= $podata_row['id'] ?>" id="igst_per<?= $i ?>" readonly value="<?= $igst_per ?>" style="width:50px;text-align:right;padding: 5px;"/>
                    </td>
                    <td>
                    	<input type="text" class="form-control" name="igst_amt<?= $podata_row['id'] ?>" id="igst_amt<?= $i ?>" readonly value="<?= $igst_amt ?>" style="width:65px;text-align:right;padding: 5px;"/>
                    </td>
                    <td>
                    	<input type="text" class="form-control" name="total_val<?= $podata_row['id'] ?>" id="total_val<?= $i ?>" autocomplete="off" readonly value="<?= number_format($linetotal, '2', '.', '') ?>" style="width:75px;text-align:right;padding: 5px;">
                    </td>
					<?php /*?><td align="center">
                    <a href="#" onClick="checkScheme('<?=$podata_row['prod_code']?>','<?=$i?>','<?=$podata_row['po_value']?>','<?=$hsn_code['productname'].' | '.$hsn_code['productcolor'].' | '.$podata_row['prod_code']?>');" id="scheme<?=$i?>" title='Applicable Schemes'><i class='fa fa-tags fa-lg'></i></a>
                    <input type="hidden"  name="sch_cd<?= $podata_row['id'] ?>"  id="sch_cd<?=$i?>" value="" /></td><?php */?>
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
            <input type="hidden"  id="count" name="count" value="<?=$i;?>"/>
            <input type="hidden"  id="norow" name="norow" value="0"/>
            <input type="hidden"  id="qtyto" name="qtyto" value="<?=$totqty?>"/>                                                                           
            </tbody>
		</table>
        <span id="newpartrow"></span>
<?php /*?>        <tfoot id='productfooter' style="z-index:-9999;">
        	<tr class="0">
            	<td colspan="12" style="font-size:13px;">
                	<a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a><input type="hidden" name="theValue" id="theValue" value="-1"/>
              	</td>
        	</tr>
    	</tfoot><?php */?>
	</div><!--close panel body-->
</div><!--close panel-->
<div class="panel panel-info table-responsive">
    <div class="panel-heading heading1">Amount Information</div>
    <div class="panel-body">
        <table class="table table-bordered" width="100%">
        	<tbody>
        		<tr>
        			<td width="20%"><label class="control-label">Total Qty</label></td>
        			<td width="30%">
                    	<input type="text" name="total_qty" id="total_qty" class="form-control" value="<?=$totqty;?>" readonly style="width:200px;"/>
                        <input type="hidden" name="total_qty_ext" id="total_qty_ext" class="form-control" readonly value="0"/>
                        <input type="hidden" name="total_qty_add" id="total_qty_add" class="form-control" value="0" readonly style="width:200px;"/>
                        <input type="hidden" name="total_qty1" id="total_qty1" value="0"/>
                    </td>
        			<td width="20%"><label class="control-label">Sub Total</label></td>
        			<td width="30%">
                    	<input type="text" name="sub_total" id="sub_total" class="form-control" value="<?=currencyFormat($subtotal)?>" readonly style="width:200px;text-align:right"/>
                        <input type="hidden" name="sub_total_add" id="sub_total_add" value="0.00"/>
                        <input type="hidden" name="sub_total_ext" id="sub_total_ext" value="0.00"/>
                   	</td>
        		</tr>
        		<tr>
                    <td rowspan="2"><label class="control-label">Delivery Address</label></td>
                    <td rowspan="2">
                    	<textarea name="delivery_address" id="delivery_address" class="form-control required" rows="5"  style="resize:none; width:200px" required><?php echo $po_row['delivery_address']; ?></textarea>
                    </td>
                    <td><label class="control-label">Discount</label></td>
                    <td>
                    	<input type="text" name="total_discount" id="total_discount" class="form-control number" value="<?=$totdiscount?>" style="width:200px;text-align:right" readonly/>
                        <input type="hidden" name="total_discount_add" id="total_discount_add" value="0.00"/>
                        <input type="hidden" name="total_discount_ext" id="total_discount_ext" value="0.00"/>
                  	</td>
                </tr>
                <tr>
                  	<td><label class="control-label">Total SGST</label></td>
                  	<td>
                    	<input type="text" name="total_sgst" id="total_sgst" class="form-control" value="<?= $totsgst ?>" readonly style="width:200px;text-align:right"/>
                        <input type="hidden" name="total_sgst_add" id="total_sgst_add" value="0.00"/>
                        <input type="hidden" name="total_sgst_ext" id="total_sgst_ext" value="0.00"/>
                   	</td>
                </tr>
                <tr>
                	<td height="56" width="20%"><label class="control-label">Ship To</label></td>
                    <td width="30%">
                    	<select name="ship_to" id="ship_to" class="form-control selectpicker" data-live-search="true" style="width:150px;" onChange="getShipToInfo(this.value);">
                            <option value="" selected="selected">Please Select </option>
                            <?php
                            $sql_shipto = "SELECT * FROM delivery_address_master WHERE location_code='".$po_row['po_from']."' AND status='Active'";
                            $res_shipto = mysqli_query($link1, $sql_shipto);
                            while($row_shipto = mysqli_fetch_array($res_shipto)) {
                            ?>
                            <option value="<?=$row_shipto['address_code']."~".$row_shipto['address']."~".$row_shipto['city']."~".$row_shipto['gstin']?>"><?=$row_shipto['party_name']." | ".$row_shipto['address']." | ".$row_shipto['city']." | ".$row_shipto['state']?></option>
                            <?php
                            }
                            ?>
                        </select>
                 	</td>
                    <td  width="20%"><label class="control-label">Total CGST</label></td>
                    <td  width="30%">
                    	<input type="text" name="total_cgst" id="total_cgst" class="form-control" value="<?= $totcgst ?>" readonly style="width:200px;text-align:right"/>
                        <input type="hidden" name="total_cgst_add" id="total_cgst_add" value="0.00"/>
                        <input type="hidden" name="total_cgst_ext" id="total_cgst_ext" value="0.00"/>
                   	</td>
              	</tr>
                <tr>
                    <td><label class="control-label">Ship To GSTIN</label></td>
                    <td><input type="text" name="shipto_gstin" id="shipto_gstin" class="form-control alphanumeric" minlength="15" maxlength="15" style="width:200px;"></td>
                    <td><label class="control-label">Total IGST</label></td>
                    <td>
                    	<input type="text" name="total_igst" id="total_igst" class="form-control" value="<?= $totigst ?>" readonly style="width:200px;text-align:right"/>
                        <input type="hidden" name="total_igst_add" id="total_igst_add" value="0.00"/>
                        <input type="hidden" name="total_igst_ext" id="total_igst_ext" value="0.00"/>
                   	</td>
               	</tr>
                <tr>
                    <td><label class="control-label">Ship To City</label></td>
                    <td><input type="text" name="shipto_city" id="shipto_city" class="form-control mastername" style="width:200px;"></td>
                    <td><label class="control-label">Grand Total</label></td>
                    <td>
                    	<input type="text" name="grand_total" id="grand_total" class="form-control" value="<?php echo currencyFormat($grandtot); ?>" readonly style="width:200px;text-align:right"/>
                        <input type="hidden" name="grand_total_add" id="grand_total_add" value="0.00"/>
                        <input type="hidden" name="grand_total_ext" id="grand_total_ext" value="0.00"/>
                	</td>
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
							<option value="1.0">1 %</option>
                            <?php }?>
                     	</select>
                     </td>
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
                <?php if($_REQUEST['doc_type']=="DC"){ if($_REQUEST['ledger_name']==""){ $btn_enable=0;}else{$btn_enable=1;}}else{$btn_enable=1;} ?>
                <tr>
                	<td colspan="4" align="center">
                    	<input type="submit" class="btn <?=$btncolor?>" name="upd" id="upd" value="Process" title="Make Invoice" <?php if($btn_enable==0 || $_POST["upd"]=="Process"){?> disabled<?php }?>>
                        <input type="hidden" name="doctype" id="doctype" value="<?=$_REQUEST['doc_type']?>"/>
                        <input type="hidden" name="billto" id="billto" value="<?=$_REQUEST['bill_to']?>"/>
                        <input type="hidden" name="ledgername" id="ledgername" value="<?= $_REQUEST['ledger_name'] ?>"/>
                        <input type="hidden" name="stockfrom" id="stockfrom" value="<?= base64_encode($_REQUEST['stock_from'])?>"/>
                        <input type="hidden" name="id" id="id" value="<?= $_REQUEST['id'] ?>"/>
                        <input type="hidden" name="rowno" id="rowno" value="<?= $i ?>"/>
                        <input type="hidden" name="disc_amt" id="disc_amt" value="<?=$totdisc."~".$row_Nom."~".$res_schm['scheme_given_type'];?>"/>
                        <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href = 'comboInvoice.php?<?= $pagenav ?>'">
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
</body>
</html>