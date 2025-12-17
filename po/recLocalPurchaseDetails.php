<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM vendor_order_master where po_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
///// after hitting receive button ///
if($_POST){
 if ($_POST['upd']=='Receive'){
	$ref_no=base64_decode($_POST['refno']);
	mysqli_autocommit($link1, false);
	$flag = true;
	////// run data cycle of po and get posted value of receive qty
	 $sql_po_data="select * from vendor_order_data where po_no='".$ref_no."'";
    $res_poData=mysqli_query($link1,$sql_po_data)or die("error1".mysqli_error($link1));
    while($row_poData=mysqli_fetch_assoc($res_poData)){
		  ///// initialize posted variables
		  $reqqty="req_qty".$row_poData['id'];
		  $okqty="ok_qty".$row_poData['id'];
		  $damageqty="damage_qty".$row_poData['id'];
		  $missqty="miss_qty".$row_poData['id'];
		  ///// update stock in inventory //
		  if(mysqli_num_rows(mysqli_query($link1,"select partcode from stock_status where partcode='".$row_poData['prod_code']."' and asc_code='".$_POST['locationname']."'"))>0){
			 ///if product is exist in inventory then update its qty 
			 $result=mysqli_query($link1,"update stock_status set qty=qty+'".$_POST[$reqqty]."',okqty=okqty+'".$_POST[$okqty]."',broken=broken+'".$_POST[$damageqty]."',missing=missing+'".$_POST[$missqty]."',updatedate='".$datetime."' where partcode='".$row_poData['prod_code']."' and asc_code='".$_POST['locationname']."'");
		  }
		  else{
			 //// if product is not exist then add in inventory
			 $result=mysqli_query($link1,"insert into stock_status set asc_code='".$_POST['locationname']."',partcode='".$row_poData['prod_code']."',qty=qty+'".$_POST[$reqqty]."',okqty='".$_POST[$okqty]."',broken='".$_POST[$damageqty]."',missing='".$_POST[$missqty]."',uom='PCS',updatedate='".$datetime."'");
		  }
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               echo "Error details1: " . mysqli_error($link1) . ".";
           }
		   ////// insert in stock ledger////
		   ### CASE 1 if user enter somthing in ok qty
		   if($_POST[$okqty]!="" && $_POST[$okqty]!=0 && $_POST[$okqty]!=0.00){
		      $flag=stockLedger($ref_no,$today,$row_poData['prod_code'],$_POST['vendorname'],$_POST['locationname'],$_POST['locationname'],"IN","OK","Local Purchase",$_POST[$okqty],$row_poData['po_price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   }
		   ### CASE 2 if user enter somthing in damage qty
		   if($_POST[$damageqty]!="" && $_POST[$damageqty]!=0 && $_POST[$damageqty]!=0.00){
		      $flag=stockLedger($ref_no,$today,$row_poData['prod_code'],$_POST['vendorname'],$_POST['locationname'],$_POST['locationname'],"IN","DAMAGE","Local Purchase",$_POST[$damageqty],$row_poData['po_price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   }
		   ### CASE 3 if user enter somthing in missing qty
		   if($_POST[$missqty]!="" && $_POST[$missqty]!=0 && $_POST[$missqty]!=0.00){
		      $flag=stockLedger($ref_no,$today,$row_poData['prod_code'],$_POST['vendorname'],$_POST['locationname'],$_POST['locationname'],"IN","MISSING","Local Purchase",$_POST[$missqty],$row_poData['po_price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   }
		   ///// update data table
           $result=mysqli_query($link1,"update vendor_order_data set okqty='".$_POST[$okqty]."',damageqty='".$_POST[$damageqty]."',missingqty='".$_POST[$missqty]."' where id='".$row_poData['id']."'");
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               echo "Error details2: " . mysqli_error($link1) . ".";
		   }
	}//// close while loop
	//// Update status in  master table
    $result=mysqli_query($link1,"update vendor_order_master set status='Received',receive_date='".$today."',receive_time='".$currtime."',receive_by='".$_SESSION['userid']."',receive_remark='".$_POST['rcv_rmk']."',receive_ip='".$ip."' where po_no='".$ref_no."'");
	//// check if query is not executed
    if (!$result) {
	   $flag = false;
	   echo "Error details3: " . mysqli_error($link1) . ".";
    }
	////// maintain party ledger////
	$flag=partyLedger($_POST['locationname'],$_POST['vendorname'],$ref_no,$today,$today,$currtime,$_SESSION['userid'],"VPO",$grand_total,"CR",$link1,$flag);
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$ref_no,"VPO","RECEIVE",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Local Purchase Order is successfully received against ".$ref_no;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
    header("location:localPurchaseList.php?msg=".$msg."".$pagenav);
    exit;
 }
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
		document.getElementById("miss_qty"+a).focus();
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
      <h2 align="center"><i class="fa fa-shopping-basket"></i> Receive Local Purchase Details</h2><br/>
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Purchase Order From</label></td>
                <td width="30%"><?php echo str_replace("~",",",getVendorDetails($po_row['po_from'],"name,city,state",$link1));?><input name="vendorname" id="vendorname" type="hidden" value="<?=$po_row['po_from']?>"/></td>
                <td width="20%"><label class="control-label">Purchase Order To</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['po_to'],"name,city,state",$link1));?><input name="locationname" id="locationname" type="hidden" value="<?=$po_row['po_to']?>"/></td>
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
                <td><label class="control-label">Invoice No.</label></td>
                <td><?php echo $po_row['invoice_no'];?></td>
                <td><label class="control-label">Invoice Date</label></td>
                <td><?php echo $po_row['invoice_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Currency Type</label></td>
                <td><?php echo $po_row['currency_type'];?></td>
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
                <th width="5%" rowspan="2" style="text-align:center">#</th>
                <th width="17%" rowspan="2" style="text-align:center">Product</th>
                <th width="5%" rowspan="2" style="text-align:center">Req. Qty</th>
                <th width="6%" rowspan="2" style="text-align:center">Price</th>
                <th width="6%" rowspan="2" style="text-align:center">Value</th>
                <th width="5%" rowspan="2" style="text-align:center">SGST<br>(%)</th>
                <th width="6%" rowspan="2" style="text-align:center">SGST Amt</th>
                <th width="5%" rowspan="2" style="text-align:center">CGST<br>(%)</th>
                <th width="6%" rowspan="2" style="text-align:center">CGST Amt</th>
                <th width="5%" rowspan="2" style="text-align:center">IGST<br>(%)</th>
                <th width="6%" rowspan="2" style="text-align:center">IGST Amt</th>
                <th colspan="3" style="text-align:center">Receive Qty</th>
                <th width="6%" rowspan="2" style="text-align:center">Line Total</th>
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
				$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,productcolor,productcode",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0]." | ".$proddet[1]." | ".$proddet[2]?></td>
                <td style="text-align:right"><?=$podata_row['req_qty']?><input type="hidden" name="req_qty<?=$podata_row['id']?>" id="req_qty<?=$i?>" value="<?=$podata_row['req_qty']?>"></td>
                <td style="text-align:right"><?=$podata_row['po_price']?></td>
                <td style="text-align:right"><?=$podata_row['po_value']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['igst_per']?></td>
                <td style="text-align:right"><?=$podata_row['igst_amt']?></td>
                <td style="text-align:right"><input type="text" class="digits form-control" style="width:50px;" name="ok_qty<?=$podata_row[id]?>" id="ok_qty<?=$i?>"  autocomplete="off" required onBlur="checkRecQty('<?=$i?>');myFunction(this.value,'<?=$i?>','ok_qty');" onKeyPress="return onlyNumbers(this.value);"></td>
                <td style="text-align:right"><input type="text" class="digits form-control" style="width:50px;" name="damage_qty<?=$podata_row[id]?>" id="damage_qty<?=$i?>"  autocomplete="off" required onBlur="checkRecQty('<?=$i?>');myFunction(this.value,'<?=$i?>','damage_qty');" onKeyPress="return onlyNumbers(this.value);" value="0"></td>
                <td style="text-align:right"><input type="text" class="digits form-control" style="width:50px;" name="miss_qty<?=$podata_row[id]?>" id="miss_qty<?=$i?>"  autocomplete="off" value="0" readonly></td>
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
                <td><label class="control-label">Delivery Address</label></td>
                <td><?php echo $po_row['delivery_address'];?></td>
                <td width="20%"><label class="control-label">Sub Total</label></td>
                <td width="30%"><?php echo $po_row['po_value'];?></td>                
              </tr>
              <tr>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['remark'];?></td> 
                <td><label class="control-label">Round Off</label></td>
                <td><?=$po_row['round_off'];?></td>                
              </tr>
               <tr>
                   <td><label class="control-label">Receive Remark <span style="color:#F00">*</span></label></td>
                 <td><textarea name="rcv_rmk" id="rcv_rmk" class="form-control required" style="resize:none;width:300px;" required></textarea></td>
                <td><label class="control-label">Grand Total</label></td>
                <td><?php echo currencyFormat($po_row['po_value']+$po_row['taxamount']);?><input type="hidden" name="grand_total" id="grand_total" value="<?=$po_row['po_value']+$po_row['taxamount']?>" readonly/></td> 
                 </tr>
               <tr>
                 <td colspan="4" align="center">
                    <input type="submit" class="btn <?=$btncolor?>" name="upd" id="upd" value="Receive" title="Receive This PO">&nbsp;
                    <input name="refno" id="refno" type="hidden" value="<?=base64_encode($po_row['po_no'])?>"/>
                   <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='localPurchaseList.php?<?=$pagenav?>'">
                 </td>
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