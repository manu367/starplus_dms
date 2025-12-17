<?php
require_once("../config/config.php");
$getid=base64_decode($_REQUEST['id']);
////// get details of selected product////
$res_locdet=mysqli_query($link1,"SELECT * FROM reward_points_master where id='".$getid."'")or die(mysqli_error($link1));
$row=mysqli_fetch_array($res_locdet);
////// final submit form ////
if($_POST['Submit']=="Update"){
	if(mysqli_query($link1,"UPDATE reward_points_master SET reward_point='".$_POST['reward_point']."',parent_party_reward='".$_POST['parent_reward_point']."',status='".$_POST['status']."', update_on='".$datetime."', update_by='".$_SESSION['userid']."' WHERE id='".$getid."' ")or die("ER4".mysqli_error($link1)))
	{	   
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$row['partcode'],"REWARD POINT","UPDATE",$ip,$link1,"");
		//return message
		$msg="You have successfully updated reward point";
		$cflag="success";
		$cmsg = "Success";
   	}else{
		////// return message
		$msg="Something went wrong. Please try again.";
		$cflag="danger";
		$cmsg = "Failed";
   	}
	///// move to parent page
    header("Location:reward_points_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <script type="text/javascript" src="../js/ajax.js"></script>
 
 <script>
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
});
 </script>
 
 
<style>
.red_small{
	color:red;
}
</style>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<link href="../css/loader.css" rel="stylesheet"/>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-money"></i>&nbsp;&nbsp;Edit Reward Point</h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post" >
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">State<span class="red_small">*</span></label>
              <div class="col-md-5">
              <input type="text" name="state" id="state" class="form-control"  disabled value="<?php echo $row['state']; ?>"  required />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Location Type<span class="red_small">*</span></label>
              <div class="col-md-5">
                <input type="text" name="loc" id="loc" class="form-control" disabled value="<?php echo getLocationType($row['id_type'],$link1);?>"  required />
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Reward Point<span class="red_small">*</span></label>
              <div class="col-md-5">
               
	          <input type="text" name="reward_point" id="reward_point" class="form-control digits required" placeholder="0" value="<?php echo $row['reward_point'];?>"  required />
            
                 
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Parent Reward</label>
              <div class="col-md-5">
                   <input type="text" name="parent_reward_point" id="parent_reward_point" class="form-control digits" value="<?php echo $row['parent_party_reward'];?>" placeholder="0"/>
              </div>
            </div>
            
          </div>
          
          

          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Product <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <textarea name="productname" id="productname" class="form-control" disabled style="resize:vertical"><?php echo getProduct($row['partcode'],$link1);?></textarea>              
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Status<span class="red_small">*</span></label>
              <div class="col-md-5">
                   <select name="status" class="form-control required" required>
				 <option value="">--Plaese Select--</option>
	           <option value="A"<?php if($row['status']=="A"){ echo "selected";}?>>Active</option>
	           <option value="D"<?php if($row['status']=="D"){ echo "selected";}?>>Deactive</option>
	           
            </select>
              </div>
            </div>
            
          </div>
		 
		 
		 
		   
          <div class="form-group">
            <div class="col-md-12" align="center">
              
            <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="" value="Update" >
        <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='reward_points_master.php?<?=$pagenav?>'">
      
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
