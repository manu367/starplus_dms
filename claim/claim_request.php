<?php
////// Function ID ///////
$fun_id = array("u"=>array(133)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

@extract($_POST);
////// if we hit process button
if($_POST){
	if($_POST['Submit']=='Save'){
		///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
		$messageIdent = md5($_POST['Submit'].$party_code);
		//and check it against the stored value:
		$sessionMessageIdent = isset($_SESSION['msgclaimreq'])?$_SESSION['msgclaimreq']:'';
		if($messageIdent!=$sessionMessageIdent){//if its different:
			//save the session var:
			$_SESSION['msgclaimreq'] = $messageIdent;
			##########  transcation parameter ########################33
			mysqli_autocommit($link1, false);
			$flag = true;
			$err_msg = "";
			
			//////// check mandatory fields
			if($party_code != "" && $claim_type != ""){
				$explode_party = explode("~",$party_code);
				////// select
				$sql1 ="SELECT MAX(tempid) AS qa FROM claim_master WHERE YEAR(entry_date) = '".date("Y")."'";
				$res1 = mysqli_query($link1,$sql1)or die("ER1 making ref no. ".mysqli_error($link1));
				$row1 = mysqli_fetch_array($res1);
				$cod1 = $row1['qa']+1;
				/// make 6 digit padding
				$pad1 = str_pad($cod1,6,"0",STR_PAD_LEFT);
				//// make logic of claim no.
				$claimno = "CL/".date("Ymd")."/".$pad1;
				///////
				$total_qty = 0;
				$total_amt = 0.00;
				///// claim data table update
				foreach($claim_subject as $j=>$value){
					if($claim_subject[$j]){
						$sql_data = "INSERT INTO claim_data SET claim_no='".$claimno."', claim_subject='".$claim_subject[$j]."', claim_desc='".$claim_desc[$j]."', claim_date='".$claim_date[$j]."' ,qty='".$claim_qty[$j]."', amount='".$claim_amt[$j]."'";
						$res_data = mysqli_query($link1,$sql_data);
					   //// check if query is not executed
						if (!$res_data) {
							 $flag = false;
							 $err_msg = "Error Code0.2: ".mysqli_error($link1);
						}
						$total_qty += $claim_qty[$j];
						$total_amt += $claim_amt[$j];
					}
				}
				////// entry in master table 
				$sql_master = "INSERT INTO claim_master SET claim_no='".$claimno."', tempid='".$cod1."', claim_type='".$claim_type."', plant_id='".$plant_code."', party_id='".$explode_party[0]."', entry_date='".$today."' ,entry_time='".$currtime."', entry_by='".$_SESSION['userid']."', entry_ip='".$ip."', total_qty='".$total_qty."', total_amount='".$total_amt."', status='Pending' ,remark=''";
				$res_master = mysqli_query($link1,$sql_master);
			   //// check if query is not executed
				if (!$res_master) {
					 $flag = false;
					 $err_msg = "Error Code0.1: ".mysqli_error($link1);
				}
				if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM billing_master WHERE from_location='" . $explode_party[0] . "' AND challan_no='" . $invoice_no . "'"))==0){
					/////// invoice details save
					$doctype =  "INVOICE";
					$invoicetype = "RETAIL INVOICE";
					///// get parent location details
					$parentloc = getLocationDetails($explode_party[0], "addrs,disp_addrs,name,city,state,gstin_no,pincode,email,phone", $link1);
					$parentlocdet = explode("~", $parentloc);
					///// get child location details
					$childloc = getLocationDetails($plant_code, "addrs,disp_addrs,name,city,state,gstin_no,pincode,email,phone", $link1);
					$childlocdet = explode("~", $childloc);
					/////////// insert data
					///// Insert in item data by picking each data row one by one
					$sub_total = 0.00;
					$total_discount = 0.00;
					$total_sgstamt = 0.00;
					$total_cgstamt = 0.00;
					$total_igstamt = 0.00;
					$grand_total = 0.00;
                	foreach ($prod_code as $k => $val) {
						if($bill_qty[$k]>0){
						$expld_part = explode("~",$val);
						$query2 = "insert into billing_model_data set from_location='" . $explode_party[0] . "', prod_code='" . $expld_part[0] . "',combo_code='".$hsn[$k]."',combo_name='".$claimno."', qty='" . $bill_qty[$k] . "', okqty='" . $bill_qty[$k] . "',mrp='" . $mrp[$k] . "', price='" . $price[$k] . "', hold_price='" . $price[$k] . "', value='" . $rowsubtotal[$k] . "',tax_name='" . $tax_per[1] . "',tax_per='" . $tax_per[0] . "', tax_amt='" . $rowtaxamount[$k] . "',discount='" . $rowdiscount[$k] . "', totalvalue='" . $total_val[$k] . "',challan_no='" . $invoice_no . "' ,sale_date='" . $invoicedate . "',entry_date='" . $today . "' ,sgst_per='".$rowsgstper[$k]."' ,sgst_amt='".$rowsgstamount[$k]."',igst_per='".$rowigstper[$k]."' ,igst_amt='".$rowigstamount[$k]."',cgst_per='".$rowcgstper[$k]."' ,cgst_amt='".$rowcgstamount[$k]."'";
						$result = mysqli_query($link1, $query2);
						//// check if query is not executed
						if (!$result) {
							$flag = false;
							$err_msg = "Error Code0.11: ".mysqli_error($link1);
						}
						$sub_total += $rowsubtotal[$k];
						$total_discount += $rowdiscount[$k];
						$total_sgstamt += $rowsgstamount[$k];
						$total_cgstamt += $rowcgstamount[$k];
						$total_igstamt += $rowigstamount[$k];
						$grand_total += $total_val[$k];
						}
					}
					///// Insert Master Data
					$query1 = "INSERT INTO billing_master set from_location='" . $explode_party[0] . "', to_location='" . $plant_code . "',sub_location='".$explode_party[0]."',from_gst_no='".$parentlocdet[5]."', from_partyname='".$parentlocdet[2]."', party_name='".$childlocdet[2]."', to_gst_no='".$childlocdet[5]."', challan_no='" . $invoice_no . "',ref_no='".$claimno."', sale_date='" . $invoicedate . "', entry_date='" . $today . "', entry_time='" . $currtime . "', entry_by='" . $_SESSION['userid'] . "', status='Pending', type='RETAIL', document_type='".$doctype."', discountfor='" . $disc_type . "', taxfor='" . $tx_type . "',basic_cost='" . $sub_total . "',discount_amt='" . $total_discount . "',total_sgst_amt='".$total_sgstamt."',total_cgst_amt='".$total_cgstamt."',total_igst_amt='".$total_igstamt."',tax_cost='" . $tax_amount . "',total_cost='" . $grand_total . "',tax_type='" . $splitcompltetax[1] . "',tax_header='" . $splitcompltetax[2] . "',tax='" . $splitcompltetax[0] . "',bill_from='" . $explode_party[0] . "',bill_topty='" . $plant_code . "',from_addrs='" . $parentlocdet[0] . "',disp_addrs='" . $parentlocdet[1] . "',to_addrs='" . $childlocdet[0] . "',deliv_addrs='" . $deli_addrs . "',billing_rmk='" . $remark . "',from_state='".$parentlocdet[4]."', to_state='".$childlocdet[4]."', from_city='".$parentlocdet[3]."', to_city='".$childlocdet[3]."', from_pincode='".$parentlocdet[6]."', to_pincode='".$childlocdet[6]."', from_phone='".$parentlocdet[8]."', to_phone='".$childlocdet[8]."', from_email='".$parentlocdet[7]."', to_email='".$childlocdet[7]."',round_off='".$round_off."',tcs_per='".$tcs_per."', tcs_amt='".$tcs_amt."',ship_to='".$shiptodet[0]."',ship_to_gstin='".$shiptodet[5]."',ship_to_city='".$shiptodet[2]."',ship_to_state='".$shiptodet[3]."',ship_to_pincode='".$shiptodet[4]."',ledger_name='".$ledgername."',sale_person='".$sales_executive."'";
					$result = mysqli_query($link1, $query1);
					//// check if query is not executed
					if (!$result) {
						$flag = false;
						$err_msg = "Error Code0.2: ".mysqli_error($link1);
					}
				}else{
					$flag = false;
                    $err_msg = "Error Code0.3:  Invoice is already claimed.";
				}
				///check directory
				$dirct = "../claim_doc/".date("Y-m");
				if (!is_dir($dirct)) {
					mkdir($dirct, 0777, 'R');
				}
				///// Insert in document attach detail by picking each data row one by one
				foreach($document_name as $k=>$val){
					////////////////upload file
					$filename = "fileupload".$k;
					$file_name = $_FILES[$filename]["name"];
					if($file_name){
						//$file_basename = substr($file_name, 0, strripos($file_name, '.')); // get file extention
						$file_ext = substr($file_name, strripos($file_name, '.')); // get file name
						//////upload image
						if ($_FILES[$filename]["error"] > 0){
							$code=$_FILES[$filename]["error"];
						}
						else{
							// Rename file
							$newfilename = str_replace("/","_",$claimno)."_".$today.$now.$file_ext;
							move_uploaded_file($_FILES[$filename]["tmp_name"],$dirct."/".$newfilename);
							$file = $dirct."/".$newfilename;
							//chmod ($file, 0755);
						}
						$sql_inst = "INSERT INTO document_attachment set ref_no='".$claimno."', ref_type='Claim Document',document_name='".ucwords($document_name[$k])."', document_path='".$file."', document_desc='".ucwords($document_desc[$k])."' , updatedate='".$datetime."'";
						$res_inst = mysqli_query($link1,$sql_inst);
						 //// check if query is not executed
						if (!$res_inst) {
							 $flag = false;
							 $err_msg = "Error Code0.11:".mysqli_error($link1);
						}
					}
				}
				$main_status = "Pending";
				///// App steps
				$app_steps = mysqli_fetch_assoc(mysqli_query($link1,"SELECT approval_steps FROM process_approval_step WHERE process_name='CLAIM' AND status='1'"));
				$arr_steps = explode(",",$app_steps['approval_steps']);
				///entry for each steps
				for($j=0; $j<count($arr_steps); $j++){
					/////
					if($j==0){ 
					   	///// get status
						$main_status = getAnyDetails($arr_steps[$j],"process_name","process_id","approval_step_master",$link1)." ".$main_status;
						$currstatus = "Pending";}else{ $currstatus = "";}
					$result5 = mysqli_query($link1,"INSERT INTO approval_status_matrix SET ref_no ='".$claimno."', process_name = 'CLAIM', process_id='".$arr_steps[$j]."', current_status='".$currstatus."'");
					if (!$result5) {
						$flag = false;
						$err_msg = "Error Code5: ".mysqli_error($link1);
					}
				}
				////// update main status in master table
				$main_master = mysqli_query($link1,"UPDATE claim_master SET status='".$main_status."' WHERE claim_no ='".$claimno."'");
				if (!$main_master) {
					$flag = false;
					$err_msg = "Error Code6: ".mysqli_error($link1);
				}
				////// insert in activity table////
				$flag=dailyActivity($_SESSION['userid'],$claimno,"CLAIM","ADD",$ip,$link1,$flag);
			}
			else {
			   $flag = false;
			   $err_msg = "Mandatory field was missing";
			}
			//// check both master and data query are successfully executed
			if ($flag) {
				mysqli_commit($link1);
				$msg = "Claim is successfully created with ref. no.".$claimno;
				$cflag = "success";
            	$cmsg = "Success";
				/////// send email
				$useremail = explode("~",getAnyDetails($explode_party[0],"name,email","asc_code","asc_master",$link1));
				$usercc = mysqli_fetch_assoc(mysqli_query($link1,"SELECT GROUP_CONCAT(emailid) AS ccemail FROM admin_users WHERE utype='5' AND status='Active'"));
				if($useremail){
					require_once("claim_email_notification.php");
					$email_to = $useremail[1];
					$email_cc = $usercc;
					$email_bcc = "shekhar@candoursoft.com";
					$email_subject = $claim_type." Claim Generated with ref. no. ".$claimno;
					$email_title = "Claim Generation";
					$email_from = "";
					$emailmsg = "You have created a <b>".$claim_type."</b> claim with reference no. <b>".$claimno."</b> of amount <b>".$total_amt."</b>";
					$url = $root."/email_template.php?msg=".urlencode($emailmsg)."&uname=".urlencode($useremail[0])."&title=".urlencode($email_title);
					$message = file_get_contents($url);
					/////////////////////////////////mail function////////////////////
					//$resp = send_mail_fun($message,$email_subject,$email_to,$email_cc,$email_bcc,$emailfrom);	
				}
			} else {
				mysqli_rollback($link1);
				$msg = "Request could not be processed. Please try again.".$err_msg;
				$cflag = "danger";
            	$cmsg = "Failed";
			} 
			mysqli_close($link1);
			///// move to parent page
			header("location:claim_list.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
			exit;
		}else{
			//you've sent this already!
			$msg = "Re-submission was detected.";
			$cflag = "warning";
			$cmsg = "Warning";
			///// move to parent page
			header("location:claim_list.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
			exit;
		}
	}	
}
///get access product
$acc_psc = getAccessProduct($_SESSION['userid'],$link1);
///get access brand
$acc_brd = getAccessBrand($_SESSION['userid'],$link1);
//echo $model_query = "select productcode, productname,model_name from product_master where status='Active' AND productsubcat IN (".$acc_psc.") AND brand IN (".$acc_brd.")";
$party_info = explode("~",$_REQUEST['party_code']);
$toloctiondet = explode("~", getLocationDetails($_REQUEST['plant_code'], "state,id_type", $link1));
$fromlocationdet  = explode("~", getLocationDetails($party_info[0], "state,id_type,name,city", $link1));
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= siteTitle ?></title>
<script src="../js/jquery.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="../css/bootstrap-select.min.css">
<script src="../js/bootstrap-select.min.js"></script>
<script>
$(document).ready(function(){
	var spinner = $('#loader');
    $("#form1").validate({
		submitHandler: function (form) {
			if (!this.wasSent) {
				this.wasSent = true;
				$(':submit', form).val('Please wait...')
						.attr('disabled', 'disabled')
						.addClass('disabled');
				spinner.show();
				form.submit();
			} else {
				return false;
			}
		}
	});
	$('#claim_date0').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?=$today?>",
		todayHighlight: true,
		autoclose: true
	});
	$('#invoicedate').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?//=$po_row['entry_date']?>",
		endDate: "<?=$today?>",
		todayHighlight: true,
		autoclose: true
	});
});
function makeClaimDate(ind){
	$('#claim_date'+ind).datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?=$today?>",
		todayHighlight: true,
		autoclose: true,
	});
}
</script>
<script language="javascript" type="text/javascript"> 
function HandleBrowseClick(ind){
    var fileinput = document.getElementById("browse"+ind);
    fileinput.click();
}
function Handlechange(ind){
	var fileinput = document.getElementById("browse"+ind);
	var textinput = document.getElementById("filename"+ind);
	textinput.value = fileinput.value;
}
///// add new row for claim summary
$(document).ready(function() {
	$("#add_row2").click(function() {		
		var numi = document.getElementById('rowno2');
		var itm = "claim_subject[" + numi.value+"]";
		var preno=document.getElementById('rowno2').value;
		var num = (document.getElementById("rowno2").value -1)+2;
		numi.value = num;
		if ((document.getElementById(itm).value != "") || ($("#addr_claim" + numi.value + ":visible").length == 0)) {
			var r = '<tr id="addr_claim'+num+'"><td><i class="fa fa-close fa-lg" onClick="fun_remove2('+num+');"></i><input type="text" class="form-control entername cp required" required name="claim_subject['+num+']" id="claim_subject['+num+']" value=""></td><td><textarea class="form-control addressfield cp required" required name="claim_desc['+num+']" id="claim_desc['+num+']" style="resize:vertical"></textarea></td><td><input type="text" class="form-control required" required name="claim_date['+num+']" id="claim_date'+num+'" value="<?=$today?>"></td><td><input type="text" class="form-control required digits" required name="claim_qty['+num+']" id="claim_qty['+num+']" value="1"></td><td><input type="text" class="form-control required number" required name="claim_amt['+num+']" id="claim_amt['+num+']" value=""></td></tr>';
			$('#itemsTable2').append(r);
			makeClaimDate(num);
		}
	});
});
function makeSelect(){
	$('.selectpicker').selectpicker({
		liveSearch: true,
		showSubtext: true
	});
}
function fun_remove2(con){
	var c = document.getElementById('addr_claim' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('rowno2').value = con;
}
///// add new row for Invoice summary
$(document).ready(function() {
	$("#add_row21").click(function() {		
		var numi = document.getElementById('rowno21');
		var itm = "prod_code[" + numi.value+"]";
		var preno=document.getElementById('rowno21').value;
		var num = (document.getElementById("rowno21").value -1)+2;
		numi.value = num;
		if ((document.getElementById(itm).value != "") || ($("#addr_inv" + numi.value + ":visible").length == 0)) {
			var r = '<tr id="addr_inv'+num+'"><td><i class="fa fa-close fa-lg" onClick="fun_remove21('+num+');"></i><select name="prod_code['+num+']" id="prod_code['+num+']"  class="form-control selectpicker" data-live-search="true" style="width:150px;padding-right:100px;" onchange="get_price('+num+');"><option value="">--None--</option><?php $model_query = "select productcode, productname,model_name,hsn_code from product_master where status='Active' AND productsubcat IN (".$acc_psc.") AND brand IN (".$acc_brd.")";$check1 = mysqli_query($link1, $model_query); while ($br = mysqli_fetch_array($check1)){?><option value="<?php echo $br['productcode']."~".$br['hsn_code']; ?>"><?php echo $br['productname']." | ".$br['productcode']." | ".$br['model_name']; ?></option><?php } ?></select></td><td><input type="text" class="form-control" name="hsn['+num+']" id="hsn['+num+']" autocomplete="off" value="" style="text-align:left" readonly></td><td><input type="text" class="form-control digits" name="bill_qty['+num+']" id="bill_qty['+num+']" onkeyup="rowTotal(' + num + ');" autocomplete="off" style="text-align:right"></td><td><input type="text" class="form-control number" name="price['+num+']" id="price['+num+']" onkeyup="rowTotal(' + num + ');" autocomplete="off" style="text-align:right"></td><td><input type="text" class="form-control number" name="rowdiscount['+num+']" id="rowdiscount['+num+']" onkeyup="rowTotal(' + num + ');" autocomplete="off" style="text-align:right"></td><td><input type="text" class="form-control" name="rowsubtotal['+num+']" id="rowsubtotal['+num+']" autocomplete="off" value="" style="text-align:right" readonly></td><td><?php if($fromlocationdet[0]==$toloctiondet[0]){ ?><div class="row"><div class="col-md-4"><input type="text" class="form-control" name="rowsgstper['+num+']" id="rowsgstper['+num+']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div class="col-md-4"><input type="text" class="form-control" name="rowsgstamount['+num+']" id="rowsgstamount['+num+']" value="0" readonly style="width:80px;text-align:right;padding: 4px"></div></div><div class="row"><div class="col-md-4"><input type="text" class="form-control" name="rowcgstper['+num+']" id="rowcgstper['+num+']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div class="col-md-4"><input type="text" class="form-control" name="rowcgstamount['+num+']" id="rowcgstamount['+num+']" value="0" readonly style="width:80px;text-align:right;padding: 4px"></div></div><?php }else{?><div class="row"><div class="col-md-4"><input type="text" class="form-control" name="rowigstper['+num+']" id="rowigstper['+num+']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div class="col-md-4"><input type="text" class="form-control" name="rowigstamount['+num+']" id="rowigstamount['+num+']" value="0" readonly style="width:60px;text-align:right;padding: 4px"></div></div><?php }?></td><td><input type="text" class="form-control" name="total_val['+num+']" id="total_val['+num+']" autocomplete="off" readonly  style="text-align:right"></td></tr>';
			$('#itemsTable21').append(r);
			makeSelect();
		}
	});
});
function fun_remove21(con){
	var c = document.getElementById('addr_inv' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('rowno21').value = con;
	calculatetotal()
}
///// add new row for document attachment
$(document).ready(function() {
	$("#add_row3").click(function() {		
		var numi = document.getElementById('rowno3');
		var itm = "document_name[" + numi.value+"]";
		var preno=document.getElementById('rowno3').value;
		var num = (document.getElementById("rowno3").value -1)+2;
		numi.value = num;
		if ((document.getElementById(itm).value != "") || ($("#addr_doc" + numi.value + ":visible").length == 0)) {
			var r = '<tr id="addr_doc'+num+'"><td><i class="fa fa-close fa-lg" onClick="fun_remove3('+num+');"></i><input type="text" class="form-control entername cp" name="document_name['+num+']" id="document_name['+num+']" value=""></td><td><input type="text" class="form-control entername  cp" name="document_desc['+num+']"  id="document_desc['+num+']" value=""></td><td><div style="display:inline-block; float:left"><input type="file" id="browse'+num+'" name="fileupload'+num+'" style="display: none" onChange="Handlechange('+num+');" accept="image/*"/><input type="text" id="filename'+num+'" readonly="true" style="width:300px;" class="form-control"/></div><div style="display:inline-block; float:left">&nbsp;&nbsp;<input type="button" value="Click to upload attachment" id="fakeBrowse'+num+'" onclick="HandleBrowseClick('+num+');" class="btn btn-warning"/></div></td></tr>';
			$('#itemsTable3').append(r);
		}
	});
});
function fun_remove3(con){
	var c = document.getElementById('addr_doc' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('rowno3').value = con;
}
function confirmDel(store){
	var where_to= confirm("Are you sure to delete this document?");
	if (where_to== true)
	{
		//alert(window.location.href)
		var url="<?php echo $url ?>";
		window.location=url+store;
	}
	else
	{
		return false;
	}
}
function get_price(ind) {
	var productCode2 = document.getElementById("prod_code[" + ind + "]").value;
	var pc = productCode2.split("~");
	var productCode = pc[0];
	var hsncode = pc[1];
	document.getElementById("hsn[" + ind + "]").value = hsncode;
	var billingfrom = $('#po_from').val();		
	var billingto  =  $("#po_to").val();		
	var tolocation = document.getElementById("toloctionstate").value;
	var fromlocation = document.getElementById("fromloctionstate").value;   
	var fromidtype = document.getElementById("fromidtype").value;
	var toidtype = document.getElementById("toidtype").value;  
	$.ajax({
		type: 'post',
		url: '../includes/getAzaxFields.php',
		data: {productinfo: productCode, idtype: toidtype, fromstate:tolocation},
		success: function(data) {
			var splitprice = data.split("~");
			document.getElementById("price[" + ind + "]").value = splitprice[3];
			
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
/////// calculate line total /////////////
function rowTotal(ind) {
	//get_price(ind);
	var ent_qty = "bill_qty" + "[" + ind + "]";
	var ent_rate = "price" + "[" + ind + "]";
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
	var total = parseFloat(qty) * parseFloat(price);				
	if (parseFloat(total) >= parseFloat(dicountval)) {
		
		var totalcost = parseFloat(total) - parseFloat(dicountval);
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
		calculatetotal();
	}
}
////// calculate final value of form /////
function calculatetotal() {
	var rowno1 = (document.getElementById("rowno21").value);
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
		sum_tax += parseFloat(total_tax);//// total taxable amt
		<?php if($fromlocationdet[0]==$toloctiondet[0]){ ?>
		sum_sgst += parseFloat(total_sgst);
		sum_cgst += parseFloat(total_cgst);
		<?php }else{?>
		sum_igst += parseFloat(total_igst);
		<?php }?>
		
		//sum += parseFloat(total);

	}/// close for loop
	document.getElementById("totqty").value = sum_qty;
	document.getElementById("tottaxable").value = sum_tax;
	document.getElementById("totgst").value = sum_sgst+sum_cgst+sum_igst;
	document.getElementById("totamt").value = sum_total;
/*	document.getElementById("total_qty").value = sum_qty;
	document.getElementById("sub_total").value = formatCurrency(sum_tax);//// total taxable amt

	document.getElementById("total_discount").value = formatCurrency(sum_discount);

	 document.getElementById("tax_amount").value = formatCurrency(sum_sgst+sum_cgst+sum_igst);
	 <?php if($fromlocationdet[0]==$toloctiondet[0]){ ?>
	 document.getElementById("total_sgstamt").value = formatCurrency(sum_sgst);
	 document.getElementById("total_cgstamt").value = formatCurrency(sum_cgst);
	 <?php }else{?>
	 document.getElementById("total_igstamt").value = formatCurrency(sum_igst);
	 <?php }?>

	document.getElementById("grand_total").value = formatCurrency(parseFloat(sum_total));*/
}
/// Currency Format//////////// 
function formatCurrency(num) {
   num = num.toString().replace(/\$|\,/g,'');
   if(isNaN(num))
    num = "0";
    signt = (num == (num = Math.abs (num)));
	num = Math.floor(num*100+0.50000000001);
	cents = num%100;
	num = Math.floor(num/100).toString();
   if(cents<10)
	cents = "0" + cents;
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++) 
	num = num.substring(0,num.length-(4*i+3))+''+
	num.substring(num.length-(4*i+3));
	return (((signt)?'':'-') + '' + num + '.' + cents);
}
</script>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<link href="../css/loader.css" rel="stylesheet"/>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      		<h2 align="center"><i class="fa fa-clipboard"></i> Add New Claim</h2><br/>
      			<div class="form-group"  id="page-wrap" style="margin-left:10px;">
          			<form  name="form1" class="form-horizontal" action="" method="post" id="form1" enctype="multipart/form-data">
                    <div class="panel-group">
    				<div class="panel panel-info">
        				<div class="panel-heading">Party Information</div>
         				<div class="panel-body">
        				<div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Party Name <span class="red_small">*</span></label>
              					<div class="col-md-7">
               						<select name="party_code" id="party_code" required class="form-control selectpicker required" data-live-search="true" onChange="document.form1.submit()">
                                    	<option value="" selected="selected">Please Select </option>
										<?php
                                        $sql_parent = "select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y'";
                                        $res_parent = mysqli_query($link1, $sql_parent);
                                        while ($result_parent = mysqli_fetch_array($res_parent)) {   
                                            $party_det = mysqli_fetch_array(mysqli_query($link1, "select name, city, state, id_type, gstin_no from asc_master where asc_code='" . $result_parent['location_id'] . "'"));
											if($party_det['name']){
                                        ?>
                                        <option value="<?=$result_parent['location_id']."~".$party_det['gstin_no']?>" <?php if ($result_parent['location_id']."~".$party_det['gstin_no'] == $_REQUEST['party_code']) echo "selected"; ?> ><?= $party_det['name'] . " | " . $party_det['city'] . " | " . $party_det['state'] . " | " . $result_parent['location_id']?></option>
                                     	<?php
											}
                                        }
                                        ?>
                            		</select>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Claim Type <span class="red_small">*</span></label>
              					<div class="col-md-7">
          							<select name="claim_type" id="claim_type" required class="form-control selectpicker required" data-live-search="true" onChange="document.form1.submit()">
                                    	<option value="" selected="selected">Please Select </option>
										<?php
                                        $sql_claim = "select id,claim_type from claim_type_master where status='1'";
                                        $res_claim = mysqli_query($link1, $sql_claim);
                                        while ($row_claim = mysqli_fetch_array($res_claim)) {   
                                        ?>
                                        <option value="<?= $row_claim['claim_type']?>" <?php if ($row_claim['claim_type'] == $_REQUEST['claim_type']) echo "selected"; ?> ><?= $row_claim['claim_type']?></option>
                                     	<?php
                                        }
                                        ?>
                            		</select>
      							</div>
            				</div>
          				</div>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Plant Code <span class="red_small">*</span></label>
              					<div class="col-md-7">
               						<select name="plant_code" id="plant_code" class="form-control selectpicker" data-live-search="true" onChange="document.form1.submit()">
                                    	<option value="" selected="selected">Please Select </option>
										<?php
                                        $sql_parent = "SELECT name, asc_code, city, state FROM asc_master WHERE id_type IN ('HO','BRANCH') AND status='Active'";
                                        $res_parent = mysqli_query($link1, $sql_parent);
                                        while ($row_parent = mysqli_fetch_array($res_parent)) {   
                                        ?>
                                        <option value="<?= $row_parent['asc_code']?>" <?php if ($row_parent['asc_code'] == $_REQUEST['plant_code']) echo "selected"; ?> ><?= $row_parent['name'] . " | " . $row_parent['city'] . " | " . $row_parent['state'] . " | " . $row_parent['asc_code']?></option>
                                     	<?php
                                        }
                                        ?>
                            		</select>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">&nbsp;</label>
              					<div class="col-md-7">
          							
      							</div>
            				</div>
          				</div>
                        <?php
						
						$res_clm_bgt = mysqli_query($link1,"SELECT budget_year, budget_yearly FROM claim_budget WHERE party_id='".$party_info[0]."' AND  claim_type='".$_REQUEST['claim_type']."' AND status='1'");
						while($row_clm_bgt = mysqli_fetch_assoc($res_clm_bgt)){
						?>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Budget Year</label>
              					<div class="col-md-7">
               						<input type="text" class="form-control" name="claim_bgt_year" id="claim_bgt_year" readonly value="<?=$row_clm_bgt['budget_year']?>">
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Claim Budget</label>
              					<div class="col-md-7">
          							<input type="text" class="form-control number" name="claim_bgt" id="claim_bgt" readonly value="<?=$row_clm_bgt['budget_yearly']?>">
      							</div>
            				</div>
          				</div>
                        <?php }?>
          				</div>
                        </div>
                        <div class="panel panel-info">
        				<div class="panel-heading">Claim Summary</div>
         				<div class="panel-body">
		    
			 			<div class="form-group">
                			<div class="col-sm-12">
                				<table class="table table-bordered" width="100%" id="itemsTable2">
                    				<thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th width="20%">Subject</th>
                                            <th width="25%">Description</th>
                                            <th width="15%">Date</th>
                                            <th width="20%">Nos.</th>
                                            <th width="20%">Amount</th>
                                        </tr>
                    				</thead>
                    				<tbody>
                        				<tr id="addr_claim0">
                                            <td><input type="text" class="form-control entername cp required" required name="claim_subject[0]" id="claim_subject[0]" value=""></td>
                                            <td><textarea class="form-control addressfield cp required" required name="claim_desc[0]" id="claim_desc[0]" style="resize:vertical"></textarea></td>
                                            <td><input type="text" class="form-control required" required name="claim_date[0]" id="claim_date0" value="<?=$today?>"></td>
                                            <td><input type="text" class="form-control required digits" required name="claim_qty[0]" id="claim_qty[0]" value="1"></td>
                                            <td><input type="text" class="form-control required number" required name="claim_amt[0]" id="claim_amt[0]" value=""></td>
                        				</tr>
                    				</tbody>
                				</table>   
                			</div>
                		</div>
                		<div class="form-group">
           					<div class="col-sm-4" style="display:inline-block; float:left">
           						<a id="add_row2" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add More Line</a>
                                <input type="hidden" name="rowno2" id="rowno2" value="0"/>
                          	</div>
          				</div>
                        
                        </div>
                        </div>
                        
                        <div class="panel panel-info">
        				<div class="panel-heading">Invoice Summary</div>
         				<div class="panel-body">
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Party Name</label>
              					<div class="col-md-7">
               						<?=$fromlocationdet[2].", ".$fromlocationdet[3].", ".$fromlocationdet[0];?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Party GSTIN</label>
              					<div class="col-md-7">
          							<input type="text" class="form-control alphanumeric" name="party_gstin" id="party_gstin" autocomplete="off" readonly value="<?=$party_info[1]?>"/>
      							</div>
            				</div>
          				</div>
		    			<div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Invoice No.</label>
              					<div class="col-md-7">
               						<input type="text" name="invoice_no" id="invoice_no" value="" class="form-control mastername" autocomplete="off"/>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Invoice Date</label>
              					<div class="col-md-7">
          							<input type="text" class="form-control span2" name="invoicedate" id="invoicedate" autocomplete="off"/>
      							</div>
            				</div>
          				</div>
			 			<div class="form-group">
                			<div class="col-sm-12">
                				<table class="table table-bordered" width="100%" id="itemsTable21">
                    				<thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th width="25%">Product</th>
                                            <th width="8%">HSN</th>
                                            <th width="8%">Qty</th>
                                            <th width="10%">Price</th>
                                            <th width="10%">Discount</th>
                                            <th width="12%">Taxable Val</th>
                                            <th width="15%">GST</th>
                                            <th width="12%">Total</th>
                                        </tr>
                    				</thead>
                    				<tbody>
                        				<tr id="addr_inv0">
                                            <td><select name="prod_code[0]" id="prod_code[0]"  class="form-control selectpicker" data-live-search="true" style="width:150px;padding-right:100px;" onChange="get_price(0);">
                                                    <option value="">--None--</option>
                                                    <?php
                                                    $model_query = "select productcode, productname,model_name,hsn_code from product_master where status='Active' AND productsubcat IN (".$acc_psc.") AND brand IN (".$acc_brd.")";
                                                    $check1 = mysqli_query($link1, $model_query);
                                                    while ($br = mysqli_fetch_array($check1)) {
                                                        ?>
                                                        <option value="<?php echo $br['productcode']."~".$br['hsn_code']; ?>"><?php echo $br['productname']." | ".$br['productcode']." | ".$br['model_name']; ?></option>
                                                    <?php } ?>
                                                </select></td>
                                            <td><input type="text" class="form-control" name="hsn[0]" id="hsn[0]" autocomplete="off" value="" style="text-align:left" readonly></td>
                                            <td><input type="text" class="form-control digits" name="bill_qty[0]" id="bill_qty[0]" onKeyUp="rowTotal(0);" autocomplete="off" style="text-align:right"></td>
                                            <td><input type="text" class="form-control number" name="price[0]" id="price[0]" onKeyUp="rowTotal(0);" autocomplete="off" style="text-align:right"></td>
                                            <td><input type="text" class="form-control number" name="rowdiscount[0]" id="rowdiscount[0]" onKeyUp="rowTotal(0);" autocomplete="off" style="text-align:right"></td>
                                            <td><input type="text" class="form-control" name="rowsubtotal[0]" id="rowsubtotal[0]" autocomplete="off" value="" style="text-align:right" readonly></td>
                                            <td><?php if($fromlocationdet[0]==$toloctiondet[0]){ ?>
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
                                                <?php }?></td>
                                            <td><input type="text" class="form-control" name="total_val[0]" id="total_val[0]" autocomplete="off" readonly  style="text-align:right"></td>
                        				</tr>
                    				</tbody>
                				</table>   
               			  </div>
                		</div>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Total Qty</label>
              					<div class="col-md-7">
               						<input type="text" name="totqty" id="totqty" value="" class="form-control digits" autocomplete="off" readonly/>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Taxable Amount</label>
              					<div class="col-md-7">
          							<input type="text" name="tottaxable" id="tottaxable" value="" class="form-control number" autocomplete="off" readonly/>
      							</div>
            				</div>
          				</div>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Total GST</label>
              					<div class="col-md-7">
               						<input type="text" name="totgst" id="totgst" value="" class="form-control number" autocomplete="off" readonly/>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Total Amount</label>
              					<div class="col-md-7">
          							<input type="text" name="totamt" id="totamt" value="" class="form-control number" autocomplete="off" readonly/>
      							</div>
            				</div>
          				</div>
                		<div class="form-group">
           					<div class="col-sm-4" style="display:inline-block; float:left">
           						<a id="add_row21" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add More Line</a>
                                <input type="hidden" name="rowno21" id="rowno21" value="0"/>
                                <input type="hidden" name="toloctionstate" id="toloctionstate" value="<?= $toloctiondet[0] ?>"/>
									<input type="hidden" name="fromloctionstate" id="fromloctionstate" value="<?= $fromlocationdet[0] ?>"/>
									<input type="hidden" name="fromidtype" id="fromidtype" value="<?= $fromlocationdet[1] ?>"/>
                                    <input type="hidden" name="toidtype" id="toidtype" value="<?= $toloctiondet[1] ?>"/>
                          	</div>
          				</div>
                        
                        </div>
                        </div>
                        
                   		<div class="panel panel-info table-responsive">
        				<div class="panel-heading">Supporting Document</div>
         				<div class="panel-body">
		    
			 			<div class="form-group">
                			<div class="col-sm-12">
                				<table class="table table-bordered" width="100%" id="itemsTable3">
                    				<thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th width="25%">Document Name</th>
                                            <th width="20%">Description</th>
                                            <th width="50%">Attachment</th>
                                        </tr>
                    				</thead>
                    				<tbody>
                        				<tr id="addr_doc0">
                                            <td><input type="text" class="form-control entername cp" name="document_name[0]"  id="document_name[0]" value=""></td>
                                            <td><input type="text" class="form-control entername cp" name="document_desc[0]"  id="document_desc[0]" value=""></td>
                                            <td>
                                                <div style="display:inline-block; float:left">
                                                <input type="file" class="required" id="browse0" name="fileupload0" style="display: none" onChange="Handlechange(0);" accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf"/>
                                                <input type="text" id="filename0" readonly style="width:300px;" class="form-control "/>
                                                </div><div style="display:inline-block; float:left">&nbsp;&nbsp;
                                                <input type="button" value="Click to upload attachment" id="fakeBrowse0" onClick="HandleBrowseClick(0);" class="btn btn-warning"/>
                                                </div>
                            				</td>
                        				</tr>
                    				</tbody>
                				</table>   
                			</div>
                		</div>
                		<div class="form-group">
           					<div class="col-sm-4" style="display:inline-block; float:left">
           						<a id="add_row3" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add More Attachment</a>
                                <input type="hidden" name="rowno3" id="rowno3" value="0"/>
                          	</div>
            				<div class="col-md-8" style="display:inline-block; float:right" align="left">
                                <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Save" title="" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
                                <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='claim_list.php?<?=$pagenav?>'">
            				</div>
          				</div>
                        
                        </div>
                        </div>
                        </div>
    				</form>
      			</div>
    		</div>
  		</div>
	</div>
<div id="loader"></div>     
<?php
include("../includes/footer.php");
?>
</body>
</html>

<script type="text/javascript">
	///// function for checking duplicate Product value
	function checkDuplicate(fldIndx1, enteredsno) { 
	 document.getElementById("save").disabled = false;
		if (enteredsno != '') {
			var check2 = "document_name[" + fldIndx1 + "]";
			var flag = 1;
			for (var i = 0; i <= fldIndx1; i++) {
				var check1 = "document_name[" + i + "]";
				if (fldIndx1 != i && (document.getElementById(check2).value == document.getElementById(check1).value )){
					if ((document.getElementById(check2).value == document.getElementById(check1).value)) {
						alert("Duplicate Document Selection.");
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