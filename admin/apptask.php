<?php
require_once("../config/config.php");
@extract($_POST);
////// filters value/////
$filter_str = 1;
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " and DATE(entry_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " and DATE(entry_date) <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['task_by'] !=''){
	$filter_str	.= " and entry_by = '".$_REQUEST['task_by']."'";
}
if($_REQUEST['task_status'] !=''){
	$filter_str	.= " and status = '".$_REQUEST['task_status']."'";
}
//////End filters value/////
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
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
});
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
</script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      		<h2 align="center"><i class="fa fa-hourglass-half"></i>&nbsp;App Task</h2>
      		<?php if($_REQUEST['msg']){?><br>
      			<h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      		<?php }?>
            <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="post">
                <div class="form-group">
                  <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Task From</label>
                     <div class="col-sm-5 col-md-5 col-lg-5 input-append date">
                        <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate" autocomplete="off" id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo "";}?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                     </div>
                  </div> 
                  <div class="col-md-6"><label class="col-md-5 control-label">Task To</label>
                    <div class="col-md-5 input-append date">
                        <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate" autocomplete="off" id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo "";}?>"style="width:160px;"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                    </div>
                  </div>
                </div><!--close form group-->
                <div class="form-group">
                  <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Task By</label>
                     <div class="col-sm-5 col-md-5 col-lg-5">
                        <select  name='task_by' id="task_by" class='form-control'>
                          <option value=''>All</option>
                          	<?php
                        	$sql = "SELECT username,name FROM admin_users where 1 order by name";
                        	$res = mysqli_query($link1,$sql);
                        	while($row = mysqli_fetch_array($res)){
                        	?>
                          <option value="<?=$row['username']?>"<?php if($_REQUEST['task_by']==$row['username']){echo 'selected';}?>><?=$row['name']?></option>
                        	<?php
                        	}
                        	?>
                       </select>
                     </div>
                  </div> 
                  <div class="col-md-6"><label class="col-md-5 control-label">Task Status</label>
                    <div class="col-md-3">
                       <select name="task_status" id="task_status" class="form-control">
                          <option value=''>All</option>
                          <option value='Pending'<?php if($_REQUEST['task_status']=="Pending"){echo "selected";}?>>Pending</option>
                          <option value='Open'<?php if($_REQUEST['task_status']=="Open"){echo "selected";}?>>Open</option>
                          <option value='In-Progress'<?php if($_REQUEST['task_status']=="In-Progress"){echo "selected";}?>>In-Progress</option>
                          <option value='Done'<?php if($_REQUEST['task_status']=="Done"){echo "selected";}?>>Done</option>
                        </select>
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
	  			<button title="Add New Location" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='appaddtask.php?op=add<?=$pagenav?>'"><span>Add Task</span></button>		
      			<div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       				<table width="100%" id="myTable" class="table-bordered table-hover" align="center">
          				<thead>
							<tr class="<?=$tableheadcolor?>">
                                <th width="4%"><a href="#" name="entity_id" title="asc" ></a>#</th>
                                <th width="14%" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Task Name</th>
                                <th width="16%"><a href="#" name="name" title="asc" ></a>Task Subject</th>
                                <th width="15%" data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Task Details</th>
                                <th width="13%" data-hide="phone,tablet">Assigned To</th>
                                <th width="9%" data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Status</th>
                                <th width="10%" data-hide="phone,tablet">Entry By</th>
                                <th width="13%" data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Entry Date</th>
                                <th width="6%" data-hide="phone,tablet">History</th>
            				</tr>
          				</thead>
          				<tbody>
             			<?php 
						$i=1;
						$sql1 = "SELECT * FROM task_master WHERE ".$filter_str;
       					$rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   					while($row1=mysqli_fetch_assoc($rs1)) { ?>
	    					<tr>
								<td><?php echo $i ;?></td>
                                <td><?php echo $row1['task_name']?></td>
                                <td><?php echo $row1['task_subject']?></td>
                                <td><?php echo $row1['task_details']?></td>
                                <td><?php echo getAdminDetails($row1['assign_to'],"name",$link1)?></td>
                                <td><?php echo $row1['status']?></td>
                                <td><?php echo getAdminDetails($row1['entry_by'],"name",$link1);?></td>
                                <td><?php echo $row1['entry_date']?></td>
                                <td align="center"><a href="#" onClick="checkTaskHistory('<?=$row1['id']?>');"><i class="fa fa-history fa-lg" title="view history"></i></a></td>
							</tr>
	   					<?php 
	  						$i++;
						}
	   					?>  
          				</tbody>
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