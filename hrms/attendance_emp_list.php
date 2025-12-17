<?php
require_once("../config/config.php");

////// used for login details /////
$login_flag = mysqli_num_rows(mysqli_query($link1, "SELECT in_datetime FROM okwu_attendence  WHERE user_id = '".$_SESSION['userid']."' and in_datetime != '0000-00-00 00:00:00' and insert_date = '".$today."' "));
$login_flag_data = mysqli_fetch_array(mysqli_query($link1, "SELECT in_datetime FROM okwu_attendence  WHERE user_id = '".$_SESSION['userid']."' and in_datetime != '0000-00-00 00:00:00' and insert_date = '".$today."' "));

////// used for logout details /////
$logout_flag = mysqli_num_rows(mysqli_query($link1, "SELECT out_datetime FROM okwu_attendence  WHERE user_id = '".$_SESSION['userid']."' and out_datetime != '0000-00-00 00:00:00' and insert_date = '".$today."' "));
$logout_flag_data = mysqli_fetch_array(mysqli_query($link1, "SELECT out_datetime FROM okwu_attendence  WHERE user_id = '".$_SESSION['userid']."' and out_datetime != '0000-00-00 00:00:00' and insert_date = '".$today."' "));

////////// this script run at login time ///////////
if($_POST['login']=="Login"){
	$in = mysqli_num_rows(mysqli_query($link1, "SELECT in_datetime FROM okwu_attendence  WHERE user_id = '".$_SESSION['userid']."' and ( in_datetime == '0000-00-00 00:00:00' or in_datetime == '') and insert_date = '".$today."' "));
	if($in == 0){
		$res1 = mysqli_query($link1, "INSERT INTO okwu_attendence SET user_id = '".$_SESSION['userid']."', action_type = 'WEB', in_datetime = '".$datetime."', insert_date = '".$today."' ");
	}
	///// move to parent page
	header("location:attendance_emp_list.php?msg=".$msg."&sts=fail".$pagenav);
	exit;
}

////////// this script run at logout time ///////////
if($_POST['logout']=="Logout"){
	$in_flg = mysqli_num_rows(mysqli_query($link1, "SELECT in_datetime FROM okwu_attendence  WHERE user_id = '".$_SESSION['userid']."' and ( in_datetime != '0000-00-00 00:00:00' or in_datetime != '') and insert_date = '".$today."' "));
	$out = mysqli_num_rows(mysqli_query($link1, "SELECT out_datetime FROM okwu_attendence  WHERE user_id = '".$_SESSION['userid']."' and ( out_datetime == '0000-00-00 00:00:00' or out_datetime == '') and insert_date = '".$today."' "));
	if(($out == 0)&&($in_flg == 1)){
		$res2 = mysqli_query($link1, "INSERT INTO okwu_attendence SET user_id = '".$_SESSION['userid']."', action_type = 'WEB', out_datetime = '".$datetime."', insert_date = '".$today."' ");
	}
	///// move to parent page
	header("location:attendance_emp_list.php?msg=".$msg."&sts=fail".$pagenav);
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
 
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-book"></i> Attendance </h2>
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
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
                               
          <div class="panel-group">
            <div class="panel panel-info table-responsive">
                <div class="panel-heading heading1"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Attendance Details</div>
                 <div class="panel-body">
                 	  <div style="text-align:center; padding:10px;">
                          <div style="color: red;"> Date -  <?=$today;?></div>
                      </div>
					  <?php 
					  	if($logout_flag_data[0]!=""){ $lg_time = $logout_flag_data[0]; }else{ $lg_time = "0000-00-00 00:00:00"; } 
					  ?>
					  <div style="text-align:center; padding:10px; margin-bottom:30px;">
					  	  <?php if($login_flag_data[0]!=""){ ?>
					      <div style="color: green;">
								<!-- Display the countdown timer in an element -->
								Logged in duration - <span id="demo"></span>
						  </div>
						  <?php } ?>
                      </div>
					  					  
					  <div class="row">
                      	<div class="col-sm-6">
                        	<div style="text-align:center;margin: 20px;" > 
                             <?php if($login_flag == 0){ ?>
                             <input type="submit" class="btn <?=$btncolor?>" name="login" id="login" value="Login" />  
                             <?php }else{ ?>
                             <div style="background-color: green;padding: 5px;border-radius: 5px;color: white;font-weight: 600;" > Your log in time is: <?=$login_flag_data[0];?></div>
                             <?php } ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                        	<div style="text-align:center;margin: 20px;" > 
                            <?php if($logout_flag == 0){ ?>
                            <input type="submit" class="btn <?=$btncolor?>" name="logout" id="logout" value="Logout" /> 
                            <?php }else{ ?>
                            <div style="background-color: green;padding: 5px;border-radius: 5px;color: white;font-weight: 600;" > Your log out time is: <?=$logout_flag_data[0];?></div>
                            <?php } ?>
                            </div>
                        </div>
                      </div> 
					</div>                                                      
                </div><!--close panel body-->
            </div><!--close panel-->
         
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
<script>
	  // Set the date we're counting down to
	  var countDownDate = new Date('<?=$login_flag_data[0];?>').getTime();
	  // Update the count down every 1 second
	  var x = setInterval(function() {
		  // Get today's date and time
		  var now = new Date().getTime();
		  var lg_out = new Date('<?=$logout_flag_data[0];?>').getTime();
		  // Find the distance between now and the count down date
		  if(lg_out){
		  	var distance = lg_out - countDownDate;
		  }else{
		  	var distance = now - countDownDate;
		  }
		  // Time calculations for days, hours, minutes and seconds
		  //var days = Math.floor(distance / (1000 * 60 * 60 * 24));
		  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
		  
		  var endTime = hours+" : "+minutes+" : "+seconds;
		  // Display the result in the element with id="demo"
		  document.getElementById("demo").innerHTML = endTime;
		  // If the count down is finished, write some text 
		  if("<?=$lg_time;?>" != "0000-00-00 00:00:00") {
			clearInterval(x);
			document.getElementById("demo").innerHTML = endTime;
		  }
	 }, 1000);
</script>