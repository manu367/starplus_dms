<?php
require_once("../config/config.php");




$bill_from = $_POST["parentcode"];
$bill_to = explode(" | ",$_POST["partycode"]);
////// posted array data to be handled
$post_serial = $_POST["imei"];
$post_partcode = $_POST["prod_code"];
$post_price = $_POST["price"];
$post_rowdisc = $_POST["rowdiscount"];
$post_rowsubt = $_POST["rowsubtotal"];
$post_rowsgstp = $_POST["rowsgstper"];
$post_rowsgsta = $_POST["rowsgstamount"];
$post_rowcgstp = $_POST["rowcgstper"];
$post_rowcgsta = $_POST["rowcgstamount"];
$post_rowigstp = $_POST["rowigstper"];
$post_rowigsta = $_POST["rowigstamount"];
$post_totval = $_POST["total_val"];
//$post_reward_info_chck  = $_POST["reward_info_chck"];
$post_reward_point = $_POST["reward_point"];
$post_coupon_code = $_POST['coupon_code'];
$post_coupon_amt= $_POST['coupon_amt'];


/////////
$arr_pqty = array();
$arr_price = array();
$arr_disc = array();
$arr_subt = array();
$arr_sgstp = array();
$arr_sgsta = array();
$arr_cgstp = array();
$arr_cgsta = array();
$arr_igstp = array();
$arr_igsta = array();
$arr_totval = array();
$arr_serial = array();
$arr_couponcode = array();
$arr_couponamt = array();
//$arr_reward_info_chck = array();
$arr_reward_point = array();

