<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST[id]);
$po_sql="SELECT * FROM billing_master where challan_no='".$docid."'";
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
      <h2 align="center"><i class="fa fa-shopping-basket"></i> Purchase Order Details</h2><br/>
   <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                
                <td width="20%"><label class="control-label">Stock Transfer From</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['from_location'],"name,city,state",$link1));?></td>
                <td width="20%"><label class="control-label">Stock Transfer To</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['to_location'],"name,city,state",$link1));?></td>
              </tr>
              <tr>
                <td><label class="control-label">Document No.</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label">Document Date</label></td>
                <td><?php echo $po_row['sale_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo getAdminDetails($po_row['entry_by'],"name",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $po_row['status'];?></td>
              </tr>
			  <tr>
                <td><label class="control-label">Document Type</label></td>
                <td><?php echo $po_row['document_type'];?></td>
                <td><label class="control-label"></label></td>
                <td></td>
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
              <tr>
                <th style="text-align:center" width="5%">#</th>
                <th style="text-align:center" width="20%">Product</th>
                <th style="text-align:center" width="6%">Req. Qty</th>
                <th style="text-align:center" width="6%">Price</th>
                <th style="text-align:center" width="6%">Value</th>
				<th style="text-align:center" width="6%">Sgst Per(%)</th>
				<th style="text-align:center" width="6%">Sgst Amt</th>
				<th style="text-align:center" width="6%">Cgst Per(%)</th>
				<th style="text-align:center" width="6%">Cgst Amt</th>
				<th style="text-align:center" width="6%">Igst Per(%)</th>
				<th style="text-align:center" width="6%">Igst Amt</th>
                <th style="text-align:center" width="15%">Total</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM billing_model_data where challan_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
			?>
              <tr>
                <td><?=$i?></td>
                <td><?php $data = getProductDetails($podata_row['prod_code'],"productname,productcolor,productcode",$link1); $d = explode('~', $data); echo $d[0].' | '.$d[1].' | '.$d[2];?></td>
                <td style="text-align:right"><?=$podata_row['qty']?></td>
                <td style="text-align:right"><?=$podata_row['price']?></td>
                <td style="text-align:right"><?=$podata_row['value']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_per']?></td>
				<td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
				<td style="text-align:right"><?=$podata_row['cgst_per']?></td>
				<td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
				<td style="text-align:right"><?=$podata_row['igst_per']?></td>
				<td style="text-align:right"><?=$podata_row['igst_amt']?></td>
				<td style="text-align:right"><?=$podata_row['totalvalue']?></td>
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
                <td width="20%"><label class="control-label">Sub Total</label></td>
                <td width="30%"><?php echo $po_row['basic_cost'];?></td>
                <td width="20%"><label class="control-label">Grand Total</label></td>
                <td><?php echo ($po_row['total_cost']);?></td>
              </tr>
              
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
       
  </div><!--close panel group-->
  <div class="row" align="center">
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='stndistribution_list.php?<?=$pagenav?>'">
  </div>
  <br><br>
 </div>
   <!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>