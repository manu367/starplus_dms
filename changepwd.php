<?php
require_once("config/dbconnect.php");
session_start();
if(!empty($_POST['newpwd'])){
	$chkquery="select * from admin_users where username='$_SESSION[userid]' and password='$_POST[oldpwd]'";
	$check=mysqli_query($link1,$chkquery);
	if(mysqli_num_rows($check)=='1'){
	  if($_POST['newpwd']!=$_SESSION['userid']){
		$query="UPDATE admin_users set password='".$_POST['newpwd']."',first_login='Y' where username='".$_SESSION['userid']."' and password='$_POST[oldpwd]'";
		$result=mysqli_query($link1,$query) or die(mysql_error());
		echo "<p align=center style='color:#F00'>Password Changed Sucessfully<BR><BR><BR><a class='BT_style' href=index.php?msg=Please Relogin>Continue</a></p>";
		//header("Location:../index.php?msg=Please Relogin");
		exit;
	  }
	  else{
	    echo "<p align=center style='color:#F00'>New Password can not be as user id.<BR><BR><BR><a class='BT_style' href=changepwd.php>Try Again</a></p>";
	  }
	}
	else{
		echo "<p align=center style='color:#F00'>Invalid Old Password<BR><BR><BR><a class='BT_style' href=changepwd.php>Try Again</a></p>";
		exit;
	}
}
?>
<!doctype html>
<html lang="en-us" dir="ltr">
<head>
<meta charset="utf-8">
<title>Change Password</title>
<link href="../css/crm.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="JSCal2/css/win2k/win2k.css" />
<script language="javascript">
function chkData(){
if(document.frm1.oldpwd.value==''){
alert("Please enter the Old Password.");
document.frm1.oldpwd.focus();
return false;
}
if(document.frm1.newpwd.value==''){
alert("Please enter the New Password.");
document.frm1.newpwd.focus();
return false;
}
if(document.frm1.newpwd2.value==''){
alert("Please enter the confirm Password.");
document.frm1.newpwd2.focus();
return false;
}
if(document.frm1.newpwd.value!=document.frm1.newpwd2.value){
alert("Both Passwords must be same.");
document.frm1.newpwd2.focus();
return false;
}
}
/////Password length validation///
function pwdValid(){
// alert(field);
doc=document.frm1.newpwd;
if(doc.value.length!=0){
if(doc.value.length < 6){
alert("Password must have atleast 6 characters.");
doc.value='';
doc.focus();
doc.select();
}
}
}
///////////////////////////
</script>
</head>
<body>
<form name="frm1" id="frm1" method="post"  onSubmit="return chkData();">
  <table width="100%" cellpadding="0" cellspacing="0" >
  <tr>
    <td colspan="3" height="50px">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" ></td>
    <td width="50%"><fieldset class="Table_body">
      <legend class="lable"><span class="Head">Change Password</span></legend>
      <br />
      <table width="450px" align="center" border="0" cellpadding="2" cellspacing="1">
        <tr align="center">
          <td height="28" colspan="2" class="Table"><strong>Change Password</strong></td>
        </tr>
        <tr class="Table_body">
          <td height="24" class="Table_body">User Id:</td>
          <td class="Table_body"><?php echo $_SESSION['userid']?></td>
        </tr>
        <tr class="DynarchCalendar">
          <td style="font-size:x-small">Old Password: <span class="red_small">*</span></td>
          <td><input type="password" name="oldpwd" id="oldpwd" class="drop_menu"/></td>
        </Tr>
        <tr class="Table_body">
          <td class="Table_body">New Password: <span class="red_small">*</span></td>
          <td class="Table_body"><input type="password" name="newpwd" id="newpwd" class="drop_menu" onBlur="return pwdValid();"/></td>
        </tr>
        <tr class="Table_body">
          <td class="Table_body">Confirm Password: <span class="red_small">*</span></td>
          <td class="Table_body"><input type="password" name="newpwd2" id="newpwd2" class="drop_menu"/></td>
        </tr>
        <tr class="Table_body">
          <td colspan="2" class="Table_body" height="25"><span class="red_small">Note : Password must have atleast 6 characters</span></td>
          </tr>
        <tr class="Table" align="center">
          <td colspan="2"><input type="submit" value="Change"></td>
        </tr>
      </table>
      </fieldset></td>
    <td width="25%">&nbsp;</td>
  </tr>
  </table>
</form>
</body>
</html>