<?php
include("../config/config.php");
$getpage=explode("/",$_SERVER['REQUEST_URI']);
$makeurl="http://".$_SERVER['SERVER_NAME']."/".$getpage[1]."/".$_REQUEST['pagename'].".php?".base64_decode($_REQUEST['urlparameter']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?=siteTitle?></title>
  <meta charset="utf-8">
  <link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="refresh" content="2;URL='<?=$makeurl?>'" />
  <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      <p align="center" style="margin-top:250px;">
      <span><img src="../img/processing.gif"/></span>
      <h4 align="center" style="color:#060"><?=base64_decode($_REQUEST['msg'])?></h4>
      <h4 align="center" style="color:#FF9900">You are redirecting to user permission page.</h4>
      <h4 align="center" style="color:#FF0000">Do Not Refersh or close browser, process is being execute.</h4>
      </p>
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
