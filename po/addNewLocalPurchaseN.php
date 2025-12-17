<?php
require_once("../config/config.php");
/////// get from party state
$from_ptydet = explode("~",getLocationDetails($_REQUEST["po_to"],"state,addrs",$link1));
$from_state = $from_ptydet[0];
/////// get to party state
$to_state = getVendorDetails($_REQUEST["po_from"],"state",$link1);
@extract($_POST);
////// case 2. if we want to Add new user

if ($_POST) {
    if ($_POST['upd'] == 'Save') {
		///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
		$messageIdent = md5($_POST['upd'].$parentcode.$partycode);
		//and check it against the stored value:
		$sessionMessageIdent = isset($_SESSION['messageIdentLP1'])?$_SESSION['messageIdentLP1']:'';
		if($messageIdent!=$sessionMessageIdent){//if its different:
		//save the session var:
		$_SESSION['messageIdentLP1'] = $messageIdent;
		//// check post invoice no. is already entered
		if (mysqli_num_rows(mysqli_query($link1,"SELECT id FROM billing_master WHERE inv_ref_no='".$invoiceno."' AND from_location='".$partycode."' AND status!='Cancelled'"))>0) {
			$cflag="danger";
			$cmsg="Failed";
			$msg = "Request could not be processed. Please try again. You have entered duplicate invoice number.";
			header("location:localPurchaseList.php?msg=".$msg."".$pagenav);
			exit;
		}
		if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM vendor_order_master WHERE invoice_no='" . $invoiceno . "' AND po_from='" . $partycode . "' AND status!='Cancelled'"))==0){
        //// Make System generated PO no.//////
  		$res_po = mysqli_query($link1, "select max(temp_no) as no from vendor_order_master where po_to='".$parentcode."'");
  		$row_po=mysqli_fetch_assoc($res_po);
      	$c_nos = $row_po[no]+1;
       	$po_no = $parentcode . "LP" . $c_nos;
        mysqli_autocommit($link1, false);
        $flag = true;
		$tax_total = $tot_sgst_amt+$tot_cgst_amt+$tot_igst_amt;
        ///// Insert Master Data
       $query1 = "INSERT INTO vendor_order_master set po_to='" . $parentcode . "',po_from='" . $partycode . "',po_no='" . $po_no . "',temp_no='" . $c_nos . "',ref_no='" . $ref_no . "',requested_date='" . $today . "',entry_date='" . $today . "',entry_time ='" . $currtime . "',req_type='LP',status='Pending',po_value='" . $sub_total . "',create_by='" . $_SESSION['userid'] . "',ip='" . $ip . "',taxtype='" . $tax_type . "',taxper='" . $tax_per . "',taxamount='" . $tax_total . "',currency_type='" . $currency_type . "',invoice_no='" . $invoiceno . "',invoice_date='" . $invoicedate . "',remark='" . $remark . "',delivery_address='" . $delivery_address . "',round_off='" . $round_off . "',total_sgst_amt='" . $tot_sgst_amt . "',total_cgst_amt='" . $tot_cgst_amt . "',total_igst_amt='" . $tot_igst_amt . "',total_amt='" . $grand_total . "',tcs_per='".$tcs_per."', tcs_amt='".$tcs_amt."',grand_total='".$final_total."',ledger_name='".$ledgername."', document_type='".$doctype."',freight='".$freight."',tds='".$tds_194q."'";
        $result = mysqli_query($link1, $query1);
		
        //// check if query is not executed
        if (!$result) {
            $flag = false;
            echo "Error details: " . mysqli_error($link1) . ".";
        }
        ///// Insert in item data by picking each data row one by one
        foreach ($prod_code as $k => $val) {
            // checking row value of product and qty should not be blank
            if ($prod_code[$k] != '' && $req_qty[$k] != '' && $req_qty[$k] != 0) {
                /////////// insert data
                 $query2 = "insert into vendor_order_data set po_no='" . $po_no . "', prod_code='" . $val . "', req_qty='" . $req_qty[$k] . "', po_price='" . $price[$k] . "', po_value='" . $value[$k] . "', mrp='" . $mrp[$k] . "', totalval='" . $linetotal[$k] . "',currency_type='" . $currency_type . "',sgst_per='" . $sgst_per[$k] . "', sgst_amt='" . $sgst_amt[$k] . "',cgst_per='" . $cgst_per[$k] . "', cgst_amt='" . $cgst_amt[$k] . "',igst_per='" . $igst_per[$k] . "', igst_amt='" . $igst_amt[$k] . "',uom='PCS'";
			
                $result = mysqli_query($link1, $query2);
                //// check if query is not executed
                if (!$result) {
                    $flag = false;
                    echo "Error details: " . mysqli_error($link1) . ".";
                }
            }// close if loop of checking row value of product and qty should not be blank
        }// close for loop
        ////// insert in activity table////
        $flag = dailyActivity($_SESSION['userid'], $po_no, "VPO", "ADD", $ip, $link1, $flag);
        ///// check both master and data query are successfully executed
        if ($flag) {
            mysqli_commit($link1);
            $msg = "Local Purchase Order is successfully placed with ref. no." . $po_no;
        } else {
            mysqli_rollback($link1);
            $msg = "Request could not be processed. Please try again.";
        }
        mysqli_close($link1);
		}else{
			$msg = "Request could not be processed. You have entered duplicate invoice no.";
		}
		}else {
		//you've sent this already!
		$msg="You have saved this already ";
		$cflag = "warning";
		$cmsg = "Warning";
	}
        ///// move to parent page
        header("location:localPurchaseList.php?msg=" . $msg . "" . $pagenav);
       exit;
    }
}
///get access product
$acc_psc = getAccessProduct($_SESSION['userid'],$link1);
///get access brand
$acc_brd = getAccessBrand($_SESSION['userid'],$link1);
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
                        var r = '<tr id="addr' + num + '"><td><span id="pdtid' + num + '"><select class="form-control selectpicker" data-live-search="true" name="prod_code[' + num + ']" id="prod_code[' + num + ']" onchange="getAvlStk(' + num + ');checkDuplicate(' + num + ',this.value);" required><option value="">--None--</option><?php $model_query = "select productcode,productname,productcolor from product_master where status='active' AND productsubcat IN (".$acc_psc.") AND brand IN (".$acc_brd.")";$check1 = mysqli_query($link1, $model_query);while ($br = mysqli_fetch_array($check1)) {?><option value="<?php echo $br['productcode']; ?>"><?= $br['productname'] . ' | ' . $br['productcolor'] . ' | ' . $br['productcode']; ?></option><?php } ?></select></span></td><td><input type="text" name="req_qty[' + num + ']" id="req_qty[' + num + ']" onKeyUp=rowTotal(' + num + '); class="digits form-control required" required value="0" style="text-align: right;padding: 4px;"/></td><td><input  name="price[' + num + ']" id="price[' + num + ']" type="text" class="form-control number required" required onKeyUp="rowTotal(' + num + ');" style="text-align: right;padding: 4px;" min="1"></td><td><input type="text" name="value[' + num + ']" id="value[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="sgst_per[' + num + ']" id="sgst_per[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="sgst_amt[' + num + ']" id="sgst_amt[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="cgst_per[' + num + ']" id="cgst_per[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="cgst_amt[' + num + ']" id="cgst_amt[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="igst_per[' + num + ']" id="igst_per[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px;"/></td><td><input type="text" name="igst_amt[' + num + ']" id="igst_amt[' + num + ']" class="form-control" value="0.00" readonly style="text-align: right;padding: 5px;"/></td><td><input type="text" class="form-control" name="linetotal[' + num + ']" id="linetotal[' + num + ']" autocomplete="off" readonly style="width:100px;text-align: right;padding: 4px;"><input type="hidden" class="form-control" name="avl_stock[' + num + ']" id="avl_stock[' + num + ']"  autocomplete="off"  style="width:50px;text-align: right" readonly value="0"><input name="mrp[' + num + ']" id="mrp[' + num + ']" type="hidden"/><input name="holdRate[' + num + ']" id="holdRate[' + num + ']" type="hidden"/></div><div style="display:inline-block;float:right"><i class="fa fa-close fa-lg" onClick="deleteRow(' + num + ');"></i></div></td></tr>';
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
                if (parseFloat(qty)) {
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
                }
                else {
                    alert("Please Enter Qty.");
                    document.getElementById(ent_qty).value = "";
                    document.getElementById(ent_rate).value = document.getElementById(ent_rate).value;
                }
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
                    <h2 align="center"><i class="fa fa-ship"></i> Add New Local Purchase </h2><br/>
                    <div class="form-group"  id="page-wrap" style="margin-left:10px;">
                        <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
                            <div class="form-group">
                                <div class="col-md-10"><label class="col-md-5 control-label">Purchase From<span style="color:#F00">*</span></label>
                                    <div class="col-md-7">
                                        <select name="po_from" id="po_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                                         <option value=""  selected="selected">Please Select </option>
                                            <?php
                                            $sql_parent = "select * from vendor_master where status='active' and id!=''";
                                            $res_parent = mysqli_query($link1, $sql_parent);
                                            while ($result_parent = mysqli_fetch_array($res_parent)) {
                                                ?>
                                                <option data-tokens="<?= $result_parent['name'] . " | " . $result_parent['id'] ?>" value="<?= $result_parent['id']  ?>" 
                                       <?php if ($result_parent['id'] == $_REQUEST['po_from']) echo "selected"; ?>> <?= $result_parent['name'] . " | " . $result_parent['city'] . " | " . $result_parent['state'] . " | " . $result_parent['country'] ?>
                                                </option>
                                                <?php
                                            }
                                            ?>   
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10"><label class="col-md-5 control-label">Purchase To<span style="color:#F00">*</span></label>
                                    <div class="col-md-7">
                                        <select name="po_to" id="po_to" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                                            <option value="" selected="selected">Please Select </option>
                                            <?php
                                            $sql_chl = "select * from access_location where uid='$_SESSION[userid]' and status='Y' AND id_type IN ('HO','BR')";
                                            $res_chl = mysqli_query($link1, $sql_chl);
                                            while ($result_chl = mysqli_fetch_array($res_chl)) {
                                                $party_det = mysqli_fetch_array(mysqli_query($link1, "select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
                                                //if ($party_det[id_type] == 'HO') {
                                                    ?>
                                                    <option data-tokens="<?= $party_det['name'] . " | " . $result_chl['location_id'] ?>" value="<?= $result_chl['location_id'] ?>" <?php if ($result_chl['location_id'] == $_REQUEST['po_to']) echo "selected"; ?> >
                                                        <?= $party_det['name'] . " | " . $party_det['city'] . " | " . $party_det['state'] . " | " . $result_chl['location_id'] ?>
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
                                <div class="col-md-10"><label class="col-md-5 control-label">Document Type<span style="color:#F00">*</span></label>
                                    <div class="col-md-7">
										<select name="doc_type" id="doc_type" class="form-control required" onChange="document.frm1.submit();">
											<option value=""<?php if($_REQUEST["doc_type"]==""){ echo "selected";}?>>Invoice</option>
                                            <option value="DC"<?php if($_REQUEST["doc_type"]=="DC"){ echo "selected";}?>>Delivery Challan</option>
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
                            <div class="form-group">
                                <div class="col-md-10">                                   
                                    <label class="col-md-5 control-label">Currency Type</label>
                                    <div class="col-md-3">
                                        <select name="currency_type" id="currency_type" required class="form-control required" onChange="document.frm1.submit();">
                                            <option value="INR"<?php if ($_REQUEST['currency_type'] == "INR") echo "selected"; ?>>INR</option>
                                            
                                        </select>
                                    </div>
                                    <label class="col-md-2 control-label"></label>
                                    <div class="col-md-2">
<!--                                <input type="text" name="cr_bal" id="cr_bal" class="form-control" value="<?= getCRBAL($_REQUEST['po_to'], $_REQUEST['po_from'], $link1); ?>" readonly/>-->
                                    </div>
                                </div>
                            </div>
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
                                                        $model_query = "select productcode,productname,productcolor from product_master where status='active' AND productsubcat IN (".$acc_psc.") AND brand IN (".$acc_brd.")";
                                                        $check1 = mysqli_query($link1, $model_query);
                                                        while ($br = mysqli_fetch_array($check1)) {
                                                            ?>
                                                            <option value="<?php echo $br['productcode']; ?>"><?= $br['productname'] . ' | ' . $br['productcolor'] . ' | ' . $br['productcode']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </span>
                                            </td>
                                            <td width="6%"><input type="text" class="form-control digits required"  name="req_qty[0]" id="req_qty[0]"  autocomplete="off" required onKeyUp="rowTotal(0);" style="text-align: right;padding: 4px; width:80px" value="0"></td>
                                            <td class="col-md-1"><input type="text" class="form-control number required" required name="price[0]" id="price[0]"  onKeyUp="rowTotal(0);" autocomplete="off" style="text-align: right;padding: 3px;  width:100px" min="1"></td>                                            
                                            <td width="10%"><input type="text" name="value[0]" id="value[0]" class="form-control" value="0.00" readonly style="text-align: right;padding: 2px; width:80px"/></td>
                                            <td width="6%"><input type="text" name="sgst_per[0]" id="sgst_per[0]" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px; width:80px"/></td>
                                            <td width="10%"><input type="text" name="sgst_amt[0]" id="sgst_amt[0]" class="form-control" value="0.00" readonly style="text-align: right;padding: 2px; width:80px"/></td>
                                            <td width="6%"><input type="text" name="cgst_per[0]" id="cgst_per[0]" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px; width:80px"/></td>
                                            <td width="10%"><input type="text" name="cgst_amt[0]" id="cgst_amt[0]" class="form-control" value="0.00" readonly style="text-align: right;padding: 2px; width:80px"/></td>
                                            <td width="6%"><input type="text" name="igst_per[0]" id="igst_per[0]" class="form-control" value="0.00" readonly style="text-align: right;padding: 4px; width:80px"/></td>
                                            <td width="10%"><input type="text" name="igst_amt[0]" id="igst_amt[0]" class="form-control" value="0.00" readonly style="text-align: right;padding: 2px; width:80px"/></td> 
                                            <td class="col-md-1"><input type="text" class="form-control" name="linetotal[0]" id="linetotal[0]" autocomplete="off" readonly style="width:85px;text-align: right;padding: 4px; width:100px"><input type="hidden" class="form-control" name="avl_stock[0]" id="avl_stock[0]"  autocomplete="off" style="width:50px;text-align: right;padding: 4px;" value="0" readonly>
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
                                    <label class="col-md-3 control-label">Invoice Date<span style="color:#F00">*</span></label>
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
                                    <label class="col-md-3 control-label"><!--Freight & Cartage--></label>
                                    <div class="col-md-3">
                                    	<!--<input type="text" name="freight" id="freight" class="form-control number" value="0.00" style="text-align:right"/>-->
                                    </div>
                                    <label class="col-md-3 control-label">TDS(194Q)</label>
                                    <div class="col-md-3">
                                        <input type="text" name="tds_194q" id="tds_194q" class="form-control number" value="0.00" style="text-align:right"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10">
                                    <label class="col-md-3 control-label">Delivery Address<span style="color:#F00">*</span></label>
                                    <div class="col-md-3">
                                        <textarea name="delivery_address" id="delivery_address" class="form-control required" style="resize:none" required><?=$from_ptydet[1]?></textarea>
                                    </div>
                                    <label class="col-md-3 control-label">Remark</label>
                                    <div class="col-md-3">
                                        <textarea name="remark" id="remark" class="form-control" style="resize:none"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="form-group">
                                <div class="col-md-12" align="center">
                                    <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Save" title="Save This PO" <?php if ($_POST['upd']=='Save'){ echo "disabled";}?>>
                                    <input type="hidden" name="parentcode" id="parentcode" value="<?= $_REQUEST['po_to'] ?>"/>
                                    <input type="hidden" name="partycode" id="partycode" value="<?= $_REQUEST['po_from'] ?>"/>
                                    <input type="hidden" name="doctype" id="doctype" value="<?= $_REQUEST['doc_type'] ?>"/>
                                    <input type="hidden" name="ledgername" id="ledgername" value="<?php if($_REQUEST['doc_type']=="DC"){ echo $_REQUEST['ledger_name'];}else{ echo "";}?>"/>
                                    <input type="hidden" name="currency_type" id="currency_type" value="<?= $_REQUEST['currency_type'] ?>"/>
                                    <a title="Back" type="button" class="btn btn-primary" onClick="window.location.href = 'localPurchaseList.php?<?= $pagenav ?>'">Back</a>
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
<?php if ($_REQUEST['po_to'] == '' || $_REQUEST['po_from'] == '' || ($_REQUEST['doc_type']=="DC" && $_REQUEST['ledger_name']=="")) { ?>
    <script>
        $("#frm2").find("input:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
    </script>
<?php } ?>