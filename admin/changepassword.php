<?php
require_once("../config/config.php");
if(!empty($_POST['newpwd'])){
	/////// check old password is correct or not
	$chkquery="Select uid from admin_users where username='".$_SESSION['userid']."' and password='".$_POST['oldpwd']."'";
	$check=mysqli_query($link1,$chkquery);
	//// old password should be matched
	if(mysqli_num_rows($check)==1){
	  ///// check password should not be equal to userid
	  if($_POST['newpwd']!=$_SESSION['userid']){
		///// if everything is OK then proceed to change password
		$query="UPDATE admin_users set password='".$_POST['newpwd']."' where username='".$_SESSION['userid']."' and password='".$_POST['oldpwd']."'";
		$result=mysqli_query($link1,$query);
		$msg="Your password has sucessfully changed.";
	  }
	  else{
	    $msg="New password can not be as user id. Please try something else.";
	  }
	}
	else{
		$msg="Invalid current password.";
	}
	///// move to parent page
    header("location:changepassword.php?msg=".$msg."".$pagenav);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
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
    $('#myTable').dataTable();
});
$(document).ready(function(){
    $("#form1").validate();
});
$(function(){
  $('#oldpwd').bind('input', function(){
    $(this).val(function(_, v){
     return v.replace(/\s+/g, '');
    });
  });
});
$(function(){
  $('#newpwd').bind('input', function(){
    $(this).val(function(_, v){
     return v.replace(/\s+/g, '');
    });
  });
});
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
    <br/><br/><br/>
    <div class="container bootstrap snippet" style="width:1150px;">
    <form class="form-group" role="form" name="form1" action="" method="post">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-2">
            <div class="panel panel-info">
                <div class="panel-heading heading1">
                    <h3 class="panel-title">
                        <i class="fa fa-th fa-lg" aria-hidden="true"></i>
                        Change password   
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6 separator social-login-box"> <br>
						   <?php
						   		$profile_img = mysqli_fetch_assoc(mysqli_query($link1,"SELECT profile_img_name, profile_img_path FROM admin_users  WHERE username = '".$_SESSION['userid']."' "));
								if($profile_img['profile_img_path']==""){
									$img = "../img/usrpwd.png";
								}else{
									$img = $profile_img['profile_img_path'];
								}
						   ?>
                           <img style="height: 220px;width: 220px;" alt="Profile Pic." class="img-thumbnail" src="<?=$img;?>">                        
                        </div>
                        <div style="margin-top:80px;" class="col-xs-6 col-sm-6 col-md-6 login-box">
                         <div class="form-group">
                            <div class="input-group">
                              <div class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></div>
                              <input class="form-control required" required type="password" placeholder="Current Password" id="oldpwd" name="oldpwd">
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="input-group">
                              <div class="input-group-addon"><i class="fa fa-sign-in fa-lg" aria-hidden="true"></i></div>
                              <input class="form-control required" required type="password" placeholder="New Password" id="newpwd" name="newpwd">
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6"></div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <button class="btn icon-btn-save btn-primary" type="submit" name="save">
                            <span class="btn-save-label"><i class="fa fa-floppy-o" aria-hidden="true"></i></span> save</button>
                        </div>
                    </div>
                </div>
            </div>
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