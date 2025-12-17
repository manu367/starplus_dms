<?php
require_once("../config/config.php");
$id = base64_decode($_REQUEST['id']);

$info = mysqli_fetch_array(mysqli_query($link1, "SELECT * FROM hrms_activities_master WHERE pid = '".$id."' "));
////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Save"){
	mysqli_autocommit($link1, false);
	$flag = true;
	
	if($_FILES['activity_doc']["name"]!=''){	
	    //// upload doc into folder ////
		$file_name =$_FILES['activity_doc']['name'];
		$file_tmp =$_FILES['activity_doc']['tmp_name'];
		$file_path="../doc_attach/activity_feedback_doc/$today.$file_name";
		move_uploaded_file($file_tmp,$file_path);	
	}
	
	///// INSERT INTO hrms_activities_master  TABLE////
	$act_add="insert into hrms_activities_feedback set activity_no  ='".$actno."', feedback ='".$remark."', file_name='".$file_path."',  update_by ='".$_SESSION['userid']."', update_date ='".$today."' ";

	$res_add=mysqli_query($link1,$act_add)or die("error3".mysqli_error($link1));

	/// check if query is execute or not//
	if(!$res_add){
		$flag = false;
		$err_msg = "Error 1". mysqli_error($link1) . ".";
	}	
									
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$activate_no,"FEEDBACK","ADD",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Activity Feedback is successfully added.";
		///// move to parent page
		header("location:activities_emp_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:activities_emp_list.php?msg=".$msg."&sts=fail".$pagenav);
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
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script>
 	$(document).ready(function(){
		$("#frm1").validate();
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
      <h2 align="center"><i class="fa fa-cogs"></i> View Activity </h2>
      <h5 align="center"> ( Activity No. -  <?=$info['activity_no'];?> ) </h5>
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
                <div class="panel-heading heading1"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Activity Details</div>
                 <div class="panel-body">
                  <table class="table table-bordered" width="100%">
                    <tbody>
                      <tr>
                        <td width="20%"><label class="control-label">Status</label></td>
                        <td width="80%"><?=$info['status'];?></td>
                      </tr>
                      <tr>
                        <td width="20%"><label class="control-label">Activity</label></td>
                        <td width="80%"><?=$info['activity_desc'];?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Activity Details</label></td>
                        <td colspan="3"><?=$info['activity_detail'];?></td>
                      </tr>
                    </tbody>
                  </table>
				  <div style="float:left;">
                      <span style="font-weight: 800; font-size:13px; padding-right: 10px;" >Download Document : </span><?php if($info['file_path']) {?><a href='<?=$info['file_path']?>' target='_blank' title='download'><i class='fa fa-download fa-lg' title='Download Document'></i></a><?php }?>
                  </div> 
                  <div style="float:right;text-align:right;">
                      Posted By : <?=$info['create_by'];?><br>
                      On Date : <?=dt_format($info['create_date']);?>
                  </div> 
                </div><!--close panel body-->
            </div><!--close panel-->
              <div class="panel panel-info table-responsive">
             <div class="panel-heading heading1"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Feedback</div>
                 <div class="panel-body">
				<table class="table table-bordered" width="100%">
                <tbody>
                      <tr class="<?=$tableheadcolor?>">
                        <td align="center"><label class="control-label">Update By </label></td>
                        <td align="center"><label class="control-label">Update Date</label></td>
						<td align="center"><label class="control-label">Feedback</label></td>
						<td align="center"><label class="control-label">Attachment</label></td>
                      </tr>
					  <?php
					  	$qr =	mysqli_query($link1, "SELECT * FROM hrms_activities_feedback WHERE activity_no = '".$info['activity_no']."' and update_by = '".$_SESSION['userid']."' ");
					  	if(mysqli_num_rows($qr)>0){
						while($r = mysqli_fetch_assoc($qr)){
					  ?>
                      <tr>
                        <td align="center"><?=$r['update_by'];?></td>
                        <td align="center"><?=dt_format($r['update_date']);?></td>
						<td><?=$r['feedback'];?></td>
						<td align="center"><?php if($r['file_name']) {?><a href='<?=$r['file_name']?>' target='_blank' title='download'><i class='fa fa-download fa-lg' title='Download Document'></i></a><?php }?></td>
                      </tr>
					  <?php
					  	}
					  }
					  ?>
                    
                    </tbody>
                  </table>
	
               <table class="table table-bordered" width="100%">
                <tbody>
                      <tr>
                        <td width="20%" align="center"><label class="control-label">Remark </label></td>
                        <td width="80%" colspan="3"><textarea id="remark" name="remark" class="form-control addressfield" required></textarea></td>
                      </tr>
                      <tr>
                        <td width="20%" align="center"><label class="control-label">Attachments</label></td>
                        <td width="80%" colspan="3"><input type="file" name="activity_doc" id="activity_doc" class="form-control"  /></td>
                      </tr>
                    
                    </tbody>
                  </table>
                </div><!--close panel body-->
            </div><!--close panel-->
          </div>
                    
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <button class="btn <?=$btncolor?>" type="submit" name="submit" value="Save"> Save </button>  
                  <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='activities_emp_list.php?<?=$pagenav?>'">
                  <input type="hidden" name="actno" id="actno" value="<?=$info['activity_no'];?>" />
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