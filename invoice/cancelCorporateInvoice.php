<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$inv_sql="SELECT * FROM billing_master  where challan_no='".$docid."'";
$inv_res=mysqli_query($link1,$inv_sql);
$inv_row=mysqli_fetch_assoc($inv_res);
////// final submit form ////
@extract($_POST);
if($_POST){
  if($_POST['Submit']=='Cancel'){
	  mysqli_autocommit($link1, false);
	  $flag = true;
	  $err_msg ="";
	  ///// cancel corporate invoice ///////////
	  $query1=("UPDATE billing_master set status='Cancelled',cancel_by='".$_SESSION['userid']."',cancel_date='$today',cancel_rmk='$remark',cancel_step='After ".$inv_row['status']."',cancel_ip='$ip' where challan_no='".$docid."'");
	  $result = mysqli_query($link1,$query1);
	  //// check if query is not executed
	  if (!$result) {
	     $flag = false;
         $err_msg = "Error Code1:" . mysqli_error($link1) . ".";
      }
	  $vpo_sql="SELECT * FROM billing_model_data where challan_no='".$docid."'";
	  $vpo_res=mysqli_query($link1,$vpo_sql);
	  while($vpo_row=mysqli_fetch_assoc($vpo_res)){
	     //// update stock of from loaction
	     $result=mysqli_query($link1, "update stock_status set okqty=okqty+'".$vpo_row['qty']."',updatedate='".$datetime."' where asc_code='".$inv_row['from_location']."' and partcode='".$vpo_row['prod_code']."'");
		//// check if query is not executed
		if (!$result) {
		   $flag = false;
		   $err_msg = "Error Code2:" . mysqli_error($link1) . ".";
		 }
		 ///// insert in stock ledger////
		 $flag=stockLedger($docid,$today,$vpo_row['prod_code'],$inv_row['from_location'],$inv_row['to_location'],$inv_row['from_location'],"IN","OK","Cancel Corporate Invoice",$vpo_row['qty'],$vpo_row['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);	 
	  }/// close for loop
	  /////// check if imei is attached then it should also cancelled or reverse to the from location
	   if($inv_row['imei_attach']=="Y"){
		      $result=mysqli_query($link1, "delete from billing_imei_data where doc_no='".$docid."'");
		      //// check if query is not executed
			  if (!$result) {
				 $flag = false;
				 echo "Error Code2.0: " . mysqli_error($link1) . ".";
			  }
	   }
   //// update cr bal 
   $result=mysqli_query($link1,"update current_cr_status set cr_abl=cr_abl+'".$grand_total."',total_cr_limit=total_cr_limit+'".$grand_total."', last_updated='".$datetime."' where parent_code='".$inv_row['from_location']."' and asc_code='".$inv_row['to_location']."'");
	/// check if query is not executed
	if (!$result) {
	$flag = false;
	$err_msg = "Error Code3:" . mysqli_error($link1) . ".";
	}	
	/// insert in party ledger
    $flag=partyLedger($inv_row['from_location'],$inv_row['to_location'],$docid,$today,$today,$currtime,$_SESSION['userid'],"CANCEL CORPORATE INVOICE",$grand_total,"CR",$link1,$flag);  
    ////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$docid,"CORPORATE INVOICE","CANCEL",$ip,$link1,$flag);
	 ///// check  master  query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Invoice is Cancelled successfully with ref. no." .$docid ;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed ".$err_msg.". Please try again.";
	} 
    mysqli_close($link1);
  ///// move to parent page
  header("Location:corporateInvoiceList.php?msg=".$msg."".$pagenav);
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

 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
});
</script>
<script type="text/javascript" src="../js/common_js.js"></script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-shopping-basket"></i>Cancel Corporate Invoice Details</h2><br/>
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post" onSubmit="return myConfirm();">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Invoice To</label></td>
                <td width="30%"><i><?php echo str_replace("~",",",getLocationDetails($inv_row['to_location'],"name,city,state",$link1));?></i></td>
                <td width="20%"><label class="control-label">Invoice From</label></td>
                <td width="30%"><i><?php echo str_replace("~",",",getLocationDetails($inv_row['from_location'],"name,city,state",$link1));?></i></td>
              </tr>
              <tr>
                <td><label class="control-label">Invoice No.</label></td>
                <td><?php echo $inv_row['challan_no'];?></td>
                <td><label class="control-label">Invoice Date</label></td>
                <td><?php echo $inv_row['sale_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">PO No.</label></td>
                <td><?php echo $inv_row['po_no'];?></td>
                <td><label class="control-label">Ref. No.</label></td>
                <td><?php echo $inv_row['ref_no'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo getAdminDetails($inv_row['entry_by'],"name",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $inv_row['status'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Delivery Address</label></td>
                <td><?php echo $inv_row['deliv_addrs'];?></td>
                <td><label class="control-label">Dispatch Address</label></td>
                <td><?php echo $inv_row['disp_addrs'];?></td>
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
                <th style="text-align:center" width="3%">#</th>
                <th style="text-align:center" width="11%">Product</th>
                <th style="text-align:center" width="8%">Bill Qty</th>
                <th style="text-align:center" width="8%">Price</th>
                <th style="text-align:center" width="8%">Value</th>
                <th style="text-align:center" width="8%">Discount</th>
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
			$invdata_sql="SELECT * FROM billing_model_data where challan_no='".$docid."'";
			$invdata_res=mysqli_query($link1,$invdata_sql);
			while($invdata_row=mysqli_fetch_assoc($invdata_res)){
				$discount_val = number_format(($invdata_row['value'] - ($invdata_row['discount']*$invdata_row['qty'])),'2','.','');
				$proddet=explode("~",getProductDetails($invdata_row['prod_code'],"productname,productcolor",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0]." (".$proddet[1].")"?></td>
                <td style="text-align:right"><?=$invdata_row['qty']?></td>
                <td style="text-align:right"><?=$invdata_row['price']?></td>
                <td style="text-align:right"><?=$invdata_row['value']?></td>
                <td style="text-align:right"><?=$invdata_row['discount']?></td>
                <td style="text-align:right"><?=$discount_val?></td>
                <td style="text-align:left"><?=$invdata_row['sgst_per']?></td>
                <td style="text-align:right"><?=$invdata_row['sgst_amt']?></td>
                <td style="text-align:left"><?=$invdata_row['cgst_per']?></td>
                <td style="text-align:right"><?=$invdata_row['cgst_amt']?></td>
                <td style="text-align:left"><?=$invdata_row['igst_per']?></td>
                <td style="text-align:right"><?=$invdata_row['igst_amt']?></td>
                <td style="text-align:right"><?=$invdata_row['totalvalue']?></td>
              </tr>
            <?php
			$tot_sgst_amt+=$invdata_row['sgst_amt'];
            $tot_cgst_amt+=$invdata_row['cgst_amt'];
            $tot_igst_amt+=$invdata_row['igst_amt'];
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
                <td><label class="control-label">Discount Type</label></td>
                <td><?php echo getDiscountType($inv_row['discountfor']);?></td>
                <td width="20%"><label class="control-label">Sub Total</label></td>
                <td width="30%" align="right"><?php echo $inv_row['basic_cost'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Total SGST</label></td>
                <td><?=$tot_sgst_amt ?></td>
                <td><label class="control-label">Discount</label></td>
                <td align="right"><?php echo $inv_row['discount_amt'];?></td>
              </tr>
               <tr>
                 <td><label class="control-label">Total CGST</label></td>
                 <td><?=$tot_cgst_amt ?></td>
                 <td><label class="control-label">GST</label></td>
                 <td align="right"><?php echo ($tot_sgst_amt+$tot_cgst_amt+$tot_igst_amt);?></td>
               </tr>
               <tr>
                 <td><label class="control-label">Total IGST</label></td>
                 <td><?=$tot_igst_amt ?></td>
                 <td><label class="control-label">Grand Total</label></td>
                 <td align="right"><?php echo $inv_row['total_cost'];?><input type="hidden" name="grand_total" id="grand_total" value="<?=$inv_row['total_cost'];?>" class="form-control"  readonly/></td>
               </tr>
               <tr>
               	
                <td><label class="control-label">Remark</label></td>
                <td colspan="3"><?php echo $inv_row['billing_rmk'];?></td>
                </tr>
              
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Logistic Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Logistic Name</label></td>
                <td width="30%"><?php echo getLogistic($inv_row['diesel_code'],$link1);?></td>
                <td width="20%"><label class="control-label">Docket No.</label></td>
                <td width="30%"><?php echo $inv_row['docket_no'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Logistic Person</label></td>
                <td><?php echo $inv_row['logistic_person'];?></td>
                <td><label class="control-label">Contact No.</label></td>
                <td><?php echo $inv_row['logistic_contact'];?></td>
              </tr>
               <tr>
                 <td><label class="control-label">Carrier No.</label></td>
                 <td><?php echo $inv_row['vehical_no'];?></td>
                 <td><label class="control-label">Dispatch Date</label></td>
                 <td><?php echo $inv_row['dc_date'];?></td>
               </tr>
               <tr>
                <td><label class="control-label">Dispatch Remark</label></td>
                <td colspan="3"><?php echo $inv_row['disp_rmk'];?></td>
                </tr>
              
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
	<div class="panel panel-default table-responsive">
      <div class="panel-heading heading1"> Action </div>
      <div class="panel-body">
       
        <table class="table table-bordered" width="100%">
            <tbody>
              
              <tr>
                <td><label class="control-label">Cancel Remark <span class="red_small">*</span></label></td>
                <td><textarea name="remark" id="remark" required class="required form-control" onkeypress = "return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical;width:300px;"></textarea></td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Cancel" title="" <?php if($_POST['Submit']=='Cancel'){?>disabled<?php }?>>&nbsp;
                  <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='corporateInvoiceList.php?<?=$pagenav?>'">
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