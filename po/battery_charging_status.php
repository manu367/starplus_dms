<?php
require_once("../config/config.php");
///// if action taken
if($_POST['upddckt']=="Update"){
	////initialize params
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg = "";
	$ref_no = base64_decode($_POST['ref_no']);
	$serial_no = base64_decode($_POST['serial_no']);
	$part_code = base64_decode($_POST['part_code']);
	$sql_doc = "UPDATE battery_charging_status SET input_voltage = '".$_POST["inputvoltage"]."', output_voltage='".$_POST["outputvoltage"]."', status='CHARGED', last_charging_date='".$today."', update_date='".$datetime."', update_by='".$_SESSION["userid"]."' where id='".$ref_no."' ";
	$res_doc = mysqli_query($link1,$sql_doc);
	//// check if query is not executed
	if(!$res_doc){
		$flag = false;
		$error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}
	////// insert in charging history
	$sql_hist = "INSERT INTO battery_charging_history SET serial_no='".$serial_no."', prod_code='".$part_code."', input_voltage = '".$_POST["inputvoltage"]."', output_voltage='".$_POST["outputvoltage"]."', charging_remark='".$_POST["chgrmk"]."', charging_date='".$today."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."', update_ip='".$ip."'";
	$res_hist = mysqli_query($link1,$sql_hist);
	//// check if query is not executed
	if(!$res_hist){
		$flag = false;
		$error_msg = "Error details2: " . mysqli_error($link1) . ".";
	}
	///// check both query are successfully executed
	if ($flag) {
		mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
		$msg = "Battery charging details is succussfully updated against serial no. ".$serial_no;
	} else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
	header("location:battery_charging_status.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= siteTitle ?></title>
    <script src="../js/jquery-1.10.1.min.js"></script>
 	<link href="../css/font-awesome.min.css" rel="stylesheet">
 	<link href="../css/abc.css" rel="stylesheet">
 	<script src="../js/bootstrap.min.js"></script>
 	<link href="../css/abc2.css" rel="stylesheet">
 	<link rel="stylesheet" href="../css/bootstrap.min.css">
 	<link rel="stylesheet" href="../css/bootstrap-select.min.css">
 	<script src="../js/bootstrap-select.min.js"></script>
    <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 	<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 	<script type="text/javascript">
	
	$(document).ready(function () {
		$('#fdate').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
	});
	$(document).ready(function () {
		$('#tdate').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
	});
	$(document).ready(function(){
		$('#myTable').dataTable();		
	});
	////// function for open model to update battery charging details
	function openActionModel(docid, ptycode){
	//alert(docid+"    "+ptycode);
		$.get('fill_battery_charging_model.php?doc_id='+docid+'&partycode='+ptycode, function(html){
			 $('.modal-dialog,.modal-dialogTH,.modal-lg').removeClass('modal-dialog modal-dialogTH modal-lg').addClass('modal-dialog modal-dialogTH');
			 $('#actionModel .modal-title').html('<i class="fa fa-battery-three-quarters fa-lg faicon"></i> Update Battery Charging Details');
			 $('#actionModel .modal-body').html(html);
				var showbtn = '<input type="submit" class="btn btn-primary" name="upddckt" id="upddckt" value="Update" <?php if($_POST['upddckt']=='Update'){?>disabled<?php }?>><button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>';
			 $('#actionModel .modal-footer').html(showbtn);
			 $('#actionModel').modal({
				show: true,
				backdrop:"static"
			});
		 });
	}
	////// function for open model to see charging history
	function openHistoryModel(sno,prodc,impdate){
	//alert(docid+"    "+ptycode);
		$.get('battery_charging_history_model.php?serialno='+sno+'&prodcode='+prodc+'&importdate='+impdate, function(html){
			 $('.modal-dialog,.modal-dialogTH').addClass('modal-lg');
			 $('#actionModel .modal-title').html('<i class="fa fa-history fa-lg faicon"></i> Battery Charging History');
			 $('#actionModel .modal-body').html(html);
				var showbtn = '<button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>';
			 $('#actionModel .modal-footer').html(showbtn);
			 $('#actionModel').modal({
				show: true,
				backdrop:"static"
			});
		 });
	}
	</script>
 	<link rel="stylesheet" href="../css/datepicker.css">
	<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
	<div class="container-fluid">
   		<div class="row content">
            <?php
            include("../includes/leftnav2.php");
            ?>
            <div class="col-sm-9 tab-pane fade in active" id="home">
            	<h2 align="center"><i class="fa fa-battery-three-quarters"></i> Battery Charging Details</h2>
                <?php if($_REQUEST['msg']){?>
                <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
                </div>
                <?php 
					unset($_POST);
                 }?>
              	<form class="form-horizontal" role="form" name="form1" action="" method="post">
                	<div class="row">
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">From Date</label>
                            <input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>">
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
                            <input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>">
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Vendor Name</label>
                            <select name="vendor_name" id="vendor_name" class="form-control selectpicker" data-live-search="true"  onChange="document.form1.submit();">
                                    <option value="">--Please select--</option>
                                    <?php 
                                    $sql_parent = "SELECT * FROM vendor_master WHERE status='active'";
                                    $res_parent = mysqli_query($link1,$sql_parent);
                                    while($result_parent=mysqli_fetch_array($res_parent)){
                                    ?>
                                	<option data-tokens="<?=$result_parent['name']." | ".$result_parent['id']?>" value="<?=$result_parent['id']?>" <?php if($result_parent['id']==$_REQUEST['vendor_name'])echo "selected";?>><?=$result_parent['name']." | ".$result_parent['city']." | ".$result_parent['state']." | ".$result_parent['country']?></option>
                                	<?php
                                	}
                                	?>
                            	</select>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-6">&nbsp;</label><br/>
                        	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                            <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1"><br/>
                        	<?php /*?><a href="excelexport.php?rname=<?= base64_encode("batteryChargingstatusReport") ?>&rheader=<?= base64_encode("Battery Charging Status") ?>&vendor_name=<?= base64_encode($_REQUEST['vendor_name']) ?>&fromDate=<?= base64_encode($_REQUEST['fdate']) ?>&toDate=<?= base64_encode($_REQUEST['tdate']) ?>" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-2x" title="Export details in excel"></i></a><?php */?>
                        </div>
                      </div>
              		</form>
                    <br/>
<table width="100%" id="myTable" class="table-striped table-bordered table-hover">
  <thead>
  <tr class="<?=$tableheadcolor?>">
    <th width="5%">S.No.</th>
    <th width="20%">Vendor/Supplier</th>
    <th width="15%">Document No.</th>
    <th width="10%">Product Name</th>
    <th width="10%">Serial No.</th>
    <th width="10%">Status</th>
    <th width="20%">Charging Info</th>
    <th width="10%">Charging Status</th>
  </tr>
  </thead>
  <tbody>
  <?php
	$i = 1;
	$res_dv = mysqli_query($link1,"SELECT * FROM battery_charging_status WHERE import_date >= '".$_REQUEST["fdate"]."' AND import_date <= '".$_REQUEST["tdate"]."' ORDER BY id ASC")or die("ER1 ".mysqli_error($link1));
	if(mysqli_num_rows($res_dv)>0){
	while($row_dv = mysqli_fetch_array($res_dv)){
		if(substr($row_dv["doc_no"],0,3)=="GRN"){
			$from_party = mysqli_fetch_assoc(mysqli_query($link1, "SELECT from_location AS party_code FROM billing_master WHERE challan_no='".$row_dv['doc_no']."'"));
		}else{
			$from_party = mysqli_fetch_assoc(mysqli_query($link1, "SELECT po_to AS party_code FROM vendor_order_master WHERE po_no='".$row_dv['doc_no']."'"));
		}
	?>
  <tr>
    <td><?=$i?></td>
    <td><?php echo getVendorDetails($from_party["party_code"],"name,city,state",$link1)." (".$from_party["party_code"].")"; ?></td>
    <td><?=$row_dv["doc_no"]?></td>
    <td><?php echo getProductDetails($row_dv["prod_code"],"productname",$link1)." (".$row_dv["prod_code"].")";?></td>
    <td><?=$row_dv["serial_no"]?></td>
    <td><?=$row_dv["status"]?></td>
    <td><?php echo "<b>Input Voltage:</b> ".$row_dv["input_voltage"]."<br/><b>Output Voltage:</b> ".$row_dv["output_voltage"];?></td>
    <td align="left"><a href="#" class="btn <?=$btncolor?>" title="Check Charging Status" onClick="openActionModel('<?=$row_dv['id']?>','<?=$from_party['party_code']?>')"><i class="fa fa-bolt" title="Check Charging Status"></i></a>&nbsp;<a href="#" class="btn <?=$btncolor?>" title="Check Charging History" onClick="openHistoryModel('<?=$row_dv["serial_no"]?>','<?=$row_dv["prod_code"]?>','<?=$row_dv["import_date"]?>')"><i class="fa fa-history" title="Check Charging History"></i></a></td>
  </tr>
  <?php
		$i++;
    }
  ?>
  <?php 
  }else{
  ?>
  <tr>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">No</td>
    <td align="center">Data</td>
    <td align="left">Found</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <?php
  }
  ?>
  </tbody>
</table>
<!-- Start Model Mapped Modal -->
<div class="modal modalTH fade" id="actionModel" role="dialog">
<form class="form-horizontal" role="form" id="frm2" name="frm2" method="post">
<div class="modal-dialog modal-dialogTH">
  <!-- Modal content-->
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title" align="center"></h4>
    </div>
    <div class="modal-body modal-bodyTH">
     <!-- here dynamic task details will show -->
    </div>
    <div class="modal-footer">
      <?php /*?><input type="submit" class="btn<?=$btncolor?>" name="upddckt" id="upddckt" value="Update" title="" <?php if($_POST['upddckt']=='Update'){?>disabled<?php }?>>
      <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button><?php */?>
    </div>
    
  </div>
</div>
</form>
</div>
<!--close Model Mapped modal-->
       	  </div>
      		</div>
		</div>
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>