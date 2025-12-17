<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$from=base64_decode($_REQUEST['from']);
$to=base64_decode($_REQUEST['to']);
$po_sql="SELECT * FROM billing_master where challan_no='".$docid."' and type='PURCHASE RETURN' ";
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
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-reply-all fa-lg"></i> Purchase </h2><br/>
   <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1 ">Party Information</div>
		  <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php } ?>
        <div class="panel-body">
         <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Purchase Return To:</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['from_location'],"name,city,state",$link1));?></td>
                <td width="20%"><label class="control-label">Purchase Return From:</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['to_location'],"name,city,state",$link1));?></td>
              </tr>
              <tr>
                <td><label class="control-label">Invoice No.:</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label">Purchase Return Date:</label></td>
                <td><?php echo $po_row['sale_date'];?></td>
              </tr>
			    <tr>
                <td><label class="control-label">Reference Invoice No.</label></td>
                <td><?php echo $po_row['ref_no'];?></td>
                <td><label class="control-label">Reference Invoice Date</label></td>
                <td><?php echo $po_row['ref_date'];?></td>
              </tr>
               <tr>
                <td><label class="control-label">Entry By:</label></td>
                <td><?php echo getAdminDetails($po_row['entry_by'],"name",$link1);?></td>
                <td><label class="control-label"></label></td>
                <td></td>
              </tr>
             
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
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
                <th style="text-align:center" width="15%">Discount/Unit</th>
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
			$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,productcolor,productcode",$link1));
			?>
              <tr>
                <td><?=$i?></td>
               <td><?=$proddet[0]." | ".$proddet[1]." | ".$proddet[2]?></td>
                <td style="text-align:right"><?=$podata_row['qty']?></td>
                <td style="text-align:right"><?=$podata_row['price']?></td>
                <td style="text-align:right"><?=$podata_row['value']?></td>
                <td style="text-align:right"><?=$podata_row['discount']?></td>
                 <td style="text-align:right"><?=$podata_row['sgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['igst_per']?></td>
                <td style="text-align:right"><?=$podata_row['igst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['totalvalue']?></td>
                
              </tr>
            <?php
			 $tot_val+=$podata_row['value'];
			 $tot_dics+= $podata_row['discount'];
			$tot_qty+=$podata_row['qty'];
			$i++;
			
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1 ">Amount Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                 <td><label class="control-label">Total Discount</label></td>
                <td><?php echo $tot_dics;?></td>
                <td width="20%"><label class="control-label">Total Qty:</label></td>
                <td width="30%"><?php echo $tot_qty;?></td>
              </tr>
              <tr>
              <td><label class="control-label">Total Value</label></td>
                <td><?php echo $tot_val;?></td>
                 <td width="20%"><label class="control-label">Delivery Address:</label></td>
                <td width="30%"><?php echo $po_row['deliv_addrs'];?></td>
              </tr>
               <tr>
                 <td><label class="control-label">Grand Total</label></td>
                <td><?php echo $po_row['total_cost'];?></td>
                <td><label class="control-label">Remark:</label></td>
                <td><?php echo $po_row['billing_rmk'];?></td>
              </tr>
			 
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
  </div><!--close panel group-->
  <div class="row" align="center">
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='purchase_return.php?<?=$pagenav?>'">
  </div>
  <br><br>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>