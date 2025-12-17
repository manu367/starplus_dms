<?php
////// Function ID ///////
$fun_id = array("u"=>array(2)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$docid = base64_decode($_REQUEST['id']);
$po_sql = "SELECT * FROM billing_master WHERE challan_no='".$docid."'";
$po_res = mysqli_query($link1,$po_sql);
$po_row = mysqli_fetch_assoc($po_res);
////// final submit form ////
@extract($_POST);
if($_POST) {
	if($_POST['Submit'] == 'Generate CN' || $_POST['Submit']=='Cancel') {
	///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
	$messageIdent = md5($_POST['Submit'] . $docid);
	//and check it against the stored value:
	$sessionMessageIdent = isset($_SESSION['msgRetailSRN'])?$_SESSION['msgRetailSRN']:'';
	if($messageIdent!=$sessionMessageIdent){//if its different:
		//save the session var:
		$_SESSION['msgRetailSRN'] = $messageIdent;
		///start transaction
		mysqli_autocommit($link1, false);
        $flag = true;
        $err_msg = "";
		if($po_row["document_type"]=="INVOICE"){
			////////generate CN no.
			$res_cnt = mysqli_query($link1, "SELECT srn_str, srn_counter FROM document_counter WHERE location_code='".$po_row['from_location']."'");
			$row_cnt = mysqli_fetch_array($res_cnt);
			$crn_cnt = $row_cnt['srn_counter'] + 1;
			$crn_pad = $crn_cnt;
			$crn_no = $row_cnt['srn_str'].$crn_pad;
			//////insert in master table
			$sql_cn = "INSERT INTO credit_note SET 
			cust_id='".$po_row['to_location']."',
			location_id='".$po_row['from_location']."',
			sub_location='".$po_row['sub_location']."',
			entered_ref_no='".$po_row['challan_no']."',
			entered_ref_date='".$po_row['sale_date']."',
			ref_no='".$crn_no."',
			sys_ref_temp_no='".$crn_pad."',
			challan_no='CN-AGAINST-NON-DISPATCH',
			create_by='".$_SESSION['userid']."',
			remark='".$remark."',
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
			to_gst_no='".$po_row['to_gst_no']."',
			party_name='".$po_row['party_name']."',
			from_partyname='".$po_row['from_partyname']."',
			from_gst_no='".$po_row['from_gst_no']."',
			bill_from='".$po_row['bill_from']."',
			bill_topty='".$po_row['bill_topty']."',
			from_addrs='".$po_row['from_addrs']."',
			disp_addrs='".$po_row['disp_addrs']."',
			to_addrs='".$po_row['to_addrs']."',
			deliv_addrs='".$po_row['deliv_addrs']."',
			to_state='".$po_row['to_state']."',
			from_state='".$po_row['from_state']."',
			to_city='".$po_row['to_city']."',
			from_city='".$po_row['from_city']."',
			to_pincode='".$po_row['to_pincode']."',
			from_pincode='".$po_row['from_pincode']."',
			to_phone='".$po_row['to_phone']."',
			from_phone='".$po_row['from_phone']."',
			to_email='".$po_row['to_email']."',
			from_email='".$po_row['from_email']."',
			billing_type='".$po_row['billing_type']."'";
			$res_cn= mysqli_query($link1,$sql_cn);
			//// check if query is not executed
			if (!$res_cn){
				 $flag = false;
				 $err_msg = "Error details1: " . mysqli_error($link1) . ".";
			}
			$resultcn = mysqli_query($link1, "UPDATE document_counter SET srn_counter=srn_counter+1, update_by='".$_SESSION['userid']."', updatedate='".$datetime."' WHERE location_code='".$po_row['from_location']."'");
			//// check if query is not executed
			if (!$resultcn) {
				$flag = false;
				$err_msg = "Error Code1.1: ".mysqli_error($link1);
			}
		}
		///////// fetch item details from data table
        $i = 1;
		$vpo_sql = "SELECT * FROM billing_model_data WHERE challan_no='".$docid."'";
        $vpo_res = mysqli_query($link1, $vpo_sql);
        while($vpo_row = mysqli_fetch_assoc($vpo_res)) {
			///// bypass combo model
			if($vpo_row["prod_cat"]!="C"){
				//// update stock of from loaction
				$result1 = mysqli_query($link1, "UPDATE stock_status SET okqty=okqty+'".$vpo_row['qty']."', updatedate='".$datetime."' WHERE asc_code='".$po_row['from_location']."' AND sub_location='".$po_row['sub_location']."' AND partcode='".$vpo_row['prod_code']."'");
				//// check if query is not executed
				if (!$result1) {
					$flag = false;
					$err_msg = "Error Code1:". mysqli_error($link1) . ".";
				}
				///// insert in stock ledger////
				$flag = stockLedger($docid, $today, $vpo_row['prod_code'], $po_row['sub_location'], $po_row['to_location'], $po_row['sub_location'], "IN", "OK", "Cancel Retail Invoice", $vpo_row['qty'], $vpo_row['price'], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
				$i++;
			}
			if($po_row["document_type"]=="INVOICE"){
				/////// credit note data
				$query2 = "INSERT INTO credit_note_data SET prod_code='".$vpo_row['prod_code']."',combo_code='".$vpo_row['combo_code']."',combo_name='".$vpo_row['combo_name']."',prod_cat='".$vpo_row['prod_cat']."',req_qty='".$vpo_row['qty']."' , price='".$vpo_row['price']."', value='".$vpo_row['value']."' , discount='".$vpo_row['discount']."', totalvalue='".$vpo_row['totalvalue']."',ref_no='".$crn_no."',entry_date='".$today."' ,sgst_per='".$vpo_row['sgst_per']."' ,sgst_amt='".$vpo_row['sgst_amt']."',igst_per='".$vpo_row['igst_per']."' ,igst_amt='".$vpo_row['igst_amt']."',cgst_per='".$vpo_row['cgst_per']."' ,cgst_amt='".$vpo_row['cgst_amt']."'";			
				$result2 = mysqli_query($link1, $query2);
				//// check if query is not executed
				if (!$result2) {
					$flag = false;
					$err_msg = "Error details2: " . mysqli_error($link1) . ".";
				}
			}
        }/// close for loop
        /////// check if imei is attached then it should also cancelled or reverse to the from location
        if($po_row['imei_attach'] == "Y") {
			$billing_data = mysqli_query ($link1 ,"SELECT * FROM billing_imei_data WHERE doc_no = '".$docid."'");
			if(mysqli_num_rows($billing_data) > 0) {
				while($row = mysqli_fetch_array($billing_data)){		
					//////   insert deleted imeis into new table///////////////////////////////////////////////////
					$result3 = mysqli_query($link1,"INSERT INTO cancel_imei_data SET from_location = '".$row['from_location']."', to_location = '".$row['to_location']."', owner_code = '".$row['owner_code']."', prod_code = '".$row['prod_code']."', doc_no = '".$row['doc_no']."', imei1 = '".$row['imei1']."', imei2 = '".$row['imei2']."', flag = '".$row['flag']."', stock_type = '".$row['stock_type']."'");	
					//// check if query is not executed
					if (!$result3) {
						$flag = false;
						$err_msg = "Error Code31:" . mysqli_error($link1) . ".";
					}	
				}
				///////////   delete entry from billing imei data /////////////////////////////////////////////////////////
				$result4 = mysqli_query($link1, "DELETE FROM billing_imei_data WHERE doc_no = '".$docid."'");
				//// check if query is not executed////////
				if (!$result4) {
					$flag = false;
					$err_msg = "Error Code4:" . mysqli_error($link1) . ".";
				}
			}
        }
        ///// cancel retail invoice ///////////
        $query3 = "UPDATE billing_master SET status='Cancelled', cancel_by='".$_SESSION['userid']."', cancel_date='".$today."', cancel_rmk='".$remark."', cancel_step='After ".$po_row['status']."', cancel_ip='".$ip."' WHERE challan_no='".$docid."'";
        $result3 = mysqli_query($link1, $query3);
        //// check if query is not executed
        if (!$result3) {
            $flag = false;
            $err_msg = "Error Code3:". mysqli_error($link1) . ".";
        }
       /* ///// check retail billing was not for customer. we reverse credit balance for location only not for customer
        if (substr($po_row['to_location'], 0, 4) != "CUST") {
            //// update cr bal of child location
            $result4 = mysqli_query($link1, "UPDATE current_cr_status SET cr_abl=cr_abl+'".$po_row['total_cost']."', total_cr_limit=total_cr_limit+'".$po_row['total_cost']."', last_updated='".$datetime."' WHERE parent_code='".$po_row['from_location']."' AND asc_code='".$po_row['to_location']."'");
            //// check if query is not executed
            if (!$result4) {
                $flag = false;
                $err_msg = "Error Code4:". mysqli_error($link1) . ".";
            }
        }*/
        ////// maintain party ledger////
        //$flag = partyLedger($po_row['from_location'], $po_row['to_location'], $docid, $today, $today, $currtime, $_SESSION['userid'], "CANCEL RETAIL INVOICE", $po_row['total_cost'], "CR", $link1, $flag);
        ////// insert in activity table////
        $flag = dailyActivity($_SESSION['userid'], $docid, "RETAIL INVOICE", "CANCEL", $ip, $link1, $flag);
        ///// check  master  query are successfully executed
        if ($flag) {
            mysqli_commit($link1);
			if($po_row["document_type"]=="INVOICE"){
            	$msg = "Invoice is Cancelled successfully with with ref. no." . $docid."<br/>Please approve CN now.";
			}else{
				$msg = "Invoice/DC is Cancelled successfully with with ref. no." . $docid." Please check stock.";
			}
			$cflag = "success";
			$cmsg = "Success";
        } else {
            mysqli_rollback($link1);
            $msg = "Request could not be processed " . $err_msg . ". Please try again.";
			$cflag = "danger";
			$cmsg = "Failed";
        }
	}else {
		//you've sent this already!
		$msg="You have saved this already ";
		$cflag = "warning";
		$cmsg = "Warning";
	}		
        mysqli_close($link1);
        ///// move to parent page
        header("Location:retailbillinglist.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= siteTitle ?></title>
    <script src="../js/jquery.min.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/bootstrap.min.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
	<div class="container-fluid">
    	<div class="row content">
			<?php
            include("../includes/leftnav2.php");
            ?>
        	<div class="col-sm-9">
            	<h2 align="center"><i class="fa fa-user"></i> Retail Billing  Details</h2>
                <h4 align="center"><i class="fa fa-reply-all"></i> Sale Return</h4>
                <div class="panel-group">
                	<form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
                    	<div class="panel panel-info table-responsive">
                        	<div class="panel-heading">Party Information</div>
                            <div class="panel-body">
                            	<table class="table table-bordered" width="100%">
                                	<tbody>
                                    	<tr>
                                        	<td width="20%"><label class="control-label">Billing From</label></td>
                                            <td width="30%"><?php echo str_replace("~", ",", getLocationDetails($po_row['from_location'], "name,city,state", $link1)); ?></td>
                                            <td width="20%"><label class="control-label">Billing To</label></td>
                                            <td width="30%"><?php 
											/// bill to party
											$billto=getLocationDetails($po_row['to_location'],"name,city,state",$link1);
											$explodeval=explode("~",$billto);
											if($explodeval[0]){ $toparty=$billto; }else{ $toparty=getCustomerDetails($po_row['to_location'],"customername,city,state",$link1);}
											echo str_replace("~",",",$toparty);?></td>
                                   		</tr>
                                       	<tr>
                                        	<td><label class="control-label">Invoice No.</label></td>
                                            <td><?php echo $po_row['challan_no']; ?></td>
                                            <td><label class="control-label">Billing Date</label></td>
                                            <td><?php echo $po_row['sale_date']; ?></td>
                                      	</tr>
                                        <tr>
                                            <td><label class="control-label">Entry By</label></td>
                                            <td><?php echo getAdminDetails($po_row['entry_by'], "name", $link1); ?></td>
                                            <td><label class="control-label">Status</label></td>
                                            <td><?php echo $po_row['status']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Document Type</label></td>
                                            <td><?php echo $po_row['document_type'];?></td>
                                            <td><label class="control-label">Cost Center</label></td>
                                            <td><?php $subl = getAnyDetails($po_row['sub_location'],"cost_center,sub_location_name","sub_location","sub_location_master",$link1); if($subl){ echo $subl;}else{ echo getAnyDetails($po_row['sub_location'],"name","asc_code","asc_master",$link1);}?></td>
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
                                            <th style="text-align:center" width="15%">Bill Qty</th>
                                            <th style="text-align:center" width="15%">Price</th>
                                            <th style="text-align:center" width="15%">Value</th>
                                            <th style="text-align:center" width="15%">Discount</th>
                                            <th style="text-align:center" width="15%">Tax Amount</th>
                                            <th style="text-align:center" width="15%">Total</th>
                                        </tr>
                                  	</thead>
                                    <tbody>
									<?php
                                    $i = 1;
                                    $podata_sql = "SELECT * FROM billing_model_data WHERE challan_no='".$docid."'";
                                    $podata_res = mysqli_query($link1, $podata_sql);
                                    while ($podata_row = mysqli_fetch_assoc($podata_res)) {
                                        $proddet = explode("~", getProductDetails($podata_row['prod_code'], "productname,model_name", $link1));
                                        ?>
                                   		<tr>
                                            <td><?= $i ?></td>
                                            <td><?= $proddet[0]." | ".$proddet[1]. " (" . $podata_row['prod_code'] . ")" ?></td>
                                            <td style="text-align:right"><?= $podata_row['qty'] ?></td>
                                            <td style="text-align:right"><?= $podata_row['price'] ?></td>
                                            <td style="text-align:right"><?= $podata_row['value'] ?></td>
                                            <td style="text-align:right"><?= $podata_row['discount'] ?></td>
                                            <td style="text-align:right"><?= $podata_row['tax_amt'] ?></td>
                                            <td style="text-align:right"><?= $podata_row['totalvalue'] ?></td>
                                        </tr>
                                   	<?php
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
                        <?php if($po_row["document_type"]=="INVOICE"){ ?>
                        <div class="panel panel-info table-responsive">
                        	<div class="panel-heading">CN Action</div>
                            <div class="panel-body">
                            	<table class="table table-bordered" width="100%">
                                	<tbody>
										<tr>
                                            <td><label class="control-label">CN Remark <span class="red_small">*</span></label></td>
                                            <td><textarea name="remark" id="remark" required class="required form-control addressfield" style="resize:vertical;width:300px;"></textarea></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" align="center">
                                                <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Generate CN" title="" <?php if ($_POST['Submit'] == 'Generate CN') { ?>disabled<?php } ?>>&nbsp;
                                                <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href = 'retailbillinglist.php?<?= $pagenav ?>'">
                                            </td>
                                        </tr>
                                	</tbody>
                             	</table>
                        	</div><!--close panel body-->
                       	</div><!--close panel-->
                        <?php }else{?>
                        <div class="panel panel-info table-responsive">
                        	<div class="panel-heading">Cancel Action</div>
                            <div class="panel-body">
                            	<table class="table table-bordered" width="100%">
                                	<tbody>
										<tr>
                                            <td><label class="control-label">Cancel Remark <span class="red_small">*</span></label></td>
                                            <td><textarea name="remark" id="remark" required class="required form-control addressfield" style="resize:vertical;width:300px;"></textarea></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" align="center">
                                                <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Cancel" title="" <?php if ($_POST['Submit'] == 'Cancel') { ?>disabled<?php } ?>>&nbsp;
                                                <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href = 'retailbillinglist.php?<?= $pagenav ?>'">
                                            </td>
                                        </tr>
                                	</tbody>
                             	</table>
                        	</div><!--close panel body-->
                       	</div><!--close panel-->
                        <?php }?>
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