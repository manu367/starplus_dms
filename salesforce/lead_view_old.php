<?php
require_once("../config/config.php");
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
////// final submit form ////
@extract($_POST);
$msg='';
if($_POST['internalnote']=='InternalNote'){
	//$inote=preg_replace('/[^a-zA-Z]+/', '', $internal_note);
	$inote=$internal_note;
	mysqli_query($link1,"insert into sf_ticket_master set lead_id='".$row['reference']."', subject='".$sub."',  internal_note='".$inote."', ticket_dt='".$today."', ticket_time='".$logged_time."', ticket_ip='".$ip."', ticket_loggedby='".$_SESSION['userid']."',type='Internal Note', contact_person='".$contact_person."', schedule_date='".$sch_date."', schedule_time='".$sch_time."', comm_type='".$comm_type."'");
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
		include "lead_commonmail.php";
		}
		$msgg="Internal Note Posted Successfully!";
		header("Location:lead_view.php?id=$id&msg=$msgg&sts=success");
	}
	else
	{
		$msgg="Request could not be processed!";
		header("Location:lead_view.php?id=$id&msg=$msgg&sts=fail");
	}
}
if($_POST['clientnote']=='ClientNote'){
	//$cnote=preg_replace('/[^a-zA-Z]+/', '', $client_note);
	$cnote=$client_note;
	mysqli_query($link1,"insert into sf_ticket_master set lead_id='".$row['reference']."', client_note='".$cnote."', ticket_dt='".$today."', ticket_ip='".$ip."', ticket_loggedby='".$_SESSION['userid']."', ticket_time='".$logged_time."', type='Client Note'");
	if(mysqli_insert_id($link1)>0)
	{
		dailyActivity($_SESSION['userid'],$row['reference'],"LEAD","C-NOTE ADD",$ip,$link1,"");
		if($row['party_email']){
        include "lead_commonmail.php";
		}
		$msgg="Client Note Posted Successfully!";
		header("Location:lead_view.php?id=$id&msg=$msgg&sts=success");
	}
	else
	{
		$msgg="Error!";
		header("Location:lead_view.php?id=$id&msg=$msgg&sts=fail");
	}
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
    });
	$(document).ready(function(){
    	$('#dt_basic5').dataTable();
	});
	$(document).ready(function() {
		$("#txtEditor1").Editor();
		$("#txtEditor2").Editor();
	});
	$(document).ready(function() {
		$('#sch_date').datepicker({
			format: "yyyy-mm-dd",
			startDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true
		});
	});
	function setHtmlAreaValue1(){
	 document.internalnote.internal_note.value =  $("#txtEditor1").Editor("getText"); 
	}
	function setHtmlAreaValue2(){
	 document.clientnote.client_note.value =  $("#txtEditor2").Editor("getText");
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
 </script>
 <script src="../js/editor.js"></script>
 <link href="../css/editor.css" type="text/css" rel="stylesheet"/>
 <script src="../js/bootstrap-datepicker.js"></script>
 
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-child"></i> Lead Detail</h2><br/>
      <?php if($_REQUEST['msg']!=''){?>
      <h4 align="center">
        <span <?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span></h4><?php }?>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      <div style="margin-bottom:20px;">
      <button class="btn<?=$btncolor?>" onClick="window.open('../print/print_lead.php?id=<?php echo base64_encode($_REQUEST['id']);?>&page=lead');" style="cursor: pointer;" type="button" >Print Lead</button>&nbsp &nbsp
  	  <button title="Back" type="button" class="btn<?=$btncolor?>" style="float:right" onClick="window.location.href='lead_list.php?tab=<?php echo $_REQUEST['tab'];?>&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'"><span>Back</span></button>
      </div>
      	<table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Lead Id</label></td>
                <td width="30%"><?php echo ucfirst ($row['reference']); ?></td>
                <td width="20%"><label class="control-label">Remark</label></td>
                <td width="30%"><?php echo ucfirst($row['intial_remark']); ?></td>
              </tr>
              <tr class="alert-success">
                <td><label class="control-label">Party Name</label></td>
                <td><?php echo ucwords(($row['partyid'])); ?> </td>
                <td><label class="control-label">Priority</label></td>
                <td><?php echo ucfirst (getProcessStatus($row['priority'],$link1)); ?></td>
              </tr>
              <tr class="alert-success">
                <td><label class="control-label">Contact No.</label></td>
                <td><?php echo $row['party_contact']; ?></td>
                <td><label class="control-label">Email</label></td>
                <td><?php echo  $row['party_email'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Type</label></td>
                <td><?php echo ucfirst($row['type']); ?></td>
                <td><label class="control-label">Party Address</label></td>
                <td><?php echo  $row['party_address'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Create Date</label></td>
                <td><?php echo dt_format($row['tdate']); ?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo  ucwords(get_status($row['status'],$link1));?></td>
              </tr>
              <tr>
                <td><label class="control-label">Visiting Card</label></td>
                <td> <?php if($row['vcard_url']!=''){?><a href="#" class="pop">
    					<img src="<?php echo $row['vcard_url'];?>" style="width: auto; height: auto; display:none">
						<strong>Click To View Visiting Card</strong></a><?php } ?></td>
                <td><label class="control-label">Sales Executive</label></td>
                <td><?php echo  getAdminDetails($row['sales_executive'],"name",$link1);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Create Date</label></td>
                <td><?php echo dt_format($row['tdate']); ?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo  ucwords(get_status($row['status'],$link1));?></td>
              </tr>
              <tr>
                <td><label class="control-label">Lead Source</label></td>
                <td><?php echo  ucwords(get_leadsource($row['lead_source'],$link1));?></td>
                <td><label class="control-label">Approve By</label></td>
                <td><?php echo  ucwords(getAdminDetails($row['approve_by'],"name",$link1));?></td>
              </tr>
              <tr>
                <td><label class="control-label">Approve Remark</label></td>
                <td><?php echo  $row['approve_remark'];?></td>
                <td><label class="control-label">Approve Date</label></td>
                <td><?php echo  dt_format($row['approve_date']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Create Location</label></td>
                <td><?php echo  $row['create_location'];?></td>
                <td><label class="control-label">Create By</label></td>
                <td><?php echo  getAdminDetails($row['create_by'],"name",$link1);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Update By</label></td>
                <td><?php echo ucwords(getAdminDetails($row['update_by'],"name",$link1));?></td>
                <td><label class="control-label">Update Date & Time</label></td>
                <td><?php echo  $row['update_dt_time'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Product Name</label></td>
                <td><?php echo  $row['productname'];?></td>
                <td><label class="control-label">Product Code</label></td>
                <td><?php echo  $row['productcode'];?></td>
              </tr>
            </tbody>
          </table>
	<!-- row -->
	<div class="row">
					<h4 align="center">Ticket Thread</h4>
					<!-- widget content -->
					<div class="widget-body no-padding">

						<table id="dt_basic5" class="table table-striped table-bordered table-hover" width="100%">
							<thead>
								<tr class="<?=$tableheadcolor?>" >
								
			<th data-class="expand"><a href="#" name="entity_id" title="asc" ></a><i class="fa fa-calendar txt-color-blue hidden-md hidden-sm hidden-xs"></i> <strong>Date</strong> </th>
            <th data-hide="phone"><a href="#" name="email" title="asc" class="not-sort"></a><i class="fa fa-fw fa-user txt-color-blue hidden-md hidden-sm hidden-xs" ></i><strong>Subject</strong></th>
            <th data-hide="phone"><a href="#" name="email" title="asc" class="not-sort"></a><i class="fa fa-fw fa-user txt-color-blue hidden-md hidden-sm hidden-xs" ></i><strong>Contact Person</strong></th>
            <th data-hide="phone"><a href="#" name="email" title="asc" class="not-sort"></a><i class="fa fa-envelope-o txt-color-blue hidden-md hidden-sm hidden-xs"></i><strong>Notes</strong></th>
              <th data-hide="phone"><a href="#" name="email" title="asc" class="not-sort"></a><i class="fa fa-fw fa-user text-muted hidden-md hidden-sm hidden-xs"></i><strong>Post By</strong></th>
              <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a><i class="fa fa-fw fa-map-marker txt-color-blue hidden-md hidden-sm hidden-xs"></i><strong>Type</strong></th>
              <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a><i class="fa fa-phone txt-color-blue hidden-md hidden-sm hidden-xs"></i><strong>Comm. Type</strong></th>
              <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a><i class="fa fa-calendar txt-color-blue hidden-md hidden-sm hidden-x"></i> <strong>Sched. Date</strong></th>
               <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a><i class="fa fa-clock-o  txt-color-blue hidden-md hidden-sm hidden-x"></i> <strong>Sched. Time<br/>(In days)</strong></th>
			
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
								
                 	<tr title="" class="even pointer">
                    <td><?php echo $trow['ticket_dt'];?></td>
                    <td><?php echo $trow['subject'];?></td>
                    <td><?php echo $trow['contact_person'];?></td>
                    <td><?php if($trow['internal_note']!=''){echo ucwords(htmlspecialchars_decode($trow['internal_note']));} else {echo ucwords(htmlspecialchars_decode($trow['client_note']));}?></td>
                     <td><?php echo getAdminDetails($trow['ticket_loggedby'],"name",$link1);?></td>
                     <td><?php echo $trow['type'];?></td>
                     <td><?php echo get_communication($trow['comm_type'],$link1);?></td>
                  	 <td><?php echo ($trow['schedule_date']);?></td>
                      <td><?php echo $trow['schedule_time'];?></td>

                  </tr>
                 <?php }}?>
               
				
 </tbody>

      </table>
    </div>
									<!-- end widget content -->
				
							</div>
							<!-- end widget -->
                           
      	<ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-sticky-note"></i> Internal Note</a></li>
            <li><a data-toggle="tab" href="#menu2"><i class="fa fa-edit"></i> Client Note</a></li>
          </ul>
           <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br/>
          	<form name="internalnote" id="internalnote" class="form-horizontal" action="" method="post">
            <div class="form-group">
                <div class="col-md-6"><label class="col-md-6 control-label">Subject <strong><span style="color:red">*</span></strong></label>
                  <div class="col-md-6">
                    <input type="text" name="sub" id="sub" class="form-control entername" required /> 
                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6 control-label"></label>
                  <div class="col-md-6" >
                  </div>
                </div>
              </div>
              <div class="form-group">
                  <div class="col-md-12" >
                        <textarea name="internal_note" id="txtEditor1" class="form-control" placeholder="Note Detail" required> </textarea> 
                	</div>
              </div>
              <div class="form-group">
                <div class="col-md-6"><label class="col-md-6 control-label">Schedule Date<strong><span style="color:red"></span></strong></label>
                  <div class="col-md-6 input-append date">
                        <div style="display:inline-block;float:left;">
                            <input type="text" class="form-control span2" name="sch_date"  id="sch_date" style="width:180px;">
                        </div>
                        <div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6 control-label">Schedule Time(In Days) <strong><span style="color:red"></span></strong></label>
                  <div class="col-md-6" >
                  	<input type="text" name="sch_time" id="sch_time" class="form-control digits" />
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-6"><label class="col-md-6 control-label">Contact Person<strong><span style="color:red"></span></strong></label>
                  <div class="col-md-6">
                    <input type="text" name="contact_person" id="contact_person" class="form-control entername"  />
                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6 control-label">Communication Type<strong><span style="color:red"></span></strong></label>
                  <div class="col-md-6" >
                  	<select name="comm_type" id="comm_type" class="form-control" requireds >
                           <option value="">Select Communication Type</option>
                           <?php $comm=mysqli_query($link1,"select * from sf_tbl_comm_type");
                           while($crow=mysqli_fetch_assoc($comm)){
                           ?>
                           <option value="<?php echo $crow['id'];?>"><?php echo $crow['comm_type'];?></option>
                           <?php } ?>
                     </select>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-12" align="center"><input name="taskid" id="taskid" type="hidden" value="<?=base64_encode($taskid);?>"/>
				  <input name="id" id="id" type="hidden" value="<?=$row['lid'];?>"/>	
                  <button class="btn <?=$btncolor?>" type="submit" name="internalnote" value="InternalNote" onClick="setHtmlAreaValue1();"><i class="fa fa-save"></i> Post Internal Note</button>
                  <button class="btn <?=$btncolor?>"  name="cancel" onClick="reset();">Cancel</button>
                </div>
              </div>
            </form>
    </div>
            <div id="menu2" class="tab-pane fade"> <br/>
              <form name="clientnote" id="clientnote" class="form-horizontal" action="" method="post">
                 <div class="form-group">
                  <div class="col-md-12" >
                        <textarea name="client_note" id="txtEditor2" class="form-control" placeholder="" required> </textarea> 
                	</div>
              </div>
              <div class="form-group">
                <div class="col-md-12" align="center">
                  <button class="btn<?=$btncolor?>" type="submit" name="clientnote" value="ClientNote" onClick="setHtmlAreaValue2();"><i class="fa fa-save"></i> Post Client Note</button>
                  <button class="btn <?=$btncolor?>"  name="cancel" onClick="reset();">Cancel</button>
                </div>
              </div>                    
       		</form>
            </div>
          </div>
      </div><!--End form group-->
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
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>