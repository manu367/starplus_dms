<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST[id]);
$po_sql="SELECT * FROM vendor_order_master where po_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);

////// final submit form ////
@extract($_POST);
if($_POST){
	if($_POST[Submit]=='Cancel'){
		mysqli_autocommit($link1, false);
	  	$flag = true;
		$error_msg = "";
		/////// define array for update margin after each import
		$arr_imptpart = array();
	 	####### first check whether grn  document is created fro this po or not , if not then po is cancelled  ########################################3
	 	$check = mysqli_query($link1 , "select challan_no from billing_master  where ref_no = '".$docid."' ");
	 	if(mysqli_num_rows($check) == 0) {
	 		///// cancel vpo ///////////
	  		$query1=("UPDATE vendor_order_master set status='Cancelled',cancel_by='$_SESSION[userid]',cancel_date='$today',cancel_rmk='$remark',cancel_step='',cancel_ip='$ip' where po_no='".$docid."'");
	  		$result = mysqli_query($link1,$query1)or die ("ER1".mysqli_error($link1));
			//// check if query is not executed
			if (!$result) {
				$flag = false;
				$error_msg = "Error details: " . mysqli_error($link1) . ".";
			}else{
				///// make partcode array
				$sql_po = "SELECT prod_code FROM vendor_order_data where po_no='".$docid."'";
				$res_po = mysqli_query($link1,$sql_po);
				while($row_po = mysqli_fetch_assoc($res_po)){
					$arr_imptpart[] = $row_po["prod_code"];
				}
			}
        }
		else {
			///// move to parent page
			$msg  = "PO cannot be cancelled, as GRN Dcoument is created for this PO";
			header("Location:vendorPurchaseList.php?msg=".$msg."".$pagenav);
			exit;						 
		}      
	}/// close if condition 	
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$docid,"VPO","CANCELLED",$ip,$link1,$flag);
	///// check  master  query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Vendor Purchase Order is  successfully Cancelled with PO no." .$docid ;
		///// include auto run script for each partcode of this PO so that margin can update accordingly
		include("update_margin_after_import.php");
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
    mysqli_close($link1);
  	///// move to parent page
	header("Location:vendorPurchaseList.php?msg=".$msg."".$pagenav);
	exit;
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
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-ship"></i>Cancel Vendor Purchase</h2><br/>
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Purchase Order To</label></td>
                <td width="30%"><i><?php echo str_replace("~",",",getVendorDetails($po_row['po_to'],"name,city,state",$link1));?></i></td>
                <td width="20%"><label class="control-label">Purchase Order From</label></td>
                <td width="30%"><i><?php echo str_replace("~",",",getLocationDetails($po_row['po_from'],"name,city,state",$link1));?></i></td>
              </tr>
              <tr>
                <td><label class="control-label">Purchase Order No.</label></td>
                <td><?php echo $po_row['po_no'];?></td>
                <td><label class="control-label">Purchase Order Date</label></td>
                <td><?php echo $po_row['requested_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><i><?php echo getAdminDetails($po_row['create_by'],"name",$link1);?></i></td>
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
                <td><label class="control-label">Currency Type</label></td>
                <td><?php echo $po_row['currency_type'];?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
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
              <tr>
                <th width="4%"  style="text-align:center">S.No.</th>
                <th width="16%"  style="text-align:center">Product</th>
				<th width="8%"  style="text-align:center">Req. Qty</th>
				<th width="7%"  style="text-align:center">Purchase Price</th>
                <th width="8%"  style="text-align:center">Value</th>
				<?php if($po_row['total_igst_amt'] == '0.00') {?>
                <th width="7%" style="text-align:center">CGST (%)</th>
				<th width="9%" style="text-align:center">CGST Amt</th>
				<th width="7%" style="text-align:center">SGST (%)</th>
				<th  width="8%"style="text-align:center">SGST Amt</th>
				<?php } else {?>
				<th width="7%" style="text-align:center">IGST (%)</th>
				<th  width="8%"style="text-align:center">IGST Amt</th>
				<?php }?>
				<th  width="11%"style="text-align:center">Total Amt</th>
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM vendor_order_data where po_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
			$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,productcolor,productcode",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                 <td><?=$proddet[0]." | ".$proddet[1]." | ".$proddet[2]?></td>
				 <td style="text-align:right"><?=$podata_row[req_qty]?></td>
				<td style="text-align:right"><?=$podata_row[po_price]?></td>
                <td style="text-align:right"><?=$podata_row[po_value]?></td>
				<?php if($po_row['total_igst_amt'] == '0.00') {?>				
                <td style="text-align:right"><?=$podata_row[cgst_per]?></td>
                <td style="text-align:right"><?=$podata_row[cgst_amt]?></td>
                <td style="text-align:right"><?=$podata_row[sgst_per]?></td>
				<td style="text-align:right"><?=$podata_row[sgst_amt]?></td>
				<?php } else {?>
				<td style="text-align:right"><?=$podata_row[igst_per]?></td>
				<td style="text-align:right"><?=$podata_row[igst_amt]?></td>
				<?php }?>
				<td style="text-align:right"><?=$podata_row[totalval]?></td>
                </tr>
            <?php
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
                <td width="20%" rowspan="2"><label class="control-label">Delivery Address</label></td>
                <td width="30%" rowspan="2"><?php echo $po_row['delivery_address'];?></td>
                <td width="20%"><label class="control-label">Sub Total </label></td>
                <td width="30%" align="right"><?php echo $po_row['po_value'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Tax Amount</label></td>
                <td align="right"><?php echo currencyFormat($po_row['total_sgst_amt']+$po_row['total_cgst_amt']+$po_row['total_igst_amt']);?></td>
              </tr>
               <tr>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['remark'];?></td>
                <td><label class="control-label">Grand Total</label></td>
                <td align="right"><?php echo currencyFormat($po_row['grand_total']);?></td>
              </tr>
               <tr>
                 <td><label class="control-label">Receive By</label></td>
                 <td><i><?php echo getAdminDetails($po_row['receive_by'],"name",$link1);?></i></td>
                 <td><label class="control-label">Receive Date</label></td>
                 <td><?php echo $po_row['receive_date'];?></td>
               </tr>
               <tr>
                 <td><label class="control-label">Receive Remark</label></td>
                 <td ><?php echo $po_row['receive_remark'];?></td>
				 <?php if($po_row[status]=="Cancelled"){ ?> 
				 <td><label class="control-label">Cancel By</label></td>
                 <td><?php echo $po_row['cancel_by'];?></td>
                </tr>
				<tr>
                 <td><label class="control-label">Cancel Date</label></td>
                 <td ><?php echo $po_row['cancel_date'];?></td>
				 <td><label class="control-label">Cancel Remark</label></td>
                 <td ><?php echo $po_row['cancel_rmk'];?></td>
                </tr>
				
				 <?php }?>
                 <?php if($po_row['status'] != 'Cancelled') {?>
				<tr>
                 <td><label class="control-label">Cancel Remark <span style="color:#F00">*</span></label></td>
                 <td colspan="3"><textarea name="remark" id="rcv_rmk" class="form-control required" style="resize:none;width:500px;" required></textarea></td>
                 </tr>
                 <?php }?>
               <tr>
                 <td colspan="4" align="center">
                    <?php if($po_row['status'] != 'Cancelled') {?><input type="submit" class="btn btn-primary" name="Submit" id="upd" value="Cancel" ><?php }?>
                    
                   <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='vendorPurchaseList.php?<?=$pagenav?>'">
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