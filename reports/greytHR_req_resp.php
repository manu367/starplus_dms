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
	$('#myTable').dataTable();
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
                            <select name="username" id="username" class="form-control selectpicker" data-live-search="true"  onChange="document.form1.submit();">
                                    <option value="">--Please select--</option>
                                    <?php
                                    $sql = mysqli_query($link1, "SELECT name,username,oth_empid FROM admin_users WHERE status='active' ORDER BY name");
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
	?>
    <br/><br/>
    <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
      <thead>
        <tr class="<?=$tableheadcolor?>" >
          <th width="10%">User Id</th>
          <th width="10%">Request Type</th>
          <th width="10%">Update On</th>
          <th width="10%">Response Code</th>
          <th width="30%">Request</th>
          <th width="30%">Response</th>
          
        </tr>
      </thead>
      <tbody>
        <?php 
		$res_timeline = mysqli_query($link1,"SELECT * FROM greythr_api_data WHERE userid ='".$_REQUEST["username"]."' AND DATE(updatedate) >= '".$_REQUEST["fdate"]."' AND DATE(updatedate) <= '".$_REQUEST["tdate"]."' ORDER BY id ASC")or die("ER1 ".mysqli_error($link1));
		while($row_timeline = mysqli_fetch_array($res_timeline)){
        ?>
        <tr class="even pointer">
          <td><?=$row_timeline['userid']." - ".$row_timeline['empcode']?></td>
          <td><?=$row_timeline['requesttype']?></td>
          <td><?=$row_timeline['updatedate']?></td>
          <td><?=$row_timeline['response_code']?></td>
          <td><?=$row_timeline['requestdata']?></td>
          <td><?=$row_timeline['response']?></td>
          
          
        </tr>
        <?php 
		$i++;
		}
		?>
      </tbody>
    </table>
<?php }?>
            	</div>
      		</div>
		</div>
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>