<?php
require_once("security/dbh.php");
require_once("includes/globalvariables.php");
session_start();
$mobile = base64_decode($_REQUEST['m']);
$userid = base64_decode($_REQUEST['u']);
$pass = $_REQUEST['t'];
$cn1 = substr($mobile,0,2);
$cn2 = substr($mobile,-4);
$uid = $mobile;
$todayDate = $today;
$todayTime = $currtime;
$deviceId = session_id();
/// check otp exp time if it expire then we will send otp again
if(strtotime($todayTime) > strtotime($_SESSION['exptime']) && $_REQUEST['rs']=="1"){

}else{
	$resp = "OTP is not expired or wait for 90 sec.";
	$flag = 0;
}	
$_SESSION['remexptime'] = $_SESSION['exptimeS'] - time();
?>
<!DOCTYPE html>
<html>
<title><?=siteTitle?></title>
<script src="js/jquery.min.js"></script>
<script>
<?php if($uid!=""){?>
// Time before expiring
var secondsBeforeExpire = "<?=$_SESSION['remexptime']?>";

// This will trigger your timer to begin
var timer = setInterval(function(){
	// If the timer has expired, disable your button and stop the timer
	if(secondsBeforeExpire <= 0){
		clearInterval(timer);
		$("#sendOtp").removeClass("disable-click");
		$("#time-remaining").html("");
	}
	// Otherwise the timer should tick and display the results
	else{
		// Decrement your time remaining
		secondsBeforeExpire--;
		$("#time-remaining").html("Re-send OTP after: "+secondsBeforeExpire+" seconds");
		$("#sendOtp").addClass("disable-click");
	}
},1000);
<?php }?>
</script>
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
.disable-click{
    pointer-events:none;
}
.height-100 {
	height:100vh
}
.card {
	width:400px;
	border:none;
	height:300px;
	box-shadow: 0px 5px 20px 0px #d2dae3;
	z-index:1;
	display:flex;
	justify-content:center;
	align-items:center
}
.card h6 {
	color:red;
	font-size:20px
}
.inputs input {
	width:40px;
	height:40px
}
input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button {
-webkit-appearance: none;
-moz-appearance: none;
appearance: none;
margin: 0
}
.card-2 {
	background-color:#fff;
	padding:10px;
	width:350px;
	height:100px;
	bottom:-50px;
	left:20px;
	position:absolute;
	border-radius:5px
}
.card-2 .content {
	margin-top:50px
}
.card-2 .content a {
	color:red
}
.form-control:focus {
	box-shadow:none;
	border:2px solid blue
}
.validate {
	border-radius:20px;
	height:40px;
	/*background-color:red;
	border:1px solid red;*/
	width:140px
}
</style>
<script>
document.addEventListener("DOMContentLoaded", function(event) {
  
function OTPInput() {
const inputs = document.querySelectorAll('#otp > *[id]');
for (let i = 0; i < inputs.length; i++) { inputs[i].addEventListener('keydown', function(event) { if (event.key==="Backspace" ) { inputs[i].value='' ; if (i !==0) inputs[i - 1].focus(); } else { if (i===inputs.length - 1 && inputs[i].value !=='' ) { return true; } else if ((event.keyCode> 47 && event.keyCode < 58) || (event.keyCode> 95 && event.keyCode < 106)) { inputs[i].value=event.key; if (i !==inputs.length - 1) inputs[i + 1].focus(); event.preventDefault(); } else if (event.keyCode> 64 && event.keyCode < 91) { inputs[i].value=String.fromCharCode(event.keyCode); if (i !==inputs.length - 1) inputs[i + 1].focus(); event.preventDefault(); } } }); } } OTPInput();   
});
</script>
</head>
<body class="bg-info">
<div class="container height-100 d-flex justify-content-center align-items-center">
  <div class="position-relative">
    <div class="card p-2 text-center">
      <?php
	  if($mobile){
	  //print_r($_SESSION);
	  ?>
      <h6>Please enter the one time password <br>
        to verify your account</h6>
      <div> <span>A code has been sent to</span> <small><?=$cn1?>****<?=$cn2?></small> </div>
      <?php }else{?>
      <h6><?=$resp?></h6>
      <?php }?>
      <form class="form-horizontal" role="form" id="login_form" name="login_form" method="post" action="verify.php">
      <div id="otp" class="inputs d-flex flex-row justify-content-center mt-2">
        <input class="m-2 text-center form-control rounded" name="codeBox1" type="text" id="first" maxlength="1" required pattern="[0-9]"/>
        <input class="m-2 text-center form-control rounded" name="codeBox2" type="text" id="second" maxlength="1" required pattern="[0-9]"/>
        <input class="m-2 text-center form-control rounded" name="codeBox3" type="text" id="third" maxlength="1" required pattern="[0-9]"/>
        <input class="m-2 text-center form-control rounded" name="codeBox4" type="text" id="fourth" maxlength="1" required pattern="[0-9]"/>
        <input class="m-2 text-center form-control rounded" name="codeBox5" type="text" id="fifth" maxlength="1" required pattern="[0-9]"/>
        <input class="m-2 text-center form-control rounded" name="codeBox6" type="text" id="sixth" maxlength="1" required pattern="[0-9]"/>
      </div>
      <div class="mt-4">
        <button type="submit" class="btn <?=$btncolor?> px-4 validate">Validate</button>
        <input type="hidden" required name="deviceId" value="<?=base64_encode($deviceId)?>"/>
        <input type="hidden" required name="userid" value="<?=base64_encode($userid)?>"/>
        <input type="hidden" id="u" name="u" value="<?=$_GET["u"];?>">
        <input type="hidden" id="t" name="t" value="<?=$_GET["t"];?>">
        <input type="hidden" id="m" name="m" value="<?=$_GET["m"];?>">
      </div>
      <span id="time-remaining"></span>
      </form>
    </div>
    <div class="card-2">
      <div class="content d-flex justify-content-center align-items-center"> <span>Didn't get the code&nbsp;</span> <a href="verifyotp.php?token=<?=$_REQUEST['token']?>&id=<?=$_REQUEST['id']?>&rs=1" class="text-decoration-none ms-3" id="sendOtp">Resend</a></div>
    </div>
  </div>
</div>
</body>
</html>
