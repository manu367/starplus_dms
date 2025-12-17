<?php

$page_type = "insecure";
require_once("security/backend.php");
require_once("includes/common_function.php");
require_once("includes/globalvariables.php");
//var_dump($_SESSION['userid']);
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

if($_SESSION['userid'])
{
	header("Location:admin/home2.php");
	exit;
}

else 
{
include('chatBot/database.inc.php');
session_start();
$browserid = session_id();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="shortcut icon" href="img/titleimg.png" type="image/png">
<title><?=siteTitle?></title>
<link href="loginpages/sb-admin-2.min.css" rel="stylesheet">
<link href="loginpages/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="loginpages/bootstrap.min.js"></script>
<script src="loginpages/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->
<style>
body { 
  background: url('img/Banner-1.jpg') no-repeat center center fixed; 
  width:auto;

}

.panel-default {
opacity: 0.9;
margin-top:30px;
}
.form-group.last { margin-bottom:0px; }
input[type=number] {
  height: 45px;
  width: 45px;
  font-size: 25px;
  text-align: center;
  /*border: 1px solid #000000;*/
}
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
-webkit-appearance: none;
margin: 0;
}
</style>
<link href="chatBot/chat.css" rel="stylesheet"/>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-12 col-md-5 col-lg-5 col-xl-5 col-sm-offset-7 col-md-offset-6 col-lg-offset-4 col-xl-offset-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-lock"></span> Login</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" id="login_form" name="login_form" method="post" action="verify.php">
                    <div class="form-group">
                        <div class="col-sm-12" align="center">
                            <img src="img/logo.png" style="width:210px"/>                        </div>
                    </div>
                    <br/><br/>
                    <div class="form-group">
                        <label for="userid" class="col-sm-3 control-label" style="text-align:left">USER ID</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-user" id="userid" required name="userid" placeholder="Enter user id">
                      		<div class="invalid-feedback">Please enter valid user id</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="userid" class="col-sm-3 control-label" style="text-align:left">PASSWORD</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control form-control-user" id="pwd" required name="pwd" placeholder="Enter Password">
                      		<div class="invalid-feedback">Please enter valid password</div>
                        </div>
                    </div>
                    <div class="form-group last">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" name="sbmt" id="sbmt" class="btn btn-primary btn-lg">
                                Login</button>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group text-center alert-danger" id="otp_resp"></div>
                    <?php if(isset($_REQUEST["msg"])){ ?>
                    <div class="form-group">
                        <div class="col-sm-12 text-center alert-danger">
                            <?php echo errorMsg($_REQUEST["msg"]);?>                        
                       	</div>
                    </div>
                    <?php }?>
                    <?php
					if(isset($_SESSION["logres"]["msg"]))
					{
						$t_color = (isset($_SESSION["logres"]["status"]) && $_SESSION["logres"]["status"] == "success")?'#33a201':'#e51111';
						echo '<div class="py-2 overflow-hidden" style="background:#f1f1f1;padding:15px;line-height:20px;color:'.$t_color.';margin:15px 0px;font-size:12px;border-radius:20px;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.$_SESSION["logres"]["msg"].'</div>';
						unset($_SESSION["logres"]["msg"]);
					}
					unset($_SESSION["logres"]);
					unset($_SESSION["otp"]);
					?>
                    </form>
                </div>
                <div class="panel-footer">
                	<span>Copyright &copy; <?=COMPANYNAME?> <?=date("Y")?>. All Rights Reserved. Powered By : <a href="http://www.candoursoft.com/" target="_blank">CANDOUR SOFTWARE</a></span>                </div>
            </div>
        </div>
    </div>
