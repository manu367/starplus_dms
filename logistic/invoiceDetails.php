<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$inv_sql="SELECT * FROM billing_master where challan_no='".$docid."'";
$inv_res=mysqli_query($link1,$inv_sql);
$inv_row=mysqli_fetch_assoc($inv_res);
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
      <h2 align="center"><i class="fa fa-shopping-basket"></i> Invoice Details</h2><br/>
   <div class="panel-group">
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
                <th style="text-align:center" width="5%">#</th>
                <th style="text-align:center" width="20%">Product</th>
                <th style="text-align:center" width="15%">Bill Qty</th>
                <th style="text-align:center" width="15%">Price</th>
                <th style="text-align:center" width="15%">Value</th>
                <th style="text-align:center" width="15%">Discount/Unit</th>
                <th style="text-align:center" width="10%">After Discount Value</th>
                <th style="text-align:center" width="10%">SGST (%)</th>
                <th style="text-align:center" width="10%">SGST Amt</th>
                <th style="text-align:center" width="10%">CGST (%)</th>
                <th style="text-align:center" width="10%">CGST Amt</th>
                <th style="text-align:center" width="10%">IGST (%)</th>
                <th style="text-align:center" width="10%">IGST Amt</th>
                <th style="text-align:center" width="15%">Total</th>
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
				$proddet=explode("~",getProductDetails($invdata_row['prod_code'],"productname,model_name",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0]." | ".$proddet[1]." (".$invdata_row['prod_code'].")"?></td>
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
                 <td align="right"><?php echo $inv_row['total_cost'];?></td>
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
    <div class="row">
    	<div class="col-sm-12" style="text-align:center;">
                 <td colspan="4" align="center"><input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='updateInvLogistic.php?<?=$pagenav?>'"></td>
        </div>
    </div>
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