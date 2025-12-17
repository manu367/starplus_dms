<?php
////// Function ID ///////
$fun_id = array("u"=>array(2)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

$_SESSION['retailBill']="";
$_SESSION['msgdirbil']="";
$_SESSION['msgdirbilc']="";
$_SESSION['msgRetailSRN']="";
////// filters value/////
$filter_str = "";
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND entry_date >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND entry_date <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['from_location'] !=''){
	$filter_str	.= " AND from_location = '".$_REQUEST['from_location']."'";
}
if($_REQUEST['to_location'] !=''){
	$filter_str	.= " AND to_location = '".$_REQUEST['to_location']."'";
}
if($_REQUEST['docType'] !=''){
	$filter_str	.= " AND document_type = '".$_REQUEST['docType']."'";
}
if($_REQUEST['status'] !=''){
	$filter_str	.= " AND status = '".$_REQUEST['status']."'";
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
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
 <script>
$(document).ready(function() {
	var dataTable = $('#myTable').DataTable( {
		"processing": true,
		"serverSide": true,
		"bStateSave": true,
		"order":  [[5,"desc"]],
		"ajax":{
			url :"../pagination/retailInvoice_grid_data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>",  "fdate": "<?=$_REQUEST["fdate"]?>","tdate": "<?=$_REQUEST['tdate']?>","from_location": "<?=$_REQUEST['from_location']?>","to_location": "<?=$_REQUEST['to_location']?>","docType": "<?=$_REQUEST['docType']?>","status": "<?=$_REQUEST['status']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".myTable_error").html("");
				$("#myTable").append('<tbody class="myTable_error"><tr><th colspan="12">No data found in the server</th></tr></tbody>');
				$("#myTable_processing").css("display","none");
			}
		}
	});
});
$(document).ready(function(){
	////// from date
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
	/////// to date
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
});
</script>
	<style type="text/css">
    div.dropdown-menu.open
    {
        max-width:200px !important;
        overflow:hidden;
    }
    ul.dropdown-menu.inner
    {
        max-width:200px !important;
        overflow-y:auto;
    }
</style>
<link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-user"></i> Sale Invoice List</h2>
      <?php if(isset($_REQUEST['msg'])){?>
        <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
      <?php
		if(isset($_SESSION["logres"]) && $_SESSION["logres"]){
		echo '<div class="py-2 overflow-hidden" style="background:#f1f1f1;padding:15px;line-height:20px;color:#e51111;margin:15px;font-size:12px;">';
		echo '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.$_SESSION["logres"]["msg"];
		echo '<br/><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.implode(" , ",$_SESSION["logres"]["invalid"]);
		echo '</div>';
		}
		unset($_SESSION["logres"]);
		?>
      <form class="form-horizontal" role="form" name="frm1" action="" method="POST">
      <div class="row">
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">From Date</label>
            <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate" autocomplete="off" id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo "";}?>" onChange="document.frm1.submit();"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
            <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate" autocomplete="off" id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo "";}?>"style="width:160px;" onChange="document.frm1.submit();"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">From Location</label>
            <select name="from_location" id="from_location" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();" >
                <option value="" selected="selected">Please Select </option>
                <?php                                 
                $sql_chl="select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y' AND id_type IN ('HO','BR','DS')";
                $res_chl=mysqli_query($link1,$sql_chl);
                while($result_chl=mysqli_fetch_array($res_chl)){
                $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='".$result_chl['location_id']."'"));
                ?>
                <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['from_location'])echo "selected";?>><?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?></option>
                <?php
                }
                ?>
            </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">To Location</label>
            <select name="to_location" id="to_location" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();">
                <option value="" selected="selected">Please Select </option>
                <?php                                 
                $smfm_sql = "SELECT a.asc_code, a.name, a.city, a.state, a.id_type FROM asc_master a, billing_master b WHERE a.asc_code=b.to_location AND b.from_location='".$_REQUEST['from_location']."' GROUP BY b.to_location";
                $smfm_res = mysqli_query($link1,$smfm_sql);
                while($smfm_row = mysqli_fetch_array($smfm_res)){
                ?>
                <option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['to_location'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
                <?php
                }
                ?>
            </select>
            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
        </div>
      </div>
 
      <div class="row">
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Type</label>
            <select name="docType" id="docType" class="form-control"  onChange="document.frm1.submit();">
                <option value=''>All</option>
                <option value="INVOICE"<?php if($_REQUEST['docType']=="INVOICE"){ echo "selected";}?>>INVOICE</option>
                <option value="Delivery Challan"<?php if($_REQUEST['docType']=="Delivery Challan"){ echo "selected";}?>>Delivery Challan</option>
            </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Status</label>
            <select name="status" id="status" class="form-control"  onChange="document.frm1.submit();">
                <option value=''>All</option>
                <option value="Pending"<?php if($_REQUEST['status']=="Pending"){ echo "selected";}?>>Pending</option>
                <option value="Pending For Serial"<?php if($_REQUEST['status']=="Pending For Serial"){ echo "selected";}?>>Pending For Serial</option>
                <?php /*?><option value="PFA"<?php if($_REQUEST['status']=="PFA"){ echo "selected";}?>>Pending For Approval</option><?php */?>
                <option value="Dispatched"<?php if($_REQUEST['status']=="Dispatched"){ echo "selected";}?>>Dispatched</option>
                <option value="Received"<?php if($_REQUEST['status']=="Received"){ echo "selected";}?>>Received</option>
                <option value="Cancelled"<?php if($_REQUEST['status']=="Cancelled"){ echo "selected";}?>>Cancelled</option>
            </select>
        </div>
      </div>
      </form> 
      <br/> 
      <form class="form-horizontal" role="form">
        <div style="display:inline-block;float:right" class="btn-toolbar">
        <button title="Direct Billing" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='comboBillingN.php?<?=$pagenav?>'"><i class="fa fa-object-group"></i> <span>Add Combo Billing To Location</span></button>&nbsp;&nbsp;&nbsp;&nbsp;
        <button title="Direct Billing" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='directBillingN.php?<?=$pagenav?>'"><i class="fa fa-cubes"></i> <span>Add Retail Billing To Location</span></button>&nbsp;&nbsp;&nbsp;&nbsp;
        <button title="Add New Opening" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='retail_billing.php?rb=add<?=$pagenav?>'"><i class="fa fa-cube"></i> <span>Add Retail Billing To Customer</span></button></div>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th width="5%">S.No</th>
              <th width="5%">Tally Sync</th>
              <th width="17%">Billing From</th>
              <th width="17%">Billing To</th>
              <th width="15%">Invoice No.</th>
              <th width="10%">Invoice Date</th>
              <th width="8%">Pending Aging</th>
              <th width="8%">Status</th>
              <th width="8%">Print</th>
              <th width="6%">Serial Attached</th>
              <th width="6%">View</th>
              <th width="6%">Cancel</th>
            </tr>
          </thead>
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