</div>
<?php /*?>	
<div class="fabs">
  <div class="chat">
    <div class="chat_header">
      <div class="chat_option">
      <div class="header_img">
        <img src="chatBot/image/logo.png"/>        </div>
        <span id="chat_head">Candour Software</span> <br> <span class="agent">ChatBOT</span> <span class="online">(Online)</span>
       <span id="chat_fullscreen_loader" class="chat_fullscreen_loader"><i class="fullscreen zmdi zmdi-window-maximize"></i></span>      </div>
    </div>
    <div id="chat_fullscreen" class="chat_conversion chat_converse">
      <span class="chat_body"><p>We make it simple and seamless for businesses and people to talk to each other. Type " hi " to use BOT menu</p></span> 
      <span class="messages-list">
      <?php
		$res=mysqli_query($con,"SELECT * FROM message WHERE browserid='$browserid'");
		if(mysqli_num_rows($res)>0){
			$html='';
			while($row=mysqli_fetch_assoc($res)){
				$message=$row['message'];
				$added_on=$row['added_on'];
				$strtotime=strtotime($added_on);
				$time=date('h:i A',$strtotime);
				$type=$row['type'];
				if($type=='user'){
					$class="chat_msg_item_user";
					$imgAvatar="user_avatar.png";
					$name="Me";
					$sts = '<div class="status">'.$time.'</div>';
				}else{
					$class="chat_msg_item_admin";
					$imgAvatar="bot_avatar.png";
					$name="Chatbot";
					$sts = "";
				}
				$html.='<span class="chat_msg_item '.$class.'"><div class="chat_avatar"><img src="chatBot/image/'.$imgAvatar.'" class="avatar-sm rounded-circle"></div>'.$message.'</span>'.$sts;
			}
			//$html.='<span class="chat_msg_item"><ul class="tags"><li><a href="#" onclick="send_btnmsg(1)" style="text-decoration:none">Product Registration</a></li><li><a href="#" onclick="send_btnmsg(2)" style="text-decoration:none">Service Request</a></li><li><a href="#" onclick="send_btnmsg(5)" style="text-decoration:none">Buy products</a></li></ul></span>';
			echo $html;
		}else{
		?>
        <span class="chat_msg_item"><ul class="tags"><li><a href="#" onClick="send_btnmsg('hi')" style="text-decoration:none">Main Menu</a></li><li><a href="#" onClick="send_btnmsg('Product Registration')" style="text-decoration:none">Product Registration</a></li><li><a href="#" onClick="send_btnmsg('Service Request')" style="text-decoration:none">Service Request</a></li><li><a href="#" onClick="send_btnmsg('Buy Products')" style="text-decoration:none">Buy Products</a></li></ul></span>
        <!--<span class="chat_msg_item"><ul class="tags"><li><a href="#" onclick="send_btnmsg('hi')" style="text-decoration:none">Main Menu</a></li></ul></span>-->
		<?php
        }
			?>
        </span>    </div>
    <div class="fab_field">
      <!--<a id="fab_camera" class="fab"><i class="zmdi zmdi-camera"></i></a>-->
      <a id="fab_send" class="fab" onClick="send_msg()"><i class="zmdi zmdi-mail-send"></i></a>
      <!--<textarea id="chatSend" name="chat_message" placeholder="Send a message" class="chat_field chat_message" onkeypress="process(event, this);"></textarea>-->
      <input id="chatSend" name="chat_message" placeholder="Send a message" class="chat_field chat_message" onkeypress="process(event, this);"/>
    </div>
  </div>
    <a id="prime" class="fab"><i class="prime zmdi zmdi-comment-outline"></i></a></div>
</body>
<script type="text/javascript"> 
$(document).bind("contextmenu",function(e) {
 e.preventDefault();
});	
document.onkeydown = function (e) {
	if (event.keyCode == 123) {
		return false;
	}
	if (e.ctrlKey && e.shiftKey && (e.keyCode == 'I'.charCodeAt(0) || e.keyCode == 'i'.charCodeAt(0))) {
		return false;
	}
	if (e.ctrlKey && e.shiftKey && (e.keyCode == 'C'.charCodeAt(0) || e.keyCode == 'c'.charCodeAt(0))) {
		return false;
	}
	if (e.ctrlKey && e.shiftKey && (e.keyCode == 'J'.charCodeAt(0) || e.keyCode == 'j'.charCodeAt(0))) {
		return false;
	}
	if (e.ctrlKey && (e.keyCode == 'U'.charCodeAt(0) || e.keyCode == 'u'.charCodeAt(0))) {
		return false;
	}
	if (e.ctrlKey && (e.keyCode == 'S'.charCodeAt(0) || e.keyCode == 's'.charCodeAt(0))) {
		return false;
	}
}
  $("#sbmt").click(function(event) {

    // Fetch form to apply custom Bootstrap validation
    var form = $("#login_form")

    if (form[0].checkValidity() === false) {
      event.preventDefault()
      event.stopPropagation()
    }
    
    form.addClass('was-validated');
    // Perform ajax submit here...
});
</script> 
<script src='chatBot/jquery-1.11.3.min.js'></script>
<script>
    hideChat(0);

$('#prime').click(function() {
  toggleFab();
});
////// function to handle enter click on text area
function process(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if (code == 13) { //Enter keycode
        //alert("Sending your Message : " + document.getElementById('txt').value);
		send_msg();
    }
}

//Toggle chat and links
function toggleFab() {
  $('.prime').toggleClass('zmdi-comment-outline');
  $('.prime').toggleClass('zmdi-close');
  $('.prime').toggleClass('is-active');
  $('.prime').toggleClass('is-visible');
  $('#prime').toggleClass('is-float');
  $('.chat').toggleClass('is-visible');
  $('.fab').toggleClass('is-visible');
  
}

  $(document).ready(function() {
        hideChat(4);
  });

  $('#chat_fullscreen_loader').click(function(e) {
      $('.fullscreen').toggleClass('zmdi-window-maximize');
      $('.fullscreen').toggleClass('zmdi-window-restore');
      $('.chat').toggleClass('chat_fullscreen');
      //$('.fab').toggleClass('is-hide');
      $('.header_img').toggleClass('change_img');
      $('.img_container').toggleClass('change_img');
      $('.chat_header').toggleClass('chat_header2');
      $('.fab_field').toggleClass('fab_field2');
      $('.chat_converse').toggleClass('chat_converse2');
      //$('#chat_converse').css('display', 'none');
     // $('#chat_body').css('display', 'none');
     // $('#chat_form').css('display', 'none');
     // $('.chat_login').css('display', 'none');
     // $('#chat_fullscreen').css('display', 'block');
  });

function hideChat(hide) {
    switch (hide) {
      case 4:
            $('.chat_fullscreen_loader').css('display', 'block');
            $('#chat_fullscreen').css('display', 'block');
            break;
    }
}

    </script>
<script type="text/javascript">
		 function getCurrentTime(){
			var now = new Date();
			var hh = now.getHours();
			var min = now.getMinutes();
			var ampm = (hh>=12)?'PM':'AM';
			hh = hh%12;
			hh = hh?hh:12;
			hh = hh<10?'0'+hh:hh;
			min = min<10?'0'+min:min;
			var time = hh+":"+min+" "+ampm;
			return time;
		 }
		 function send_msg(){
			//jQuery('.chat_msg_item').hide();
			var txt=jQuery('#chatSend').val();
			if(txt){
				var bwsrid = "<?=$browserid?>";
				var html='<span class="chat_msg_item chat_msg_item_user"><div class="chat_avatar"><img src="chatBot/image/user_avatar.png" class="avatar-sm rounded-circle"></div>'+txt+'</span><div class="status">'+getCurrentTime()+'</div>';
				jQuery('.messages-list').append(html);
				jQuery('#chatSend').val('');
				if(txt){
					jQuery.ajax({
						url:'chatBot/get_bot_message.php',
						type:'post',
						data:'txt='+txt+'&bwsid='+bwsrid,
						success:function(result){
							//var html='<span class="chat_msg_item chat_msg_item_admin"><div class="chat_avatar"><img src="image/bot_avatar.png" class="avatar-sm rounded-circle"></div>'+result+'</span><div class="status">'+getCurrentTime()+'</div>';
							var html='<span class="chat_msg_item chat_msg_item_admin"><div class="chat_avatar"><img src="chatBot/image/bot_avatar.png" class="avatar-sm rounded-circle"></div>'+result+'</span>';
							jQuery('.messages-list').append(html);
							jQuery('.chat_conversion').scrollTop(jQuery('.chat_conversion')[0].scrollHeight);
						}
					});
				}
			}else{
				
			}
		 }
		 function send_btnmsg(msg){
			//jQuery('.chat_msg_item').hide();
			var txt=msg;
			if(txt){
				var bwsrid = "<?=$browserid?>";
				var html='<span class="chat_msg_item chat_msg_item_user"><div class="chat_avatar"><img src="chatBot/image/user_avatar.png" class="avatar-sm rounded-circle"></div>'+txt+'</span><div class="status">'+getCurrentTime()+'</div>';
				jQuery('.messages-list').append(html);
				jQuery('#chatSend').val('');
				if(txt){
					jQuery.ajax({
						url:'chatBot/get_bot_message.php',
						type:'post',
						data:'txt='+txt+'&bwsid='+bwsrid,
						success:function(result){
							//var html='<span class="chat_msg_item chat_msg_item_admin"><div class="chat_avatar"><img src="image/bot_avatar.png" class="avatar-sm rounded-circle"></div>'+result+'</span><div class="status">'+getCurrentTime()+'</div>';
							var html='<span class="chat_msg_item chat_msg_item_admin"><div class="chat_avatar"><img src="chatBot/image/bot_avatar.png" class="avatar-sm rounded-circle"></div>'+result+'</span>';
							jQuery('.messages-list').append(html);
							jQuery('.chat_conversion').scrollTop(jQuery('.chat_conversion')[0].scrollHeight);
						}
					});
				}
			}else{
			}
		 }
      </script>
	  <?php */?>
</html>
<?php }?>