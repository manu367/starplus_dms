<?php
require_once("../config/config.php");
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
		var dataTable = $('#openstock').DataTable( {
			"processing": true,
			"search": {
				"return": true
			},
			"serverSide": true,
			"bStateSave": true,
			"order": [[ 0, "desc" ]],
			"ajax":{
				url :"../pagination/saleupload_grid_data.php", // json datasource
				data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>","fdate": "<?=$_REQUEST['fdate']?>", "tdate": "<?=$_REQUEST['tdate']?>","location_code": "<?=$_REQUEST['location_code']?>","status": "<?=$_REQUEST['status']?>"},
				type: "post",  // method  , by default get
				error: function(){  // error handling
					$(".stock-grid-error").html("");
					$("#stock-grid").append('<tbody class="stock-grid-error"><tr><th colspan="10">No data found in the server</th></tr></tbody>');
					$("#stock-grid_processing").css("display","none");
				}
			}
		});
	});
	$(document).ready(function(){
		////// from date
		$('#fdate').datepicker({
			format: "yyyy-mm-dd",
			endDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true
		});
		/////// to date
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?=$today?>",
		todayHighlight: true,
		autoclose: true
	});
});
</script>
<style type="text/css">
    div.dropdown-menu.open
    {
        max-width:300px !important;
        overflow:hidden;
    }
    ul.dropdown-menu.inner
    {
        max-width:300px !important;
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
      		<h2 align="center"><i class="fa fa-book"></i> Sale Upload List</h2>
      		<?php if($_REQUEST['msg']){?><br>
      		<h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
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
                	<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">From Date</label>
                    	<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate" autocomplete="off" id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>" onChange="document.frm1.submit();"></div><div style="display:inline-block;float:left;">&nbsp;&nbsp;&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                	</div>
                	<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
                    	<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate" autocomplete="off" id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>"style="width:160px;" onChange="document.frm1.submit();"></div><div style="display:inline-block;float:left;">&nbsp;&nbsp;&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                	</div>
                	<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Location</label>
                    	<select name="location_code" id="location_code" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();" >
                            <option value="" selected="selected">Please Select </option>
                            <?php                                 
                            $sql_chl="select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y' AND id_type IN ('DS','DL')";
                            $res_chl=mysqli_query($link1,$sql_chl);
                            while($result_chl=mysqli_fetch_array($res_chl)){
                            $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='".$result_chl['location_id']."'"));
                            ?>
                            <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['location_code'])echo "selected";?>><?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?></option>
                            <?php
                            }
                            ?>
                    	</select>
                	</div>
                	<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Status</label>
                        <select name="status" id="status" class="form-control"  onChange="document.frm1.submit();">
                            <option value=''>All</option>                      
                            <option value="Dispatched"<?php if($_REQUEST['status']=="Dispatched"){ echo "selected";}?>>Dispatched</option>
                            <option value="Received"<?php if($_REQUEST['status']=="Received"){ echo "selected";}?>>Received</option>
                            <option value="Cancelled"<?php if($_REQUEST['status']=="Cancelled"){ echo "selected";}?>>Cancelled</option>
                        </select>
                    </div>
              	</div>         
      		</form>
			</br>
        	<button title="Upload Primary Sale" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='upload_primary_sale.php?<?=$pagenav?>'"><span>Upload Primary Sale</span></button>
      		<div class="form-group"  id="page-wrap" style="margin-left:10px;">
       			<table  width="100%" id="openstock" class="table-striped table-bordered table-hover" align="center">
          			<thead>
            			<tr class="<?=$tableheadcolor?>" >
                            <th>S.No</th>
                            <th>Sale Type</th>
                            <th>From Location</th>
                            <th>To Location</th>
                            <th>To Location SAPCode</th>
                            <th>Doc. No.</th>
                            <th>Doc. Date</th>
                            <th>Entry By</th>
                            <th>Status</th>
                            <th>Print</th>
                            <th>View</th>
                            <th>Cancel</th>
            			</tr>
          			</thead>
        		</table>
      		</div>
    	</div>  
  	</div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>