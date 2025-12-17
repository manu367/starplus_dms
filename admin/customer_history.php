<?php
////// Function ID ///////
$fun_id = array("a"=>array(76));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$getid=base64_decode($_REQUEST['id']);

////// get details of selected location////
$res_cust=mysqli_query($link1,"SELECT * FROM customer_master where customerid='".$getid."'")or die(mysqli_error($link1));
$row_custdet=mysqli_fetch_array($res_cust);


?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link href="../css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script>
	function makeTable(){
		$('#myTable').dataTable();
	}
	
	
	////// function for open model to see the purchase
function checkPurchase(refid){
	$.get('cust_purchasehistory.php?pk=' + refid, function(html){
		 $('#viewModal .modal-body').html(html);
		 $('#viewModal').modal({
			show: true,
			backdrop:"static"
		});
		//makeTable();
	 });
	 $("#viewModal #tile_name").html("<i class='fa fa-shopping-cart'></i> Purchase Details");
}
	
 </script>
 </head>
<body>
<div  class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-bank"></i> Customer History</h2>
      <h4 align="center">
          <?=$row_custdet['customername']."  (".$row_custdet['customerid'].")";?>
        </h4>
      <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
	  <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
    <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;Purchase Details</div>
         <div class="panel-body">
        <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
				  <td width="3%"><strong>Sno.</strong></td>
                    <td width="15%"><strong>Billing From</strong></td>
                    <td width="10%"><strong>Billing To</strong></td>
                    <td width="15%" ><strong>Invoice No.</strong></td>
                    <td width="10%"><strong>Invoice Date</strong></td> 
					<td width="10%"><strong>Amount</strong></td>                  
                    <td width="10%"><strong>Status</strong></td>
                    <td width="10%"><strong>Entry By</strong></td>
					<td width="10%" align="center"><strong>View</strong></td>
                  </tr>
                </thead>
                <tbody>
                <?php
				$i=1;
				////// get details of customer  for purchase  ////
				$res_locdet=mysqli_query($link1,"SELECT from_location,to_location,challan_no,sale_date,total_cost,status,entry_by  FROM billing_master where to_location='".$getid."'")or die(mysqli_error($link1));
				while($row_locdet = mysqli_fetch_array($res_locdet)){
				?>
                  <tr>
				  <td><?=$i?></td>
                    <td><?=getLocationDetails($row_locdet['from_location'],"name",$link1);?></td>
                    <td><?=getCustomerDetails($row_locdet['to_location'],"customername",$link1)?></td>
                    <td ><?=$row_locdet['challan_no']?></td>
                    <td><?=$row_locdet['sale_date']?></td>
					<td><?=$row_locdet['total_cost']?></td>
                    <td><?=$row_locdet['status']?></td>
                    <td><?=getAdminDetails($row_locdet['entry_by'],"name",$link1)?></td>				
                    <td align="center"><a href="#"  onClick="checkPurchase('<?=$row_locdet['challan_no']?>');"><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
                  </tr>
                  <?php
				  $i++;
				}
				  ?>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-rupee"></i>&nbsp;&nbsp;Payment History</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
				  <td width="5%"><strong>Sno.</strong></td>
                    <td width="15%"><strong>Receive From</strong></td>
                    <td width="10%"><strong>Receive By</strong></td>
                    <td width="15%" ><strong>Invoice No.</strong></td>
                    <td width="10%"><strong>Payment Mode</strong></td>
                   
                    <td width="10%"><strong>Amount</strong></td>
					<td width="10%"><strong>Receive Amt</strong></td>
					<td width="10%"><strong>Remark</strong></td>
					<td width="10%"><strong>Payment Date</strong></td>
                    <td width="10%"><strong>Status</strong></td>
					<td width="10%"><strong>Transcation Id</strong></td>
                  </tr>
                </thead>
                <tbody>
                <?php
				$k=1;
				////// get details of customer  for purchase  ////
				$res_pay=mysqli_query($link1,"SELECT * FROM payment_receive where from_location='".$getid."'")or die(mysqli_error($link1));
				while($row_paydet = mysqli_fetch_array($res_pay)){
				?>
                  <tr>
				  <td><?=$k?></td>
				  <td><?=getCustomerDetails($row_paydet['from_location'],"customername",$link1)?></td>
                   <td><?=getLocationDetails($row_paydet['to_location'],"name",$link1);?></td>                    
                    <td ><?=$row_paydet['doc_no']?></td>
                    <td><?=$row_paydet['payment_mode']?></td>
					<td><?=$row_paydet['amount']?></td>
					<td><?=$row_paydet['rec_amount']?></td>
					<td><?=$row_paydet['remark']?></td>
					<td><?=$row_paydet['payment_date']?></td>
                    <td><?=$row_paydet['status']?></td>			
                    <td><?=$row_paydet['transaction_id']?></td>
                  </tr>
                  <?php
				  $k++;
				}
				  ?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Ledger Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
                    <td width="5%"><strong>Sno.</strong></td>
                    <td width="10%"><strong>Location Name</strong></td>
                    <td width="15%" ><strong>Party Name</strong></td>
					 <td width="15%" ><strong>Document No.</strong></td>
                    <td width="10%"><strong>Document Type</strong></td>                  
                    <td width="10%"><strong>Reward Points</strong></td>
					<td width="10%"><strong>CR/DR Type</strong></td>
					<td width="10%"><strong>Amount</strong></td>
                    <td width="10%"><strong>Entry Date</strong></td>
                  </tr>
                </thead>
                <tbody>
                <?php
				
				////// get details of customer  for purchase  ////
				$res_ledger=mysqli_query($link1,"SELECT * FROM party_ledger where cust_id='".$getid."' order by entry_date desc")or die(mysqli_error($link1));

				$j=1;
				while($row_ledgerdet = mysqli_fetch_array($res_ledger)){
				?>
                  <tr>
				  <td><?=$j?></td>
				  <td><?=getLocationDetails($row_ledgerdet['location_code'],"name",$link1);?></td>  
				  <td><?=getCustomerDetails($row_ledgerdet['cust_id'],"customername",$link1)?></td>                                     
                    <td ><?=$row_ledgerdet['doc_no']?></td>
					<td ><?=$row_ledgerdet['doc_type']?></td>
                    <td align="center">0</td>
					<td><?=$row_ledgerdet['cr_dr']?></td>
					<td><?=$row_ledgerdet['amount']?></td>
                    <td><?=$row_ledgerdet['entry_date']?></td>			
                    
                  </tr>
                  <?php
				  if($row_ledgerdet['cr_dr'] == 'CR'){ $cr+=$row_ledgerdet['amount'];} 
				  if($row_ledgerdet['cr_dr'] == 'DR'){ $dr+=$row_ledgerdet['amount'];}
				  $j++;
				   }
			       $balance = $cr - $dr;
				$m= $j;
				////  fetch details from customer reward ledger ////////////////////////////////////
				$res_custledger=mysqli_query($link1,"SELECT * FROM customer_reward_ledger where customer_id ='".$getid."'  ")or die(mysqli_error($link1));
			
				while($row_custledgerdet = mysqli_fetch_array($res_custledger)){
				$loc_code = mysqli_fetch_array(mysqli_query($link1 , "select from_location from billing_master where challan_no = '".$row_custledgerdet['ref_no']."' "));
				?>
                  <tr>
				  <td><?=$m?></td>
				  <td><?=getLocationDetails($loc_code['from_location'],"name",$link1);?></td>  
				  <td><?=getCustomerDetails($row_custledgerdet['customer_id'],"customername",$link1)?></td>                                     
                    <td ><?=$row_custledgerdet['ref_no']?></td>
					<td >RETAIL</td>
                    <td align="center"><?=$row_custledgerdet['rewards']?></td>
					<td><?= $row_custledgerdet['cr_dr_type']?></td>
					<td><?=$row_custledgerdet['rewards']?></td>
                    <td><?=$row_custledgerdet['entry_date']?></td>			
                    
                  </tr>
                  <?php
				  $m++;
				  $totreward+= $row_custledgerdet['rewards'];
				  }
				
				  ?>
            </tbody>
			<tr>
			<td colspan="7" align="right"><strong>Total Amount :</strong></td>
			<td colspan="2"><?=$balance?></td>
			</tr>
			<tr>
			<td colspan="7" align="right"><strong>Total Reward Points :</strong></td>
			<td colspan="2"><?=$totreward?></td>
			</tr>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	 </div><!--close panel-->
	 
	 
	  <div class="form-group">
                                <div class="col-md-12" align="center">
                                   
                                    <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href = 'customer_details.php?<?= $pagenav ?>'">
                                    
                                </div>
                            </div>
   
	</form>
	
	<!-- Start Inventory Modal -->
<div class="modal modalTH fade" id="viewModal" role="dialog">
	<div class="modal-dialog modal-dialogTH modal-lg">
  		<!-- Modal content-->
  		<div class="modal-content">
    		<div class="modal-header">
      			<button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title" align="center" id="tile_name"></h2>
    		</div>
    		<div class="modal-body modal-bodyTH">
     			<!-- here dynamic task details will show -->
    		</div>
    		<div class="modal-footer">
      			<button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
    		</div>
  		</div>
	</div>
</div>
<!--close Inventory modal-->

</div><!--close container-fluid-->
<?php
include("../includes/connection_close.php");
?>
</body>
</html>