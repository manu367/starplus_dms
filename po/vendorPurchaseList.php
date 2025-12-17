<?php
require_once("../config/config.php");
$_SESSION['msgVPO']="";
if($_REQUEST["fdate"]){
	$fdatefilter = " AND entry_date >= '".$_REQUEST["fdate"]."'";
}
if($_REQUEST["tdate"]){
	$tdatefilter = " AND entry_date <= '".$_REQUEST["tdate"]."'";
}
if($_REQUEST["po_to"]){
	$vndfilter = " AND po_to = '".$_REQUEST["po_to"]."'";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="../js/jquery-1.10.1.min.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="../css/bootstrap-select.min.css">
<script src="../js/bootstrap-select.min.js"></script>
<script>
$(document).ready(function(){
	$('#myTable').dataTable();
});
$(document).ready(function () {
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true,
		todayHighlight:true
	});
});
$(document).ready(function () {
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true,
		todayHighlight:true
	});
});
</script>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
	<div class="container-fluid">
  		<div class="row content">
		<?php 
    	include("../includes/leftnav2.php");
    	?>
    		<div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      			<h2 align="center"><i class="fa fa-ship"></i> Vendor Purchase List</h2>
      			<?php if($_REQUEST['msg']){?><br>
      			<h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      			<?php }?>
                 <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
    				<div class="form-group">
          				<div class="col-md-10"><label class="col-md-3 control-label">PO From Date</label>
             				<div class="col-md-3 input-append date">
  								<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" autocomplete="off"></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   				</div>
              				<label class="col-md-3 control-label">PO To Date</label>
              				<div class="col-md-3 input-append date">
  								<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>"style="width:160px;" autocomplete="off"></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   				</div>
           				</div>
        			</div>
                    <div class="form-group">
                    	<div class="col-md-10"><label class="col-md-3 control-label">Select Vendor</label>
                        	<div class="col-md-6">
                            	<select name="po_to" id="po_to" class="form-control selectpicker" data-live-search="true">
                             		<option value="" selected="selected">All</option>
									<?php 
                                    $sql_parent = "SELECT * FROM vendor_master WHERE status='active'";
                                    $res_parent = mysqli_query($link1,$sql_parent);
                                    while($result_parent=mysqli_fetch_array($res_parent)){
                                    ?>
                                	<option data-tokens="<?=$result_parent['name']." | ".$result_parent['id']?>" value="<?=$result_parent['id']?>" <?php if($result_parent['id']==$_REQUEST['po_to'])echo "selected";?>><?=$result_parent['name']." | ".$result_parent['city']." | ".$result_parent['state']." | ".$result_parent['country']?></option>
                                	<?php
                                	}
                                	?>
                             	</select>
                          	</div>
                            <div class="col-md-3">
                            	<input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                               	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                               	<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                            </div>
                        </div>
                	</div>
                </form>    
      			<form class="form-horizontal" role="form">
        			<button title="Add New PO" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='addNewVendPON.php?op=add<?=$pagenav?>'">
                    	<span>Add New PO</span>
                    </button>
      				<div class="form-group" id="page-wrap" style="margin-left:10px;">
       					<table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          					<thead>
            					<tr class="<?=$tableheadcolor?>" >
                                    <th>S.No</th>
                                    <th>PO From</th>
                                    <th>PO To</th>
                                    <th>PO No.</th>
                                    <th>PO Date</th>
                                    <th>Entry By</th>
                                    <th>Status</th>
                                    <th>View</th>
                                    <th>Print</th>
                             <!--    <th>Receive</th> -->
                                    <th>Cancel</th>
                              	</tr>
                     		</thead>
          					<tbody>
            				<?php
							$sno=0;
							//// get cancel rights
							$isCnlRight = getCancelRightNew($_SESSION['userid'],"9",$link1);
							///// get access location ///
							$accesslocation = getAccessLocation($_SESSION['userid'],$link1);	
							$sql = mysqli_query($link1,"SELECT * FROM vendor_order_master WHERE req_type='VPO' AND po_from IN (".$accesslocation.") ".$fdatefilter." ".$tdatefilter." ".$vndfilter." ORDER BY id DESC");
							while($row=mysqli_fetch_assoc($sql)){
				  				$sno=$sno+1;
							?>
            					<tr class="even pointer">
                                    <td><?php echo $sno;?></td>
                                    <td><?php echo str_replace("~",",",getLocationDetails($row['po_from'],"name,city,state",$link1));?></td>
                                    <td><?php echo str_replace("~",",",getVendorDetails($row['po_to'],"name,city,state",$link1));?></td>
                                    
                                    <td><?php echo $row['po_no'];?></td>
                                    <td><?php echo dt_format($row['requested_date']);?></td>
                                    <td><?php echo getAdminDetails($row['create_by'],"name",$link1);?></td>
                                    <td <?php if($row['status']=="Pending"){ echo "class='red_small'";}?>><?php echo $row['status'];?></td>
                                    <td align="center"><a href='vpoDetailsN.php?op=edit&id=<?php echo base64_encode($row['po_no']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
                                     <td align="center"><a href='../print/vpo_print.php?id=<?php echo base64_encode($row['po_no']);?><?=$pagenav?>'  title='view' target='_blank'><i class="fa fa-print fa-lg" title="PO Print"></i></a></td>
                                  <!--  <td align="center"><?php if($row['status']=="Pending"){?><a href='recvpoDetails.php?op=edit&id=<?php echo base64_encode($row['po_no']);?><?=$pagenav?>'  title='Receive PO'><i class="fa fa-shopping-bag fa-lg" title="Receive PO"></i></a><?php } ?></td> -->
                                    <td align="center"><?php if($isCnlRight == 1){ if($row[status]!='Received' && $row[status]!='Cancelled') { ?><a href='cancelvendorPurchaseN.php?op=cancel&id=<?php echo base64_encode($row['po_no']);?><?=$pagenav?>'  title='Cancel PO'><i class="fa fa-trash fa-lg" title="Cancel PO"></i></a><?php } }?></td>
                            	</tr>
                         	<?php }?>
                 			</tbody>
                    	</table>
      				</div>
      			</form>
    		</div>
  		</div>
	</div>
	<?php
	include("../includes/footer.php");
	include("../includes/connection_close.php");
	?>
</body>
</html>