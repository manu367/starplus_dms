<?php
require_once("../config/config.php");
$id = base64_decode($_REQUEST['id']);

$info = mysqli_fetch_array(mysqli_query($link1, "SELECT * FROM hrms_request_master WHERE id = '".$id."' and type in ('IR','VR','LR') "));

@extract($_POST);
if($_POST['submit']=="Approve"){
	mysqli_autocommit($link1, false);
	$flag = true;
	
	$full_file_name = "";
	$file_path = "";
	if($_FILES['approve_attach_doc']["name"]!=''){	
	   //// upload doc into folder ////
		$file_name =$_FILES['approve_attach_doc']['name'];
		$file_tmp =$_FILES['approve_attach_doc']['tmp_name'];
		$file_path="../doc_attach/approve_application_doc/$today.$file_name";
		$full_file_name="$today.$file_name";
		move_uploaded_file($file_tmp,$file_path);	
	}
	
	////// update in application tbl ///////////
	if($application_type == "IR"){
		$sql1 = " UPDATE hrms_request_icard SET status = '".$action."' WHERE sno = '".$info['request_no']."' ";	
	}else if($application_type == "VR"){
		$sql1 = " UPDATE hrms_request_vcard SET status = '".$action."' WHERE sno = '".$info['request_no']."' ";	
	}else if($application_type == "LR"){
		$sql1 = " UPDATE hrms_request_loan SET status = '".$action."', approved_amt = '".$approv_amt."' WHERE sno = '".$info['request_no']."' ";	
	}else{}
	
	$res_qr1 = mysqli_query($link1, $sql1);
	/// check if query is execute or not//
	if(!$res_qr1){
		$flag = false;
		$err_msg = "Error 1". mysqli_error($link1) . ".";
	}
	
	/////// update in requester master table ///////
	$sql2 = " UPDATE hrms_request_master SET status = '".$action."', approve_date = '".$today."', approve_by = '".$_SESSION['userid']."', approve_remark = '".$approv_rmk."', approve_file_name = '".$full_file_name."', approve_file_path = '".$file_path."' WHERE request_no = '".$info['request_no']."' ";
	$res_qr2 = mysqli_query($link1, $sql2);
	/// check if query is execute or not//
	if(!$res_qr2){
		$flag = false;
		$err_msg = "Error 2". mysqli_error($link1) . ".";
	}
		
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$info['request_no'],"APPLICATION REQUEST",$action,$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Action performed successfully.";
		///// move to parent page
		header("location:application_approve_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:application_approve_list.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	} 
    mysqli_close($link1);
}

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
 <script>
 $(document).ready(function(){
	$("#frm1").validate();
 });
 function checkAmt(val){
	 var reqAmt = document.getElementById('req_amt').value;
	 if((parseFloat(reqAmt) >= parseFloat(val)) && (parseFloat(val) >= '0.00')){
		 
	 }else{
		 document.getElementById('approv_amt').value = "0.00";
		 alert(" Approved amount is less or eqal to request amount. ");
	 }
 }
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
      <h2 align="center"><i class="fa fa-thumbs-up"></i> Approve Application </h2>
      <?php /*?><h5 align="center"> ( Activity No. -  <?=$info['activity_no'];?> ) </h5><?php */?>
      <?php if($_REQUEST['msg']!=''){?>
      	<h4 align="center">
        	<span 
			<?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span>
        </h4>
	  <?php }?>
      <br>     
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data" >
                               
          <div class="panel-group">
            <div class="panel panel-info table-responsive">
                <div class="panel-heading heading1"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Application Details</div>
                 <div class="panel-body">
                 	<?php 
					if($info['type'] == "IR"){
						$main_info1 = mysqli_fetch_array(mysqli_query($link1, "SELECT * FROM hrms_request_icard WHERE sno = '".$info['request_no']."' "));
						if($main_info1 !=""){
					?>	
					<table class="table table-bordered" width="100%">
                        <tbody>
                          <tr>
                            <td width="20%"><label class="control-label">Application For</label></td>
                            <td width="80%"><?=$info['name'];?></td>
                          </tr>
                          <tr>
                            <td width="20%"><label class="control-label">Manager Name</label></td>
                            <td width="80%"><?=getAnyDetails($main_info1['mgr_id'],'empname','loginid','hrms_employe_master',$link1)." | ".$main_info1['mgr_id'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Emergency No.</label></td>
                            <td colspan="3"><?=$main_info1['emergency_no'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Entry Date</label></td>
                            <td colspan="3"><?=dt_format($main_info1['update_date']);?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Remark </label></td>
                            <td colspan="3"><?=$main_info1['remark'];?></td>
                          </tr>
                        </tbody>
                    </table>
                    <?php	
					}}else if($info['type'] == "VR"){
						$main_info2 = mysqli_fetch_array(mysqli_query($link1, "SELECT * FROM hrms_request_vcard WHERE sno = '".$info['request_no']."' "));
						if($main_info2 !=""){
					?>	
					<table class="table table-bordered" width="100%">
                        <tbody>
                          <tr>
                            <td width="20%"><label class="control-label">Application For</label></td>
                            <td width="80%"><?=$info['name'];?></td>
                          </tr>
                          <tr>
                            <td width="20%"><label class="control-label">Manager Name</label></td>
                            <td width="80%"><?=getAnyDetails($main_info2['mgr_id'],'empname','loginid','hrms_employe_master',$link1)." | ".$main_info2['mgr_id'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Email ID.</label></td>
                            <td colspan="3"><?=$main_info2['email'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Mobile No.</label></td>
                            <td colspan="3"><?=$main_info2['mobile_no'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Entry Date</label></td>
                            <td colspan="3"><?=dt_format($main_info2['update_date']);?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Remark </label></td>
                            <td colspan="3"><?=$main_info2['remark'];?></td>
                          </tr>
                        </tbody>
                    </table>	
                    <?php    
					}}else if($info['type'] == "LR"){
						$main_info3 = mysqli_fetch_array(mysqli_query($link1, "SELECT * FROM hrms_request_loan WHERE sno = '".$info['request_no']."' "));
						if($main_info3 !=""){
					?>	
					<table class="table table-bordered" width="100%">
                        <tbody>
                          <tr>
                            <td width="20%"><label class="control-label">Application For</label></td>
                            <td width="80%"><?=$info['name'];?></td>
                          </tr>
                          <tr>
                            <td width="20%"><label class="control-label">Manager Name</label></td>
                            <td width="80%"><?=getAnyDetails($main_info3['mgr_id'],'empname','loginid','hrms_employe_master',$link1)." | ".$main_info3['mgr_id'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Date of Joining</label></td>
                            <td colspan="3"><?=dt_format($main_info3['doj']);?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Email ID.</label></td>
                            <td colspan="3"><?=$main_info3['email'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Mobile No.</label></td>
                            <td colspan="3"><?=$main_info3['phone'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Requested Amount </label></td>
                            <td colspan="3"><?=$main_info3['requested_amt'];?><input type="hidden" name="req_amt" id="req_amt" value="<?=$main_info3['requested_amt'];?>" ></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Approved Amount </label></td>
                            <td colspan="3"><?=$main_info3['approved_amt'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Entry Date</label></td>
                            <td colspan="3"><?=dt_format($main_info3['update_date']);?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Remark </label></td>
                            <td colspan="3"><?=$main_info3['remark'];?></td>
                          </tr>
                        </tbody>
                    </table>
                    <?php	
					}}else {}
					?>
                                    
                </div><!--close panel body-->
            </div><!--close panel-->
            <br><br>
              <div class="panel panel-info table-responsive">
             <div class="panel-heading heading1"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Application Status</div>
                 <div class="panel-body">
               <table class="table table-bordered" width="100%">
                <tbody>
                      <tr>
                        <td width="20%"><label class="control-label">Status </label></td>
                        <td width="80%" colspan="3"><?=$info['status'];?></td>
                      </tr>
                      <tr>
                        <td width="20%"><label class="control-label">Approve By </label></td>
                        <td width="80%" colspan="3"><?=$info['approve_by'];?></td>
                      </tr>
                      <tr>
                        <td width="20%"><label class="control-label">Approve Date </label></td>
                        <td width="80%" colspan="3"><?=dt_format($info['approve_date']);?></td>
                      </tr>
					  <tr>
                        <td width="20%"><label class="control-label">Approve Remark</label></td>
                        <td width="80%" colspan="3">
                        	<textarea class="form-control addressfield" id="approv_rmk" name="approv_rmk"><?=$info['approve_remark']?></textarea>
                        </td>
                      </tr>
					  <tr>
                        <td width="20%"><label class="control-label">Attachment </label></td>
                        <td width="80%" colspan="3">
                        	<?php if($info['approve_file_name'] == ""){ ?>
							<input type="file" name="approve_attach_doc" id="approve_attach_doc" class="form-control" />
							<?php }else{ ?>
							<?php if($info['approve_file_name']) {?><a href='<?=$info['approve_file_path']?>' target='_blank' title='download'><i class='fa fa-download ' title='Download Document'></i></a><?php }else{ echo "No added attachment."; } ?>
							<?php } ?>
                        </td>
                      </tr>
					  
                      <?php 
					  if($info['approve_by']== ""){ 
					  if($info['type']== "LR"){
					  ?>
                      <tr>
                        <td width="20%"><label class="control-label">Approved Amount <span class="red_small">*</span></label></td>
                        <td width="80%" colspan="3">
                            <input type="text" class="form-control required" name="approv_amt" id="approv_amt" required onBlur="checkAmt(this.value)" >
                        </td>
                      </tr>
                      <?php }else{} ?>
                      <tr>
                        <td width="20%"><label class="control-label">Action <span class="red_small">*</span></label></td>
                        <td width="80%" colspan="3">
                        	<select class="form-control required" name="action" id="action" required >
                                <option value=""> -- Please Select -- </option>
                            	<option value="Approved">Approved</option>
                                <option value="Reject">Reject</option>
                            </select>
                            <input type="hidden" name="application_type" id="application_type" value="<?=$info['type'];?>" >
                        </td>
                      </tr>
                      <?php } ?>
                      
                    </tbody>
                  </table>
                </div><!--close panel body-->
            </div><!--close panel-->
          </div>
                    
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
              	<?php if($info['approve_by']== ""){ ?>
                  <button class="btn <?=$btncolor?>" type="submit" name="submit" value="Approve"> Apply </button>  
                <?php } ?>  
                  <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='application_approve_list.php?<?=$pagenav?>'">
              </div>  
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