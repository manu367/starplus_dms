<?php
require_once("../config/config.php");
@extract($_POST);
////// final submit form ////

if($_POST[Submit]=='Save'){
	if(!empty($assign_to)){
		foreach($assign_to as $key => $value){
			///// make FB ref no
			$res_ref = mysqli_query($link1,"SELECT MAX(ref_id) as mno FROM task_master WHERE assign_to = '".$value."'");
			$row_ref = mysqli_fetch_assoc($res_ref);
			$next_mno = $row_ref["mno"] + 1;
			$ref_no = "TSK/".$value."/".$next_mno;
			$sql=mysqli_query($link1,"INSERT INTO task_master SET ref_no = '".$ref_no."', ref_id = '".$next_mno."', task_name ='".$_POST['task']."',task_subject='".$_POST['subject']."',task_details='".$_POST['detail']."',status='".$_POST['status']."', entry_date ='".$datetime."',entry_by='".$_SESSION['userid']."', assign_to='".$value."' ")or die("ER4".mysqli_error($link1)); 
		}
    	//return message
		$msg="You have successfully added Task ";
	}else{
		$msg="Assign to was not selected";
	}
	///// move to parent page
    header("Location:apptask.php?msg=".$msg."".$pagenav);
	exit;
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
	$(document).ready(function() {
		$('#assign_to').multiselect();
	});

 </script>
 <style>
.red_small{
	color:red;
}
</style>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css">
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9">
     		<h2 align="center"><i class="fa fa-hourglass-half"></i>&nbsp;&nbsp;Add Task</h2><br/><br/> 
      		<div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          	<form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post">   
          
         <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Task Name <span class="red_small">*</span></label>
              <div class="col-md-6">
			  <input name="task" type="text"   id="task" class="form-control required">              
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Task Subject<span class="red_small">*</span></label>
			      <div class="col-md-6">
                <input name="subject" type="text" class="form-control required"  id="subject" >
				 </div>
               </div>
          </div>
		   <div class="form-group">
		  <div class="col-md-6"><label class="col-md-6 control-label">Task Details<span class="red_small">*</span></label>
              <div class="col-md-6">
              <textarea name="detail" id="detail" class="form-control required"   style="resize:vertical"></textarea>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Status<span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name='status' id='status' class="form-control required"  required>
				 	<option value="">--Please Select--</option>
                    <option value="Pending">Pending</option>
                    <option value="In-Progress">In-Progress</option>
					<option value="Open">Open</option>
					<option value="Done">Done</option>
                 </select>
              </div>
            </div>		
			</div>
            <div class="form-group">
		  <div class="col-md-6"><label class="col-md-6 control-label">Assign User Type<span class="red_small">*</span></label>
              <div class="col-md-6">
              <select name='usertype' id='usertype' class="form-control required"  required>
				 <option value="">--Please Select--</option>
                 <?php
				 $res_utype = mysqli_query($link1,"SELECT typename,refid FROM usertype_master WHERE refid='6'");	
				 while($row_utype = mysqli_fetch_assoc($res_utype)){
				 ?>
                 <option value="<?=$row_utype["refid"]?>"><?=$row_utype["typename"]?></option>
                 <?php 
				 }
				 ?>
              </select>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Assign To<span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name='assign_to[]' id='assign_to' class="form-control required" required multiple="multiple">
                	 <?php
					 $res_user = mysqli_query($link1,"SELECT username,name FROM admin_users WHERE (utype='6' or utype='7') order by name");	
					 while($row_user = mysqli_fetch_assoc($res_user)){
					 ?>
					 <option value="<?=$row_user["username"]?>"><?=$row_user["name"]?></option>
					 <?php 
					 }
					 ?>
                </select>
              </div>
            </div>		
			</div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="" value="Save" title="submit">
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='apptask.php?<?=$pagenav?>'">
            </div>
          </div>
    </form>
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