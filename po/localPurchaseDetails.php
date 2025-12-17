<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
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
      <h2 align="center"><i class="fa fa-shopping-basket"></i> Local Purchase Details</h2><br/>
	  <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
   <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Purchase Order From</label></td>
                <td width="30%"><i><?php echo getAnyParty($po_row['po_from'],$link1);?></i></td>
                <td width="20%"><label class="control-label">Purchase Order To</label></td>
                <td width="30%"><i><?php echo str_replace("~",",",getLocationDetails($po_row['po_to'],"name,city,state",$link1));?></i></td>
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
                <td><label class="control-label">Document Type</label></td>
                <td><?php if($po_row['document_type']=="DC"){ echo $po_row['document_type']." (".$po_row["ledger_name"].")";}else{ echo $po_row['document_type'];}?></td>
                <td><label class="control-label">Cost Center</label></td>
                <td><?php  
					$billfrom=getLocationDetails($po_row['sub_location'],"name,city,state",$link1);
				  $explodevalf=explode("~",$billfrom);
				  if($explodevalf[0]){ $fromparty=$billfrom; }else{ $fromparty=getAnyDetails($po_row['sub_location'],"sub_location_name","sub_location","sub_location_master",$link1);} echo str_replace("~",",",$fromparty);?></td>
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
                <th width="5%" rowspan="2" style="text-align:center">Req.<br> Qty</th>
                <th width="6%" rowspan="2" style="text-align:center">Price</th>
                <th width="6%" rowspan="2" style="text-align:center">Value</th>
                <th width="6%" rowspan="2" style="text-align:center">SGST(%)</th>
                <th width="6%" rowspan="2" style="text-align:center">SGST Amt</th>
                <th width="6%" rowspan="2" style="text-align:center">CGST(%)</th>
                <th width="6%" rowspan="2" style="text-align:center">CGST Amt</th>
                <th width="6%" rowspan="2" style="text-align:center">IGST(%)</th>
                <th width="6%" rowspan="2" style="text-align:center">IGST Amt</th>
                <th colspan="3" style="text-align:center">Receive Qty</th>
                <th width="6%" rowspan="2" style="text-align:center">Line Total</th>
				<!---<th width="6%" rowspan="2" style="text-align:center">Imei Upload</th>---->
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
                $proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,model_name,productcode",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?php if($podata_row["prod_cat"]=="C"){ echo $podata_row["combo_name"];}else{ echo $proddet[0]." | ".$proddet[1]." | ".$proddet[2];}?></td>
                <td style="text-align:right"><?=$podata_row['req_qty']?></td>
                <td style="text-align:right"><?=$podata_row['po_price']?></td>
                <td style="text-align:right"><?=$podata_row['po_value']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['igst_per']?></td>
                <td style="text-align:right"><?=$podata_row['igst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['okqty']?></td>
                <td style="text-align:right"><?=$podata_row['damageqty']?></td>
                <td style="text-align:right"><?=$podata_row['missingqty']?></td>
				 <td style="text-align:right"><?=$podata_row['totalval']?></td>
				<?php /*?><td><?php if($po_row['status'] == 'Received' ) {
				if($podata_row['imei_attach'] == 'Y') {
				echo "Imei Attached";
				}
			else {	
				?><a href='localUploadImei.php?po_no=<?=$podata_row['po_no']?>&reqty=<?=$podata_row['req_qty']?>&prodcode=<?=$podata_row['prod_code']?><?=$pagenav?>' title='Upload IMEI'><i class="fa fa-upload fa-lg" aria-hidden="true"></i></a><?php } }?></td><?php */?>
               
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
                  <td><label class="control-label">Receive By</label></td>
                 <td><i><?php echo getAdminDetails($po_row['receive_by'],"name",$link1);?></i></td>
                <td width="20%"><label class="control-label">Sub Total</label></td>
                <td width="30%"><?php echo $po_row['po_value'];?></td>
               </tr>
               <tr>
                <td><label class="control-label">Receive Date</label></td>
                 <td><?php echo $po_row['receive_date'];?></td>
                <td><label class="control-label">Round Off</label></td>
                <td><?php echo $po_row['round_off'];?></td>
               </tr>
               <tr>                 
                 <td><label class="control-label">Delivery Address</label></td>
                <td><?php echo $po_row['delivery_address'];?></td>
                 <td><label class="control-label">Grand Total</label></td>
                <td><?=$po_row['total_amt'];?></td>
               </tr>
               <tr>                
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['remark'];?></td>
                <td><label class="control-label">&nbsp;</label></td>
                <td>&nbsp;</td>
               </tr>
               <tr>
                 <td><label class="control-label">Receive Remark</label></td>
                 <td ><?php echo $po_row['receive_remark'];?></td>
				 <?php if($po_row['status']=="Cancelled"){ ?> 
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
                   <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='localPurchaseList.php?<?=$pagenav?>'">
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