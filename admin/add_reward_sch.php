<?php
////// Function ID ///////
$fun_id = array("u"=>array(152)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
////// if we hit process button
if($_POST){
	if($_POST['Submit']=='Save'){
		@extract($_POST);
		///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
		$messageIdent = md5($_POST['Submit'].$scheme_name);
		//and check it against the stored value:
		$sessionMessageIdent = isset($_SESSION['msgscheme'])?$_SESSION['msgscheme']:'';
		if($messageIdent!=$sessionMessageIdent){//if its different:
			//save the session var:
			$_SESSION['msgscheme'] = $messageIdent;
			///check directory
			$folder = "../scheme/".date("Y-m");
			if (!is_dir($folder)) {
				mkdir($folder, 0777, 'R');
			}
			$allowedExts = array("gif","jpeg","jpg","png","PNG","GIF","JPEG","JPG","xlsx","xls","doc","docx","ppt","pptx","txt","pdf");
			////// check attach file
   			if($_FILES['attach']['name'] != ''){	
				$temp = explode(".", $_FILES["attach"]["name"]);
	 			$extension = end($temp);
	 			$f_size=$_FILES["attach"]["size"];
				///// check extension
				if(!in_array($extension, $allowedExts)){
					$msg = ".".$extension." ". "not allowed";
					$cflag = "danger";
					$cmsg = "Failed";
					header("location:add_reward_sch.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
					exit;
				}
				////// check file size upto 2 MB
				if ($_FILES["attach"]["size"]>2097152){
					$msg = "File size should be less than or equal to 2 mb";
					$cflag = "danger";
					$cmsg = "Failed";
					header("location:add_reward_sch.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
					exit;
				}
				else{ 
					$file_name = $_FILES['attach']['name'];
					$file_tmp = $_FILES['attach']['tmp_name'];
					$up = move_uploaded_file($file_tmp, $folder."/".time().$file_name);
					$path1 = $folder."/".time().$file_name;	
				}
			}
			///// make Scheme ref no
			$res_ref = mysqli_query($link1,"SELECT MAX(ref_id) AS mno FROM reward_scheme_master");
			$row_ref = mysqli_fetch_assoc($res_ref);
			$next_mno = $row_ref["mno"] + 1;
			$ref_no = "SCH/".date("Y")."/".$next_mno;
			/////// insert scheme 
			$res_inst = mysqli_query($link1,"INSERT INTO reward_scheme_master SET scheme_code = '".$ref_no."', ref_id = '".$next_mno."', scheme_name = '".$scheme_name."', scheme_description = '".$scheme_desc."', valid_from = '".$fdate."', valid_to = '".$tdate."', attachment = '".$path1."', status = 'Active', create_by = '".$_SESSION['userid']."', create_on = '".$datetime."'"); 
			//// check if query is not executed
			if (!$res_inst) {
				 $flag = false;
				 $err_msg = "Error Code0.11:".mysqli_error($link1);
			}
			////// insert in activity table////
			dailyActivity($_SESSION['userid'],$ref_no,"Scheme","ADD",$ip,$link1,"");
			//return message
			$msg = "You have successfully created a new scheme with ref. no. ".$ref_no."";
			$cflag = "success";
			$cmsg = "Success";
			///// move to parent page
			header("location:reward_sch_master.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
			exit;
		}else{
			//you've sent this already!
			$msg = "Re-submission was detected.";
			$cflag = "warning";
			$cmsg = "Warning";
			///// move to parent page
			header("location:reward_sch_master.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
			exit;
		}
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
<script type="text/javascript">
$(document).ready(function(){
	var spinner = $('#loader');
    $("#frm1").validate({
		submitHandler: function (form) {
			if (!this.wasSent) {
				this.wasSent = true;
				$(':submit', form).val('Please wait...')
						.attr('disabled', 'disabled')
						.addClass('disabled');
				spinner.show();
				form.submit();
			} else {
				return false;
			}
		}
	});
	////// from date
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
	/////// to date
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
});
</script>
<script src="../js/jquery.validate.js"></script>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<link href="../css/loader.css" rel="stylesheet"/>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9">
      		<h2 align="center"><i class="fa fa-gift"></i>&nbsp;&nbsp;Add New Scheme</h2><br/><br/>
            <?php if(isset($_REQUEST['msg'])){?>
            <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
            </div>
          <?php }?>
      		<div class="form-group"  id="page-wrap" style="margin-left:10px;">
            	<form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post" enctype="multipart/form-data">
          			<div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Scheme Name <span class="red_small">*</span></label>
              				<div class="col-md-6">
                 				<input name="scheme_name" id="scheme_name" type="text" class="form-control required mastername" value="<?=$_REQUEST['scheme_name']?>"/>
              				</div>
            			</div>
          			</div>
                    <div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Scheme Description</label>
              				<div class="col-md-6">
                 				<textarea name="scheme_desc" id="scheme_desc" style="resize:vertical" class="form-control addressfield"></textarea>
              				</div>
            			</div>
          			</div>
                    <div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Valid From <span class="red_small">*</span></label>
              				<div class="col-md-6">
                 				<div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="fdate" autocomplete="off" id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo "";}?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
              				</div>
            			</div>
          			</div>
                    <div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Valid To <span class="red_small">*</span></label>
              				<div class="col-md-6">
                 				<div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="tdate" autocomplete="off" id="tdate" style="width:160px;" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo "";}?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
              				</div>
            			</div>
          			</div>
                    <div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Attachment<span class="red_small">*</span><br/><span class="small">(Allowed upto 2 MB)</span></label>
              				<div class="col-md-6">
                 				<input type="file" name="attach" id="attach" class="form-control required" required accept=".xlsx,.xls,image/*,.doc,.docx,.ppt,.pptx,.txt,.pdf"/>
              				</div>
            			</div>
          			</div>
          			<div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Status<span class="red_small">*</span></label>
              				<div class="col-md-6">
                 				<select name='status' id='status' class="form-control required" required/>
                                    <option value="Active" selected>Active</option>
                                    <option value="Deactive">Deactive</option>
                 				</select>
              				</div>
            			</div>
          			</div>
                    <div class="form-group">
                        <div class="col-md-12" align="center">
                          <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="" value="Save" title="Save This Scheme" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
                          <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='reward_sch_master.php?<?=$pagenav?>'">
                        </div>
                      </div>
            	</form>
      		</div>
    	</div>
  	</div>
</div>
<div id="loader"></div> 
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>