<?php
////// Function ID ///////
$fun_id = array("u"=>array(9)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM opening_stock_master where doc_no='".$docid."'";
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
      <h2 align="center"><i class="fa fa-cubes"></i> Opening Stock Challan Details</h2><br/>
   <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Location Name</label></td>
                <td colspan="3"><i><?php echo str_replace("~",",",getLocationDetails($po_row['location_code'],"name,city,state",$link1));?></i></td>
                </tr>
              <tr>
                <td><label class="control-label">Opening Challan</label></td>
                <td width="30%"><?php echo $po_row['doc_no'];?></td>
                <td width="20%"><label class="control-label">Challan Date</label></td>
                <td width="30%"><?php echo $po_row['requested_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><i><?php echo getAdminDetails($po_row['create_by'],"name",$link1);?></i></td>
                <td><label class="control-label">Entry Date</label></td>
                <td><?php echo $po_row['entry_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Ref. No.</label></td>
                <td><?php echo $po_row['ref_no'];?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $po_row['status'];?></td>
              </tr>
              <?php if($po_row['cancel_by']){?>
              <tr>
                <td><label class="control-label">Cancel By</label></td>
                <td><i><?php echo getAdminDetails($po_row['cancel_by'],"name",$link1);?></i></td>
                <td><label class="control-label">Cancel Date</label></td>
                <td><?php echo $po_row['cancel_date'];?></td>
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
    <br><br>
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr>
                <th width="5%" style="text-align:center">#</th>
                <th width="20%" style="text-align:center">Product</th>
                <th width="15%" style="text-align:center">Ok Qty</th>
                <th width="15%" style="text-align:center">Damage Qty</th>
                <th width="15%" style="text-align:center">Missing Qty</th>
                <th width="15%" style="text-align:center">Price</th>
                <th width="15%" style="text-align:center">Value</th>
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM opening_stock_data where doc_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,productcolor",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0]." (".$proddet[1].") - ".$podata_row['prod_code']?></td>
                <td style="text-align:right"><?=$podata_row['okqty']?></td>
                <td style="text-align:right"><?=$podata_row['damageqty']?></td>
                <td style="text-align:right"><?=$podata_row['missingqty']?></td>
                <td style="text-align:right"><?=$podata_row['price']?></td>
                <td style="text-align:right"><?=$podata_row['value']?></td>
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
                <td width="20%"><label class="control-label">Total Amount</label></td>
                <td width="30%"><?php echo $po_row['stock_value'];?></td>
                <td width="20%"><label class="control-label">Remark</label></td>
                <td width="30%"><?php echo $po_row['remark'];?></td>
              </tr>
               <tr>
                 <td colspan="4" align="center">
                   <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='openingStockList.php?<?=$pagenav?>'">
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