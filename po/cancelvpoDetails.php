<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM vendor_order_master WHERE po_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
////// final submit form ////
@extract($_POST);
if($_POST){
	if($_POST['Submit']=='Cancel'){
		mysqli_autocommit($link1, false);
	  	$flag = true;
	  	$err_msg = "";
		if($po_row["status"]=="Received"){ $cnlstep = "After Received";}else{$cnlstep = "Before Received";}
		if($po_row["billing_type"]=="COMBO"){ $narr1 = "Cancel Combo Purchase"; $narr2 = "COMBO PURCHASE";}else{ $narr1 = "Cancel Local Purchase"; $narr2 = "LP";}
	 	///// cancel vpo ///////////
	  	$query1 = "UPDATE vendor_order_master SET status='Cancelled', cancel_by='".$_SESSION['userid']."', cancel_date='".$today."', cancel_rmk='".$remark."', cancel_step='".$cnlstep."', cancel_ip='".$ip."' WHERE po_no='".$docid."'";
	  	$result1 = mysqli_query($link1,$query1);
	  	//// check if query is not executed
	  	if (!$result1) {
	    	$flag = false;
         	$err_msg="Error details1: ".mysqli_error($link1).".";
      	}
	  	////// insert in stock ledger////
	  	$vpo_sql = "SELECT * FROM vendor_order_data WHERE po_no='".$docid."'";
      	$vpo_res = mysqli_query($link1,$vpo_sql);
	  	$i=1;
       	while($vpo_row=mysqli_fetch_assoc($vpo_res)){
			// check if status is received
			if($po_row["status"]=="Received"){
				//// there is serive type product
				///// check is there any service in line item or combo
		  		$proddet = getAnyDetails($vpo_row['prod_code'],"is_service","productcode","product_master",$link1);
				if($proddet=="Y" || $vpo_row["prod_cat"]=="C"){
					
				}else{
					//// update stock of from loaction
					$result2=mysqli_query($link1, "UPDATE stock_status SET qty=qty-'".$vpo_row['req_qty']."', okqty = okqty-'".$vpo_row['okqty']."',broken=broken-'".$vpo_row['damageqty']."',missing=missing-'".$vpo_row['missingqty']."',updatedate='".$datetime."' WHERE asc_code='".$po_row['po_to']."' AND sub_location='".$po_row['sub_location']."' AND partcode='".$vpo_row['prod_code']."'");
					//// check if query is not executed
					if (!$result2) {
						$flag = false;
						$err_msg = "Error Code2:" . mysqli_error($link1) . ".";
					}
					### CASE 1 if user enter somthing in ok qty
					if($vpo_row['okqty']!="" && $vpo_row['okqty']!=0 && $vpo_row['okqty']!=0.00){
						$flag=stockLedger($vpo_row['po_no'],$today,$vpo_row['prod_code'],$po_row['po_from'],$po_row['sub_location'],$po_row['sub_location'],"OUT","OK",$narr1,$vpo_row['okqty'],$vpo_row['po_price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag); 
						   
					}
					### CASE 2 if user enter somthing in damage qty
					if($vpo_row['damageqty']!="" && $vpo_row['damageqty']!=0 && $vpo_row['damageqty']!=0.00){
						$flag=stockLedger($vpo_row['po_no'],$today,$vpo_row['prod_code'],$po_row['po_from'],$po_row['sub_location'],$po_row['sub_location'],"OUT","DAMAGE",$narr1,$vpo_row['damageqty'],$vpo_row['po_price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
					}
					### CASE 3 if user enter somthing in missing qty
					if($vpo_row['missingqty']!="" && $vpo_row['missingqty']!=0 && $vpo_row['missingqty']!=0.00){
						$flag=stockLedger($vpo_row['po_no'],$today,$vpo_row['prod_code'],$po_row['po_from'],$po_row['sub_location'],$po_row['sub_location'],"OUT","MISSING",$narr1,$vpo_row['missingqty'],$vpo_row['po_price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
					}
				}
			}
		   	///release from stock status/
		   	//$flag=releaseStock($po_row['po_to'],$po_row['po_to'],$vpo_row['prod_code'],$vpo_row['okqty'],$vpo_row['damageqty'],$vpo_row['missingqty'],$link1,$flag); 	 
	 		/////////////  cancel imei  condition (updated by priya on 23 august) ////////////////////////////////////////////////////////////////////////////
			if($vpo_row['imei_attach'] == 'Y'){
				///////////   check if stock is available /////////////////////////////////////////////////// 			
				$billing_data = mysqli_query ($link1 ,"SELECT * FROM billing_imei_data WHERE doc_no = '".$docid."' AND prod_code = '".$vpo_row['prod_code']."'");
				if(mysqli_num_rows($billing_data) > 0) {
					while ($row = mysqli_fetch_array($billing_data)){		
						//////   insert deleted imeis into new table///////////////////////////////////////////////////
						$result3 = mysqli_query($link1,"insert into cancel_imei_data set from_location = '".$row['from_location']."' , to_location = '".$row['to_location']."' , owner_code = '".$row['owner_code']."' , prod_code = '".$row['prod_code']."' , doc_no = '".$row['doc_no']."' , imei1 = '".$row['imei1']."' , imei2 = '".$row['imei2']."' , flag = '".$row['flag']."'  , stock_type = '".$row['stock_type']."' ");	
						//// check if query is not executed
						if (!$result3) {
							$flag = false;
							$err_msg = "Error Code3:" . mysqli_error($link1) . ".";
						}	
					}
	  				///////////   delete entry from billing imei data /////////////////////////////////////////////////////////
					$result4 = mysqli_query($link1, "DELETE FROM billing_imei_data WHERE doc_no='".$docid."' AND prod_code = '".$vpo_row['prod_code']."'");
		      		//// check if query is not executed////////
			  		if (!$result4) {
						$flag = false;
						$err_msg = "Error Code4:" . mysqli_error($link1) . ".";
			  		}
		 		} 
		 	}
 		 	////////////////////////////  end of imei cancel condition ///////////////////////////////////////////////////////////////////////////////////////////////////
	 		$i++;
		} //////////// end of while //////////////////////////////////////////////////// 
		if($po_row["status"]=="Received"){
			///// cancel corporate invoice ///////////
			$query41="UPDATE billing_master SET status='Cancelled',cancel_by='".$_SESSION['userid']."',cancel_date='".$today."',cancel_rmk='".$remark."',cancel_step='After ".$po_row["status"]."',cancel_ip='".$ip."' WHERE challan_no='".$docid."'";
			$result41 = mysqli_query($link1,$query41);
			//// check if query is not executed
			if (!$result41) {
				$flag = false;
			 	$err_msg = "Error Code41:" . mysqli_error($link1) . ".";
			}
			//// update cr bal 
		   	$result5 = mysqli_query($link1,"UPDATE current_cr_status SET cr_abl=cr_abl-'".$po_row["total_amt"]."',total_cr_limit=total_cr_limit-'".$po_row["total_amt"]."', last_updated='".$datetime."' WHERE parent_code='".$po_row['po_to']."' AND asc_code='".$po_row['po_from']."'");
			/// check if query is not executed
			if (!$result5) {
				$flag = false;
				$err_msg = "Error Code5:" . mysqli_error($link1) . ".";
			}	
			/// insert in party ledger
			$flag=partyLedger($po_row['po_to'],$po_row['po_from'],$docid,$today,$today,$currtime,$_SESSION['userid'],$narr1,$po_row["total_amt"],"DR",$link1,$flag);  
			////// insert in activity table////
			$flag=dailyActivity($_SESSION['userid'],$docid,$narr2,"CANCELLED",$ip,$link1,$flag);
		} 		
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$docid,"VPO","CANCELLED",$ip,$link1,$flag);
		///// check  master  query are successfully executed
		if ($flag) {
			mysqli_commit($link1);
			$msg = "Local Purchase is  successfully Cancelled having PO No." .$docid ;
		} else {
			mysqli_rollback($link1);
			$msg = "Request could not be processed. Please try again.".$err_msg;
		} 
		mysqli_close($link1);
		///// move to parent page
		header("Location:localPurchaseList.php?msg=".$msg."".$pagenav);
		exit;
	}/// close if condition 
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
</script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-ship"></i>Cancel Local Purchase</h2><br/>
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Purchase Order To</label></td>
                 <td width="30%"><i><?php echo str_replace("~",",",getLocationDetails($po_row['po_to'],"name,city,state",$link1));?></i></td>
                <td width="20%"><label class="control-label">Purchase Order From</label></td>               
				 <td width="30%"><i><?php echo getAnyParty($po_row['po_from'],$link1);?></i></td>
              </tr>
              <tr>
                <td><label class="control-label">Purchase Order No.</label></td>
                <td><?php echo $po_row['po_no'];?></td>
                <td><label class="control-label">Purchase Order Date</label></td>
                <td><?php echo $po_row['requested_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><i><?php echo getAdminDetails($po_row['create_by'],"name",$link1);?></i></td>
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
                <td><label class="control-label">Document Type</label></td>
                <td><?php if($po_row['document_type']=="DC"){ echo $po_row['document_type']." (".$po_row["ledger_name"].")";}else{ echo $po_row['document_type'];}?></td>
                <td><label class="control-label">Cost Center</label></td>
                <td><?php  
					$billfrom=getLocationDetails($po_row['sub_location'],"name,city,state",$link1);
				  $explodevalf=explode("~",$billfrom);
				  if($explodevalf[0]){ $fromparty=$billfrom; }else{ $fromparty=getAnyDetails($po_row['sub_location'],"sub_location_name","sub_location","sub_location_master",$link1);} echo str_replace("~",",",$fromparty);?></td>
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
			$cancel=1;
			$podata_sql="SELECT * FROM vendor_order_data where po_no='".$docid."'";
            $podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,model_name,productcode",$link1));
				$flag=1;
				if($po_row["status"]=="Received"){
					if($podata_row["prod_cat"]!="C"){
					$res=mysqli_query($link1,"SELECT SUM(okqty) AS okstk, SUM(broken) AS brokestk, SUM(missing) AS missing FROM stock_status WHERE asc_code='".$po_row['po_to']."' AND sub_location='".$po_row['sub_location']."' AND partcode='".$podata_row['prod_code']."'")or die(mysqli_error($link1));		
					$chk_stk=mysqli_fetch_assoc($res);
					if(($chk_stk['okstk']>=$podata_row['okqty']) && ($chk_stk['brokestk']>=$podata_row['damageqty'])  &&($chk_stk['missing']>=$podata_row['missingqty']) ){ $flag*=1; }else{ $flag*=0;}
				   if($flag==1){ $cancel*=1;}else{ $cancel*=0;}
				   }
			   }
			?>
             <tr>
                <td><?=$i?></td>
              	<td><?php if($podata_row["prod_cat"]=="C"){ echo $podata_row["combo_name"];}else{ echo $proddet[0]." (".$proddet[1].")";}?></td>
                <td style="text-align:right"><?=$podata_row['req_qty']?></td>
                <td style="text-align:right"><?=$podata_row['po_price']?></td>
                <td style="text-align:right"><?=$podata_row['po_value']?></td>
                <td style="text-align:right"><?=$podata_row['okqty']?></td>
                <td style="text-align:right"><?=$podata_row['damageqty']?></td>
                <td style="text-align:right"><?=$podata_row['missingqty']?></td>
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
                <td width="20%"><label class="control-label">Tax Amount</label></td>
                <td width="30%"><?php echo $po_row['taxamount'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Grand Total</label></td>
                <td><?php echo currencyFormat($po_row['po_value']+$po_row['taxamount']);?></td>
                <td><label class="control-label">Tax Type</label></td>
                <td><?php echo $po_row['taxtype']; if($po_row['taxper']!=0){ echo " @ ".$po_row['taxper']."%";}?></td>
              </tr>
               <tr>
                <td><label class="control-label">Delivery Address</label></td>
                <td><?php echo $po_row['delivery_address'];?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['remark'];?></td>
              </tr>
               <tr>
                 <td><label class="control-label">Receive By</label></td>
                 <td><i><?php echo getAdminDetails($po_row['receive_by'],"name",$link1);?></i></td>
                 <td><label class="control-label">Receive Date</label></td>
                 <td><?php echo $po_row['receive_date'];?></td>
               </tr>
               <tr>
                 <td><label class="control-label">Receive Remark</label></td>
                 <td colspan="3"><?php echo $po_row['receive_remark'];?></td>
                </tr>
				 <tr>
                 <td><label class="control-label">Cancel Remark <span style="color:#F00">*</span></label></td>
                 <td colspan="3"><textarea name="remark" id="rcv_rmk" class="form-control required" style="resize:none;width:500px;" required></textarea></td>
                 </tr>
               <tr>
                 <td colspan="4" align="center">
                     <?php if($cancel==1){?><input type="submit" class="btn <?=$btncolor?>" name="Submit" id="upd" value="Cancel" ><?php }else{ echo "Stock is not available";}?>
                    
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