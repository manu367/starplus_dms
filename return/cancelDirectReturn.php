<?php
require_once("../config/config.php");
$docid = base64_decode($_REQUEST['id']);
$from = base64_decode($_REQUEST['from']);
$to = base64_decode($_REQUEST['to']);
$po_sql = "SELECT * FROM billing_master WHERE challan_no='".$docid."' AND from_location='".$to."' and to_location='".$from."' AND type='DIRECT SALE RETURN' ";
$po_res = mysqli_query($link1,$po_sql);
$po_row = mysqli_fetch_assoc($po_res);
////// final submit form ////
@extract($_POST);
if($_POST){
	if($_POST['Submit']=='Cancel'){
	///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
	$messageIdent = md5($_POST['Submit'] . $docid);
	//and check it against the stored value:
	$sessionMessageIdent = isset($_SESSION['msgCnlDSR'])?$_SESSION['msgCnlDSR']:'';
	if($messageIdent!=$sessionMessageIdent){//if its different:
		//save the session var:
		$_SESSION['msgCnlDSR'] = $messageIdent;
		mysqli_autocommit($link1, false);
	  	$flag = true;
	  	$err_msg ="";
	  	///// cancel corporate invoice ///////////
	 	$query1="UPDATE billing_master SET status='Cancelled', cancel_by='".$_SESSION['userid']."', cancel_date='".$today."', cancel_rmk='".$remark."', cancel_ip='".$ip."' WHERE challan_no='".$docid."'";
	  	$result1 = mysqli_query($link1,$query1);
	 	 //// check if query is not executed
	  	if (!$result1) {
	     	$flag = false;
         	$err_msg = "Error Code1:" . mysqli_error($link1) . ".";
      	}
		$vpo_sql="SELECT * FROM billing_model_data WHERE challan_no='".$docid."'";
		$vpo_res=mysqli_query($link1,$vpo_sql);
		while($vpo_row=mysqli_fetch_assoc($vpo_res)){
	  		if($vpo_row["prod_cat"]!="C"){
				//checking row value of product and qty should not be blank
				$selstok = mysqli_fetch_assoc(mysqli_query($link1,"SELECT okqty FROM stock_status WHERE partcode = '".$vpo_row["prod_code"]."' AND asc_code='".$po_row["to_location"]."' AND sub_location='".$po_row["sub_location"]."'"));
				//// check stock should be available ////
				if ($selstok["okqty"] < $vpo_row['qty']) {
					$flag = false;
					$err_msg = "Error Code0.1: Stock is not available for ".$vpo_row["prod_code"];
				} else {
					
				}
	     		//// update stock of from loaction
	    		$result2 = mysqli_query($link1, "UPDATE stock_status SET okqty = okqty-'".$vpo_row['qty']."', updatedate='".$datetime."' where asc_code='".$po_row['to_location']."' AND sub_location='".$po_row['sub_location']."' AND partcode='".$vpo_row['prod_code']."'");
				//// check if query is not executed
				if (!$result2) {
					$flag = false;
					$err_msg = "Error Code2:" . mysqli_error($link1) . ".";
				}
		 		///// insert in stock ledger////
		 		$flag=stockLedger($docid,$today,$vpo_row['prod_code'],$po_row['sub_location'],$po_row['from_location'],$po_row['sub_location'],"OUT","OK","CANCEL DIRECT SALE RETURN",$vpo_row['qty'],$vpo_row['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		 	}
			/////// check if imei is attached then it should also cancelled or reverse to the from location
			if($vpo_row['imei_attach']=="Y"){
				///////////   check if stock is available /////////////////////////////////////////////////// 			
				$billing_data = mysqli_query ($link1 ,"SELECT * FROM billing_imei_data WHERE doc_no = '".$docid."' AND prod_code = '".$vpo_row['prod_code']."'");
				if(mysqli_num_rows($billing_data) > 0) {
					while ($row = mysqli_fetch_array($billing_data)){		
						//////   insert deleted imeis into new table///////////////////////////////////////////////////
						$result3 = mysqli_query($link1,"insert into cancel_imei_data set from_location = '".$row['from_location']."' , to_location = '".$row['to_location']."' , owner_code = '".$row['owner_code']."' , prod_code = '".$row['prod_code']."' , doc_no = '".$row['doc_no']."' , imei1 = '".$row['imei1']."' , imei2 = '".$row['imei2']."' , flag = '".$row['flag']."'  , stock_type = '".$row['stock_type']."' ");	
						//// check if query is not executed
						if (!$result3) {
							$flag = false;
							$err_msg = "Error Code31:" . mysqli_error($link1) . ".";
						}	
					}
					///////////   delete entry from billing imei data /////////////////////////////////////////////////////////
					$result4 = mysqli_query($link1, "DELETE FROM billing_imei_data WHERE doc_no = '".$docid."' AND prod_code = '".$vpo_row['prod_code']."'");
					//// check if query is not executed////////
					if (!$result4) {
						$flag = false;
						$err_msg = "Error Code4:" . mysqli_error($link1) . ".";
					}
				}
			}
		}/// close for loop
   		//// update cr bal 
		if(mysqli_num_rows(mysqli_query($link1,"SELECT sno FROM credit_note WHERE challan_no='".$po_row["challan_no"]."' AND status='Approved'"))>0){
			/// insert in party ledger
			$flag=partyLedger($po_row['to_location'],$po_row['from_location'],$docid,$today,$today,$currtime,$_SESSION['userid'],"CANCEL DIRECT SALE RETURN",$po_row["total_cost"],"DR",$link1,$flag);
			if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM current_cr_status WHERE parent_code='".$po_row['to_location']."' AND asc_code='".$po_row['from_location']."'"))>0){
				$upd = mysqli_query($link1,"UPDATE current_cr_status SET cr_abl=cr_abl-'".$amt."',total_cr_limit=total_cr_limit-'".$amt."', last_updated='".$today."' WHERE parent_code='".$po_row['to_location']."' AND asc_code='".$po_row['from_location']."'");
				############# check if query is not executed
				if (!$upd) {
					$flag = false;
					$err_msg = "Error details11: " . mysqli_error($link1) . ".";
				}
			}else{
				$upd = mysqli_query($link1,"INSERT INTO current_cr_status SET cr_abl=cr_abl-'".$amt."',total_cr_limit=total_cr_limit-'".$amt."', last_updated='".$today."', parent_code='".$po_row['to_location']."' , asc_code='".$po_row['from_location']."'");
				############# check if query is not executed
				if (!$upd) {
					$flag = false;
					$err_msg = "Error details12: " . mysqli_error($link1) . ".";
				}
			}
		}
		$result4 = mysqli_query($link1,"UPDATE credit_note SET status='Cancelled', cancelled_by='".$_SESSION['userid']."', cancel_date='".$today."', cancel_reason='".$remark."' WHERE challan_no='".$docid."'");
		/// check if query is not executed
		if (!$result4) {
			$flag = false;
			$err_msg = "Error Code4:" . mysqli_error($link1) . ".";
		}
    	////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$docid,"DIRECT RETURN","CANCEL",$ip,$link1,$flag);
		$flag = dailyActivity($_SESSION['userid'],$docid,"CREDIT NOTE","CANCEL",$ip,$link1,$flag);
	 	///// check  master  query are successfully executed
		if ($flag) {
        	mysqli_commit($link1);
        	$msg = "Return and CN is Cancelled successfully with ref. no." .$docid ;
    	} else {
			mysqli_rollback($link1);
			$msg = "Request could not be processed ".$err_msg.". Please try again.";
		} 
		}else {
		//you've sent this already!
		$msg="You have saved this already ";
		$cflag = "warning";
		$cmsg = "Warning";
	}	
    	mysqli_close($link1);
  		///// move to parent page
  		header("Location:direct_return.php?msg=".$msg."".$pagenav);
  		exit;
  	}
}
$res_cn = mysqli_query($link1,"SELECT ref_no FROM credit_note WHERE challan_no='".$po_row["challan_no"]."'");
$row_cn = mysqli_fetch_assoc($res_cn);
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

<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#myTable').dataTable();
});
</script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   	<div class="col-sm-9">
    	<h2 align="center"><i class="fa fa-shopping-basket"></i> Cancel Direct Return</h2>
   		<div class="panel-group">
   		<form id="frm2" name="frm2" class="form-horizontal" action="" method="post" onSubmit="return myConfirm();">
    		<div class="panel panel-default table-responsive">
        		<div class="panel-heading heading1 ">Party Information</div>
		  		<?php if($_REQUEST['msg']){?><br>
      				<h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      			<?php } ?>
        			<div class="panel-body">
         				<table class="table table-bordered" width="100%">
            				<tbody>
              					<tr>
                					<td width="20%"><label class="control-label">Return From:</label></td>
                                    <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['from_location'],"name,city,state",$link1));?></td>
                                    <td width="20%"><label class="control-label">Return To:</label></td>
                                    <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['to_location'],"name,city,state",$link1));?></td>
              					</tr>
              					<tr>
                                    <td><label class="control-label">Invoice No.:</label></td>
                                    <td><?php echo $po_row['challan_no'];?></td>
                                    <td><label class="control-label">Return Date:</label></td>
                                    <td><?php echo $po_row['sale_date'];?></td>
                                </tr>
                            	<tr>
                                    <td><label class="control-label">Reference Invoice No.</label></td>
                                    <td><?php echo $po_row['inv_ref_no'];?></td>
                                    <td><label class="control-label">Reference Invoice Date</label></td>
                                    <td><?php echo $po_row['po_inv_date'];?></td>
                              	</tr>
                                <tr>
                                    <td><label class="control-label">Entry By:</label></td>
                                    <td><?php echo getAdminDetails($po_row['entry_by'],"name",$link1);?></td>
                                    <td><label class="control-label">Remark</label></td>
                                    <td><?php echo $po_row['billing_rmk'];?></td>
                                </tr>
                                <tr>
                                    <td><label class="control-label">Cost Center(Go-Down)</label></td>
                                    <td><?php $subl = getAnyDetails($po_row['sub_location'],"cost_center,sub_location_name","sub_location","sub_location_master",$link1); if($subl){ echo $subl;}else{ echo getAnyDetails($po_row['sub_location'],"name","asc_code","asc_master",$link1);}?></td>
                                    <td><label class="control-label">CN</label></td>
                                    <td><?=$row_cn["ref_no"]?></td>
                              	</tr>
            				</tbody>
          				</table>
      				</div><!--close panel body-->
    			</div><!--close panel-->
    			<div class="panel panel-default table-responsive">
      				<div class="panel-heading heading1 ">Items Information</div>
      				<div class="panel-body">
       					<table class="table table-bordered" width="100%">
            				<thead>
                                <tr class="<?=$tableheadcolor?>">
                                <th style="text-align:center" width="5%">#</th>
                                <th style="text-align:center" width="20%">Product</th>
                                <th style="text-align:center" width="15%">Return Qty</th>
                                <th style="text-align:center" width="15%">Price</th>
                                <th style="text-align:center" width="15%">Value</th>
                                <!--<th style="text-align:center" width="15%">Discount/Unit</th>-->
                                <th width="6%"  style="text-align:center">SGST(%)</th>
                                <th width="6%"  style="text-align:center">SGST Amt</th>
                                <th width="6%"  style="text-align:center">CGST(%)</th>
                                <th width="6%"  style="text-align:center">CGST Amt</th>
                                <th width="6%"  style="text-align:center">IGST(%)</th>
                                <th width="6%"  style="text-align:center">IGST Amt</th>
                                <th style="text-align:center" width="15%">Total</th>
              				</tr>
            			</thead>
            			<tbody>
						<?php
                        $i=1;
                        $podata_sql="SELECT * FROM billing_model_data where challan_no='".$docid."' and from_location='".$to."'";
                        $podata_res=mysqli_query($link1,$podata_sql);
                        while($podata_row=mysqli_fetch_assoc($podata_res)){
                        $proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,model_name,productcode",$link1));
                        ?>
              				<tr>
                                <td><?=$i?></td>
                                <td><?=$proddet[0]." | ".$proddet[1]." | ".$proddet[2]?></td>
                                <td style="text-align:right"><?=$podata_row['qty']?></td>
                                <td style="text-align:right"><?=$podata_row['price']?></td>
                                <td style="text-align:right"><?=$podata_row['value']?></td>
                                <?php /*?><td style="text-align:right"><?=$podata_row['discount']?></td><?php */?>
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
    		<div class="panel panel-default table-responsive">
      			<div class="panel-heading heading1 ">Amount Information</div>
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
                                <td><label class="control-label">&nbsp;</label></td>
                                <td align="right"><?php // echo $po_row['discount_amt'];?></td>
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
      			<div class="panel-heading heading1"> Action </div>
      				<div class="panel-body">
        				<table class="table table-bordered" width="100%">
            				<tbody>
                            	<tr>
                					<td colspan="2" class="red_small">
                                     NOTE: If you cancel this sale return then CN <strong><?=$row_cn["ref_no"]?></strong> against this return will also cancelled parallelly
                  					</td>
                				</tr>              
              					<tr>
                                    <td><label class="control-label">Cancel Remark <span class="red_small">*</span></label></td>
                                    <td><textarea name="remark" id="remark" required class="required form-control" onkeypress = "return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical;width:300px;"></textarea></td>
              					</tr>
								<tr>
                					<td colspan="2" align="center">
                                        <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Cancel" title="" <?php if($_POST['Submit']=='Cancel'){?>disabled<?php }?>>&nbsp;
                                        <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='direct_return.php?<?=$pagenav?>'">               
                  					</td>
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