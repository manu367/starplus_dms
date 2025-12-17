<?php
include "../config/config.php";
if($_SESSION[userid]){
?>
<!DOCTYPE>
<html>
<style type="text/css"></style>
<link href="../LSPT/css/main.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
body {
	/*background-color: #D4D4D4;*/
	background-color: #FFFFFF;
}
-->
</style>
<style type="text/css">
<!--
.style12 {
	font-size: 16;
	color: #FFFFFF;
}
.style15 {
	font-size: 14;
	font-weight: bold;
}
.style16 {
	color: #000000;
}
.style3 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	color: #FFFFFF;
	font-size: 12px;
}
-->
</style>
<head >
<link rel="shortcut icon" href="../images/titleimage.png" type="image/png">
<title>Stock Management System(SMS)</title>
<!-- SmartMenus core CSS (required) -->
<link href="../css/sm-core-css.css" rel="stylesheet" type="text/css" />
<!-- "sm-blue" menu theme (optional, you can use your own CSS, too) -->
<link href="../css/sm-blue.css" rel="stylesheet" type="text/css" />
<!-- jQuery -->
<script type="text/javascript" src="../js/jquery.js"></script>

<!-- SmartMenus jQuery plugin -->
<script type="text/javascript" src="../js/jquery.smartmenus.js"></script>
<script src="../js/respond.min.js"></script>
<!-- SmartMenus jQuery init -->
<script type="text/javascript">
	$(function() {
		$('#main-menu').smartmenus({
			subMenusSubOffsetX: 1,
			subMenusSubOffsetY: -8
		});
	});
</script>
</head>
<body onLoad="init();" leftmargin="0"  topmargin="0" rightmargin="0" bottommargin="0">

<script> var ld=(document.all);  var ns4=document.layers; var ns6=document.getElementById&&!document.all; var ie4=document.all;  if (ns4) 	ld=document.loading; else if (ns6) 	ld=document.getElementById("loading").style; else if (ie4) 	ld=document.all.loading.style;  function init() { if(ns4){ld.visibility="hidden";} else if (ns6||ie4) ld.display="none"; } </script>
<table width="100%" height="78" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#F4BE1E"  bgcolor="#FFFFFF">
  <tr>
    <td height="78"  align="left" valign="middle" ><img src="../images/HAlogo.png" /></td>
    <td width="250">
        <?php if (isset($_SESSION['uname'])) { ?>
          <div style="display:inline-block; float:left"><table width="100%" border="0" cellpadding="0">
  <tr>
    <td><span class="style16" ><strong>Welcome : </strong></span></td>
    <td><span class="style16" ><?php echo $_SESSION['uname'];?></span></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><span class="style16" ><?php echo $_SESSION['state']; ?></span></td>
  </tr>
</table>
          <?php } ?>
</td>
  </tr>
</table>
<table width="100%" height="20" border="0" align="center" cellspacing="0" cellpadding="0">
  <tr>
    <td height="30" valign="top"><?php
include("top_admin_adv.php");
$page="";
if($_REQUEST[page]==""){
//$page="grid_top_adv";
$page="welcome_page";
}else
{
$page=$_REQUEST[page];
}
?></td>
  </tr>
</table>
<table width="100%" border="0" align="center" bgcolor="#FFFFFF" cellpadding="0" cellspacing="0" >
  <tr >
    <td height="Auto" align="center" valign="top" ><iframe src="<?=$page?>.php" id="skg" width="100%" marginwidth="0"  height="600" marginheight="0" scrolling="Auto" frameborder="0" allowtransparency="allowtransparency"></iframe></td>
  </tr>
</table>
</body>
</html>
<?php
}else
{
header("Location:../sessionExpire.php");
}
?>