<?php
require_once("../config/config.php");
@extract($_POST);
$date=date("Y-m-d");
///// if action taken
if($_POST['upddckt']=="Update"){
	$sql_doc = "UPDATE deviation_request set app_by = '".$_SESSION["userid"]."', app_date='".$datetime."', app_status='".$app_status."', app_remark='".$apprmk."', app_ip='".$_SERVER['REMOTE_ADDR']."' where id='".base64_decode($_POST['ref_no'])."' ";
	$res_doc = mysqli_query($link1,$sql_doc);
	//// check if query is not executed
	if (!$res_doc) {
		$flag = false;
		$error_msg = "Error details1: " . mysqli_error($link1) . ".";
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	}else{
		if($app_status=="Approved"){
			$row_v = mysqli_fetch_assoc(mysqli_query($link1,"SELECT pjp_id,change_visit FROM deviation_request WHERE id='".base64_decode($_POST['ref_no'])."'"));
			///// check if approved
			mysqli_query($link1,"UPDATE pjp_data SET visit_area='".$row_v["change_visit"]."' WHERE id = '".$row_v["pjp_id"]."'");
		}
		$cflag = "success";
		$cmsg = "Success";
		$msg = "You have taken action as ".$app_status;
	}
	//$sms_msg="Dear Partner. your consignment has been dispatched with courier ".$_POST['courier_name']." and docket no ".$_POST['docket_no']."";
	$sms_msg = "";
	header("location:deviation_request_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
}
////// filters value/////
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	
}else{
	$team = getTeamMembers($_SESSION['userid'],$link1);
	if($team){
		$team = $team.",'".$_SESSION['userid']."'"; 
	}else{
		$team = "'".$_SESSION['userid']."'"; 
	}
}
$filter_str = 1;
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " and DATE(entry_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " and DATE(entry_date) <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST["task_type"]){
	$filter_str	.= " and task_type = '".$_REQUEST['task_type']."'";
}
/*if($_REQUEST["assign_to"]){
	$filter_str	.= " and entry_by = '".$_REQUEST['assign_to']."'";
}*/
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	if($_REQUEST["assign_to"]){
		$team2 = getTeamMembers($_REQUEST["assign_to"],$link1);
		if($team2){
			$team2 = $team2.",'".$_REQUEST["assign_to"]."'"; 
		}else{
			$team2 = "'".$_REQUEST["assign_to"]."'"; 
		}
		$filter_str	.= " AND entry_by IN (".$team2.")";
	}else{
		$filter_str	.= " ";
	}
}else{
	if($_REQUEST["assign_to"]){
		$team3 = getTeamMembers($_REQUEST["assign_to"],$link1);
		if($team3){
			$team3 = $team2.",'".$_REQUEST["assign_to"]."'"; 
		}else{
			$team3 = "'".$_REQUEST["assign_to"]."'"; 
		}
		$filter_str	.= " AND entry_by IN (".$team3.")";
	}else{
		$filter_str	.= " AND entry_by IN (".$team.")";
	}
}
//////End filters value/////
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'/>
<title><?=siteTitle?></title>
<link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src='../js/jquery.min.js'></script>
<script src="../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../js/moment.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
});
////// function for open model to update courier details
function openActionModel(docid, appstatus){
//alert(docid+"    "+appstatus);
	$.get('action_on_deviation.php?doc_id=' + docid, function(html){
		 $('#actionModel .modal-body').html(html);
		 if(appstatus == "Pending For Approval"){
		 	var showbtn = '<input type="submit" class="btn btn-primary" name="upddckt" id="upddckt" value="Update" <?php if($_POST['upddckt']=='Update'){?>disabled<?php }?>><button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>';
		 }else{
		 	var showbtn = 'You have taken action against this request';
		 }
		 $('#actionModel .modal-footer').html(showbtn);
		 $('#actionModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
}
$(document).ready(function(){
   $("#frm2").validate();
});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      		<h2 align="center"><i class="fa fa-clock-o fa-lg"></i>&nbsp;Deviation Request</h2>
      		<?php if($_REQUEST['msg']){?>
            <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
            </div>
            <?php unset($_POST);
            
             }?>
            <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="post">
                <div class="form-group">
                  <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Request From</label>
                     <div class="col-md-5 input-append date">
                        <div style="display:inline-block;float:left;"><input type="date" class="form-control span2" name="fdate" id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>"></div>
                     </div>
                  </div> 
                  <div class="col-md-6"><label class="col-md-5 control-label">Request To</label>
                    <div class="col-md-5 input-append date">
                        <div style="display:inline-block;float:left;"><input type="date" class="form-control span2" name="tdate" id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>"style="width:160px;"></div>
                    </div>
                  </div>
                </div><!--close form group-->
                <div class="form-group">
                  <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Task Type</label>
                     <div class="col-md-5">
                        <select name='task_type' id='task_type' class="form-control">
						 <?php
                         $res_task = mysqli_query($link1,"SELECT task_name FROM pjptask_master WHERE status='A' and task_name='Dealer Visit' order by task_name");	
                         while($row_task = mysqli_fetch_assoc($res_task)){
                         ?>
                         <option value="<?=$row_task["task_name"]?>"<?php if($_REQUEST["task_type"]==$row_task["task_name"]){ echo "selected";}?>><?=$row_task["task_name"]?></option>
                         <?php 
                         }
                         ?>
                      </select>
                     </div>
                  </div> 
                  <div class="col-md-6"><label class="col-md-5 control-label">Assigned Users</label>
                    <div class="col-md-5">
                        <select name='assign_to' id='assign_to' class='form-control selectpicker' data-live-search="true">
                        	<option value="">All</option>
                             <?php
							if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
								$sql = mysqli_query($link1, "SELECT name,username,oth_empid FROM admin_users WHERE status='active' ORDER BY name");
							}else{
								$sql = mysqli_query($link1, "SELECT name,username,oth_empid FROM admin_users WHERE status='active' AND username IN (".$team.") ORDER BY name");
							}
							while ($row = mysqli_fetch_assoc($sql)) {
											?>
							<option value="<?= $row['username']; ?>" <?php if ($_REQUEST['assign_to'] == $row['username']) { echo "selected";}?>><?= $row['name']." | ".$row['username']." ".$row['oth_empid'];?>
							</option>
							<?php } ?>
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
              <div align="center"><a href="../reports/excelexport.php?rname=<?= base64_encode("deviationReqReport") ?>&rheader=<?= base64_encode("Deviation Request") ?>&taskType=<?= base64_encode($_REQUEST['task_type'])?>&assignTo=<?= base64_encode($_REQUEST['assign_to'])?>&fromDate=<?= base64_encode($_REQUEST['fdate']) ?>&toDate=<?= base64_encode($_REQUEST['tdate']) ?>" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-2x" title="Export details in excel"></i></a></div>
				<form class="form-horizontal" role="form">	
      			<!--<div class="form-group"  id="page-wrap" style="margin-left:10px;">-->
       				<table width="98%" id="myTable" class="table-bordered table-hover" align="center">
          				<thead>
							<tr class="<?=$tableheadcolor?>">
                                <th width="5%">#</th>
                                <th width="15%">User Name</th>
                                <th width="10%">Scheduled Date</th>
                                <th width="10%">Scheduled Visit</th>
                                <th width="10%">Change Visit</th>
                                <th width="15%">Request Raised On</th>
                                <th width="20%">Request Remark</th>
                                <th width="10%">Approval Status</th>
                                <th width="5%">Action</th>
                            </tr>
          				</thead>
          				<tbody>
             			<?php 
						$i=1;
						$sql1 = "SELECT * FROM deviation_request WHERE ".$filter_str." order by entry_date DESC";
       					$rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   					while($row1=mysqli_fetch_assoc($rs1)) { 
							$schdate = mysqli_fetch_assoc(mysqli_query($link1,"SELECT plan_date FROM pjp_data WHERE id='".$row1["pjp_id"]."'"));
						?>
	    					<tr>
								<td><?php echo $i ;?></td>
                                <td><?php echo getAdminDetails($row1['entry_by'],"name",$link1)." (".$row1['entry_by'].")";?></td>
                                <td><?php echo $schdate['plan_date']?></td>
                                <td><?php echo $row1['sch_visit']?></td>
                                <td><?php echo $row1['change_visit']?></td>
                                <td><?php echo $row1['entry_date']?></td>
                                <td><?php echo $row1['remark']?></td>
                                <td><?php echo $row1['app_status']?></td>
                                <td align="center"><?php if($row1['app_status']=="Pending For Approval"){ ?><a href='#' onClick="openActionModel('<?=$row1['id']?>','<?=$row1['app_status']?>')"><i class='fa fa-edit fa-lg' title='Take Action Against Request'></i></a><?php }else{}?></td>
                            </tr>
	   					<?php 
	  						$i++;
						}
	   					?>  
          				</tbody>
          			</table>
      			<!--</div>-->
      		</form>
                  <!-- Start Model Mapped Modal -->
          <div class="modal modalTH fade" id="actionModel" role="dialog">
          <form class="form-horizontal" role="form" id="frm2" name="frm2" method="post">
            <div class="modal-dialog modal-dialogTH">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center"><i class='fa fa-edit fa-lg faicon'></i> Deviation Approval</h4>
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
          </div><!--close Model Mapped modal-->
		</div>
	</div>
</div>
<?php
include("../includes/footer.php");
?>
</body>
</html>