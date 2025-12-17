<?php
require_once("../config/config.php");
@extract($_POST);
///////filter value
if($department){
	$dept = " AND department ='".$department."'";
}else{
	$dept = "";
}
if($subdepartment){
	$subdept = " AND subdepartment ='".$subdepartment."'";
}else{
	$subdept = "";
}
/////////
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	
}else{
	$team = getTeamMembers($_SESSION['userid'],$link1);
	if($team){
		$team = $team.",'".$_SESSION['userid']."'"; 
	}else{
		$team = "'".$_SESSION['userid']."'"; 
	}
}
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	if($isp_name){
		$team2 = getTeamMembers($isp_name,$link1);
		if($team2){
			$team2 = $team2.",'".$isp_name."'"; 
		}else{
			$team2 = "'".$isp_name."'"; 
		}
		$user_id = " AND userid IN (".$team2.")";
	}else{
		$user_id = " ";
	}
}else{
	if($isp_name){
		$team3 = getTeamMembers($isp_name,$link1);
		if($team3){
			$team3 = $team2.",'".$isp_name."'"; 
		}else{
			$team3 = "'".$isp_name."'"; 
		}
		$user_id = " AND userid IN (".$team3.")";
	}else{
		$user_id = " AND userid IN (".$team.")";
	}
}
//////End filters value/////
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=siteTitle?></title>
<script src="../js/jquery-1.10.1.min.js"></script>
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
	////// from date
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		endDate: "<?=$today?>",
		autoclose: true
	});
	/////// to date
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		endDate: "<?=$today?>",
		autoclose: true
	});
});
/////// datatable
$(document).ready(function() {
	var dataTable = $('#myTable').DataTable( {
		"processing": true,
		"serverSide": true,
		"bStateSave": true,
		"order": [[ 4, "desc" ]],
		"ajax":{
			url :"../pagination/trackdist-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>", "fdate": "<?=$_REQUEST['fdate']?>", "tdate": "<?=$_REQUEST['tdate']?>", "isp_name": "<?=$_REQUEST['isp_name']?>", "department": "<?=$_REQUEST['department']?>", "subdepartment": "<?=$_REQUEST['subdepartment']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".employee-grid-error").html("");
				$("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
				$("#employee-grid_processing").css("display","none");
				
			}
		}
	});
});
</script>
<link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
	<div class="container-fluid">
  		<div class="row content">
		<?php 
    	include("../includes/leftnav2.php");
    	?>
    		<div class="col-sm-9">
      			<h2 align="center"><i class="fa fa-child"></i>&nbsp;Track Location</h2>
      			<?php if($_REQUEST['msg']){?>
	  			<h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      			<?php }?>
	  				<form class="form-horizontal" role="form" name="form1" action="" method="post">
                    	<div class="row">
                            <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">From Date</label>
                                <input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>" required>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
                                <input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>" required>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Department</label>
                                <select name='department' id='department' class="form-control" onChange="document.form1.submit();">
                                    <option value="">All</option>
                                    <?php
                                    $res_dept=mysqli_query($link1,"select * from hrms_department_master where status='1' order by dname")or die("erro1".mysqli_error($link1));
                                    while($row_dept=mysqli_fetch_assoc($res_dept)){
                                    ?>
                                    <option value="<?=$row_dept['departmentid']?>"<?php if($_REQUEST['department'] ==$row_dept['departmentid']) { echo 'selected'; }?>><?=$row_dept['dname'];?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Sub-Department</label>
                                <span id="subdptdiv">
                                <select name='subdepartment' id='subdepartment' class="form-control" onChange="document.form1.submit();">
                                    <option value="">All</option>
                                    <?php
                                    $res_sdept=mysqli_query($link1,"select * from hrms_subdepartment_master where status='1' AND departmentid='".$_REQUEST['department']."' order by department,subdept")or die("erro1".mysqli_error($link1));
                                    while($row_sdept=mysqli_fetch_assoc($res_sdept)){
                                    ?>
                                    <option value="<?=$row_sdept['subdeptid']?>"<?php if($_REQUEST['subdepartment'] ==$row_sdept['subdeptid']) { echo 'selected'; }?>><?=$row_sdept['subdept'];?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                </span>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Employee Name</label>
                                <select name="isp_name" id="isp_name" class="form-control selectpicker" data-live-search="true">
                                <option value="">All</option>
                                <?php
								if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
									$sql = mysqli_query($link1, "SELECT name,username,oth_empid FROM admin_users WHERE oth_empid!='' AND status='active' ".$dept." ".$subdept." ORDER BY name");
								}else{
                                	$sql = mysqli_query($link1, "SELECT name,username,oth_empid FROM admin_users WHERE status='active' AND username IN (".$team.") ".$dept." ".$subdept." ORDER BY name");
								}
                                while ($row = mysqli_fetch_assoc($sql)) {
                                                ?>
                                <option value="<?= $row['username']; ?>" <?php if ($_REQUEST['isp_name'] == $row['username']) { echo "selected";}?>><?= $row['name']." | ".$row['username']." ".$row['oth_empid'];?>
                                </option>
                                <?php } ?>
                              </select>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">&nbsp;</label><br/>
                                <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                                <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                                <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6"><br/>
                                <a href="excelexport.php?rname=<?=base64_encode("trackuser")?>&rheader=<?=base64_encode("Track User")?>&user=<?=base64_encode($_REQUEST['isp_name'])?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>&department=<?=base64_encode($_REQUEST['department'])?>&subdepartment=<?=base64_encode($_REQUEST['subdepartment'])?>" title="Export Track details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Track details in excel"></i> Summerize Report</a>
                                &nbsp;&nbsp;
               					<a href="excelexport.php?rname=<?=base64_encode("trackuserdet")?>&rheader=<?=base64_encode("Track User")?>&user=<?=base64_encode($_REQUEST['isp_name'])?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>" title="Export Track details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Track details in excel"></i> Detailed Report</a>
                            </div>
                          </div>
	  				</form>
                    <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/>
      				<form class="form-horizontal" role="form">
      					
       						<table width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          						<thead>
            						<tr class="<?=$tableheadcolor?>">
                                        <th width="5%">S.No.</th>
                                        <th width="25%">User Id</th>
                                        <th width="15%">Department</th>
                                        <th width="10%">Activity</th>
                                        <th width="13%">Total Air Distance Covered(in KM)</th>
                                        <th width="12%">Google API Distance(in KM)</th>
                                        <th width="10%">Travel Date</th>
                                        <th width="10%">View</th>
                                    </tr>
                               	</thead>
          					</table>
      					
      				</form></div>
    		</div>
  		</div>
	</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>