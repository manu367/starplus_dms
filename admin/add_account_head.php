<?php
////// Function ID ///////
$fun_id = array("a"=>array(86)); 
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
/////get status//
//$arrstatus = getFullStatus("master",$link1);
///// get operation rights
//$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);
////// case 1. if we want to update details
if ($_REQUEST['op']=='edit'){
	$sel_usr="select * from account_head_master where id='".$_REQUEST['id']."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
	@extract($_POST);
	if(isset($_POST['addButton']) || isset($_POST['updButton'])){
   	if ($_POST['addButton']=='ADD'){
		///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
		$messageIdent = md5($head_name . $status);
		//and check it against the stored value:
    	$sessionMessageIdent = isset($_SESSION['messageIdent'])?$_SESSION['messageIdent']:'';
		if($messageIdent!=$sessionMessageIdent){//if its different:
			//save the session var:
            $_SESSION['messageIdent'] = $messageIdent;
			$account_head=ucwords($head_name);
			$acgroup = explode("~",$_POST['ac_group']);
    		$usr_add="INSERT INTO account_head_master set head_name ='".$head_name."',nature_of_head = '".$nature_of_head."',group_id='".$acgroup[0]."',group_name='".$acgroup[1]."', status='Active',entry_date='".$datetime."',entry_by='".$_SESSION["userid"]."'";
    		$res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
			$insid = mysqli_insert_id($link1);
			dailyActivity($_SESSION['userid'],$insid,"Account Head","ADD",$ip,$link1,"");
			////// return message
			$msg="You have successfully created an account with name. ".$head_name;
			$cflag = "success";
			$cmsg = "Success";
		}else {
        	//you've sent this already!
			$msg="You have saved this already ";
			$cflag = "warning";
			$cmsg = "Warning";
    	}
	}
	else if ($_POST['updButton']=='Update'){
		
            $usr_upd="update account_head_master set status ='".$status."',update_date='".$datetime."',update_by='".$_SESSION["userid"]."'  where id = '".$_REQUEST['id']."'";			
            $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
            ////// insert in activity table////
            dailyActivity($_SESSION['userid'],$_REQUEST['id'],"Account Head","UPDATE",$ip,$link1,"");
            ////// return message
            $msg="You have successfully updated status ";
            $cflag = "success";
            $cmsg = "Success";
        /*}else{
            $msg="Email-id or Mobile No. is already exist.";
            $cflag = "danger";
            $cmsg = "Failed";
        }*/
	}
   	///// move to parent page
    header("location:account_head_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    exit;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script language="javascript" type="text/javascript">
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
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<link href="../css/loader.css" rel="stylesheet"/>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-bandcamp"></i> <?= ucwords($_REQUEST['op'])?> New Account Head</h2><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-5 control-label">Head Name <span class="red_small">*</span></label>
              <div class="col-md-4">
                 <input type="text" name="head_name" class="required entername form-control cp" id="head_name" value="<?php if(!empty($sel_result['head_name'])){ echo $sel_result['head_name'];}?>" <?php if($_REQUEST['op'] == 'add'){?> required <?php } else {?>  readonly<?php }?>/>
              </div>
            </div>       
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-5 control-label">Nature of Head <span class="red_small">*</span></label>
              <div class="col-md-4">
              <?php if($_REQUEST['op'] == 'add'){?>
                 <select name="nature_of_head" id="nature_of_head" class="form-control"required>
                     <option value="">Please Select</option>
                 	<option value="Debit" <?php if(isset($sel_result['nature_of_head'])){ if($sel_result['nature_of_head'] =='Debit') {echo 'selected'; }}?>>Debit</option>
            		<option value="Credit" <?php if(isset($sel_result['nature_of_head'])){ if($sel_result['nature_of_head'] =='Credit') {echo 'selected'; }}?>>Credit</option>
                 </select>
                 <?php } else {?>
                 <input type="text" name="nature_of_head" id="nature_of_head" value="<?=$sel_result['nature_of_head']?>"  class="form-control" readonly/>
                 <?php }?>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-5 control-label">Account Group <span class="red_small">*</span></label>
              <div class="col-md-4">
               <select name="ac_group" id="ac_group" class="form-control" <?php if($_REQUEST['op'] == 'add'){?> required <?php } else {?> disabled<?php }?>>
                  <option value="">Please Select</option>
                     <?php 
                     $res_acgroup=mysqli_query($link1,"select id, group_name from account_group_master where status='Active'");
                     while($row_acgroup=mysqli_fetch_array($res_acgroup)){
                      ?>
                      <option value="<?=$row_acgroup['id']."~".$row_acgroup['group_name']?>" <?php if(!empty($sel_result['group_name'])){if($sel_result['group_name'] == $row_acgroup['group_name']) {echo "selected";}} ?>><?=$row_acgroup['group_name']?></option>
                     <?php } ?>        
                </select>
              </div>
            </div>
          </div>
           <?php if($_REQUEST['op'] == 'edit'){?>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-5 control-label">Status</label>
              <div class="col-md-4">
                 <select name="status" id="status" class="form-control">
                 	<option value="Active" <?php if(isset($sel_result['status'])){ if($sel_result['status'] =='Active') {echo 'selected'; }}?>>Activate</option>
            		<option value="Deactive" <?php if(isset($sel_result['status'])){ if($sel_result['status'] =='Deactive') {echo 'selected'; }}?>>Deactivate</option>
                 </select>
              </div>
            </div>
          </div>
          <?php }?>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='add'){ ?>
              <button class='btn<?=$btncolor?>' id="add" type="submit" name="addButton" value="ADD"><i class="fa fa-save fa-lg"></i>&nbsp;&nbsp;Add</button>
              <?php }else{
                ////// check this user have right to edit the details
			    //if($get_opr_rgts['edit']=="Y"){
                ?>
              <button class='btn<?=$btncolor?>' id="upd" type="submit" name="updButton" value="Update"><i class="fa fa-retweet fa-lg"></i>&nbsp;&nbsp;Update</button>
            
              <?php
                //}
                }?>
              <input type="hidden" name="sno"  id="sno" value="<?=$sel_result['id']?>" />
              <button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='account_head_master.php?status=<?php if(isset($_REQUEST['status'])){echo $_REQUEST['status'];}?><?=$pagenav?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
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