////// make partcode wise serial nos
for($i=0; $i<count($post_serial); $i++){
	if(empty($arr_serial[$post_partcode[$i]])){ $arr_serial[$post_partcode[$i]] = $post_serial[$i];}else{ $arr_serial[$post_partcode[$i]] .= ",".$post_serial[$i];}
	$arr_pqty[$post_partcode[$i]] += 1;
	$arr_price[$post_partcode[$i]] += $post_price[$i];
	$arr_disc[$post_partcode[$i]] += $post_rowdisc[$i];
	$arr_subt[$post_partcode[$i]] += $post_rowsubt[$i];
	$arr_sgstp[$post_partcode[$i]] += $post_rowsgstp[$i];
	$arr_sgsta[$post_partcode[$i]] += $post_rowsgsta[$i];
	$arr_cgstp[$post_partcode[$i]] += $post_rowcgstp[$i];
	$arr_cgsta[$post_partcode[$i]] += $post_rowcgsta[$i];
	$arr_igstp[$post_partcode[$i]] += $post_rowigstp[$i];
	$arr_igsta[$post_partcode[$i]] += $post_rowigsta[$i];
	$arr_totval[$post_partcode[$i]] += $post_totval[$i];
    $arr_couponcode[$post_partcode[$i]] .= ",".$post_coupon_code[$i];
    $arr_couponamt[$post_partcode[$i]] .= ",".$post_coupon_amt[$i];
    $arr_reward_point[$post_partcode[$i]] += $post_reward_point[$i];
     $couponstrval = $arr_couponcode[$post_partcode[$i]];
    $couponstramt = $arr_couponamt[$post_partcode[$i]];
	
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?=siteTitle?></title>
    <script src="../js/jquery.js"></script>
 	<link href="../css/font-awesome.min.css" rel="stylesheet">
 	<link href="../css/abc.css" rel="stylesheet">
 	<script src="../js/bootstrap.min.js"></script>
 	<link href="../css/abc2.css" rel="stylesheet">
 	<link rel="stylesheet" href="../css/bootstrap.min.css">
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
	
	function showCoupon(coupon, amount){
		$.get('showCoupondetails.php?couponcode=' + coupon +'&coupon_amt=' + amount, function(html){
			 $('#viewModalcoupon .modal-body').html(html);
			 $('#viewModalcoupon').modal({
				show: true,
				backdrop:"static"
			});
		 });
		 $("#viewModalcoupon #tile_name").html("<i class='fa fa-tags'></i> Coupon Available");
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
	///// function to get price of product
	function checkSerialNo(ind) {
		var productIMEI = document.getElementById("schserial"+ind).value;
		var locationCode = '<?=$bill_from?>';
		$.ajax({
			type: 'post',
			url: '../includes/getAzaxFields.php',
			data: {checkSerialIsAvl: productIMEI, loccode: locationCode},
			success: function(data) {
				//alert(data);
				var splitprice = data.split("~");
				if(splitprice[0]=="Y"){
					document.getElementById("msg"+ind).innerHTML = "";
				}else{
					document.getElementById("schserial"+ind).value = "";
					document.getElementById("msg"+ind).innerHTML = splitprice[2];
				}
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
		var num1 = document.getElementById("norow").value;
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
				var num1 = document.getElementById("norow").value;
				document.getElementById("norow").value = parseInt(parseInt(num1)+1);
				
				var list = $("#tbl_scheam");
				var nextid = ($('#tbl_scheam tr').length);
				
				$.each(list, function(i) {
								
				var r='<tr id="'+nextid+'" align="right"><td align="left">'+val[5]+'</td><td><input type="text" name="schqty'+nextid+'" id="schqty'+nextid+'" style="width:50px;text-align:right;" class="form-control" value="'+val[0]+'" readonly/><input type="hidden" name="schprd'+nextid+'" id="schprd'+nextid+'" value="'+val[4]+'"/></td><td><i class="fa fa-inr" aria-hidden="true"></i> 0.00</td><td colspan="2"><input type="text" name="schserial'+nextid+'" id="schserial'+nextid+'" class="form-control" placeholder="Enter serial no." required onBlur="checkSerialNo(' + nextid + ');"/><span id="msg'+nextid+'" style="color:red;"></span></td><td>0</td><td><i class="fa fa-inr" aria-hidden="true"></i> 0.00</td><td>0</td><td><i class="fa fa-inr" aria-hidden="true"></i> 0.00</td><td><i class="fa fa-inr" aria-hidden="true"></i> 0.00</td><td>FOC&nbsp;&nbsp;<i style="color:red;" id="canicn_'+nextid+'" class="fa fa-close" onclick="removeROW(this,'+val[3]+','+val[0]+')" ></i></td></tr>';
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
			//rowTotal(val[3]);		
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
		calculatetotal();	
	}
	////// calculate final value of form /////
	function calculatetotal() {
		var rowno1 = (document.getElementById("count").value);
		//alert(rowno1);
		var sum_discount = 0.00;
		var sum_subtotal = 0.00;
		var sum_cgstamt = 0.00;
		var sum_sgstamt = 0.00;
		var sum_igstamt = 0.00;
		var sum_totalval = 0.00;
		for (var i = 1; i < rowno1; i++) {
			///// check if discount value is something
			if (document.getElementById("rowdiscount"+i).value) {
				var discountvar = document.getElementById("rowdiscount"+i).value;
			} else {
				var discountvar = 0.00;
			}
			////// calculate sub total with discount value
			var valAfterDisc = (parseInt(document.getElementById("bill_qty"+i).value) * parseFloat(document.getElementById("rowprice"+i).value)) - parseFloat(discountvar);
			document.getElementById("rowsubtotal"+i).value = valAfterDisc.toFixed(2);
			///// check if sub total value is something
			if (document.getElementById("rowsubtotal"+i).value) {
				var subtotalamt = document.getElementById("rowsubtotal"+i).value;
			} else {
				var subtotalamt = 0.00;
			}
			<?php if($cust_state == $floc_state){?>
			////// calculate cgst amt
			var cgstamount = (parseFloat(document.getElementById("cgstper"+i).value) * parseFloat(subtotalamt))/100; 
			document.getElementById("cgstamt"+i).value = cgstamount.toFixed(2);
			///// check if cgst value is something
			if (document.getElementById("cgstamt"+i).value) {
				var cgstvar = document.getElementById("cgstamt"+i).value;
			} else {
				var cgstvar = 0.00;
			}
			////// calculate sgst amt
			var sgstamount = (parseFloat(document.getElementById("sgstper"+i).value) * parseFloat(subtotalamt))/100; 
			document.getElementById("sgstamt"+i).value = sgstamount.toFixed(2);
			///// check if sgst value is something
			if (document.getElementById("sgstamt"+i).value) {
				var sgstvar = document.getElementById("sgstamt"+i).value;
			} else {
				var sgstvar = 0.00;
			}
			////// calculate line total
			var linetot = parseFloat(subtotalamt) + parseFloat(cgstvar) + parseFloat(sgstvar);
			document.getElementById("total_val"+i).value = linetot.toFixed(2);
			<?php }else{?>
			////// calculate igst amt
			var igstamount = (parseFloat(document.getElementById("igstper"+i).value) * parseFloat(subtotalamt))/100; 
			document.getElementById("igstamt"+i).value = igstamount.toFixed(2);
			///// check if igst value is something
			if (document.getElementById("igstamt"+i).value) {
				var igstvar = document.getElementById("igstamt"+i).value;
			} else {
				var igstvar = 0.00;
			}
			////// calculate line total
			var linetot = parseFloat(subtotalamt) + parseFloat(igstvar);
			document.getElementById("total_val"+i).value = linetot.toFixed(2);
			<?php }?>
			///// check if total value is something
			if (document.getElementById("total_val"+i).value) {
				var totalamt = document.getElementById("total_val"+i).value;
			} else {
				var totalamt = 0.00;
			}
			sum_discount += parseFloat(discountvar);
			sum_subtotal += parseFloat(subtotalamt);
			<?php if($cust_state == $floc_state){?>
			sum_cgstamt += parseFloat(cgstvar);
			sum_sgstamt += parseFloat(sgstvar);
			<?php }else{?>
			sum_igstamt += parseFloat(igstvar);
			<?php }?>
			sum_totalval += parseFloat(totalamt);
		}/// close for loop
		document.getElementById("total_discount").value = sum_discount.toFixed(2);
		document.getElementById("subTotal").value = sum_subtotal.toFixed(2);
		var round_off = parseFloat(parseFloat(Math.round(sum_totalval)) - parseFloat(sum_totalval)).toFixed(2);
		document.getElementById("totalTax").value = (sum_cgstamt+sum_sgstamt+sum_igstamt).toFixed(2);
		document.getElementById("roundOff").value = round_off;
		document.getElementById("grandTotal").value = Math.round(sum_totalval).toFixed(2);
	}
	function checkdata() {
		var flag = 1;
		if(document.getElementById("deliveryAddress").value!="" && document.getElementById("parentcode").value!="" && document.getElementById("partycode").value!="" && document.getElementById("total_qty").value!="0" && document.getElementById("total_qty").value!=""){
			flag *= 1;
		}else{
			flag *= 0;
		}
		//////
		var no_row = parseInt(document.getElementById("norow").value);
		var count_vl = parseInt(document.getElementById("count").value);
		if(no_row>0){
			for(var p= count_vl; p < (count_vl + no_row); p++){
				var checkval = document.getElementById("schserial"+p).value;
				for (var j = count_vl; j < (count_vl + no_row); j++) {
					var checkvalin = document.getElementById("schserial"+j).value;
					if (j != p && checkvalin != '' && checkval != '') {
						if (checkval == checkvalin) {
							alert("Duplicate Serial NO.");
							document.getElementById("schserial"+j).value = '';
							document.getElementById("schserial"+j).style.backgroundColor = "#F66";
							document.getElementById("schserial"+j).style.padding = '4';
							//flag *= 0;
						} else {
							document.getElementById("schserial"+j).style.backgroundColor = "#FFFFFF";
							document.getElementById("schserial"+j).style.padding = '4';
							//flag *= 1;
						}
					}
				}
			}
		}
		///////////////////
		if (flag == 0) {
			document.getElementById("upd").style.display = "none";
			return false;
		} else {
			document.getElementById("upd").style.display = "";
			return true;
		}
	}
	
	
	function recalculateamt(){
 
	var couponcode = document.getElementById("couponcode_show").value;
	if(couponcode) { ///// check whether coupon code is empty or not ///////////////////////////////////
	var index = document.getElementById("count").value;
    var subtotalval = 0.00;
	 for(var i =1 ; i<index ; i++) {
	 var prodcode = document.getElementById("prodcode"+i).value;
	  $.ajax({
			type: 'post',
			url: '../includes/getAzaxFields.php',
			data: {checkCouponIsAvl: couponcode, productcode: prodcode , indexval:i},
			success: function(data) {
		    var splitprice = data.split("~");
			if(splitprice[0] != ''){
			   
			   /// fisrt check coupon is only applicable only when price is greater than coupon code ////////////////////////
			 var price = document.getElementById("rowprice"+splitprice[2]).value;
			 if(parseInt(price) > parseInt(splitprice[1])) {
					  
			 //// placed coupon name and coupon amt value  in hidden  variable so that we cam save in data table //////////////////
			 
			 document.getElementById("coupon_codename"+splitprice[2]).value = splitprice[0];
			 document.getElementById("couponamount"+splitprice[2]).value = splitprice[1];
			 			
			 ///// step 1  fisrt find coupon percentage /////////////////////////////
			 var linetotal = document.getElementById("total_val"+splitprice[2]).value;
			 var coupon_per =  (splitprice[1]* 100)/linetotal;
			 var couponperfixed = coupon_per.toFixed(2);  ///// final 2 decimal place coupon percentage //////////////////////////
			 
			  ////////// step 2 coupon percentage of value of linewise rows ///////////////////////////////
			  var linewise_value =  ((document.getElementById("rowsubtotal"+splitprice[2]).value ) * couponperfixed)/100 ;
			  var linewisefixedvalue =  linewise_value.toFixed(2); //// final 2 decimal plcae of linewise value ////////////////
			  
			  //////////// step 3  subtract  linewise value coupon percentage value from  value //////////////////////////////
			  var final_subvalue = (document.getElementById("rowsubtotal"+splitprice[2]).value ) - (linewisefixedvalue);
			  var fixedfinal_subvalue = final_subvalue.toFixed(2);  //// final value 
			  document.getElementById("rowsubtotal"+splitprice[2]).value = fixedfinal_subvalue;  ///// place coupon apply value on value column ////////////////////
			  //// tax calculation 
			  if(document.getElementById("sgstper"+splitprice[2]).value) {
			  var sgst_amt =  ((document.getElementById("sgstper"+splitprice[2]).value)* fixedfinal_subvalue)/100;
			  document.getElementById("sgstamt"+splitprice[2]).value = sgst_amt.toFixed(2);
			  
			   var cgst_amt =  ((document.getElementById("cgstper"+splitprice[2]).value)* fixedfinal_subvalue)/100;
			   document.getElementById("cgstamt"+splitprice[2]).value = cgst_amt.toFixed(2);
			   var totalamt = parseFloat(cgst_amt) + parseFloat(sgst_amt) + fixedfinal_subvalue ;
			   var sumtot = parseFloat(cgst_amt) + parseFloat(sgst_amt) + parseFloat(fixedfinal_subvalue);
			   document.getElementById("total_val"+splitprice[2]).value = sumtot.toFixed(2) ;	
			   calcuateTotal(index);			   
			   } 
			   else {
				   var igst_amt =  ((document.getElementById("igstper"+splitprice[2]).value)* fixedfinal_subvalue)/100;
				   document.getElementById("igstamt"+splitprice[2]).value = igst_amt.toFixed(2);
				   document.getElementById("total_val"+splitprice[2]).value =  parseFloat(fixedfinal_subvalue)+ parseFloat(igst_amt) ;
				   calcuateTotal(index);	
			    }
				document.getElementById("err_msg").innerHTML = "";
				 
			  }
			  else {
			    document.getElementById("err_msg").innerHTML = "Coupon Code is not Applicable";
				document.getElementById("Submit").disabled = true;
			    }
			  }else {}	
			}
		});	
		
	   } /// for loop ends ////////////////////////	   
	  } /// if condition ends ///////////////////////////////
	  else {
	    
	    document.getElementById("err_msg").innerHTML = "Please enter couponcode";
	    document.getElementById("Submit").disabled = false; 
	   } 
	   	 
	}
	
	function calcuateTotal(ind){
	var c = ind;
	var subtotalnew =0.00;
	var sum_gst =0.00;
	var grandtotal =0.00;

	 for(var i=1; i < c ;i++){
		var tempt_rs="rowsubtotal"+i+"";
		var tempt_s="sgstamt"+i+"";
		//var tempt_i="igstamt"+i+"";
		var tempt_c="cgstamt"+i+"";
	
		subtotalnew+=parseInt(document.getElementById(tempt_rs).value);
		sum_gst+=parseFloat(document.getElementById(tempt_c).value)+parseFloat(document.getElementById(tempt_s).value);
      
		}
		///alert(sum_gst);alert(sum_amt);alert(sum_tot);alert(tot_qty);
		document.getElementById("subTotal").value=subtotalnew.toFixed(2);
		document.getElementById("totalTax").value=sum_gst.toFixed(2);
		document.getElementById("grandTotal").value=(subtotalnew+sum_gst).toFixed(2);
		document.getElementById("err_msg").innerHTML = "";
	    document.getElementById("Submit").disabled = true;
	
				 }
	
	function getValidate(val){
	
	  if(val == ''){
	  //location.reload();
	  calculatetotal();
	  document.getElementById("Submit").disabled = false;

	   }else {
	   
	  // document.getElementById("Submit").disabled = true;
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
			
                <div class="alert alert-success">
                    <strong>From:</strong>&nbsp;&nbsp;<?php $fromLocation = str_replace("~",",",getLocationDetails($bill_from,"name,city,state",$link1)); echo $fromLocation;?>
                </div>
                <div class="alert alert-success">
                    <strong>To:</strong>&nbsp;&nbsp;<?php 
				  /// bill to party
				  $billto=getLocationDetails($bill_to[0],"name,city,state",$link1);
				  $explodeval=explode("~",$billto);
				  if($explodeval[0]){ $toparty=$billto; }else{ $toparty=getCustomerDetails($bill_to[0],"customername,city,state",$link1);}
				  echo str_replace("~",",",$toparty);?>
                </div>
		
                    
			
			
                <form id="frm2" name="frm2" class="form-horizontal" action="saveRetailBilling.php" method="post">
                <!--<div class="panel-group col-lg-12">-->
                	<?php 
					foreach($arr_serial as $product => $serialnos){ $serial_str = explode(",",$serialnos);?>
                    <div class="panel panel-info table-responsive col-lg-4" style="height:150px;">
                    <div class="panel-heading"><?=str_replace("~"," | ",getProductDetails($product,"productname,productcolor,productcode",$link1))?> (Serial Nos.)</div>
                       	<div class="panel-body">
                           <ol>
                           <?php for($k=0; $k<count($serial_str); $k++){?>
                        	<li><?=$serial_str[$k]?></li>
                            <?php }?>
                           </ol>
                        </div><!--close panel body-->
                    </div><!--close panel-->
                    <?php }?>
					
					
					
                    <div class="panel panel-info table-responsive col-lg-12">
                    	<div class="panel-heading">Item Information</div>
                       	<div class="panel-body">
                        	<table class="table table-bordered table-responsive" width="100%" id="tbl_scheam">
                            <thead>
                                <tr class="<?=$tableheadcolor?>" >
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Discount</th>
                                    <th>Value</th>
                                    <?php if($cust_state == $floc_state){?>
                                    <th>SGST(%)</th>
                                    <th>SGST Amt</th>
                                    <th>CGST(%)</th>
                                    <th>CGST Amt</th>
                                    <?php }else{?>
                                    <th>IGST(%)</th>
                                    <th>IGST Amt</th>
                                    <?php }?>
                                    <th>Total </th>
                                    <th>Scheme </th>
                                </tr>
                            </thead>
                            <tbody>
                              	<?php
								$i=1; 
							
								foreach($arr_pqty as $partcode => $qty){
							  
							    $proddet=explode("~",getProductDetails($partcode,"productname,productcolor,productcode",$link1));  
								$amt = ltrim($arr_couponcode[$partcode],",");
								
									
									  
									  
									
								?>
                             <tr align="right">
                                <td align="left"><?=$proddet[0]." | ".$proddet[1]." | ".$proddet[2]?><input type="hidden" name="prodcode<?=$i?>" id="prodcode<?=$i?>" value="<?=$partcode?>">
								<input type="hidden" name="reward_point<?=$i?>" id="reward_point<?=$i?>" value="<?=$arr_reward_point[$partcode]?>" >
								<input type="hidden" name="coupon_codename<?=$i?>" id="coupon_codename<?=$i?>" >
								<input type="hidden" name="couponamount<?=$i?>" id="couponamount<?=$i?>" >
								</td>
                                <td><?=$qty?><input type="hidden" name="bill_qty<?=$i?>" id="bill_qty<?=$i?>" value="<?=$qty?>">
								
								</td>
                                <td><?php //echo currencyFormat(($arr_price[$partcode]/$qty))?><input type="text" class="form-control" name="rowprice<?=$i?>" id="rowprice<?=$i?>" value="<?=($arr_price[$partcode]/$qty)?>" style="width:72px;text-align:right;padding: 5px;" readonly></td>
                                <td><?php //echo currencyFormat(($arr_disc[$partcode]))?><input type="text" class="form-control" name="rowdiscount<?=$i?>" id="rowdiscount<?=$i?>" value="<?=($arr_disc[$partcode])?>" style="width:72px;text-align:right;padding: 5px;" readonly></td>
                                <td><?php //echo currencyFormat(($arr_subt[$partcode]))?><input type="text" class="form-control" name="rowsubtotal<?=$i?>" id="rowsubtotal<?=$i?>" value="<?=($arr_subt[$partcode])?>" style="width:72px;text-align:right;padding: 5px;" readonly></td>
                                <?php if($cust_state == $floc_state){?>
                                <td><?php //echo round($arr_sgstp[$partcode]/$qty)?><input type="text" class="form-control" name="sgstper<?=$i?>" id="sgstper<?=$i?>" value="<?=$arr_sgstp[$partcode]/$qty?>" style="width:50px;text-align:right;padding: 5px;" readonly></td>
                                <td><?php //echo currencyFormat(($arr_sgsta[$partcode]))?><input type="text" class="form-control" name="sgstamt<?=$i?>" id="sgstamt<?=$i?>" value="<?=($arr_sgsta[$partcode])?>" style="width:72px;text-align:right;padding: 5px;" readonly></td>
                                <td><?php //echo round($arr_cgstp[$partcode]/$qty)?><input type="text" class="form-control" name="cgstper<?=$i?>" id="cgstper<?=$i?>" value="<?=$arr_cgstp[$partcode]/$qty?>" style="width:50px;text-align:right;padding: 5px;" readonly></td>
                                <td><?php //echo currencyFormat(($arr_cgsta[$partcode]))?><input type="text" class="form-control" name="cgstamt<?=$i?>" id="cgstamt<?=$i?>" value="<?=($arr_cgsta[$partcode])?>" style="width:72px;text-align:right;padding: 5px;" readonly></td>
                                <?php }else{?>
                                <td><?php //echo round($arr_igstp[$partcode]/$qty)?><input type="text" class="form-control" name="igstper<?=$i?>" id="igstper<?=$i?>" value="<?=$arr_igstp[$partcode]/$qty?>" style="width:50px;text-align:right;padding: 5px;" readonly></td>
                                <td><?php //echo currencyFormat(($arr_igsta[$partcode]))?><input type="text" class="form-control" name="igstamt<?=$i?>" id="igstamt<?=$i?>" value="<?=($arr_igsta[$partcode])?>" style="width:72px;text-align:right;padding: 5px;" readonly></td>
                                <?php }?>
                                <td><?php //echo currencyFormat(($arr_totval[$partcode]))?><input type="text" class="form-control" name="total_val<?=$i?>" id="total_val<?=$i?>" value="<?=($arr_totval[$partcode])?>" style="width:72px;text-align:right;padding: 5px;" readonly></td>
                                <td align="center"><a href="#" onClick="checkScheme('<?=$partcode?>','<?=$i?>','<?=$arr_subt[$partcode]?>','<?=$proddet[0]." | ".$proddet[1]." | ".$proddet[2]?>');" id="scheme<?=$i?>" title='Applicable Schemes'><i class='fa fa-tags fa-lg'></i></a><input type="hidden"  name="sch_cd<?=$i?>"  id="sch_cd<?=$i?>" value="" /></td>
                              </tr>
                              <?php $i++; }?>
                            </tbody>  
                            </table>
                            <input type="hidden"  id="count" name="count" value="<?=$i;?>">
							<input type="hidden"  id="norow" name="norow" value="0">
                            <input type="hidden"  id="qtyto" name="qtyto" value="<?=$_POST["total_qty"]?>">
                            <input type="hidden"  id="arr_serial" name="arr_serial" value="<?php print_r(urlencode(base64_encode(serialize($arr_serial))));?>">
                        </div><!--close panel body-->
                    </div><!--close panel-->
                    <?php /*?><div class="panel panel-info table-responsive col-lg-12">
                    	<div class="panel-heading">Scheme Information</div>
                       	<div class="panel-body">
                        	<table class="table table-bordered table-responsive" width="100%" id="itemsTable2">
                            <thead>
                                <tr class="<?=$tableheadcolor?>" >
                                    <th>Select</th>
                                    <th>Scheme Code</th>
                                    <th>Scheme Name</th>
                                    <th>Validity</th>
                                    <th>Applicable On</th>
                                    <th>Min. Criteria</th>
                                    <th>Scheme Given</th>
                                    <th>Offer</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
							$i=1;
							$arr_unipart = array_unique($post_partcode);
							$sql_schm = "SELECT * FROM scheme_master WHERE productcode IN ('".implode("','",$arr_unipart)."') AND from_date <= '".$today."' AND to_date >= '".$today."' AND status='Active'"; 
							$res_schm = mysqli_query($link1,$sql_schm) or die(mysqli_error($link1));
							if(mysqli_num_rows($res_schm)>0){
								while($row_schm = mysqli_fetch_assoc($res_schm)){
							  ?>
                              <tr>
                                <td><input name="scheme_applicable" id="schm<?=$i?>" type="radio" value="<?=$row_schm["scheme_code"]?>" <?php  if($_REQUEST['scheme_applicable'] == $row_schm['scheme_code']){ echo "checked"; } ?> /></td>
                                <td><?=$row_schm["scheme_code"]?></td>
                                <td><?=$row_schm['from_date']." to ".$row_schm['to_date'];?></td>
                                <td><?=$row_schm['scheme_name'];?></td>
                                <td><?=$row_schm['scheme_based_type'];?></td>
                                <td align="right"><?=$row_schm['scheme_based_on'];?></td>
                                <td><?=$row_schm['scheme_given_type'];?></td>
                                <td align="right"><?=$row_schm['scheme_given'];?></td>
                              </tr>
                              <?php
								$i++;
								}
							}else{
							?>
							  <tr>
								<td colspan="8" align="center">No scheme found</td>
							  </tr>
							<?php
							}
							?>
                            </tbody>  
                            </table>

                        </div><!--close panel body-->
                    </div><!--close panel-->
                <!--</div>--><!--close panel group--><?php */?>
                <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Total Qty</label>
                      <div class="col-md-6">
                        <input name="total_qty" id="total_qty" type="text" class="form-control" value="<?=$_POST["total_qty"]?>" readonly><input type="hidden" name="total_qty1" id="total_qty1" value="0"/>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Sub Total</label>
                      <div class="col-md-6">
                      	<input name="subTotal" id="subTotal" type="text" class="form-control" value="<?=$_POST["sub_total"]?>" readonly>
                      </div>
                    </div>
                </div>
				
				
				<div class="form-group">
          <div class="col-md-12">
              <label class="col-md-2 control-label">Apply Coupon Code</label>
             
               <div class="col-md-3 ">
			   <div style="margin-top:7px;">
  					<a href="#" onClick="showCoupon('<?=$couponstrval?>','<?=$couponstramt?>');" id="show_coupon" title='Applicable Coupon' style="margin-top:2px;"><i class='fa fa-external-link fa-lg'></i></a>  </div>
			   </div>
                 
              
              <label class="col-md-2 control-label">Enter Coupon Code</label>
              
             <div class="col-md-3 ">
  				  <input type="text" name="couponcode_show" id="couponcode_show" value="<?=$_REQUEST['couponcode_show']?>" class="form-control" onBlur="getValidate(this.value);" >
			   </div>
             
             <div class="col-md-2" style="text-align:center;">
               <input name="Submit" id="Submit" type="button" class="btn btn-primary" value="Apply!"   title="Apply!"   onClick="recalculateamt();">
			   <span id="err_msg"  class="red_small"></span>
               
            </div>
           
          </div>
        </div>
				
                <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Discount</label>
                      <div class="col-md-6">
                        <input name="total_discount" id="total_discount" type="text" class="form-control" value="<?=$_POST["total_discount"]?>" readonly>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Tax Amt</label>
                      <div class="col-md-6">
                      	<input name="totalTax" id="totalTax" type="text" class="form-control" value="<?=array_sum($arr_cgsta)+array_sum($arr_sgsta)+array_sum($arr_igsta)?>" readonly>
                      </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Round Off</label>
                      <div class="col-md-6">
                        <input name="roundOff" id="roundOff" type="text" class="form-control" value="<?=$_POST["round_off"]?>" readonly>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Grand Total</label>
                      <div class="col-md-6">
                      	<input name="grandTotal" id="grandTotal" type="text" class="form-control" value="<?=$_POST["grand_total"]?>" readonly>
                      </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Delivery Address <span style="color:#F00">*</span></label>
                      <div class="col-md-6">
                        <textarea name="deliveryAddress" id="deliveryAddress" class="form-control addressfield required" style="resize:none;" required><?php echo $_POST["delivery_address"];?></textarea>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Remark</label>
                      <div class="col-md-6">
                      	<textarea name="remark" id="remark" class="form-control addressfield" style="resize:none;"><?php echo $_POST["remark"];?></textarea>
                      </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12" align="center">
                        <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Process" title="Make Invoice" onClick="return checkdata();">
                        &nbsp;
                        <a title="Discard"  class="btn btn-primary" onClick="window.location.href = 'retailbillinglist.php?<?= $pagenav ?>'">Discard</a>
                        <input type="hidden" name="parentcode" id="parentcode" value="<?=base64_encode($bill_from)?>"/>
                        <input type="hidden" name="partycode" id="partycode" value="<?=base64_encode($bill_to[0])?>"/>
                        <input type="hidden" name="pricepickstr" id="pricepickstr" value="<?=$toloctiondet[0]."~RETAIL"?>"/>
                    </div>
                </div>
                </form>
            </div><!--close col sm 9-->
       	</div><!--close row content-->
	</div><!--close container-->
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
		
		
		<div class="modal modalTH fade" id="viewModalcoupon" role="dialog">
			 <form id="frm4" name="frm4" class="form-horizontal" action="" method="post">
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
						
                            <button type="button" id="btnCancel1" class="btn <?=$btncolor?>" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
				 </form>
            </div>
		
        <?php
        include("../includes/footer.php");
        include("../includes/connection_close.php");
        ?>
</body>
</html>