<?php
////// Function ID ///////
$fun_id = array("a"=>array(104));
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
//////////////////////////
/////get status//
//$arrstatus = getFullStatus("master",$link1);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from claim_type_master where id='".$_REQUEST['id']."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
   if ($_POST['addButton']=='ADD'){
   	
	if(mysqli_num_rows(mysqli_query($link1,"select id from claim_type_master where claim_type='".$claim_type."'"))==0){   	
    $usr_add="INSERT INTO claim_type_master set claim_type ='".ucwords($claim_type)."',status='".$status."',update_date='".$datetime."',update_by='".$_SESSION['userid']."'";
    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
	$dptid = mysqli_insert_id($link1); 
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$dptid,"CLAIM TYPE","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
	////// return message
	$msg="You have successfully created a claim type like ".ucwords($claim_type);
	$cflag = "success";
	$cmsg = "Success";

	}else{
		$msg = ucwords($claim_type)." is already exists";
		$cflag = "warning";
		$cmsg = "Warning";
	}   
}
   
   else if ($_POST['updButton']=='Update'){
    $usr_upd="update claim_type_master set claim_type ='".ucwords($claim_type)."',status='".$status."',update_date='".$datetime."',update_by='".$_SESSION['userid']."' where id = '".$refid."'";
    $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$refid,"CLAIM TYPE","UPDATE",$ip,$link1,"");
	////// return message
	$msg="You have successfully updated claim type details for ".ucwords($claim_type);
	$cflag = "success";
	$cmsg = "Success";
   }
   ///// move to parent page
    header("location:claimtype_master.php?msg=".urlencode($msg)."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script>
$(document).ready(function(){
	 var spinner = $('#loader');
        $("#frm1").validate({
		  submitHandler: function (form) {
			if(!this.wasSent){
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
});
</script>
<script src="../js/jquery.validate.js"></script>
<link href="../css/loader.css" rel="stylesheet"/>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-edit"></i> <?=$_REQUEST['op']?> Claim Type</h2><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">          
		<div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Claim Type</label>
              <div class="col-md-6">
                 <input type="text" name="claim_type" class="required form-control mastername cp" id="claim_type" value="<?php if(!empty($sel_result['claim_type'])){ echo $sel_result['claim_type'];}?>" required/> 
              </div>
            </div>
          </div>   
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Status</label>
              <div class="col-md-6">
                 <select name="status" id="status" class="form-control custom-select">
                    <option value="1" <?php if(isset($sel_result['status'])){ if($sel_result['status'] =='1') {echo 'selected'; }}?>>Activate</option>
            		<option value="2" <?php if(isset($sel_result['status'])){ if($sel_result['status'] =='2') {echo 'selected'; }}?>>Deactivate</option>
                 </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
              <button class='btn<?=$btncolor?>' id="add" type="submit" name="addButton" value="ADD"><i class="fa fa-save fa-lg"></i>&nbsp;&nbsp;Add</button>
              <?php }else{
					//if($get_opr_rgts['edit']=="Y"){              	
              	?>
              <button class='btn<?=$btncolor?>' id="upd" type="submit" name="updButton" value="Update"><i class="fa fa-retweet fa-lg"></i>&nbsp;&nbsp;Update</button>
              <?php //}
			   }?>
              <input type="hidden" name="refid"  id="refid" value="<?=$sel_result['id']?>" />
              <button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='claimtype_master.php?status=<?php if(isset($_REQUEST['status'])){ echo $_REQUEST['status'];}?><?=$pagenav?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
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