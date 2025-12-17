<?php
require_once("../config/config.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= siteTitle ?></title>
	 <script src="../js/jquery-1.10.1.min.js"></script>
     <link href="../css/font-awesome.min.css" rel="stylesheet">
     <link href="../css/abc.css" rel="stylesheet">
     <script src="../js/bootstrap.min.js"></script>
     <link href="../css/abc2.css" rel="stylesheet">
     <link rel="stylesheet" href="../css/bootstrap.min.css">
      <link rel="stylesheet" href="../css/bootstrap-select.min.css">
     <script src="../js/bootstrap-select.min.js"></script>
     <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
     <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 	<script type="text/javascript">
	$(document).ready(function () {
	/////// datatable
	$('#fdate').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
	});
	$(document).ready(function () {
		$('#tdate').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
	});
	
	</script>
    <style type="text/css">
	table {
  table-layout:fixed;
}
table td {
  word-wrap: break-word;
  max-width: 400px;
}
#example td {
  white-space:inherit;
}
	</style>
 	<link rel="stylesheet" href="../css/datepicker.css">
	<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
	<div class="container-fluid">
   		<div class="row content">
            <?php
            include("../includes/leftnav2.php");
            ?>
            <div class="col-sm-9 tab-pane fade in active" id="home">
            	<h2 align="center"><i class="fa fa-users"></i> GreytHR API Request Response</h2>
              	<form class="form-horizontal" role="form" name="form1" action="" method="post">
                	<div class="row">
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">From Date</label>
                            <input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>">
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
                            <input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>">
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Employee Name</label>
                            <select name="username" id="username" class="form-control selectpicker" data-live-search="true">
                                    <option value="">--Please select--</option>
                                    <?php
                                    $sql = mysqli_query($link1, "SELECT name,username,oth_empid FROM admin_users WHERE 1 ORDER BY name");
                                    while ($row = mysqli_fetch_assoc($sql)) {
                                    ?>
                                    <option value="<?= $row['username']; ?>" <?php if ($_REQUEST['username'] == $row['username']) { echo "selected";}?>><?= $row['name']." | ".$row['username']." ".$row['oth_empid'];?></option>
                                    <?php } ?>
                            	</select>
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">&nbsp;</label><br/>
                        	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                            <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                        </div>
                      </div>
              		</form>
<?php 
if($_REQUEST["username"]){	
	$usercode = $_REQUEST["username"];
	$fromdate = $_REQUEST['fdate'];
	$todate = $_REQUEST['tdate'];
	$datediff = daysDifference($todate,$fromdate);
	///////// get eastman employee code 
	$res_emp = mysqli_query($link1,"SELECT oth_empid FROM admin_users WHERE username='".$usercode."'");
	$row_emp = mysqli_fetch_assoc($res_emp);
	$empcode = $row_emp["oth_empid"];
	$msg = "";
	for($j=0; $j<=$datediff; $j++){
		$makedate = date('Y-m-d', strtotime($fromdate. ' + '.$j.' days'));
		//echo "<br/>";
		////// get attendance
		$res_att = mysqli_query($link1,"SELECT in_datetime,out_datetime FROM user_attendence WHERE user_id='".$usercode."' AND insert_date='".$makedate."'");
		$row_att = mysqli_fetch_assoc($res_att);
		
		//$swipes = file_get_contents("swipes.txt");//Batch of swipes, one swipe per line
		if($row_att["in_datetime"]!="" && $row_att["in_datetime"]!="0000-00-00 00:00:00"){
			$punchtime = date(sprintf('Y-m-d\TH:i:s%sP', substr(microtime(), 1, 4)), strtotime($row_att["in_datetime"]));
			$mkpunchtime = $punchtime;
			$swipes = $mkpunchtime.",".$empcode.",Gurgaon,1";///// for IN type=1 and for OUT type = 0
			$id = "4398033b-d66c-4a72-94f6-e6bc3b8aa1db";//API ID generated from greytHR in API details page
			$private_key = file_get_contents("private-key.pem");
			$pkeyid = openssl_pkey_get_private($private_key);//Private Key generated from greytHR in API details page
			openssl_sign($swipes, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
			$data = array(
				"id" => $id,
				"swipes" => $swipes,
				"sign" => base64_encode($signature)
			);
			////start curl
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://eastmanglobal-corp.greythr.com/v2/attendance/asca/swipes",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER => true,
				CURLOPT_NOBODY => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $data,
				CURLOPT_HTTPHEADER => array(
					"X-Requested-With: XMLHttpRequest"
				)
			));
			$response = curl_exec($curl);
			$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			//Need to test for status 200(Ok) to make sure the request was successful
			$err = curl_error($curl);
			/////insert data in to response table
			$ty = "ATTENDANCE IN";
			mysqli_query($link1,"INSERT INTO greythr_api_data SET userid='".$usercode."', empcode='".$empcode."', requesttype='".$ty."', requestdata='".json_encode($data)."', response='".$response."',response_code='".$httpcode."',flag='M'");
			curl_close($curl);
			$msg .= "Attendance IN for ".$makedate."<br/>".$httpcode."~".$err."<br/>";
		}
		if($row_att["out_datetime"]!="" && $row_att["out_datetime"]!="0000-00-00 00:00:00"){
			$punchtime = date(sprintf('Y-m-d\TH:i:s%sP', substr(microtime(), 1, 4)), strtotime($row_att["out_datetime"]));
			$mkpunchtime = $punchtime;
			$swipes = $mkpunchtime.",".$empcode.",Gurgaon,0";///// for IN type=1 and for OUT type = 0
			$id = "4398033b-d66c-4a72-94f6-e6bc3b8aa1db";//API ID generated from greytHR in API details page
			$private_key = file_get_contents("private-key.pem");
			$pkeyid = openssl_pkey_get_private($private_key);//Private Key generated from greytHR in API details page
			openssl_sign($swipes, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
			$data = array(
				"id" => $id,
				"swipes" => $swipes,
				"sign" => base64_encode($signature)
			);
			////start curl
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://eastmanglobal-corp.greythr.com/v2/attendance/asca/swipes",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER => true,
				CURLOPT_NOBODY => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $data,
				CURLOPT_HTTPHEADER => array(
					"X-Requested-With: XMLHttpRequest"
				)
			));
			$response = curl_exec($curl);
			$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			//Need to test for status 200(Ok) to make sure the request was successful
			$err = curl_error($curl);
			/////insert data in to response table
			$ty = "ATTENDANCE OUT";
			mysqli_query($link1,"INSERT INTO greythr_api_data SET userid='".$usercode."', empcode='".$empcode."', requesttype='".$ty."', requestdata='".json_encode($data)."', response='".$response."',response_code='".$httpcode."',flag='M'");
			curl_close($curl);
			$msg .= "Attendance OUT for ".$makedate."<br/>".$httpcode."~".$err."<br/>";
		}  
	}////end for loop
}
echo $msg;
?>
            	</div>
      		</div>
		</div>
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>