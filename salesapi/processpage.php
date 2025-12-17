<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title>SALES APP</title>
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
<style>
body {
  font-size: 16px;
}
</style>
</head>
<body>
<div class="container-fluid">
	<div class="col-sm-12 col-md-3 col-lg-3">
    	<div class="row">
        	<!--<div class="col-sm-12 col-md-3 col-lg-3"><img src="../img/inner_logo.png" width="200"/></div>-->
            <div class="col-sm-12 col-md-9 col-lg-9"><h2><i class="fa fa-thumbs-up fa-lg"></i> <?=$_REQUEST['headerline']?></h2></div>
        </div>
   	  	<div class="row" id="page-wrap">
			<div class="jumbotron" style="height:200px">
              <h1 class="display-3"><?=$_REQUEST['respheadmsg']?></h1>
              <p class="lead"><?=base64_decode($_REQUEST['respmsg'])?></p>
            </div>
			<?php if($_REQUEST['headerline']=="Sales Order"){ ?>
			<button title="Make New Sale Order" type="button" class="btn btn-primary" onClick="window.location.href='postNewPO.php?usercode=<?=$_REQUEST['usercode']?>&latitude=<?=$_REQUEST['latitude']?>&longitude=<?=$_REQUEST['longitude']?>&taskid=<?=$_REQUEST['taskid']?>&trackaddress=<?=$_REQUEST['trackaddress']?>'"><span>Make Sale Order</span></button>
			<?php }else if($_REQUEST['headerline']=="Combo Sales Order"){ ?>
			<button title="Make New Sale Order" type="button" class="btn btn-primary" onClick="window.location.href='postNewCPO.php?usercode=<?=$_REQUEST['usercode']?>&latitude=<?=$_REQUEST['latitude']?>&longitude=<?=$_REQUEST['longitude']?>&taskid=<?=$_REQUEST['taskid']?>&trackaddress=<?=$_REQUEST['trackaddress']?>'"><span>Make Sale Order</span></button>
			<?php } else if($_REQUEST['headerline']=="Location Creation"){ ?>
			<button title="Add New Location" type="button" class="btn btn-primary" onClick="window.location.href='addNewDealer.php?usercode=<?=$_REQUEST['usercode']?>&latitude=<?=$_REQUEST['latitude']?>&longitude=<?=$_REQUEST['longitude']?>&taskid=<?=$_REQUEST['taskid']?>&trackaddress=<?=$_REQUEST['trackaddress']?>'"><span>Add New Location</span></button>
			<?php } else if($_REQUEST['headerline']=="Dealer Target"){ ?>
			<button title="Add Dealer Target" type="button" class="btn btn-primary" onClick="window.location.href='postDealerTarget.php?userid=<?=base64_encode($_REQUEST['usercode'])?>&usercode=<?=$_REQUEST['usercode']?>&latitude=<?=$_REQUEST['latitude']?>&longitude=<?=$_REQUEST['longitude']?>&taskid=<?=$_REQUEST['taskid']?>&trackaddress=<?=$_REQUEST['trackaddress']?>'"><span>Add Dealer Target</span></button>
			<?php }else{
			
			}?>
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
