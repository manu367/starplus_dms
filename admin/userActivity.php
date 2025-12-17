<?php
require_once("../config/config.php");
///// if action taken
if($_POST['upd']=="Update"){
	##########  transcation parameter ########################33
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg = "";
	///// extract lat long
	$latlong = explode(",",base64_decode($_POST["latlong"]));
	$latitude = $latlong[0];
	$longitude = $latlong[1];
	///check directory
	$dirct = "../salesapi/activityimg/".date("Y-m");
	if (!is_dir($dirct)) {
		mkdir($dirct, 0777, 'R');
	}
	////////////////upload file
	$filename = "attachment";
	$file_name = $_FILES[$filename]["name"];
	if($file_name){
		//$file_basename = substr($file_name, 0, strripos($file_name, '.')); // get file extention
		$file_ext = substr($file_name, strripos($file_name, '.')); // get file name
		//////upload image
		if ($_FILES[$filename]["error"] > 0){
			$code=$_FILES[$filename]["error"];
		}
		else{
			move_uploaded_file($_FILES[$filename]["tmp_name"],$dirct."/".$file_name);
			$file = $file_name;
			//chmod ($file, 0755);
		}
	}	
	//////// get max lead id
	$res_sysref = mysqli_query($link1,"SELECT COUNT(id) AS cnt FROM activity_master WHERE user_id ='".$_SESSION['userid']."'");
	$row_sysref = mysqli_fetch_assoc($res_sysref);
	$next_no = $row_sysref['cnt']+1;
	$pad = str_pad($next_no,5,"0",STR_PAD_LEFT);  
	$reference = "ACT/".$_SESSION['userid']."/".$pad;
	///// insert in lead master
	$res2 =	mysqli_query($link1,"INSERT INTO activity_master SET ref_no ='".$reference."', activity_type='".$_POST['activitytype']."',activity_date ='".$today."', user_id='".$_POST['username']."', party_code='', party_name='".$_POST['partyname']."', party_address='', party_city='', party_state='',party_contact='".$_POST['contactno']."', party_email='', intial_remark='".$_POST['remark']."', initial_attach='".$file."', status ='Start', entry_by ='".$_SESSION['userid']."', entry_date='".$today." ".$currtime."',entry_ip='".$_SERVER['REMOTE_ADDR']."',activity_action='".$_POST['activityaction']."'");
	//// check if query is not executed
	if (!$res2) {
		 $flag = false;
		 $err_msg = "ER2: " . mysqli_error($link1) . ".";
	}
	///// insert in lead history
	$res1 =	mysqli_query($link1,"INSERT INTO activity_history SET ref_no ='".$reference."', remark ='".$_POST['remark']."', status='Start', attachment='".$file."', entry_by='".$_SESSION['userid']."', entry_date='".$today." ".$currtime."', entry_ip='".$_SERVER['REMOTE_ADDR']."', latitude='".$latitude."', longitude='".$longitude."', address='".$address."'");
	//// check if query is not executed
	if (!$res1) {
		 $flag = false;
		 $err_msg = "ER1: " . mysqli_error($link1) . ".";
	}
	$flag = dailyActivity($_SESSION['userid'],$reference,"ACTIVITY","ADD",$ip,$link1,$flag);
	//// check if query is not executed
	if ($flag) {
		mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
		$msg = "Activity ".$reference." is added successfully.";	
	}else{
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. ".$err_msg;		
	}
	mysqli_close($link1);
	header("location:userActivity.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
}
///// if action taken
if($_POST['upd2']=="Update"){
	##########  transcation parameter ########################33
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg = "";
	///// extract lat long
	$latlong = explode(",",base64_decode($_POST["latlong2"]));
	$latitude = $latlong[0];
	$longitude = $latlong[1];
	//// system ref no.
	$reference = base64_decode($_POST["ref_no"]);
	///check directory
	$dirct = "../salesapi/activityimg/".date("Y-m");
	if (!is_dir($dirct)) {
		mkdir($dirct, 0777, 'R');
	}
	////////////////upload file
	$filename = "attachment";
	$file_name = $_FILES[$filename]["name"];
	if($file_name){
		//$file_basename = substr($file_name, 0, strripos($file_name, '.')); // get file extention
		$file_ext = substr($file_name, strripos($file_name, '.')); // get file name
		//////upload image
		if ($_FILES[$filename]["error"] > 0){
			$code=$_FILES[$filename]["error"];
		}
		else{
			move_uploaded_file($_FILES[$filename]["tmp_name"],$dirct."/".$file_name);
			$file = $file_name;
			//chmod ($file, 0755);
		}
	}	
	///// insert in lead history
	$res1 =	mysqli_query($link1,"INSERT INTO activity_history SET ref_no ='".$reference."', remark ='".$_POST['remark']."', status='".$_POST['status']."', attachment='".$file."', entry_by='".$_SESSION['userid']."', entry_date='".$today." ".$currtime."', entry_ip='".$_SERVER['REMOTE_ADDR']."', latitude='".$latitude."', longitude='".$longitude."', address='".$address."'");
	//// check if query is not executed
	if (!$res1) {
		 $flag = false;
		 $err_msg = "ER1: " . mysqli_error($link1) . ".";
	}
	///// update activity master
	$res2 =	mysqli_query($link1,"UPDATE activity_master SET status='".$_POST['status']."', last_updateby ='".$_SESSION['userid']."', last_updatedate='".$today." ".$currtime."',last_updateip='".$_SERVER['REMOTE_ADDR']."' WHERE ref_no ='".$reference."'");
	//// check if query is not executed
	if (!$res2) {
		 $flag = false;
		 $err_msg = "ER2: " . mysqli_error($link1) . ".";
	}
	$flag = dailyActivity($_SESSION['userid'],$reference,"ACTIVITY","UPDATE",$ip,$link1,$flag);
	//// check if query is not executed
	if ($flag) {
		mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
		$msg = "Activity ".$reference." is updated successfully.";	
	}else{
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. ".$err_msg;		
	}
	mysqli_close($link1);
	header("location:userActivity.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
}
///////////////
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	
}else{
	$team = getTeamMembers($_SESSION['userid'],$link1);
	if($team){
		$team = $team.",'".$_SESSION['userid']."'"; 
	}else{
		$team = "'".$_SESSION['userid']."'"; 
	}
}
if($_REQUEST['fdate']=="" && $_REQUEST['tdate']==""){
	$f_date = $today;
	$t_date = $today;
}else{
	$f_date = $_REQUEST['fdate'];
	$t_date = $_REQUEST['tdate'];
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
/////// datatable
$(document).ready(function() {
	var dataTable = $('#myTable').DataTable( {
		"processing": true,
		"serverSide": true,
		"bStateSave": true,
		"order": [[ 0, "desc" ]],
		"ajax":{
			url :"../pagination/useractivity-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>", "fdate": "<?=$f_date?>", "tdate": "<?=$t_date?>", "user_id": "<?=$_REQUEST['username']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".user-grid-error").html("");
				$("#user-grid").append('<tbody class="user-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#user-grid_processing").css("display","none");
				
			}
		}
	});
}); 
$(document).ready(function(){
	$("#frm2").validate({
	  submitHandler: function (form) {
		if(!this.wasSent){
			this.wasSent = true;
			$(':submit', form).val('Please wait...')
							  .attr('disabled', 'disabled')
							  .addClass('disabled');
			//spinner.show();				  
			form.submit();
		} else {
			return false;
		}
	  }
	});
	$("#frm3").validate({
	  submitHandler: function (form) {
		if(!this.wasSent){
			this.wasSent = true;
			$(':submit', form).val('Please wait...')
							  .attr('disabled', 'disabled')
							  .addClass('disabled');
			//spinner.show();				  
			form.submit();
		} else {
			return false;
		}
	  }
	});
	////// from date
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?= $today ?>",
		todayHighlight: true,
		autoclose: true
	});
	/////// to date
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?= $today ?>",
		todayHighlight: true,
		autoclose: true
	});
});
function openModel(){
	$.get('add_activity.php', function(html){
		 $('#viewModel .modal-body').html(html);
		 $('#viewModel').modal({
			show: true,
			backdrop:"static"
		});
		getLocation();
		$('.selectpicker').selectpicker();
	 });
	 $("#viewModel #close_btn").html('<input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" title="Save this" value="Update" <?php if($_POST['upd']=='Update'){?>disabled<?php }?> onClick="return checkLocation2();"/>&nbsp;<button type="button" id="btnCancel" class="btn btn-success" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>');
}
function openModelUpd(docid){
	$.get('update_activity.php?id=' + docid, function(html){
		 $('#viewModel2 .modal-body').html(html);
		 $('#viewModel2').modal({
			show: true,
			backdrop:"static"
		});
		getLocation2();
	 });
	 $("#viewModel2 #close_btn").html('<input type="submit" class="btn<?=$btncolor?>" name="upd2" id="upd2" title="Save this" value="Update" <?php if($_POST['upd2']=='Update'){?>disabled<?php }?> onClick="return checkLocation3();"/>&nbsp;<button type="button" id="btnCancel" class="btn btn-success" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>');
}
</script>
<link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9 tab-pane fade in active" id="home">
      		<h2 align="center"><i class="fa fa-users"></i>&nbsp;User Activities</h2>
      		<?php if($_REQUEST['msg']){?><br>
      		<h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      		<?php }?>
            <?php if(isset($_REQUEST['msg'])){?>
                <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                  </button>
                    <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
                </div>
              <?php }?>
            <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="post">
            	<div class="row">
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">From Date</label>
                        <input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>" required>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
                        <input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>" required>
                    </div>
                    
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Employee Name</label>
                        <select name="username" id="username" class="form-control selectpicker" data-live-search="true">
                        <option value="">All</option>
                        <?php
						if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
							$sql = mysqli_query($link1, "SELECT name,username,oth_empid FROM admin_users WHERE status='active' AND oth_empid!='' ORDER BY name");
						}else{
							$sql = mysqli_query($link1, "SELECT name,username,oth_empid FROM admin_users WHERE status='active' AND username IN (".$team.") ORDER BY name");
						}
						while ($row = mysqli_fetch_assoc($sql)) {
                                        ?>
                        <option value="<?= $row['username']; ?>" <?php if ($_REQUEST['username'] == $row['username']) { echo "selected";}?>><?= $row['name']." | ".$row['username']." ".$row['oth_empid'];?>
                        </option>
                        <?php } ?>
                      </select>
                    </div>
                   
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">&nbsp;</label><br/>
                        <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                        <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                        <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                    </div>
                    
                  </div>
              </form>
            <button type="button" class="btn btn-success" style="float:right;" onClick="openModel();" title="Add New Activity"><i class='fa fa-street-view'></i> Add Activity</button>  
      		<div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       		<table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          		<thead>
            		<tr class="<?=$tableheadcolor?>">
              			<th>S.No.</th>
                        <th>Ref. No.</th>
                        <th>Activity Type</th>
                        <th>Activity Date</th>
                        <th>Party Name</th>
                        <th>Status</th>
                        <th>Employee Name</th>
						<th>Activity Action</th>
                        <th>View / Update</th>
                        <th>Initial Image</th>
            		</tr>
          		</thead>
          	</table>
   		  </div>
		</div>
	</div>
