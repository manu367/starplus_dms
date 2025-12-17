<?php
require_once("../config/config.php");
require_once("../includes/ledger_function.php");
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM billing_master WHERE challan_no='".$docid."' AND type IN ('PURCHASE RETURN')";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
///// after hitting receive button ///
if($_POST){
	if ($_POST['upd']=='Receive'){
		$ref_no=base64_decode($_POST['refno']);
		///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
		$messageIdent = md5($_POST['upd'] . $ref_no);
		//and check it against the stored value:
		$sessionMessageIdent = isset($_SESSION['msgRecStock'])?$_SESSION['msgRecStock']:'';
		if($messageIdent!=$sessionMessageIdent){//if its different:
			//save the session var:
			$_SESSION['msgRecStock'] = $messageIdent;
		///// start transaction
		mysqli_autocommit($link1, false);
		$flag = true;
		$err_msg = "";
		$arr_tax = array();
		$arr_val = array();
		$gst_type = "";
		if($po_row["document_type"]=="INVOICE"){
			////////generate CN no.
			$res_cnt = mysqli_query($link1, "SELECT srn_str, srn_counter FROM document_counter WHERE location_code='".$po_row['to_location']."'");
			$row_cnt = mysqli_fetch_array($res_cnt);
			$crn_cnt = $row_cnt['srn_counter'] + 1;
			$crn_pad = $crn_cnt;
			$crn_no = $row_cnt['srn_str'].$crn_pad;
			//////insert in master table
			$sql_cn = "INSERT INTO credit_note SET 
			cust_id='".$po_row['from_location']."',
			location_id='".$po_row['to_location']."',
			sub_location='".$_POST['stock_in']."',
			entered_ref_no='".$po_row['ref_no']."',
			entered_ref_date='".$po_row['ref_date']."',
			ref_no='".$crn_no."',
			sys_ref_temp_no='".$crn_pad."',
			challan_no='".$po_row['challan_no']."',
			create_by='".$_SESSION['userid']."',
			remark='".$_POST['rcv_rmk']."',
			create_date='".$today."',
			amount='".$po_row['total_cost']."',
			status='Pending For Approval',
			create_ip='".$ip."',
			basic_amt = '".$po_row['basic_cost']."',
			discount_type = '',
			discount = '".$po_row['discount_amt']."',
			round_off='".$po_row['round_off']."',
			tcs_per='".$po_row['tcs_per']."',
			tcs_amt='".$po_row['tcs_amt']."',
			sgst_amt='".$po_row['total_sgst_amt']."',
			cgst_amt='".$po_row['total_cgst_amt']."',
			igst_amt='".$po_row['total_igst_amt']."',
			tax_cost='',description='SALE RETURN',
			to_gst_no='".$po_row['from_gst_no']."',
			party_name='".$po_row['from_partyname']."',
			from_partyname='".$po_row['party_name']."',
			from_gst_no='".$po_row['to_gst_no']."',
			bill_from='".$po_row['bill_topty']."',
			bill_topty='".$po_row['bill_from']."',
			from_addrs='".$po_row['to_addrs']."',
			disp_addrs='".$po_row['deliv_addrs']."',
			to_addrs='".$po_row['from_addrs']."',
			deliv_addrs='".$po_row['disp_addrs']."',
			to_state='".$po_row['from_state']."',
			from_state='".$po_row['to_state']."',
			to_city='".$po_row['from_city']."',
			from_city='".$po_row['to_city']."',
			to_pincode='".$po_row['from_pincode']."',
			from_pincode='".$po_row['to_pincode']."',
			to_phone='".$po_row['from_phone']."',
			from_phone='".$po_row['to_phone']."',
			to_email='".$po_row['from_email']."',
			from_email='".$po_row['to_email']."',
			billing_type='".$po_row['billing_type']."'";
			$res_cn= mysqli_query($link1,$sql_cn);
			//// check if query is not executed
			if (!$res_cn){
				 $flag = false;
				 $err_msg = "Error details1: " . mysqli_error($link1) . ".";
			}
			$resultcn = mysqli_query($link1, "UPDATE document_counter SET srn_counter=srn_counter+1, update_by='".$_SESSION['userid']."', updatedate='".$datetime."' WHERE location_code='".$po_row['to_location']."'");
			//// check if query is not executed
			if (!$resultcn) {
				$flag = false;
				$err_msg = "Error Code1.1: ".mysqli_error($link1);
			}
		}
		////// run data cycle of invoice and get posted value of receive qty
		$sql_inv_data = "SELECT * FROM billing_model_data WHERE challan_no='".$ref_no."'";
    	$res_invData = mysqli_query($link1,$sql_inv_data)or die("error1".mysqli_error($link1));
    	while($row_invData=mysqli_fetch_assoc($res_invData)){
			///// initialize posted variables
		  	$reqqty="req_qty".$row_invData['id'];
		  	$okqty="ok_qty".$row_invData['id'];
		  	$damageqty="damage_qty".$row_invData['id'];
		  	$missqty="miss_qty".$row_invData['id'];
		  	if($row_invData["prod_cat"]!="C"){
				///// update stock in inventory //
				if(mysqli_num_rows(mysqli_query($link1,"SELECT partcode FROM stock_status WHERE partcode='".$row_invData['prod_code']."' AND asc_code='".$_POST['to_location']."' AND sub_location='".$_POST['stock_in']."'"))>0){
					///if product is exist in inventory then update its qty 
					$result=mysqli_query($link1,"UPDATE stock_status SET qty=qty+'".$_POST[$reqqty]."',okqty=okqty+'".$_POST[$okqty]."',broken=broken+'".$_POST[$damageqty]."',missing=missing+'".$_POST[$missqty]."',updatedate='".$datetime."' WHERE partcode='".$row_invData['prod_code']."' AND asc_code='".$_POST['to_location']."' AND sub_location='".$_POST['stock_in']."'");
				}
				else{
					//// if product is not exist then add in inventory
					$result=mysqli_query($link1,"INSERT INTO stock_status SET asc_code='".$_POST['to_location']."', sub_location='".$_POST['stock_in']."',partcode='".$row_invData['prod_code']."',qty=qty+'".$_POST[$reqqty]."',okqty='".$_POST[$okqty]."',broken='".$_POST[$damageqty]."',missing='".$_POST[$missqty]."',uom='PCS',updatedate='".$datetime."'");
				}
				//// check if query is not executed
				if(!$result) {
					$flag = false;
					$err_msg = "Error details1: " . mysqli_error($link1) . ".";
			   }
			   ////// insert in stock ledger////
			   ### CASE 1 if user enter somthing in ok qty
			   if($_POST[$okqty]!="" && $_POST[$okqty]!=0 && $_POST[$okqty]!=0.00){
				  $flag=stockLedger($ref_no,$today,$row_invData['prod_code'],$_POST['billfrom'],$_POST['stock_in'],$_POST['stock_in'],"IN","OK",$_POST['invtype'],$_POST[$okqty],$row_invData['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
			   }
			   ### CASE 2 if user enter somthing in damage qty
			   if($_POST[$damageqty]!="" && $_POST[$damageqty]!=0 && $_POST[$damageqty]!=0.00){
				  $flag=stockLedger($ref_no,$today,$row_invData['prod_code'],$_POST['billfrom'],$_POST['stock_in'],$_POST['stock_in'],"IN","DAMAGE",$_POST['invtype'],$_POST[$damageqty],$row_invData['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
			   }
			   ### CASE 3 if user enter somthing in missing qty
			   if($_POST[$missqty]!="" && $_POST[$missqty]!=0 && $_POST[$missqty]!=0.00){
				  $flag=stockLedger($ref_no,$today,$row_invData['prod_code'],$_POST['billfrom'],$_POST['stock_in'],$_POST['stock_in'],"IN","MISSING",$_POST['invtype'],$_POST[$missqty],$row_invData['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
			   }
		   }
		   //////// make
		   if(($row_invData["prod_cat"]=="C" && $po_row["billing_type"]=="COMBO") || $po_row["billing_type"]!="COMBO"){
			   if($row_invData['sgst_per']!="" && $row_invData['sgst_per']!=0 && $row_invData['sgst_per']!=0.00){
					$gstper = round($row_invData['sgst_per']+$row_invData['cgst_per']);
					$arr_tax[$gstper] += $row_invData['sgst_amt'] + $row_invData['cgst_amt'];
					$arr_val[$gstper] += $row_invData['value'];
					$gst_type = "SGST-CGST";
				}else{
					$gstper = round($row_invData['igst_per']);
					$arr_tax[$gstper] += $row_invData['igst_amt'];
					$arr_val[$gstper] += $row_invData['value'];
					$gst_type = "IGST";
				}
			}
		   ///// update data table
           $result=mysqli_query($link1,"UPDATE billing_model_data SET okqty='".$_POST[$okqty]."',damageqty='".$_POST[$damageqty]."',missingqty='".$_POST[$missqty]."' WHERE id='".$row_invData['id']."'");
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $err_msg = "Error details2: " . mysqli_error($link1) . ".";
		   }
		   if($po_row["document_type"]=="INVOICE"){
				/////// credit note data
				$query2 = "INSERT INTO credit_note_data SET prod_code='".$row_invData['prod_code']."',combo_code='".$row_invData['combo_code']."',combo_name='".$row_invData['combo_name']."',prod_cat='".$row_invData['prod_cat']."',req_qty='".$row_invData['qty']."' , price='".$row_invData['price']."', value='".$row_invData['value']."' , discount='".$row_invData['discount']."', totalvalue='".$row_invData['totalvalue']."',ref_no='".$crn_no."',entry_date='".$today."' ,sgst_per='".$row_invData['sgst_per']."' ,sgst_amt='".$row_invData['sgst_amt']."',igst_per='".$row_invData['igst_per']."' ,igst_amt='".$row_invData['igst_amt']."',cgst_per='".$row_invData['cgst_per']."' ,cgst_amt='".$row_invData['cgst_amt']."'";			
				$result2 = mysqli_query($link1, $query2);
				//// check if query is not executed
				if (!$result2) {
					$flag = false;
					$err_msg = "Error details2: " . mysqli_error($link1) . ".";
				}
			}		   
		}//// close while loop
		//// Update status in  master table
    	$result=mysqli_query($link1,"UPDATE billing_master SET status='Received',receive_sub_location='".$_POST['stock_in']."',receive_date='".$today."',receive_time='".$currtime."',receive_by='".$_SESSION['userid']."',receive_remark='".$_POST['rcv_rmk']."',receive_ip='".$ip."' WHERE challan_no='".$ref_no."'");
		//// check if query is not executed
		if (!$result) {
		   $flag = false;
		   $err_msg = "Error details3: " . mysqli_error($link1) . ".";
		}
  		///// update billing_imei_data  table set ownercode equal to to location ////////////////////////
		$result_data=mysqli_query($link1,"UPDATE billing_imei_data SET owner_code ='".$_POST['to_location']."' WHERE doc_no='".$ref_no."'");
		//// check if query is not executed
    	if (!$result_data) {
	   		$flag = false;
	   		$err_msg = "Error details4: " . mysqli_error($link1) . ".";
    	}
		////// insert in activity table////
		$flag=dailyActivity($_SESSION['userid'],$ref_no,$_POST['invtype'],"RECEIVE",$ip,$link1,$flag);
		if($po_row["document_type"]!="INVOICE"){
			/////// make account ledger entry for location
			/////// start ledger entry for tally purpose ///// written by shekhar on 06 jan 2023
			///// make ledger array which are need to be process
			if($po_row["type"]=="STN"){
				if($po_row["document_type"] == 'Delivery Challan'){
					$hedid = "8";
					$hed = "Receipt Note";
					$arr_ldg_name = array(
					"igstldgname" => "Input IGST @",
					"cgstldgname" => "Input CGST @",
					"sgstldgname" => "Input SGST @",
					"igstdocldgname" => "Branch Purchase @",
					"cgstdocldgname" => "Branch Purchase within State",
					"sgstdocldgname" => "Branch Purchase within State",
					"tcsldgname" => "TCS on Purchase @",
					"roundoffldgname" => "Rounded Off"
					);
				}else{
					$hedid = "1";
					$hed = "Purchase";
					$arr_ldg_name = array(
					"igstldgname" => "Input IGST @",
					"cgstldgname" => "Input CGST @",
					"sgstldgname" => "Input SGST @",
					"igstdocldgname" => "Branch Purchase @",
					"cgstdocldgname" => "Branch Purchase within State",
					"sgstdocldgname" => "Branch Purchase within State",
					"tcsldgname" => "TCS on Purchase @",
					"roundoffldgname" => "Rounded Off"
					);
				}
			}else{
				if($po_row["document_type"] == 'Delivery Challan'){
					$hedid = "8";
					$hed = "Receipt Note";
					$arr_ldg_name = array(
					"igstldgname" => "Input IGST @",
					"cgstldgname" => "Input CGST @",
					"sgstldgname" => "Input SGST @",
					"igstdocldgname" => $po_row["ledger_name"],
					"cgstdocldgname" => $po_row["ledger_name"],
					"sgstdocldgname" => $po_row["ledger_name"],
					"tcsldgname" => "TCS on Purchase @",
					"roundoffldgname" => "Rounded Off"
					);
				}else{
					$hedid = "1";
					$hed = "Purchase";
					$arr_ldg_name = array(
					"igstldgname" => "Input IGST @",
					"cgstldgname" => "Input CGST @",
					"sgstldgname" => "Input SGST @",
					"igstdocldgname" => "Central Purchase @",
					"cgstdocldgname" => "Local Purchase @",
					"sgstdocldgname" => "Local Purchase @",
					"tcsldgname" => "TCS on Purchase @",
					"roundoffldgname" => "Rounded Off"
					);
				}
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
			//// check if tally sink option is enable
			$tallySink = explode("~",getAnyDetails($_POST['to_location'],"id_type,tally_sink","asc_code","asc_master",$link1));
			if($tallySink[1]=="Y" && $tallySink[0]=="BR"){
				$resp = explode("~",storeLedgerTransaction($_POST['to_location'],$ref_no,$today,$hedid,$hed,$arr_tax,$arr_val,$po_row["tcs_per"],$po_row["tcs_amt"],$po_row["round_off"],$gst_type,$arr_ldg_name,"Direct Purchase","Purchase Accounts",$link1,$flag));
				$flag = $resp[0];
				$err_msg = $resp[1];
			}
		}
		///// check both master and data query are successfully executed
		if($flag) {
        	mysqli_commit($link1);
       	 	$msg = "Invoice is successfully received against ".$ref_no;
			if($po_row["document_type"]=="INVOICE"){
            	$msg = "Invoice is successfully received with ref. no." . $ref_no."<br/>Please approve CN ".$crn_no." now.";
			}else{
				$msg = "Invoice/DC is successfully received with ref. no." . $ref_no." Please check stock.";
			}
    	}else{
			mysqli_rollback($link1);
			$msg = "Request could not be processed. Please try again.".$err_msg;
		} 
		}else {
		//you've sent this already!
		$msg="You have saved this already ";
		$cflag = "warning";
		$cmsg = "Warning";
	}	
    	mysqli_close($link1);
		///// move to parent page
    	header("location:receiveInvoice.php?msg=".$msg."".$pagenav);
    	exit;
 	}
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
<script type="text/javascript">
$(document).ready(function(){
    $("#frm2").validate();
});
</script>
<script type="text/javascript">
function checkRecQty(a){
	var reqqty=0;
	var okqty=0;
	var damageqty=0;
	//// check requested qty
    if(document.getElementById("req_qty"+a).value==""){
       reqqty=0;
	}else{
	   reqqty=parseInt(document.getElementById("req_qty"+a).value);
	}
	//// check enter ok qty
    if(document.getElementById("ok_qty"+a).value==""){
       okqty=0;
    }else{
       okqty=parseInt(document.getElementById("ok_qty"+a).value);
    }
	//// check enter damage qty
    if(document.getElementById("damage_qty"+a).value==""){
       damageqty=0;
    }else{
       damageqty=parseInt(document.getElementById("damage_qty"+a).value);
    }
	//// check enter qty should not be greater than requested qty
    if(reqqty < (okqty + damageqty)){
       alert("Ok Qty & Damage Qty can not more than requested Qty!");
		document.getElementById("miss_qty"+a).value=0;
		document.getElementById("damage_qty"+a).value=0;
		//document.getElementById("ok_qty"+a).focus();
		document.getElementById("upd").disabled=true;
    }else{
		document.getElementById("miss_qty"+a).value=(reqqty - (okqty + damageqty));
		//document.getElementById("miss_qty"+a).focus();
		document.getElementById("upd").disabled=false;
	}
}
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
	<div class="container-fluid">
 		<div class="row content">
		<?php 
    	include("../includes/leftnav2.php");
    	?>
   		<div class="col-sm-9">
      		<h2 align="center"><i class="fa fa-level-down"></i> Receive Invoice Details</h2>
      		<h4 align="center"><?php echo $po_row['type']." ".$po_row['document_type'];?></h4>
   			<div class="panel-group">
   			<form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    			<div class="panel panel-default table-responsive">
        			<div class="panel-heading heading1">Party Information</div>
         			<div class="panel-body">
          				<table class="table table-bordered" width="100%">
            				<tbody>
              					<tr>
                                    <td width="20%"><label class="control-label">Billing To</label></td>
                                    <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['to_location'],"name,city,state",$link1));?><input name="billto" id="billto" type="hidden" value="<?=$po_row['to_location']?>"/></td>
                                    <td width="20%"><label class="control-label">Billing From</label></td>
                                    <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['from_location'],"name,city,state",$link1));?><input name="billfrom" id="billfrom" type="hidden" value="<?=$po_row['from_location']?>"/></td>
                               	</tr>
              					<tr>
                                    <td><label class="control-label">Invoice No.</label></td>
                                    <td><?php echo $po_row['challan_no'];?><input name="invtype" id="invtype" type="hidden" value="<?php echo $po_row['type']." ".$po_row['document_type'];?>"/></td>
                                    <td><label class="control-label">Billing Date</label></td>
                                    <td><?php echo $po_row['sale_date'];?></td>
                              	</tr>
              					<?php if($po_row['ref_no']!='' && $po_row['document_type']=="INVOICE"){ ?>
              					<tr>
                					<td><label class="control-label">Ref. Invoice No.</label></td>
                                    <td><?php echo $po_row['ref_no'];?></td>
                                    <td><label class="control-label">Ref. Invoice Date</label></td>
                                    <td><?php echo $po_row['ref_date'];?></td>
                                    </tr>
                           		<?php } ?>
                                <tr>
                                    <td><label class="control-label">Entry By</label></td>
                                    <td><?php echo getAdminDetails($po_row['entry_by'],"name",$link1);?></td>
                                    <td><label class="control-label">Status</label></td>
                                    <td><?php echo $po_row['status'];?></td>
                                </tr>
                                <tr>
                                    <td><label class="control-label">Cost Centre(Godown)<span style="color:#F00">*</span></label></td>
                                    <td><select name="stock_in" id="stock_in" required class="form-control required" data-live-search="true">
                                            <option value="" selected="selected">Please Select </option>
                                             <?php                                 
											$smfm_sql = "SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code='".$po_row['to_location']."'";
											$smfm_res = mysqli_query($link1,$smfm_sql);
											while($smfm_row = mysqli_fetch_array($smfm_res)){
											?>
											<option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['stock_in'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
											<?php
											}
											?>
											<?php                                 
											$smf_sql = "SELECT sub_location, sub_location_name FROM sub_location_master WHERE main_location='".$po_row['to_location']."' AND status='Active'";
											$smf_res = mysqli_query($link1,$smf_sql);
											while($smf_row = mysqli_fetch_array($smf_res)){
											?>
											<option value="<?=$smf_row['sub_location']?>" <?php if($smf_row['sub_location']==$_REQUEST['stock_in'])echo "selected";?>><?=$smf_row['sub_location_name']." | ".$smf_row['sub_location']?></option>
											<?php
											}
											?>
                                        </select></td>
                                    <td><label class="control-label">Document Type</label></td>
                                    <td><?php echo $po_row['document_type']; if($po_row['ledger_name']){ echo " (".$po_row['ledger_name'].")";}?></td>
                                </tr>
            				</tbody>
          				</table>
        			</div><!--close panel body-->
    			</div><!--close panel-->
    			<div class="panel panel-default table-responsive">
      				<div class="panel-heading heading1">Items Information</div>
      				<div class="panel-body">
       					<table class="table table-bordered" width="100%">
           	 				<thead>
              					<tr class="<?=$tableheadcolor?>">
                					<th style="text-align:center" width="3%">#</th>
                                    <th style="text-align:center" width="15%">Product</th>
                                    <th style="text-align:center" width="10%">Disp. Qty</th>
                                    <th style="text-align:center" width="10%">Ok Qty</th>
                                    <th style="text-align:center" width="10%">Damage Qty</th>
                                    <th style="text-align:center" width="10%">Missing Qty</th>
                                    <th style="text-align:center" width="7%">Price</th>
                                    <th style="text-align:center" width="10%">Value</th>
                                    <th style="text-align:center" width="5%">Discount</th>
                                    <th style="text-align:center" width="7%">After Discount Value</th>
                                    <th style="text-align:center" width="5%">SGST (%)</th>
                                    <th style="text-align:center" width="6%">SGST Amt</th>
                                    <th style="text-align:center" width="6%">CGST (%)</th>
                                    <th style="text-align:center" width="7%">CGST Amt</th>
                                    <th style="text-align:center" width="6%">IGST (%)</th>
                                    <th style="text-align:center" width="7%">IGST Amt</th>
                                    <th style="text-align:center" width="10%">Total</th>
                             	</tr>
            				</thead>
            			<tbody>
            			<?php
						$i=1;
						$tot_sgst_amt = 0;
						$tot_cgst_amt = 0;
						$tot_igst_amt = 0;
						$podata_sql="SELECT * FROM billing_model_data where challan_no='".$po_row['challan_no']."'";
						$podata_res=mysqli_query($link1,$podata_sql);
						while($podata_row=mysqli_fetch_assoc($podata_res)){
							$discount_val = number_format(($podata_row['value'] - ($podata_row['discount'])),'2','.','');
							$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,productcode",$link1));
						?>
              				<tr>
                				<td><?=$i?></td>
                				<td><?php if($podata_row["prod_cat"]=="C"){ echo $podata_row["combo_name"];}else{ echo $proddet[0]." (".$proddet[1].")";}?></td>
                				<td style="text-align:right"><input type="hidden" name="req_qty<?=$podata_row['id']?>" id="req_qty<?=$i?>" value="<?=$podata_row['qty']?>"><?=$podata_row['qty']?></td>
                				<td style="text-align:right"><input type="text" class="digits form-control" style="width:80px;" name="ok_qty<?=$podata_row['id']?>" id="ok_qty<?=$i?>"  autocomplete="off" required onKeyUp="checkRecQty('<?=$i?>');" value="" ></td>
                				<td style="text-align:right"><input type="text" class="digits form-control" style="width:80px;" name="damage_qty<?=$podata_row['id']?>" id="damage_qty<?=$i?>"  autocomplete="off" required onKeyUp="checkRecQty('<?=$i?>');" value="0" ></td>
                				<td style="text-align:right"><input type="text" class="digits form-control" style="width:80px;" name="miss_qty<?=$podata_row['id']?>" id="miss_qty<?=$i?>"  autocomplete="off" required onKeyUp="checkRecQty('<?=$i?>');" value="0" readonly ></td>
                                <td style="text-align:right"><?=$podata_row['price']?></td>
                                <td style="text-align:right"><?=$podata_row['value']?></td>
                                <td style="text-align:right"><?=$podata_row['discount']?></td>
                                <td style="text-align:right"><?=$discount_val?></td>
                                <td style="text-align:left"><?=$podata_row['sgst_per']?></td>
                                <td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
                                <td style="text-align:left"><?=$podata_row['cgst_per']?></td>
                                <td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
                                <td style="text-align:left"><?=$podata_row['igst_per']?></td>
                                <td style="text-align:right"><?=$podata_row['igst_amt']?></td>
                                <td style="text-align:right"><?=$podata_row['totalvalue'];?></td>
                           	</tr>
							<?php
							$sum_qty+=$podata_row['qty'];
							$discount+=$podata_row['discount'];
							$tot_sgst_amt+=$podata_row['sgst_amt'];
            				$tot_cgst_amt+=$podata_row['cgst_amt'];
            				$tot_igst_amt+=$podata_row['igst_amt'];
							$i++;
						}
						?>
            			</tbody>
          			</table>
      			</div><!--close panel body-->
    		</div><!--close panel-->
    		<div class="panel panel-default table-responsive">
      			<div class="panel-heading heading1">Amount Information</div>
      			<div class="panel-body">
        			<table class="table table-bordered" width="100%">
                    <tbody>
                      <tr>
                        <td width="20%"><strong>Total Qty</strong></td>
                        <td width="30%"><?=$sum_qty?></td>
                        <td width="20%"><label class="control-label">Sub Total</label></td>
                        <td width="30%" align="right"><?php echo $po_row['basic_cost'];?></td>
                      </tr>
                      <tr>
                        <td rowspan="2"><label class="control-label">Delivery Address</label></td>
                        <td rowspan="2"><?=$po_row["deliv_addrs"]?></td>
                        <td><label class="control-label">Discount</label></td>
                        <td align="right"><?php echo $po_row['discount_amt'];?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Total SGST</label></td>
                        <td align="right"><?=$tot_sgst_amt ?></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td><label class="control-label">Total CGST</label></td>
                        <td align="right"><?=$tot_cgst_amt ?></td>
                      </tr>
                      <tr>
                        <td rowspan="2"><label class="control-label">Remark</label></td>
                        <td rowspan="2"><?php echo $po_row['billing_rmk'];?></td>
                        <td><label class="control-label">Total IGST</label></td>
                        <td align="right"><?=$tot_igst_amt ?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Grand Total</label></td>
                        <td align="right"><?php echo $po_row['total_cost'];?></td>
                      </tr>
                      <?php if($po_row['tcs_per']!=0.00){ ?>
                      <tr>
                        <td><label class="control-label">TCS</label></td>
                        <td><?=$po_row['tcs_per']?></td>
                        <td><label class="control-label">TCS Amount</label></td>
                        <td align="right"><?=$po_row['tcs_amt']?></td>
                      </tr>
                      <?php }?>
                      <?php if($po_row['round_off']!=0.00){?>
                      <tr>
                        <td><label class="control-label">Round Off</label></td>
                        <td><?=$po_row['round_off']?></td>
                        <td><label class="control-label">Final Total</label></td>
                        <td align="right"><?=$po_row['tcs_amt']+$po_row['round_off']+$po_row['total_cost']?></td>
                      </tr>
                      <?php }?>                       
                    </tbody>
                  </table>
      			</div><!--close panel body-->
    		</div><!--close panel-->
    		<div class="panel panel-default table-responsive">
      			<div class="panel-heading heading1">Logistic Information</div>
      			<div class="panel-body">
        			<table class="table table-bordered" width="100%">
            			<tbody>
              				<tr>
                				<td width="20%"><label class="control-label">Logistic Name</label></td>
                                <td width="30%"><?php echo getLogistic($po_row['diesel_code'],$link1);?></td>
                                <td width="20%"><label class="control-label">Docket No.</label></td>
                                <td width="30%"><?php echo $po_row['docket_no'];?></td>
                         	</tr>
              				<tr>
                				<td><label class="control-label">Logistic Person</label></td>
                                <td><?php echo $po_row['logistic_person'];?></td>
                                <td><label class="control-label">Contact No.</label></td>
                                <td><?php echo $po_row['logistic_contact'];?></td>
                         	</tr>
               				<tr>
                 				<td><label class="control-label">Carrier No.</label></td>
                                <td><?php echo $po_row['vehical_no'];?></td>
                                <td><label class="control-label">Dispatch Date</label></td>
                                <td><?php echo $po_row['dc_date'];?></td>
                          	</tr>
                            <tr>
                                <td><label class="control-label">Dispatch Remark</label></td>
                                <td colspan="3"><?php echo $po_row['disp_rmk'];?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Receive Remark <span style="color:#F00">*</span></label></td>
                                <td colspan="3"><textarea name="rcv_rmk" id="rcv_rmk" class="form-control required" style="resize:none;width:500px;" required></textarea></td>
                            </tr>
               				<tr>
                 				<td colspan="4" align="center">
                 					<input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Receive" title="Receive This Invoice" <?php if($_POST["upd"]=="Receive"){?> disabled<?php }?>/>&nbsp;
                    				<input name="refno" id="refno" type="hidden" value="<?=base64_encode($po_row['challan_no'])?>"/>
                 					<input name="to_location" id="to_location" type="hidden" value="<?=$po_row['to_location']?>"/>
                 					<input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='receiveInvoice.php?<?=$pagenav?>'"></td>
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