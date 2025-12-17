<?php
////// Function ID ///////
$fun_id = array("u"=>array(2)); // User:
require_once("../config/config.php");
require_once("../includes/ledger_function.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$docid = base64_decode($_REQUEST['id']);
$toloctiondet = explode("~", getLocationDetails($_REQUEST['po_to'], "state,id_type,disp_addrs", $link1));
$fromlocationdet  = explode("~", getLocationDetails($_REQUEST['po_from'], "state,id_type,disp_addrs", $link1));
@extract($_POST);
////// if we hit process button
if($_POST){
	if($_POST['upd'] == 'Process') {
	///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
	$messageIdent = md5($_POST['upd'] . $parentcode);
	//and check it against the stored value:
	$sessionMessageIdent = isset($_SESSION['msgdirbilc'])?$_SESSION['msgdirbilc']:'';
	if($messageIdent!=$sessionMessageIdent){//if its different:
		//save the session var:
		$_SESSION['msgdirbilc'] = $messageIdent;
		if($total_qty != '' && $total_qty != 0) {
        	//// Make System generated Invoice no.//////
            $res_cnt = mysqli_query($link1, "SELECT inv_str,inv_counter FROM document_counter WHERE location_code='".$parentcode."'");
            if(mysqli_num_rows($res_cnt)){
            	$row_cnt = mysqli_fetch_array($res_cnt);
                $invcnt = $row_cnt['inv_counter'] + 1;
                $pad = str_pad($invcnt, 4, 0, STR_PAD_LEFT);
                $invno = $row_cnt['inv_str'] . $pad;
                ///////////// auto commit hold
				mysqli_autocommit($link1, false);
                $flag = true;
                $err_msg = "";
				/////
                $splitcompltetax = explode("~", $complete_tax);
                ///// get parent location details
                $parentloc = getLocationDetails($parentcode, "addrs,disp_addrs,name,city,state,gstin_no,pincode,email,phone", $link1);
                $parentlocdet = explode("~", $parentloc);
                ///// get child location details
                $childloc = getLocationDetails($partycode, "addrs,disp_addrs,name,city,state,gstin_no,pincode,email,phone", $link1);
                $childlocdet = explode("~", $childloc);//// explode shipto
				$expl_ship = explode("~", $ship_to);
				if($expl_ship[0]){
					$shiptodet = explode("~",getAnyDetails($expl_ship[0],"party_name,address,city,state,pincode,gstin","address_code","delivery_address_master",$link1));
					$deli_addrs = $shiptodet[1];
				}else if ($delivery_address) {
                    $deli_addrs = $delivery_address;
                } else {
                    $deli_addrs = $childlocdet[1];
                }
                ///// Insert Master Data
              	$query1 = "INSERT INTO billing_master SET from_location='" . $parentcode . "', to_location='" . $partycode . "',sub_location='".$stockfrom."',from_gst_no='".$parentlocdet[5]."', from_partyname='".$parentlocdet[2]."', party_name='".$childlocdet[2]."', to_gst_no='".$childlocdet[5]."', challan_no='" . $invno . "', sale_date='" . $today . "', entry_date='" . $today . "', entry_time='" . $currtime . "', entry_by='" . $_SESSION['userid'] . "', status='Pending', type='RETAIL',billing_type='COMBO', document_type='INVOICE', discountfor='" . $disc_type . "', taxfor='" . $tx_type . "',basic_cost='" . $sub_total_bd . "',discount_amt='" . $total_discount . "',total_sgst_amt='".$total_sgstamt."',total_cgst_amt='".$total_cgstamt."',total_igst_amt='".$total_igstamt."',tax_cost='" . $tax_amount . "',total_cost='" . $grand_total . "',tax_type='" . $splitcompltetax[1] . "',tax_header='" . $splitcompltetax[2] . "',tax='" . $splitcompltetax[0] . "',bill_from='" . $parentcode . "',bill_topty='" . $partycode . "',from_addrs='" . $parentlocdet[0] . "',disp_addrs='" . $parentlocdet[1] . "',to_addrs='" . $childlocdet[0] . "',deliv_addrs='" . $deli_addrs . "',billing_rmk='" . $remark . "',from_state='".$parentlocdet[4]."', to_state='".$childlocdet[4]."', from_city='".$parentlocdet[3]."', to_city='".$childlocdet[3]."', from_pincode='".$parentlocdet[6]."', to_pincode='".$childlocdet[6]."', from_phone='".$parentlocdet[8]."', to_phone='".$childlocdet[8]."', from_email='".$parentlocdet[7]."', to_email='".$childlocdet[7]."',round_off='".$round_off."',tcs_per='".$tcs_per."', tcs_amt='".$tcs_amt."',ship_to='".$shiptodet[0]."',ship_to_gstin='".$shiptodet[5]."',ship_to_city='".$shiptodet[2]."',ship_to_state='".$shiptodet[3]."',ship_to_pincode='".$shiptodet[4]."',sale_person='".$sales_executive."'";
              	$result1 = mysqli_query($link1, $query1);
                //// check if query is not executed
                if (!$result1) {
                    $flag = false;
                    $err_msg = "Error Code1: ".mysqli_error($link1);
                }
                /// update invoice counter /////
               	$result2 = mysqli_query($link1, "UPDATE document_counter SET inv_counter=inv_counter+1,update_by='" . $_SESSION['userid'] . "',updatedate='" . $datetime . "' WHERE location_code='" . $parentcode . "'");
                //// check if query is not executed
                if (!$result2) {
                    $flag = false;
                    $err_msg = "Error Code2: ".mysqli_error($link1);
                }
				$arr_taxx = array();
				$arr_val = array();
				$gst_type = "";
                ///// Insert in item data by picking each data row one by one
                foreach ($combo_model as $k => $val) {
					///// get combo model details
					$combo_det = explode("~",$val);
					///// get total no. of combo product at one line of item
					$numrow = $noofbomproduct[$k];
					for($p=0; $p<$numrow; $p++){
						$comboprod = "combopart".$k."_".$p;
						$comboprodqty = "combopartqty".$k."_".$p;
						$comboprodprice = "combopartprice".$k."_".$p;
						$getstk = getCurrentStockNew($parentcode,$stockfrom, $_POST[$comboprod], "okqty", $link1);
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
                    	if ($_POST[$comboprod] != '' && $_POST[$comboprodqty] != '' && $_POST[$comboprodqty] != 0) {
							$linetotal = $_POST[$comboprodqty] * $_POST[$comboprodprice];
							$sgst_amt = round(($rowsgstper[$k] * $linetotal)/100,2);
							$cgst_amt = round(($rowcgstper[$k] * $linetotal)/100,2);
							$igst_amt = round(($rowigstper[$k] * $linetotal)/100,2);
							$totalvalue = $linetotal + $sgst_amt + $cgst_amt + $igst_amt;
							
							/////////// insert data for products of each combo product
							$query_cp = "insert into billing_model_data set from_location='" . $parentcode . "', prod_code='" . $_POST[$comboprod] . "', combo_code='".$combo_det[0]."', combo_name='".$combo_det[2]."', qty='" . $_POST[$comboprodqty] . "', okqty='" . $_POST[$comboprodqty] . "',mrp='" . $mrp[$k] . "', price='" . $_POST[$comboprodprice] . "', hold_price='" . $_POST[$comboprodprice] . "', value='" . $linetotal . "',tax_name='',tax_per='', tax_amt='',discount='', totalvalue='" . $totalvalue . "',challan_no='" . $invno . "' ,sale_date='" . $today . "',entry_date='" . $today . "' ,sgst_per='".$rowsgstper[$k]."' ,sgst_amt='".$sgst_amt."',igst_per='".$rowigstper[$k]."' ,igst_amt='".$igst_amt."',cgst_per='".$rowcgstper[$k]."' ,cgst_amt='".$cgst_amt."'";
							$result_cp = mysqli_query($link1, $query_cp);
							//// check if query is not executed
							if (!$result_cp) {
								$flag = false;
								$err_msg = "Error Code4: Combo Product saving ".mysqli_error($link1);
							}
							//// update stock of from loaction
							$result3 = mysqli_query($link1, "update stock_status set okqty=okqty-'" . $_POST[$comboprodqty] . "',updatedate='" . $datetime . "' where asc_code='".$parentcode."' and sub_location='".$stockfrom."' and partcode='" . $_POST[$comboprod] . "'");
							//// check if query is not executed
							if (!$result3) {
								$flag = false;
								$err_msg = "Error Code5: ".mysqli_error($link1);
							}
							///// update stock ledger table
							$flag = stockLedger($invno, $today, $_POST[$comboprod], $stockfrom, $partycode, $stockfrom, "OUT", "OK", "Combo Invoice", $_POST[$comboprodqty], $_POST[$comboprodprice], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
						}	
					}/// close combo product for loop
                    /*// checking row value of product and qty should not be blank
                    $getstk = getCurrentStock($parentcode, $prod_code[$k], "okqty", $link1);
                    //// check stock should be available ////
                    if ($getstk < $bill_qty[$k]) {
                        $flag = false;
                        $err_msg = "Error Code3: Stock is not available";
                    } else {
                        
                    }*/
                    // checking row value of product and qty should not be blank
                    if ($combo_det[0] != '' && $bill_qty[$k] != '' && $bill_qty[$k] != 0) {
						//$combo_value = $price[$k]*$bill_qty[$k];
						////////////////////
						if($rowsgstper[$k]!="" && $rowsgstper[$k]!="0.00"  && $rowsgstper[$k]!="0"){
							$gstper = $rowsgstper[$k] + $rowcgstper[$k];					
							$arr_taxx[$gstper] += $rowsgstamount[$k] + $rowcgstamount[$k];
							$arr_val[$gstper] += $total_beforedisc[$k];
							$gst_type = "SGST-CGST";
						}else{
							$gstper = $rowigstper[$k];					
							$arr_taxx[$gstper] += $rowigstamount[$k];
							$arr_val[$gstper] += $total_beforedisc[$k];
							$gst_type = "IGST";
						} 
                    	/////////// insert data for combo model
                    	$query2 = "insert into billing_model_data set from_location='" . $parentcode . "', prod_code='" . $combo_det[0] . "', combo_code='".$combo_det[0]."', combo_name='".$combo_det[2]."',prod_cat='C', qty='" . $bill_qty[$k] . "', okqty='" . $bill_qty[$k] . "',mrp='" . $mrp[$k] . "', price='" . $price[$k] . "', hold_price='" . $holdRate[$k] . "', value='" . $total_beforedisc[$k] . "',tax_name='',tax_per='', tax_amt='',discount='" . $rowdiscount[$k] . "', totalvalue='" . $total_val[$k] . "',challan_no='" . $invno . "' ,sale_date='" . $today . "',entry_date='" . $today . "' ,sgst_per='".$rowsgstper[$k]."' ,sgst_amt='".$rowsgstamount[$k]."',igst_per='".$rowigstper[$k]."' ,igst_amt='".$rowigstamount[$k]."',cgst_per='".$rowcgstper[$k]."' ,cgst_amt='".$rowcgstamount[$k]."'";
						$result4 = mysqli_query($link1, $query2);
                        //// check if query is not executed
                        if (!$result4) {
                            $flag = false;
                            $err_msg = "Error Code4.1: Combo model saving ".mysqli_error($link1);
                        }
                        /*//// update stock of from loaction
                        $result = mysqli_query($link1, "update stock_status set okqty=okqty-'" . $bill_qty[$k] . "',updatedate='" . $datetime . "' where asc_code='" . $parentcode . "' and partcode='" . $combo_det[0] . "'");
                        //// check if query is not executed
                        if (!$result) {
                            $flag = false;
                            $err_msg = "Error Code5.1: ".mysqli_error($link1);
                        }
                        ///// update stock ledger table
                       	$flag = stockLedger($invno, $today, $combo_det[0], $parentcode, $partycode, $parentcode, "OUT", "OK", "Combo Invoice", $bill_qty[$k], $price[$k], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);*/
					}///close if loop
				}/// close foreach loop
                //// update cr bal of child location
                $result5 = mysqli_query($link1, "update current_cr_status set cr_abl=cr_abl-'" . $grand_total . "',total_cr_limit=total_cr_limit-'" . $grand_total . "', last_updated='" . $datetime . "' where parent_code='" . $parentcode . "' and asc_code='" . $partycode . "'");
                //// check if query is not executed
                if (!$result5) {
                    $flag = false;
                    $err_msg = "Error Code6: ".mysqli_error($link1);
                }
                ////// maintain party ledger////
                $flag = partyLedger($parentcode, $partycode, $invno, $today, $today, $currtime, $_SESSION['userid'], "COMBO INVOICE", $grand_total, "DR", $link1, $flag);
                ////// insert in activity table////
                $flag = dailyActivity($_SESSION['userid'], $invno, "COMBO INVOICE", "ADD", $ip, $link1, $flag);
				/////// make account ledger entry for location
				/////// start ledger entry for tally purpose ///// written by shekhar on 21 july 2022
				///// make ledger array which are need to be process
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
				$resp = explode("~",storeLedgerTransaction($parentcode,$invno,$today,"2","Sale",$arr_taxx,$arr_val,$tcs_per,$tcs_amt,$round_off,$gst_type,$arr_ldg_name,"GST Sales","GST Sales Account",$link1,$flag));
				$flag = $resp[0];
				$error_msg = $resp[1];
				/////// end ledger entry for tally purpose ///// written by shekhar on 21 july 2022
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
        } else {
            $msg = "Request could not be processed . Please dispatch some qty.";
        }
		}else {
		//you've sent this already!
		$msg="You have saved this already ";
		$cflag = "warning";
		$cmsg = "Warning";
	}	
        ///// move to parent page
        header("location:retailbillinglist.php?msg=" . $msg . "" . $pagenav);
       exit;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
        <title><?= siteTitle ?></title>
        <script src="../js/jquery.js"></script>
        <link href="../css/font-awesome.min.css" rel="stylesheet">
        <link href="../css/abc.css" rel="stylesheet">
        <script src="../js/bootstrap.min.js"></script>
        <link href="../css/abc2.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/bootstrap-select.min.css">
        <script src="../js/bootstrap-select.min.js"></script>
		<link href='../css/select2.min.css' rel='stylesheet' type='text/css'>
		<script src='../js/select2.min.js'></script>
        <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
        <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#myTable').dataTable();
				$('#ship_to').selectpicker({
					width: "200px",
				});
            });
            $(document).ready(function() {
                $("#frm2").validate();
            });
			$(document).ready(function(){
				$('[data-toggle="popover"]').popover({
					trigger : 'hover',
					html : true
				});   
			});
			function rePop(){
				$('[data-toggle="popover"]').popover({
					trigger : 'hover',	
					html : true
				}); 
			}
        </script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/common_js.js"></script>
        <script>
			var list_prod = new Array; ///this one way of declaring array in javascript
			//////// function to get combo product
            function getComboProduct(indx) {
                var comboModel = (document.getElementById("combo_model[" + indx + "]").value).split("~");
				var comboModelQty = document.getElementById("bill_qty[" + indx + "]").value;
                var locationCode = $('#po_from').val();
				var tolocation = document.getElementById("toloctionstate").value;
				var fromlocation = document.getElementById("fromloctionstate").value;   
				var fromidtype = document.getElementById("fromidtype").value  ;
                $.ajax({
                    type: 'post',
                    url: '../includes/getAzaxFields.php',
                    data: {combModel: comboModel[0], combModelQty: comboModelQty, loccode: locationCode, idtype: fromidtype, fromstate:fromlocation, indxx: indx},
                    success: function(data) {
						//// split response
                        var getdata = data.split("~");
						// Display the array elements
						var comboProd = JSON.parse(getdata[0]);
						$("#combo_prodlist"+getdata[1]+"").html("");
						$("#combo_prodqty"+getdata[1]+"").html("");
						$("#combo_prodprice"+getdata[1]+"").html("");
						var totprice = 0.00;
						for(var i = 0; i < comboProd.length; i++){
							var newindx = getdata[1]+"_"+i;
							///// split product and its combo qty
							var combopart = comboProd[i].split("^");
							//var productlist = '<input name="combopart'+newindx+'" id="combopart'+newindx+'" type="text" class="form-control" value="'+combopart[3]+'~'+combopart[0]+'" readonly/>';
							var productlist = '<select class="form-control" data-live-search="true" name="combopart'+newindx+'" id="combopart'+newindx+'" required onchange=getAvlStk("'+newindx+'","'+getdata[1]+'");><option value="">--None--</option><?php $model_query = "SELECT productcode,productname,productcolor FROM product_master WHERE status='Active'";$check1 = mysqli_query($link1, $model_query);while ($br = mysqli_fetch_array($check1)) {?><option data-tokens="<?php echo $br['productname']." | ".$br['productcode']; ?>" value="<?php echo $br['productcode'];?>"><?php echo $br['productname']." | ".$br['productcode']; ?></option><?php } ?></select>';
							var productqty = '<input name="combopartqty'+newindx+'" id="combopartqty'+newindx+'" type="text" class="form-control" value="'+combopart[1]+'" readonly/>';
							var productprice = '<input name="combopartprice'+newindx+'" id="combopartprice'+newindx+'" type="text" class="form-control" value="'+combopart[2]+'" style="text-align:right" readonly/>';
							$("#combo_prodlist"+getdata[1]+"").append(productlist);
							$("#combo_prodqty"+getdata[1]+"").append(productqty);
							$("#combo_prodprice"+getdata[1]+"").append(productprice);
							document.getElementById("combopart"+newindx).value = combopart[0];
							list_prod[combopart[0]] = parseInt(combopart[1]);
							totprice += parseFloat(combopart[2]*combopart[1]);
						}
						document.getElementById("price["+getdata[1]+"]").value = totprice;
						document.getElementById("noofbomproduct["+getdata[1]+"]").value = comboProd.length;
						get_price(getdata[1]);
						//console.log(list_prod);
                        //document.getElementById("combo_prodlist"+getdata[1]+"").innerHTML = productlist;
                    }
                });
            }
			/////////// function to get available stock of ho
            function getAvlStk(indx,mainindx) {
				//alert(indx);
               /* var productCode = document.getElementById("combopart"+indx+"").value;
                var locationCode = $('#po_from').val();
                var stocktype = "okqty";
                $.ajax({
                    type: 'post',
                    url: '../includes/getAzaxFields.php',
                    data: {locstk: productCode, loccode: locationCode, stktype: stocktype, indxx: indx},
                    success: function(data) {
                        var getdata = data.split("~");
                        document.getElementById("avl_stock[" + getdata[1] + "]").value = getdata[0];
                    }
                });*/
				get_comboproductprice(indx,mainindx);
            }
			///// function to get price of combo product
            function get_comboproductprice(ind,mainind) {
				//alert(ind);
                var productCode = document.getElementById("combopart"+ind+"").value;
				var billingfrom = $('#po_from').val();		
				var billingto  =  $("#po_to").val();
                var tolocation = document.getElementById("toloctionstate").value;
				var fromlocation = document.getElementById("fromloctionstate").value;   
				var fromidtype = document.getElementById("fromidtype").value  ;   
                $.ajax({
                    type: 'post',
                    url: '../includes/getAzaxFields.php',
                    data: {productinfo: productCode, idtype: fromidtype, fromstate:fromlocation},
                    success: function(data) {
                        var splitprice = data.split("~");
						document.getElementById("combopartprice"+ind+"").value = splitprice[7];
						getSumOfProductPrice(mainind);
                    }
                });	
            }
			function getSumOfProductPrice(mainindx){
				var comboModelQty = document.getElementById("bill_qty["+mainindx+"]").value;
				var noofbompart = document.getElementById("noofbomproduct["+mainindx+"]").value;
				var totprice = 0.00;
				for(var i=0; i<noofbompart; i++){
					var newindx = mainindx+"_"+i;
					var qty = document.getElementById("combopartqty"+newindx+"").value;
					var price = document.getElementById("combopartprice"+newindx+"").value;
					totprice += parseInt(qty)*parseFloat(price);
				}
				document.getElementById("price["+mainindx+"]").value = totprice;
				get_price(mainindx);
			}
        </script>
        <script>
            ///// function to get price of product
            function get_price(ind) {
                var comboModel = (document.getElementById("combo_model[" + ind + "]").value).split("~");
				var billingfrom = $('#po_from').val();		
				var billingto  =  $("#po_to").val();		
                var tolocation = document.getElementById("toloctionstate").value;
				var fromlocation = document.getElementById("fromloctionstate").value;
				var fromidtype = document.getElementById("fromidtype").value;
				var toidtype = document.getElementById("toidtype").value; 
                $.ajax({
                    type: 'post',
                    url: '../includes/getAzaxFields.php',
                    data: {combomodelinfo: comboModel[0], combomodelhsn: comboModel[1], idtype: toidtype, fromstate:tolocation},
                    success: function(data) {
                        var splitprice = data.split("~");
						//document.getElementById("price[" + ind + "]").value = splitprice[3];				
						if ((tolocation == fromlocation) ){ ///// for new customer //////////////////////////////////////////////////////////
                            document.getElementById("rowsgstper[" + ind + "]").value = splitprice[0];
                            document.getElementById("rowcgstper[" + ind + "]").value = splitprice[1];
                            $("#rowigstper[" + ind + "]").value = '0';
						}
						else {					
                            $("#rowsgstper[" + ind + "]").value = '0';
                            $("#rowcgstper[" + ind + "]").value = '0';
                            document.getElementById("rowigstper[" + ind + "]").value = splitprice[2];
                        }
						rowTotal(ind);
                    }
                });
				
			
				
            }
        </script>
        <script>

            $(document).ready(function() {
                $("#add_row").click(function() {
                    var numi = document.getElementById('rowno');
                    var itm = "combo_model[" + numi.value + "]";
                    var qTy = "bill_qty[" + numi.value + "]";
                    var preno = document.getElementById('rowno').value;
                    var num = (document.getElementById("rowno").value - 1) + 2;
                    if ((document.getElementById(itm).value != "" && document.getElementById(qTy).value != "" && document.getElementById(qTy).value != "0") || ($("#addr" + numi.value + ":visible").length == 0)) {
                        numi.value = num;
                        var r = '<tr id="addr'+num+'"><td><select name="combo_model['+num+']" id="combo_model['+num+']"  class="form-control selectpicker" required data-live-search="true" onChange="getComboProduct('+num+');" style="width:150px;padding-right:100px;"><option value="">--None--</option><?php $model_query = "select bom_modelcode, bom_modelname, bom_hsn FROM combo_master WHERE status='1' GROUP BY bom_modelcode";$check1 = mysqli_query($link1, $model_query);while ($br = mysqli_fetch_array($check1)) {?><option value="<?php echo $br['bom_modelcode']."~".$br['bom_hsn']."~".$br['bom_modelname']; ?>"><?php echo $br['bom_modelname']." | ".$br['bom_modelcode']; ?></option><?php } ?></select><input type="hidden" name="noofbomproduct['+num+']" id="noofbomproduct['+num+']"><span id="combo_prodlist'+num+'"></span></td><td style="text-align:right"><input type="text" class="form-control required digits" name="bill_qty['+num+']" id="bill_qty['+num+']" autocomplete="off" required onKeyUp="getComboProduct('+num+');" value="1" style="width:80px;text-align:right"><span id="combo_prodqty'+num+'"></span></td><td><input type="text" class="form-control required number" name="price['+num+']" id="price['+num+']" onKeyUp="rowTotal('+num+');" autocomplete="off" required style="width:80px;text-align:right"><span id="combo_prodprice'+num+'"></span></td><td><input type="text" class="form-control number" name="rowdiscount['+num+']" id="rowdiscount['+num+']" autocomplete="off" <?php if ($_REQUEST['discount_type'] != "PD") { ?> disabled="disabled"<?php } ?> onKeyUp="rowTotal('+num+');" style="width:80px;text-align:right"></td><td><input name="total_beforedisc['+num+']" id="total_beforedisc['+num+']" type="hidden"/><input type="text" class="form-control" name="rowsubtotal['+num+']" id="rowsubtotal['+num+']" onKeyPress="return onlyFloatNum(this.value);" autocomplete="off" value="" style="width:100px;text-align:right" readonly></td><td><?php if($fromlocationdet[0]==$toloctiondet[0]){ ?><div class="row"><div class="col-md-4"><input type="text" class="form-control" name="rowsgstper['+num+']" id="rowsgstper['+num+']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div class="col-md-4"><input type="text" class="form-control" name="rowsgstamount['+num+']" id="rowsgstamount['+num+']" value="0" readonly style="width:80px;text-align:right;padding: 4px"></div></div><div class="row"><div class="col-md-4"><input type="text" class="form-control" name="rowcgstper['+num+']" id="rowcgstper['+num+']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div class="col-md-4"><input type="text" class="form-control" name="rowcgstamount['+num+']" id="rowcgstamount['+num+']" value="0" readonly style="width:80px;text-align:right;padding: 4px"></div></div><?php }else{?><div class="row"><div class="col-md-4"><input type="text" class="form-control" name="rowigstper['+num+']" id="rowigstper['+num+']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div class="col-md-4"><input type="text" class="form-control" name="rowigstamount['+num+']" id="rowigstamount['+num+']" value="0" readonly style="width:60px;text-align:right;padding: 4px"></div></div><?php }?></td><td><input type="text" class="form-control" name="total_val['+num+']" id="total_val['+num+']" autocomplete="off" readonly  style="width:120px;text-align:right"><input type="hidden" name="avl_stock['+num+']" id="avl_stock['+num+']"><input name="mrp['+num+']" id="mrp['+num+']" type="hidden"/><input name="holdRate['+num+']" id="holdRate['+num+']" type="hidden"/></td></tr>';

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
        </script>
        <script>

            function fun_remove(con)
            {
                var c = document.getElementById('addr' + con);
                c.parentNode.removeChild(c);
                con--;
                document.getElementById('rowno').value = con;
                rowTotal(con);

            }
            ////// delete product row///////////
            /*function deleteRow(ind){  
             //$("#addr"+(indx)).html(''); 
             var numi = document.getElementById('rowno');
             var num = (document.getElementById("rowno").value +1)- 2;
             numi.value=num;alert(num);
             
             var id="addr"+ind; 
             var itemid="prod_code"+"["+ind+"]";
             var qtyid="req_qty"+"["+ind+"]";
             var rateid="price"+"["+ind+"]";
             var totalid="total_val"+"["+ind+"]";
             var lineTotal="linetotal["+ind+"]";
             var mrpid="mrp"+"["+ind+"]";
             var holdRateid="holdRate"+"["+ind+"]";
             var discountField="rowdiscount["+ind+"]";
             var abl_qtyid="avl_stock"+"["+ind+"]";
             // hide fieldset \\
             document.getElementById(id).style.display="none";
             // Reset Value\\
             // Blank the Values \\
             document.getElementById(itemid).value="";
             document.getElementById(lineTotal).value="0.00";
             document.getElementById(qtyid).value="0.00";
             document.getElementById(rateid).value="0.00";
             document.getElementById(totalid).value="0.00";
             document.getElementById(mrpid).value="0.00";
             document.getElementById(holdRateid).value="0.00";
             document.getElementById(discountField).value="0.00";
             document.getElementById(abl_qtyid).value="0.00";
             
             rowTotal(ind);
             }*/
        </script>

        <script type="text/javascript">
            /////// calculate line total /////////////
            function rowTotal(ind) {
                var ent_qty = "bill_qty" + "[" + ind + "]";
                var ent_rate = "price" + "[" + ind + "]";
				var tot_bd = "total_beforedisc" + "[" + ind + "]";
                var hold_rate = "holdRate" + "[" + ind + "]";
                var prodmrpField = "mrp" + "[" + ind + "]";
                var discountField = "rowdiscount" + "[" + ind + "]";
                var totalvalField = "total_val" + "[" + ind + "]";
                var st = "rowsubtotal" + "[" + ind + "]";
				<?php if($fromlocationdet[0]==$toloctiondet[0]){ ?>
              	var rowsgstper = "rowsgstper" + "[" + ind + "]";
                var rowcgstper = "rowcgstper" + "[" + ind + "]";
				var rowsgstamount = "rowsgstamount" + "[" + ind + "]";
                var rowcgstamount = "rowcgstamount" + "[" + ind + "]";
				<?php }else{ ?>
                var rowigstper = "rowigstper" + "[" + ind + "]";
				var rowigstamount = "rowigstamount" + "[" + ind + "]";
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
				<?php if($fromlocationdet[0]==$toloctiondet[0]){ ?>
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
				<?php } else{?>
                // check if igst per
                if (document.getElementById(rowigstper).value) {
                    var igstper = (document.getElementById(rowigstper).value);
                } else {
                    var igstper = 0.00;
                }
				<?php }?>				
                    //if (parseFloat(price) >= parseFloat(dicountval)) {
                        var total = parseFloat(qty) * parseFloat(price);
						document.getElementById(tot_bd).value = formatCurrency(total);
                        //var totalcost = (parseFloat(qty) * (parseFloat(price)) - parseFloat(dicountval));
						//var total = parseFloat(price);
                        var totalcost = ((parseFloat(total)) - parseFloat(dicountval));
						<?php if($fromlocationdet[0]==$toloctiondet[0]){ ?>
						var sgst_amt = ((totalcost * sgstper) / 100);
                        var cgst_amt = ((totalcost * cgstper) / 100);
						<?php }else{?>
                        var igst_amt = ((totalcost * igstper) / 100);
						<?php }?>
                        //// calculate row wise discount                
                        document.getElementById(st).value = formatCurrency(totalcost);
						<?php if($fromlocationdet[0]==$toloctiondet[0]){ ?>
                        document.getElementById(rowsgstamount).value = formatCurrency(sgst_amt);
                        document.getElementById(rowcgstamount).value = formatCurrency(cgst_amt);
                        var tot = parseFloat(totalcost) + parseFloat(sgst_amt) + parseFloat(cgst_amt);
                        <?php }else{?>
                         document.getElementById(rowigstamount).value = formatCurrency(igst_amt);
                         var tot = parseFloat(totalcost) + parseFloat(igst_amt);
                        <?php } ?>
                        document.getElementById(totalvalField).value = formatCurrency(parseFloat(tot));
                        calculatetotal();
                    /*} else {
                        alert("Discount is exceeding from price");
                        var total = parseFloat(qty) * parseFloat(price);
                        var var3 = "linetotal" + "[" + ind + "]";
                        document.getElementById(var3).value = formatCurrency(total);
                        document.getElementById(discountField).value = "0.00";
                        document.getElementById(totalvalField).value = formatCurrency(total);
                        calculatetotal();
                    }*/

            }
            ////// calculate final value of form /////
            function calculatetotal() {
                var rowno1 = (document.getElementById("rowno").value);
                var sum_qty = 0;
                var sum_total = 0.00;
				var sum_totalbd = 0.00;
                var sum_discount = 0.00;
                var sum_tax = 0.00;
				var sum_sgst = 0.00;
				var sum_cgst = 0.00;
				var sum_igst = 0.00;
                var sum = 0.00;
                for (var i = 0; i <= rowno1; i++) {
                    var temp_qty = "bill_qty" + "[" + i + "]";
					var temp_totbd = "total_beforedisc" + "[" + i + "]"; 
                    var temp_discount = "rowdiscount" + "[" + i + "]";   
					var temp_total = "rowsubtotal" + "[" + i + "]";
					<?php if($fromlocationdet[0]==$toloctiondet[0]){ ?>
					var temp_sgst = "rowsgstamount" + "[" + i + "]";					          
					var temp_cgst = "rowcgstamount" + "[" + i + "]";	
					<?php }else{?>				          
					var temp_igst = "rowigstamount" + "[" + i + "]";					          
					<?php }?>
                    var total_amt = "total_val" + "[" + i + "]";
                    var discountvar = 0.00;
					var totalbdvar = 0.00;
                    var totalamtvar = 0.00;
                    var total = 0.00;
					var  total_tax = 0.00;
					var  total_sgst = 0.00;
					var  total_cgst = 0.00;
					var  total_igst = 0.00;
                    ///// check if discount value is something
                    if (document.getElementById(temp_discount).value) {
                        discountvar = document.getElementById(temp_discount).value;
                    } else {
                        discountvar = 0.00;
                    }
					if (document.getElementById(temp_totbd).value) {
                        totalbdvar = document.getElementById(temp_totbd).value;
                    } else {
                        totalbdvar = 0.00;
                    }                 
                    ///// check if line qty is something
                    if (document.getElementById(temp_qty).value) {
                        totqty = document.getElementById(temp_qty).value;
                    } else {
                        totqty = 0;
                    } 
					///// check if line taxaable amount is something
                    if (document.getElementById(temp_total).value) {
                        total_tax = document.getElementById(temp_total).value;
                    } else {
                        total_tax = 0.00;
                    } 
					///// check if line taxaable amount is something
					<?php if($fromlocationdet[0]==$toloctiondet[0]){ ?>
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
					<?php }else{?>
					if (document.getElementById(temp_igst).value) {
                        total_igst = document.getElementById(temp_igst).value;
                    } else {
                        total_igst = 0.00;
                    }
					<?php }?>
					                 
                    ///// check if line total amount is something
                    if (document.getElementById(total_amt).value) {
                        total = document.getElementById(total_amt).value;
                    } else {
                        total = 0.00;
                    }

                    sum_qty += parseFloat(totqty);
					sum_totalbd += parseFloat(totalbdvar);
					sum_discount += parseFloat(discountvar);
                    sum_total += parseFloat(total);
                  	//  sum_discount += parseFloat(discountvar) ;
				  	sum_tax += parseFloat(total_tax);//// total taxable amt
                    <?php if($fromlocationdet[0]==$toloctiondet[0]){ ?>
					sum_sgst += parseFloat(total_sgst);
					sum_cgst += parseFloat(total_cgst);
					<?php }else{?>
					sum_igst += parseFloat(total_igst);
					<?php }?>

                }/// close for loop
                document.getElementById("total_qty").value = sum_qty;
				document.getElementById("sub_total_bd").value = formatCurrency(sum_totalbd);//// total before discount
                document.getElementById("sub_total").value = formatCurrency(sum_tax);//// total taxable amt
<?php if ($_REQUEST['discount_type'] == "PD") { ?>
                    document.getElementById("total_discount").value = formatCurrency(sum_discount);
<?php } ?>
<?php //if ($_REQUEST['tax_type'] == "PT") { ?>
                    document.getElementById("tax_amount").value = formatCurrency(sum_sgst+sum_cgst+sum_igst);
<?php //} ?>
				<?php if($fromlocationdet[0]==$toloctiondet[0]){ ?>
				 document.getElementById("total_sgstamt").value = formatCurrency(sum_sgst);
				 document.getElementById("total_cgstamt").value = formatCurrency(sum_cgst);
				 <?php }else{?>
				 document.getElementById("total_igstamt").value = formatCurrency(sum_igst);
				 <?php }?>
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

            ///// check total discount is exceeding from total minimum price of all product
            function check_total_discount() {
                if (parseFloat(document.getElementById("sub_total").value) < parseFloat(document.getElementById("total_discount").value)) {
                    alert("Discount is exceeding..!!");
                    document.getElementById("total_discount").value = "0.00";
                    document.getElementById("grand_total").value = formatCurrency(parseFloat(document.getElementById("sub_total").value));
                } else {

                    document.getElementById("grand_total").value = formatCurrency(parseFloat(document.getElementById("sub_total").value) - parseFloat(document.getElementById("total_discount").value) + parseFloat(document.getElementById("tax_amount").value));
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

                var dis = document.getElementById("total_discount").value;
                if (dis) {
                    var disc = dis
                } else {
                    var disc = 0.00;
                }

                var calculateTax = (parseFloat(completeTax) * (parseFloat(document.getElementById("sub_total").value) - parseFloat(disc))) / 100;
                document.getElementById("tax_amount").value = formatCurrency(calculateTax);

                document.getElementById("grand_total").value = formatCurrency(parseFloat(document.getElementById("sub_total").value) - parseFloat(disc) + parseFloat(calculateTax));
            }
			function getShipToInfo(val){
				var shipto = val.split("~");
				$("#shipto_gstin").val(shipto[3]);
				$("#shipto_city").val(shipto[2]);
				$("#delivery_address").val(shipto[1]);
			}
$(document).ready(function(){
	$("#po_to").select2({
  		ajax: {
   			url: "../includes/getAzaxFields.php",
			type: "post",
			dataType: 'json',
			delay: 250,
   			data: function (params) {
    			return {
					searchCust: params.term, // search term
					requestFor: "billto",
					userid: '<?=$_SESSION['userid']?>',
					po_from: '<?=$_REQUEST['po_from']?>'
    			};
   			},
   			processResults: function (response) {
     			return {
        			results: response
     			};
   			},
   			cache: true
  		}
	});
});
        </script>

    </head>
    <body onKeyPress="return keyPressed(event);">
        <div class="container-fluid">
            <div class="row content">
                <?php
                include("../includes/leftnav2.php");
                ?>
                <div class="col-sm-9">
                    <h2 align="center"><i class="fa fa-object-group"></i> Combo Billing </h2><br/>
                    <div class="form-group" id="page-wrap" style="margin-left:10px;">
                        <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
                            <div class="form-group">
                                <div class="col-md-12"><label class="col-md-3 control-label">Billing  From<span style="color:#F00">*</span></label>
                                    <div class="col-md-6">
                                        <select name="po_from" id="po_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                                            <option value="" selected="selected">Please Select </option>
                                            <?php
                                            $sql_parent = "select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y' AND id_type IN ('HO','BR')";
                                            $res_parent = mysqli_query($link1, $sql_parent);
                                            while ($result_parent = mysqli_fetch_array($res_parent)) {

                                                $party_det = mysqli_fetch_array(mysqli_query($link1, "select name , city, state,id_type from asc_master where asc_code='" . $result_parent['location_id'] . "'"));
                                                ?>
                                                <option data-tokens="<?= $party_det['name'] . " | " . $result_parent['uid'] ?>" value="<?= $result_parent['location_id']?>" <?php if ($result_parent['location_id'] == $_REQUEST['po_from']) echo "selected"; ?> >
                                                    <?= $party_det['name'] . " | " . $party_det['city'] . " | " . $party_det['state'] . " | " . $result_parent['location_id'] ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>

                                    </div>
                                </div>
                            </div>				
                            <div class="form-group">
                                <div class="col-md-12"><label class="col-md-3 control-label">Billing To<span style="color:#F00">*</span></label>
                                    <div class="col-md-6">
                                    	<select name="po_to" id="po_to" class="form-control required">
        
                                            <option value=''>--Please Select--</option>
                                            <?php
											if(isset($_POST["po_to"])){
											  $loc_name = explode("~",getAnyDetails($_POST["po_to"],"name, city, state","asc_code","asc_master",$link1));
                                            echo '<option value="'.$_POST["po_to"].'" selected>'.$loc_name[0].' | '.$loc_name[1].' | '.$loc_name[2].' | '.$_POST["po_to"].'</option>';
											}
                                            ?>
        
                                        </select>
                                        <?php /*?><select name="po_to" id="po_to" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                                            <option value="" selected="selected">Please Select </option>
                                            <?php
                                        $sql_chl = "select mapped_code,uid from mapped_master where uid='" . $_REQUEST['po_from'] . "' and status='Y'";
										
                                            $res_chl = mysqli_query($link1, $sql_chl);
                                            while ($result_chl = mysqli_fetch_array($res_chl)) {
                                                $party_det = mysqli_fetch_array(mysqli_query($link1, "select name , city, state,id_type from asc_master where asc_code='" . $result_chl['mapped_code'] . "'"));
                                                if ($party_det[id_type] != 'HO') {
                                                    ?>
                                                    <option data-tokens="<?= $party_det['name'] . " | " . $result_chl['uid'] ?>" value="<?= $result_chl['mapped_code']?>" <?php if ($result_chl['mapped_code'] == $_REQUEST['po_to']) echo "selected"; ?>>
                                                        <?= $party_det['name'] . " | " . $party_det['city'] . " | " . $party_det['state'] . " | " . $result_chl['mapped_code'] ?>
                                                    </option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select><?php */?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12"><label class="col-md-3 control-label">Cost Centre(Godown)<span style="color:#F00">*</span></label>
                                    <div class="col-md-6">
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
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label">Tax Type</label>
                                    <div class="col-md-2">
                                        <select name="tax_type" id="tax_type" required class="form-control required" style="width:150px;" onChange="document.frm1.submit();">
                                            <option value="NONE"<?php if ($_REQUEST['tax_type'] == "NONE") echo "selected"; ?>>NONE</option>
                                            <option value="PT"<?php if ($_REQUEST['tax_type'] == "PT") echo "selected"; ?>>Productwise Tax</option>
                                            <option value="TT"<?php if ($_REQUEST['tax_type'] == "TT") echo "selected"; ?>>Total Tax</option>
                                        </select>
                                    </div>
                                    <label class="col-md-2 control-label">Discount Type</label>
                                    <div class="col-md-3">
                                        <select name="discount_type" id="discount_type" required class="form-control required" style="width:150px;" onChange="document.frm1.submit();">
                                            <option value="NONE"<?php if ($_REQUEST['discount_type'] == "NONE") echo "selected"; ?>>NONE</option>
                                            <option value="PD"<?php if ($_REQUEST['discount_type'] == "PD") echo "selected"; ?>>Productwise Discount</option>
                                            <option value="TD"<?php if ($_REQUEST['discount_type'] == "TD") echo "selected"; ?>>Total Discount</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
                            <div class="form-group">
                                <table class="table table-bordered" width="100%" id="itemsTable1">
                                    <thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th style="text-align:center" width="30%">Combo</th>
                                            <th style="text-align:center" width="10%">Bill Qty</th>
                                            <th style="text-align:center" width="10%">Price</th>                                           
                                            <th style="text-align:center" width="10%">Discount</th>
                                            <th style="text-align:center" width="12%">Taxable Val</th>
                                            <th style="text-align:center" width="15%">GST</th>
                                            <th style="text-align:center" width="13%">Total </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="addr0">
                                            <td> <select name="combo_model[0]" id="combo_model[0]"  class="form-control selectpicker" required data-live-search="true" onChange="getComboProduct(0);" style="width:150px;padding-right:100px;">
                                                    <option value="">--None--</option>
                                                    <?php
                                                    $model_query = "select bom_modelcode, bom_modelname, bom_hsn FROM combo_master WHERE status='1' GROUP BY bom_modelcode";
                                                    $check1 = mysqli_query($link1, $model_query);
                                                    while ($br = mysqli_fetch_array($check1)) {
                                                        ?>
                                                        <option value="<?php echo $br['bom_modelcode']."~".$br['bom_hsn']."~".$br['bom_modelname']; ?>"><?php echo $br['bom_modelname']." | ".$br['bom_modelcode']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <input type="hidden" name="noofbomproduct[0]" id="noofbomproduct[0]">
                                                <span id="combo_prodlist0"></span>												</td>
                                            <td style="text-align:right"><input type="text" class="form-control required digits" name="bill_qty[0]" id="bill_qty[0]" autocomplete="off" required onKeyUp="getComboProduct(0);" value="1" style="width:80px;text-align:right">
                                            <span id="combo_prodqty0"></span>                                            </td>

                                            <td><input type="text" class="form-control required number" name="price[0]" id="price[0]" onKeyUp="rowTotal(0);" autocomplete="off" required style="width:80px;text-align:right">
                                            <span id="combo_prodprice0"></span>                                            </td>                                          
                                          <td><input type="text" class="form-control number" name="rowdiscount[0]" id="rowdiscount[0]" autocomplete="off" <?php if ($_REQUEST['discount_type'] != "PD") { ?> disabled="disabled"<?php } ?> onKeyUp="rowTotal(0);" style="width:80px;text-align:right"></td>
                                            <td><input name="total_beforedisc[0]" id="total_beforedisc[0]" type="hidden"/><input type="text" class="form-control" name="rowsubtotal[0]" id="rowsubtotal[0]" autocomplete="off" value="" style="width:100px;text-align:right" readonly>                                            </td>
                                            <td>
                                            	<?php if($fromlocationdet[0]==$toloctiondet[0]){ ?>
                                              	<div class="row">
                                                	<div class="col-md-4">
                                               		<input type="text" class="form-control" name="rowsgstper[0]" id="rowsgstper[0]" value="0" readonly style="width:50px;text-align:right;padding: 4px">
                                                	</div>
                                                	<div class="col-md-4">
                                                	<input type="text" class="form-control" name="rowsgstamount[0]" id="rowsgstamount[0]" value="0" readonly style="width:80px;text-align:right;padding: 4px">					
                                                </div>
                                                </div>
                                                
                                                
                                                <div class="row">
                                                	<div class="col-md-4">
                                                    <input type="text" class="form-control" name="rowcgstper[0]" id="rowcgstper[0]" value="0" readonly style="width:50px;text-align:right;padding: 4px">
                                                    </div>
                                                	<div class="col-md-4">
                                                    <input type="text" class="form-control" name="rowcgstamount[0]" id="rowcgstamount[0]" value="0" readonly style="width:80px;text-align:right;padding: 4px">
                                                    </div>
                                               </div>
                                                <?php }else{?>
                                                <div class="row">
                                                	<div class="col-md-4">
                                                	<input type="text" class="form-control" name="rowigstper[0]" id="rowigstper[0]" value="0" readonly style="width:50px;text-align:right;padding: 4px">
                                                	</div>
                                                    <div class="col-md-4">
                                                	<input type="text" class="form-control" name="rowigstamount[0]" id="rowigstamount[0]" value="0" readonly style="width:60px;text-align:right;padding: 4px">
                                                    </div>
                                                </div>
                                                <?php }?>                                            </td>
                                            <td><input type="text" class="form-control" name="total_val[0]" id="total_val[0]" autocomplete="off" readonly  style="width:120px;text-align:right">
                                                <input type="hidden" name="avl_stock[0]" id="avl_stock[0]">
                                                <input name="mrp[0]" id="mrp[0]" type="hidden"/>
                                                <input name="holdRate[0]" id="holdRate[0]" type="hidden"/>                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot id='productfooter' style="z-index:-9999;">
                                        <tr class="0">
                                            <td colspan="12" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a><input type="hidden" name="rowno" id="rowno" value="0"/></td>
                                        </tr>
                                    </tfoot>
                                </table>
                          </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label">Total Qty</label>
                                    <div class="col-md-2">
                                        <input type="text" name="total_qty" id="total_qty" class="form-control" value="0" readonly style="width:200px;"/>
                                    </div>
                                    <label class="col-md-2 control-label">Sub Total</label>
                                    <div class="col-md-2">
                                    	<input type="text" name="sub_total_bd" id="sub_total_bd" class="form-control" value="0.00" readonly style="width:200px;text-align:right"/>
                                        <input type="text" name="sub_total" id="sub_total" class="form-control" value="0.00" readonly style="width:200px;text-align:right"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label">Ship To</label>
                                    <div class="col-md-2">
                                    	<select name="ship_to" id="ship_to" required class="form-control selectpicker required" data-live-search="true" style="width:200px;" onChange="getShipToInfo(this.value);">
                                            <option value="" selected="selected">Please Select </option>
                                            <?php
                                            $sql_shipto = "SELECT * FROM delivery_address_master WHERE location_code='".$_REQUEST['po_to']."' AND status='Active'";
                                            $res_shipto = mysqli_query($link1, $sql_shipto);
                                            while($row_shipto = mysqli_fetch_array($res_shipto)) {
											?>
                                            <option value="<?=$row_shipto['address_code']."~".$row_shipto['address']."~".$row_shipto['city']."~".$row_shipto['gstin']?>"><?=$row_shipto['party_name']." | ".$row_shipto['address']?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <label class="col-md-2 control-label">Discount</label>
                                    <div class="col-md-2">
                                        <input type="text" name="total_discount" id="total_discount" class="form-control" value="0.00" <?php if ($_REQUEST['discount_type'] != "TD") { ?> readonly <?php } ?> style="width:200px;text-align:right" onKeyUp="check_total_discount();"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label">Ship To GSTIN</label>
                                    <div class="col-md-2">
                                       <input type="text" name="shipto_gstin" id="shipto_gstin" class="form-control alphanumeric" minlength="15" maxlength="15" style="width:200px;">
                                    </div>
                                    <label class="col-md-2 control-label">Tax Amount</label>
                                    <div class="col-md-2">
                                        <input type="text" name="tax_amount" id="tax_amount" class="form-control" value="0.00" readonly style="width:200px;text-align:right"/>
                                        <input type="hidden" name="total_sgstamt" id="total_sgstamt" class="form-control" readonly style="width:200px;text-align:right"/>
                                        <input type="hidden" name="total_cgstamt" id="total_cgstamt" class="form-control" readonly style="width:200px;text-align:right"/>
                                        <input type="hidden" name="total_igstamt" id="total_igstamt" class="form-control" readonly style="width:200px;text-align:right"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label">Ship To City</label>
                                    <div class="col-md-2">
                                    	<input type="text" name="shipto_city" id="shipto_city" class="form-control mastername" style="width:200px;">
                                    </div>
                                    <label class="col-md-2 control-label">Grand Total</label>
                                    <div class="col-md-2">
                                        <input type="text" name="grand_total" id="grand_total" class="form-control" value="0.00" readonly style="width:200px;text-align:right"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label">TCS %</label>
                                    <div class="col-md-2">
                                    	<select name="tcs_per" id="tcs_per" class="form-control" onChange="calculatetotal();" style="width:200px">
                                            <option value="">--Please Select--</option>
                                            <option value="0.1">0.1 %</option>
                                        </select>
                                    </div>
                                    <label class="col-md-2 control-label">TCS Amount</label>
                                    <div class="col-md-2">
                                        <input type="text" name="tcs_amt" id="tcs_amt" class="form-control" value="0.00" style="width:200px;text-align:right" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label">Round Off</label>
                                    <div class="col-md-2">
                                    	<input type="text" name="round_off" id="round_off" class="form-control" value="0.00" style="width:200px;text-align:right" readonly/>
                                    </div>
                                    <label class="col-md-2 control-label">Final Total</label>
                                    <div class="col-md-2">
                                        <input type="text" name="final_total" id="final_total" class="form-control" value="0.00" style="width:200px;text-align:right" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label">Delivery Address</label>
                                    <div class="col-md-2">
                                        <textarea name="delivery_address" id="delivery_address" class="form-control required" style="resize:vertical; width:200px" required><?php echo $toloctiondet[2]; ?></textarea>
                                    </div>
                                    <label class="col-md-2 control-label">Remark</label>
                                    <div class="col-md-2">
                                        <textarea name="remark" id="remark" class="form-control" style="resize:vertical;width:200px" ></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label">Sales Executive</label>
                                    <div class="col-md-2">
                                        <select name="sales_executive" id="sales_executive" class="form-control selectpicker" data-live-search="true" style="width:200px;">
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
                                    <label class="col-md-2 control-label">&nbsp;</label>
                                    <div class="col-md-2">
                                  
                                    </div>
                                </div>
                            </div>
                         
                            <div class="form-group">
                                <div class="col-md-12" align="center">
                                    <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Process" title="Make Invoice" <?php if($_POST["upd"]=="Process"){?> disabled<?php }?>>
                                    &nbsp;
                                    <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href = 'retailbillinglist.php?<?= $pagenav ?>'">
                                    <input type="hidden" name="parentcode" id="parentcode" value="<?= $_REQUEST['po_from'] ?>"/>
                                    <input type="hidden" name="partycode" id="partycode" value="<?= $_REQUEST['po_to'] ?>"/>
                                    <input type="hidden" name="stockfrom" id="stockfrom" value="<?= $_REQUEST['stock_from'] ?>"/>
                                    <input type="hidden" name="disc_type" id="disc_type" value="<?= $_REQUEST['discount_type'] ?>"/>
                                    <input type="hidden" name="tx_type" id="tx_type" value="<?= $_REQUEST['tax_type'] ?>"/>
                                    <input type="hidden" name="toloctionstate" id="toloctionstate" value="<?= $toloctiondet[0] ?>"/>
									<input type="hidden" name="fromloctionstate" id="fromloctionstate" value="<?= $fromlocationdet[0] ?>"/>
									<input type="hidden" name="fromidtype" id="fromidtype" value="<?= $fromlocationdet[1] ?>"/>
                                    <input type="hidden" name="toidtype" id="toidtype" value="<?= $toloctiondet[1] ?>"/>
                                </div>
                            </div>
                        </form>
                    </div><!--close panel group-->
                </div><!--close col-sm-9-->
            </div><!--close row content-->
        </div><!--close container-fluid-->
        <?php
        include("../includes/footer.php");
        include("../includes/connection_close.php");
        ?>
         <script>
		$("#po_to").on('change', function(){
			let val = this.value;
			if(val){
				$("#partycode").val(val);
			}
			else{
				$("#partycode").val("");
			}
		});
		</script>
    </body>
    <?php if($_REQUEST['po_to']=='' || $_REQUEST['po_from']=='' || $_REQUEST['stock_from']==''){ ?>

<script>

$("#frm2").find("input[type='submit']:enabled, input[type='text']:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");

</script>
<?php }?>
</html>