<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST[id]);
$po_sql="SELECT * FROM vendor_order_master where po_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
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
      <h2 align="center"><i class="fa fa-shopping-basket"></i> Vendor Purchase Order Details</h2><br/>
	 <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
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
                <td><?php echo dt_format($po_row['requested_date']);?></td>
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
    <br><br>
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th width="5%" rowspan="2" style="text-align:center">#</th>
                <th width="20%" rowspan="2" style="text-align:center">Product</th>
                <th width="15%" rowspan="2" style="text-align:center">Req. Qty</th>
                <th width="15%" rowspan="2" style="text-align:center">Price</th>
                <th width="15%" rowspan="2" style="text-align:center">Value</th>
                <th colspan="3" style="text-align:center">Receive Qty</th>
				<th colspan="3" rowspan ="2" style="text-align:center"><?=$imeitag?>Upload</th>
                </tr>
              <tr class="<?=$tableheadcolor?>">
                <th style="text-align:center" width="10%">Ok</th>
                <th style="text-align:center" width="10%">Damage</th>
                <th style="text-align:center" width="10%">Missing</th>
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
				
                <td style="text-align:right"><?=$podata_row[okqty]?></td>
                <td style="text-align:right"><?=$podata_row[damageqty]?></td>
                <td style="text-align:right"><?=$podata_row[missingqty]?></td>
				<td><?php if($po_row['status'] == 'Received' ) {
				if($podata_row['imei_attach'] == 'Y') {
				echo $imeitag."Attached";
				}
			else {	
				?><a href='vendorUploadImei.php?po_no=<?=$podata_row['po_no']?>&reqty=<?=$podata_row['req_qty']?>&prodcode=<?=$podata_row['prod_code']?><?=$pagenav?>' title='Upload<?=$imeitag?>'><i class="fa fa-upload fa-lg" aria-hidden="true"></i></a><?php } }?></td>
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
    <div class="panel panel-info table-responsive">
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
               <tr>
                 <td colspan="4" align="center">
                 	<?php if($_REQUEST["req"]){ $pagelink=$_REQUEST["req"];}else{ $pagelink="vendorPurchaseList";} ?>
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='<?=$pagelink?>.php?<?=$pagenav?>'">
                 </td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
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