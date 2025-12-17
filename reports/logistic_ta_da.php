<?php
require_once("../config/config.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= siteTitle ?></title>
    <script src="../js/jquery.js"></script>
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
	$('#myTable').dataTable();
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
            	<h2 align="center"><i class="fa fa-truck"></i> Logistic TA/DA</h2>
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
                        	<a href="excelexport.php?rname=<?= base64_encode("logisticTaDaReport") ?>&rheader=<?= base64_encode("Logistic TA/DA") ?>&user_id=<?= base64_encode($_REQUEST['username']) ?>&fromDate=<?= base64_encode($_REQUEST['fdate']) ?>&toDate=<?= base64_encode($_REQUEST['tdate']) ?>" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-2x" title="Export details in excel"></i></a>
                        </div>
                      </div>
              		</form>
<?php 
if($_REQUEST["username"]){
	$empdet = explode("~",getAnyDetails($_REQUEST["username"],"name,oth_empid,phone,emailid,designationid,department,subdepartment","username","admin_users",$link1));
?>
<br/>
<table width="100%" border="1" class="table table-bordered">
  <tr>
    <td width="20%"><strong>Employee Name</strong></td>
    <td width="30%"><?=$empdet[0]?></td>
    <td width="20%"><strong>Employee Code</strong></td>
    <td width="30%"><?=$empdet[1]?></td>
    </tr>
  <tr>
    <td><strong>Designation</strong></td>
    <td><?=getAnyDetails($empdet[4],"designame","designationid","hrms_designation_master",$link1)?></td>
    <td><strong>Department</strong></td>
    <td><?=getAnyDetails($empdet[5],"dname","departmentid","hrms_department_master",$link1)." | ".getAnyDetails($empdet[6],"subdept","subdeptid","hrms_subdepartment_master",$link1)?></td>
    </tr>
  <tr>
    <td><strong>Contact Details</strong></td>
    <td><?=$empdet[2]." , ".$empdet[3];?></td>
    <td><strong>Expense Period</strong></td>
    <td><?="From:-".$_REQUEST["fdate"]." To:-".$_REQUEST["tdate"];?></td>
    </tr>
</table>
<table width="100%" border="1" class="table table-bordered">
  <thead>
  <tr class="<?=$tableheadcolor?>">
    <th width="5%">S.No.</th>
    <th width="10%">Expense Date</th>
    <th width="10%">Entry Date</th>
    <th width="25%">Description of Expenses</th>
    <th width="15%">Amount</th>
    <th width="12%">Approval By</th>
    <th width="13%">Approval Date</th>
    <th width="10%">Approval Status</th>
  </tr>
  </thead>
  <tbody>
  <?php
	$i = 1;
	$totamt = 0.00;
	$res_tada = mysqli_query($link1,"SELECT * FROM ta_da WHERE userid ='".$_REQUEST["username"]."' AND entry_date >= '".$_REQUEST["fdate"]."' AND entry_date <= '".$_REQUEST["tdate"]."' AND courier_exp!=0.00 ORDER BY id DESC")or die("ER1 ".mysqli_error($link1));
	if(mysqli_num_rows($res_tada)>0){
	while($row_tada = mysqli_fetch_array($res_tada)){
		$appdet = explode("~",getAnyDetails($row_tada["system_ref_no"],"action_by,action_date,action_time","ref_no","approval_activities",$link1));
	?>
  <tr>
    <td><?=$i?></td>
    <td align="center"><?=$row_tada["expense_date"]?></td>
    <td align="center"><?=$row_tada["entry_date"]?></td>
    <td><?=$row_tada["remark"]?></td>
    <td align="right"><?=$row_tada["courier_exp"]?></td>
    <td><?=$appdet[0]?></td>
    <td align="center"><?=$appdet[1]?></td>
    <td><?=$row_tada["status"]?></td>
  </tr>
  <?php
		$i++;
		$totamt +=$row_tada["courier_exp"];
    }
  ?>
  <tr>
    <td colspan="4" align="right"><strong>Total</strong></td>
    <td align="right"><strong><?=number_format($totamt,"2",".","");?></strong></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php 
  }else{
  ?>
  <tr>
    <td colspan="8" align="center">No data found</td>
  </tr>
  <?php
  }
  ?>
  </tbody>
</table>

	
<?php
}
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