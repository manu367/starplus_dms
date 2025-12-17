<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST[id]);
 $from=base64_decode($_REQUEST[from]);
 $to=base64_decode($_REQUEST[to]);
$po_sql="SELECT * FROM billing_master where challan_no='".$docid."' and from_location='".$to."' and to_location='".$from."' and document_type='RETAIL PRN' ";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);

////// final submit form ////
@extract($_POST);
if($_POST){
  if($_POST['Submit']=='Cancel'){
	  mysqli_autocommit($link1, false);
	  $flag = true;
	  $err_msg ="";
	  ///// cancel corporate invoice ///////////
	   $query1=("UPDATE billing_master set status='Cancelled',cancel_by='".$_SESSION['userid']."',cancel_date='$today',cancel_rmk='$remark',cancel_ip='$ip' where challan_no='".$docid."'");
	  $result = mysqli_query($link1,$query1);
	  //// check if query is not executed
	  if (!$result) {
	     $flag = false;
         $err_msg = "Error Code1:" . mysqli_error($link1) . ".";
      }
	 
	 ///// fetch details //////
	   $refno = explode("~", getAnyDetails($docid ,"ref_no","challan_no" ,"billing_master",$link1));
	   
	   $Finvoice_no = getAnyDetails($refno[0] ,"Finvoice_no","challan_no" ,"billing_master",$link1);
	  
	 
	  $vpo_sql="SELECT * FROM billing_model_data where challan_no='".$docid."'";
	  $vpo_res=mysqli_query($link1,$vpo_sql);
	  while($vpo_row=mysqli_fetch_assoc($vpo_res)){
	     //// update stock of from loaction
		 
	    $result=mysqli_query($link1, "update stock_status set okqty=okqty-'".$vpo_row['qty']."',updatedate='".$datetime."' where asc_code='".$vpo_row['from_location']."' and partcode='".$vpo_row['prod_code']."'");
		//// check if query is not executed
		if (!$result) {
		   $flag = false;
		   $err_msg = "Error Code2:" . mysqli_error($link1) . ".";
		 }
		 ///// insert in stock ledger////
		 $flag=stockLedger($docid,$today,$vpo_row['prod_code'],$vpo_row['from_location'],$_POST['from_loc'],$vpo_row['from_location'],"OUT","OK","Cancel Retail Return",$vpo_row['qty'],$vpo_row['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);	 
		 
		  /////// check if imei is attached then it should also cancelled or reverse to the from location
	   if($vpo_row['retail_return']=="Y"){
	  
		    $result=mysqli_query($link1, "delete from billing_imei_data where doc_no='".$docid."' and prod_code = '".$vpo_row['prod_code']."' ");
		      //// check if query is not executed
			  if (!$result) {
			     $flag = false;
				 echo "Error Code2.0: " . mysqli_error($link1) . ".";
			     }
	   }
       
	
	   //////////  SET RETAIL RETURN FLAG EQUAL TO BLANK SO THAT IT AGAIN REFLECT FRO RETAIL RETURN /////////////////////////////////
	  $data_table = mysqli_query($link1 , " update billing_model_data set  retail_return = '' where challan_no = '".$refno[0]."' and prod_code = '".$vpo_row['prod_code']."'   ");
	      if (!$data_table) {
			  $flag = false;
			  echo "Error Code2.11: " . mysqli_error($link1) . ".";
				 }	 
		 
	  }/// close for loop
	 
	  if($Finvoice_no != ''){
	    ///// set  Finovice no equal to blank  //////////////////////
	     $master_upd  = mysqli_query($link1 , "update billing_master set Finvoice_no = '' where challan_no = '".$refno[0]."'  ");	  
	     if (!$master_upd) {
				 $flag = false;
				echo "Error Code2.12: " . mysqli_error($link1) . ".";
				 }	
	     
	   }

	/// insert in party ledger
     $flag=partyLedger($_POST['to_loc'],$_POST['from_loc'],$docid,$today,$today,$currtime,$_SESSION['userid'],"CANCEL Retail Return",$_POST['grandtotal'],"DR",$link1,$flag);  
    ////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$docid,"CANCEL Retail Return","CANCEL",$ip,$link1,$flag);
	 ///// check  master  query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Invoice is Cancelled successfully with ref. no." .$docid ;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed ".$err_msg.". Please try again.";
	} 
    mysqli_close($link1);
  ///// move to parent page
    header("Location:retailcust_return.php?msg=".$msg."".$pagenav);
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
</script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-reply-all fa-lg"></i> Cancel Retail Return Details </h2><br/>
   <div class="panel-group">
     <form id="frm2" name="frm2" class="form-horizontal" action="" method="post" onSubmit="return myConfirm();">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1 ">Party Information</div>
		  <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php } ?>
        <div class="panel-body">
         <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Retail Return To:</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['from_location'],"name,city,state",$link1));?></td>
                <td width="20%"><label class="control-label">Retail Return From:</label></td>
                <td width="30%"><?php echo str_replace("~",",",getCustomerDetails($po_row['to_location'],"customername,city,state",$link1));?></td>
              </tr>
              <tr>
                <td><label class="control-label">Invoice No.:</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label"> Return Date:</label></td>
                <td><?php echo $po_row['sale_date'];?></td>
              </tr>
			    <tr>
                <td><label class="control-label">Reference Invoice No.</label></td>
                <td><?php echo $po_row['ref_no'];?></td>
                <td><label class="control-label">Reference Invoice Date</label></td>
                <td><?php echo $po_row['ref_date'];?></td>
              </tr>
               <tr>
                <td><label class="control-label">Entry By:</label></td>
                <td><?php echo getAdminDetails($po_row['entry_by'],"name",$link1);?></td>
                <td><label class="control-label"></label></td>
                <td></td>
              </tr>
             
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1 ">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th style="text-align:center" width="5%">#</th>
                <th style="text-align:center" width="20%">Product</th>
                <th style="text-align:center" width="15%">Return Qty</th>
                <th style="text-align:center" width="15%">Price</th>
                <th style="text-align:center" width="15%">Value</th>
                <th style="text-align:center" width="15%">Discount/Unit</th>
                <th width="6%"  style="text-align:center">SGST(%)</th>
                <th width="6%"  style="text-align:center">SGST Amt</th>
                <th width="6%"  style="text-align:center">CGST(%)</th>
                <th width="6%"  style="text-align:center">CGST Amt</th>
                <th width="6%"  style="text-align:center">IGST(%)</th>
                <th width="6%"  style="text-align:center">IGST Amt</th>
                <th style="text-align:center" width="15%">Total</th>
				
              </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM billing_model_data where challan_no='".$docid."' and from_location='".$to."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
			$proddet=explode("~",getProductDetails($podata_row['prod_code'],"productname,productcolor,productcode",$link1));
			?>
              <tr>
                <td><?=$i?></td>
               <td><?=$proddet[0]." | ".$proddet[1]." | ".$proddet[2]?></td>
                <td style="text-align:right"><?=$podata_row['qty']?></td>
                <td style="text-align:right"><?=$podata_row['price']?></td>
                <td style="text-align:right"><?=$podata_row['value']?></td>
                <td style="text-align:right"><?=$podata_row['discount']?></td>
                 <td style="text-align:right"><?=$podata_row['sgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['igst_per']?></td>
                <td style="text-align:right"><?=$podata_row['igst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['totalvalue']?></td>
                
              </tr>
            <?php
			 $tot_val+=$podata_row['value'];
			 $tot_dics+= $podata_row['discount'];
			$tot_qty+=$podata_row['qty'];
			$i++;
			
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1 "> Amount Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                 <td><label class="control-label"> Total Discount</label></td>
                <td><?php echo $tot_dics;?></td>
                <td width="20%"><label class="control-label">Total Qty:</label></td>
                <td width="30%"><?php echo $tot_qty;?></td>
              </tr>
              <tr>
              <td><label class="control-label"> Total Value</label></td>
                <td><?php echo $tot_val;?></td>
                 <td width="20%"><label class="control-label">Delivery Address:</label></td>
                <td width="30%"><?php echo $po_row['deliv_addrs'];?></td>
              </tr>
               <tr>
                 <td><label class="control-label"> Grand Total</label></td>
                <td><?php echo $po_row['total_cost'];?></td>
                <td><label class="control-label">Remark:</label></td>
                <td><?php echo $po_row['billing_rmk'];?></td>
              </tr>
			 
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
	<div class="panel panel-default table-responsive">
      <div class="panel-heading heading1"> Action </div>
      <div class="panel-body">
       
        <table class="table table-bordered" width="100%">
            <tbody>
              
              <tr>
                <td><label class="control-label">Cancel Remark <span class="red_small">*</span></label></td>
                <td><textarea name="remark" id="remark" required class="required form-control" onkeypress = "return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical;width:300px;"></textarea></td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Cancel" title="" <?php if($_POST['Submit']=='Cancel'){?>disabled<?php }?>>&nbsp;
				  <input type="hidden" name="from_loc" id="from_loc" value="<?=$from?>" >
				  <input type="hidden" name="to_loc" id="to_loc" value="<?=$to?>" >
				  <input type="hidden" name="grandtotal" id="grandtotal" value="<?=$po_row['total_cost']?>" >
 				  
                  <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='retailcust_return.php?<?=$pagenav?>'">
                  </td>
                </tr>
            </tbody>
          </table>
         
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
	</form>	
  </div><!--close panel group-->
  <br><br>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>