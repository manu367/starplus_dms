<?php
////// Function ID ///////
$fun_id = array("u"=>array(109)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
//////////////// decode challan number////////////////////////////////////////////////////////
$po_no =base64_decode($_REQUEST['id']);
////////////////////////////////////////// fetching datta from table///////////////////////////////////////////////
$po_sql="select * from billing_master where challan_no='".$po_no."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
///get VPO details
$vpo_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT entry_date FROM vendor_order_master WHERE po_no='".$po_row['ref_no']."'"));
//// check serial no. is uploaded or not
$rs12=mysqli_query($link1,"SELECT imei_attach, prod_code FROM billing_model_data WHERE challan_no='".$po_row['challan_no']."'");
$check=1;
while($row12=mysqli_fetch_array($rs12)){
$get_result12 = explode("~",getAnyDetails($row12['prod_code'],"productcode,is_serialize","productcode" ,"product_master",$link1));
if($get_result12[1]=='Y'){ if($row12['imei_attach']=="Y"){ $check*=1;}else{ $check*=0;}}else{ $check*=1;}
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title>GRN STATUS VIEW</title>
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <style type="text/css">
	.modal-dialogTH{
		overflow-y: initial !important
	}
	.modal-bodyTH{
		max-height: calc(100vh - 212px);
		overflow-y: auto;
	}
	.modalTH {
	  width: 1000px;
	  margin: auto;
	}

</style>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
     include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-ship"></i> GRN View</h2>
          
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>	  
	  <?php
		if(isset($_SESSION["logres"]) && $_SESSION["logres"]){
		echo '<div class="py-2 overflow-hidden" style="background:#f1f1f1;padding:15px;line-height:20px;color:#e51111;margin:15px;font-size:12px;">';
		echo '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.$_SESSION["logres"]["msg"];
		echo '<br/><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.implode(" , ",$_SESSION["logres"]["invalid"]);
		echo '</div>';
		}
		unset($_SESSION["logres"]);
		?>
	  <?php if($po_row['imei_attach'] != 'Y' && $po_row['status'] != 'Cancelled' && $check==0) {?>
	  <div align="right">
	  <input title="uploader" type="button" class="btn<?=$btncolor?>" value="Serial Attach" onClick="window.location.href='grn_uploader.php?challan_no=<?=base64_encode($po_row['challan_no'])?>&challan_date=<?=base64_encode($po_row['sale_date'])?><?=$pagenav?>'">
	  </div>
	  <?php }else{}?>
	  
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">GRN Entry Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">GRN From</label></td>
                <td width="30%"><?php $fromloc = explode("~",getVendorDetails($po_row['from_location'],"name,city,state",$link1)); echo implode(",",$fromloc)." (".$po_row['from_location'].")";?></td>
                <td width="20%"><label class="control-label">GRN To</label></td>
                <td width="30%"><?php $toloc = explode("~",getLocationDetails($po_row['to_location'],"name,city,state",$link1)); echo implode(",",$toloc)." (".$po_row['to_location'].")";?></td>
              </tr>
              <tr>
                <td><label class="control-label">GRN No.</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label">GRN Date</label></td>
                <td><?php echo dt_format($po_row['receive_date']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">PO No.</label></td>
                <td><?php echo $po_row['ref_no'];?></td>
                <td><label class="control-label">PO Date</label></td>
                <td><?php echo dt_format($vpo_det['entry_date']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Invoice No.</label></td>
                <td><?php echo $po_row['inv_ref_no'];?></td>
                <td><label class="control-label">Invoice Date</label></td>
                <td><?php echo dt_format($po_row['po_inv_date']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Cost Center</label></td>
                <td><?php $subl = getAnyDetails($po_row['sub_location'],"cost_center,sub_location_name","sub_location","sub_location_master",$link1); if($subl){ echo $subl;}else{ echo getAnyDetails($po_row['sub_location'],"name","asc_code","asc_master",$link1);}?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['receive_remark'];?></td>
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
			   <td rowspan="2">TAG /<?php echo $imeitag;?></td>
			  
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
			 <td align="center"><?php 
				if($data_row['imei_attach'] == 'Y') {
				 echo "Serial No. is Attached";
				}
			else {	
				if($check==0){
				?><a href='vendorUploadImeinew.php?po_no=<?=base64_encode($po_row['challan_no'])?>&reqty=<?=base64_encode($data_row['okqty'])?>&prodcode=<?=base64_encode($data_row['prod_code'])?><?=$pagenav?>' title='Upload<?=$imeitag?>'><i class="fa fa-upload fa-lg" aria-hidden="true"></i></a><?php 
				}else{
					echo "Not Applicable";
				}
				
				} ?></td>
             
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
             	<td><strong>&nbsp;</strong></td>
              </tr>
             <tr>
                 <td colspan="16" align="center">
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='grnList.php?<?=$pagenav?>'">
                 </td>
             </tr>
            </tbody>
          </table>

          
      </div><!--close panel body-->
    </div><!--close panel-->
 <!--close panel-->
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

