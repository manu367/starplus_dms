<?php
require_once("../config/config.php");
$loc=explode("~",getLocationDetails($_REQUEST['location'],"state,id_type,disp_addrs,city",$link1));
$cust=explode("~",getCustomerDetails($_REQUEST['customer'],"state,city",$link1));
if($cust[0]==""){
	$cust = explode("~",getLocationDetails($_REQUEST['customer'],"state,id_type,disp_addrs,city",$link1));
}
@extract($_POST);
////// if we hit process button
if ($_POST['upd']=="Save")
{	
	//// initialize transaction parameters
	$flag = true;
	mysqli_autocommit($link1, false);
	$error_msg = "";	
	//////////////
	/*$query_code = "SELECT MAX(sys_ref_temp_no) FROM credit_note WHERE location_id='".$_POST['location_code']."'";
	$result_code = mysqli_query($link1,$query_code);
	$arr_result2 = mysqli_fetch_array($result_code);
	$code_id = $arr_result2[0];
	$pad =++$code_id;
	$mobiCode = "CR/".$_POST['location_code']."/".$pad;*/
	$res_cnt = mysqli_query($link1, "SELECT srn_str, srn_counter FROM document_counter WHERE location_code='" . $_POST['location_code'] . "'");
	$row_cnt = mysqli_fetch_array($res_cnt);
	$invcnt = $row_cnt['srn_counter'] + 1;
	//$pad = str_pad($invcnt, 4, 0, STR_PAD_LEFT);
	$pad = $invcnt;
	$mobiCode = $row_cnt['srn_str'].$pad;
	//// check any credit note is created against the selected invoice
	if(mysqli_num_rows(mysqli_query($link1,"SELECT sno FROM credit_note WHERE ref_no='".$mobiCode."'"))==0){
		//$post_rmk = realstring($link1,$_REQUEST['rmk']);
		//// get some invoice details from billing table
		$inv_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT sub_location FROM billing_master WHERE challan_no='".$_POST['invoice_no']."'"));
		$sql= "INSERT INTO credit_note SET cust_id='".$_POST['custid']."',location_id='".$_POST['location_code']."',sub_location='".$inv_det["sub_location"]."',entered_ref_no='".$_POST['invoice_no']."',ref_no='".$mobiCode."',sys_ref_temp_no='".$pad."',create_by='".$_SESSION['userid']."',remark='".$_POST['remark']."',create_date='".$today."',amount='".$_POST['grand_total']."',status='Pending For Approval',create_ip='".$ip."' ,basic_amt = '".$_POST['sub_total']."' , discount_type = '".$_POST['disc_type']."' , discount = '".$_POST['total_discount']."',round_off='".$round_off."',tcs_per='".$tcs_per."', tcs_amt='".$tcs_amt."',sgst_amt='".$total_sgstamt."',cgst_amt='".$total_cgstamt."',igst_amt='".$total_igstamt."',tax_cost='" . $tax_amount . "' ";
		$db_add= mysqli_query($link1,$sql);
		//// check if query is not executed
		if (!$db_add) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
		$resultdc = mysqli_query($link1, "UPDATE document_counter SET srn_counter=srn_counter+1,update_by='" . $_SESSION['userid'] . "',updatedate='" . $datetime . "' WHERE location_code='" .$_POST['location_code']. "'");
		//// check if query is not executed
		if (!$resultdc) {
			$flag = false;
			$error_msg = "Error Code1.1: ".mysqli_error($link1);
		}
		///// Insert in billing data by picking each data row one by one
		foreach($prod_code as $k=>$val)
		{   
			if($val!='' && $req_qty[$k]!='' && $req_qty[$k]!=0){
				/////////// insert data
				$query2 = "INSERT INTO credit_note_data SET prod_code='".$val."',req_qty='".$req_qty[$k]."' , price='".$price[$k]."', value='".$linetotal[$k]."',discount_per = '".$dis_per[$k]."' , discount='".$rowdiscount[$k]."', totalvalue='".$total_val[$k]."',ref_no='".$mobiCode."',entry_date='".$today."' ,sgst_per='".$sgst_per[$k]."' ,sgst_amt='".$sgst_amt1[$k]."',igst_per='".$igst_per[$k]."' ,igst_amt='".$igst_amt1[$k]."',cgst_per='".$cgst_per[$k]."' ,cgst_amt='".$cgst_amt1[$k]."'";				
				$result1 = mysqli_query($link1, $query2);
				//// check if query is not executed
				if (!$result1) {
					$flag = false;
					$error_msg = "Error details2: " . mysqli_error($link1) . ".";
				}
			}
			$data_table  = mysqli_query($link1, "UPDATE billing_model_data SET crdr_qty = crdr_qty+'".$req_qty[$k]."' WHERE challan_no='".$_POST['invoice_no']."' AND id='".$ch_id[$k]."' AND prod_code='".$val."'");
			//// check if query is not executed
			if (!$data_table) {
				$flag = false;
				$error_msg = "Error details3: " . mysqli_error($link1) . ".";
			}
		}/// close for loop				
	}
	///// check both master and data query are successfully executed
	if ($flag) {
		mysqli_commit($link1);
		$msg = "Credit Note is successfully created with ref. no.".$mobiCode;
		$cmsg = "success";
		$cflag = "success"; 
	} else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again. ".$error_msg;
		$cmsg = "danger";
		$cflag = "danger"; 
	} 
	mysqli_close($link1);
	header("location:process_credit_notes.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
}
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
<link href='../css/select2.min.css' rel='stylesheet' type='text/css'>
<script src='../js/select2.min.js'></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#frm2").validate();
});
$(document).ready(function(){
	$("#location").select2({
  		ajax: {
   			url: "../includes/getAzaxFields.php",
			type: "post",
			dataType: 'json',
			delay: 250,
   			data: function (params) {
    			return {
					searchCust: params.term, // search term
					requestFor: "allloc",
					userid: '<?=$_SESSION['userid']?>'
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
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript">
/////// calculate line total /////////////
function rowTotal(ind){
	var ent_qty="req_qty["+ind+"]";
	var qtyval ="qty["+ind+"]";
	var ent_rate="price["+ind+"]";
	var hold_rate="holdRate["+ind+"]";
	var availableQty="avl_stock["+ind+"]";
	var prodCodeField="prod_code["+ind+"]";
	var prodmrpField="mrp["+ind+"]";
	var discountField="rowdiscount["+ind+"]";
	var totalvalField="total_val["+ind+"]";
	var disperField="dis_per["+ind+"]";
	var rowsgstper = "sgst_per[" + ind + "]";
	var rowcgstper = "cgst_per[" + ind + "]";
	var rowigstper = "igst_per[" + ind + "]";
	var rowsgstamount = "sgst_amt[" + ind + "]";
	var rowsgstamount1 = "sgst_amt1[" + ind + "]";
	var rowcgstamount = "cgst_amt[" + ind + "]";
	var rowcgstamount1 = "cgst_amt1[" + ind + "]";
	var rowigstamount = "igst_amt[" + ind + "]";
	var rowigstamount1 = "igst_amt1[" + ind + "]";
  	var holdRate=document.getElementById(hold_rate).value;
  	var discper=document.getElementById(disperField).value;
  	var invqty=document.getElementById(qtyval).value;  
	var locstate = '<?=$loc[0]?>'; 
	var custstate = '<?=$cust[0]?>'; 
  	////// check if entered qty is something
  	if(document.getElementById(ent_qty).value){ 
		var qty=document.getElementById(ent_qty).value;
	}else{ 
		var qty=0;
	}
  	/////  check if entered price is somthing
  	if(document.getElementById(ent_rate).value){ 
		var price=document.getElementById(ent_rate).value;
	}else{ 
		var price=0.00;
	}
	///// for SGST/CGST
	if(locstate == custstate){
   		// check if cgst per
   		if(document.getElementById(rowcgstper).value) {
       		var cgstper = document.getElementById(rowcgstper).value;
        }else{
            var cgstper = 0.00;
       	}
		//  check if sgst per
		if(document.getElementById(rowsgstper).value){
			var sgstper = document.getElementById(rowsgstper).value;
		}else{ 
			var sgstper = 0.00;
		}
	}else {
  		// check if igst per
    	if(document.getElementById(rowigstper).value) {
         	var igstper = (document.getElementById(rowigstper).value);
       	}else{
           	var igstper = 0.00;
        }
  	}  
	///// check if discount value is something
	// if(document.getElementById(discountField).value){ var dicountval=document.getElementById(discountField).value;}else{ var dicountval=0.00; }
	////// check entered qty should be available
  	if( parseInt(invqty) >= parseInt(qty) ){
     	var total= parseFloat(qty)*parseFloat(price);
	 	var discountamt=0.00;
	  	if(document.getElementById(disperField).value!=''){
			//alert(document.getElementById(disperField).value);
			//alert(total);  
	  		var discountamt = ((parseFloat(total) * parseFloat(discper))/100);
	  		//alert(discountamt);
	 	 	document.getElementById(discountField).value=discountamt;
	  	}else{ 
	    	discountamt=0.00; 
			document.getElementById(discountField).value=0.00;
	  	}
  		var totalcost=(parseFloat(total)-parseFloat(discountamt));
	 	if(locstate == custstate){
	 		var sgst_amt = ((totalcost * sgstper) / 100);
     		var cgst_amt = ((totalcost * cgstper) / 100);
	 	}else {
     		var igst_amt = ((totalcost * igstper) / 100);
	 	}
		if(locstate == custstate){
      		document.getElementById(rowsgstamount).value = sgst_amt.toFixed(2);
	   		document.getElementById(rowsgstamount1).value = sgst_amt.toFixed(2);
      		document.getElementById(rowcgstamount).value = cgst_amt.toFixed(2);
	    	document.getElementById(rowcgstamount1).value = cgst_amt.toFixed(2);
      		var tot = parseFloat(totalcost) + parseFloat(sgst_amt) + parseFloat(cgst_amt);
       	}else{
      		document.getElementById(rowigstamount).value = igst_amt.toFixed(2);
	  		document.getElementById(rowigstamount1).value = igst_amt.toFixed(2);
      		var tot = parseFloat(totalcost) + parseFloat(igst_amt);
       	}  
     	var var3="linetotal["+ind+"]";
    	document.getElementById(var3).value=total.toFixed(2);
     	document.getElementById(totalvalField).value=tot.toFixed(2);
     	calculatetotal();
  	}
	else{  
		alert("Enter qty cannot be more than Invoice qty");
		document.getElementById(ent_qty).value="";
		//document.getElementById(availableQty).value="";
		document.getElementById(ent_rate).value="";
		document.getElementById(hold_rate).value="";
		document.getElementById(prodCodeField).value="";
		document.getElementById(prodmrpField).value="";
		document.getElementById(prodCodeField).focus();
  	}
}
////// calculate final value of form /////
function calculatetotal(){
	var rowno=(document.getElementById("rowno").value);
	var sum_qty=0;
	var sum_total=0.00; 
	var final_total=0.00; 
	var sum_discount=0.00;
	var sum_sgst = 0.00;
	var sum_cgst = 0.00;
	var sum_igst = 0.00;
    for(var i=0;i<=rowno;i++){
		var temp_qty="req_qty["+i+"]";
		var temp_total="linetotal["+i+"]";		
		var temp_totalval="total_val["+i+"]";
		var temp_discount="rowdiscount["+i+"]";
		<?php if($loc[0]==$cust[0]){ ?>
		var temp_sgst = "sgst_amt" + "[" + i + "]";					          
		var temp_cgst = "cgst_amt" + "[" + i + "]";	
		<?php }else{?>				          
		var temp_igst = "igst_amt" + "[" + i + "]";					          
		<?php }?>
		var discountvar=0.00;
		var totalamtvar=0.00;
		var totalamtgrand=0.00;
		var  total_sgst = 0.00;
		var  total_cgst = 0.00;
		var  total_igst = 0.00;
		///// check if discount value is something
		if(document.getElementById(temp_discount).value){ discountvar= document.getElementById(temp_discount).value;}else{ discountvar=0.00;}
		///// check if line total value is something
        if(document.getElementById(temp_total).value){ totalamtvar= document.getElementById(temp_total).value;}else{ totalamtvar=0.00;}
		
		if(document.getElementById(temp_totalval).value){ totalamtgrand= document.getElementById(temp_totalval).value;}else{ totalamtgrand=0.00;}
		///// check if line qty is something
        if(document.getElementById(temp_qty).value){ totqty= document.getElementById(temp_qty).value;}else{ totqty=0;}
		<?php if($loc[0]==$cust[0]){ ?>
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
		sum_qty+=parseFloat(totqty);
		sum_total+=parseFloat(totalamtvar);
		sum_discount+=parseFloat(discountvar);
		
		<?php if($loc[0]==$cust[0]){ ?>
		sum_sgst += parseFloat(total_sgst);
		sum_cgst += parseFloat(total_cgst);
		<?php }else{?>
		sum_igst += parseFloat(total_igst);
		<?php }?>
		
		final_total+=parseFloat(totalamtgrand);
	}/// close for loop
    document.getElementById("total_qty").value=sum_qty;
    document.getElementById("sub_total").value=sum_total.toFixed(2);
    <?php //if($_REQUEST[discount_type]=="PD"){ ?>	
    document.getElementById("total_discount").value=sum_discount.toFixed(2);
    <?php //} ?>
	document.getElementById("tax_amount").value = (sum_sgst+sum_cgst+sum_igst).toFixed(2);
	 <?php if($loc[0]==$cust[0]){ ?>
	 document.getElementById("total_sgstamt").value = (sum_sgst).toFixed(2);
	 document.getElementById("total_cgstamt").value = (sum_cgst).toFixed(2);
	 <?php }else{?>
	 document.getElementById("total_igstamt").value = (sum_igst).toFixed(2);
	 <?php }?>
	document.getElementById("grand_total").value=parseFloat(final_total);
	////// check if TCS is applicable or not
	var tcs = document.getElementById("tcs_per").value;
	if(tcs){
		var ft = (final_total * parseFloat(tcs))/100;
		document.getElementById("tcs_amt").value=(ft).toFixed(2);
		var ftwro = (final_total+ft).toFixed(2);
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
		var ftwro = final_total.toFixed(2);
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
function check_total_discount(){
	if(parseFloat(document.getElementById("sub_total").value) < parseFloat(document.getElementById("total_discount").value)){
	  alert("Discount is exceeding..!!");
	  document.getElementById("total_discount").value="0.00";
	  document.getElementById("grand_total").value=(parseFloat(document.getElementById("sub_total").value)).toFixed(2);
	}else{
	  document.getElementById("grand_total").value=(parseFloat(document.getElementById("sub_total").value)-parseFloat(document.getElementById("total_discount").value)).toFixed(2);	
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
      			<h2 align="center"><i class="fa fa-shopping-basket"></i> Itemwise Credit Note </h2><br/>
      			<div class="form-group" id="page-wrap" style="margin-left:10px;">
          			<form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          			<div class="form-group">
            			<div class="col-md-10"><label class="col-md-5 control-label">Location<span style="color:#F00">*</span></label>
              				<div class="col-md-7">
                            	<select name="location" id="location" class="form-control required" onChange="document.frm1.submit();">        
                                    <option value=''>--Please Select--</option>
                                    <?php
                                    if(isset($_POST["location"])){
                                      $loc_name = explode("~",getAnyDetails($_POST["location"],"name, city, state","asc_code","asc_master",$link1));
                                    echo '<option value="'.$_POST["location"].'" selected>'.$loc_name[0].' | '.$loc_name[1].' | '.$loc_name[2].' | '.$_POST["location"].'</option>';
                                    }
                                    ?>
                                </select>
              				</div>
            			</div>
          			</div>
          			<div class="form-group">
            			<div class="col-md-10"><label class="col-md-5 control-label">Customer<span style="color:#F00">*</span></label>
              				<div class="col-md-7">
                 				<select name="customer" id="customer" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                 					<option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_parent="select customerid, customername,city,state from customer_master where mapplocation='".$_REQUEST['location']."'";
					$res_parent=mysqli_query($link1,$sql_parent);
					while($result_parent=mysqli_fetch_array($res_parent)){    
                          ?>
                    <option data-tokens="<?=$result_parent['customername']." | ".$result_parent['customerid']?>" value="<?=$result_parent['customerid']?>" <?php if($result_parent['customerid']==$_REQUEST['customer'])echo "selected";?> >
                       <?=$result_parent['customername']." | ".$result_parent['city']." | ".$result_parent['state']." | ".$result_parent['customerid']?>
                    </option>
                    <?php
					}
                    ?>
                    <?php 
					$sql_parent="select to_location as mapped_code from billing_master where from_location='".$_REQUEST['location']."'";
					$res_parent=mysqli_query($link1,$sql_parent);
					if(mysqli_num_rows($res_parent)>0){
						while($result_parent=mysqli_fetch_array($res_parent)){
							  $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_parent[mapped_code]'"));
							  ?>
					  <option value="<?=$result_parent['mapped_code']?>" <?php if($result_parent['mapped_code']==$_REQUEST['customer'])echo "selected";?> >
					  <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_parent['mapped_code']?>
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
            <div class="col-md-10"><label class="col-md-5 control-label">Discount Type<span style="color:#F00">*</span></label>
              <div class="col-md-7">
                 <select name="discount_type" id="discount_type" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
				 <option value="" selected="selected">--Please Select--</option>
                 <option value="PD"<?php if($_REQUEST['discount_type']=="PD")echo "selected";?>>Product Wise Discount</option>
                 <?php /*?><option value="TD"<?php if($_REQUEST['discount_type']=="TD")echo "selected";?>>Total Discount</option>
                 <option value="NONE"<?php if($_REQUEST['discount_type']=="NONE")echo "selected";?>>NONE</option><?php */?>
                 </select>
              </div>
            </div>
          </div>
		  
          <div class="form-group">
          <div class="col-md-10">
              <label class="col-md-5 control-label">Ref No.<span style="color:#F00">*</span></label>
              <div class="col-md-3">
                <select name="inv_no" id="inv_no" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                 <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_parent="select challan_no  from billing_master where from_location='".$_REQUEST['location']."' and to_location='".$_REQUEST['customer']."' and document_type='INVOICE' and status IN ('Dispatched','Received') and crdr_flag = '' ";
					$res_parent=mysqli_query($link1,$sql_parent);
					while($result_parent=mysqli_fetch_array($res_parent)){
	                      ?>
                    <option data-tokens="<?=$result_parent['challan_no']?>" value="<?=$result_parent['challan_no']?>"<?php if($result_parent['challan_no']==$_REQUEST['inv_no'])echo "selected";?>>
                       <?=$result_parent['challan_no']?>
                    </option>
                     <?php
					}
                    ?>
                 </select>
              </div>
              <label class="col-md-1 control-label">
<!--                  Discount Type-->
              </label>
              <div class="col-md-3">
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
                <th class="col-md-1" style="font-size:13px">Qty</th>
                <th data-hide="phone"  class="col-md-1" style="font-size:13px">Price</th>
                <th data-hide="phone"  class="col-md-1" style="font-size:13px">Value</th>
				
				<th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">Discount %</th>
                <th data-hide="phone,tablet" class="col-md-1" style="font-size:13px">Discount Amount</th>
				<?php if($loc[0] == $cust[0]){?>				
				<th data-hide="phone,tablet" class="col-md-1" style="font-size:12px">SGST %</th>
				 <th data-hide="phone,tablet" class="col-md-1" style="font-size:12px">SGST Amount </th>
				 <th data-hide="phone,tablet" class="col-md-1" style="font-size:12px">CGST %</th>
				 <th data-hide="phone,tablet" class="col-md-1" style="font-size:12px">CGST Amount </th>
				 <?php } else {?>
				 <th data-hide="phone,tablet" class="col-md-1" style="font-size:12px">IGST %</th>
				 <th data-hide="phone,tablet" class="col-md-1" style="font-size:12px">IGST Amount</th>
				 <?php }?>
                <th data-hide="phone,tablet" class="col-md-2" style="font-size:13px">Total</th>

              </tr>
            </thead>
            <tbody>
						
			<?php
			$i=0;
			$tot_cgst = 0.00;
			$tot_sgst = 0.00;
			$tot_igst = 0.00;
			$total_qty = 0.00;
			$toqty = 0;
			$sql=mysqli_query($link1,"Select * from billing_model_data  where from_location='".$_REQUEST['location']."' and challan_no='".$_REQUEST['inv_no']."'");
			while($row=mysqli_fetch_assoc($sql)){
				$toqty = $row['qty']-$row['crdr_qty'];
			   if($row['crdr_qty'] != $row['qty']) {
			 
			?>
              <tr id='addr<?php echo $i;?>'>
                <td class="col-md-2">
                    <div id="pdtid0" style="display:inline-block;float:left; width:200px">
					<input type="text"    class="form-control"   value="<?php echo getProduct($row['prod_code'],$link1);?>" readonly>
                  	<input  type="hidden" name="prod_code[<?php echo $i;?>]" id="prod_code[<?php echo $i;?>]"  value="<?=$row['prod_code']?>"/>
				  	<input  type="hidden" name="qty[<?php echo $i;?>]" id="qty[<?php echo $i;?>]"  value="<?=intval($toqty)?>"/>
                 	</div>
                </td>
                <td class="col-md-1"><input type="text" class="form-control digits" name="req_qty[<?php echo $i;?>]" id="req_qty[<?php echo $i;?>]"  autocomplete="off" required onBlur="rowTotal(0);"  value="<?php echo ($toqty);?>"></td>
                <td class="col-md-1"><input type="text" class="form-control number" name="price[<?php echo $i;?>]" id="price[<?php echo $i;?>]" onBlur="rowTotal(<?php echo $i;?>);" autocomplete="off" value="<?php echo $row['price'];?>" required></td>
                <td class="col-md-1"><input type="text" class="form-control" name="linetotal[<?php echo $i;?>]" id="linetotal[<?php echo $i;?>]" value="<?php if($row['crdr_qty'] > 0.00) {echo ($row['price']*$row['crdr_qty']);} else {echo $row['price']*$toqty; }?>" autocomplete="off" readonly></td>
				
				<td class="col-md-1"><input type="text" class="form-control number" name="dis_per[<?php echo $i;?>]" id="dis_per[<?php echo $i;?>]" autocomplete="off" onBlur="rowTotal(<?php echo $i;?>);" <?php if($_REQUEST['discount_type']!="PD"){?> disabled="disabled"<?php } ?>></td>
                <td class="col-md-1"><input type="text" class="form-control number" name="rowdiscount[<?php echo $i;?>]" id="rowdiscount[<?php echo $i;?>]" autocomplete="off" value="<?php echo $row['discount'];?>" readonly <?php if($_REQUEST['discount_type']!="PD"){?> disabled="disabled"<?php } ?>></td>
				<?php
				if($row['crdr_qty'] > 0.00){
				       $totprice  = $row['price']*$row['crdr_qty'];
					   $sgstnewval = ($row['sgst_per']*$totprice)/100;
					   $cgstnewval = ($row['cgst_per']*$totprice)/100;
					   $igstnewval = ($row['igst_per']*$totprice)/100;
					   $newqty+= $row['crdr_qty'];
					   $newsubtot+= $totprice;
					   $newvaltot+= $totprice+$cgstnewval+$igstnewval+$sgstnewval ;
					   
					   $totcgstval += $cgstnewval;
					   $totsgstval += $sgstnewval;
					   $totigstval += $igstnewval;
				   }else{
				   	   $totpric  = $row['price']*$toqty;
				   	   $sgstval = ($row['sgst_per']*$totpric)/100;
					   $cgstval = ($row['cgst_per']*$totpric)/100;
					   $igstval = ($row['igst_per']*$totpric)/100;
					   
					   $totcgstval += $cgstval;
					   $totsgstval += $sgstval;
					   $totigstval += $igstval;
				   }
				 ?>
				 
				<?php if($loc[0] == $cust[0]){
				?>
				<td><div style="display:inline-block;float:left"><input type="text" style="width:55px;text-align:right;"  name="sgst_per[<?php echo $i;?>]" id="sgst_per[<?php echo $i;?>]" class="form-control" onBlur="rowTotal(<?php echo $i;?>);"  value="<?php echo $row['sgst_per'];?>" readonly><input name="sgst_per[<?php echo $i;?>]" id="sgst_per[<?php echo $i;?>]" type="hidden" value="<?=$row['sgst_per']?>"/></div></td>
				
				<td><div style="display:inline-block;float:left"><input type="text" style="width:80px;text-align:right;"  name="sgst_amt[<?php echo $i;?>]" id="sgst_amt[<?php echo $i;?>]" class="form-control" onBlur="rowTotal(<?php echo $i;?>);"  value="<?php if($row['crdr_qty'] > 0.00){ echo $sgstnewval; } else {echo $row['sgst_amt'];}?>" readonly>
				<input name="sgst_amt1[<?php echo $i;?>]" id="sgst_amt1[<?php echo $i;?>]" type="hidden" value="<?php if($row['crdr_qty'] > 0.00){ echo $sgstnewval; } else {echo $row['sgst_amt'];}?>" /></div></td>
				
				<td><div style="display:inline-block;float:left"><input type="text" style="width:55px;text-align:right;"  name="cgst_per[<?php echo $i;?>]" id="cgst_per[<?php echo $i;?>]" class="form-control" onBlur="rowTotal(<?php echo $i;?>);"  value="<?php echo $row['cgst_per'];?>" readonly><input name="cgst_per[<?php echo $i;?>]" id="cgst_per[<?php echo $i;?>]" type="hidden" value="<?=$row['cgst_per']?>"/></div></td>
				
				<td><div style="display:inline-block;float:left"><input type="text" style="width:80px;text-align:right;"  name="cgst_amt[<?php echo $i;?>]" id="cgst_amt[<?php echo $i;?>]" class="form-control" onBlur="rowTotal(<?php echo $i;?>);"  value="<?php  if($row['crdr_qty'] > 0.00){ echo $cgstnewval; } else {echo $row['cgst_amt'];}?>" readonly>
				<input name="cgst_amt1[<?php echo $i;?>]" id="cgst_amt1[<?php echo $i;?>]" type="hidden" value="<?php  if($row['crdr_qty'] > 0.00){ echo $cgstnewval; } else {echo $row['cgst_amt'];}?>" /></div></td>
				
				<?php } else {?>
				
				<td><div style="display:inline-block;float:left"><input type="text" style="width:55px;text-align:right;"  name="igst_per[<?php echo $i;?>]" id="igst_per[<?php echo $i;?>]" class="form-control" onBlur="rowTotal(<?php echo $i;?>);"  value="<?php echo $row['igst_per'];?>" readonly>
				<input name="igst_per[<?php echo $i;?>]" id="igst_per[<?php echo $i;?>]" type="hidden" value="<?=$row['igst_per']?>"/></div></td>
				
				<td><div style="display:inline-block;float:left"><input type="text" style="width:70px;text-align:right;"  name="igst_amt[<?php echo $i;?>]" id="igst_amt[<?php echo $i;?>]" class="form-control" onBlur="rowTotal(<?php echo $i;?>);"  value="<?php if($row['crdr_qty'] > 0.00){ echo $igstnewval; } else {echo $row['igst_amt'];}?>" readonly>
				<input name="igst_amt1[<?php echo $i;?>]" id="igst_amt1[<?php echo $i;?>]" type="hidden" value="<?php if($row['crdr_qty'] > 0.00){ echo $igstnewval; } else {echo $row['igst_amt'];}?>" /></div></td>
				
				<?php }?>
                <td class="col-md-2"><input type="text" class="form-control" name="total_val[<?php echo $i;?>]" id="total_val[<?php echo $i;?>]" value="<?php if($row['crdr_qty'] > 0.00){ echo $totprice+$cgstnewval+$igstnewval+$sgstnewval ; } else { echo $row['totalvalue'];} ?>" autocomplete="off" readonly>
				  <input name="mrp[0]" id="mrp[0]" type="hidden"/>
                                     <input name="holdRate[<?php echo $i;?>]" id="holdRate[<?php echo $i;?>]" type="hidden"/>
									 <input type="hidden" name="ch_id[<?php echo $i;?>]" id="ch_id[<?php echo $i;?>]" value="<?php echo $row['id'];?>">
				</td>
              </tr>
			   <?php 
					$total_qty+=$row['qty'];
					$subtotal+= $row['price']*$toqty;
					$discount+=$row['discount'];
					$total_val+= $row['totalvalue'];
					$i++;  
					}		
			    }			 
			 ?>
            </tbody>
            <tfoot id='productfooter' style="z-index:-9999;">
              <tr class="0">
			  <input type="hidden" name="rowno" id="rowno" value="<?=$i-1;?>"/>
              <!--  <td colspan="9" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a></td>  -->
              </tr>
            </tfoot>
          </table>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-3 control-label">Sub Total</label>
              <div class="col-md-3">
                <input type="text" name="sub_total" id="sub_total" class="form-control" style="text-align:right" value="<?php if($newsubtot) { echo $newsubtot ;} else { echo $subtotal;}?>" readonly/>
              </div>
              <label class="col-md-3 control-label">Tax Amount</label>
              <div class="col-md-3">
                <input type="text" name="tax_amount" id="tax_amount" class="form-control" value="<?=$totcgstval+$totsgstval+$totigstval?>" readonly style="text-align:right"/>
                <input type="hidden" name="total_sgstamt" id="total_sgstamt" class="form-control" value="<?=$totsgstval?>" readonly style="width:200px;text-align:right"/>
                <input type="hidden" name="total_cgstamt" id="total_cgstamt" class="form-control" value="<?=$totcgstval?>" readonly style="width:200px;text-align:right"/>
                <input type="hidden" name="total_igstamt" id="total_igstamt" class="form-control" value="<?=$totigstval?>" readonly style="width:200px;text-align:right"/>
              </div> 
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-3 control-label">Total Discount</label>
              <div class="col-md-3">
                <input type="text" name="total_discount" id="total_discount" class="form-control" style="text-align:right" value="<?=$discount?>" onKeyUp="check_total_discount();" readonly/>
              </div>
              <label class="col-md-3 control-label">Grand Total</label>
              <div class="col-md-3">
              					<?php 
								if($newvaltot) { $gt = $newvaltot;} else { $gt = $total_val;}
										$grand_total=$gt; 
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
              <input type="text" name="grand_total" id="grand_total" class="form-control" style="text-align:right" value="<?php echo $gt?>" readonly/>
              </div>
            </div>
          </div>
          <div class="form-group">
                <div class="col-md-10">
                    <label class="col-md-3 control-label">TCS %</label>
                    <div class="col-md-3">
                        <select name="tcs_per" id="tcs_per" class="form-control" onChange="calculatetotal();" style="">
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
              <label class="col-md-3 control-label">Total Qty</label>
              <div class="col-md-3">
              <input type="text" name="total_qty" id="total_qty" class="form-control" style="text-align:right" value="<?php if($newqty) { echo $newqty;} else { echo $total_qty; }?>" readonly/>
              </div>
              <label class="col-md-3 control-label">Remark</label>
              <div class="col-md-3">
                <textarea name="remark" id="remark" class="form-control addressfield" style="resize:none"></textarea>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Save" title="Save This CN">
                <input type="hidden" name="custid" id="custid" value="<?=$_REQUEST['customer']?>"/>
                <input type="hidden" name="location_code" id="location_code" value="<?=$_REQUEST['location']?>"/>
				<input type="hidden" name="invoice_no" id="invoice_no" value="<?=$_REQUEST['inv_no']?>"/>
               <input type="hidden" name="disc_type" id="disc_type" value="<?=$_REQUEST['discount_type']?>"/>
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='process_credit_notes.php?<?=$pagenav?>'">
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
<script>
$("#location").on('change', function(){
	let val = this.value;
	if(val){
		$("#location_code").val(val);
	}
	else{
		$("#location_code").val("");
	}
});
</script>
</body>
</html>
<?php if($_REQUEST['location']=='' || $_REQUEST['customer']=='' || $_REQUEST['discount_type'] == '' || $_REQUEST['inv_no'] == ''){ ?>
<script>
$("#frm2").find("input[type='submit']:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
</script>
<?php } ?>

 