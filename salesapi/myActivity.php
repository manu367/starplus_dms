<?php
	 header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
	 ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
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
      		<h2 align="center"><i class="fa fa-list-alt"></i>&nbsp;&nbsp;My Activity</h2>
            <div class="row">
                <div class="col-lg-4 col-md-3 col-sm-4 col-xs-12">
                	<a href="addActivity.php?userid=<?=base64_encode($_REQUEST["usercode"])?>" title='My Target' style="text-decoration:none">    
                    <div class="card bg-success text-white text-center mb-4">
                        <div class="card-body">
                            <p><i class="fa fa-plus fa-lg" title="My Target"></i>&nbsp;&nbsp;Add Activity</p>
                        </div>
                    </div>    
                    </a>
                    <a href="addActivity.php?userid=<?=base64_encode($_REQUEST["usercode"])?>"  title='Add Party Target' style="text-decoration:none">
                    <div class="card bg-info text-white text-center mb-4">
                        <div class="card-body">
                            <p><i class="fa fa-bullseye fa-lg" title="Add Party Target"></i>View Previous Activity</p>
                        </div>
                    </div>
                    </a></div>
            </div>
        </div>
    </div>
</div>
</body>
</html>