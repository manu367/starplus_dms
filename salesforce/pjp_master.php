<?php
require_once("../config/config.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=siteTitle?></title>
<script src="../js/jquery.js"></script>
<script src="../js/jquery.min.js"></script>
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
// $(document).ready(function(){
//     $('#myTable').dataTable();
// });
////// function for open model to see the purchase
function checkTaskHistory(refid){
	$.get('task_history.php?pk=' + refid, function(html){
		 $('#viewModal .modal-body').html(html);
		 $('#viewModal').modal({
			show: true,
			backdrop:"static"
		});
	 });
	 $("#viewModal #tile_name").html("<i class='fa fa-hourglass-half'></i> Task History");
}
$(document).ready(function() {
	var dataTable = $('#pjp').DataTable( {
		"processing": true,
		"serverSide": true,
		"bStateSave": true,
		"order":  [[0,"desc"]],
		"ajax":{
			url :"../pagination/pjp_grid_data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>",  "fdate": "<?=$_REQUEST["fdate"]?>","tdate": "<?=$_REQUEST['tdate']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".pjp_grid_error").html("");
				$("#pjp_grid").append('<tbody class="pjp_grid_error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
				$("#pjp_grid_processing").css("display","none");
			}
		}
	});
});
</script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      		<h2 align="center"><i class="fa fa-hourglass-half"></i>&nbsp;PJP Master</h2>
      		<?php if($_REQUEST['msg']){?><br>
      			<h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      		<?php }?>
            <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="post">
                <div class="form-group">
                  <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">PJP From</label>
                     <div class="col-md-5 input-append date">
                        <div style="display:inline-block;float:left;"><input type="date" class="form-control span2" name="fdate" id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>"></div>
                     </div>
                  </div> 
                  <div class="col-md-6"><label class="col-md-5 control-label">PJP To</label>
                    <div class="col-md-5 input-append date">
                        <div style="display:inline-block;float:left;"><input type="date" class="form-control span2" name="tdate" id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>"style="width:160px;"></div>
                    </div>
                     <div class="col-md-2">
                    	<input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
                       	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                       	<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                    </div>
                  </div>
                </div><!--close form group-->
              </form>
				<form class="form-horizontal" role="form">
	  			<button title="Add New Location" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='app_pjp.php?op=add<?=$pagenav?>'"><span>Add PJP</span></button>		
      			<div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       				<table width="100%" id="pjp" class="table-bordered table-hover" align="center">
          				<thead>
							<tr class="<?=$tableheadcolor?>">
                                <th>#</th>
                                <th>PJP Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Entry By</th>
                                <th>Entry Date</th>
                            </tr>
          				</thead>
          			</table>
      			</div>
      		</form>
		</div>
	</div>
</div>
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
<?php
include("../includes/footer.php");
?>
</body>
</html>