</div>
<!-- Start Modal view -->
<div class="modal modalTH fade" id="viewModel" role="dialog">
	<form class="form-horizontal" role="form" id="frm2" name="frm2" method="post" enctype="multipart/form-data">	
		<div class="modal-dialog modal-dialogTH modal-lg">
  			<!-- Modal content-->
  			<div class="modal-content">
    			<div class="modal-header">
                	<h2 class="modal-title" align="center"><i class='fa fa-plus faicon'></i>&nbsp; &nbsp;Add Activity</h2>
      				<button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div align="center" id="err_msg"></div>
      				
    			</div>
    			<div class="modal-body modal-bodyTH">
     				<!-- here dynamic task details will show -->
    			</div>
    			<div class="modal-footer" id="close_btn">
      
    			</div> 
  			</div>
		</div>
	</form>        
</div><!--close Modal view -->
<!-- Start Modal view -->
<div class="modal modalTH fade" id="viewModel2" role="dialog">
	<form class="form-horizontal" role="form" id="frm3" name="frm3" method="post" enctype="multipart/form-data">	
		<div class="modal-dialog modal-dialogTH modal-lg">
  			<!-- Modal content-->
  			<div class="modal-content">
    			<div class="modal-header">
                	<h2 class="modal-title" align="center"><i class='fa fa-pencil-square-o faicon'></i>&nbsp; &nbsp;Update Activity</h2>
      				<button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div align="center" id="err_msg"></div>
      				
    			</div>
    			<div class="modal-body modal-bodyTH">
     				<!-- here dynamic task details will show -->
    			</div>
    			<div class="modal-footer" id="close_btn">
      
    			</div> 
  			</div>
		</div>
	</form>        
</div><!--close Modal view -->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>