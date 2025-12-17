<?php
////// Function ID ///////
$fun_id = array("u"=>array(2)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM billing_master where challan_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
@extract($_POST);
/////// if payment is received against invoice
if(isset($_POST['saveButton'])){
	if($_POST['saveButton'] == "Receive"){
		////// make receipt no.
		$res_recp = mysqli_query($link1,"SELECT MAX(rcvpay_counter) as no, rcvpay_str FROM document_counter WHERE location_code='".$po_row['from_location']."'");
		if(mysqli_num_rows($res_recp)>0){
			$row_recp = mysqli_fetch_array($res_recp);
			$c_nos = $row_recp['no']+1;
			$pad1 = str_pad($c_nos,4,"0",STR_PAD_LEFT);  
			$receipt_no = $row_recp['rcvpay_str'].$pad1; 
			mysqli_autocommit($link1, false);
			$flag = true;
			$err_msg = "";
			$rec_amount = 0.00;
			///// Insert in payment by picking each data row one by one
			foreach($payMode as $k=>$val)
			{
				// checking row value of receive amount, it should not be blank/0/0.00
				if($payMode[$k]!='' && $recAmount[$k]!='' && $recAmount[$k]!=0 && $recAmount[$k]!=0.00) {
					///// Insert payment details
					$query1 = "INSERT INTO payment_receive SET doc_no='".$receipt_no."', against_ref_no='".$docid."', from_location='".$po_row['to_location']."', to_location='".$po_row['from_location']."', amount='".$recAmount[$k]."', rec_amount='".$recAmount[$k]."', status='Approve', payment_mode='".$payMode[$k]."', bank_name='', bank_branch='', dd_cheque_no='', dd_cheque_dt='', receipt_no='', transaction_id='".$ref_no[$k]."', remark='".$remark[$k]."',payment_date='".$today."',entry_dt='".$today."',entry_time='".$currtime."',entry_by='".$_SESSION['userid']."',ip='".$ip."'";
					$result = mysqli_query($link1,$query1)or die ("ER1".mysqli_error($link1));
					//// check if query is not executed
					if (!$result) {
						 $flag = false;
						 $err_msg = "Error details1: " . mysqli_error($link1) . ".";
					}
					$rec_amount += $recAmount[$k];
				}
			}
			///// update document counter
			$result=mysqli_query($link1, "update document_counter set rcvpay_counter='".$c_nos."', updatedate='".$datetime."'  where location_code='".$po_row['from_location']."'");
			if (!$result) {
				 $flag = false;
				 $err_msg = "Error details1.1: " . mysqli_error($link1) . ".";
			}
			///// entry in party ledger
			$flag = partyLedger($po_row['from_location'],$po_row['to_location'],$receipt_no,$today,$today, $currtime, $_SESSION['userid'],"RP", $rec_amount,'CR',$link1, $flag);
			/// insert  into  location_account_ledger table //////////////////////////
   			$result2 = mysqli_query($link1,"UPDATE current_cr_status SET cr_abl = cr_abl +'".$rec_amount."', total_cr_limit = total_cr_limit +'".$rec_amount."' WHERE  parent_code = '".$po_row['from_location']."' AND asc_code = '".$po_row['to_location']."'");
			//// check if query is not executed
			if (!$result2) {
			   $flag = false;
			   $err_msg = "Error details2: " . mysqli_error($link1) . ".";
			}					
			///// update status in bill master
			$result3 = mysqli_query($link1,"UPDATE billing_master set payment_ref='".$receipt_no."', adjusted_amt='".$rec_amount."', is_adjust='Y', payment_date='".$today."' WHERE challan_no='".$docid."'");
			//// check if query is not executed
			if (!$result3) {
			   $flag = false;
			   $err_msg = "Error details3: " . mysqli_error($link1) . ".";
			}
			////// insert in activity table////
			$flag=dailyActivity($_SESSION['userid'],$receipt_no,"RP","Receive payment",$ip,$link1,$flag);
			///// check payment receive query is successfully executed
			if ($flag) {
				mysqli_commit($link1);
				$msg = "Payment received successfully  with ref. no.".$receipt_no;
				$cflag="success";
				$cmsg = "Success";
			} else {
				mysqli_rollback($link1);
				$msg = "Request could not be processed. Please try again. ".$err_msg;
				$cflag="danger";
				$cmsg = "Failed";
			} 
			//mysqli_close($link1);			
		}else{
			$msg = "Request could not be processed receipt series not found. Please try again.";
			$cflag="danger";
			$cmsg = "Failed";
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
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
////// function for open modal to view planning details
function openModel(refno,totamt){
	$.get('retailPayReceive.php?rid=' + refno + '&invamt=' + totamt, function(html){
		 $('#myModal .modal-body').html(html);
		 $('#myModal').modal({
			show: true,
			backdrop:"static"
		});
	 });
	 var msg = "<strong>Invoice No. : </strong>" + refno;
	 $("#docno").html(msg);
}
function addNewRow(){
		var numi = document.getElementById('rowno');
		var pay_mode="payMode["+numi.value+"]";
        var rec_amt="recAmount["+numi.value+"]";
		var preno=document.getElementById('rowno').value;
		var num = (document.getElementById("rowno").value -1)+ 2;
		if((document.getElementById(pay_mode).value!="" && document.getElementById(rec_amt).value!="" && document.getElementById(rec_amt).value!="0") || ($("#addr"+numi.value+":visible").length==0)){
			numi.value = num;
			var r='<tr id="addr'+num+'"><td><select name="payMode['+num+']" id="payMode['+num+']" class="form-control" required><option value="">Please Select</option><option value="CASH">CASH</option><option value="CHEQUE">CHEQUE</option><option value="BANK TRANSFER">BANK TRANSFER</option><option value="PAYTM">PAYTM</option><option value="OTHER">OTHER</option></select></td><td><input type="number" name="recAmount['+num+']" class="form-control" id="recAmount['+num+']" value="" required onkeyup="checkReceiveAmt();"/></td><td><input name="ref_no['+num+']" id="ref_no['+num+']" type="text" class="form-control" pattern="[0-9a-zA-Z )(_.\/-]*$"/></td><td><textarea name="remark['+num+']" class="form-control addressfield" id="remark['+num+']"></textarea></td></tr>';
			$('#itemsTable1').append(r);
		}
}
////// check total receive payment
function checkReceiveAmt(){
	var maxrow = document.getElementById('rowno').value;
	var totEntAmt = 0.00;
	for(var i=0; i<=maxrow; i++){
		totEntAmt += parseFloat(document.getElementById("recAmount["+i+"]").value,2);
	}
	document.getElementById("totRecAmount").value = totEntAmt.toFixed(2);
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
      <h2 align="center"><i class="fa fa-user"></i> Retail Billing  Details</h2>
      <?php if(!empty($msg)){?>
        <div class="alert alert-<?php echo $cflag;?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?php echo $cmsg;?>!</strong>&nbsp;&nbsp;<?=$msg?>.
        </div>
        <?php }?>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
              	<td width="20%"><label class="control-label">Billing From</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['from_location'],"name,city,state",$link1));?></td>
                <td width="20%"><label class="control-label">Billing To</label></td>
                <td width="30%">
				  <?php 
				  /// bill to party
				  $billto=getLocationDetails($po_row['to_location'],"name,city,state",$link1);
				  $explodeval=explode("~",$billto);
				  if($explodeval[0]){ $toparty=$billto; }else{ $toparty=getCustomerDetails($po_row['to_location'],"customername,city,state",$link1);}
				  echo str_replace("~",",",$toparty);?></td>
                
              </tr>
              <tr>
                <td><label class="control-label">Invoice No.</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label">Billing Date</label></td>
                <td><?php echo $po_row['sale_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo getAdminDetails($po_row['entry_by'],"name",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $po_row['status'];?></td>
              </tr>
              <tr>
              	<td><label class="control-label">Cost Center(Go-Down)</label></td>
                <td><?php $subl = getAnyDetails($po_row['sub_location'],"cost_center,sub_location_name","sub_location","sub_location_master",$link1); if($subl){ echo $subl;}else{ echo getAnyDetails($po_row['sub_location'],"name","asc_code","asc_master",$link1);}?></td>
                <td><label class="control-label">State</label></td>
                <td><?php $d = explode('~',$toparty); echo $d['2']?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th style="text-align:center" width="5%">#</th>
                <th style="text-align:center" width="20%">Product</th>
                <th style="text-align:center" width="8%">Bill Qty</th>
                <th style="text-align:center" width="8%">Price</th>                
                <th style="text-align:center" width="11%">Discount</th>
                <th style="text-align:center" width="8%">Value After Discount</th>
                <th style="text-align:center" width="12%">SGST(%)</th>
                <th style="text-align:center" width="12%">SGST Amount</th>
                <th style="text-align:center" width="12%">CGST(%)</th>
                <th style="text-align:center" width="12%">CGST Amount</th>
                <th style="text-align:center" width="12%">IGST(%)</th>
                <th style="text-align:center" width="12%">IGST Amount</th>
                <th style="text-align:center" width="15%">Total</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM billing_model_data where challan_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,productcode",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?php if($podata_row["prod_cat"]=="C"){ echo $podata_row["combo_name"];}else{ echo $proddet[0]." (".$proddet[1].")";}?></td>
                <td style="text-align:right"><?=$podata_row['qty']?></td>
                <td style="text-align:right"><?=$podata_row['price']?></td>
                <td style="text-align:right"><?=$podata_row['discount']?></td>
				<?php  
				$valueafterdiscount =  ($podata_row['qty']*$podata_row['price'])-$podata_row['discount'];
				?>
                <td style="text-align:right"><?=$valueafterdiscount?></td>
                <td style="text-align:right"><?=$podata_row['sgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['igst_per']?></td>
                <td style="text-align:right"><?=$podata_row['igst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['totalvalue']?></td>
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
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Amount Information</div>
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
              <?php if($po_row['status']=="Cancelled"){ ?> 
              <tr>
				 <td><label class="control-label">Cancel By</label></td>
                 <td><?php echo getAdminDetails ($po_row['cancel_by'],"name",$link1);?></td>
				 <td><label class="control-label">Cancel Date</label></td>
                 <td ><?php echo dt_format ($po_row['cancel_date']);?></td>
				 </tr>
				<tr>                 
				 <td><label class="control-label">Cancel Remark</label></td>
                 <td colspan="3"><?php echo $po_row['cancel_rmk'];?></td>
                </tr>
			  <?php }?>
               
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <?php if($po_row['is_adjust']){?>
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Payment Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Receipt No.</label></td>
                <td width="30%"><?php echo $po_row['payment_ref'];?></td>
                <td width="20%"><label class="control-label">Payment Date</label></td>
                <td width="30%"><?php echo $po_row['payment_date'];?></td>
              </tr>
            </tbody>
          </table>
          <table class="table table-bordered" width="100%" border="0" cellspacing="0" cellpadding="0">
          	<thead>
              <tr class="<?=$tableheadcolor?>">
                <th>Payment Mode</th>
                <th>Receive Amount</th>
                <th>Ref. No.</th>
                <th>Remark</th>
              </tr>
            </thead>
            <tbody>  
              <?php
			  $tot_amt = 0.00;
			  $res_paymentt = mysqli_query($link1,"SELECT amount, payment_mode, transaction_id, remark FROM payment_receive WHERE doc_no='".$po_row['payment_ref']."'");
			  while($row_paymentt = mysqli_fetch_assoc($res_paymentt)){
			   ?>
              <tr>
                <td><?=$row_paymentt["payment_mode"]?></td>
                <td><?=currencyFormat($row_paymentt["amount"])?></td>
                <td><?=$row_paymentt["transaction_id"]?></td>
                <td><?=$row_paymentt["remark"]?></td>
              </tr>
              <?php $tot_amt += $row_paymentt["amount"];}?>
              <tr>
                <td><strong>Total</strong></td>
                <td><strong><?=currencyFormat($tot_amt)?></strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
             </tbody> 
           </table>

      </div><!--close panel body-->
    </div><!--close panel-->
    <?php }?>
    <div class="row" >
    	<div class="col-sm-12" style="text-align:center;">
                 <td colspan="4" align="center">
                 <?php if($po_row["is_adjust"] == "" && $msg==""){?>
                 <button title="Click to receive payment" type="button" class="btn btn-primary" onClick="openModel('<?php echo $docid;?>','<?php echo $po_row['total_cost'];?>');"><i class="fa fa-rupee fa-lg"></i>&nbsp;&nbsp;Receive Payment</button><?php }?>
                 <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='retailbillinglist.php?<?=$pagenav?>'"></td>
         </div>        
    </div>
  </div><!--close panel group-->
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<!-- The Modal -->
  <div class="modal fade" id="myModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
      	 <form class="form-horizontal" id="frm2" name="frm2" method="post">
        <!-- Modal Header -->
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 id="docno" align="center"></h4>
        </div>
        <!-- Modal body -->
        <div class="modal-body modal-bodyTH">
        

        </div>
        <!-- Modal footer -->
        <div class="modal-footer">
          <button class="btn btn-primary" id="saveButton" type="submit" name="saveButton" value="Receive"><i class="fa fa-save fa-lg"></i>&nbsp;&nbsp;Receive</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>
        </div>
        </form>
      </div>
    </div>
  </div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>