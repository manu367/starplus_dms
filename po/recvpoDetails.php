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
		      $flag=stockLedger($ref_no,$today,$row_poData['prod_code'],$_POST['vendorname'],$_POST['locationname'],$_POST['locationname'],"IN","OK","Vendor Purchase",$_POST[$okqty],$row_poData['po_price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   }
		   ### CASE 2 if user enter somthing in damage qty
		   if($_POST[$damageqty]!="" && $_POST[$damageqty]!=0 && $_POST[$damageqty]!=0.00){
		      $flag=stockLedger($ref_no,$today,$row_poData['prod_code'],$_POST['vendorname'],$_POST['locationname'],$_POST['locationname'],"IN","DAMAGE","Vendor Purchase",$_POST[$damageqty],$row_poData['po_price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   }
		   ### CASE 3 if user enter somthing in missing qty
		   if($_POST[$missqty]!="" && $_POST[$missqty]!=0 && $_POST[$missqty]!=0.00){
		      $flag=stockLedger($ref_no,$today,$row_poData['prod_code'],$_POST['vendorname'],$_POST['locationname'],$_POST['locationname'],"IN","MISSING","Vendor Purchase",$_POST[$missqty],$row_poData['po_price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
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
        $msg = "Vendor Purchase Order is successfully received against ".$ref_no;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
    header("location:vendorPurchaseList.php?msg=".$msg."".$pagenav);
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
		document.getElementById("ok_qty"+a).value=reqqty;
		document.getElementById("upd").disabled=false;
    }else{
		document.getElementById("miss_qty"+a).value=(reqqty - (okqty + damageqty));
		document.getElementById("miss_qty"+a).focus();
		//document.getElementById("upd").disabled=true;
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
      <h2 align="center"><i class="fa fa-shopping-basket"></i> Vendor Purchase Order Details</h2><br/>
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Purchase Order To</label></td>
                <td width="30%"><?php echo str_replace("~",",",getVendorDetails($po_row['po_to'],"name,city,state",$link1));?><input name="vendorname" id="vendorname" type="hidden" value="<?=$po_row['po_to']?>"/></td>
                <td width="20%"><label class="control-label">Purchase Order From</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['po_from'],"name,city,state",$link1));?><input name="locationname" id="locationname" type="hidden" value="<?=$po_row['po_from']?>"/></td>
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
    <div class="panel panel-default table-responsive">
      <div class="panel-heading">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr>
                <th width="5%" rowspan="2" style="text-align:center">#</th>
                <th width="20%" rowspan="2" style="text-align:center">Product</th>
                <th width="15%" rowspan="2" style="text-align:center">Req. Qty</th>
                <th width="15%" rowspan="2" style="text-align:center">Price</th>
                <th width="15%" rowspan="2" style="text-align:center">Value</th>
                <th colspan="3" style="text-align:center">Receive Qty</th>
                </tr>
              <tr>
                <th style="text-align:center" width="10%">Ok</th>
                <th style="text-align:center" width="10%">Damage</th>
                <th style="text-align:center" width="10%">Missing</th>
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM vendor_order_data where po_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,productcolor",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0]." (".$proddet[1].")"?></td>
                <td style="text-align:right"><?=$podata_row[req_qty]?><input type="hidden" name="req_qty<?=$podata_row[id]?>" id="req_qty<?=$i?>" value="<?=$podata_row[req_qty]?>"></td>
                <td style="text-align:right"><?=$podata_row[po_price]?></td>
                <td style="text-align:right"><?=$podata_row[po_value]?></td>
                <td style="text-align:right"><input type="text" class="digits form-control" style="width:100px;" name="ok_qty<?=$podata_row[id]?>" id="ok_qty<?=$i?>"  autocomplete="off" required onBlur="checkRecQty('<?=$i?>');myFunction(this.value,'<?=$i?>','ok_qty');" onKeyPress="return onlyNumbers(this.value);"></td>
                <td style="text-align:right"><input type="text" class="digits form-control" style="width:100px;" name="damage_qty<?=$podata_row[id]?>" id="damage_qty<?=$i?>"  autocomplete="off" required onBlur="checkRecQty('<?=$i?>');myFunction(this.value,'<?=$i?>','damage_qty');" onKeyPress="return onlyNumbers(this.value);" value="0"></td>
                <td style="text-align:right"><input type="text" class="digits form-control" style="width:100px;" name="miss_qty<?=$podata_row[id]?>" id="miss_qty<?=$i?>"  autocomplete="off" value="0" readonly></td>
                </tr>
            <?php
			$i++;
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-default table-responsive">
      <div class="panel-heading">Amount Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Sub Total</label></td>
                <td width="30%"><?php echo $po_row['po_value'];?></td>
                <td width="20%"><label class="control-label">Tax Amount</label></td>
                <td width="30%"><?php echo $po_row['taxamount'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Grand Total</label></td>
                <td><?php echo currencyFormat($po_row['po_value']+$po_row['taxamount']);?><input type="hidden" name="grand_total" id="grand_total" value="<?=$po_row['po_value']+$po_row['taxamount']?>" readonly/></td>
                <td><label class="control-label">Tax Type</label></td>
                <td><?php echo $po_row['taxtype'] . " @ ".$po_row['taxper']."%";?></td>
              </tr>
			  <?php  if(($po_row['taxtype1']!= '') ||  ($po_row['taxtype2']!= ''))
		{
			  ?>
			   <tr>
                <td><label class="control-label">Tax Type 1</label></td>
                <td><?php echo $po_row['taxtype1']. " @ ".$po_row['tax_per1']."%";?></td>
                <td><label class="control-label">Tax Type 2</label></td>
                <td><?php echo $po_row['taxtype2'] . " @ ".$po_row['tax_per2']."%";?></td>
              </tr>
			  <?php
			  }
			  ?>
			   <?php  if(($po_row['taxtype3']!= '' ) ||  ($po_row['taxtype4']!= '')) {
			  ?>
			   <tr>
                <td><label class="control-label">Tax Type 3</label></td>
                <td><?php echo $po_row['taxtype3']; if($po_row['tax_per3']!=0){ echo " @ ".$po_row['tax_per3']."%";}?></td>
                <td><label class="control-label">Tax Type 4</label></td>
                <td><?php echo $po_row['taxtype4']; if($po_row['tax_per4']!=0){ echo " @ ".$po_row['tax_per4']."%";}?></td>
              </tr>
			  <?php
			  }
			  ?>
               <tr>
                <td><label class="control-label">Delivery Address</label></td>
                <td><?php echo $po_row['delivery_address'];?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['remark'];?></td>
              </tr>
               <tr>
                 <td><label class="control-label">Receive Remark </label></td>
                 <td colspan="3"><textarea name="rcv_rmk" id="rcv_rmk" class="form-control" style="resize:none;width:500px;" ></textarea></td>
                 </tr>
               <tr>
                 <td colspan="4" align="center">
                    <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Receive" title="Receive This PO">&nbsp;
                    <input name="refno" id="refno" type="hidden" value="<?=base64_encode($po_row['po_no'])?>"/>
                   <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='vendorPurchaseList.php?<?=$pagenav?>'">
                 </td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
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