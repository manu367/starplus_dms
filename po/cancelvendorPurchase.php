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
	  
	 ///// cancel vpo ///////////
	  $query1=("UPDATE vendor_order_master set status='Cancelled',cancel_by='$_SESSION[userid]',cancel_date='$today',cancel_rmk='$remark',cancel_step='',cancel_ip='$ip' where po_no='".$docid."'");
	  $result = mysqli_query($link1,$query1)or die ("ER1".mysqli_error($link1));
	  
	  //// check if query is not executed
	  if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
      }
	  ////// insert in stock ledger////
	  $vpo_sql="SELECT * FROM vendor_order_data where po_no='".$docid."'";
      $vpo_res=mysqli_query($link1,$vpo_sql);
	  $i=1;
       while($vpo_row=mysqli_fetch_assoc($vpo_res)){
		   ### CASE 1 if user enter somthing in ok qty
		   if($vpo_row['okqty']!="" && $vpo_row['okqty']!=0 && $vpo_row['okqty']!=0.00){
	$flag=stockLedger($vpo_row['po_no'],$today,$vpo_row['prod_code'],$po_row['po_from'],$po_row['po_to'],$po_row['po_to'],"OUT","OK","Cancel Vendor Purchase",$vpo_row['okqty'],$vpo_row['po_price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag); 
               	   
			}
		   
		   ### CASE 2 if user enter somthing in damage qty
		   if($vpo_row[damageqty]!="" && $vpo_row[damageqty]!=0 && $vpo_row[damageqty]!=0.00){
		   $flag=stockLedger($vpo_row['po_no'],$today,$vpo_row['prod_code'],$po_row['po_from'],$po_row['po_to'],$po_row['po_to'],"OUT","DAMAGE"," Cancel Vendor Purchase",$vpo_row[damageqty],$vpo_row['po_price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   }
		   ### CASE 3 if user enter somthing in missing qty
		   if($vpo_row[missingqty]!="" && $vpo_row[missingqty]!=0 && $vpo_row[missingqty]!=0.00){
		      $flag=stockLedger($vpo_row['po_no'],$today,$vpo_row['prod_code'],$po_row['po_from'],$po_row['po_to'],$po_row['po_to'],"OUT","MISSING","Cancel Vendor Purchase",$vpo_row[missingqty],$vpo_row['po_price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   }
		   ///release from stock status/
		   $flag=releaseStock($po_row['po_from'],$po_row['po_from'],$vpo_row['prod_code'],$vpo_row['okqty'],$vpo_row['damageqty'],$vpo_row['missingqty'],$link1,$flag); 
	 $i++;
			  }
		 }/// close if condition 	
	 
	 
     ////// insert in activity table////
	 dailyActivity($_SESSION['userid'],$docid,"VPO","CANCELLED",$ip,$link1,$flag);
	  ///// check  master  query are successfully executed
	 if ($flag) {
        mysqli_commit($link1);
        $msg = "Vendor Purchase Order is  successfully Cancelled with PO no." .$docid ;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
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
        <div class="panel-heading">Party Information</div>
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
      <div class="panel-heading">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr>
                <th width="5%" rowspan="2" style="text-align:center">#</th>
                <th width="20%" rowspan="2" style="text-align:center">Product</th>
                <th width="15%" rowspan="2" style="text-align:center">Req. Qty</th>
                <th width="15%" rowspan="2" style="text-align:center">Price</th>
                <th width="15%" rowspan="2" style="text-align:center">Value</th>
                <th colspan="3" style="text-align:center">Receive Qty</th>
                </tr>
              <tr>
                <th style="text-align:center" width="10%">Ok</th>
                <th style="text-align:center" width="10%">Damage</th>
                <th style="text-align:center" width="10%">Missing</th>
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$cancel=1;
		$podata_sql="SELECT * FROM vendor_order_data where po_no='".$docid."'";
            $podata_res=mysqli_query($link1,$podata_sql);
			  while($podata_row=mysqli_fetch_assoc($podata_res)){
			  $proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,productcolor,productcode",$link1));
				$flag=1;
			$res=mysqli_query($link1,"select sum(okqty) as okstk ,sum(broken) as brokestk ,sum(missing) as missing from stock_status where asc_code='$po_row[po_from]' and partcode='$podata_row[prod_code]'")or die(mysqli_error($link1));		
			   $chk_stk=mysqli_fetch_assoc($res);
			   if(($chk_stk[okstk]>=$podata_row[okqty]) && ($chk_stk[brokestk]>=$podata_row[damageqty])  &&($chk_stk[missing]>=$podata_row[missingqty]) ){ $flag*=1; }else{ $flag*=0;}
			   if($flag==1){ $cancel*=1;}else{ $cancel*=0;}
				
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0]." | ".$proddet[1]." | ".$proddet[2]?></td>
                <td style="text-align:right"><?=$podata_row[req_qty]?></td>
                <td style="text-align:right"><?=$podata_row[po_price]?></td>
                <td style="text-align:right"><?=$podata_row[po_value]?></td>
                <td style="text-align:right"><?=$podata_row[okqty]?></td>
                <td style="text-align:right"><?=$podata_row[damageqty]?></td>
                <td style="text-align:right"><?=$podata_row[missingqty]?></td>
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
      <div class="panel-heading">Amount Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Sub Total</label></td>
                <td width="30%"><?php echo $po_row['po_value'];?></td>
                <td width="20%"><label class="control-label">Tax Amount</label></td>
                <td width="30%"><?php echo $po_row['taxamount'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Grand Total</label></td>
                <td><?php echo currencyFormat($po_row['po_value']+$po_row['taxamount']);?></td>
                <td><label class="control-label">Tax Type</label></td>
                <td><?php echo $po_row['taxtype']; if($po_row['taxper']!=0){ echo " @ ".$po_row['taxper']."%";}?></td>
              </tr>
               <tr>
                <td><label class="control-label">Delivery Address</label></td>
                <td><?php echo $po_row['delivery_address'];?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['remark'];?></td>
              </tr>
               <tr>
                 <td><label class="control-label">Receive By</label></td>
                 <td><i><?php echo getAdminDetails($po_row['receive_by'],"name",$link1);?></i></td>
                 <td><label class="control-label">Receive Date</label></td>
                 <td><?php echo $po_row['receive_date'];?></td>
               </tr>
               <tr>
                 <td><label class="control-label">Receive Remark</label></td>
                 <td colspan="3"><?php echo $po_row['receive_remark'];?></td>
                </tr>
				 <tr>
                 <td><label class="control-label">Cancel Remark <span style="color:#F00">*</span></label></td>
                 <td colspan="3"><textarea name="remark" id="rcv_rmk" class="form-control required" style="resize:none;width:500px;" required></textarea></td>
                 </tr>
               <tr>
                 <td colspan="4" align="center">
                     <?php if($cancel==1){?><input type="submit" class="btn btn-primary" name="Submit" id="upd" value="Cancel" ><?php }else{ echo "Stock is not available";}?>
                    
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