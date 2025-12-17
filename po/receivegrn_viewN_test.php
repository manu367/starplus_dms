<?php
require_once("../config/config.php");
require_once("../includes/ledger_function.php");
//////////////// decode challan number////////////////////////////////////////////////////////
$po_no = base64_decode($_REQUEST['id']);
////////////////////////////////////////// fetching datta from table/////////////////////////
$po_sql = "SELECT * FROM vendor_order_master WHERE po_no='".$po_no."'";
$po_res = mysqli_query($link1,$po_sql);
$po_row = mysqli_fetch_assoc($po_res);
///////// get location details
//$loc_det =  explode("~",getLocationDetails($po_row['po_from'],"id_type,state",$link1));
$loc_det =  explode("~",getLocationDetails($po_row['po_from'],"name,id_type,email,phone,addrs,disp_addrs,city,state,statecode,pincode,gstin_no",$link1));
//////// get vendor details
$vend_det = explode("~",getVendorDetails($po_row['po_to'],"name,city,state,address,phone,email,pincode,bill_address,gstin_no",$link1));
$msg="";
///// after hitting receive button ///
@extract($_POST);
	if($_POST){
		if ($_POST['upd']=='Receive'){
			mysqli_autocommit($link1, false);
			$flag = true;
			$error_msg = "";
			$status = "Received";
			$allowed = array('gif', 'png', 'jpg', 'jpeg');
			$allowed1 = array('gif', 'png', 'jpg', 'jpeg', 'pdf', 'xlsx', 'xls');
		   	#########  invocie attachment code ##############################
			if($_FILES['inv_attachment']['name']){
				$folder="grn_doc";
				$file_name = $_FILES['inv_attachment']['name'];
				$ext1 = pathinfo($file_name, PATHINFO_EXTENSION);
				if (!in_array($ext1, $allowed1)) {
					$cflag="danger";
					$cmsg="Failed";
					$msg = "Request could not be processed. Please try again. ".$ext1." file extension is not allowed in invoice attachment";
					header("location:grnList.php?msg=".$msg."".$pagenav);
			  		exit;
				}else{
					$file_tmp = $_FILES['inv_attachment']['tmp_name'];
					$path1 = "../".$folder."/".time().$file_name;
					$up = move_uploaded_file($file_tmp,$path1);
				}
			}
			if($_FILES['pod1']['name']){
				$filepod1 = $_FILES['pod1']['name'];
				$extpod1 = pathinfo($filepod1, PATHINFO_EXTENSION);
				if (!in_array($extpod1, $allowed)) {
					$cflag="danger";
					$cmsg="Failed";
					$msg = "Request could not be processed. Please try again. ".$extpod1." file extension is not allowed in POD 1 attachment";
					header("location:grnList.php?msg=".$msg."".$pagenav);
					exit;
				}else{
					$dirct1 = "../grn_pod/".date("Y-m");
					if (!is_dir($dirct1)) {
						mkdir($dirct1, 0777, 'R');
					}
					$filepod1_tmp = $_FILES['pod1']['tmp_name'];
					$pod1_path = "../".$dirct1."/".time().$filepod1;
					$up_pod1 = move_uploaded_file($filepod1_tmp, $dirct1."/".time().$filepod1);
				}
			}
			if($_FILES['pod2']['name']){
				$filepod2 = $_FILES['pod2']['name'];
				$extpod2 = pathinfo($filepod2, PATHINFO_EXTENSION);
				if (!in_array($extpod2, $allowed)) {
					$cflag="danger";
					$cmsg="Failed";
					$msg = "Request could not be processed. Please try again. ".$extpod2." file extension is not allowed in POD 2 attachment";
					header("location:grnList.php?msg=".$msg."".$pagenav);
					exit;
				}else{
					$dirct2 = "../grn_pod/".date("Y-m");
					if (!is_dir($dirct2)) {
						mkdir($dirct2, 0777, 'R');
					}
					$filepod2_tmp = $_FILES['pod2']['tmp_name'];
					$pod2_path = "../".$dirct2."/".time().$filepod2;
					$up_pod2 = move_uploaded_file($filepod2_tmp, $dirct2."/".time().$filepod2);
				}
			}
			if ($stock_in=="") {
				$cflag="danger";
				$cmsg="Failed";
				$msg = "Request could not be processed. Please try again. Cost Centre is mandatory to select";
				header("location:grnList.php?msg=".$msg."".$pagenav);
				exit;
			}



		}
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
	<script type="text/javascript">
	$(document).ready(function(){
		$("#frm2").validate();
	});		
	 $(document).ready(function() {
		$('#invoicedate').datepicker({
			format: "yyyy-mm-dd",
			startDate: "<?=$po_row['entry_date']?>",
			endDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true
		});
	});
	</script>
    <link rel="stylesheet" href="../css/datepicker.css">
	<script src="../js/bootstrap-datepicker.js"></script>
	<script type="text/javascript">
	
	function checkPriceInvRcb(indx){
	var inv_qty_data = document.getElementById("inv_qty"+indx).value;
	var rcb_qty_data = document.getElementById("qty"+indx).value;
	if(parseInt(rcb_qty_data) < parseInt(inv_qty_data)){
		alert("Invoice qty cannot be greater then Pending Qty ");
		document.getElementById('inv_qty'+indx).value = rcb_qty_data;
	}else{
	
	}
}
	/////// calculate line total /////////////
	function rowTotal(ind){
		var ent_qty = "inv_qty"+ind+"";
		var ent_rate = "price"+ind+"";
		var cgst_per = "cgst_per"+ind+"";
		var sgst_per = "sgst_per"+ind+"";
		var igst_per = "igst_per"+ind+"";
		var cgstamt = "cgst_amt"+ind+"";
		var sgstamt = "sgst_amt"+ind+"";
		var igstamt = "igst_amt"+ind+"";
		var linetotal = "totalval"+ind+"";
		
		var vend_state = '<?=$vend_det[2]?>' ;
		var loc_state = '<?=$loc_det[7]?>';
		////// check if entered qty is something
		if(document.getElementById(ent_qty).value){ 
			var qty = document.getElementById(ent_qty).value;
		}else{ 
			var qty = 0;
		}
		
		/////  check if entered price is somthing
		if(document.getElementById(ent_rate).value){ 
			var price = document.getElementById(ent_rate).value;
		}else{ 
			var price = 0.00;
		}
		var total = parseFloat(qty) * parseFloat(price);
		var var3 = "po_value"+ind+"";
		document.getElementById(var3).value = total.toFixed(2);	

		if(vend_state == loc_state){
			var cgstamtval = ((total)*(document.getElementById(cgst_per).value))/100;
			document.getElementById(cgstamt).value=(cgstamtval).toFixed(2);
	 
			var sgstamtval = ((total)*(document.getElementById(sgst_per).value))/100;
			document.getElementById(sgstamt).value=(sgstamtval).toFixed(2);	 
			
			document.getElementById(linetotal).value = parseFloat(total+cgstamtval+sgstamtval).toFixed(2);
			recalculatetotal();
		}
		else {
			var igstamtval = ((total)*(document.getElementById(igst_per).value))/100;
			document.getElementById(igstamt).value=(igstamtval).toFixed(2);
			
			document.getElementById(linetotal).value = (total+igstamtval).toFixed(2);
			recalculatetotal();
		} 
		//recalculatetotal(); 
	}
	////// calculate final value of form /////
	function recalculatetotal(){
		var rowno = (document.getElementById("row_no").value);
		var sum_total = 0.00; 
		var sum_subtot = 0.00;
		var sum_cgsttot = 0.00;
		var sum_sgsttot = 0.00;
		var sum_igsttot = 0.00;
		var vend_state = '<?=$vend_det[2]?>' ;
		var loc_state = '<?=$loc_det[7]?>';
			for(var i=1; i<rowno; i++){
				var temp_qty = "inv_qty"+i+"";
				var temp_total = "totalval"+i+"";
				var temp_subtot = "po_value"+i+"";
				var temp_sgstamt = "sgst_amt"+i+"";
				var temp_cgstamt = "cgst_amt"+i+"";
				var temp_igstamt = "igst_amt"+i+"";
	
				var totalamtvar = 0.00;
				var totalsubtotal = 0.00;
				var cgsttotal = 0.00;
				var sgsttotal = 0.00;
				var igsttotal = 0.00;
				///// check if line total value is something
				if(document.getElementById(temp_total).value){ totalamtvar= document.getElementById(temp_total).value;}else{ totalamtvar=0.00;}
				if(document.getElementById(temp_subtot).value){ totalsubtotal= document.getElementById(temp_subtot).value;}else{ totalsubtotal=0.00;}
				if(vend_state == loc_state) {
					if(document.getElementById(temp_sgstamt).value){ sgsttotal= document.getElementById(temp_sgstamt).value;}else{ sgsttotal=0.00;}
				
					if(document.getElementById(temp_cgstamt).value){ cgsttotal= document.getElementById(temp_cgstamt).value;}else{ cgsttotal=0.00;}
					sum_cgsttot+=parseFloat(cgsttotal);
					sum_sgsttot+=parseFloat(sgsttotal);
				}
				else {
					if(document.getElementById(temp_igstamt).value){ igsttotal= document.getElementById(temp_igstamt).value;}else{ igsttotal=0.00;}
					sum_igsttot+=parseFloat(igsttotal);
				}
				sum_total+=parseFloat(totalamtvar);
				sum_subtot+=parseFloat(totalsubtotal);
			}/// close for loop
			document.getElementById("sub_total").value=(sum_subtot).toFixed(2);
			document.getElementById("tax_total").value=(sum_cgsttot+sum_sgsttot+sum_igsttot).toFixed(2);
			document.getElementById("grand_total").value=(sum_total).toFixed(2);
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
		
		
		function checkRecQty(a){
	  
			var holdqty = 0;
			var recvqty = 0;
			var poqty = 0;
			var missqty = 0;
			//// check hold qty
			if(document.getElementById("damage_qty"+a).value==""){ holdqty=0; }else{ holdqty=parseInt(document.getElementById("damage_qty"+a).value); }
			//// check missing qty
			if(document.getElementById("miss_qty"+a).value==""){ missqty=0; }else{ missqty=parseInt(document.getElementById("miss_qty"+a).value);}
			//// entered received qty
			if(document.getElementById("ok_qty"+a).value==""){ recvqty=0; }else{ recvqty=parseInt(document.getElementById("ok_qty"+a).value);}
			//// check enter poqty qty
			if(document.getElementById("inv_qty"+a).value==""){ poqty=0; }else{ poqty=parseInt(document.getElementById("inv_qty"+a).value); }
			
			if(poqty < (recvqty)){
				alert("Ok Qty  can not more than Invoice Qty!");
				document.getElementById("ok_qty"+a).value=poqty;
				document.getElementById("miss_qty"+a).value=0;
				document.getElementById("damage_qty"+a).value=0;
			}
			else{
				document.getElementById("miss_qty"+a).value=(poqty - recvqty);
				//document.getElementById("miss_qty"+a).focus();
				//document.getElementById("upd").disabled=true;
	        }
			
			//// entered shipped qty
			//if(document.getElementById("shippedqty"+a).value==""){ shipqty=0; }else{ shipqty=parseInt(document.getElementById("shippedqty"+a).value);}
			if(poqty < (recvqty + holdqty)){
				alert("Ok Qty & Damage Qty can not more than Invoice Qty!");
				document.getElementById("ok_qty"+a).value=poqty;
				document.getElementById("miss_qty"+a).value=0;
				document.getElementById("damage_qty"+a).value=0;
			}else{
				document.getElementById("miss_qty"+a).value=(poqty - (recvqty + holdqty));
				//document.getElementById("miss_qty"+a).focus();
				//document.getElementById("upd").disabled=true;
	        }
			
			/*if(poqty < (recvqty + missqty)){
				alert("Ok Qty & Missing Qty can not more than Invoice Qty!");
				document.getElementById("ok_qty"+a).value=poqty;
				document.getElementById("miss_qty"+a).value=0;
				document.getElementById("damage_qty"+a).value=0;
			}*/
			
			/*if(poqty < (recvqty + missqty+ holdqty)){
				alert("Ok Qty & Missing Qty & Damage Qty can not more than Invoice Qty!");
				document.getElementById("ok_qty"+a).value=poqty;
				document.getElementById("miss_qty"+a).value=0;
				document.getElementById("damage_qty"+a).value=0;
			}*/
			calculateTotal();
		}

		///// calculate total amount //
		function calculateTotal(){
			var maxrow = document.getElementById("row_no").value;
			var subtotal = 0.00;
			var taxtotal = 0.00;
			var grandtotal = 0.00;
			var qtytotal = 0;
			var newqty = 0;
			for(var i=1; i < maxrow; i++){
				if(document.getElementById("inv_qty"+i).value==""){ var shipqty=0; }else{ var shipqty=parseInt(document.getElementById("inv_qty"+i).value);}
				if(document.getElementById("ok_qty"+i).value==""){ var okqtynew=0; }else{ var okqtynew=parseInt(document.getElementById("ok_qty"+i).value);}
				
				if(document.getElementById("excess"+i).value==""){ var excessqty=0; }else{ var excessqty=parseInt(document.getElementById("excess"+i).value);}
				
				var totqty = shipqty + excessqty;
				
				qtytotal+= totqty;
			    
				var calsub = parseFloat(totqty) * parseFloat(document.getElementById("price"+i).value);
				subtotal+=  calsub;
				//var caltax = (calsub * parseFloat(document.getElementById("taxper"+i).value))/100;
				//taxtotal+=  caltax;
				grandtotal+= calsub;
				newqty+= okqtynew;
			}
			
			document.getElementById("tot_qty").value = qtytotal;
			//document.getElementById("sub_total").value = subtotal;
			//document.getElementById("tax_total").value = taxtotal;
			//document.getElementById("grand_total").value = grandtotal;
			document.getElementById("totok_qty").value = newqty;
		}
		function putdata(){			
			var invno = document.getElementById("invoice_no").value;
			var invdate = document.getElementById("invoicedate").value;
			var stockinn = document.getElementById("stock_inn").value;
			document.getElementById("postinv_no").value = invno;
			document.getElementById("postinv_date").value = invdate;
			document.getElementById("stock_in").value = stockinn;
		}
		</script>
		<script type="text/javascript" src="../js/jquery.validate.js"></script>
		<!-- Include multiselect -->
	<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
    <link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
	</head>
	<body onLoad="putdata();">
	<div class="container-fluid">
		<div class="row content">
		<?php 
		include("../includes/leftnav2.php");
		?>
	  <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
		  <h2 align="center"><i class="fa fa-ship"></i> Receive GRN </h2><br/>
	   <div class="panel-group">
	   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
		<div class="panel panel-info">
			<div class="panel-heading">VPO Details</div>
			 <div class="panel-body">
			  <table class="table table-bordered" width="100%">
				<tbody>
				  <tr>
                  	<td width="20%"><label class="control-label">PO From</label></td>
					<td width="30%"><?php echo getLocationDetails($po_row["po_from"],"name",$link1)."(".$po_row['po_from'].")";?><input name="to_loc" id="to_loc" type="hidden" value="<?=$po_row['po_from']?>"/></td>
					<td width="20%"><label class="control-label">PO To:</label></td>
					<td width="30%"><?php echo getVendorDetails($po_row["po_to"],"name",$link1)."(".$po_row['po_to'].")";?><input name="supplier" id="supplier" type="hidden" value="<?=$po_row['po_to']?>"/></td>
					
				  </tr>
				 
				  <tr>
					<td><label class="control-label">PO No.</label></td>
					<td><?php echo $po_row['po_no'];?><input name="po_no" id="po_no" type="hidden" value="<?=$po_row['po_no']?>"/></td>
					<td><label class="control-label">PO Date</label></td>
					<td><?php echo dt_format($po_row['entry_date']);?></td>
				  </tr>  
				  <tr>
					<td><label class="control-label">Status</label></td>
					<td><?php  echo $po_row["status"]?></td>
					<td><label class="control-label">Invoice Number</label></td>
					<td><input type="text" name="invoice_no" id="invoice_no" value="<?=$po_row['invoice_no']?>" class="form-control" onKeyUp="putdata();"/></td>
				  </tr>
				  <tr>
				    <td><label class="control-label">Invoice Date</label></td>
				    <td><input type="text" class="form-control span2" name="invoicedate"  id="invoicedate" autocomplete="off" onChange="putdata();"></td>
				    <td><label class="control-label">Cost Centre(Godown)<span style="color:#F00">*</span></label></td>
				    <td><select name="stock_inn" id="stock_inn" required class="form-control selectpicker required" data-live-search="true" onChange="putdata();">
                                            <option value="">Please Select </option>
                                             <?php                                 
											$smfm_sql = "SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code='".$po_row["po_from"]."'";
											$smfm_res = mysqli_query($link1,$smfm_sql);
											while($smfm_row = mysqli_fetch_array($smfm_res)){
											?>
											<option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['stock_inn'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
											<?php
											}
											?>
											<?php /*?><?php                                 
											$smf_sql = "SELECT sub_location, sub_location_name FROM sub_location_master WHERE main_location='".$po_row["po_from"]."' AND status='Active'";
											$smf_res = mysqli_query($link1,$smf_sql);
											while($smf_row = mysqli_fetch_array($smf_res)){
											?>
											<option value="<?=$smf_row['sub_location']?>" <?php if($smf_row['sub_location']==$_REQUEST['stock_in'])echo "selected";?>><?=$smf_row['sub_location_name']." | ".$smf_row['sub_location']?></option>
											<?php
											}
											?><?php */?>
                                        </select></td>
			      </tr>     
				</tbody>
			  </table>
			</div><!--close panel body-->
		</div><!--close panel-->	
		</form>
<form  method="post" id="frm1" name="frm1" enctype="multipart/form-data">
	<div class="panel panel-info table-responsive">
		<div class="panel-heading">Items Information</div>
		<div class="panel-body">
			<table class="table table-bordered" width="200%">
				<thead>
					<tr class="<?=$tableheadcolor?>">
				  		<td>S.No</td>
                        <td>Product</td>
                        <td>PO Qty</td>
                        <td>Pending Qty</td>
						<th>Invoice Qty</th>
                        <td>Purchase Price</td>
                        <td>SubTotal</td>
                        <?php  if($vend_det[2] == $loc_det[7]){?>
                        <td>CGST(%)</td>
                        <td>CGST Amount</td>
                        <td>SGST(%)</td>
                        <td>SGST Amount</td>
                        <?php } else {?>
                        <td>IGST(%)</td>
                        <td>IGST Amount</td>
                        <?php }?>
                        <td>Total Amount</td>
                        
                        <!--<td>Receive Qty</td>-->
                        <td>OK</td>
						<td>Damage</td>
						<td>Missing</td>
                        <td>Excess</td>
					</tr>
				</thead>
				<tbody>
				<?php
				$i = 1;
			    $data_sql = "SELECT * FROM vendor_order_data WHERE po_no='".$po_no."' AND pending_qty != 0.00 ";
				$data_res = mysqli_query($link1,$data_sql);
				while($data_row = mysqli_fetch_assoc($data_res)){
					$hsncode = mysqli_fetch_array(mysqli_query($link1,"select hsn_code,productname from product_master where productcode = '".$data_row['prod_code']."' "));
				    $taxdet = mysqli_fetch_array(mysqli_query($link1,"select igst,cgst,sgst from tax_hsn_master where hsn_code = '".$hsncode['hsn_code']."' "));
				 ?>
					<tr>
				  		<td><?=$i?></td>
				  		<td><?=$hsncode['productname'];?></td>
				  		<td><?=$data_row['req_qty'];?></td>	
                        <td><?=$data_row['pending_qty'];?><input name="qty<?=$data_row['id']?>" id="qty<?=$i?>" type="hidden" value="<?=$data_row['pending_qty']?>"/></td>
                        <td><input name="inv_qty<?=$data_row['id']?>" onBlur="checkPriceInvRcb(<?=$i?>);rowTotal('<?=$i?>');" id="inv_qty<?=$i?>" type="text" class="form-control number required" style="width:80px;text-align:right" required/></td>
                  						
				  		<td><input name="price<?=$data_row['id']?>" id="price<?=$i?>" type="text" value="<?=$data_row['po_price']?>" class="form-control" onBlur="rowTotal('<?=$i?>');" style="width:80px;text-align:right"/></td>
				  		<td><input name="po_value<?=$data_row['id']?>" id="po_value<?=$i?>" type="text" value="<?=$data_row['po_value']?>" class="form-control" style="width:100px;" readonly/></td> 
				  		<?php  if($vend_det[2] == $loc_det[7]){
							$cgstamtval =  ($taxdet['cgst']*$data_row['po_value'])/100;
							$sgstamtval =  ($taxdet['sgst']*$data_row['po_value'])/100;
							$totalval = $cgstamtval + $cgstamtval + $data_row['po_value'];
							?>
				  		<td><?=$taxdet['cgst'];?><input name="cgst_per<?=$data_row['id']?>" id="cgst_per<?=$i?>" type="hidden" value="<?=$taxdet['cgst']?>" class="form-control" readonly/></td>
				  		<td><input name="cgst_amt<?=$data_row['id']?>" id="cgst_amt<?=$i?>" type="text" value="<?=$cgstamtval?>" class="form-control" style="width:80px;" readonly/></td>
				  		<td><?=$taxdet['sgst'];?><input name="sgst_per<?=$data_row['id']?>" id="sgst_per<?=$i?>" type="hidden" value="<?=$taxdet['sgst']?>" class="form-control" readonly/></td>
				  		<td><input name="sgst_amt<?=$data_row['id']?>" id="sgst_amt<?=$i?>" type="text" value="<?=$sgstamtval?>" class="form-control" style="width:80px;" readonly/></td>
				   		<?php } else {
							  $igstamtval =  ($taxdet['igst']*$data_row['po_value'])/100;
							$totalval = $igstamtval + $data_row['po_value'];
							?>
				  		<td><?=$taxdet['igst'];?><input name="igst_per<?=$data_row['id']?>" id="igst_per<?=$i?>" type="hidden" value="<?=$taxdet['igst']?>" class="form-control" readonly/></td>
				  		<td><input name="igst_amt<?=$data_row['id']?>" id="igst_amt<?=$i?>" type="text" value="<?=$igstamtval?>" class="form-control" style="width:80px;" readonly/></td>
				  		<?php }?>
				  		<td><input name="totalval<?=$data_row['id']?>" id="totalval<?=$i?>" type="text" value="<?=$totalval?>" class="form-control" style="width:100px;" readonly/></td>
				  		
				  		<td><input type="text" class="digits form-control" style="width:80px;" name="ok_qty<?=$data_row['id']?>" id="ok_qty<?=$i?>"  autocomplete="off" onBlur="checkPriceInvRcb(<?=$i?>);" onKeyUp="checkRecQty('<?=$i?>');" value="<?=$data_row['qty'];?>"></td>
				  		<td><input type="text" class="digits form-control" style="width:80px;" name="damage_qty<?=$data_row[id]?>" id="damage_qty<?=$i?>"  autocomplete="off" required onBlur="checkRecQty('<?=$i?>')"; value="0" ></td>
                        <td><input type="text" class="digits form-control" style="width:80px;" name="miss_qty<?=$data_row[id]?>" id="miss_qty<?=$i?>"  autocomplete="off" value="0" readonly></td>
						<td><input name="excess<?=$data_row['id']?>" id="excess<?=$i?>" style="width:80px;"  class="digits form-control" type="text"  size="3" value="0" onKeyUp="checkRecQty('<?=$i?>');"/></td>
					</tr>
				<?php
					$total_qty+= $data_row['req_qty'];
					$sub_total+= $data_row['po_value'];
					$tax_total+= $data_row['tax_cost'];
					$grand_total+= $data_row['totalval'];
					$i++;
				}
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
					<tr>
						<td colspan="10" align="right"><strong>Total Qty</strong></td>
						<td><input type="text" name="totok_qty" id="totok_qty"  class="form-control"></td>
                        <td colspan="3">&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</div><!--close panel body-->
	</div><!--close panel-->
	<div class="panel panel-info table-responsive">
		  <div class="panel-heading">Receive</div>
		  <div class="panel-body">
			<table class="table table-bordered" width="100%">
				<tbody>          
				   <tr>
					 <td width="25%"><label class="control-label">Total Qty</label></td>
					 <td width="25%"><input type="text" name="tot_qty" id="tot_qty" class="form-control" value="<?=$total_qty;?>" style="width:150px;text-align:right" readonly/></td>
					 <td width="25%"><label class="control-label">Sub Total</label></td>
					 <td width="25%"><input type="text" name="sub_total" id="sub_total" class="form-control" value="<?=$sub_total?>" style="width:150px;text-align:right" readonly/></td>
				   </tr>
				   <tr>
					 <td><label class="control-label">Tax Total</label></td>
					 <td><input type="text" name="tax_total" id="tax_total" class="form-control" value="<?=$po_row['total_sgst_amt']+$po_row['total_cgst_amt']+$po_row['total_igst_amt'];?>" style="width:150px;text-align:right" readonly/></td>
					 <td><label class="control-label">Grand Total</label></td>
					 <td><input type="text" name="grand_total" id="grand_total" class="form-control" value="<?=$grand_total;?>" style="width:150px;text-align:right" readonly/></td>
				   </tr>
				   <tr>
					 <td><label class="control-label">TCS</label></td>
					 <td>
                     	<select name="tcs_per" id="tcs_per" class="form-control" onChange="recalculatetotal();">
                     		<option value="">--Please Select--</option>
                            <option value="0.1">0.1 %</option>
                     	</select></td>
					 <td><label class="control-label">TCS Amount</label></td>
					 <td><input type="text" name="tcs_amt" id="tcs_amt" class="form-control" value="0.00" style="width:150px;text-align:right" readonly/></td>
				   </tr>
                   <tr>
					 <td><label class="control-label">Round Off</label></td>
					 <td><input type="text" name="round_off" id="round_off" class="form-control" value="<?=$roundoff?>" style="width:150px;text-align:right" readonly/></td>
					 <td><label class="control-label">Final Total</label></td>
					 <td><input type="text" name="final_total" id="final_total" class="form-control" value="<?=$roundoff+$grand_total?>" style="width:150px;text-align:right" readonly/></td>
				   </tr>
				   <tr>
				   <td><label class="control-label">Receive Remark <span style="color:#F00">*</span></label></td>
					 <td colspan="3">
					   <textarea  name="rcv_rmk" id="rcv_rmk"  class=" form-control required" style="width:500px; resize:vertical"  required /></textarea></td>
					  </tr>
					  <tr>
					   <td><label class="control-label">Invoice Attachment</label></td>
					 <td colspan="3">
					   <input type="file" class="form-control" name="inv_attachment" id="inv_attachment" accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf" style="width:500px;"/></td>
					  </tr>
					  <tr>
					    <td><label class="control-label">Upload POD 1</label></td>
					    <td colspan="3"><input type="file" class="form-control" name="pod1" id="pod1" accept="image/*" style="width:500px;"/></td>
			      </tr>
					  <tr>
					    <td><label class="control-label">Upload POD 2</label></td>
					    <td colspan="3"><input type="file" class="form-control" name="pod2" id="pod2" accept="image/*" style="width:500px;"/></td>
			      </tr>
				   <tr>
					 <td colspan="4" align="center">
					<input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Receive" title="Receive">&nbsp;
				  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='grnAgainstPO.php?<?=$pagenav?>'">
						 <input type="hidden" id="po_from" name="po_from" class="required" required value="<?=$po_row['po_from']?>">
						 <input type="hidden" id="po_to" name="po_to" class="required" required value="<?=$po_row['po_to']?>">
                         <input type="hidden" id="stock_in" name="stock_in" class="required" required value="<?=$po_row['stock_in']?>">
						 <input type="hidden" id="po_no" name="po_no" class="required" required value="<?=$po_row['po_no']?>">
						 <input type="hidden" id="row_no" name="row_no" value="<?=$i?>">
						 <input type="hidden" id="postinv_no" name="postinv_no" value="<?=$po_row['invoice_no']?>">
                         <input type="hidden" id="postinv_date" name="postinv_date" value="<?=$po_row['invoice_date']?>">					 </td>
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