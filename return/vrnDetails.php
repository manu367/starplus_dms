<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
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
      <h2 align="center"><i class="fa fa-shopping-basket"></i> Vendor Purchase Return Details</h2><br/>
	 <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">Return Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
              	<td width="20%"><label class="control-label">Return From</label></td>
                <td width="30%"><?php $toloc = explode("~",getLocationDetails($po_row['from_location'],"name,city,state",$link1)); echo implode(",",$toloc)." (".$po_row['from_location'].")";?></td>
                <td width="20%"><label class="control-label">Return To</label></td>
                <td width="30%"><?php $fromloc = explode("~",getVendorDetails($po_row['to_location'],"name,city,state",$link1)); echo implode(",",$fromloc)." (".$po_row['to_location'].")";?></td>
               
              </tr>
              <tr>
                <td><label class="control-label">Challan No.</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label">Challan Date</label></td>
                <td><?php echo dt_format($po_row['sale_date']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Ref No.</label></td>
                <td><?php echo $po_row['ref_no'];?></td>
                <td><label class="control-label">Ref Date</label></td>
                <td><?php echo dt_format($vpo_det['ref_date']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Cost Center</label></td>
                <td><?php $subl = getAnyDetails($po_row['sub_location'],"cost_center,sub_location_name","sub_location","sub_location_master",$link1); if($subl){ echo $subl;}else{ echo getAnyDetails($po_row['sub_location'],"name","asc_code","asc_master",$link1);}?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['remark'];?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->

    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%" style="font-size:12px">
           <thead>
            <tr class="<?=$tableheadcolor?>">
              <td rowspan="2">S.No</td>          
              <td rowspan="2">Product</td>
              <td rowspan="2">Invoice Qty</td>
			  <td colspan="3" align="center">Received Qty</td>
			  <td rowspan="2">Price</td>
              <td rowspan="2">Subtotal</td>
             
			  <?php if($toloc[2] == $fromloc[2]) {?>
              <td rowspan="2">CGST %</td>
			  <td rowspan="2">CGST Amt</td>
			  <td rowspan="2">SGST %</td>
			  <td rowspan="2">SGST Amt</td>
			  <?php } else {?>
			  <td rowspan="2">IGST %</td>
			  <td rowspan="2">IGST Amt</td>
			  <?php }?>
              <td rowspan="2">Total Amt</td>
			   </tr>
            <tr class="<?=$tableheadcolor?>">
              <td>OK</td>
              <td>DAMAGE</td>
              <td>MISSING</td>
            </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$data_sql="select * from billing_model_data where challan_no='".$po_row['challan_no']."' and qty != 0.00 ";
			$data_res=mysqli_query($link1,$data_sql);
			while($data_row=mysqli_fetch_assoc($data_res)){
			?>
              <tr>
                <td><?=$i?></td>
              <td><?=getProductDetails($data_row['prod_code'],"productname",$link1)." | <b>".$data_row['prod_code']."</b>"?></td>
              <td align="right"><?=$data_row['qty'];?></td>
               <td align="right"><?=$data_row['okqty'];?></td>
                <td align="right"><?=$data_row['damageqty'];?></td>
                <td align="right"><?=$data_row['missingqty'];?></td>  
                <td align="right"><?=$data_row['price'];?></td>  
             	<td align="right"><?=$data_row['value'];?></td>
             
			 	  <?php if($toloc[2] == $fromloc[2]) {?>
             <td align="right"><?=$data_row['cgst_per'];?></td>
			  <td align="right"><?=$data_row['cgst_amt'];?></td>
			   <td align="right"><?=$data_row['sgst_per'];?></td>
			    <td align="right"><?=$data_row['sgst_amt'];?></td>
				<?php } else {?>
				 <td align="right"><?=$data_row['igst_per'];?></td>
				  <td align="right"><?=$data_row['igst_amt'];?></td>
				  <?php }?>
             <td align="right"><?=$data_row['totalvalue'];?></td>
			 </tr>
            <?php
			$total_qty+= $data_row['qty'];
			$total_okqty+= $data_row['okqty'];
			$total_dmgqty+= $data_row['damageqty'];
			$total_misqty+= $data_row['missingqty'];
			$total_sub+= $data_row['value'];
			$total_igstamt+= $data_row['igst_amt'];
			$total_cgstamt+= $data_row['cgst_amt'];
			$total_sgstamt+= $data_row['sgst_amt'];
			$total_amt+= $data_row['totalvalue'];
			$i++;
			}
			?>
            <tr align="right">
                <td colspan="2" align="right"><strong>Total</strong></td>
                <td><strong><?=$total_qty?></strong></td>
                <td><strong><?=$total_okqty?></strong></td>
                <td><strong><?=$total_dmgqty?></strong></td>                
                <td>&nbsp;</td>
				<td>&nbsp;</td>
                <td><strong><?=currencyFormat($total_sub)?></strong></td>
				<?php if($toloc[2] == $fromloc[2]) {?>
                <td>&nbsp;</td>
                <td><strong><?=currencyFormat($total_cgstamt)?></strong></td>
				<td>&nbsp;</td>
				<td><strong><?=currencyFormat($total_sgstamt)?></strong></td>
				<?php } else { ?>
                <td>&nbsp;</td>
                <td><strong><?=currencyFormat($total_igstamt)?></strong></td>
				<?php }?>
                <td><strong><?=currencyFormat($total_amt)?></strong></td>
             	</tr>
            </tbody>
          </table>

          
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Amount Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><strong>Total Qty</strong></td>
                <td width="30%"><?=$total_qty?></td>
                <td width="20%"><label class="control-label">Sub Total</label></td>
                <td width="30%" align="right"><?php echo $po_row['basic_cost'];?></td>
              </tr>
              <tr>
                <td rowspan="2"><label class="control-label">Delivery Address</label></td>
                <td rowspan="2"><?=$po_row["deliv_addrs"]?></td>
                <td><label class="control-label">Discount</label></td>
                <td align="right"><?php echo $po_row['discount_amt'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Total SGST</label></td>
                <td align="right"><?=$total_sgstamt ?></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><label class="control-label">Total CGST</label></td>
                <td align="right"><?=$total_cgstamt ?></td>
              </tr>
              <tr>
                <td rowspan="2"><label class="control-label">Remark</label></td>
                <td rowspan="2"><?php echo $po_row['billing_rmk'];?></td>
                <td><label class="control-label">Total IGST</label></td>
                <td align="right"><?=$total_igstamt ?></td>
              </tr>
              <tr>
                <td><label class="control-label">Grand Total</label></td>
                <td align="right"><?php echo $po_row['total_cost'];?></td>
              </tr>
              <?php if($po_row['tcs_per']!=0.00){ ?>
              <tr>
                <td><label class="control-label">TCS</label></td>
                <td><?=$po_row['tcs_per']?></td>
                <td><label class="control-label">TCS Amount</label></td>
                <td align="right"><?=$po_row['tcs_amt']?></td>
              </tr>
              <?php }?>
              <?php if($po_row['round_off']!=0.00){?>
              <tr>
                <td><label class="control-label">Round Off</label></td>
                <td><?=$po_row['round_off']?></td>
                <td><label class="control-label">Final Total</label></td>
                <td align="right"><?=$po_row['tcs_amt']+$po_row['round_off']+$po_row['total_cost']?></td>
              </tr>
              <?php }?>
              <?php if($po_row['status']=="Cancelled"){ ?> 
              <tr>
				 <td><label class="control-label">Cancel By</label></td>
                 <td><?php echo getAdminDetails ($po_row['cancel_by'],"name",$link1);?></td>
				 <td><label class="control-label">Cancel Date</label></td>
                 <td ><?php echo dt_format ($po_row['cancel_date']);?></td>
				 </tr>
				<tr>                 
				 <td><label class="control-label">Cancel Remark</label></td>
                 <td colspan="3"><?php echo $po_row['cancel_rmk'];?></td>
                </tr>
			  <?php }?>
                <tr>
                 <td colspan="4" align="center">
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='vendor_return.php?<?=$pagenav?>'">                 </td>
             </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
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