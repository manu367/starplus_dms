<?php
require_once("../config/config.php");
$docid = base64_decode($_REQUEST['id']);
$toloctiondet = explode("~", getLocationDetails($_REQUEST['po_to'], "state,id_type,disp_addrs", $link1));
$fromlocationdet  = explode("~", getLocationDetails($_REQUEST['po_from'], "state,id_type,disp_addrs", $link1));
@extract($_POST);
////// if we hit process button
if ($_POST) {
    if ($_POST['upd'] == 'Process') {
        if ($total_qty != '' && $total_qty != 0) {
            //// Make System generated Invoice no.//////
            $res_cnt = mysqli_query($link1, "select inv_str,inv_counter from document_counter where location_code='" . $parentcode . "'");
            if (mysqli_num_rows($res_cnt)) {
                $row_cnt = mysqli_fetch_array($res_cnt);
                $invcnt = $row_cnt['inv_counter'] + 1;
                $pad = str_pad($invcnt, 4, 0, STR_PAD_LEFT);
                $invno = $row_cnt['inv_str'] . $pad;
                mysqli_autocommit($link1, false);
                $flag = true;
                $err_msg = "";
                $splitcompltetax = explode("~", $complete_tax);
                ///// get parent location details
                $parentloc = getLocationDetails($parentcode, "addrs,disp_addrs,name,city,state,gstin_no,pincode,email,phone", $link1);
                $parentlocdet = explode("~", $parentloc);
                ///// get child location details
                $childloc = getLocationDetails($partycode, "addrs,disp_addrs,name,city,state,gstin_no,pincode,email,phone", $link1);
                $childlocdet = explode("~", $childloc);
                if ($delivery_address) {
                    $deli_addrs = $delivery_address;
                } else {
                    $deli_addrs = $childlocdet[1];
                }
                ///// Insert Master Data
              $query1 = "INSERT INTO billing_master set from_location='" . $parentcode . "', to_location='" . $partycode . "',from_gst_no='".$parentlocdet[5]."', from_partyname='".$parentlocdet[2]."', party_name='".$childlocdet[2]."', to_gst_no='".$childlocdet[5]."', challan_no='" . $invno . "', sale_date='" . $today . "', entry_date='" . $today . "', entry_time='" . $currtime . "', entry_by='" . $_SESSION['userid'] . "', status='Pending', type='RETAIL', document_type='INVOICE', discountfor='" . $disc_type . "', taxfor='" . $tx_type . "',basic_cost='" . $sub_total . "',discount_amt='" . $total_discount . "',total_sgst_amt='".$total_sgstamt."',total_cgst_amt='".$total_cgstamt."',total_igst_amt='".$total_igstamt."',tax_cost='" . $tax_amount . "',total_cost='" . $grand_total . "',tax_type='" . $splitcompltetax[1] . "',tax_header='" . $splitcompltetax[2] . "',tax='" . $splitcompltetax[0] . "',bill_from='" . $parentcode . "',bill_topty='" . $partycode . "',from_addrs='" . $parentlocdet[0] . "',disp_addrs='" . $parentlocdet[1] . "',to_addrs='" . $childlocdet[0] . "',deliv_addrs='" . $deli_addrs . "',billing_rmk='" . $remark . "',from_state='".$parentlocdet[4]."', to_state='".$childlocdet[4]."', from_city='".$parentlocdet[3]."', to_city='".$childlocdet[3]."', from_pincode='".$parentlocdet[6]."', to_pincode='".$childlocdet[6]."', from_phone='".$parentlocdet[8]."', to_phone='".$childlocdet[8]."', from_email='".$parentlocdet[7]."', to_email='".$childlocdet[7]."'";
              $result = mysqli_query($link1, $query1)or die("ER1" . mysqli_error($link1));
                //// check if query is not executed
                if (!$result) {
                    $flag = false;
                    $err_msg = "Error Code1:";
                }
                /// update invoice counter /////
               $result = mysqli_query($link1, "update document_counter set inv_counter=inv_counter+1,update_by='" . $_SESSION['userid'] . "',updatedate='" . $datetime . "' where location_code='" . $parentcode . "'");
                //// check if query is not executed
                if (!$result) {
                    $flag = false;
                    $err_msg = "Error Code2:";
                }
                ///// Insert in item data by picking each data row one by one
                foreach ($prod_code as $k => $val) {
                    // checking row value of product and qty should not be blank
                    $getstk = getCurrentStock($parentcode, $prod_code[$k], "okqty", $link1);
                    //// check stock should be available ////
                    if ($getstk < $bill_qty[$k]) {
                        $flag = false;
                        $err_msg = "Error Code3: Stock is not available";
                    } else {
                        
                    }
                    // checking row value of product and qty should not be blank
                    if ($prod_code[$k] != '' && $bill_qty[$k] != '' && $bill_qty[$k] != 0) {
					$arr_prodcode[]=$prod_code[$k];
					$arr_qty[]=$bill_qty[$k];
					$arr_price[]=$price[$k];
					$arr_mrp[]=$mrp[$k];
					$arr_holdprice[]=$holdRate[$k];				
					$arr_tax[]=$taxType[$k];
					$arr_taxamt[]=$rowtaxamount[$k];
					$arr_linetotal[]=$rowsubtotal[$k];			
					$arr_discount[]=$rowdiscount[$k];
					$arr_sgstper[]=$rowsgstper[$k];
					$arr_sgstamount[]=$rowsgstamount[$k];
					$arr_cgstper[]=$rowcgstper[$k];
					$arr_cgstamount[]=$rowcgstamount[$k];
					$arr_igstper[]=$rowigstper[$k];
					$arr_igstamount[]=$rowigstamount[$k];
					$arr_totalval[]=$total_val[$k];
					}
					
					///// apply logic to insert data in data table//
					$uniq_prod=array_unique($arr_prodcode);
					foreach($uniq_prod as $key => $value){
					//// find all key of every product in main array
					$keyarr=array_keys($arr_prodcode, $value);
					///// make product wise all deatils in consolidate form
					for($i=0;$i<count($keyarr);$i++){
					$sum_qty+=$arr_qty[$keyarr[$i]];
					$sum_price+=$arr_price[$keyarr[$i]];
					$sum_mrp+=$arr_mrp[$keyarr[$i]];
					$sum_holdprice+=$arr_holdprice[$keyarr[$i]];
					$sum_linetotal+=$arr_linetotal[$keyarr[$i]];
					$sum_taxamt+=$arr_taxamt[$keyarr[$i]];
					$sum_discount+=$arr_discount[$keyarr[$i]];
					$sum_totalval+=$arr_totalval[$keyarr[$i]];
			 		$sgstper =$arr_sgstper[$keyarr[$i]];
			 		$sgstamt=$arr_sgstamount[$keyarr[$i]];
					$igstper=$arr_igstper[$keyarr[$i]];
					$igstamt=$arr_igstamount[$keyarr[$i]];
			 		$cgstper=$arr_cgstper[$keyarr[$i]];
					$cgstamt=$arr_cgstamount[$keyarr[$i]];								
					}
				 }	             
                        /////////// insert data
                    $query2 = "insert into billing_model_data set from_location='" . $parentcode . "', prod_code='" . $val . "', qty='" . $bill_qty[$k] . "', okqty='" . $bill_qty[$k] . "',mrp='" . $mrp[$k] . "', price='" . $price[$k] . "', hold_price='" . $holdRate[$k] . "', value='" . $rowsubtotal[$k] . "',tax_name='" . $tax_per[1] . "',tax_per='" . $tax_per[0] . "', tax_amt='" . $rowtaxamount[$k] . "',discount='" . $rowdiscount[$k] . "', totalvalue='" . $total_val[$k] . "',challan_no='" . $invno . "' ,sale_date='" . $today . "',entry_date='" . $today . "' ,sgst_per='".$sgstper."' ,sgst_amt='".$sgstamt."',igst_per='".$igstper."' ,igst_amt='".$igstamt."',cgst_per='".$cgstper."' ,cgst_amt='".$cgstamt."'";
		
                     $result = mysqli_query($link1, $query2);
                        //// check if query is not executed
                        if (!$result) {
                            $flag = false;
                            $err_msg = "Error Code4:";
                        }
                        //// update stock of from loaction
                        $result = mysqli_query($link1, "update stock_status set okqty=okqty-'" . $bill_qty[$k] . "',updatedate='" . $datetime . "' where asc_code='" . $parentcode . "' and partcode='" . $val . "'");
                        //// check if query is not executed
                        if (!$result) {
                            $flag = false;
                            $err_msg = "Error Code5:";
                        }
                        ///// update stock ledger table
                       $flag = stockLedger($invno, $today, $val, $parentcode, $partycode, $parentcode, "OUT", "OK", "Retail Invoice", $bill_qty[$k], $price[$k], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
					   
					   ////// entry in Customer Reward Ledger /////////////////////////////
		
						 if(($reward_info_chck[$k] == 'Y') && ($reward_point[$k] >0)){
						  ///// entry in customer reward ledger //////////////////////
						   mysqli_query($link1 , " insert into customer_reward_ledger set customer_id = '".$partycode."' , ref_no = '".$invno."' ,rewards = '".$reward_point[$k]."' , cr_dr_type = 'CR' , remark = 'EARNED' , entry_date = '".$today."' , entry_by = '".$_SESSION['userid']."' , ip = '".$ip."' , product_code = '".$value."'  ");
						 
						  ///////////////////////////////////////////////////////////////////////////
						   }
					   
					   
                  
					 } // close if loop of checking row value of product and qty should not be blank
	 
                /// close for loop
                //// update cr bal of child location
                $result = mysqli_query($link1, "update current_cr_status set cr_abl=cr_abl-'" . $grand_total . "',total_cr_limit=total_cr_limit-'" . $grand_total . "', last_updated='" . $datetime . "' where parent_code='" . $parentcode . "' and asc_code='" . $partycode . "'");
                //// check if query is not executed
                if (!$result) {
                    $flag = false;
                    $err_msg = "Error Code6:";
                }
                ////// maintain party ledger////
                $flag = partyLedger($parentcode, $partycode, $invno, $today, $today, $currtime, $_SESSION['userid'], "RETAIL INVOICE", $grand_total, "DR", $link1, $flag);
                ////// insert in activity table////
                $flag = dailyActivity($_SESSION['userid'], $po_no, "RETAIL INVOICE", "ADD", $ip, $link1, $flag);
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

        <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
        <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#myTable').dataTable();
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
            ////// function to check available stock
            /*function check_stock(id)
             {
             var product=document.getElementById('prod_code['+id+']').value;
             var fparty=document.getElementById('po_to').value; 
             var ok='okqty';
             $.ajax({
             type:'post',
             url:'../includes/getAzaxFields.php',
             data:{locstk:product, loccode:fparty, indxx:id, stktype:ok},
             success:function(data)
             {
             var res=data.split('~');
             document.getElementById('avl_stock'+id).value=res[0];
             
             }
             });
             
             }
             */
            /////////// function to get available stock of ho
            function getAvlStk(indx) {
                var productCode = document.getElementById("prod_code[" + indx + "]").value;
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
                });
            }
        </script>

        <script>
            ///// function to get price of product
            function get_price(ind) {
                var productCode = document.getElementById("prod_code[" + ind + "]").value;
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
						document.getElementById("price[" + ind + "]").value = splitprice[3];
						
						document.getElementById("reward_info_chck[" + ind + "]").value = splitprice[4];
						document.getElementById("reward_point[" + ind + "]").value = splitprice[5];
						
						if((splitprice[4] == 'Y') && (splitprice[5] >0)){
						   document.getElementById("prd_desc"+ind+"").innerHTML = "<a href='#' title='Reward Point' style='color:#FF0000;' data-toggle='popover' data-trigger='focus' data-content='"+splitprice[6]+"'><i class='fa fa-th'></i></a>";

			              rePop();
						
						 }
						 else {
						
						  }	
						
						
						
									
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
                    }
                });
				
			
				
            }
        </script>
        <script>

            $(document).ready(function() {
                $("#add_row").click(function() {
                    var numi = document.getElementById('rowno');
                    var itm = "prod_code[" + numi.value + "]";
                    var qTy = "bill_qty[" + numi.value + "]";
                    var preno = document.getElementById('rowno').value;
                    var num = (document.getElementById("rowno").value - 1) + 2;
                    if ((document.getElementById(itm).value != "" && document.getElementById(qTy).value != "" && document.getElementById(qTy).value != "0") || ($("#addr" + numi.value + ":visible").length == 0)) {
                        numi.value = num;
                        var r = '<tr id="addr' + num + '"><td><span id="pdtid' + num + '"><select class="form-control" data-live-search="true" name="prod_code[' + num + ']" id="prod_code[' + num + ']" required onchange="getAvlStk(' + num + '); get_price(' + num + ');"><option value="">--None--</option><?php $model_query = "select productcode,productname,productcolor from product_master where status='Active'";$check1 = mysqli_query($link1, $model_query);while ($br = mysqli_fetch_array($check1)) {?><option data-tokens="<?php echo $br['productname']; ?>" value="<?php echo $br['productcode']; ?>"><?php echo $br['productname']." | ".$br['productcode']." | ".$br['productcolor']; ?></option><?php } ?></select></span><div id="prd_desc'+num+'" style="display:inline-block;float:right"></div><input type="hidden" name="reward_info_chck[' + num + ']" id="reward_info_chck[' + num + ']"><input type="hidden" name="reward_point[' + num + ']" id="reward_point[' + num + ']"></td><td><input type="text" name="bill_qty[' + num + ']" id="bill_qty[' + num + ']" onblur=rowTotal(' + num + ');myFunction(this.value,' + num + ',"bill_qty[' + num + ']"); class="digits form-control" onkeypress="return onlyNumbers(this.value);" style="text-align:right; width:80px;"/></td><td><input  name="price[' + num + ']" id="price[' + num + ']" type="text" onkeypress="return onlyFloatNum(this.value)" class="required form-control" onblur="rowTotal(' + num + ');" style="text-align:right; width:80px;"></td><td><input type="text" class="form-control" name="rowdiscount[' + num + ']" id="rowdiscount[' + num + ']" onkeypress="return onlyFloatNum(this.value);" autocomplete="off" onblur="rowTotal(' + num + ');" <?php if ($_REQUEST['discount_type'] != "PD") { ?> disabled="disabled"<?php } ?> style="width:80px;text-align:right" /></td><td><input type="text" class="form-control" name="rowsubtotal[' + num + ']" id="rowsubtotal[' + num + ']" autocomplete="off" value="" style="width:100px;text-align:right" readonly></td><td><?php if($fromlocationdet[0]==$toloctiondet[0]){ ?><div class="row"><div class="col-md-4"><input type="text" class="form-control" name="rowsgstper[' + num + ']" id="rowsgstper[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div class="col-md-4"><input type="text" class="form-control" name="rowsgstamount[' + num + ']" id="rowsgstamount[' + num + ']" value="0" readonly style="width:80px;text-align:right;padding: 4px"></div></div><div class="row"><div class="col-md-4"><input type="text" class="form-control" name="rowcgstper[' + num + ']" id="rowcgstper[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div class="col-md-4"><input type="text" class="form-control" name="rowcgstamount[' + num + ']" id="rowcgstamount[' + num + ']" value="0" readonly style="width:80px;text-align:right;padding: 4px"></div></div><?php }else{?><div class="row"><div class="col-md-4"><input type="text" class="form-control" name="rowigstper[' + num + ']" id="rowigstper[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div class="col-md-4"><input type="text" class="form-control" name="rowigstamount[' + num + ']" id="rowigstamount[' + num + ']" value="0" readonly style="width:60px;text-align:right;padding: 4px"></div></div><?php }?></td><td><div style="display:inline-block;float:left"><input type="text" class="form-control" name="total_val[' + num + ']" id="total_val[' + num + ']" autocomplete="off" readonly style="width:100px;text-align:right"><input name="mrp[' + num + ']" id="mrp[' + num + ']" type="hidden"/><input name="holdRate[' + num + ']" id="holdRate[' + num + ']" type="hidden"/><input type="hidden" name="avl_stock[' + num + ']" id="avl_stock[' + num + ']"></div><div style="display:inline-block;float:right"><i class="fa fa-close fa-lg" onClick="fun_remove(' + num + ');"></i></div></td></tr>';

                        $('#itemsTable1').append(r);

                    }
                });
            });
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
				get_price(ind);
                var ent_qty = "bill_qty" + "[" + ind + "]";
                var ent_rate = "price" + "[" + ind + "]";
                var hold_rate = "holdRate" + "[" + ind + "]";
                var availableQty = "avl_stock" + "[" + ind + "]";
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
                ////// check entered qty should be available
                if (parseFloat(qty) <= parseFloat(document.getElementById(availableQty).value)) {				
                    if (parseFloat(price) >= parseFloat(dicountval)) {
                        var total = parseFloat(qty) * parseFloat(price);
                        var totalcost = (parseFloat(qty) * (parseFloat(price)) - parseFloat(dicountval));
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
                    } else {
                        alert("Discount is exceeding from price");
                        var total = parseFloat(qty) * parseFloat(price);
                        var var3 = "rowsubtotal" + "[" + ind + "]";
                        document.getElementById(var3).value = formatCurrency(total);
                        document.getElementById(discountField).value = "0.00";
                        document.getElementById(totalvalField).value = formatCurrency(total);
                        rowTotal(ind);
                    }
                } else if (parseFloat(document.getElementById(availableQty).value) == '0.00') {
                    alert("Stock is not Available.");
                    document.getElementById(ent_qty).value = "";
                    //document.getElementById(availableQty).value="";
                    document.getElementById(ent_rate).value = document.getElementById(hold_rate).value;
					rowTotal(ind);
                }
                else {
                    alert("Stock is not Available..");
                    document.getElementById(ent_qty).value = "";
                    //document.getElementById(availableQty).value="";
                    document.getElementById(ent_rate).value = document.getElementById(hold_rate).value;
					rowTotal(ind);
                }

            }
            ////// calculate final value of form /////
            function calculatetotal() {
                var rowno1 = (document.getElementById("rowno").value);
                var sum_qty = 0;
                var sum_total = 0.00;
                var sum_discount = 0.00;
                var sum_tax = 0.00;
				var sum_sgst = 0.00;
				var sum_cgst = 0.00;
				var sum_igst = 0.00;
                var sum = 0.00;
                for (var i = 0; i <= rowno1; i++) {
                    var temp_qty = "bill_qty" + "[" + i + "]";            
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
					sum_discount += parseFloat(discountvar) ;
                    sum_total += parseFloat(total);
                  //  sum_discount += parseFloat(discountvar) ;
                    sum_tax += parseFloat(total_tax);
					<?php if($fromlocationdet[0]==$toloctiondet[0]){ ?>
					sum_sgst += parseFloat(total_sgst);
					sum_cgst += parseFloat(total_cgst);
					<?php }else{?>
					sum_igst += parseFloat(total_igst);
					<?php }?>
					
                    //sum += parseFloat(total);

                }/// close for loop
                document.getElementById("total_qty").value = sum_qty;
                document.getElementById("sub_total").value = formatCurrency(total_tax);
<?php if ($_REQUEST['discount_type'] == "PD") { ?>
                    document.getElementById("total_discount").value = formatCurrency(sum_discount);
<?php } ?>
<?php //if ($_REQUEST['tax_type'] == "PT") { ?>
                 document.getElementById("tax_amount").value = formatCurrency(sum_sgst+sum_cgst+sum_igst);
				 <?php if($fromlocationdet[0]==$toloctiondet[0]){ ?>
				 document.getElementById("total_sgstamt").value = formatCurrency(sum_sgst);
				 document.getElementById("total_cgstamt").value = formatCurrency(sum_cgst);
				 <?php }else{?>
				 document.getElementById("total_igstamt").value = formatCurrency(sum_igst);
				 <?php }?>
<?php //} ?>
                document.getElementById("grand_total").value = formatCurrency(parseFloat(sum_total));

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
        </script>

    </head>
    <body onKeyPress="return keyPressed(event);">
        <div class="container-fluid">
            <div class="row content">
                <?php
                include("../includes/leftnav2.php");
                ?>
                <div class="col-sm-9">
                    <h2 align="center"><i class="fa fa-user"></i> Retail Billing </h2><br/>
                    <div class="form-group" id="page-wrap" style="margin-left:10px;">
                        <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
                            <div class="form-group">
                                <div class="col-md-12"><label class="col-md-3 control-label">Billing  From<span style="color:#F00">*</span></label>
                                    <div class="col-md-6">
                                        <select name="po_from" id="po_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                                            <option value="" selected="selected">Please Select </option>
                                            <?php
                                            $sql_parent = "select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y'";
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
                                        <select name="po_to" id="po_to" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
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
                                            <th style="text-align:center" width="30%">Product</th>
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
                                            <td > <select name="prod_code[0]" id="prod_code[0]"  class="form-control selectpicker" required data-live-search="true" onChange=" getAvlStk(0);get_price(0);" style="width:150px;padding-right:100px;">
                                                    <option value="">--None--</option>
                                                    <?php
                                                    $model_query = "select productcode, productname,productcolor from product_master where status='Active'";
                                                    $check1 = mysqli_query($link1, $model_query);
                                                    while ($br = mysqli_fetch_array($check1)) {
                                                        ?>
                                                        <option data-tokens="<?php echo $br['productname']; ?>" value="<?php echo $br['productcode']; ?>"><?php echo $br['productname']." | ".$br['productcode']." | ".$br['productcolor']; ?></option>
                                                    <?php } ?>
                                                </select> <div id="prd_desc0" style="display:inline-block;float:right"></div>
												<input type="hidden" name="reward_info_chck[0]" id="reward_info_chck[0]" >
												<input type="hidden" name="reward_point[0]" id="reward_point[0]" >
												</td>
                                            <td style="text-align:right"><input type="text" class="form-control digits" name="bill_qty[0]" id="bill_qty[0]"  autocomplete="off" required onBlur="myFunction(this.value, 'none', 'bill_qty[0]');rowTotal(0);" onKeyPress="return onlyNumbers(this.value);check_stock(0);" style="width:80px;text-align:right">
                                            </td>

                                            <td><input type="text" class="form-control" name="price[0]" id="price[0]" onBlur="rowTotal(0);" autocomplete="off" onKeyPress="return onlyFloatNum(this.value);" required style="width:80px;text-align:right"></td>                                          
                                            <td><input type="text" class="form-control" name="rowdiscount[0]" id="rowdiscount[0]" onKeyPress="return onlyFloatNum(this.value);" autocomplete="off" <?php if ($_REQUEST['discount_type'] != "PD") { ?> disabled="disabled"<?php } ?> onBlur="rowTotal(0);" style="width:80px;text-align:right"></td>
                                            <td><input type="text" class="form-control" name="rowsubtotal[0]" id="rowsubtotal[0]" onKeyPress="return onlyFloatNum(this.value);" autocomplete="off" value="" style="width:100px;text-align:right" readonly></td>
                                            </td>
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
                                                <?php }?>
                                            </td>
                                            <td><input type="text" class="form-control" name="total_val[0]" id="total_val[0]" autocomplete="off" readonly  style="width:120px;text-align:right">
                                                <input type="hidden" name="avl_stock[0]" id="avl_stock[0]">
                                                <input name="mrp[0]" id="mrp[0]" type="hidden"/>
                                                <input name="holdRate[0]" id="holdRate[0]" type="hidden"/>
                                            </td>

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
                                        <input type="text" name="sub_total" id="sub_total" class="form-control" value="<?= $po_row['po_value'] ?>" readonly style="width:200px;text-align:right"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-2">
                                    </div>
                                    <label class="col-md-2 control-label">Discount</label>
                                    <div class="col-md-2">
                                        <input type="text" name="total_discount" id="total_discount" class="form-control" value="<?php echo $po_row['discount']; ?>" <?php if ($_REQUEST['discount_type'] != "TD") { ?> readonly <?php } ?> style="width:200px;text-align:right" onKeyUp="check_total_discount();"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-2">
                                       
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
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-2">
                                    </div>
                                    <label class="col-md-2 control-label">Grand Total</label>
                                    <div class="col-md-2">
                                        <input type="text" name="grand_total" id="grand_total" class="form-control" value="<?php echo currencyFormat($po_row['po_value'] - $po_row['discount']); ?>" readonly style="width:200px;text-align:right"/>
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
                            <br><br>
                            <div class="form-group">
                                <div class="col-md-12" align="center">
                                    <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Process" title="Make Invoice">
                                    &nbsp;
                                    <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href = 'retailbillinglist.php?<?= $pagenav ?>'">
                                    <input type="hidden" name="parentcode" id="parentcode" value="<?= $_REQUEST['po_from'] ?>"/>
                                    <input type="hidden" name="partycode" id="partycode" value="<?= $_REQUEST['po_to'] ?>"/>
                                    <input type="hidden" name="disc_type" id="disc_type" value="<?= $_REQUEST['discount_type'] ?>"/>
                                    <input type="hidden" name="tx_type" id="tx_type" value="<?= $_REQUEST['tax_type'] ?>"/>
                                    <input type="hidden" name="toloctionstate" id="toloctionstate" value="<?= $toloctiondet[0] ?>"/>
									<input type="hidden" name="fromloctionstate" id="fromloctionstate" value="<?= $fromlocationdet[0] ?>"/>
									<input type="hidden" name="fromidtype" id="fromidtype" value="<?= $fromlocationdet[1] ?>"/>
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
    </body>
</html>