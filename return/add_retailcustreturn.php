	<?php
	require_once("../config/config.php");
	@extract($_POST);
	//echo $sql_parent="select distinct(to_location) from billing_master where from_location='".$_REQUEST['po_from']."' and type = 'Retail'  and Finvoice_no = '' "; 
	
	 if($_POST){
	 if ($_POST['upd']=='Process'){
	  if($total_qty!='' && $total_qty!=0){
	  $query_run='';
		///// get parent location details
		$parentloc=getLocationDetails($parentcode,"name,city,state,addrs,disp_addrs",$link1);
		$parentlocdet=explode("~",$parentloc);
		///// get child location details
		$childloc=getLocationDetails($partycode,"name,city,state,addrs,disp_addrs",$link1);
		$childlocdet=explode("~",$childloc);  
		//// Make System generated Invoice no.//////
		$res_cnt=mysqli_query($link1,"select prn_str,prn_counter from document_counter where location_code='".$parentcode."'");
		if(mysqli_num_rows($res_cnt)){
		$row_cnt=mysqli_fetch_array($res_cnt);
		$invcnt=$row_cnt['prn_counter']+1;
		$pad=str_pad($invcnt,4,0,STR_PAD_LEFT);
		$invno=$row_cnt['prn_str'].$pad; 
		mysqli_autocommit($link1, false);
		$flag = true;
		$err_msg="";
		///// Insert Master Data
		$splitcompltetax=explode("~",$complete_tax);
		if($delivery_address){$deli_addrs=$delivery_address;}else{$deli_addrs=$childlocdet[4];}
	   
		///// Insert in billing data by picking each data row one by one
		foreach($prod_code as $k=>$val)
		{   
		
		 /////////// only post check box 
	   if($checkb[$k]=='Y'){
		
		  /// check imei is already bill or not
			   $res_imei=mysqli_query($link1,"select owner_code,imei1,imei2 from billing_imei_data where imei1='".$serial_no[$k]."' or imei2='".$serial_no[$k]."' order by id desc");
			   $checkimei=mysqli_fetch_assoc($res_imei);
			   if(mysqli_num_rows($res_imei) >0 || $checkimei['owner_code']==$parentcode){						
				  //////////////insert in billing imei data///////////////////////			  
			$result_imei=mysqli_query($link1,"insert into billing_imei_data  set from_location='".$parentcode."',to_location='".$partycode."',owner_code='".$partycode."',prod_code='".$prod_code[$k]."' ,doc_no='".$invno."',imei1='".$checkimei['imei1']."',imei2='".$checkimei['imei2']."'");
				//// check if query is not executed
			   if (!$result_imei) {
				   $flag = false;
				   $err_msg = "Error Code3.1:". mysqli_error($link1) . ".";
			   }
		
			 }
			 else {
				$flag = false;
				$err_msg = "Error Code3.7:". mysqli_error($link1) . ".";
			 
			  }
		// checking row value of product and qty should not be blank
			$getstk=getCurrentStock($parentcode,$val,"okqty",$link1);
			//// check stock should be available ////
			if($getstk < $req_qty[$k]){ 
			   $flag = false;
			   $err_msg = "Error Code3: Stock is not available";
			}
			else{}
			if($val!='' && $req_qty[$k]!='' && $req_qty[$k]!=0){
	
				/////////// insert data
			   $query2="insert into billing_model_data set from_location='".$parentcode."',prod_code='".$val."',prod_cat='".$prod_cat[$k]."',qty='".$req_qty[$k]."', mrp='".$mrp[$k]."', price='".$price[$k]."', value='".$linetotal[$k]."',tax_name='".$taxname[$k]."',tax_per='".$tax[$k]."',tax_amt='".$total_tax[$k]."',discount='".$rowdiscount[$k]."', totalvalue='".$tot_value[$k]."',challan_no='".$invno."',sale_date='".$today."',entry_date='".$today."' ,sgst_per='".$sgst_per[$k]."' ,sgst_amt='".$sgst_amt1[$k]."',igst_per='".$igst_per[$k]."' ,igst_amt='".$igst_amt1[$k]."',cgst_per='".$cgst_per[$k]."' ,cgst_amt='".$cgst_amt1[$k]."' , retail_return = 'Y' ";	
		
			  $result1 = mysqli_query($link1, $query2);
			   //// check if query is not executed
			   if (!$result1) {
				   $flag = false;
					$err_msg = "Error Code4:";
			   }
			   //// update stock of from loaction
		 if(mysqli_num_rows(mysqli_query($link1,"select * from stock_status where asc_code='".$parentcode."' and partcode='".$val."'" ))>0){ 
			 $result_stock=mysqli_query($link1, "update stock_status set okqty=okqty+'".$req_qty[$k]."',updatedate='".$datetime."' where asc_code='".$parentcode."' and partcode='".$val."'");
			 }
			 else {
			  $result_stock=mysqli_query($link1, "insert into  stock_status set okqty=okqty+'".$req_qty[$k]."',updatedate='".$datetime."' , asc_code='".$parentcode."' , partcode='".$val."'");
			   }
			 
			   //// check if query is not executed
			   if (!$result_stock) {
				   $flag = false;
				   $err_msg = "Error Code5:";
			   }
			  
			  $query_run=1;
			  
			  ///// update stock ledger table
			$flag=stockLedger($invno,$today,$val,$parentcode,$partycode,$parentcode,"IN","OK","Retail Invoice",$req_qty[$k],$price[$k],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
			
			$data_table  = mysqli_query($link1, "update billing_model_data set retail_return='Y' where challan_no='".$_POST['inv']."' and id='".$ch_id[$k]."' and prod_code='".$val."'");
				   
						 //// check if query is not executed
			   if (!$data_table) {
				   $flag = false;
					   $err_msg = "Error Code5.1:";
						 }
				 
				$imeidata_table  = mysqli_query($link1, "update billing_imei_data set retail_return='Y' where doc_no='".$_POST['inv']."' and (imei1='".$serial_no[$k]."' or imei2='".$serial_no[$k]."' or imei2='".$serial_no[$k]."') and prod_code='".$val."'"); 
					
								//// check if query is not executed
			   if (!$imeidata_table) {
				   $flag = false;
					   $err_msg = "Error Code5.1:";
						 }
			  
				  }
			
			  }
			  
			  $subtotal+= $linetotal[$k];
			  $cgstamt+= $cgst_amt1[$k];
			  $sgstamt+= $sgst_amt1[$k];
			  $igstamt+= $igst_amt1[$k];
			  $grandtot+=$tot_value[$k];
			  
		}/// close for loop
		

	
	   if($query_run==1){
	  
	   $status= mysqli_fetch_array(mysqli_query($link1,"select count(id) as vid from billing_imei_data where retail_return = 'Y' and doc_no='".$_POST['inv']."' and from_location='".$parentcode."' "));
			  $po_no = mysqli_fetch_array(mysqli_query($link1,"select count(id) as vid from billing_imei_data where doc_no='".$_POST['inv']."' and from_location='".$parentcode."' "));
			  if($status['vid']  == $po_no['vid'])
			  {
		$master_data	=   mysqli_query($link1,"update billing_master set Finvoice_no='Retail_Return' where challan_no='".$_POST['inv']."' and from_location='".$parentcode."' and  Finvoice_no=''");
	
						//// check if query is not executed
				   if (!$master_data) {
					  $flag = false;
						   $err_msg = "Error Code5.1.2:";
						   }
						  
			  }	
			  
			  ///////////   master table ////////////////////////////////////////////////////////////////////////////////////////
			  $query1= "INSERT INTO billing_master set from_location='".$parentcode."',to_location='".$partycode."',challan_no='".$invno."',ref_no='".$inv."',sale_date='".$today."',entry_date='".$today."',entry_time='".$currtime."',type='Customer Retail Return',document_type='RETAIL PRN',discountfor='".$discountfor."',taxfor='".$taxfor."',status='Approved',entry_by='".$_SESSION['userid']."',basic_cost='".$subtotal."',discount_amt='".$total_discount."',tax_cost='".($cgstamt+$sgstamt+$igstamt)."',total_cost='".$grandtot."',tax_type='".$splitcompltetax[1]."',tax_header='".$splitcompltetax[2]."',tax='".$splitcompltetax[0]."',bill_from='".$partycode."',bill_topty='".$parentcode."',from_addrs='".$parentlocdet[3]."',disp_addrs='".$parentlocdet[4]."',to_addrs='".$childlocdet[3]."',deliv_addrs='".$deli_addrs."',billing_rmk='".$remark."' , total_cgst_amt = '".$cgstamt."', total_sgst_amt = '".$sgstamt."', total_igst_amt = '".$igstamt."' ";
		$result = mysqli_query($link1,$query1)or die ("ER1".mysqli_error($link1));
		//// check if query is not executed
		if (!$result) {
			 $flag = false;
			 $err_msg = "Error Code1:";
		}
		/// update invoice counter /////
		$result_doc=mysqli_query($link1,"update document_counter set prn_counter=prn_counter+1,update_by='".$_SESSION['userid']."',updatedate='".$datetime."' where location_code='".$parentcode."'");
		//// check if query is not executed
		if (!$result_doc) {
			 $flag = false;
			 $err_msg = "Error Code2:";
		}
			  
			  
			  ////// maintain party ledger////
		$flag=partyLedger($parentcode,$partycode,$invno,$today,$today,$currtime,$_SESSION['userid'],"RETAIL INVOICE",$grand_total,"CR",$link1,$flag);
		
		////// insert in activity table////
		$flag=dailyActivity($_SESSION['userid'],$invno,"Retail PRN","ADD",$ip,$link1,$flag);
			  
	   }
	   else {
		 ///// move to parent page
		 $msg = "Request could not be processed. Please select atleast one checkbox";
		 header("location:retailcust_return.php?msg=".$msg."".$pagenav);
		 exit;
		}
		
		
		if ($flag) {
			mysqli_commit($link1);
			$msg = "Retail Customer Return is successfully placed with ref. no. ".$invno;
		} else {
			mysqli_rollback($link1);
			$msg = "Request could not be processed. Please try again.";
		} 
		mysqli_close($link1);
		}else{
			$msg = "Request could not be processed invoice series not found. Please try again.";
		}
	  }else{
		 $msg = "Request could not be processed . Please dispatch some qty.";
	  }
		 ///// move to parent page
		 header("location:retailcust_return.php?msg=".$msg."".$pagenav);
		 exit;
	 }
	}
	
	
	$address=mysqli_query($link1,"Select * from billing_master where challan_no='".$_REQUEST['inv_no']."' and from_location='".$_REQUEST['po_from']."' and to_location='".$_REQUEST['po_to']."'"); 
	$row1=mysqli_fetch_assoc($address);
	
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
	
	 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
	 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
	 <script type="text/javascript">
	$(document).ready(function(){
		$('#myTable').dataTable();
	});
	$(document).ready(function(){
		$("#frm2").validate();
	});
	</script>
	<script src="../js/frmvalidate.js"></script>
	<script type="text/javascript" src="../js/jquery.validate.js"></script>
	<script type="text/javascript" src="../js/common_js.js"></script>
	<script type="text/javascript" src="../js/ajax.js"></script>
	<script type="text/javascript" >
	/////////// function to get available stock of ho
	  function getAvlStk(indx){
	   var productCode=document.getElementById("prod_code["+indx+"]").value;
	   var locationCode=$('#po_from').val();
	   var stocktype="okqty";
	   $.ajax({
		 type:'post',
	  url:'../includes/getAzaxFields.php',
	  data:{locstk:productCode,loccode:locationCode,stktype:stocktype,indxx:indx},
	  success:function(data){ 
	   var getdata=data.split("~");
	  
			 document.getElementById("avl_stock["+getdata[1]+"]").value=getdata[0];
		 }
	   });
	   //rowTotal(indx);
	  }
	//////////////////chk enter qty should not exceed by avl qty ///////
	function  check_qty(ind){
		
		var avl="req["+ind+"]";
		
	  var req1="req_qty["+ind+"]";
	  
		var availble=document.getElementById(avl).value;
		
	  var requested=document.getElementById(req1).value;
	  if(parseFloat(requested) > parseFloat(availble)){
		  
		 alert("Entered Qty is more then Bill  Qty.");
		 document.getElementById(req1).value="0.00";
		  return false;
	  }
	  rowTotal(ind);
	}
	 /////// calculate line total /////////////
	function rowTotal(ind){
		var req="req["+ind+"]";
		var ent_qty="req_qty["+ind+"]";
		var hident_qty="hidreq_qty["+ind+"]";
		var line="linetotal["+ind+"]";
		var ent_rate="price["+ind+"]";
		var availableQty="avl_stock["+ind+"]";
		var prodCodeField="prod_code["+ind+"]";
		var taxvalue="tax["+ind+"]";
		var discountField="rowdiscount["+ind+"]";
		var totalvalField="total_val["+ind+"]";
		var total1="total_tax["+ind+"]";
		var total2="tot_value["+ind+"]";
		var discount="discount["+ind+"]";	
		var rowsgstper = "sgst_per[" + ind + "]";
		var rowcgstper = "cgst_per[" + ind + "]";
		var rowigstper = "igst_per[" + ind + "]";
		var rowsgstamount = "sgst_amt[" + ind + "]";
		var rowsgstamount1 = "sgst_amt1[" + ind + "]";
		var rowcgstamount = "cgst_amt[" + ind + "]";
		var rowcgstamount1 = "cgst_amt1[" + ind + "]";
		var rowigstamount = "igst_amt[" + ind + "]";
		var rowigstamount1 = "igst_amt1[" + ind + "]";
		
	
	/////  check if entered qty is somthing
	 if(document.getElementById(ent_qty).value){ var qty=document.getElementById(ent_qty).value;
	 }else{ var qty=0;}
	
	  /////  check if entered price is somthing
	  if(document.getElementById(ent_rate).value){ var price=document.getElementById(ent_rate).value;}else{ var price=0.00;}
	  
	  ///// check if discount value is something
	  if(document.getElementById(discountField).value){ var dicountval=document.getElementById(discountField).value;}else{ var dicountval=0.00; }
	
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
	  if(parseFloat(qty) <= parseFloat(document.getElementById(availableQty).value)){	  
		if(parseFloat(price)>=parseFloat(dicountval)){
		 var total= parseFloat(qty)*parseFloat(price);
		  var var3="linetotal["+ind+"]";
		  var actualval = document.getElementById(var3).value - parseFloat(dicountval);
		 //var totalcost= parseFloat(total)-parseFloat(dicountval);
		 var sgst_amt = ((actualval * sgstper) / 100);
		 var cgst_amt = ((actualval * cgstper) / 100);
		 var igst_amt = ((actualval * igstper) / 100);
		
		 if(sgst_amt !='' && cgst_amt !=''){
		  document.getElementById(rowsgstamount).value = formatCurrency(sgst_amt);
		   document.getElementById(rowsgstamount1).value = formatCurrency(sgst_amt);
		  document.getElementById(rowcgstamount).value = formatCurrency(cgst_amt);
			document.getElementById(rowcgstamount1).value = formatCurrency(cgst_amt);
		  var tot = parseFloat(actualval) + parseFloat(sgst_amt) + parseFloat(cgst_amt);
		   }else{
		  document.getElementById(rowigstamount).value = formatCurrency(igst_amt);
		  document.getElementById(rowigstamount1).value = formatCurrency(igst_amt);
		  var tot = parseFloat(actualval) + parseFloat(igst_amt);
		   }                          
		document.getElementById(total2).value=formatCurrency(tot);; 
		 calculatetotal();
		}else{
			alert("Discount is exceeding from price");
		  var total= parseFloat(qty)*parseFloat(price);
		  var var3="linetotal["+ind+"]";
		  document.getElementById(var3).value=formatCurrency(total);
		  document.getElementById(discountField).value="0.00";
		  document.getElementById(totalvalField).value=formatCurrency(total);
		  calculatetotal();
		}
	  }
	  else if(parseFloat(document.getElementById(availableQty).value)=='0.00'){
		  alert("Stock is not Available");  
		  document.getElementById(ent_qty).value="";
		  //document.getElementById(availableQty).value="";
		  document.getElementById(ent_rate).value="";
		  document.getElementById(hold_rate).value="";
		  document.getElementById(prodCodeField).value="";
		  document.getElementById(prodmrpField).value="";
		  document.getElementById(prodCodeField).focus();
		  }
	  else{
		 alert("Stock is  not Available");
		  document.getElementById(ent_qty).value="";
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
		var rowno=document.getElementById("rowno").value;
		var sum_qty=0.00;
		var total_dis=0.00;
		var total_tax=0.00;
		var grand=0.00; 
		var linewisetot = 0.00;
		
		for(var i=0;i<rowno;i++){
			var temp_qty="req_qty["+i+"]";
			var discount="rowdiscount["+i+"]";
			var total="tot_value["+i+"]";
			var linetot ="linetotal["+i+"]";
	
			
			if(document.getElementById(temp_qty).value){ qty= document.getElementById(temp_qty).value;}else{ qty=0.00;}		
			///// check if discount value is something
			if(document.getElementById(discount).value){ discount1= document.getElementById(discount).value;}else{ discount1=0.00;}		
			///// check total qty
			sum_qty+=parseFloat(qty);
			total_dis+=parseFloat(discount1);
			linewisetot+=parseFloat(document.getElementById(linetot).value);
			grand+=parseFloat(document.getElementById(total).value);
			
			
	}/// close for loop
	
		document.getElementById("total_qty").value=formatCurrency(sum_qty);
		document.getElementById("total_discount").value=formatCurrency(total_dis);
		document.getElementById("grand_total").value=formatCurrency(grand);
	   document.getElementById("sub_total").value=formatCurrency(linewisetot);
	   
	   
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
		  <h2 align="center"><i class="fa fa-reply-all fa-lg"></i> Add New Retail Return </h2><br/>
		  <div class="form-group"  id="page-wrap" style="margin-left:10px;">
			  <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
			  <div class="form-group">
				<div class="col-md-10"><label class="col-md-3 control-label">Retail Return To<span style="color:#F00">*</span></label>
				  <div class="col-md-9">
					 <select name="po_from" id="po_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
						<option value="" selected="selected">Please Select </option>
						<?php 
						$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y'";
						$res_chl=mysqli_query($link1,$sql_chl);
						while($result_chl=mysqli_fetch_array($res_chl)){
							  $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
						 
							  ?>
						<option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['po_from'])echo "selected";?> >
						   <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
						</option>
						<?php
							 
						}
						?>
					 </select>
				  </div>
				</div>
			  </div>
			  <div class="form-group">
				<div class="col-md-10"><label class="col-md-3 control-label">Retail Return From<span style="color:#F00">*</span></label>
				  <div class="col-md-9">
					 <select name="po_to" id="po_to" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
					 <option value="" selected="selected">Please Select </option>
						<?php 
						$sql_parent="select distinct(to_location) from billing_master where from_location='".$_REQUEST['po_from']."' and type = 'Retail'  and Finvoice_no = '' ";
						$res_parent=mysqli_query($link1,$sql_parent);
						while($result_parent=mysqli_fetch_array($res_parent)){
						
							  $custname = mysqli_fetch_array(mysqli_query($link1,"select customername , customerid from customer_master where customerid='".$result_parent['to_location']."'"));	
							  if($custname['customername'] != '') {
							  ?>
						<option data-tokens="<?=$custname['customername']." | ".$custname['customerid']?>" value="<?=$result_parent['to_location']?>" <?php if($result_parent['to_location']==$_REQUEST['po_to'])echo "selected";?> >
						   <?=$custname['customername']." | ".$result_parent['to_location']?>
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
			  <?php if($_REQUEST['po_to']!='' && $_REQUEST['po_from']!=''){?>
			  <div class="col-md-10">
				  <label class="col-md-3 control-label">Invoice Number</label>
				  <div class="col-md-4">
				 <select name="inv_no" id="inv_no" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
					 <option value="" selected="selected">Please Select </option>
						<?php 
						$sql_parent="select challan_no  from billing_master where from_location='".$_REQUEST['po_from']."' and to_location='".$_REQUEST['po_to']."' and document_type='INVOICE' and status='Dispatched' and type = 'Retail' and Finvoice_no = ''  ";
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
			  </div>
			  <?php }?>
			</div>
			 </form>
			 <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
			  <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
			   <?php if($_REQUEST['inv_no']!=''){ ?> 
			  <table width="100%" id="myTable" class="table table-bordered table-hover">
				<thead>
				  <tr class="<?=$tableheadcolor?>" >
				  <th class="col-md-1" style="font-size:12px"></th>
					<th class="col-md-1" style="font-size:12px">Serial No.</th>
					<th data-class="expand" class="col-md-3" style="font-size:12px;">Product</th>
					<th data-hide="phone"  class="col-md-1" style="font-size:12px">Price</th>
					<th data-hide="phone"  class="col-md-1" style="font-size:12px">Value</th>
					<th data-hide="phone,tablet" class="col-md-1" style="font-size:11px">Discount/Unit</th>
					 <th data-hide="phone,tablet" class="col-md-1" style="font-size:12px">SGST %</th>
					 <th data-hide="phone,tablet" class="col-md-1" style="font-size:12px">SGST Amount </th>
					 <th data-hide="phone,tablet" class="col-md-1" style="font-size:12px">CGST %</th>
					 <th data-hide="phone,tablet" class="col-md-1" style="font-size:12px">CGST Amount </th>
					 <th data-hide="phone,tablet" class="col-md-1" style="font-size:12px">IGST %</th>
					 <th data-hide="phone,tablet" class="col-md-1" style="font-size:12px">IGST Amount </th>
					<th data-hide="phone,tablet" class="col-md-1" style="font-size:12px">Total Value</th>
				  </tr>
				</thead>
				<tbody>
				<?php
				$i=0;
				$tot_cgst = 0.00;
				$tot_sgst = 0.00;
				$tot_igst = 0.00;
				$total_qty = 0.00;
				
			  $model_queryt= mysqli_query($link1,"SELECT  *  FROM billing_imei_data where  doc_no = '".$_REQUEST['inv_no']."' and from_location = '".$_REQUEST['po_from']."' and retail_return = '' ");
				while($row=mysqli_fetch_assoc($model_queryt)){
				  
				  $sql=mysqli_query($link1,"Select * from billing_model_data  where from_location='".$row['from_location']."' and challan_no='".$row['doc_no']."'   and prod_code = '".$row['prod_code']."'  ");
							//$check1t=mysqli_query($link1 ,$sql);
						$brt = mysqli_fetch_array($sql);			
				?>
				  <tr>
				  <td>
				  <input name="checkb[<?php echo $i;?>]"  id="checkb[<?php echo $i;?>]" type="checkbox" value="Y" /></td>
				  <td><input name="serial_no[<?php echo $i;?>]" id="serial_no[<?php echo $i;?>]" type="text" class="form-control" value="<?php echo $row['imei1']; ?>"   maxlength="15"  autocomplete="off"  style="width:130px;" readonly  /></td>
					<td align="left"><input type="text" style="width:130px;"   class="form-control"   value="<?php echo getProduct($row['prod_code'],$link1);?>" readonly>
					<input type="hidden" name="req[<?php echo $i;?>]" id="req[<?php echo $i;?>]" value="<?php echo intval($brt[qty])/intval($brt[qty]);?>"/>
					<input type="hidden" name="avl_stock[<?php echo $i;?>]" id="avl_stock[<?php echo $i;?>]" value="<?php echo getCurrentStock($_REQUEST['po_from'], $row['prod_code'], 'okqty', $link1);?>" />
					
					<input type="hidden" name="prod_code[<?php echo $i;?>]" id="prod_code[<?php echo $i;?>]" class="form-control" value="<?php echo $row['prod_code'];?>">
					
					<input type="hidden" name="prod_cat[<?php echo $i;?>]" id="prod_cat[<?php echo $i;?>]" class="form-control" value="<?php echo $brt['prod_cat'];?>">
					<input type="hidden" name="mrp[<?php echo $i;?>]" id="mrp[<?php echo $i;?>]" class="form-control" value="<?php echo $brt['mrp'];?>">  
					</td>
					<input type="hidden" style="width:53px;text-align:right" name="req_qty[<?php echo $i;?>]" id="req_qty[<?php echo $i;?>]" onBlur="return check_qty(<?php echo $i;?>);getAvlStk(<?php echo $i;?>);rowTotal(<?php echo $i;?>);"   class="form-control" value="<?php echo $brt['qty']/$brt['qty'];?>" />
					
					<td align="left"><input type="text" style="width:85px;" name="price[<?php echo $i;?>]" id="price[<?php echo $i;?>]" class="form-control" value="<?php echo ($brt['price']/$brt['qty']);?>" onBlur="rowTotal(<?php echo $i;?>);" readonly="" ></td>
					<td align="left"><input type="text" style="width:85px;" name="linetotal[<?php echo $i;?>]" id="linetotal[<?php echo $i;?>]" class="form-control" value="<?php echo ($brt['value'])/$brt['qty'];?>" readonly></td>
					<td align="left"><input type="text" style="width:60px;" name="rowdiscount[<?php echo $i;?>]" id="rowdiscount[<?php echo $i;?>]" class="form-control" value="<?php echo $brt['discount']/$brt['qty'];?>"  onBlur="rowTotal(<?php echo $i;?>);" ><input type="hidden" style="width:100px;" name="discount[<?php echo $i;?>]" id="discount[<?php echo $i;?>]" onBlur="rowTotal(<?php echo $i;?>);" class="form-control" value="<?php echo $brt[discount]/$br[qty];?>" readonly><input type="hidden"  style="width:100px;" name="total_val[<?php echo $i;?>]" id="total_val[<?php echo $i;?>]" class="form-control" value="<?php echo $row[sub_total];?>" ></td>
					<td><div style="display:inline-block;float:left"><input type="text" style="width:55px;text-align:right;"  name="sgst_per[<?php echo $i;?>]" id="sgst_per[<?php echo $i;?>]" class="form-control" onBlur="rowTotal(<?php echo $i;?>);"  value="<?php echo $brt['sgst_per'];?>" readonly><input name="sgst_per[<?php echo $i;?>]" id="sgst_per[<?php echo $i;?>]" type="hidden" value="<?=$brt['sgst_per']?>"/></div></td>
					
					<?php 
						 $value = ($brt['value'])/$brt['qty']; 
						 $final_val = $value - ($brt[discount]/$brt['qty']);
						 $cgst_amt =  ($final_val * $brt[cgst_per])/100;
						 $sgst_amt =  ($final_val * $brt[sgst_per])/100;
						 $igst_amt =   ($final_val * $brt[igst_per])/100; 
						 $totalval = $cgst_amt +$sgst_amt+$igst_amt+$final_val;
					?>
					
					<td><div style="display:inline-block;float:left"><input type="text" style="width:80px;text-align:right;"  name="sgst_amt[<?php echo $i;?>]" id="sgst_amt[<?php echo $i;?>]" class="form-control" onBlur="rowTotal(<?php echo $i;?>);"  value="<?php echo $sgst_amt;?>" readonly>
					<input name="sgst_amt1[<?php echo $i;?>]" id="sgst_amt1[<?php echo $i;?>]" type="hidden" value="<?php echo $sgst_amt;?>" /></div></td>
					
					<td><div style="display:inline-block;float:left"><input type="text" style="width:55px;text-align:right;"  name="cgst_per[<?php echo $i;?>]" id="cgst_per[<?php echo $i;?>]" class="form-control" onBlur="rowTotal(<?php echo $i;?>);"  value="<?php echo $brt['cgst_per'];?>" readonly><input name="cgst_per[<?php echo $i;?>]" id="cgst_per[<?php echo $i;?>]" type="hidden" value="<?=$brt['cgst_per']?>"/></div></td>
					
					<td><div style="display:inline-block;float:left"><input type="text" style="width:80px;text-align:right;"  name="cgst_amt[<?php echo $i;?>]" id="cgst_amt[<?php echo $i;?>]" class="form-control" onBlur="rowTotal(<?php echo $i;?>);"  value="<?php echo $cgst_amt;?>" readonly>
					<input name="cgst_amt1[<?php echo $i;?>]" id="cgst_amt1[<?php echo $i;?>]" type="hidden" value="<?php echo $cgst_amt;?>" /></div></td>
					
					<td><div style="display:inline-block;float:left"><input type="text" style="width:55px;text-align:right;"  name="igst_per[<?php echo $i;?>]" id="igst_per[<?php echo $i;?>]" class="form-control" onBlur="rowTotal(<?php echo $i;?>);"  value="<?php echo $brt['igst_per'];?>" readonly>
					<input name="igst_per[<?php echo $i;?>]" id="igst_per[<?php echo $i;?>]" type="hidden" value="<?=$brt['igst_per']?>"/></div></td>
					
					<td><div style="display:inline-block;float:left"><input type="text" style="width:70px;text-align:right;"  name="igst_amt[<?php echo $i;?>]" id="igst_amt[<?php echo $i;?>]" class="form-control" onBlur="rowTotal(<?php echo $i;?>);"  value="<?php echo $igst_amt;?>" readonly>
					<input name="igst_amt1[<?php echo $i;?>]" id="igst_amt1[<?php echo $i;?>]" type="hidden" value="<?php echo $igst_amt;?>" /></div></td>
				
					<td align="left"><input type="text" style="width:90px;"name="tot_value[<?php echo $i;?>]" id="tot_value[<?php echo $i;?>]" class="form-control" value="<?php echo $totalval;?>" readonly>
					<input type="hidden" name="ch_id[<?php echo $i;?>]" id="ch_id[<?php echo $i;?>]" value=<?php echo $brt['id'];?>>
					
					</td>
				 </tr>
				  <?php 
				$tot_cgst+= $brt['cgst_amt'];
				$tot_sgst+= $brt['sgst_amt'];
				$tot_igst+= $brt['igst_amt'];
				$total_qty+=($brt[qty]/$brt[qty]);
				$total_disc+=($brt[discount]/$brt[qty]);
				$subtotal+=$value;
				$grandval+=$totalval;
				$i++;   }?>
				
					 <?php }?>
					 <input type="hidden" name="rowno" id="rowno" value="<?php echo $i;?>" /> 
				</tbody>
			   
			  </table>
			  </div>
			  <div class="form-group">
				<div class="col-md-10">
				  <label class="col-md-3 control-label">Total Qty</label>
				  <div class="col-md-3">
				  <input type="text" name="total_qty" id="total_qty" class="form-control"  value="<?=$total_qty;?>"  readonly/>
				  </div>
				   <label class="col-md-3 control-label">Sub total</label>
				  <div class="col-md-3">
					<input type="text" name="sub_total" id="sub_total" class="form-control"   value="<?=$subtotal;?>"  readonly  />
				  </div>
				</div>
			  </div>
			  <div class="form-group">
				<div class="col-md-10">
				  <label class="col-md-3 control-label">Discount Applied As</label>
				  <div class="col-md-3">
				  <input type="text" name="discountfordispl" id="discountfordispl" value="<?php echo getDiscountType($row1['discountfor']);?>" class="form-control" readonly/><input type="hidden" name="discountfor" id="discountfor" value="<?=$row1['discountfor'];?>" class="form-control" readonly/>
				  </div>
				   <label class="col-md-3 control-label">Total Discount</label>
				  <div class="col-md-3">
					<input type="text" name="total_discount" id="total_discount" class="form-control"   value="<?=$total_disc;?>"  readonly  />
				  </div>
				</div>
			  </div>
			  <div class="form-group">
				<div class="col-md-10">
				  <label class="col-md-3 control-label"></label>
				  <div class="col-md-3">
				  </div>
				  <label class="col-md-3 control-label">Grand Total</label>
				  <div class="col-md-3">
				  <input type="hidden" id="hid_tot_cgst" name="hid_tot_cgst" value="<?=$tot_cgst;?>" />
				  <input type="hidden" id="hid_tot_sgst" name="hid_tot_sgst" value="<?=$tot_sgst;?>" />
				  <input type="hidden" id="hid_tot_igst" name="hid_tot_igst" value="<?=$tot_igst;?>" />
				  <input type="text" name="grand_total" id="grand_total" value="<?=$grandval;?>" class="form-control"  readonly/>
				  </div>
				</div>
			  </div>
			  <div class="form-group">
				<div class="col-md-10">
				  <label class="col-md-3 control-label">Delivery Address<span style="color:#F00">*</span></label>
				  <div class="col-md-3">
					<textarea name="delivery_address" id="delivery_address" class="form-control required"  value=""style="resize:none" required><?php echo $row1['disp_addrs'];?></textarea>
				  </div>
				  <label class="col-md-3 control-label">Remark</label>
				  <div class="col-md-3">
					<textarea name="remark" id="remark" class="form-control" style="resize:none"></textarea>
				  </div>
				</div>
			  </div>
			  <div class="form-group">
				<div class="col-md-10">              
				</div>
			  </div>
			  <div class="form-group">
				<div class="col-md-12" align="center">
				  <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Process" title="Process this purchase return">
					<input type="hidden" name="parentcode" id="parentcode" value="<?=$_REQUEST['po_from']?>"/>
					<input type="hidden" name="partycode" id="partycode" value="<?=$_REQUEST['po_to']?>"/>
					<input type="hidden" name="inv" id="inv" value="<?=$_REQUEST['inv_no']?>"/>
				  <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='retailcust_return.php?<?=$pagenav?>'">
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
	<?php if($_REQUEST[po_to]=='' || $_REQUEST[po_from]==''){ ?>
	<script>
	$("#frm2").find("input[type='submit']:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
	</script>
	<?php } ?>