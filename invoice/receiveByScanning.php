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
$(document).ready(function(){
    $("#frm2").validate();
});
</script>
<script type="text/javascript">
function checkRecQty(a){
	var reqqty=0;
	var okqty=0;
	var damageqty=0;
	//// check requested qty
    if(document.getElementById("req_qty"+a).value==""){
       reqqty=0;
	}else{
	   reqqty=parseInt(document.getElementById("req_qty"+a).value);
	}
	//// check enter ok qty
    if(document.getElementById("ok_qty"+a).value==""){
       okqty=0;
    }else{
       okqty=parseInt(document.getElementById("ok_qty"+a).value);
    }
	//// check enter damage qty
    if(document.getElementById("damage_qty"+a).value==""){
       damageqty=0;
    }else{
       damageqty=parseInt(document.getElementById("damage_qty"+a).value);
    }
	//// check enter qty should not be greater than requested qty
    if(reqqty < (okqty + damageqty)){
       alert("Ok Qty & Damage Qty can not more than requested Qty!");
		document.getElementById("miss_qty"+a).value=0;
		document.getElementById("damage_qty"+a).value=0;
		//document.getElementById("ok_qty"+a).focus();
		document.getElementById("upd").disabled=true;
    }else{
		document.getElementById("miss_qty"+a).value=(reqqty - (okqty + damageqty));
		//document.getElementById("miss_qty"+a).focus();
		document.getElementById("upd").disabled=false;
	}
}
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-level-down"></i> Receive Invoice Details</h2>
      <h4 align="center"><?php echo $po_row['type']." ".$po_row['document_type'];?></h4>
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Billing To</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['to_location'],"name,city,state",$link1));?><input name="billto" id="billto" type="hidden" value="<?=$po_row['to_location']?>"/></td>
                <td width="20%"><label class="control-label">Billing From</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['from_location'],"name,city,state",$link1));?><input name="billfrom" id="billfrom" type="hidden" value="<?=$po_row['from_location']?>"/></td>
              </tr>
              <tr>
                <td><label class="control-label">Invoice No.</label></td>
                <td><?php echo $po_row['challan_no'];?><input name="invtype" id="invtype" type="hidden" value="<?php echo $po_row['type']." ".$po_row['document_type'];?>"/></td>
                <td><label class="control-label">Billing Date</label></td>
                <td><?php echo $po_row['sale_date'];?></td>
              </tr>
              <?php if($po_row['ref_no']!='' && $po_row['document_type']=="INVOICE"){ ?>
              <tr>
                <td><label class="control-label">Ref. Invoice No.</label></td>
                <td><?php echo $po_row['ref_no'];?></td>
                <td><label class="control-label">Ref. Invoice Date</label></td>
                <td><?php echo $po_row['ref_date'];?></td>
              </tr>
              <?php } ?>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo getAdminDetails($po_row['entry_by'],"name",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $po_row['status'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Discount Type</label></td>
                <td><?php echo getDiscountType($po_row['discountfor']);?></td>
                <td><label class="control-label">Tax Type</label></td>
                <td><?php echo getTaxType($po_row['taxfor']);?></td>
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
                <th style="text-align:center" width="3%">#</th>
                <th style="text-align:center" width="15%">Product</th>
                <th style="text-align:center" width="10%">Disp. Qty</th>
                <th style="text-align:center" width="7%">Price</th>
                <th style="text-align:center" width="10%">Value</th>
                <th style="text-align:center" width="5%">Disc./ Unit</th>
                <th style="text-align:center" width="7%">After Discount Value</th>
                <th style="text-align:center" width="5%">SGST (%)</th>
                <th style="text-align:center" width="6%">SGST Amt</th>
                <th style="text-align:center" width="6%">CGST (%)</th>
                <th style="text-align:center" width="7%">CGST Amt</th>
                <th style="text-align:center" width="6%">IGST (%)</th>
                <th style="text-align:center" width="7%">IGST Amt</th>
                <th style="text-align:center" width="10%">Total</th>
				<th style="text-align:center" width="10%">Scan Serial</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$tot_sgst_amt = 0;
			$tot_cgst_amt = 0;
			$tot_igst_amt = 0;
			$podata_sql="SELECT * FROM billing_model_data where challan_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$discount_val = number_format(($podata_row['value'] - ($podata_row['discount']*$podata_row['qty'])),'2','.','');
				$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,productcolor",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0]." (".$proddet[1].")"?></td>
                <td style="text-align:right"><input type="hidden" name="req_qty<?=$podata_row[id]?>" id="req_qty<?=$i?>" value="<?=$podata_row['qty']?>"><?=$podata_row['qty']?></td>
                <td style="text-align:right"><?=$podata_row['price']?></td>
                <td style="text-align:right"><?=$podata_row['value']?></td>
                <td style="text-align:right"><?=$podata_row['discount']?></td>
                <td style="text-align:right"><?=$discount_val?></td>
                <td style="text-align:left"><?=$podata_row['sgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
                <td style="text-align:left"><?=$podata_row['cgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
                <td style="text-align:left"><?=$podata_row['igst_per']?></td>
                <td style="text-align:right"><?=$podata_row['igst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['totalvalue'];?></td>
				<td style="text-align:right"><?php if($podata_row['scan'] == ''){?> <input title="Scan Serial" type="button" class="btn<?=$btncolor?>" value="Scan Serial" onClick="window.location.href='scanningreceiveInvoice.php?prodcode=<?=base64_encode($podata_row['prod_code'])?>&qty=<?=base64_encode($podata_row['qty'])?>&invoiceno=<?=base64_encode($docid)?><?=$pagenav?>'"><?php }?></td>
              </tr>
            <?php
			$tot_sgst_amt+=$podata_row['sgst_amt'];
            $tot_cgst_amt+=$podata_row['cgst_amt'];
            $tot_igst_amt+=$podata_row['igst_amt'];
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
                <td width="20%"><label class="control-label">Total Discount</label></td>
                <td width="30%"><?php echo $po_row['discount_amt'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Grand Total</label></td>
                <td><?php echo currencyFormat($po_row['total_cost']);?><input type="hidden" name="grand_total" id="grand_total" value="<?=$po_row['total_cost']?>" readonly/></td>
                <td><label class="control-label">Total GST</label></td>
                <td><?php echo currencyFormat($tot_sgst_amt+$tot_cgst_amt+$tot_igst_amt);?></td>
              </tr>
               <tr>
                <td><label class="control-label">Delivery Address</label></td>
                <td><?php echo $po_row['deliv_addrs'];?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['disp_rmk'];?></td>
              </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Logistic Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Logistic Name</label></td>
                <td width="30%"><?php echo getLogistic($po_row['diesel_code'],$link1);?></td>
                <td width="20%"><label class="control-label">Docket No.</label></td>
                <td width="30%"><?php echo $po_row['docket_no'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Logistic Person</label></td>
                <td><?php echo $po_row['logistic_person'];?></td>
                <td><label class="control-label">Contact No.</label></td>
                <td><?php echo $po_row['logistic_contact'];?></td>
              </tr>
               <tr>
                 <td><label class="control-label">Carrier No.</label></td>
                 <td><?php echo $po_row['vehical_no'];?></td>
                 <td><label class="control-label">Dispatch Date</label></td>
                 <td><?php echo $po_row['dc_date'];?></td>
               </tr>
               <tr>
                <td><label class="control-label">Dispatch Remark</label></td>
                <td colspan="3"><?php echo $po_row['disp_rmk'];?></td>
                </tr>
              
               <tr>
                 <td colspan="4" align="center">
                
                    <input name="refno" id="refno" type="hidden" value="<?=base64_encode($po_row['challan_no'])?>"/>
                 <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='receiveInvoice.php?<?=$pagenav?>'"></td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
    </form>
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