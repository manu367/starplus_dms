<?php
require_once("../config/config.php");
$post_cust = explode(" | ",$_REQUEST['po_to']);
$toloctiondet = explode("~",getCustomerDetails($post_cust[0],"state,category,address",$link1));
$fromloctiondet = explode("~",getLocationDetails($_REQUEST['po_from'],"state,city",$link1));
$cust_state = $toloctiondet[0];
//echo "----";
$floc_state = $fromloctiondet[0]; 
if($toloctiondet[0] == "" && $_REQUEST['po_from']!=''){ 
	$toloctiondet = $fromloctiondet;
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
 	<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 	<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
		$(document).ready(function() {
			$('#myTable').dataTable();
		});
		$(document).ready(function() {
			$("#frm1").validate();
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
    <script>
	/////////////////////////////// getting city dropdown ///////////////////////////////////////////////////////////////////
	function getCity(val){
    	if(val!=""){
			var strSubmit = "action=getCity&value="+val;
			var strURL = "../includes/getField.php";	
			var strResultFunc = "displayCity";
			xmlhttpPost(strURL,strSubmit,strResultFunc);
			return false;	
		}
	}
	function displayCity(result){
    	if(result != "" && result != 0){
			document.getElementById('citydiv').innerHTML = result;
		}
	}
	///// function to get price of product
	function get_price(ind) {
		var productIMEI = document.getElementById("imei[" + ind + "]").value;
		var locationCode = $('#po_from').val();
		var customer_state = $("#state").val();
		var  exist_custstate  =  $("#po_to").val();	
		var existcuststate =  exist_custstate.split(" | ");	
		var price_pickstr = document.getElementById("pricepickstr").value;
		var pricestate = price_pickstr.split("~");
		$.ajax({
			type: 'post',
			url: '../includes/getAzaxFields.php',
			data: {prodimei: productIMEI, loccode: locationCode, locstate: pricestate[0], lctype: pricestate[1]},
			success: function(data) {
//alert(data);
				var splitprice = data.split("~");
				document.getElementById("price[" + ind + "]").value = splitprice[0];
				document.getElementById("holdRate[" + ind + "]").value = splitprice[0];
				document.getElementById("mrp[" + ind + "]").value = splitprice[1];
				document.getElementById("descrip[" + ind + "]").value = splitprice[4];
				document.getElementById("avl_stock[" + ind + "]").value = splitprice[2];
				document.getElementById("prod_code[" + ind + "]").value = splitprice[3];
				
				document.getElementById("reward_info_chck[" + ind + "]").value = splitprice[9];
				document.getElementById("reward_point[" + ind + "]").value = splitprice[10];
				document.getElementById("coupon_code[" + ind + "]").value = splitprice[12];
				document.getElementById("coupon_amt[" + ind + "]").value = splitprice[13];
				
				
				if((splitprice[9] == 'Y') && (splitprice[10] >0)){
						   document.getElementById("prd_desc"+ind+"").innerHTML = "<a href='#' title='Reward Point' style='color:#FF0000;' data-toggle='popover' data-trigger='focus' data-content='"+splitprice[11]+"'><i class='fa fa-th'></i></a>";

			              rePop();
						
						 }
						 else {
						
						  }
				
									
				if ((splitprice[8] == customer_state) ){ ///// for new customer //////////////////////////////////////////////////////////
					document.getElementById("rowsgstper[" + ind + "]").value = splitprice[5];
					document.getElementById("rowcgstper[" + ind + "]").value = splitprice[6];
					$("#rowigstper[" + ind + "]").value = '0';
				}else if (splitprice[8] == $.trim(existcuststate[1])){ ///////////// for existing customer ///////////////////////////////////
					document.getElementById("rowsgstper[" + ind + "]").value = splitprice[5];
					document.getElementById("rowcgstper[" + ind + "]").value = splitprice[6];
					$("#rowigstper[" + ind + "]").value = '0';
				}
				else {
				
					$("#rowsgstper[" + ind + "]").value = '0';
					$("#rowcgstper[" + ind + "]").value = '0';
					document.getElementById("rowigstper[" + ind + "]").value = splitprice[7];
				}
				
			}
		});
	}
	</script>
    <script>
		/////// calculate line total /////////////
		function rowTotal(ind) {
			var ent_qty = "bill_qty" + "[" + ind + "]";
			var ent_rate = "price" + "[" + ind + "]";
			var hold_rate = "holdRate" + "[" + ind + "]";
			var availableQty = "avl_stock" + "[" + ind + "]";
			var prodmrpField = "mrp" + "[" + ind + "]";
			var discountField = "rowdiscount" + "[" + ind + "]";
			var rowtax = "taxType" + "[" + ind + "]";
			var totalvalField = "total_val" + "[" + ind + "]";
			var st = "rowsubtotal" + "[" + ind + "]";
			var rowsgstper = "rowsgstper" + "[" + ind + "]";
			var rowcgstper = "rowcgstper" + "[" + ind + "]";
			var rowigstper = "rowigstper" + "[" + ind + "]";
			var rowsgstamount = "rowsgstamount" + "[" + ind + "]";
			var rowcgstamount = "rowcgstamount" + "[" + ind + "]";
			var rowigstamount = "rowigstamount" + "[" + ind + "]";
			// check if entered qty is something

			if (document.getElementById(ent_qty).value) {
				var qty = document.getElementById(ent_qty).value;
			} else {
				var qty = 0;
			}
			//  check if entered price is somthing
			if (document.getElementById(ent_rate).value) {
				var price = document.getElementById(ent_rate).value;
			} else {
				var price = 0.00;
			}

			// check if discount value is something
			if (document.getElementById(discountField).value) {
				var dicountval = document.getElementById(discountField).value;
			} else {
				var dicountval = 0.00;
			}
			<?php if($cust_state == $floc_state){?>
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
			<?php }else{ ?>
			// check if igst per
			if (document.getElementById(rowigstper).value) {
				var igstper = (document.getElementById(rowigstper).value);
			} else {
				var igstper = 0.00;
			}
			<?php }?>
			// check entered qty should be available

			if (document.getElementById(availableQty).value == "Y") {
				if (parseFloat(price) >= parseFloat(dicountval)) {
					var total = parseFloat(qty) * parseFloat(price);
					var totalcost = parseFloat(price) - parseFloat(dicountval);
					<?php if($cust_state == $floc_state){?>
					var sgst_amt = ((totalcost * sgstper) / 100);
					var cgst_amt = ((totalcost * cgstper) / 100);
					document.getElementById(rowsgstamount).value = sgst_amt.toFixed(2);
					document.getElementById(rowcgstamount).value = cgst_amt.toFixed(2);
					var tot = parseFloat(totalcost) + parseFloat(sgst_amt) + parseFloat(cgst_amt);
					<?php }else{?>
					var igst_amt = ((totalcost * igstper) / 100);
					document.getElementById(rowigstamount).value = igst_amt.toFixed(2);
					var tot = parseFloat(totalcost) + parseFloat(igst_amt);
					<?php }?>
					document.getElementById(st).value = totalcost.toFixed(2);                        
					document.getElementById(totalvalField).value = tot.toFixed(2);
					calculatetotal();
					} else {
						alert("Discount is exceeding from price");
						var total = parseFloat(qty) * parseFloat(price);
						var var3 = "linetotal" + "[" + ind + "]";
						document.getElementById(var3).value = total.toFixed(2);
						document.getElementById(discountField).value = "0.00";
						document.getElementById(totalvalField).value = total.toFixed(2);
						calculatetotal();
					}
			}
			else {
				alert("Stock is not Available");
				document.getElementById(ent_qty).value = "";
				document.getElementById(availableQty).value = "";
				document.getElementById(ent_rate).value = "";
				document.getElementById(hold_rate).value = "";
				document.getElementById("imei[" + ind + "]").value = "";
			}
		}
		////// calculate final value of form /////
        function calculatetotal() {
        	var rowno1 = (document.getElementById("rowno").value);               
			// var sum_qty = 0;
			var sum_total = 0.00;
			var sum_ltotal = 0.00;
			var sum_discount = 0.00;
			//  var sum_tax = 0.00;
			var sum = 0.00;
			var qtyVal = 0.00;
            for (var i = 0; i <= rowno1; i++) {
				var temp_qty = "bill_qty" + "[" + i + "]";
				var temp_total = "rowsubtotal" + "[" + i + "]";
				var temp_discount = "rowdiscount" + "[" + i + "]";
				// var temp_taxamt = "rowtaxamount" + "[" + i + "]";
				var total_amt = "total_val" + "[" + i + "]";
				var price = "price" + "[" + i + "]";
				var discountvar = 0.00;
				// var totaltaxamt = 0.00;
				// var totalamtvar = 0.00;
				var total = 0.00;
				var ltotal = 0.00;
				var qty_val = 0.00;
				///// check if discount value is something
				if (document.getElementById(temp_discount).value) {
					discountvar = document.getElementById(temp_discount).value;
				} else {
					discountvar = 0.00;
				}
 				 ///// check if line total qty is something
				if (document.getElementById(temp_qty).value) {
					qty_val = document.getElementById(temp_qty).value;
				} else {
					qty_val = 0.00;
				}
				 
				///// check if line total amount is something
				if (document.getElementById(total_amt).value) {
					total = document.getElementById(total_amt).value;
				} else {
					total = 0.00;
				}
				///// check if line total amount is something
				if (document.getElementById(temp_total).value) {
					ltotal = document.getElementById(temp_total).value;
				} else {
					ltotal = 0.00;
				}

				qtyVal += parseFloat(qty_val);
				sum_discount += parseFloat(discountvar);
				sum += parseFloat(total);
				sum_ltotal += parseFloat(ltotal);

			}/// close for loop
			document.getElementById("sub_total").value = sum_ltotal.toFixed(2);
			var round_off = parseFloat(parseFloat(Math.round(sum)) - parseFloat(sum));
			document.getElementById("total_qty").value = qtyVal;   
			document.getElementById("total_discount").value = sum_discount.toFixed(2);
			document.getElementById("round_off").value = round_off.toFixed(2); 
			document.getElementById("grand_total").value = (Math.round(sum)).toFixed(2);
		}

		///// check total discount is exceeding from total minimum price of all product
		function check_total_discount() {
			if (parseFloat(document.getElementById("sub_total").value) < parseFloat(document.getElementById("total_discount").value)) {
				alert("Discount is exceeding..!!");
				document.getElementById("total_discount").value = "0.00";
				document.getElementById("grand_total").value = (parseFloat(document.getElementById("sub_total").value));
			} else {

				document.getElementById("grand_total").value = (parseFloat(document.getElementById("sub_total").value) - parseFloat(document.getElementById("total_discount").value) + parseFloat(document.getElementById("tax_amount").value));
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
			document.getElementById("tax_amount").value = (calculateTax).toFixed(2);

			document.getElementById("grand_total").value = (parseFloat(document.getElementById("sub_total").value) - parseFloat(disc) + parseFloat(calculateTax)).toFixed(2);
		}

	</script>
    <script>
	$(document).ready(function() {
		$("#add_row").click(function() {
			var numi = document.getElementById('rowno');
			var itm = "imei[" + numi.value + "]";
			var qTy = "bill_qty[" + numi.value + "]";
			var preno = document.getElementById('rowno').value;
			var num = (document.getElementById("rowno").value - 1) + 2;
			
			///// starting 3rd row///
			if(num > 1){ document.getElementById('cancel_img'+(num-1)).innerHTML=''; }

			

			if ((document.getElementById(itm).value != "" && document.getElementById(qTy).value != "" && document.getElementById(qTy).value != "0") || ($("#addr" + numi.value + ":visible").length == 0)) {
				numi.value = num;
				var r = '<tr id="addr' + num + '"><td><input type="text" class="form-control alphanumeric required" name="imei[' + num + ']" id="imei[' + num + ']"  autocomplete="off" required onBlur="get_price(' + num + ');checkDuplicateSerial(' + num + ',this.value);"></td><td><input type="text" class="form-control" name="descrip[' + num + ']" id="descrip[' + num + ']" readonly><div id="prd_desc'+num+'" style="display:inline-block;float:right"></div><input type="hidden" name="reward_info_chck[' + num + ']" id="reward_info_chck[' + num + ']"><input type="hidden" name="reward_point[' + num + ']" id="reward_point[' + num + ']"><input type="hidden" name="coupon_code[' + num + ']" id="coupon_code[' + num + ']"><input type="hidden" name="coupon_amt[' + num + ']" id="coupon_amt[' + num + ']"><input type="hidden" name="bill_qty[' + num + ']" id="bill_qty[' + num + ']" value="1"><input type="hidden" name="prod_code[' + num + ']" id="prod_code[' + num + ']"></td><td><input type="text" class="form-control number" name="price[' + num + ']" id="price[' + num + ']" onKeyUp="rowTotal(' + num + ');" autocomplete="off" required style="width:71px;text-align:right;padding: 4px"><input type="hidden" class="form-control" name="linetotal[' + num + ']" id="linetotal[' + num + ']"></td><td><input type="text" class="form-control number" name="rowdiscount[' + num + ']" id="rowdiscount[' + num + ']" autocomplete="off" onKeyUp="rowTotal(' + num + ');"  style="width:66px;text-align:right;padding: 4px" value="0"/></td><td><input type="text" class="form-control" name="rowsubtotal[' + num + ']" id="rowsubtotal[' + num + ']" autocomplete="off" value="" style="width:71px;text-align:right" readonly></td><?php if($cust_state == $floc_state){?><td><input type="text" class="form-control" name="rowsgstper[' + num + ']" id="rowsgstper[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td><td><input type="text" class="form-control" name="rowsgstamount[' + num + ']" id="rowsgstamount[' + num + ']" value="0" readonly style="width:60px;text-align:right;padding: 4px"></td><td><input type="text" class="form-control" name="rowcgstper[' + num + ']" id="rowcgstper[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td><td><input type="text" class="form-control" name="rowcgstamount[' + num + ']" id="rowcgstamount[' + num + ']" value="0" readonly style="width:60px;text-align:right;padding: 4px"></td><?php }else{?><td><input type="text" class="form-control" name="rowigstper[' + num + ']" id="rowigstper[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td><td><input type="text" class="form-control" name="rowigstamount[' + num + ']" id="rowigstamount[' + num + ']" value="0" readonly style="width:60px;text-align:right;padding: 4px"></td><?php }?><td><div style="display:inline-block;float:left"><input type="text" class="form-control" name="total_val[' + num + ']" id="total_val[' + num + ']" autocomplete="off" readonly style="width:80px;text-align:right"><input name="mrp[' + num + ']" id="mrp[' + num + ']" type="hidden"/><input name="holdRate[' + num + ']" id="holdRate[' + num + ']" type="hidden"/><input type="hidden" name="avl_stock[' + num + ']" id="avl_stock[' + num + ']"></div><div style="display:inline-block;float:right;padding: 4px" id="cancel_img' + num + '"><i class="fa fa-close fa-lg" onClick="fun_remove(' + num + ');"></i></div></td></tr>';
				$('#itemsTable1').append(r);
			}
		});
	});
	////// to remove new added row
	function fun_remove(con)
	{
		var c = document.getElementById('addr' + con);
		c.parentNode.removeChild(c);
		var count_row =document.getElementById('rowno').value -1;
		document.getElementById('rowno').value =count_row;
		//con--;
		//document.getElementById('rowno').value = con;
		//rowTotal(con);
		var preno = parseInt(count_row);
		//alert(preno);
		if(preno==0){
			rowTotal("0");
		}else{
			document.getElementById('cancel_img'+preno).innerHTML='<i class="fa fa-close fa-lg" onClick="fun_remove(' + preno + ');"></i>';
			//alert(count_row);
			rowTotal(count_row);
		}
	}
    </script>
    <script type="text/javascript">
            ///// function for checking duplicate IMEI value
            function checkDuplicateSerial(fldIndx1, enteredsno) {   
			 document.getElementById("upd").disabled = false;
                if (enteredsno != '') {
                    var check2 = "imei[" + fldIndx1 + "]";
                    var flag = 1;
                    for (var i = 0; i <= fldIndx1; i++) {
                        var check1 = "imei[" + i + "]";
                        if (fldIndx1 != i && document.getElementById(check2).value != '' && document.getElementById(check1).value != '') {
                            if ((document.getElementById(check2).value == document.getElementById(check1).value)) {
                                alert("Duplicate IMEI NO.");
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
            //// function to check whole form//
            function checkdata() {
                var maxno = document.getElementById("rowno").value;
                var flag = 1;
                for (var i = 0; i <= maxno; i++) {
                    var checkval = document.getElementById("imei[" + i + "]").value;
                    for (var j = 0; j <= maxno; j++) {
                        var checkvalin = document.getElementById("imei[" + j + "]").value;
                        if (j != i && checkvalin != '' && checkval != '') {
                            if (checkval == checkvalin) {
                                alert("Duplicate IMEI NO.");
                                document.getElementById("imei[" + j + "]").value = '';
                                document.getElementById("imei[" + j + "]").style.backgroundColor = "#F66";
                                document.getElementById("imei[" + j + "]").style.padding = '4';
                                flag *= 0;
                            } else {
                                document.getElementById("imei[" + j + "]").style.backgroundColor = "#FFFFFF";
                                document.getElementById("imei[" + j + "]").style.padding = '4';
                                flag *= 1;
                            }
                        }
                    }
                    ///// check available stock flag should not be N 
                    if (document.getElementById("avl_stock[" + i + "]").value == "N") {
                        flag *= 0;
                    } else {
                        flag *= 1;
                    }
                }
                if (flag == 0) {
                    document.getElementById("upd").disabled = true;
                    return false;
                } else {
                    document.getElementById("upd").disabled = false;
                    return true;
                }
            }
        </script>
    </head>
    <body>
    	<div class="container-fluid">
        	<div class="row content">
            	<?php
                	include("../includes/leftnav2.php");
                ?>
           		<div class="col-sm-9">
                	<h2 align="center"><i class="fa fa-user"></i> Retail Billing </h2>
                    <?php if ($_GET['msg']) { ?><h4 align="center" style="color:#FF0000"><?= $_GET['msg'] ?></h4><?php } ?>
                    <div class="form-group" id="page-wrap" style="margin-left:10px;">
                    	<form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
							<div style="display:inline-block;float:right">
        						<button title="Add Customer" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addCustomer.php?location<?=$pagenav?>'"><span>Add New Customer</span></button>&nbsp;&nbsp;&nbsp;&nbsp;</div>
                            <div class="form-group">
                            	<div class="col-md-12"><label class="col-md-3 control-label">Billing  From<span style="color:#F00">*</span></label>
                                	<div class="col-md-6">
                                    	<select name="po_from" id="po_from" required class="form-control selectpicker required" data-live-search="true"  onChange="document.frm1.submit();">
                                        	<option value="" selected="selected">Please Select </option>
                                            <?php
                                            $sql_parent = "select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y'";
                                            $res_parent = mysqli_query($link1, $sql_parent);
                                            while ($result_parent = mysqli_fetch_array($res_parent)) {
                                            	$party_det = mysqli_fetch_array(mysqli_query($link1, "select name , city, state,id_type from asc_master where asc_code='" . $result_parent['location_id'] . "'"));?>
                                            <option data-tokens="<?= $party_det['name'] . " | " . $result_parent['uid'] ?>" value="<?= $result_parent['location_id'] ?>" <?php if ($result_parent['location_id'] == $_REQUEST['po_from']) echo "selected"; ?>><?= $party_det['name'] . " | " . $party_det['city'] . " | " . $party_det['state'] . " | " . $result_parent['location_id'] ?></option>
                                           	<?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12"><label class="col-md-3 control-label">Customer Name<span style="color:#F00">*</span></label>
                                    <div class="col-md-6">
                                    	<select name="po_to" id="po_to" required class="form-control selectpicker required" data-live-search="true"  onChange="document.frm1.submit();">
                    						<option value="" selected="selected">Please Select </option>
											<?php 
                                            $sql_chl="select * from customer_master where mapplocation='".$_REQUEST['po_from']."' and status='Active'";
                                            $res_chl=mysqli_query($link1,$sql_chl);
                                            while($result_chl=mysqli_fetch_array($res_chl)){
                                            ?>
                    						<option data-tokens="<?=$result_chl['customername']." | ".$result_chl['city']?>" value="<?=$result_chl['customerid']." | ".$result_chl['state']?>" <?php if($result_chl['customerid']." | ".$result_chl['state']==$_REQUEST['po_to'])echo "selected";?>><?=$result_chl['customername']." | ".$result_chl['city']." | ".$result_chl['state']." | ".$result_chl['contactno']." | ".$result_chl['emailid']?></option>
                   			 				<?php
											}
                   				 			?>
               						  	</select>           
                                    </div>
                                </div>
                            </div>
                      	</form>
                     	<form id="frm2" name="frm2" class="form-horizontal" action="final_scheme_retail.php" method="post">
                        	<div class="form-group">
                                <table class="table table-bordered" width="100%" id="itemsTable1">
                                    <thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th style="text-align:center" width="20%"><?=$imeitag?></th>
                                            <th style="text-align:center" width="20%">Product</th>
                                            <th style="text-align:center" width="10%">Price</th>
                                            <th style="text-align:center" width="10%">Discount/Unit</th>
                                            <th style="text-align:center" width="10%">Value After Discount</th>
                                            <?php if($cust_state == $floc_state){?>
                                            <th style="text-align:center" width="10%">SGST(%)</th>
                                            <th style="text-align:center" width="10%">SGST Amt</th>
                                            <th style="text-align:center" width="10%">CGST(%)</th>
                                            <th style="text-align:center" width="10%">CGST Amt</th>
                                            <?php }else{?>
                                            <th style="text-align:center" width="10%">IGST(%)</th>
                                            <th style="text-align:center" width="10%">IGST Amt</th>
                                            <?php }?>
                                            <th style="text-align:center" width="10%">Total </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    	<tr id="addr0">
                                            <td><input type="text" class="form-control alphanumeric required" name="imei[0]" id="imei[0]" autocomplete="off" required onBlur="get_price(0);checkDuplicateSerial(0, this.value);" style="padding: 4px;"></td>
                                            <td>
                                                <input type="text" class="form-control" name="descrip[0]" id="descrip[0]"  style="padding: 4px;" readonly="">
												<div id="prd_desc0" style="display:inline-block;float:right"></div>
												<input type="hidden" name="reward_info_chck[0]" id="reward_info_chck[0]" >
												<input type="hidden" name="reward_point[0]" id="reward_point[0]" >
												<input type="hidden" name="coupon_code[0]" id="coupon_code[0]" >
												<input type="hidden" name="coupon_amt[0]" id="coupon_amt[0]" >
												
                                            </td>
                                            <td><input type="text" class="form-control number" name="price[0]" id="price[0]" onKeyUp="rowTotal(0);" autocomplete="off" required style="width:71px;text-align:right;padding: 4px"></td>
                                            <td><input type="text" class="form-control number" name="rowdiscount[0]" id="rowdiscount[0]" autocomplete="off" onKeyUp="rowTotal(0);" style="width:66px;text-align:right;padding: 4px" value="0"></td>
                                            <td><input type="text" class="form-control" name="rowsubtotal[0]" id="rowsubtotal[0]" value="0" style="width:71px;text-align:right;padding: 4px" readonly></td>
                                            <?php if($cust_state == $floc_state){?>
                                            <td><input type="text" class="form-control" name="rowsgstper[0]" id="rowsgstper[0]" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td>
                                            <td><input type="text" class="form-control" name="rowsgstamount[0]" id="rowsgstamount[0]" value="0" readonly style="width:60px;text-align:right;padding: 4px"></td>
                                            <td><input type="text" class="form-control" name="rowcgstper[0]" id="rowcgstper[0]" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td>
                                            <td><input type="text" class="form-control" name="rowcgstamount[0]" id="rowcgstamount[0]" value="0" readonly style="width:60px;text-align:right;padding: 4px"></td>
                                            <?php }else{?>
                                            <td><input type="text" class="form-control" name="rowigstper[0]" id="rowigstper[0]" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td>
                                            <td><input type="text" class="form-control" name="rowigstamount[0]" id="rowigstamount[0]" value="0" readonly style="width:60px;text-align:right;padding: 4px"></td>
                                            <?php }?>
                                            <td><input type="text" class="form-control" name="total_val[0]" id="total_val[0]" autocomplete="off" readonly  style="width:80px;text-align:right;padding: 4px">
                                                <input type="hidden" name="avl_stock[0]" id="avl_stock[0]">
                                                <input name="mrp[0]" id="mrp[0]" type="hidden"/>
                                                <input name="holdRate[0]" id="holdRate[0]" type="hidden"/>
                                                <input type="hidden" name="bill_qty[0]" id="bill_qty[0]" value="1">
                                                <input type="hidden" class="form-control" name="linetotal[0]" id="linetotal[0]">
                                                <input type="hidden" name="prod_code[0]" id="prod_code[0]">
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
                                    <label class="col-md-2 control-label">Discount</label>
                                    <div class="col-md-2">
                                        <input type="text" name="total_discount" id="total_discount" class="form-control" value="0.00" style="width:200px;text-align:right" readonly/>
                                    </div>
                                </div>
                            </div>
                             <div class="form-group">
                                <div class="col-md-12">                                    
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-2">
                                    </div>
                                    <label class="col-md-2 control-label">Sub Total </label>
                                    <div class="col-md-2">
                                        <input type="text" name="sub_total" id="sub_total" class="form-control" value="0.00" readonly style="width:200px;text-align:right"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-2">
                                    </div>
                                    <label class="col-md-2 control-label">Round Off </label>
                                    <div class="col-md-2">
                                        <input type="text" name="round_off" id="round_off" class="form-control" value="0.00" readonly style="width:200px;text-align:right"/>
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
                                        <input type="text" name="grand_total" id="grand_total" class="form-control" value="<?php echo ($po_row['po_value'] - $po_row['discount']); ?>" readonly style="width:200px;text-align:right"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-3 control-label">Delivery Address <span style="color:#F00">*</span></label>
                                    <div class="col-md-2">
                                        <textarea name="delivery_address" id="delivery_address" class="form-control addressfield required" style="resize:none; width:200px" required><?php echo $toloctiondet[2]; ?></textarea>
                                    </div>
                                    <label class="col-md-2 control-label">Remark</label>
                                    <div class="col-md-2">
                                        <textarea name="remark" id="remark" class="form-control addressfield" style="resize:none;width:200px" ></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" align="center">
                                    <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Next" title="Make Invoice" onClick="return checkdata();">
                                    &nbsp;
                                    <a title="Back"  class="btn btn-primary" onClick="window.location.href = 'retailbillinglist.php?<?= $pagenav ?>'">Back</a>
                                    <input type="hidden" name="parentcode" id="parentcode" value="<?=$_REQUEST['po_from']?>"/>
                                    <input type="hidden" name="partycode" id="partycode" value="<?=$_REQUEST['po_to']?>"/>           
                                    <input type="hidden" name="disc_type" id="disc_type" value="<?=$_REQUEST['discount_type']?>"/>
                                    <input type="hidden" name="tx_type" id="tx_type" value="<?=$_REQUEST['tax_type']?>"/>
                                    <input type="hidden" name="pricepickstr" id="pricepickstr" value="<?=$toloctiondet[0]."~RETAIL"?>"/>
                                </div>
                            </div>
                        </form>
                    </div><!--close panel group-->
                </div><!--close col-sm-9-->
            </div><!--close row content-->
        </div><!--close container-fluid-->
        <?php 	
		if ($_REQUEST['po_from'] == '' || $_REQUEST['po_to'] == '' ) { ?>
            <script>
                $("#frm2").find("input:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
            </script>
            <?php
        }
        include("../includes/footer.php");
        include("../includes/connection_close.php");
        ?>
    </body>
</html>