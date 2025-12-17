<?php
require_once("../config/config.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= siteTitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="../js/jquery.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/bootstrap.min.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    
    <link rel="stylesheet" href="../css/bootstrap-select.min.css">
    <script src="../js/bootstrap-select.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
        $('#myTable').dataTable();
    });
    $(document).ready(function () {
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
            	<h2 align="center"><i class="fa fa-address-card"></i> Travel Claim</h2>
              	<form class="form-horizontal" role="form" name="form1" action="" method="post">
                	<div class="form-group">
                    	<div class="col-md-6"><label class="col-md-4 control-label">From Date</label>
                        	<div class="col-md-8">
                            	<input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>" required>
                            </div>
                        </div>
                        <div class="col-md-6"><label class="col-md-4 control-label">To Date</label>
                        	<div class="col-md-8">
                            	<input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>"style="width:160px;" required>
                            </div>
                        </div>
                    </div>
                	<div class="form-group">
                  		<div class="col-md-6"><label class="col-md-4 control-label">Employee Name</label>
                    		<div class="col-md-8" align="left">
                      			<select name="username" id="username" class="form-control selectpicker" data-live-search="true" onChange="document.form1.submit();">
                        			<option value="">All</option>
                        			<?php
                                    $sql = mysqli_query($link1, "Select name,username,oth_empid from admin_users where status='active' ORDER BY name");
                                    while ($row = mysqli_fetch_assoc($sql)) {
                                    ?>
                        			<option value="<?= $row['username']; ?>" <?php if ($_REQUEST['username'] == $row['username']) {echo "selected";}?>><?= $row['name']." | ".$row['username']." ".$row['oth_empid'];?></option>
                        			<?php } ?>
                      			</select>
                    		</div>
                  		</div>
                        <div class="col-md-6"><label class="col-md-4 control-label">&nbsp;</label>
                        	<div class="col-md-4">
                            	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                                <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                                <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                            </div>
                            <div class="col-md-4">
                            	<a href="excelexport.php?rname=<?= base64_encode("travelClaimReport") ?>&rheader=<?= base64_encode("TravelClaim") ?>&user_id=<?= base64_encode($_REQUEST['username']) ?>&fromDate=<?= base64_encode($_REQUEST['fdate']) ?>&toDate=<?= base64_encode($_REQUEST['tdate']) ?>" title="Export in excel"><i class="fa fa-file-excel-o fa-2x" title="Export in excel"></i></a>
                            </div>
                        </div>
                   	</div>
                	<!--close form group-->
              	</form>
       		</div>
      	</div>
	</div>
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>