<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='0'>
	<meta http-equiv='pragma' content='no-cache'>
<title>My Target</title>
<script src="../js/jquery-1.10.1.min.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
	<div class="row content">
    	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      		<h2 align="center"><i class="fa fa-list-alt"></i>&nbsp;&nbsp;Target</h2>
            <div class="row">
                <div class="col-lg-4 col-md-3 col-sm-4 col-xs-12">
                	<a href="myTargetDetails.php?userid=<?=base64_encode($_REQUEST["usercode"])?>" title='My Target' style="text-decoration:none">    
                    <div class="card bg-success text-white text-center mb-4">
                        <div class="card-body">
                            <p><i class="fa fa-bullseye fa-lg" title="My Target"></i>&nbsp;&nbsp;My Target</p>
                        </div>
                    </div>    
                    </a>
                    <a href="postDealerTarget.php?userid=<?=base64_encode($_REQUEST["usercode"])?>" title='Add Party Target' style="text-decoration:none">
                    <div class="card bg-info text-white text-center mb-4">
                        <div class="card-body">
                            <p><i class="fa fa-plus fa-lg" title="Add Party Target"></i>&nbsp;&nbsp;Add Party Target</p>
                        </div>
                    </div>
                    </a>
                    <a href="dealerTargetDetails.php?userid=<?=base64_encode($_REQUEST["usercode"])?>" title='View Party Target' style="text-decoration:none">
                    <div class="card bg-warning text-white text-center mb-4">
                        <div class="card-body">
                            <p><i class="fa fa-eye fa-lg" title="View Party Target"></i>&nbsp;&nbsp;View Party Target</p>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>