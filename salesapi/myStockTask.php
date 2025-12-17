<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
//echo "<br/><br/>";
//print_r($_REQUEST);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='0'>
	<meta http-equiv='pragma' content='no-cache'>
<title>My Task</title>
<script src="../js/jquery-1.10.1.min.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
	<link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
	<div class="row content">
    	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      		<h2 align="center"><i class="fa fa-list-alt"></i>&nbsp;&nbsp;My Task</h2>
            <div class="row">
                <div class="col-lg-4 col-md-3 col-sm-4 col-xs-12">
                	<?php /*?><a href="DsDlStockReceiveList.php?userid=<?=base64_encode($_REQUEST["usercode"])?>" title='Stock Receive' style="text-decoration:none">    
                    <div class="card bg-success text-white text-center mb-4">
                        <div class="card-body">
                            <p><i class="fa fa-level-down fa-lg" title="Stock Receive"></i>&nbsp;&nbsp;Stock Receive</p>
                        </div>
                    </div>    
                    </a><?php */?>
                    <?php /*?><a href="saleRegistration.php?userid=<?=base64_encode($_REQUEST["usercode"])?>" title='Sale Registration' style="text-decoration:none">
                    <div class="card bg-info text-white text-center mb-4">
                        <div class="card-body">
                            <p><i class="fa fa-book fa-lg" title="Sale Registration"></i>&nbsp;&nbsp;Sale Registration</p>
                        </div>
                    </div>
                    </a>
					<?php */?>
                    <a href="receiveStockList.php?userid=<?=base64_encode($_REQUEST["usercode"])?>" title='Receive Stock' style="text-decoration:none">    
                    <div class="card bg-success text-white text-center mb-4">
                        <div class="card-body">
                            <p><i class="fa fa-level-down fa-lg" title="Stock Receive"></i>&nbsp;&nbsp;Receive Stock</p>
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