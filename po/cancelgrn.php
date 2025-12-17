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
////// final submit form ////
@extract($_POST);
if($_POST){
	if($_POST['Submit']=='Cancel' && $po_row["status"]!="Cancelled"){
		mysqli_autocommit($link1, false);
	  	$flag = true;
		$error_msg = "";
		///// get model data details
		$res_data = mysqli_query($link1,"SELECT * FROM billing_model_data WHERE challan_no='".$po_no."'");
		$i=0;
		while($row_data = mysqli_fetch_assoc($res_data)){
			if($po_row["status"]=="Received"){
			//checking row value of product and qty should not be blank
			$selstok = mysqli_fetch_assoc(mysqli_query($link1,"SELECT okqty,broken,missing FROM stock_status WHERE partcode = '".$row_data["prod_code"]."' AND asc_code='".$po_row["to_location"]."' AND sub_location='".$po_row["sub_location"]."'"));
			//// check stock should be available ////
			if ($selstok["okqty"] < $row_data['okqty'] || $selstok["broken"] < $row_data['damageqty'] || $selstok["missing"] < $row_data['missingqty']) {
				$flag = false;
				$error_msg = "Error Code0.1: Stock is not available";
			} else {
				
			}
			//// update in stock status
			$res1 = mysqli_query($link1,"UPDATE stock_status SET qty = qty - '".$row_data['qty']."', okqty = okqty - '".$row_data['okqty']."', broken = broken - '".$row_data['damageqty']."', missing = missing - '".$row_data['missingqty']."', updatedate='".$datetime."' WHERE partcode = '".$row_data["prod_code"]."' AND asc_code='".$po_row["to_location"]."' AND sub_location='".$po_row["sub_location"]."'");
			if (!$res1) {
				$flag = false;
				$error_msg = "Error details1: " . mysqli_error($link1) . ".";
			}
			if($row_data['okqty']!="" && $row_data['okqty']!=0 && $row_data['okqty']!=0.00){
				$flag=stockLedger($po_no,$today,$row_data['prod_code'],$po_row['from_location'],$po_row['sub_location'],$po_row['sub_location'],"OUT","OK","Cancel Against GRN",$row_data['okqty'],$row_data['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag); 
				   
			}
			### CASE 2 if user enter somthing in damage qty
			if($row_data['damageqty']!="" && $row_data['damageqty']!=0 && $row_data['damageqty']!=0.00){
				$flag=stockLedger($po_no,$today,$row_data['prod_code'],$po_row['from_location'],$po_row['sub_location'],$po_row['sub_location'],"OUT","DAMAGE","Cancel Against GRN",$row_data['damageqty'],$row_data['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
			}
			### CASE 3 if user enter somthing in missing qty
			if($row_data['missingqty']!="" && $row_data['missingqty']!=0 && $row_data['missingqty']!=0.00){
				$flag=stockLedger($po_no,$today,$row_data['prod_code'],$po_row['from_location'],$po_row['sub_location'],$po_row['sub_location'],"OUT","MISSING","Cancel Against GRN",$row_data['missingqty'],$row_data['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
			}
			$i++;
			}
			if($row_data['imei_attach'] == 'Y'){
				///////////   check if stock is available /////////////////////////////////////////////////// 			
				$billing_data = mysqli_query ($link1 ,"SELECT * FROM billing_imei_data WHERE doc_no = '".$po_no."' AND prod_code = '".$row_data['prod_code']."'");
				if(mysqli_num_rows($billing_data) > 0) {
					while ($row = mysqli_fetch_array($billing_data)){		
						//////   insert deleted imeis into new table///////////////////////////////////////////////////
						$result3 = mysqli_query($link1,"insert into cancel_imei_data set from_location = '".$row['from_location']."' , to_location = '".$row['to_location']."' , owner_code = '".$row['owner_code']."' , prod_code = '".$row['prod_code']."' , doc_no = '".$row['doc_no']."' , imei1 = '".$row['imei1']."' , imei2 = '".$row['imei2']."' , flag = '".$row['flag']."'  , stock_type = '".$row['stock_type']."' ");	
						//// check if query is not executed
						if (!$result3) {
							$flag = false;
							$err_msg = "Error Code31:" . mysqli_error($link1) . ".";
						}	
					}
					///////////   delete entry from billing imei data /////////////////////////////////////////////////////////
					$result4 = mysqli_query($link1, "DELETE FROM billing_imei_data WHERE doc_no = '".$po_no."' AND prod_code = '".$row_data['prod_code']."'");
					//// check if query is not executed////////
					if (!$result4) {
						$flag = false;
						$err_msg = "Error Code4:" . mysqli_error($link1) . ".";
					}
				} 
			}
		}
		if($po_row["status"]=="Received" && $i>0){
			$flag=partyLedger($po_row["to_location"],$po_row['from_location'],$po_no,$today,$today,$currtime,$_SESSION['userid'],"GRN",$po_row["total_cost"],"DR",$link1,$flag); 
			//// update cr bal 
			$result5 = mysqli_query($link1,"UPDATE current_cr_status SET cr_abl=cr_abl-'".$po_row["total_cost"]."',total_cr_limit=total_cr_limit-'".$po_row["total_cost"]."', last_updated='".$datetime."' WHERE parent_code='".$po_row['to_location']."' AND asc_code='".$po_row['from_location']."'");
			/// check if query is not executed
			if (!$result5) {
				$flag = false;
				$err_msg = "Error Code5:" . mysqli_error($link1) . ".";
			}	
		}
		/////// delete from stock ledger
		/*$res2 = mysqli_query($link1,"DELETE FROM stock_ledger WHERE reference_no = '".$po_no."'");
		if (!$res2) {
			$flag = false;
			$error_msg = "Error details2: " . mysqli_error($link1) . ".";
		}
		//// delete from party ledger
		$res3 = mysqli_query($link1,"DELETE FROM party_ledger WHERE doc_no = '".$po_no."'");
		if (!$res3) {
			$flag = false;
			$error_msg = "Error details3: " . mysqli_error($link1) . ".";
		}*/
		/////delete from billing_imeidata
		
		/*$res4 = mysqli_query($link1,"DELETE FROM billing_imei_data WHERE doc_no = '".$po_no."'");
		if (!$res4) {
			$flag = false;
			$error_msg = "Error details4: " . mysqli_error($link1) . ".";
		}*/
		//// update status in billing master
		$res5 = mysqli_query($link1,"UPDATE billing_master SET status = 'Cancelled', cancel_by = '".$_SESSION['userid']."', cancel_date = '".$today."', cancel_rmk='".$remark."', cancel_step = 'After ".$po_row['status']."',cancel_ip='".$ip."' WHERE challan_no = '".$po_no."'");
		if (!$res5) {
			$flag = false;
			$error_msg = "Error details5: " . mysqli_error($link1) . ".";
		}
		//// update status in billing master
		$res6 = mysqli_query($link1,"UPDATE vendor_order_master SET status='Cancelled', cancel_by = '".$_SESSION['userid']."', cancel_date = '".$today."', cancel_rmk='".$remark."', cancel_step = 'After ".$po_row['status']."',cancel_ip='".$ip."', grn_status = 'GRN Cancelled' WHERE po_no = '".$po_row["ref_no"]."'");
		if (!$res6) {
			$flag = false;
			$error_msg = "Error details6: " . mysqli_error($link1) . ".";
		}
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$po_no,"GRN","CANCELLED",$ip,$link1,$flag);
		///// check  master  query are successfully executed
		if ($flag) {
			mysqli_commit($link1);
			$msg = "GRN is  successfully Cancelled with ref no." .$po_no ;
		} else {
			mysqli_rollback($link1);
			$msg = "Request could not be processed. Please try again. ".$error_msg;
		} 
		mysqli_close($link1);
		///// move to parent page
		header("Location:grnList.php?msg=".$msg."".$pagenav);
		exit;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title>GRN CANCEL</title>
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
      <h2 align="center"><i class="fa fa-ship"></i> Cancel GRN</h2>
          
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>	  
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">GRN Entry Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">GRN TO</label></td>
                <td width="30%"><?php echo getVendorDetails($po_row["to_location"],"name",$link1)."(".$po_row["to_location"].")";?></td>
                <td width="20%"><label class="control-label">GRN From</label></td>
                <td width="30%"><?php echo getLocationDetails($po_row["from_location"],"name",$link1)."(".$po_row['from_location'].")";?></td>
              </tr>
              <tr>
                <td><label class="control-label">Invoice No.</label></td>
                <td><?php echo $po_row['inv_ref_no'];?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['remark'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">PO No.</label></td>
                <td><?php echo $po_row['ref_no'];?></td>
                <td><label class="control-label">GRN Date</label></td>
                <td><?php echo dt_format($po_row['receive_date']);?></td>
              </tr>  
	     <tr>
                <td><label class="control-label">GRN Status</label></td>
                <td><?php echo $po_row["status"];?></td>
                <td><label class="control-label">GRN No.</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
              </tr>   
               <tr>
                <td><label class="control-label">Stock Type</label></td>
                <td><?php echo $po_row["stock_type"];?></td>
                <td><label class="control-label">Cost Center</label></td>
                <td><?php $subl = getAnyDetails($po_row['sub_location'],"cost_center,sub_location_name","sub_location","sub_location_master",$link1); if($subl){ echo $subl;}else{ echo getAnyDetails($po_row['sub_location'],"name","asc_code","asc_master",$link1);}?></td>
              </tr>  
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo $po_row['entry_by'];?></td>
                <td><label class="control-label">Receive By</label></td>
                <td><?php echo $po_row['receive_by'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Receive Date</label></td>
                <td><?php echo dt_format($po_row['receive_date']);?></td>
                <td><label class="control-label">Receive Remark</label></td>
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
              <td rowspan="2">Qty</td>
			  <td colspan="3" align="center">Received Qty</td>
			  <td rowspan="2">Price</td>
              <td rowspan="2">Subtotal</td>
             
			  <?php if($po_row['total_igst_amt'] == '0.00') {?>
              <td rowspan="2">CGST %</td>
			  <td rowspan="2">CGST Amt</td>
			  <td rowspan="2">SGST %</td>
			  <td rowspan="2">SGST Amt</td>
			  <?php } else {?>
			  <td rowspan="2">IGST %</td>
			  <td rowspan="2">IGST Amt</td>
			  <?php }?>
              <td rowspan="2">Total Amt</td>
			   <td rowspan="2"><?php echo SERIALNO ?> No.</td>
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
			$cancel=1;
			$data_sql="select * from billing_model_data where challan_no='".$po_row['challan_no']."' and qty != 0.00 ";
			$data_res=mysqli_query($link1,$data_sql);
			while($data_row=mysqli_fetch_assoc($data_res)){
				$flag=1;
				if($po_row["status"]=="Received"){
					$res=mysqli_query($link1,"SELECT SUM(okqty) AS okstk, SUM(broken) AS brokestk, SUM(missing) AS missing FROM stock_status WHERE asc_code='".$po_row["to_location"]."' AND sub_location='".$po_row["sub_location"]."' AND partcode='".$data_row['prod_code']."'")or die(mysqli_error($link1));		
					$chk_stk=mysqli_fetch_assoc($res);
					if(($chk_stk['okstk']>=$data_row['okqty']) && ($chk_stk['brokestk']>=$data_row['damageqty'])  &&($chk_stk['missing']>=$data_row['missingqty']) ){ $flag*=1; }else{ $flag*=0;}
				   if($flag==1){ $cancel*=1;}else{ $cancel*=0;}
			   }
			?>
              <tr>
                <td><?=$i?></td>
              <td><?=getProductDetails($data_row['prod_code'],"productname",$link1)."|".$data_row['prod_code']?></td>
              <td align="right"><?=$data_row['qty'];?></td>
               <td align="right"><?=$data_row['okqty'];?></td>
                <td align="right"><?=$data_row['damage'];?></td>
                <td align="right"><?=$data_row['missing'];?></td>  
                <td align="right"><?=$data_row['price'];?></td>  
             	<td align="right"><?=$data_row['value'];?></td>
             
			 	  <?php if($po_row['total_igst_amt'] == '0.00') {?>
             <td align="right"><?=$data_row['cgst_per'];?></td>
			  <td align="right"><?=$data_row['cgst_amt'];?></td>
			   <td align="right"><?=$data_row['sgst_per'];?></td>
			    <td align="right"><?=$data_row['sgst_amt'];?></td>
				<?php } else {?>
				 <td align="right"><?=$data_row['igst_per'];?></td>
				  <td align="right"><?=$data_row['igst_amt'];?></td>
				  <?php }?>
             <td align="right"><?=$data_row['totalvalue'];?></td>
			 <td><?php if($po_row['status'] == 'Received' ) {
				if($data_row['imei_attach'] == 'Y') {
				 echo "Serial No. is Attached";
				}
			else {echo "Pending to attach";} }?></td>
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
                <td><strong>
                  <?=$total_qty?>
                </strong></td>
                <td><strong>
                  <?=$total_okqty?>
                </strong></td>
                <td><strong>
                  <?=$total_dmgqty?>
                </strong></td>                
                <td>&nbsp;</td>
				<td>&nbsp;</td>
                <td><strong>
                  <?=currencyFormat($total_sub)?>
                </strong></td>
                <td>&nbsp;</td>
				 <?php if($total_igstamt == '0.00') {?>
				
                <td><strong>
                  <?=currencyFormat($total_cgstamt)?>
                </strong></td>
				<td>&nbsp;</td>
				<td><strong>
                  <?=currencyFormat($total_sgstamt)?>
                </strong></td>
				<?php } else { ?>
		<td>&nbsp;</td>
		<td><strong>
                  <?=currencyFormat($total_igstamt)?>
                </strong></td>
				<?php }?>
                <td><strong>
                  <?=currencyFormat($total_amt)?>
                </strong></td>
             <td><strong>
                 &nbsp;
                </strong></td>
              </tr>
              <tr>
                 <td colspan="9" align="right"><strong>Cancel Remark</strong></td>
                 <td colspan="7" align="left"><textarea name="grn_cancelrmk" id="grn_cancelrmk" class="form-control required" style="resize:none;width:500px;" required></textarea></td>
                 </tr>
             <tr>
                 <td colspan="16" align="center">
                 	<?php if($cancel==1){?><?php if($po_row['status'] != 'Cancelled') {?><input type="submit" class="btn btn-primary" name="Submit" id="upd" value="Cancel" ><?php }?><?php }else{ echo "Stock is not available";}?>
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='grnList.php?<?=$pagenav?>'"></td>
             </tr>
            </tbody>
          </table>

          
      </div><!--close panel body-->
    </div><!--close panel-->
    </form>
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

