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
	$(document).ready(function(){
		$('#myTable').dataTable();		
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
            	<h2 align="center"><i class="fa fa-car"></i> Dealer Visit Details</h2>
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
                        <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-6">&nbsp;</label><br/>
                        	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                            <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1"><br/>
                        	<a href="excelexport.php?rname=<?= base64_encode("dealerVisitReport") ?>&rheader=<?= base64_encode("Dealer Visit") ?>&user_id=<?= base64_encode($_REQUEST['username']) ?>&fromDate=<?= base64_encode($_REQUEST['fdate']) ?>&toDate=<?= base64_encode($_REQUEST['tdate']) ?>" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-2x" title="Export details in excel"></i></a>
                        </div>
                      </div>
              		</form>
                    <br/>
<table width="100%" id="myTable" class="table-striped table-bordered table-hover">
  <thead>
  <tr class="<?=$tableheadcolor?>">
    <th width="5%">S.No.</th>
    <th width="12%">User Name</th>
    <th width="12%">Visit Date</th>
    <th width="13%">Visit City</th>
    <th width="8%">Dealer Type</th>
    <th width="10%">Dealer Code</th>
    <th width="20%">Dealer Addres</th>
    <th width="15%">Remark</th>
    <th width="5%">Location</th>
  </tr>
  </thead>
  <tbody>
  <?php
	$i = 1;
	if($_REQUEST["username"]){ $uid = "userid ='".$_REQUEST["username"]."'";}else{ $uid = "1";}
	$res_dv = mysqli_query($link1,"SELECT * FROM dealer_visit WHERE ".$uid." AND visit_date >= '".$_REQUEST["fdate"]."' AND visit_date <= '".$_REQUEST["tdate"]."' ORDER BY id DESC")or die("ER1 ".mysqli_error($link1));
	if(mysqli_num_rows($res_dv)>0){
	while($row_dv = mysqli_fetch_array($res_dv)){
		$cordinate ="'".$row_dv["latitude"].", ".$row_dv["longitude"]."'";
		$center_loc = $row_dv["latitude"].", ".$row_dv["longitude"];
		$username = mysqli_fetch_assoc(mysqli_query($link1, "SELECT name,oth_empid FROM admin_users WHERE username='".$row_dv['userid']."'"));
	?>
  <tr>
    <td><?=$i?></td>
    <td align=""><?= $username['name']." | ".$row_dv['userid']." ".$username['oth_empid']; ?></td>
    <td align="center"><?=$row_dv["visit_date"]?></td>
    <td><?=$row_dv["visit_city"]?></td>
    <td><?=$row_dv["dealer_type"]?></td>
    <td><?=str_replace("~"," , ",getAnyDetails($row_dv["party_code"],"name,city,state","asc_code","asc_master",$link1))." ".$row_dv["party_code"]?></td>
    <td><?=$row_dv["address"]?></td>
    <td><?=$row_dv["remark"]?></td>
    <td align="center"><a href="https://www.google.com/maps/dir/<?=$cordinate?>/@<?=$center_loc?>,13z" target="_blank" class="btn <?=$btncolor?>" title="check on google map"><i class="fa fa-map-marker" title="check on google map"></i></a></td>
  </tr>
  <?php
		$i++;
    }
  ?>
  <?php 
  }else{
  ?>
  <tr>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">No</td>
    <td align="center">Data</td>
    <td align="left">Found</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <?php
  }
  ?>
  </tbody>
</table>
       	  </div>
      		</div>
		</div>
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>