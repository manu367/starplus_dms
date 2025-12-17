<?php
require_once("../config/config.php");
require_once("../includes/mail_function.php");
$id=$_REQUEST['id'];
$taskid = $_REQUEST['task_id'];
///// extract lat long
$latlong = explode(",",base64_decode($_REQUEST["latlong"]));
if($taskid){
	$row_task = mysqli_fetch_array(mysqli_query($link1,"select visit_area AS leadid from pjp_data where id='$taskid'"));
	$leadid = $row_task['leadid'];
	$sql=mysqli_query($link1,"select * from sf_lead_master where reference='$leadid'");
	$row=mysqli_fetch_array($sql);
}else{
	$sql=mysqli_query($link1,"select * from sf_lead_master where lid='$id'");
	$row=mysqli_fetch_array($sql);
}
////// start track view status history of user every time user view lead , we log it , written by shekhar on 8 feb 2024
if($_POST['internalnote']!='InternalNote' && $_POST['clientnote']!='ClientNote' && $_POST['leadsts']!='LeadStatus' && $_POST['updtrf']!="Update"){
	set_history($row['partyid'], $row['status'], $row['reference'], "Lead View",$_SESSION['userid'],$link1);
}
////// end track view status history of user every time user view lead , we log it , written by shekhar on 8 feb 2024
////// final submit form ////
@extract($_POST);
$msg='';
if($_POST['internalnote']=='InternalNote'){
	//$inote=preg_replace('/[^a-zA-Z]+/', '', $internal_note);
	if($_FILES['inote_attchment']['name']){
		///check directory
		$dirct = "../doc_attach/lead/".date("Y-m");
		if (!is_dir($dirct)) {
			mkdir($dirct, 0777, 'R');
		}
		///// attachment 
		if ($_FILES["inote_attchment"]["size"]>2097152){
			$msgg="File size should be less than or equal to 2 mb";
			header("Location:lead_view.php?msg=$msgg&sts=fail&page=lead".$pagenav);
		}
		else{ 
			$file_name = $_FILES['inote_attchment']['name'];
			$file_tmp =$_FILES['inote_attchment']['tmp_name'];
			$up=move_uploaded_file($file_tmp,$dirct."/".time().$file_name);
			$path1=$dirct."/".time().$file_name;
			$img_name1 = time().$file_name;
		}
	}	
	$inote=$internal_note;
	mysqli_query($link1,"insert into sf_ticket_master set lead_id='".$row['reference']."', subject='".$sub."',  internal_note='".$inote."', ticket_dt='".$today."', ticket_time='".$currtime."', ticket_ip='".$ip."', ticket_loggedby='".$_SESSION['userid']."',type='Internal Note', contact_person='".$contact_person."', schedule_date='".$sch_date."', schedule_time='".$sch_time."', comm_type='".$comm_type."',attachment='".$path1."'");
	if(mysqli_insert_id($link1)>0){
		dailyActivity($_SESSION['userid'],$row['reference'],"LEAD","I-NOTE ADD",$ip,$link1,"");
		///// written by shekhar on 23 aug 23
		if($sch_date){
			$docno = date("YmdHis");
			$sql = "INSERT INTO pjp_data SET document_no = '".$docno."', pjp_name='FOLLOW-UP', plan_date ='".$sch_date."',task ='Follow-up',assigned_user ='".$_SESSION['userid']."',visit_area ='".$row['reference']."',entry_date ='".$today."',entry_by='".$_SESSION['userid']."',file_name='".$row['reference']."',task_count=''";
          	mysqli_query($link1,$sql);
		}
		////
		if($taskid){
			mysqli_query($link1,"UPDATE pjp_data SET task_acheive=task_acheive+1 WHERE id='".base64_decode($taskid)."'");
			$resultut = mysqli_query($link1,"INSERT INTO user_track SET userid='".$_SESSION['userid']."', task_name='Follow-up', task_action='Add', ref_no='".$row['reference']."', latitude='".$latlong[0]."', longitude='".$latlong[1]."', address='',travel_km='', remote_address='".$_SERVER['REMOTE_ADDR']."',remote_agent='".$_SERVER['HTTP_USER_AGENT']."' , entry_date='".$today."'");
			//// check if query is not executed
			if (!$resultut) {
				$flag = false;
			 	$err_msg = "Error details4: " . mysqli_error($link1) . ".";
			}
		}
		if($row['party_email']){
			if($sch_date!="" && $sch_date > $today){ $followup = " and we have scheduled a communication on ".$sch_date." at ".$sch_time;}else{ $followup = "";}
			$sender_data = mysqli_fetch_array(mysqli_query($link1,"select emailid , name from admin_users where username = '".$_SESSION['userid']."'"));
			$subject = "Lead Updated";
			$content = "<html><head><title>Lead Note</title></head><body><p>Dear ".$row['partyid'].",</p><p>This is to inform  that your lead no. is  ".str_replace(",","  ,  ",$row['reference'])." updated with remark <b><i>".$inote."".$followup."</b></i> </p><p><br/><br/><br/>Regards, <br/>".$sender_data['name']."<br/></p></body></html>";
			$send_to = $row['party_email'];
			$send_cc = "shekhar@candoursoft.com";
			$send_bcc = "ravi@candoursoft.com";
			$send_fromname = "Lead Note";
			$resp = sendEmailNotification($subject, $content, $send_to, $send_cc, $send_bcc, $send_fromname);
		}
		$msgg="Internal Note Posted Successfully!";
		header("Location:lead_view.php?id=$id&msg=$msgg&sts=success".$pagenav);
	}
	else
	{
		$msgg="Request could not be processed!";
		header("Location:lead_view.php?id=$id&msg=$msgg&sts=fail".$pagenav);
	}
}
if($_POST['clientnote']=='ClientNote'){
	//$cnote=preg_replace('/[^a-zA-Z]+/', '', $client_note);
	$cnote=$client_note;
	mysqli_query($link1,"insert into sf_ticket_master set lead_id='".$row['reference']."', client_note='".$cnote."', ticket_dt='".$today."', ticket_ip='".$ip."', ticket_loggedby='".$_SESSION['userid']."', ticket_time='".$currtime."', type='Client Note'");
	if(mysqli_insert_id($link1)>0)
	{
		dailyActivity($_SESSION['userid'],$row['reference'],"LEAD","C-NOTE ADD",$ip,$link1,"");
		if($row['party_email']){			
			if($sch_date!="" && $sch_date > $today){ $followup = " and we have scheduled a communication on ".$sch_date." at ".$sch_time;}else{ $followup = "";}
			$sender_data = mysqli_fetch_array(mysqli_query($link1,"select emailid , name from admin_users where username = '".$_SESSION['userid']."'"));
			$subject = "Lead Updated";
			$content = "<html><head><title>Lead Note</title></head><body><p>Dear ".$row['partyid'].",</p><p>This is to inform  that your lead no. is  ".str_replace(",","  ,  ",$row['reference'])." updated with remark <b><i>".$cnote."".$followup."</b></i> </p><p><br/><br/><br/>Regards, <br/>".$sender_data['name']."<br/></p></body></html>";
			$send_to = $row['party_email'];
			$send_cc = "shekhar@candoursoft.com";
			$send_bcc = "ravi@candoursoft.com";
			$send_fromname = "Lead Note";
			$resp = sendEmailNotification($subject, $content, $send_to, $send_cc, $send_bcc, $send_fromname);
		}
		$msgg="Client Note Posted Successfully!";
		header("Location:lead_view.php?id=$id&msg=$msgg&sts=success".$pagenav);
	}
	else
	{
		$msgg="Error!";
		header("Location:lead_view.php?id=$id&msg=$msgg&sts=fail".$pagenav);
	}
}
////// update lead status
if($_POST['leadsts']=='LeadStatus'){
	mysqli_query($link1,"UPDATE sf_lead_master set status='".$status."', intial_remark='".$remark."', update_by='".$_SESSION['userid']."' where lid='".$row['lid']."'")or die(mysqli_error($link1));
	dailyActivity($_SESSION['userid'],$row['reference'],"LEAD","STATUS CHANGE",$ip,$link1,"");

	set_history($row['partyid'], $status, $row['reference'], "Lead Status Change",$_SESSION['userid'],$link1);
	$msgg="Lead"." ".$row['reference']." is updated successfully with status ".get_status($status,$link1);
	header("Location:lead_view.php?id=$id&msg=$msgg&sts=success&page=lead".$pagenav);
	exit;
}
if($_POST['updtrf']=="Update" && $_POST['ref_no']!=""){
	$refid = base64_decode($ref_no);
	$refleadid = base64_decode($leadref);
	$sql_doc = "UPDATE sf_lead_master SET dept_id='".$dept."', update_by='".$_SESSION["userid"]."', update_dt='".$datetime."' WHERE lid='".$refid."'";
	$res_doc = mysqli_query($link1,$sql_doc);
	//// check if query is not executed
	if (!$res_doc) {
		$flag = false;
		$error_msg = "Error details1: " . mysqli_error($link1) . ".";
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	}else{
		mysqli_query($link1,"INSERT INTO sf_ticket_master SET lead_id='".$refleadid."', subject='Lead Transfer', internal_note='".$dept."',client_note='".$transferremark."', ticket_dt='".$today."', ticket_ip='".$ip."', ticket_loggedby='".$_SESSION['userid']."', ticket_time='".$currtime."', type='Internal Note'");
		dailyActivity($_SESSION['userid'],$refleadid,"LEAD","TRANSFER",$ip,$link1,"");
		$cflag = "success";
		$cmsg = "Success";
		$msg = "Lead ".$refleadid." is transfer to ".$dept;
	}
	header("location:lead_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
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
<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script>
	$(document).ready(function(){
        $("#internalnote").validate();
		$("#clientnote").validate();
		$("#leadstatusupd").validate();
    });
	$(document).ready(function(){
    	$('#dt_basic5').dataTable();
		$('#dt_basic4').dataTable();
	});
	$(document).ready(function() {
		$('#sch_date').datepicker({
			format: "yyyy-mm-dd",
			startDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true
		});
	});
	function openInfoModel(docid){
		$.get('note_modelview.php?doc_id=' + docid, function(html){
			 $('#myModal .modal-body').html(html);
			 $('#myModal').modal({
				show: true,
				backdrop:"static"
			});
			$('#viewhead').html("<i class='fa fa-pencil-square-o fa-lg faicon'></i> Note Details");
		 });
	}
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
	$(function() {
		$('.pop').on('click', function() {
			$('.imagepreview').attr('src', $(this).find('img').attr('src'));
			$('.imagepreview').css("width","auto");
			$('.imagepreview').css("height","auto");
			$('#imagemodal').modal('show');   
		});		
	});
	$(document).ready(function () {
		$('#datetimepicker3').datetimepicker({
			format: 'LT'
		});
	});
	function openModel2(docid){
		$.get('lead_transfer.php?id=' + docid, function(html){
			 $('#courierModel2 .modal-body').html(html);
			 $('#courierModel2').modal({
				show: true,
				backdrop:"static"
			});
		 });
		 $("#courierModel2 #close_btn").html('<input type="submit" class="btn<?=$btncolor?>" name="updtrf" id="updtrf" title="Save this" value="Update" <?php if($_POST['updtrf']=='Update'){?>disabled<?php }?>/>&nbsp;<button type="button" id="btnCancel" class="btn btn-success" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>');
	}
</script>
<script type="text/javascript" src="../js/moment.js"></script>
<script src="../js/bootstrap-datetimepicker.js"></script>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<link rel="stylesheet" href="../css/rightsidemodal.css">
</head> 
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
    	<h2 align="center"><i class="fa fa-child"></i> Lead Detail</h2>
        <?php if($_REQUEST['msg']!=''){?>
      <h4 align="center">
        <span <?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span></h4><?php }?>
		<div class="row">
    		<div class="col-sm-6">
    			<div class="panel panel-success" style="font-size:11px">
					<div class="panel-heading"><i class="fa fa-address-card-o" aria-hidden="true"></i> Lead Info</div>
			 		<div class="panel-body">
                    	<table class="table table-bordered" width="100%">
                            <tbody>
                              <tr>
                                <td width="20%"><label class="control-label">Lead Id</label></td>
                                <td width="30%"><?php echo ucfirst ($row['reference']); ?></td>
                                <td width="20%"><label class="control-label">Create Date</label></td>
                                <td width="30%"><?php echo dt_format($row['tdate']); ?></td>
                              </tr>
                              <tr>
                                <td><label class="control-label">Lead Type</label></td>
                                <td><?php echo ucfirst($row['type_of_lead']); ?></td>
                                <td><label class="control-label">Lead Source</label></td>
                                <td><?php echo ucwords(get_leadsource($row['lead_source'],$link1));?></td>
                              </tr>
                              <tr>
                                <td><label class="control-label">Priority</label></td>
                                <td><?php echo ucfirst (getProcessStatus($row['priority'],$link1)); ?></td>
                                <td><label class="control-label">Status</label></td>
                                <td><?php echo ucwords(get_status($row['status'],$link1));?></td>
                              </tr>
                              <tr>
                                <td><label class="control-label">Product Name</label></td>
                                <td><?php echo  $row['productname'];?></td>
                                <td><label class="control-label">Product Code</label></td>
                                <td><?php echo  $row['productcode'];?></td>
                              </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel panel-info" style="font-size:11px">
					<div class="panel-heading"><i class="fa fa-address-card-o" aria-hidden="true"></i> Party Info</div>
			 		<div class="panel-body">   
                    	<table class="table table-bordered" width="100%">
                            <tbody>
                              <tr>
                                <td><label class="control-label">Party Name</label></td>
                                <td><?php echo ucwords(($row['partyid'])); ?> </td>
                                <td><label class="control-label">Address</label></td>
                                <td><?php echo $row['party_address'];?></td>
                              </tr>
                              <tr>
                                <td><label class="control-label">Contact No.</label></td>
                                <td><?php echo $row['party_contact']; ?></td>
                                <td><label class="control-label">Email</label></td>
                                <td><?php echo $row['party_email'];?></td>
                              </tr>
                              <tr>
                                <td><label class="control-label">Visiting Card</label></td>
                                <td> <?php if($row['vcard_url']!=''){?><a href="#" class="pop">
                                        <img src="<?php echo $row['vcard_url'];?>" style="width: auto; height: auto; display:none">
                                        <strong>Click To View Visiting Card</strong></a><?php } ?></td>
                                <td><label class="control-label">Sales Executive</label></td>
                                <td><?php echo  getAdminDetails($row['sales_executive'],"name",$link1);?></td>
                              </tr>
                            </tbody>
                          </table>
             		</div>
                 </div>
                 <div class="panel panel-danger" style="font-size:11px">
					<div class="panel-heading"><i class="fa fa-edit" aria-hidden="true"></i> Approval / Update Info</div>
			 		<div class="panel-body">   
                    	<table class="table table-bordered" width="100%">
                            <tbody>
                            	<tr>
                                    <td><label class="control-label">Create By</label></td>
                                    <td><?php echo  getAdminDetails($row['create_by'],"name",$link1);?></td>
                                    <td width="20%"><label class="control-label">Create Date</label></td>
                                	<td width="30%"><?php echo dt_format($row['tdate']); ?></td>
                                </tr>
								<tr>
                                    <td><label class="control-label">Update By</label></td>
                                    <td><?php echo ucwords(getAdminDetails($row['update_by'],"name",$link1));?></td>
                                    <td><label class="control-label">Update Date</label></td>
                                    <td><?php echo dt_format($row['update_dt_time']);?></td>
                                </tr>
                                <tr>
                                    <td><label class="control-label">Approve By</label></td>
                                    <td><?php echo ucwords(getAdminDetails($row['approve_by'],"name",$link1));?></td>
                                    <td><label class="control-label">Approve Date</label></td>
                                    <td><?php echo dt_format($row['approve_date']);?></td>
                            	</tr>
                                <tr>
                                    <td><label class="control-label">Approve Remark</label></td>
                                	<td colspan="3"><?php echo $row['approve_remark'];?></td>
                            	</tr>
                            </tbody>
                        </table>
                    </div>
                </div>
          	</div>
            <div class="col-sm-6">
    			<div class="panel panel-danger" style="font-size:11px">
					<div class="panel-heading"><i class="fa fa-comments" aria-hidden="true"></i> Lead Update</div>
			 		<div class="panel-body">
                    	<ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-sticky-note"></i> Internal Note</a></li>
                        	<li><a data-toggle="tab" href="#menu2"><i class="fa fa-edit"></i> Client Note</a></li>
                            <li><a data-toggle="tab" href="#menu3"><i class="fa fa-cog"></i> Update Status</a></li>
                            <li><a href="#menu4" onClick="openModel2('<?=base64_encode($row['lid']);?>');"><i class="fa fa-sign-out"></i> Transfer Lead</a></li>
                       	</ul>
                        <div class="tab-content">
                        	<div id="home" class="tab-pane fade in active" style="font-size:11px;"><br/>
                            	<form name="internalnote" id="internalnote" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                            		<div class="form-group">
                                		<div class="col-md-12"><label class="col-md-3 control-label">Subject <strong><span style="color:red">*</span></strong></label>
                                  			<div class="col-md-9">
                                    			<input type="text" name="sub" id="sub" class="form-control entername" required/> 
                                  			</div>
                                		</div>
                                		<div class="col-md-6"><label class="col-md-6 control-label"></label>
                                  			<div class="col-md-6">
                                  			</div>
                                		</div>
                              		</div>
                              		<div class="form-group">
                                  		<div class="col-md-12" >
                                        	<textarea name="internal_note" id="txtEditor1" class="form-control" placeholder="Internal Note" style="resize:vertical;height:250px;" required></textarea> 
                                    	</div>
                              		</div>
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-md-9">Schedule Date</label>
                                            <input type="text" class="form-control span2" name="sch_date"  id="sch_date" autocomplete="off"/>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-md-9">Schedule Time&nbsp;<span class="small">(In Days)</span></label>
                                            <div class='input-group date' id='datetimepicker3' style="display:inline-table">
                                                <input type='text' class="form-control" name="sch_time" id="sch_time"/>
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-time"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-md-9">Contact Person</label>
                                            <input type="text" name="contact_person" id="contact_person" class="form-control entername"/>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-md-9">Communication Type <span style="color:red">*</span></label>
                                            <select name="comm_type" id="comm_type" class="form-control" required>
                                                <option value="">Select Type</option>
                                                <?php $comm=mysqli_query($link1,"select * from sf_tbl_comm_type");
                                                while($crow=mysqli_fetch_assoc($comm)){
                                                ?>
                                                <option value="<?php echo $crow['id'];?>"><?php echo $crow['comm_type'];?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-md-9">Attachment</label>
                                            <input type="file" class="form-control" name="inote_attchment" id="inote_attchment" accept="image/*,.pdf"/>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-md-9">&nbsp;</label>
                                            &nbsp;
                                        </div>
                                    </div>
                                    <div class="row">
                                    	&nbsp;
                                    </div>
                              		<div class="form-group">
                                		<div class="col-md-12" align="center">
                                        	<input name="taskid" id="taskid" type="hidden" value="<?=base64_encode($taskid);?>"/>
                                  			<input name="id" id="id" type="hidden" value="<?=$row['lid'];?>"/>	
                                  			<button class="btn <?=$btncolor?>" type="submit" name="internalnote" value="InternalNote"><i class="fa fa-save"></i> Post Internal Note</button>
                                  			<button title="Back" type="button" class="btn <?=$btncolor?>" onClick="window.location.href='lead_list.php?tab=<?php echo $_REQUEST['tab'];?>&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'"><span>Back</span></button>
                                		</div>
                              		</div>
                            	</form>
                    		</div>
                            <div id="menu2" class="tab-pane fade"> <br/>
                              	<form name="clientnote" id="clientnote" class="form-horizontal" action="" method="post">
                                 	<div class="form-group">
                                  		<div class="col-md-12" >
                                        	<textarea name="client_note" id="txtEditor2" class="form-control" placeholder="Client Note" required style="resize:vertical;height:250px;"> </textarea> 
                                    	</div>
                              		</div>
                              		<div class="form-group">
                                		<div class="col-md-12" align="center">
                                  			<button class="btn<?=$btncolor?>" type="submit" name="clientnote" value="ClientNote"><i class="fa fa-save"></i> Post Client Note</button>
                                            <button title="Back" type="button" class="btn <?=$btncolor?>" onClick="window.location.href='lead_list.php?tab=<?php echo $_REQUEST['tab'];?>&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'"><span>Back</span></button>
                                		</div>
                              		</div>
                            	</form>
                            </div>
                            <div id="menu3" class="tab-pane fade"> <br/>
                              	<form name="leadstatusupd" id="leadstatusupd" class="form-horizontal" action="" method="post">
                                 	<div class="form-group">
                                		<div class="col-md-12"><label class="col-md-3 control-label">Status <strong><span style="color:red">*</span></strong></label>
                                  			<div class="col-md-9">
                                    			<select name="status" id="status" class="form-control required" required>
                                                  <option value="">Select Status</option>
                                                  <?php $st=mysqli_query($link1,"select * from sf_status_master where display_for='lead' order by status_name");
                                                        while($r=mysqli_fetch_assoc($st))
                                                        {
                                                  ?>
                                                  <option value="<?php echo $r['id'];?>"<?php if($r['id']==$row['status']){echo "selected='selected'";}?>><?php echo $r['status_name'];?></option>
                                                  <?php } ?>
                                              </select>
                                  			</div>
                                		</div>
                                		<div class="col-md-6"><label class="col-md-6 control-label">&nbsp;</label>
                                  			<div class="col-md-6">
												&nbsp;
                                  			</div>
                                		</div>
                              		</div>
                                    <div class="form-group">
                                		<div class="col-md-12"><label class="col-md-3 control-label">Remark <strong><span style="color:red">*</span></strong></label>
                                  			<div class="col-md-9">
                                    			<textarea name="remark" id="remark" class="form-control addressfield required" required style="resize:vertical"></textarea> 
                                  			</div>
                                		</div>
                                		<div class="col-md-6"><label class="col-md-6 control-label">&nbsp;</label>
                                  			<div class="col-md-6">
                                            	&nbsp;
                                  			</div>
                                		</div>
                              		</div>
                              		<div class="form-group">
                                		<div class="col-md-12" align="center">
                                  			<button class="btn<?=$btncolor?>" type="submit" name="leadsts" value="LeadStatus"><i class="fa fa-save"></i> Update Status</button>
                                            <button title="Back" type="button" class="btn <?=$btncolor?>" onClick="window.location.href='lead_list.php?tab=<?php echo $_REQUEST['tab'];?>&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'"><span>Back</span></button>
                                		</div>
                              		</div>                    
                            	</form>
                            </div>
                    	</div>
             		</div>
              	</div>
          	</div>
            
    	</div>
        <div class="row">
    		<div class="col-sm-6">
    			<div class="panel panel-success" style="font-size:11px">
					<div class="panel-heading"><i class="fa fa-history" aria-hidden="true"></i> Lead History</div>
			 		<div class="panel-body">
                    	<table id="dt_basic4" class="table table-bordered" width="100%"> 
                            <thead>
                              <tr class="<?=$tableheadcolor?>">
                                <th width="20%">Party Name</th>
                                <th width="20%">Status</th>
                                <th width="20%">Type</th>
                                <th width="20%">Update By</th>
                                <th width="20%">Update On</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                            $res_poapp=mysqli_query($link1,"SELECT * FROM sf_status_history where trans_no='".$row['reference']."' ORDER BY id ASC")or die("ERR1".mysqli_error($link1)); 
                            while($row_poapp=mysqli_fetch_assoc($res_poapp)){
                            ?>
                              <tr>
                                <td><?php echo $row_poapp['party_id'];?></td>
                                <td><?php echo get_status($row_poapp['status_id'],$link1);?></td>
                                <td><?php echo $row_poapp['trans_type'];?></td>
                                <td><?php echo $row_poapp['update_by'];?></td>
                                <td><?php echo dttime_format($row_poapp['updatedate']);?></td>
                              </tr>
                              <?php }?>
                            </tbody>
                        </table>
           		  </div>
                 </div>
          	</div>
            <div class="col-sm-6">
    			<div class="panel panel-warning" style="font-size:11px">
					<div class="panel-heading"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Key Note</div>
			 		<div class="panel-body">
                    	<table id="dt_basic5" class="table table-striped table-bordered table-hover" width="100%">
							<thead>
								<tr class="<?=$tableheadcolor?>">
									<th><i class="fa fa-calendar txt-color-blue hidden-md hidden-sm hidden-xs"></i>&nbsp;Date</th>
									<th><i class="fa fa-fw fa-map-marker txt-color-blue hidden-md hidden-sm hidden-xs"></i>&nbsp;Note Type</th>
                                    <th><i class="fa fa-envelope-o txt-color-blue hidden-md hidden-sm hidden-xs"></i>&nbsp;Notes</th>
                                    <th><i class="fa fa-phone txt-color-blue hidden-md hidden-sm hidden-xs"></i>&nbsp;Call By</th>
                                    <th><i class="fa fa-info txt-color-blue hidden-md hidden-sm hidden-xs"></i>&nbsp;Info</th>
								</tr>
							</thead>
                			<tbody>
                  			<?php
								$sno=0;
								$tsql=mysqli_query($link1,"select * from sf_ticket_master where ticket_loggedby='".$_SESSION['userid']."' and lead_id='".$row['reference']."' order by id desc");
								if($tsql!=FALSE)
								{
									while($trow=mysqli_fetch_assoc($tsql))
									{
										$sno=$sno+1;
									 ?>		
                 				<tr>
                                    <td><?php echo dt_format($trow['ticket_dt']);?></td>
                                    <td><?php echo $trow['type'];?></td>
                                    <td><?php if($trow['internal_note']!=''){echo ucwords(htmlspecialchars_decode($trow['internal_note']));} else {echo ucwords(htmlspecialchars_decode($trow['client_note']));}?></td>
                                    <td><?php echo get_communication($trow['comm_type'],$link1);?></td>
                                    <td><a href="#" class="btn <?=$btncolor?>" title="Check Info" onClick="openInfoModel('<?php echo base64_encode($trow["id"]);?>');"><i class="fa fa-info-circle" aria-hidden="true"></i></a></td>                                 
                              	</tr>
                 				<?php 
								}
							}
							?>
 							</tbody>
      					</table>
             		</div>
                 </div>
          	</div>
    	</div>
    </div><!--End col-sm-9-->
  	</div><!--End row content-->
</div><!--End container fluid-->

<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" align="center">              
      <div class="modal-body">
      	<button type="button" class="btn <?=$btncolor?>" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <img src="" class="imagepreview" style="width: 100%;" >
      </div>
    </div>
  </div>
</div>

<div class="modal fade  come-from-modal right" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header alert-info">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" align="center" id="viewhead"></h4>
            </div>
            <div class="modal-body modal-bodyTH">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" id="btnCancel" class="btn <?=$btncolor?>" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> 
<!-- Start Modal view -->
<div class="modal modalTH fade" id="courierModel2" role="dialog">
	<form class="form-horizontal" role="form" id="frm2" name="frm2" method="post">	
		<div class="modal-dialog modal-dialogTH modal-lg">
  			<!-- Modal content-->
  			<div class="modal-content">
    			<div class="modal-header">
                	<h2 class="modal-title" align="center"><i class='fa fa-sign-out faicon'></i>&nbsp; &nbsp;Lead Transfer</h2>
      				<button type="button" class="close" data-dismiss="modal">&times;</button>
      				
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