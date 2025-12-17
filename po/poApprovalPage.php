<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM purchase_order_master where po_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql)or die("er1".mysqli_error($link1));
$po_row=mysqli_fetch_assoc($po_res);
////// final submit form ////
@extract($_POST);
if($_POST){
  if($_POST['Submit']=='Update'){
	  $decodepono=base64_decode($refno);
	  $decodepodt=base64_decode($refdate);
	  ///// update po status ///////////
	  mysqli_query($link1,"UPDATE purchase_order_master set status='".$actiontaken."' where po_no='".$decodepono."'")or die("ER1".mysqli_error($link1));
	  ////// insert in approval table////
	  if($po_row["req_type"]=="COMBO PO"){ $typ = "CPO";}else{ $typ = "PO";}
	 approvalActivity($decodepono,$decodepodt,$typ,$_SESSION['userid'],$actiontaken,$today,$currtime,$remark,$ip,$link1,"");
     ////// insert in activity table////
	 dailyActivity($_SESSION['userid'],$decodepono,$typ." APPROVAL","APPROVAL",$ip,$link1,"");
	 ////// return message
	 $msg="You have successfully taken approval action for PO ".$loccode;
  }else{
	 ////// return message
	 $msg="Something went wrong. Please try again.";
  }
  ///// move to parent page
  header("Location:purchaseOrderApproval.php?msg=".$msg."&fdate=".base64_decode($_REQUEST['fdate'])."&tdate=".base64_decode($_REQUEST['tdate'])."&from_state=".base64_decode($_REQUEST['from_state'])."&from_location=".base64_decode($_REQUEST['from_location'])."&to_location=".base64_decode($_REQUEST['to_location'])."&status=".base64_decode($_REQUEST['status'])."".$pagenav);
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
      <h2 align="center"><i class="fa fa-address-card"></i> Purchase Order Approval</h2><br/>
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
                <td><?php echo getDiscountType($po_row['discount_type']);?></td>
                <td><label class="control-label">PO Type</label></td>
                <td><?php echo $po_row['req_type'];?></td>
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
              <tr class="<?=$tableheadcolor?>" >
                <th style="text-align:center" width="5%">#</th>
                <th style="text-align:center" width="30%">Product</th>
                <th style="text-align:center" width="15%">Req. Qty</th>
                <th style="text-align:center" width="15%">Price</th>
                <th style="text-align:center" width="15%">Value</th>
                <th style="text-align:center" width="15%">Discount</th>
                <th style="text-align:center" width="15%">Total</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM purchase_order_data where po_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				if($po_row["req_type"]=="COMBO PO"){
					$proddet=explode("~",getAnyDetails($podata_row['prod_code'],"bom_modelname,bom_hsn","bom_modelcode","combo_master",$link1));
					$str = $proddet[0]." | ".$podata_row['prod_code'];
				}else{
					$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,model_name,productcode",$link1));
					$str = $proddet[0]." | ".$proddet[1]." | ".$proddet[2];
				}
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$str?></td>
                <td style="text-align:right"><?=round($podata_row['req_qty'])?></td>
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
      <div class="panel-heading heading1">Approval Action</div>
      <div class="panel-body">
        <?php if($po_row['status']=="PFA"){ ?>
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="50%"><label class="control-label">Action Taken <span class="red_small">*</span></label></td>
                <td width="50%">
                 <select name="actiontaken" id="actiontaken" class="required form-control" required style="width:300px;">
                  <option value="Approved">Approved</option>
                  <option value="Rejected">Rejected</option>
                </select>
                </td>
              </tr>
              <tr>
                <td><label class="control-label">Remark <span class="red_small">*</span></label></td>
                <td><textarea name="remark" id="remark" required class="required form-control" onkeypress = "return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical;width:300px;"></textarea></td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Update" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>&nbsp;
                  <input name="refno" id="refno" type="hidden" value="<?=base64_encode($po_row['po_no'])?>"/>
                  <input name="refdate" id="refdate" type="hidden" value="<?=base64_encode($po_row['requested_date'])?>"/>
                  <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='purchaseOrderApproval.php?fdate=<?=base64_decode($_REQUEST['fdate'])?>&tdate=<?=base64_decode($_REQUEST['tdate'])?>&from_state=<?=base64_decode($_REQUEST['from_state'])?>&from_location=<?=base64_decode($_REQUEST['from_location'])?>&to_location=<?=base64_decode($_REQUEST['to_location'])?>&status=<?=base64_decode($_REQUEST['status'])?><?=$pagenav?>'">
                  </td>
                </tr>
            </tbody>
          </table>
          <?php }else{ ?>
          <table class="table table-bordered" width="100%"> 
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th width="20%">Action Date & Time</th>
                <th width="30%">Action Taken By</th>
                <th width="20%">Action</th>
                <th width="30%">Action Remark</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$res_poapp=mysqli_query($link1,"SELECT * FROM approval_activities where ref_no='".$po_row['po_no']."'")or die("ERR1".mysqli_error($link1)); 
			while($row_poapp=mysqli_fetch_assoc($res_poapp)){
			?>
              <tr>
                <td><?php echo $row_poapp['action_date']." ".$row_poapp['action_time'];?></td>
                <td><?php echo getAdminDetails($row_poapp['action_by'],"name",$link1);?></td>
                <td><?php echo $row_poapp['action_taken']?></td>
                <td><?php echo $row_poapp['action_remark']?></td>
              </tr>
              <tr>
                <td colspan="4" align="center"><input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='purchaseOrderApproval.php?fdate=<?=base64_decode($_REQUEST['fdate'])?>&tdate=<?=base64_decode($_REQUEST['tdate'])?>&from_state=<?=base64_decode($_REQUEST['from_state'])?>&from_location=<?=base64_decode($_REQUEST['from_location'])?>&to_location=<?=base64_decode($_REQUEST['to_location'])?>&status=<?=base64_decode($_REQUEST['status'])?><?=$pagenav?>'"></td>
                </tr>
            </tbody>
          </table>
          <?php }}?>
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