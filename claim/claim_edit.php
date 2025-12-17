<?php
////// Function ID ///////
$fun_id = array("u"=>array(133),"a"=>array(105));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

$docid=base64_decode($_REQUEST['id']);
$pgnm=base64_decode($_REQUEST['pgnm']);
if($pgnm){ $backlink = $pgnm;}else{$backlink = "claim_list";}

$sql_master = "SELECT * FROM claim_master WHERE claim_no='".$docid."'";
$res_master = mysqli_query($link1,$sql_master);
$row_master = mysqli_fetch_assoc($res_master);

@extract($_POST);
////// if we hit process button
if($_POST){
	if($_POST['Submit']=='Update'){
		///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
		$messageIdent = md5($_POST['Submit'].$party_code);
		//and check it against the stored value:
		$sessionMessageIdent = isset($_SESSION['msgclaimedit'])?$_SESSION['msgclaimedit']:'';
		if($messageIdent!=$sessionMessageIdent){//if its different:
			//save the session var:
			$_SESSION['msgclaimedit'] = $messageIdent;
			##########  transcation parameter ########################33
			mysqli_autocommit($link1, false);
			$flag = true;
			$err_msg = "";
			//////// check mandatory fields
			if($docid){
				$explode_party = explode("~",base64_decode($party_code));
				////// delete all data items
				$sql_data2 = "DELETE FROM claim_data WHERE claim_no='".$row_master["claim_no"]."'";
				$res_data2 = mysqli_query($link1,$sql_data2);
			   //// check if query is not executed
				if (!$res_data2) {
					 $flag = false;
					 $err_msg = "Error Code0.1: ".mysqli_error($link1);
				}
				///////
				$total_qty = 0;
				$total_amt = 0.00;
				///// claim data table update
				foreach($claim_subject as $j=>$value){
					if($claim_subject[$j]){
						$sql_data = "INSERT INTO claim_data SET claim_no='".$row_master["claim_no"]."', claim_subject='".$claim_subject[$j]."', claim_desc='".$claim_desc[$j]."', claim_date='".$claim_date[$j]."' ,qty='".$claim_qty[$j]."', amount='".$claim_amt[$j]."'";
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
							$newfilename = str_replace("/","_",$row_master["claim_no"])."_".$today.$now.$file_ext;
							move_uploaded_file($_FILES[$filename]["tmp_name"],$dirct."/".$newfilename);
							$file = $dirct."/".$newfilename;
							//chmod ($file, 0755);
						}
						$sql_inst = "INSERT INTO document_attachment set ref_no='".$row_master["claim_no"]."', ref_type='Claim Document',document_name='".ucwords($document_name[$k])."', document_path='".$file."', document_desc='".ucwords($document_desc[$k])."' , updatedate='".$datetime."'";
						$res_inst = mysqli_query($link1,$sql_inst);
						 //// check if query is not executed
						if (!$res_inst) {
							 $flag = false;
							 $err_msg = "Error Code0.11: ".mysqli_error($link1);
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
					}else{ 
				
					}
				}
				////// update main status in master table
				$main_master = mysqli_query($link1,"UPDATE claim_master SET update_date='".$today."' ,update_time='".$currtime."', update_by='".$_SESSION['userid']."', update_ip='".$ip."', total_qty='".$total_qty."', total_amount='".$total_amt."', status='".$main_status."' WHERE claim_no ='".$row_master["claim_no"]."'");
				if (!$main_master) {
					$flag = false;
					$err_msg = "Error Code6: ".mysqli_error($link1);
				}
				///// update invoice details
				if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM billing_master WHERE from_location='" . $explode_party[0] . "' AND challan_no='" . $invoice_no . "'"))==0 || base64_decode($refinv)==$invoice_no){
					////// delete all master data
					$sql_invm = "DELETE FROM billing_master WHERE ref_no='".$row_master["claim_no"]."'";
					$res_invm = mysqli_query($link1,$sql_invm);
				   //// check if query is not executed
					if (!$res_invm) {
						 $flag = false;
						 $err_msg = "Error Code Inv.M: ".mysqli_error($link1);
					}
					$sql_invd = "DELETE FROM billing_model_data WHERE challan_no='".$invoice_no."' AND combo_name='".$row_master["claim_no"]."'";
					$res_invd = mysqli_query($link1,$sql_invd);
				   //// check if query is not executed
					if (!$res_invd) {
						 $flag = false;
						 $err_msg = "Error Code Inv.D: ".mysqli_error($link1);
					}
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
							$query2 = "insert into billing_model_data set from_location='" . $explode_party[0] . "', prod_code='" . $expld_part[0] . "',combo_code='".$hsn[$k]."',combo_name='".$row_master["claim_no"]."', qty='" . $bill_qty[$k] . "', okqty='" . $bill_qty[$k] . "',mrp='" . $mrp[$k] . "', price='" . $price[$k] . "', hold_price='" . $price[$k] . "', value='" . $rowsubtotal[$k] . "',tax_name='" . $tax_per[1] . "',tax_per='" . $tax_per[0] . "', tax_amt='" . $rowtaxamount[$k] . "',discount='" . $rowdiscount[$k] . "', totalvalue='" . $total_val[$k] . "',challan_no='" . $invoice_no . "' ,sale_date='" . $invoicedate . "',entry_date='" . $today . "' ,sgst_per='".$rowsgstper[$k]."' ,sgst_amt='".$rowsgstamount[$k]."',igst_per='".$rowigstper[$k]."' ,igst_amt='".$rowigstamount[$k]."',cgst_per='".$rowcgstper[$k]."' ,cgst_amt='".$rowcgstamount[$k]."'";
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
					$query1 = "INSERT INTO billing_master set from_location='" . $explode_party[0] . "', to_location='" . $plant_code . "',sub_location='".$explode_party[0]."',from_gst_no='".$parentlocdet[5]."', from_partyname='".$parentlocdet[2]."', party_name='".$childlocdet[2]."', to_gst_no='".$childlocdet[5]."', challan_no='" . $invoice_no . "',ref_no='".$row_master["claim_no"]."', sale_date='" . $invoicedate . "', entry_date='" . $today . "', entry_time='" . $currtime . "', entry_by='" . $_SESSION['userid'] . "', status='Pending', type='RETAIL', document_type='".$doctype."', discountfor='" . $disc_type . "', taxfor='" . $tx_type . "',basic_cost='" . $sub_total . "',discount_amt='" . $total_discount . "',total_sgst_amt='".$total_sgstamt."',total_cgst_amt='".$total_cgstamt."',total_igst_amt='".$total_igstamt."',tax_cost='" . $tax_amount . "',total_cost='" . $grand_total . "',tax_type='" . $splitcompltetax[1] . "',tax_header='" . $splitcompltetax[2] . "',tax='" . $splitcompltetax[0] . "',bill_from='" . $explode_party[0] . "',bill_topty='" . $plant_code . "',from_addrs='" . $parentlocdet[0] . "',disp_addrs='" . $parentlocdet[1] . "',to_addrs='" . $childlocdet[0] . "',deliv_addrs='" . $deli_addrs . "',billing_rmk='" . $remark . "',from_state='".$parentlocdet[4]."', to_state='".$childlocdet[4]."', from_city='".$parentlocdet[3]."', to_city='".$childlocdet[3]."', from_pincode='".$parentlocdet[6]."', to_pincode='".$childlocdet[6]."', from_phone='".$parentlocdet[8]."', to_phone='".$childlocdet[8]."', from_email='".$parentlocdet[7]."', to_email='".$childlocdet[7]."',round_off='".$round_off."',tcs_per='".$tcs_per."', tcs_amt='".$tcs_amt."',ship_to='".$shiptodet[0]."',ship_to_gstin='".$shiptodet[5]."',ship_to_city='".$shiptodet[2]."',ship_to_state='".$shiptodet[3]."',ship_to_pincode='".$shiptodet[4]."',ledger_name='".$ledgername."',sale_person='".$sales_executive."'";
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
				////// insert in activity table////
				$flag=dailyActivity($_SESSION['userid'],$row_master["claim_no"],"CLAIM","EDIT",$ip,$link1,$flag);
			}
			else {
			   $flag = false;
			   $err_msg = "Mandatory field was missing";
			}
			//// check both master and data query are successfully executed
			if ($flag) {
				mysqli_commit($link1);
				$msg = "Claim is successfully edited with ref. no.".$row_master["claim_no"];
				$cflag = "success";
            	$cmsg = "Success";
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
////// get invoice details
$res_bill = mysqli_query($link1,"SELECT * FROM billing_master WHERE ref_no='".$docid."'");
$row_bill = mysqli_fetch_assoc($res_bill);
//if($_REQUEST['party_code']){ $pty_code = $_REQUEST['party_code'];}else{ $pty_code = $row_bill['from_location']."~".$row_bill['from_gst_no'];}
$pty_code = $row_bill['from_location']."~".$row_bill['from_gst_no'];
if($_REQUEST['plant_code']){ $plt_code = $_REQUEST['plant_code'];}else{ $plt_code = $row_bill['to_location'];}

$party_info = explode("~",$pty_code);
$toloctiondet = explode("~", getLocationDetails($plt_code, "state,id_type", $link1));
$fromlocationdet  = explode("~", getLocationDetails($party_info[0], "state,id_type", $link1));
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
function makeSelect(){
	$('.selectpicker').selectpicker({
		liveSearch: true,
		showSubtext: true
	});
}
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
      		<h2 align="center"><i class="fa fa-edit"></i> Edit Claim Details</h2><br/>
      			<div class="form-group"  id="page-wrap" style="margin-left:10px;">
          			<form  name="form1" class="form-horizontal" action="" method="post" id="form1" enctype="multipart/form-data">
                    <div class="panel-group">
    				<div class="panel panel-info">
        				<div class="panel-heading">Party Information</div>
         				<div class="panel-body">
        				<div class="form-group">
            				<div class="col-md-6"><label class="col-md-5">Party Name</label>
              					<div class="col-md-7">
               						<?=str_replace("~"," , ",getAnyDetails($row_master["party_id"],"name,city,state,asc_code","asc_code","asc_master",$link1))?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Claim Type</label>
              					<div class="col-md-7">
          							<?=$row_master["claim_type"]?>
      							</div>
            				</div>
          				</div>
                        <div class="form-group">
            				<div class="col-md-6 alert-warning"><label class="col-md-5">Claim No.</label>
              					<div class="col-md-7">
               						<?=$row_master["claim_no"]?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Status</label>
              					<div class="col-md-7">
          							<?=$row_master["status"]?>
      							</div>
            				</div>
          				</div>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5">Entry By</label>
              					<div class="col-md-7">
               						<?=getAnyDetails($row_master["entry_by"],"name","username","admin_users",$link1)." ".$row_master["entry_by"]?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Entry Date</label>
              					<div class="col-md-7">
          							<?=$row_master["entry_date"]." ".$row_master["entry_time"]?>
      							</div>
            				</div>
          				</div>
                        <div class="form-group">
            				<div class="col-md-6 alert-success"><label class="col-md-5">Requested Claim Amount</label>
              					<div class="col-md-7">
               						<?=$row_master["total_amount"]?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Claim Budget</label>
              					<div class="col-md-7">
          							<?php
									$str = "";
									$res_clm_bgt = mysqli_query($link1,"SELECT budget_year, budget_yearly FROM claim_budget WHERE party_id='".$row_master["party_id"]."' AND  claim_type='".$row_master["claim_type"]."' AND status='1'");
									while($row_clm_bgt = mysqli_fetch_assoc($res_clm_bgt)){
										if($str){
											$str .= ", ".$row_clm_bgt["budget_yearly"]." (<b>Year:</b> ".$row_clm_bgt["budget_year"].")";
										}else{
											$str = $row_clm_bgt["budget_yearly"]." (<b>Year:</b> ".$row_clm_bgt["budget_year"].")";
										}
									}
									echo $str;
									?>
      							</div>
            				</div>
          				</div>
                        <?php
						$res_apppend = mysqli_query($link1,"SELECT process_id,last_updatedate FROM approval_status_matrix WHERE ref_no='".$row_master["claim_no"]."' AND current_status='Pending'");
						$row_apppend = mysqli_fetch_assoc($res_apppend);
						if(mysqli_num_rows($res_apppend)>0){
						?>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5">Approval Pending By</label>
              					<div class="col-md-7">
               						<?php 			
									/// get user details
									$res_user = mysqli_query($link1,"SELECT username,name FROM admin_users WHERE (app_steps_ids='".$row_apppend["process_id"]."' OR app_steps_ids LIKE '".$row_apppend["process_id"].",%' OR app_steps_ids LIKE '%,".$row_apppend["process_id"]."' OR app_steps_ids LIKE '%,".$row_apppend["process_id"].",%') AND status='active'");
									$row_user = mysqli_fetch_assoc($res_user);
									echo $row_user['name']." ".$row_user['username'];
									?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Approval Pending Aging</label>
              					<div class="col-md-7">
          							<?php 
									
									echo $diff = timeDiff($row_apppend['last_updatedate'],$datetime);
									?>
      							</div>
            				</div>
          				</div>
                        <?php }?>
          				</div>
                        </div>
                        <div class="panel panel-info table-responsive">
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
                                    <?php
									$i=0;
									$sql_data = "SELECT * FROM claim_data WHERE claim_no='".$docid."'";
									$res_data = mysqli_query($link1,$sql_data);
									while($row_data = mysqli_fetch_assoc($res_data)){
									?>
                    				
                        				<tr id="addr_claim<?=$i?>">
                                            <td><input type="text" class="form-control entername cp required" required name="claim_subject[<?=$i?>]" id="claim_subject[<?=$i?>]" value="<?=$row_data['claim_subject']?>"></td>
                                            <td><textarea class="form-control addressfield cp required" required name="claim_desc[<?=$i?>]" id="claim_desc[<?=$i?>]" style="resize:vertical"><?=$row_data['claim_desc']?></textarea></td>
                                            <td><input type="text" class="form-control required" required name="claim_date[<?=$i?>]" id="claim_date0" value="<?=$row_data['claim_date']?>"></td>
                                            <td><input type="text" class="form-control required digits" required name="claim_qty[<?=$i?>]" id="claim_qty[<?=$i?>]" value="<?=$row_data['qty']?>"></td>
                                            <td><input type="text" class="form-control required number" required name="claim_amt[<?=$i?>]" id="claim_amt[<?=$i?>]" value="<?=$row_data['amount']?>"></td>
                        				</tr>
                    				
                                    <?php
									$i++;
									}
									?>
                                    </tbody>
                				</table>   
                			</div>
                		</div>
						<div class="form-group">
           					<div class="col-sm-4" style="display:inline-block; float:left">
           						<a id="add_row2" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add More Line</a>
                                <input type="hidden" name="rowno2" id="rowno2" value="<?=($i-1)?>"/>
                          	</div>
          				</div>
                        
                        </div>
                        </div>
                        <?php 
						
						if(mysqli_num_rows($res_bill)>0){
							
						?>
                        <div class="panel panel-info">
        				<div class="panel-heading">Invoice Summary</div>
         				<div class="panel-body">
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Plant Code <span class="red_small">*</span></label>
              					<div class="col-md-7">
               						<select name="plant_code" id="plant_code" required class="form-control selectpicker required" data-live-search="true" onChange="document.form1.submit()">
                                    	<option value="" selected="selected">Please Select </option>
										<?php
                                        $sql_parent = "SELECT name, asc_code, city, state FROM asc_master WHERE id_type IN ('HO','BRANCH') AND status='Active'";
                                        $res_parent = mysqli_query($link1, $sql_parent);
                                        while ($row_parent = mysqli_fetch_array($res_parent)) {   
                                        ?>
                                        <option value="<?= $row_parent['asc_code']?>" <?php if ($row_parent['asc_code'] == $plt_code) echo "selected"; ?> ><?= $row_parent['name'] . " | " . $row_parent['city'] . " | " . $row_parent['state'] . " | " . $row_parent['asc_code']?></option>
                                     	<?php
                                        }
                                        ?>
                            		</select>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Party GSTIN <span class="red_small">*</span></label>
              					<div class="col-md-7">
          							<input type="text" class="form-control alphanumeric required" required name="party_gstin" id="party_gstin" autocomplete="off" readonly value="<?=$row_bill['from_gst_no']?>"/>
      							</div>
            				</div>
          				</div>
		    			<div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Invoice No. <span class="red_small">*</span></label>
              					<div class="col-md-7">
               						<input type="text" name="invoice_no" id="invoice_no" value="<?=$row_bill['challan_no']?>" class="form-control mastername" autocomplete="off"/>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Invoice Date <span class="red_small">*</span></label>
              					<div class="col-md-7">
          							<input type="text" class="form-control span2" name="invoicedate" id="invoicedate" autocomplete="off" value="<?=$row_bill['sale_date']?>"/>
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
                                    	<?php
										$k=0;
										$sql_invdata = "SELECT * FROM billing_model_data WHERE challan_no='".$row_bill['challan_no']."'";
										$res_invdata = mysqli_query($link1,$sql_invdata);
										while($row_invdata = mysqli_fetch_assoc($res_invdata)){
											$proddet=explode("~",getProductDetails($row_invdata['prod_code'],"productname,productcode",$link1));
										?>
                        				<tr id="addr_inv<?=$k?>">
                                            <td><select name="prod_code[<?=$k?>]" id="prod_code[<?=$k?>]"  class="form-control selectpicker" data-live-search="true" style="width:150px;padding-right:100px;" onChange="get_price(0);">
                                                    <option value="">--None--</option>
                                                    <?php
                                                    $model_query = "select productcode, productname,model_name,hsn_code from product_master where status='Active' AND productsubcat IN (".$acc_psc.") AND brand IN (".$acc_brd.")";
                                                    $check1 = mysqli_query($link1, $model_query);
                                                    while ($br = mysqli_fetch_array($check1)) {
                                                        ?>
                                                        <option value="<?php echo $br['productcode']."~".$br['hsn_code']; ?>"<?php if($br['productcode']==$row_invdata['prod_code']){ echo "selected";}?>><?php echo $br['productname']." | ".$br['productcode']." | ".$br['model_name']; ?></option>
                                                    <?php } ?>
                                                </select></td>
                                            <td><input type="text" class="form-control" name="hsn[<?=$k?>]" id="hsn[<?=$k?>]" autocomplete="off" value="<?=$row_invdata['combo_code']?>" style="text-align:left" readonly></td>
                                            <td><input type="text" class="form-control number" name="bill_qty[<?=$k?>]" id="bill_qty[<?=$k?>]" value="<?=$row_invdata['qty']?>" onKeyUp="rowTotal(<?=$k?>);" autocomplete="off" style="text-align:right"></td>
                                            <td><input type="text" class="form-control number" name="price[<?=$k?>]" id="price[<?=$k?>]" value="<?=$row_invdata['price']?>" onKeyUp="rowTotal(<?=$k?>);" autocomplete="off" style="text-align:right"></td>
                                            <td><input type="text" class="form-control number" name="rowdiscount[<?=$k?>]" id="rowdiscount[<?=$k?>]" value="<?=$row_invdata['discount']?>" onKeyUp="rowTotal(<?=$k?>);" autocomplete="off" style="text-align:right"></td>
                                            <td><input type="text" class="form-control" name="rowsubtotal[<?=$k?>]" id="rowsubtotal[<?=$k?>]" autocomplete="off" value="<?=$row_invdata['value']-$row_invdata['discount']?>" style="text-align:right" readonly></td>
                                            <td><?php if($fromlocationdet[0]==$toloctiondet[0]){ ?>
                                              	<div class="row">
                                                	<div class="col-md-4">
                                               		<input type="text" class="form-control" name="rowsgstper[<?=$k?>]" id="rowsgstper[<?=$k?>]" value="<?=$row_invdata['sgst_per']?>" readonly style="width:50px;text-align:right;padding: 4px">
                                                	</div>
                                                	<div class="col-md-4">
                                                	<input type="text" class="form-control" name="rowsgstamount[<?=$k?>]" id="rowsgstamount[<?=$k?>]" value="<?=$row_invdata['sgst_amt']?>" readonly style="width:80px;text-align:right;padding: 4px">					
                                                </div>
                                                </div>
                                                
                                                
                                                <div class="row">
                                                	<div class="col-md-4">
                                                    <input type="text" class="form-control" name="rowcgstper[<?=$k?>]" id="rowcgstper[<?=$k?>]" value="<?=$row_invdata['cgst_per']?>" readonly style="width:50px;text-align:right;padding: 4px">
                                                    </div>
                                                	<div class="col-md-4">
                                                    <input type="text" class="form-control" name="rowcgstamount[<?=$k?>]" id="rowcgstamount[<?=$k?>]" value="<?=$row_invdata['cgst_amt']?>" readonly style="width:80px;text-align:right;padding: 4px">
                                                    </div>
                                               </div>
                                                <?php }else{?>
                                                <div class="row">
                                                	<div class="col-md-4">
                                                	<input type="text" class="form-control" name="rowigstper[<?=$k?>]" id="rowigstper[<?=$k?>]" value="<?=$row_invdata['igst_per']?>" readonly style="width:50px;text-align:right;padding: 4px">
                                                	</div>
                                                    <div class="col-md-4">
                                                	<input type="text" class="form-control" name="rowigstamount[<?=$k?>]" id="rowigstamount[<?=$k?>]" value="<?=$row_invdata['igst_amt']?>" readonly style="width:60px;text-align:right;padding: 4px">
                                                    </div>
                                                </div>
                                                <?php }?></td>
                                            <td><input type="text" class="form-control" name="total_val[<?=$k?>]" id="total_val[<?=$k?>]" autocomplete="off" readonly value="<?=$row_invdata['totalvalue']?>" style="text-align:right"></td>
                        				</tr>
                                        <?php 
										$tot_qty += $row_invdata['qty'];
										$k++;
										}?>
                    				</tbody>
                				</table>   
               			  </div>
                		</div>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Total Qty <span class="red_small">*</span></label>
              					<div class="col-md-7">
               						<input type="text" name="totqty" id="totqty" value="<?=$tot_qty?>" class="form-control digits" autocomplete="off" readonly/>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Taxable Amount <span class="red_small">*</span></label>
              					<div class="col-md-7">
          							<input type="text" name="tottaxable" id="tottaxable" value="<?=$row_bill['basic_cost']-$row_bill['discount_amt']?>" class="form-control number" autocomplete="off" readonly/>
      							</div>
            				</div>
          				</div>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Total GST <span class="red_small">*</span></label>
              					<div class="col-md-7">
               						<input type="text" name="totgst" id="totgst" value="<?=$row_bill['total_sgst_amt']+$row_bill['total_cgst_amt']+$row_bill['total_igst_amt']?>" class="form-control number" autocomplete="off" readonly/>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Total Amount <span class="red_small">*</span></label>
              					<div class="col-md-7">
          							<input type="text" name="totamt" id="totamt" value="<?=$row_bill['total_cost']?>" class="form-control number" autocomplete="off" readonly/>
      							</div>
            				</div>
          				</div>
                		<div class="form-group">
           					<div class="col-sm-4" style="display:inline-block; float:left">
           						<a id="add_row21" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add More Line</a>
                                <input type="hidden" name="rowno21" id="rowno21" value="<?=$k-1?>"/>
                                	<input type="hidden" name="refinv" id="refinv" value="<?= base64_encode($row_bill['challan_no'])?>"/>
                                    <input type="hidden" name="party_code" id="party_code" value="<?= base64_encode($row_bill['from_location']."~".$row_bill['from_gst_no'])?>"/>
                                    <input type="hidden" name="toloctionstate" id="toloctionstate" value="<?= $toloctiondet[0] ?>"/>
									<input type="hidden" name="fromloctionstate" id="fromloctionstate" value="<?= $fromlocationdet[0] ?>"/>
									<input type="hidden" name="fromidtype" id="fromidtype" value="<?= $fromlocationdet[1] ?>"/>
                                    <input type="hidden" name="toidtype" id="toidtype" value="<?= $toloctiondet[1] ?>"/>
                          	</div>
          				</div>
                        
                        </div>
                        </div>
                        <?php 
						}
						?>
                        <?php
						$res_poapp = mysqli_query($link1,"SELECT * FROM approval_activities where ref_no='".$docid."'")or die("ERR1".mysqli_error($link1)); 
						if(mysqli_num_rows($res_poapp)>0){
						?>
						<div class="panel panel-info table-responsive">
							<div class="panel-heading">Approval History</div>
							<div class="panel-body">
				
							<div class="form-group">
								<div class="col-sm-12">
									<table class="table table-bordered" width="100%">
										<thead>
											<tr class="<?=$tableheadcolor?>" >
												<th width="20%">Action Date & Time</th>
												<th width="30%">Action Taken By</th>
												<th width="20%">Action</th>
												<th width="30%">Action Remark</th>
											</tr>
										</thead>
										<tbody>
											<?php
											while($row_poapp=mysqli_fetch_assoc($res_poapp)){
											?>
											  <tr>
												<td><?php echo $row_poapp['action_date']." ".$row_poapp['action_time'];?></td>
												<td><?php echo getAdminDetails($row_poapp['action_by'],"name",$link1);?></td>
												<td><?php echo $row_poapp['req_type']." ".$row_poapp['action_taken']?></td>
												<td><?php echo $row_poapp['action_remark']?></td>
											  </tr>
											<?php
											}
											?>  
										</tbody>
									</table>   
								</div>
							</div>
							</div> 
						</div>
						<?php 
						}
						?>
                   		<div class="panel panel-info table-responsive">
        				<div class="panel-heading">Supporting Document</div>
         				<div class="panel-body">
		    
			 			<div class="form-group">
                			<div class="col-sm-12">
                				<table class="table table-bordered" width="100%" id="itemsTable3">
                    				<thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th width="30%">Document Name</th>
                                            <th width="30%">Description</th>
                                            <th width="40%">Attachment</th>
                                        </tr>
                    				</thead>
                    				<tbody>
                                    	<?php
										$j=0;
										$sql_data_doc = "SELECT * FROM document_attachment WHERE ref_no='".$docid."'";
										$res_data_doc = mysqli_query($link1,$sql_data_doc);
										while($row_data_doc = mysqli_fetch_assoc($res_data_doc)){
										?>
                        				<tr id="">
                                            <td><input type="text" readonly class="form-control entername cp" name="documentname[<?=$j?>]"  id="documentname[<?=$j?>]" value="<?=$row_data_doc['document_name']?>"></td>
                                            <td><input type="text" readonly class="form-control entername cp" name="documentdesc[<?=$j?>]"  id="documentdesc[<?=$j?>]" value="<?=$row_data_doc['document_desc']?>"></td>
                                            <td><a href="<?=$row_data_doc['document_path']?>" target="_blank" class="btn <?=$btncolor?>" title="Attachment"><i class="fa fa-paperclip" title="Attachment"></i></a></td>
                        				</tr>
                                        <?php
										$j++;
										}
										?>
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
                            	<input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Update" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
                                <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='<?=$backlink?>.php?<?=$pagenav?>'">
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