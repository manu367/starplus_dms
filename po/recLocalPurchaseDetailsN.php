<?php
require_once("../config/config.php");
require_once("../includes/ledger_function.php");
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM vendor_order_master where po_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
///// after hitting receive button ///
if($_POST){
 if ($_POST['upd']=='Receive'){
	$ref_no=base64_decode($_POST['refno']);
	///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
	$messageIdent = md5($ref_no);
	//and check it against the stored value:
	$sessionMessageIdent = isset($_SESSION['messageIdentLP'])?$_SESSION['messageIdentLP']:'';
	if($messageIdent!=$sessionMessageIdent){//if its different:
	//save the session var:
	$_SESSION['messageIdentLP'] = $messageIdent;
	if($_POST['stock_in']){
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg = "";
	$chk = 0;
	$arr_tax = array();
	$arr_val = array();
	$gst_type = "";
	//// check post invoice no. is already entered
	if (mysqli_num_rows(mysqli_query($link1,"SELECT id FROM billing_master WHERE inv_ref_no='".$po_row["invoice_no"]."' AND from_location='".$_POST['vendorname']."' AND status!='Cancelled'"))>0) {
		$cflag="danger";
		$cmsg="Failed";
		$msg = "Request could not be processed. Please try again. You have entered duplicate invoice number.".$po_row["invoice_no"];
		header("location:localPurchaseList.php?msg=".$msg."".$pagenav);
		exit;
	}
	////// run data cycle of po and get posted value of receive qty
	 $sql_po_data="select * from vendor_order_data where po_no='".$ref_no."'";
    $res_poData=mysqli_query($link1,$sql_po_data)or die("error1".mysqli_error($link1));
    while($row_poData=mysqli_fetch_assoc($res_poData)){
		  ///// initialize posted variables
		  $reqqty="req_qty".$row_poData['id'];
		  $okqty="ok_qty".$row_poData['id'];
		  $damageqty="damage_qty".$row_poData['id'];
		  $missqty="miss_qty".$row_poData['id'];
		  ///// check is there any service in line item
		  $proddet = getAnyDetails($row_poData['prod_code'],"is_service","productcode","product_master",$link1);
		  if($proddet=="Y"){
		  		/////nothing to do
				$chk .= "A";
		  }else{
		  	$chk .= "B";
		  ///// update stock in inventory //
		  if(mysqli_num_rows(mysqli_query($link1,"select partcode from stock_status where partcode='".$row_poData['prod_code']."' and asc_code='".$_POST['locationname']."' and sub_location='".$_POST['stock_in']."'"))>0){
			 ///if product is exist in inventory then update its qty 
			 $result=mysqli_query($link1,"update stock_status set qty=qty+'".$_POST[$reqqty]."',okqty=okqty+'".$_POST[$okqty]."',broken=broken+'".$_POST[$damageqty]."',missing=missing+'".$_POST[$missqty]."',updatedate='".$datetime."' where partcode='".$row_poData['prod_code']."' and asc_code='".$_POST['locationname']."' AND sub_location='".$_POST['stock_in']."'");
		  }
		  else{
			 //// if product is not exist then add in inventory
			 $result=mysqli_query($link1,"insert into stock_status set asc_code='".$_POST['locationname']."',sub_location='".$_POST['stock_in']."',partcode='".$row_poData['prod_code']."',qty=qty+'".$_POST[$reqqty]."',okqty='".$_POST[$okqty]."',broken='".$_POST[$damageqty]."',missing='".$_POST[$missqty]."',uom='PCS',updatedate='".$datetime."'");
		  }
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $error_msg =  "Error details1: " . mysqli_error($link1) . ".";
			   $chk .= "C";
           }
		   
			////// insert in stock ledger////
		   ### CASE 1 if user enter somthing in ok qty
		   if($_POST[$okqty]!="" && $_POST[$okqty]!=0 && $_POST[$okqty]!=0.00){
		      $flag=stockLedger($ref_no,$today,$row_poData['prod_code'],$_POST['vendorname'],$_POST['stock_in'],$_POST['stock_in'],"IN","OK","Local Purchase",$_POST[$okqty],$row_poData['po_price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
			  $chk .= "D";
		   }
		   ### CASE 2 if user enter somthing in damage qty
		   if($_POST[$damageqty]!="" && $_POST[$damageqty]!=0 && $_POST[$damageqty]!=0.00){
		      $flag=stockLedger($ref_no,$today,$row_poData['prod_code'],$_POST['vendorname'],$_POST['stock_in'],$_POST['stock_in'],"IN","DAMAGE","Local Purchase",$_POST[$damageqty],$row_poData['po_price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
			  $chk .= "D";
		   }
		   ### CASE 3 if user enter somthing in missing qty
		   if($_POST[$missqty]!="" && $_POST[$missqty]!=0 && $_POST[$missqty]!=0.00){
		      $flag=stockLedger($ref_no,$today,$row_poData['prod_code'],$_POST['vendorname'],$_POST['stock_in'],$_POST['stock_in'],"IN","MISSING","Local Purchase",$_POST[$missqty],$row_poData['po_price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
			  $chk .= "D";
		   }
		   $sumqty = $_POST[$okqty]+$_POST[$damageqty]+$_POST[$missqty];
		   
			}
			
			if($row_poData['sgst_per']!="" && $row_poData['sgst_per']!=0 && $row_poData['sgst_per']!=0.00){
				$gstper = $row_poData['sgst_per']+$row_poData['cgst_per'];
				$arr_tax[$gstper] += $row_poData['sgst_amt'] + $row_poData['cgst_amt'];
				$arr_val[$gstper] += $row_poData['po_value'];
				$gst_type = "SGST-CGST";
			}else{
				$gstper = $row_poData['igst_per'];							
				$arr_tax[$gstper] += $row_poData['igst_amt'];
				$arr_val[$gstper] += $row_poData['po_value'];
				$gst_type = "IGST";
			}	
			
		   	$req_ins2 = "insert into billing_model_data  set challan_no ='".$ref_no."' , prod_code ='".$row_poData["prod_code"]."',from_location='".$_POST['vendorname']."', qty='".$sumqty."',okqty='".$_POST[$okqty]."' ,damageqty='".$_POST[$damageqty]."',missingqty='".$_POST[$missqty]."', price='".$row_poData['po_price']."',value='".$row_poData['po_value']."',sgst_per='".$row_poData['sgst_per']."',sgst_amt='".$row_poData['sgst_amt']."',cgst_per='".$row_poData['cgst_per']."',cgst_amt='".$row_poData['cgst_amt']."' ,igst_per='".$row_poData['igst_per']."',igst_amt='".$row_poData['igst_amt']."' ,totalvalue ='".$row_poData['totalval']."' , entry_date = '".$today."' , sale_date = '".$today."'";
			$req_res2 = mysqli_query($link1,$req_ins2);
			//// check if query is not executed
			if (!$req_res2) {
				$flag = false;
				$error_msg = "Error details2: " . mysqli_error($link1) . ".";
				$chk .= "E";
			}
			
		   ///// update data table
           $result=mysqli_query($link1,"update vendor_order_data set okqty='".$_POST[$okqty]."',damageqty='".$_POST[$damageqty]."',missingqty='".$_POST[$missqty]."' where id='".$row_poData['id']."'");
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $error_msg =  "Error details23: " . mysqli_error($link1) . ".";
			   $chk .= "F";
		   }
		   
	}//// close while loop
	//// Update status in  master table
    $result=mysqli_query($link1,"update vendor_order_master set status='Received',receive_date='".$today."',receive_time='".$currtime."',receive_by='".$_SESSION['userid']."',receive_remark='".$_POST['rcv_rmk']."',receive_ip='".$ip."',sub_location='".$_POST['stock_in']."' where po_no='".$ref_no."'");
	//// check if query is not executed
    if (!$result) {
	   $flag = false;
	   $error_msg =  "Error details3: " . mysqli_error($link1) . ".";
	   $chk .= "G";
    }
	
	///////// get location details
	$loc_det =  explode("~",getLocationDetails($_POST['locationname'],"name,id_type,email,phone,addrs,disp_addrs,city,state,statecode,pincode,gstin_no",$link1));
	//////// get vendor details
	$vend_det = explode("~",getVendorDetails($_POST['vendorname'],"name,city,state,address,phone,email,pincode,bill_address,gstin_no",$link1));
	if($vend_det[0]==""){
		$vend_det = explode("~",getLocationDetails($_POST['vendorname'],"name,city,state,addrs,phone,email,pincode,addrs,gstin_no",$link1));
	}
	if($po_row["document_type"] == 'DC'){
		$doctype =  "Delivery Challan";
		$invoicetype = "Delivery Challan";
	}else {
		$doctype =  "INVOICE";
		$invoicetype = "RETAIL INVOICE";
	}
	/////////////////////////////// insert data into grn master  table///////////////////////////////////////////////
	$grn_master="insert into billing_master set from_location ='".$_POST['vendorname']."', to_location='".$_POST['locationname']."',sub_location='".$_POST['stock_in']."',from_gst_no='".$vend_det[8]."', from_partyname='".$vend_det[0]."', party_name='".$loc_det[0]."', to_gst_no='".$loc_det[10]."' ,ref_no='', receive_date='".$today."' ,receive_time='".$time."', entry_date ='".$today."' , status='Received' , challan_no='".$ref_no."', basic_cost='".$po_row["po_value"]."', tax_cost='".($po_row["total_sgst_amt"]+$po_row["total_cgst_amt"]+$po_row["total_igst_amt"])."',total_sgst_amt='".$po_row["total_sgst_amt"]."',total_cgst_amt='".$po_row["total_cgst_amt"]."',total_igst_amt='".$po_row["total_igst_amt"]."',round_off='".$po_row["round_off"]."', total_cost='".$po_row["total_amt"]."',tcs_per='".$po_row["tcs_per"]."', tcs_amt='".$po_row["tcs_amt"]."' , inv_ref_no='".$po_row["invoice_no"]."', receive_remark='".$_POST['rcv_rmk']."',type='LP',document_type='".$doctype."', sale_date = '".$today."' ,grn_doc = '',from_state='".$vend_det[2]."', to_state='".$loc_det[7]."', from_city='".$vend_det[1]."', to_city='".$loc_det[6]."', from_pincode='".$vend_det[6]."', to_pincode='".$loc_det[9]."', from_phone='".$vend_det[4]."', to_phone='".$loc_det[3]."', from_email='".$vend_det[5]."', to_email='".$loc_det[2]."', from_addrs='".$vend_det[3]."', disp_addrs='".$vend_det[3]."', to_addrs='".$loc_det[4]."', deliv_addrs='".$po_row["delivery_address"]."',ledger_name='".$po_row["ledger_name"]."',freight='".$po_row['freight']."',tds='".$po_row['tds']."'";
	$result=mysqli_query($link1,$grn_master);
	//// check if query is not executed
	if (!$result) {
		 $flag = false;
		 $error_msg = "Error details31: " . mysqli_error($link1) . ".";
		 $chk .= "H";
	}
	
	################################################## Update credit limit of party
			if(mysqli_num_rows(mysqli_query($link1,"select id from current_cr_status where parent_code='".$_POST['locationname']."' and asc_code='".$_POST['vendorname']."'"))>0){
				$upd = mysqli_query($link1,"update current_cr_status set cr_abl=cr_abl+'".$po_row["total_amt"]."',total_cr_limit=total_cr_limit+'".$po_row["total_amt"]."', last_updated='$today' where parent_code='".$_POST['locationname']."' and asc_code='".$_POST['vendorname']."'");
			   ############# check if query is not executed
				if (!$upd) {
					$flag = false;
					$error_msg = "Error details11: " . mysqli_error($link1) . ".";
					$chk .= "I";
				}
		}else{
				$upd = mysqli_query($link1,"insert into current_cr_status set cr_abl=cr_abl+'".$po_row["total_amt"]."',total_cr_limit=total_cr_limit+'".$po_row["total_amt"]."', last_updated='$today', parent_code='".$_POST['locationname']."' , asc_code='".$_POST['vendorname']."'");
			   ############# check if query is not executed
				if (!$upd) {
					$flag = false;
					$error_msg = "Error details11: " . mysqli_error($link1) . ".";
					$chk .= "I";
				}
		}
		
	////// maintain party ledger////
	$flag=partyLedger($_POST['locationname'],$_POST['vendorname'],$ref_no,$today,$today,$currtime,$_SESSION['userid'],"LP",$po_row["total_amt"],"CR",$link1,$flag);
	$chk .= "J";
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$ref_no,"LP","RECEIVE",$ip,$link1,$flag);
	$chk .= "K";
	/////// make account ledger entry for location
	/////// start ledger entry for tally purpose ///// written by shekhar on 15 july 2022
	///// make ledger array which are need to be process
	/*$arr_ldg_name = array(
	"igstldgname" => "Input IGST @",
	"cgstldgname" => "Input CGST @",
	"sgstldgname" => "Input SGST @",
	"igstdocldgname" => "Central Purchase @",
	"cgstdocldgname" => "Local Purchase @",
	"sgstdocldgname" => "Local Purchase @",
	"tcsldgname" => "TCS on Purchase @",
	"roundoffldgname" => "Rounded Off"
	);*/
	if($po_row["document_type"] == 'DC'){
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
	$resp = explode("~",storeLedgerTransaction($_POST['locationname'],$ref_no,$today,$hedid,$hed,$arr_tax,$arr_val,$po_row["tcs_per"],$po_row["tcs_amt"],$po_row["round_off"],$gst_type,$arr_ldg_name,"Direct Purchase","Purchase Accounts",$link1,$flag));
	$flag = $resp[0];
	$error_msg = $resp[1];
	$chk .= "L";
	/////// end ledger entry for tally purpose ///// written by shekhar on 15 july 2022
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Local Purchase Order is successfully received against ".$ref_no;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.".$error_msg;
	} 
	}else{
		$msg = "Request could not be processed . Please select cost center.";
	}
	}else {
		//you've sent this already!
		$msg="You have saved this already ";
		$cflag = "warning";
		$cmsg = "Warning";
	}
    mysqli_close($link1);
	///// move to parent page
    header("location:localPurchaseList.php?msg=".$msg."".$pagenav);
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
		document.getElementById("ok_qty"+a).focus();
		document.getElementById("upd").disabled=false;
	}
}
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-shopping-basket"></i> Receive Local Purchase Details</h2><br/>
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Purchase Order From</label></td>
                <td width="30%"><?php echo getAnyParty($po_row['po_from'],$link1);?><input name="vendorname" id="vendorname" type="hidden" value="<?=$po_row['po_from']?>"/></td>
                <td width="20%"><label class="control-label">Purchase Order To</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['po_to'],"name,city,state",$link1));?><input name="locationname" id="locationname" type="hidden" value="<?=$po_row['po_to']?>"/></td>
              </tr>
              <tr>
                <td><label class="control-label">Purchase Order No.</label></td>
                <td><?php echo $po_row['po_no'];?></td>
                <td><label class="control-label">Purchase Order Date</label></td>
                <td><?php echo $po_row['requested_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo getAdminDetails($po_row['create_by'],"name",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $po_row['status'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Invoice No.</label></td>
                <td><?php echo $po_row['invoice_no'];?></td>
                <td><label class="control-label">Invoice Date</label></td>
                <td><?php echo $po_row['invoice_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Document Type</label></td>
                <td><?php echo $po_row['document_type'];?></td>
                <td><label class="control-label">Ledger Name</label></td>
                <td><?php echo $po_row['ledger_name'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Currency Type</label></td>
                <td><?php echo $po_row['currency_type'];?></td>
                <td><label class="control-label">Cost Centre(Godown)<span style="color:#F00">*</span></label></td>
                <td><select name="stock_in" id="stock_in" required class="form-control selectpicker required" data-live-search="true">
                                            <option value="" selected="selected">Please Select </option>
                                             <?php                                 
											$smfm_sql = "SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code='".$po_row['po_to']."'";
											$smfm_res = mysqli_query($link1,$smfm_sql);
											while($smfm_row = mysqli_fetch_array($smfm_res)){
											?>
											<option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['stock_in'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
											<?php
											}
											?>
											<?php                                 
											$smf_sql = "SELECT sub_location, sub_location_name FROM sub_location_master WHERE main_location='".$po_row['po_to']."' AND status='Active'";
											$smf_res = mysqli_query($link1,$smf_sql);
											while($smf_row = mysqli_fetch_array($smf_res)){
											?>
											<option value="<?=$smf_row['sub_location']?>" <?php if($smf_row['sub_location']==$_REQUEST['stock_in'])echo "selected";?>><?=$smf_row['sub_location_name']." | ".$smf_row['sub_location']?></option>
											<?php
											}
											?>
                                        </select></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th width="5%" rowspan="2" style="text-align:center">#</th>
                <th width="17%" rowspan="2" style="text-align:center">Product</th>
                <th width="5%" rowspan="2" style="text-align:center">Req. Qty</th>
                <th width="6%" rowspan="2" style="text-align:center">Price</th>
                <th width="6%" rowspan="2" style="text-align:center">Value</th>
                <th width="5%" rowspan="2" style="text-align:center">SGST<br>(%)</th>
                <th width="6%" rowspan="2" style="text-align:center">SGST Amt</th>
                <th width="5%" rowspan="2" style="text-align:center">CGST<br>(%)</th>
                <th width="6%" rowspan="2" style="text-align:center">CGST Amt</th>
                <th width="5%" rowspan="2" style="text-align:center">IGST<br>(%)</th>
                <th width="6%" rowspan="2" style="text-align:center">IGST Amt</th>
                <th colspan="3" style="text-align:center">Receive Qty</th>
                <th width="6%" rowspan="2" style="text-align:center">Line Total</th>
                </tr>
              <tr>
                <th style="text-align:center" width="5%">Ok</th>
                <th style="text-align:center" width="5%">Damage</th>
                <th style="text-align:center" width="5%">Missing</th>
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM vendor_order_data where po_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,productcolor,productcode,is_service",$link1));
				///// get scanned serial nos.
				$scanned_ok = mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS okqty FROM billing_imei_data WHERE prod_code='".$podata_row['prod_code']."' AND doc_no='".$docid."' AND stock_type='OK'"));
				if($proddet[3]=="Y"){
					$okqty = round($podata_row["req_qty"]);
				}else{
					$okqty = $scanned_ok["okqty"];
				}
				
				$scanned_dmg = mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS dmgqty FROM billing_imei_data WHERE prod_code='".$podata_row['prod_code']."' AND doc_no='".$docid."' AND stock_type='DAMAGE'"));
				$scanned_mis = mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS misqty FROM billing_imei_data WHERE prod_code='".$podata_row['prod_code']."' AND doc_no='".$docid."' AND stock_type='MISSING'"));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0]." | ".$proddet[1]." | ".$proddet[2]?></td>
                <td style="text-align:right"><?=$podata_row['req_qty']?><input type="hidden" name="req_qty<?=$podata_row['id']?>" id="req_qty<?=$i?>" value="<?=$podata_row['req_qty']?>"></td>
                <td style="text-align:right"><?=$podata_row['po_price']?></td>
                <td style="text-align:right"><?=$podata_row['po_value']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['igst_per']?></td>
                <td style="text-align:right"><?=$podata_row['igst_amt']?></td>
                <td style="text-align:right"><input type="text" class="digits form-control required" style="width:50px;" name="ok_qty<?=$podata_row['id']?>" id="ok_qty<?=$i?>"  autocomplete="off" required onKeyUp="checkRecQty('<?=$i?>');" value="<?=round($podata_row['req_qty'])?>"></td>
                <td style="text-align:right"><input type="text" class="digits form-control" style="width:50px;" name="damage_qty<?=$podata_row['id']?>" id="damage_qty<?=$i?>"  autocomplete="off" required onKeyUp="checkRecQty('<?=$i?>');" value="0"></td>
                <td style="text-align:right"><input type="text" class="digits form-control" style="width:50px;" name="miss_qty<?=$podata_row['id']?>" id="miss_qty<?=$i?>"  autocomplete="off" value="" readonly></td>
                <td style="text-align:right"><?=$podata_row['totalval']?></td>
                </tr>
            <?php
			$i++;
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Amount Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td><label class="control-label">Delivery Address</label></td>
                <td><?php echo $po_row['delivery_address'];?></td>
                <td width="20%"><label class="control-label">Sub Total</label></td>
                <td width="30%"><?php echo $po_row['po_value'];?></td>                
              </tr>
              <tr>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['remark'];?></td> 
                <td><label class="control-label">Round Off</label></td>
                <td><?=$po_row['round_off'];?></td>                
              </tr>
               <tr>
                   <td><label class="control-label">Receive Remark <span style="color:#F00">*</span></label></td>
                 <td><textarea name="rcv_rmk" id="rcv_rmk" class="form-control required" style="resize:none;width:300px;" required></textarea></td>
                <td><label class="control-label">Grand Total</label></td>
                <td><?php echo currencyFormat($po_row['po_value']+$po_row['taxamount']);?><input type="hidden" name="grand_total" id="grand_total" value="<?=$po_row['po_value']+$po_row['taxamount']?>" readonly/></td> 
                 </tr>
               <tr>
                 <td colspan="4" align="center">
                    <input type="submit" class="btn <?=$btncolor?>" name="upd" id="upd" value="Receive" title="Receive This PO" <?php if ($_POST['upd']=='Receive'){ echo "disabled";}?>>&nbsp;
                    <input name="refno" id="refno" type="hidden" value="<?=base64_encode($po_row['po_no'])?>"/>
                   <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='localPurchaseList.php?<?=$pagenav?>'">
                 </td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
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