<!DOCTYPE html>
<html lang="en">
<head>
  <title>API TOOl</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
  		<h2 align="center">API Testing</h2>
  <div class="row">
    <div class="col-sm-6">
    <div class="panel panel-info">
			<div class="panel-heading"><i class="fa fa-paper-plane" aria-hidden="true"></i> Request</div>
			 <div class="panel-body">     
  <form action="" method="post">
    <div class="form-group">
      <label for="url">Enter URL:</label>
      <input type="text" class="form-control" id="url" placeholder="Enter url" name="url" value="<?=$_REQUEST["url"]?>">
    </div>
    <div class="form-group">
      <label for="method_type">Method Type:</label>
      <select name="method_type" id="method_type" class="form-control">
      	<option value="POST"<?php if($_REQUEST["method_type"]=="POST"){ echo "selected";}?>>POST</option>
        <option value="GET"<?php if($_REQUEST["method_type"]=="GET"){ echo "selected";}?>>GET</option>
      </select>
    </div>
    <div class="form-group">
      <label for="url">Query String Parameter:</label>
      <input type="text" class="form-control" id="querystr" placeholder="Enter query string parameters by & separator" name="querystr" value="<?=$_REQUEST["querystr"]?>">
    </div>
    <div class="form-group">
      <label for="pwd">Body:</label>
      <textarea name="bodycontent" id="bodycontent" class="form-control" style="resize:none" placeholder="Enter parameter in raw JSON format"><?=$_REQUEST["bodycontent"]?></textarea>
    </div>
    <div class="form-group">
      <label for="pwd">Token:</label>
      <textarea name="token" id="token" class="form-control" style="resize:none" placeholder="Enter token"><?=$_REQUEST["token"]?></textarea>
    </div>
    <button type="submit" name="checkapi" value="chkapi" class="btn btn-primary">Submit</button>
  </form>  
  </div>
  </div>
</div>
    <div class="col-sm-6">
    <div class="panel panel-success">
			<div class="panel-heading"><i class="fa fa-reply-all" aria-hidden="true"></i> Response</div>
			 <div class="panel-body">
    <?php
if($_POST["checkapi"]=="chkapi"){
	$url = $_REQUEST["url"];
	if($_REQUEST["querystr"]){ $url .= "?".$_REQUEST["querystr"];}
	$method = $_REQUEST["method_type"];
	$postjson = $_REQUEST["bodycontent"];
	$token = $_REQUEST["token"];
	//////////////
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => $method,
	  CURLOPT_POSTFIELDS => $postjson,
	  CURLOPT_HTTPHEADER => array(
		'Authorization: Bearer '.$token,
		'Content-Type: application/json'
	  ),
	));
	
	$response = curl_exec($curl);
	
	curl_close($curl);
	echo '<span style="width:100%; word-wrap:break-word; display:inline-block;">';	
	echo $response;
	echo '</span>';
} 
?>
</div>
</div>
</div>
</div>
  
</div>

</body>
</html>
