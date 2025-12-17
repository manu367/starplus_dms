<?php
require_once("../config/config.php");
@extract($_POST);
////// case 1. if we want to update details
if(isset($_REQUEST['op'])){
	if ($_REQUEST['op']=='Edit'){
		$sel_usr="select * from charging_site_master where id='".$_REQUEST['id']."' ";
		$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
		$sel_result=mysqli_fetch_assoc($sel_res12);
	}
}
////// case 2. if we want to Add new product category
if(isset($_POST['addButton']) || isset($_POST['updButton'])){
   if ($_POST['addButton']=='ADD'){
   
	if(mysqli_num_rows(mysqli_query($link1,"select id from charging_site_master where site_code='".$site_code."'"))==0){
		$usr_add="INSERT INTO charging_site_master set site_name ='".ucwords($site_name)."',manufacturing_unit='".ucwords($manufacturing_unit)."', site_code = '".$_POST['site_code']."',status='".$status."',createdate='".$datetime."',createby='".$_SESSION['userid']."'";
		$res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
		$dptid = mysqli_insert_id($link1); 
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$dptid,"CHARGING SITE","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
		////// return message
		$msg="You have successfully created a charging site like ".ucwords($site_name);
		$cflag="success";
		$cmsg = "Success";
	}else{
		$msg= "Charging Site Name is already exist";
		$cflag="warning";
		$cmsg = "Warning";
	}
	
   }else if ($_POST['updButton']=='Update'){
   
   	if(mysqli_num_rows(mysqli_query($link1,"select id from charging_site_master where site_code='".$site_code."'"))<1){
		$usr_upd="update charging_site_master set site_name ='".ucwords($site_name)."',manufacturing_unit='".ucwords($manufacturing_unit)."', site_code = '".$_POST['site_code']."',status='".$status."',updatedate='".$datetime."',updateby='".$_SESSION['userid']."' where id = '".$refid."'";
		$res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$site_name,"CHARGING SITE","UPDATE",$ip,$link1,"");
		////// return message
		$msg="You have successfully updated charging site details for ".ucwords($site_name);
		$cflag="success";
		$cmsg = "Success";
	}else{
		$msg= "Charging Site Name is already exist";
		$cflag="warning";
		$cmsg = "Warning";
	}
	
   }else{
   
   }
   ///// move to parent page
    header("location:charging_site_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <script type="text/javascript" language="javascript" >
$(document).ready(function(){
	 var spinner = $('#loader');
        $("#frm1").validate({
		  rules: {
			short_code: {
			  required: true,
			  minlength: 2,
			  maxlength: 3
			}
		  },
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
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-bolt fa-lg"></i> <?=$_REQUEST['op']?> Charging Site </h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Charging Site Name <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="site_name" class="required mastername form-control cp" id="site_name" value="<?php if(!empty($sel_result['site_name'])){ echo $sel_result['site_name'];}?>" required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Charging Site Code <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="site_code" class="required character form-control uc" id="site_code" value="<?php if(!empty($sel_result['site_code'])){ echo $sel_result['site_code'];}?>" required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Manufacturing Unit <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="manufacturing_unit" class="required mastername form-control cp" id="manufacturing_unit" value="<?php if(!empty($sel_result['manufacturing_unit'])){ echo $sel_result['manufacturing_unit'];}?>" required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Status </label>
              <div class="col-md-6">
                 <select name="status" id="status" class="form-control custom-select">
                    <option value="A" <?php if(!empty($sel_result['status'])){ if($sel_result['status']=="A"){echo "selected";}}?>>Active</option>
					<option value="D"<?php if(!empty($sel_result['status'])){ if($sel_result['status']=="D"){echo "selected";}}?>>Deactive</option>
                 </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
               <button class="btn <?=$btncolor?>" id="add" type="submit" name="addButton" value="ADD"><i class="fa fa-save fa-lg"></i>&nbsp;&nbsp;Add</button>
              <?php }else{
              	?>
              <button class="btn<?=$btncolor?>" id="upd" type="submit" name="updButton" value="Update"><i class="fa fa-retweet fa-lg"></i>&nbsp;&nbsp;Update</button>
              <?php  }?>
              <input type="hidden" name="refid"  id="refid" value="<?=$sel_result['id']?>" />
              <button title="Back" type="button" class="btn <?=$btncolor?>" onClick="window.location.href='charging_site_master.php?status=<?php if(isset($_REQUEST['status'])){ echo $_REQUEST['status'];}?><?=$pagenav?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
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
