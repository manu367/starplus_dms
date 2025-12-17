<?php
date_default_timezone_set('Asia/Kolkata');
include('database.inc.php');
session_start();
$browserid = session_id();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Chat</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="chat.css" rel="stylesheet"/>
   <style>
   body{
	background:url(image/bckgrnd.jpg);
	background-repeat: no-repeat;
   }
   </style>
</head>

<body>
      <h3 align="center">Click on chat button to see the BOT option</h3>

  <div class="fabs">
  <div class="chat">
    <div class="chat_header">
      <div class="chat_option">
      <div class="header_img">
        <img src="image/logo.png"/>
        </div>
        <span id="chat_head">Okaya Power</span> <br> <span class="agent">ChatBOT</span> <span class="online">(Online)</span>
       <span id="chat_fullscreen_loader" class="chat_fullscreen_loader"><i class="fullscreen zmdi zmdi-window-maximize"></i></span>

      </div>

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
				$html.='<span class="chat_msg_item '.$class.'"><div class="chat_avatar"><img src="image/'.$imgAvatar.'" class="avatar-sm rounded-circle"></div>'.$message.'</span>'.$sts;
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
        </span>    
    </div>
    <div class="fab_field">
      <!--<a id="fab_camera" class="fab"><i class="zmdi zmdi-camera"></i></a>-->
      <a id="fab_send" class="fab" onClick="send_msg()"><i class="zmdi zmdi-mail-send"></i></a>
      <textarea id="chatSend" name="chat_message" placeholder="Send a message" class="chat_field chat_message"></textarea>
    </div>
  </div>
    <a id="prime" class="fab"><i class="prime zmdi zmdi-comment-outline"></i></a>
</div>
  <script src='jquery-1.11.3.min.js'></script>

    <script>
    hideChat(0);

$('#prime').click(function() {
  toggleFab();
});


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
				var html='<span class="chat_msg_item chat_msg_item_user"><div class="chat_avatar"><img src="image/user_avatar.png" class="avatar-sm rounded-circle"></div>'+txt+'</span><div class="status">'+getCurrentTime()+'</div>';
				jQuery('.messages-list').append(html);
				jQuery('#chatSend').val('');
				if(txt){
					jQuery.ajax({
						url:'get_bot_message.php',
						type:'post',
						data:'txt='+txt+'&bwsid='+bwsrid,
						success:function(result){
							//var html='<span class="chat_msg_item chat_msg_item_admin"><div class="chat_avatar"><img src="image/bot_avatar.png" class="avatar-sm rounded-circle"></div>'+result+'</span><div class="status">'+getCurrentTime()+'</div>';
							var html='<span class="chat_msg_item chat_msg_item_admin"><div class="chat_avatar"><img src="image/bot_avatar.png" class="avatar-sm rounded-circle"></div>'+result+'</span>';
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
				var html='<span class="chat_msg_item chat_msg_item_user"><div class="chat_avatar"><img src="image/user_avatar.png" class="avatar-sm rounded-circle"></div>'+txt+'</span><div class="status">'+getCurrentTime()+'</div>';
				jQuery('.messages-list').append(html);
				jQuery('#chatSend').val('');
				if(txt){
					jQuery.ajax({
						url:'get_bot_message.php',
						type:'post',
						data:'txt='+txt+'&bwsid='+bwsrid,
						success:function(result){
							//var html='<span class="chat_msg_item chat_msg_item_admin"><div class="chat_avatar"><img src="image/bot_avatar.png" class="avatar-sm rounded-circle"></div>'+result+'</span><div class="status">'+getCurrentTime()+'</div>';
							var html='<span class="chat_msg_item chat_msg_item_admin"><div class="chat_avatar"><img src="image/bot_avatar.png" class="avatar-sm rounded-circle"></div>'+result+'</span>';
							jQuery('.messages-list').append(html);
							jQuery('.chat_conversion').scrollTop(jQuery('.chat_conversion')[0].scrollHeight);
						}
					});
				}
			}else{
			}
		 }
      </script>
</body>
</html>