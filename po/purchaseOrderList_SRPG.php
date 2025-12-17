<?php
require_once("../config/config.php");
///// get access location ///
$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
///// get access state
$accessstate = getAccessState($_SESSION['userid'],$link1);
////// filters value/////
if($_SESSION["userid"]=="admin"){                              
	if($_REQUEST['from_state']){ 
		$pst_state = explode("~",$_REQUEST['from_state']); 
		$stat = " AND state='".$pst_state[0]."'";
	}else{ 
		$stat = "";
	}
}else{
	if($_REQUEST['from_state']){ 
		$pst_state = explode("~",$_REQUEST['from_state']); 
		$stat = " AND state='".$pst_state[0]."'";
		
	}else{ 
		$stat = " AND state IN (".$accessstate.")";
	}
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
<script src="../js/bootstrap-select.min.js"></script>
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function() {
	var dataTable = $('#purchase_grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"bStateSave": true,
		"order":  [[0,"desc"]],
		"ajax":{
			url :"../pagination/purchase_grid_data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>",  "fdate": "<?=$_REQUEST["fdate"]?>","tdate": "<?=$_REQUEST['tdate']?>","from_state": "<?=$_REQUEST['from_state']?>","from_location": "<?=$_REQUEST['from_location']?>","to_location": "<?=$_REQUEST['to_location']?>","status": "<?=$_REQUEST['status']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".purchase_grid_error").html("");
				$("#purchase_grid").append('<tbody class="purchase_grid_error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#purchase_grid_processing").css("display","none");
			}
		}		
	} );
} );

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
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-shopping-basket"></i> Purchase Order List</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
      <form class="form-horizontal" role="form" name="frm1" action="" method="POST">
              <div class="row">
                <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">From Date</label>
                    <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate" autocomplete="off" id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>" onChange="document.frm1.submit();"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                </div>
                <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
                    <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate" autocomplete="off" id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>"style="width:160px;" onChange="document.frm1.submit();"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                </div>
                <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">From Location State</label>
                    <select name="from_state" id="from_state" class="form-control" onChange="document.frm1.submit();" >
                        <option value="" selected="selected">All</option>
                        <?php
						if($_SESSION["userid"]=="admin"){                              
                        	$sql_state = "SELECT state,code FROM state_master WHERE 1 ORDER BY state";
						}else{
							$sql_state = "SELECT state,code FROM state_master WHERE 1 AND state IN (".$accessstate.") ORDER BY state";
						}
                        $res_state = mysqli_query($link1,$sql_state);
                        while($row_state = mysqli_fetch_array($res_state)){
                        ?>
                        <option value="<?=$row_state['state']."~".$row_state['code']?>" <?php if($row_state['state']."~".$row_state['code']==$_REQUEST['from_state'])echo "selected";?>><?=$row_state['state']?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">From Location</label>
                    <select name="from_location" id="from_location" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();" >
                        <option value="" selected="selected">Please Select </option>
                        <?php
						if($_SESSION["userid"]=="admin"){                              
                        	$sql_chl="SELECT asc_code, name, city, state, id_type from asc_master WHERE 1 ".$stat." ORDER BY name";
						}else{
							$sql_chl="SELECT asc_code, name, city, state, id_type from asc_master WHERE asc_code IN (".$accesslocation.") ".$stat." ORDER BY name";
						}
                        $res_chl=mysqli_query($link1,$sql_chl);
                        while($result_chl=mysqli_fetch_array($res_chl)){
                        ?>
                        <option value="<?=$result_chl['asc_code']?>" <?php if($result_chl['asc_code']==$_REQUEST['from_location'])echo "selected";?>><?=$result_chl['name']." | ".$result_chl['city']." | ".$result_chl['state']." | ".$result_chl['asc_code']?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">To Location</label>
                    <select name="to_location" id="to_location" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();">
                        <option value="" selected="selected">Please Select </option>
                        <?php                                 
                        $smfm_sql = "SELECT a.asc_code, a.name, a.city, a.state, a.id_type FROM asc_master a, purchase_order_master b WHERE a.asc_code=b.po_to AND b.po_from='".$_REQUEST['from_location']."' GROUP BY b.po_to";
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
                <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Status</label>
                    <select name="status" id="status" class="form-control"  onChange="document.frm1.submit();">
                        <option value=''>All</option>
                        <option value="PFA"<?php if($_REQUEST['status']=="PFA"){ echo "selected";}?>>Pending For Approval</option>
                        <option value="Approved"<?php if($_REQUEST['status']=="Approved"){ echo "selected";}?>>Approved</option>
                        <option value="Processed"<?php if($_REQUEST['status']=="Processed"){ echo "selected";}?>>Processed</option>
                        <option value="Rejected"<?php if($_REQUEST['status']=="Rejected"){ echo "selected";}?>>Rejected</option>
                        <option value="Cancelled"<?php if($_REQUEST['status']=="Cancelled"){ echo "selected";}?>>Cancelled</option>
                    </select>
                </div>
              </div>
              </form>
      <form class="form-horizontal" role="form">
        <button title="Add New PO" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='addNewPO.php?op=add<?=$pagenav?>'"><span>Add New PO</span></button>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="purchase_grid" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th>S.No</th>
              <th>PO To</th>
              <th>PO From</th>
              <th>PO No.</th>
              <th>PO Date</th>
              <th>Entry By</th>
              <th>Status</th>
              <th>Print</th>
              <th>View</th>
			  <th>Edit</th>
			  <th>Cancel</th>
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