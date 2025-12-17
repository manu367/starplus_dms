<?php
require_once("../config/config.php");
$getid=base64_decode($_REQUEST['asccode']);
////// get details of selected location////
$res_locdet=mysqli_query($link1,"SELECT * FROM asc_master where asc_code='".$getid."'")or die(mysqli_error($link1));
$row_locdet=mysqli_fetch_array($res_locdet);
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
 </script>
 </head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-bank"></i> Location History</h2>
      <h4 align="center">
          <?=$row_locdet['name']."  (".$row_locdet['asc_code'].")";?>
        </h4>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      <div class="row">
      	<div class="col-sm-4 py-2">
            <div class="card h-100 text-white bg-success">
                <div class="card-body">
                    <div style="float:left;display:inline-block"><h3 class="card-title">SALE</h3></div> <div style="float:right;display:inline-block"><h3 class="card-title"><i class='fa fa-edit'></i></h3></div>
                    <p class="card-text">Here you can see all sales done by selected location.</p>
                    <a href="#" class="btn btn-outline-light" onClick="checkSale('<?=$row_locdet['asc_code']?>');">View</a>
                </div>
            </div>
        </div>
        <div class="col-sm-4 py-2">
            <div class="card h-100 text-white bg-warning">
                <div class="card-body">
                    <div style="float:left;display:inline-block"><h3 class="card-title">PURCHASE</h3></div> <div style="float:right;display:inline-block"><h3 class="card-title"><i class='fa fa-shopping-cart'></i></h3></div>
                    <p class="card-text">Here you can see all purchase done by selected location.</p>
                    <a href="#" class="btn btn-outline-light" onClick="checkPurchase('<?=$row_locdet['asc_code']?>');">View</a>
                </div>
            </div>
        </div>
        <div class="col-sm-4 py-2">
            <div class="card h-100 text-white bg-info">
                <div class="card-body">
                    <div style="float:left;display:inline-block"><h3 class="card-title">ACCOUNT LEDGER</h3></div> <div style="float:right;display:inline-block"><h3 class="card-title"><i class='fa fa-exchange'></i></h3></div>
                    <p class="card-text">Here you can see all account ledger done by selected location.</p>
                    <a href="#" class="btn btn-outline-light" onClick="checkAccountLedger('<?=$row_locdet['asc_code']?>');">View</a>
                </div>
            </div>
        </div>
       </div>
       <div class="row">
       	<div class="col-sm-4 py-2">
            <div class="card h-100 text-white bg-secondary">
                <div class="card-body">
                    <div style="float:left;display:inline-block"><h3 class="card-title">INVENTORY</h3></div> <div style="float:right;display:inline-block"><h3 class="card-title"><i class='fa fa-database'></i></h3></div>
                    <p class="card-text">Here you can see all inventory available on selected location.</p>
                    <a href="#" class="btn btn-outline-light" onClick="checkInventory('<?=$row_locdet['asc_code']?>');">View</a>
                </div>
            </div>
        </div>
       	<div class="col-sm-4 py-2">
            <div class="card h-100 text-white bg-danger">
                <div class="card-body">
                    <h3 class="card-title">PAYMENT TAT</h3>
                    <p class="card-text">Here you can see details of payment adjusted with invoice of selected location.</p>
                    <a href="#" class="btn btn-outline-light">View</a>
                </div>
            </div>
        </div>
        <div class="col-sm-4 py-2">
            <div class="card h-100 text-white bg-success">
                <div class="card-body">
                    <h3 class="card-title">LOCATION STATICS</h3>
                    <p class="card-text">Created on: <?=$row_locdet["start_date"]?><br/>Last update on: <?=$row_locdet["update_date"]?></p>
                    <a href="#" class="btn btn-outline-light" onClick="checkLocInfo('<?=$row_locdet['asc_code']?>');">View</a>
                </div>
            </div>
        </div>
       </div>
      	<div class="form-group">
            <div class="col-md-12" align="center">
            <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='asp_details.php?<?=$pagenav?>'">
            </div>
         </div> 
      </div><!--End form group-->
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
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
<!-- Start view information Modal -->
<div class="modal modalTH fade" id="viewInfoModal" role="dialog">
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
<!--close view information modal-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>
////// function for open model to see the inventory
function checkInventory(refid){
	$.get('inventory_status.php?pk=' + refid, function(html){
		 $('#viewModal .modal-body').html(html);
		 $('#viewModal').modal({
			show: true,
			backdrop:"static"
		});
		makeTable();
	 });
	 $("#tile_name").html("<i class='fa fa-database'></i> Inventory Status");
}
////// function for open model to see the sale
function checkSale(refid){
	$.get('sale_status.php?pk=' + refid, function(html){
		 $('#viewModal .modal-body').html(html);
		 $('#viewModal').modal({
			show: true,
			backdrop:"static"
		});
		makeTable();
	 });
	 $("#viewModal #tile_name").html("<i class='fa fa-edit'></i> Sale Status");
}
////// function for open model to see the purchase
function checkPurchase(refid){
	$.get('purchase_status.php?pk=' + refid, function(html){
		 $('#viewModal .modal-body').html(html);
		 $('#viewModal').modal({
			show: true,
			backdrop:"static"
		});
		makeTable();
	 });
	 $("#viewModal #tile_name").html("<i class='fa fa-shopping-cart'></i> Purchase Status");
}
////// function for open model to see the account ledger
function checkAccountLedger(refid){
	$.get('account_status.php?pk=' + refid, function(html){
		 $('#viewModal .modal-body').html(html);
		 $('#viewModal').modal({
			show: true,
			backdrop:"static"
		});
		makeTable();
	 });
	 $("#viewModal #tile_name").html("<i class='fa fa-check-circle'></i> Account Ledger");
}
////// function for open modal to view invoice details
function checkInvInfo(refid){
	$.get('invoiceInfo.php?pk=' + refid, function(html){
		 $('#viewInfoModal .modal-body').html(html);
		 $('#viewInfoModal').modal({
			show: true,
			backdrop:"static"
		});
	 });
	 $("#viewInfoModal #tile_name").html(refid);
}
////// function for open modal to view location update details
function checkLocInfo(refid){
	$.get('location_update_history.php?pk=' + refid, function(html){
		 $('#viewInfoModal .modal-body').html(html);
		 $('#viewInfoModal').modal({
			show: true,
			backdrop:"static"
		});
		makeTable();
	 });
	 $("#viewInfoModal #tile_name").html(refid);
}
</script>
</body>
</html>