<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM purchase_order_master where po_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql)or die("er1".mysql_error());
$po_row=mysqli_fetch_assoc($po_res);
$sql="SELECT * FROM purchase_order_data where po_no='".$docid."'";
$po_result=mysqli_query($link1,$sql);
$po=mysqli_fetch_assoc($po_result);
////// final submit form ////
@extract($_POST);
if($_POST){
  if($_POST['Submit']=='Cancel'){
	  mysqli_autocommit($link1, false);
	  $flag = true;
	  
	  ///// cancel po ///////////
	 $query1=("UPDATE purchase_order_master set status='Cancelled',cancel_by='$_SESSION[userid]',cancel_date='$today',cancel_rmk='$remark',cancel_step='',cancel_ip='$ip' where po_no='".$docid."'");
	  $result = mysqli_query($link1,$query1)or die ("ER1".mysqli_error($link1));
	  
	  //// check if query is not executed
	  if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
      }
	 ////// release the PO qty in stock ////
	 //$flag=releaseStockQty($po_row[po_to],$po['prod_code'],$po['req_qty'],$link1,$flag); 
	 
  }/// close for loop
    ////// insert in activity table////
	 dailyActivity($_SESSION['userid'],$docid,"CPO","CANCELLED",$ip,$link1,$flag);
	 ///// check  master  query are successfully executed
	 if ($flag) {
        mysqli_commit($link1);
        $msg = "Purchase Order is Cancelled successfully with PO no." .$docid ;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	
  
  ///// move to parent page
  header("Location:comboPurchaseOrderList.php?msg=".$msg."".$pagenav);
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

<script type="text/javascript">

$(document).ready(function(){
        $("#frm1").validate();
});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-shopping-basket"></i>&nbsp;&nbsp;Cancel Combo Purchase Order </h2><br/>
   <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">   
   <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Party Information</div>
        <div class="panel-body">
         <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Purchase Order To</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['po_to'],"name,city,state",$link1));?></td>
                <td width="20%"><label class="control-label">Purchase Order From</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['po_from'],"name,city,state",$link1));?></td>
              </tr>
              <tr>
                <td><label class="control-label">Purchase Order No.</label></td>
                <td><?php echo $po_row['po_no'];?></td>
                <td><label class="control-label">Purchase Order Date</label></td>
                <td><?php echo $po_row['requested_date'];?></td>
              </tr>
               <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo getAdminDetails($po_row['create_by'],"name",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $po_row['status'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Discount Type</label></td>
                <td><?php echo $po_row['discount_type'];?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
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
                <th style="text-align:center" width="20%">Combo</th>
                <th style="text-align:center" width="15%">Req. Qty</th>
                <th style="text-align:center" width="15%">Price</th>
                <th style="text-align:center" width="15%">Value</th>
                <th style="text-align:center" width="15%">Discount/Unit</th>
                <th style="text-align:center" width="15%">Total</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM purchase_order_data where po_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
			?>
              <tr>
                <td><?=$i?></td>
                <td><?php $data = getAnyDetails($podata_row['prod_code'],"bom_modelname,bom_hsn","bom_modelcode","combo_master",$link1); 
						  $d = explode('~', $data); 
						  echo $d[0].' | '.$podata_row['prod_code'];?></td>
                <td style="text-align:right"><?=$podata_row['req_qty']?></td>
                <td style="text-align:right"><?=$podata_row['po_price']?></td>
                <td style="text-align:right"><?=$podata_row['po_value']?></td>
                <td style="text-align:right"><?=$podata_row['discount']?></td>
                <td style="text-align:right"><?=$podata_row['totalval']?></td>
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
                <td width="30%"><?php echo $po_row['po_value'];?></td>
                <td width="20%"><label class="control-label">Total Discount</label></td>
                <td width="30%"><?php echo $po_row['discount'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Grand Total</label></td>
                <td><?php echo ($po_row['po_value']-$po_row['discount']);?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
               <tr>
                <td><label class="control-label">Delivery Address</label></td>
                <td><?php echo $po_row['delivery_address'];?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['remark'];?></td>
              </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
  <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1"></div>
      <div class="panel-body">
       
        <table class="table table-bordered" width="100%">
            <tbody>
              
              <tr>
                <td><label class="control-label">Cancel Remark <span class="red_small">*</span></label></td>
                <td><textarea name="remark" id="remark" required class="required form-control" onkeypress = "return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical;width:300px;"></textarea></td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Cancel" title="" <?php if($_POST['Submit']=='Cancel'){?>disabled<?php }?>>&nbsp;
                  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='comboPurchaseOrderList.php?<?=$pagenav?>'">
                  </td>
                </tr>
            </tbody>
          </table>
         
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
  </div><!--close panel group-->
  </form>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>