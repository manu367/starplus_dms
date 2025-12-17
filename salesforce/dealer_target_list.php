<?php
require_once("../config/config.php");
@extract($_POST);
////// filters value/////
$filter_str = "";
if($_REQUEST['selyear'] !=''){
	$filter_str	.= " AND year = '".$_REQUEST['selyear']."'";
}
if($_REQUEST['selmonth'] !=''){
	$mnth = date("m", strtotime($_REQUEST["selmonth"]."-".$_REQUEST["selyear"]));
	$filter_str	.= " AND month = '".$mnth."'";
}
if($_REQUEST['user_id'] !=''){
	$filter_str	.= " AND user_id = '".$_REQUEST['user_id']."'";
}
if($_SESSION['userid']=="admin"){
	
}else{
	$team = getTeamMembers($_SESSION['userid'],$link1);
	if($team){
		$team = $team.",'".$_SESSION['userid']."'"; 
	}else{
		$team = "'".$_SESSION['userid']."'"; 
	}
}
//////End filters value/////
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
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
 <script>
$(document).ready(function() {
	var dataTable = $('#target_grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"bStateSave": true,
		"order":  [[0,"asc"]],
		"ajax":{
			url :"../pagination/dealer_target_grid_data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>",  "user_id": "<?=$_REQUEST["user_id"]?>","selyear": "<?=$_REQUEST['selyear']?>","selmonth": "<?=$_REQUEST['selmonth']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".target_grid_error").html("");
				$("#target_grid").append('<tbody class="target_grid_error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#target_grid_processing").css("display","none");
			}
		}
	});
});
</script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-bullseye"></i> Dealer Target </h2>
     <?php if($_REQUEST['msg']!=''){?>
      	<h4 align="center">
        	<span 
			<?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span>
        </h4>
	  <?php }?>
      <form class="form-horizontal" role="form" name="form1" action="" method="post">
        <div class="row">
            <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Year</label>
                <select name="selyear" id="selyear" class="form-control" onChange="document.form1.submit();">
					<?php 
                    for($i=0; $i<3; $i++){ 
                        $year = date('Y', strtotime(date("Y"). ' - '.$i.' year'));
                    ?>
                    <option value="<?=$year?>"<?php if($_REQUEST["selyear"]==$year){ echo "selected";}?>><?=$year?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Month</label>
                <select name="selmonth" id="selmonth" class="form-control" onChange="document.form1.submit();">
					<?php 
                    ///// check if current year is selected then month should be come till current month
                    //if($_REQUEST["selyear"]==date("Y")){ $nmonth = date("m", strtotime(date("F")."-".$_REQUEST["selyear"]));}else if($_REQUEST["selyear"]==""){$nmonth = date("m", strtotime(date("F")."-".date("Y")));}else{ $nmonth = 12;}
					$nmonth = 12;
                    for($j=0; $j<$nmonth; $j++){ 
                        if($_REQUEST["selyear"]==date("Y") || $_REQUEST["selyear"]==""){$month = date ( 'F' , strtotime ( "-".$j." month"	 , strtotime ( date("Y-F") ) ));}else{$month = date('F', strtotime(date("Y-F"). ' + '.$j.' month'));}
                    ?>
                    <option value="<?=$month?>"<?php if($_REQUEST["selmonth"]==$month){ echo "selected";}?>><?=$month?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Employee Name</label>
                <select name="user_id" id="user_id" class="form-control selectpicker" data-live-search="true"  onChange="document.form1.submit();">
                        <option value="">--Please select--</option>
                        <?php
						if($_SESSION["userid"]=="admin"){
							$sql = mysqli_query($link1, "SELECT username,name,oth_empid FROM admin_users where 1 and oth_empid!='' order by name");
						}else{
							$sql = mysqli_query($link1, "SELECT username,name,oth_empid FROM admin_users where 1 AND username IN (".$team.") order by name");							
						}
                        while ($row = mysqli_fetch_assoc($sql)) {
                        ?>
                        <option value="<?= $row['username']; ?>" <?php if ($_REQUEST['user_id'] == $row['username']) { echo "selected";}?>><?= $row['name']." | ".$row['username']." ".$row['oth_empid'];?></option>
                        <?php } ?>
                    </select>
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-6">&nbsp;</label><br/>
                <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
            </div>
            <div class="col-sm-1 col-md-1 col-lg-1"><br/>
                <a href="../reports/excelexport.php?rname=<?= base64_encode("dealerTargetReport") ?>&rheader=<?= base64_encode("Dealer Target Report") ?>&user_id=<?= base64_encode($_REQUEST['user_id']) ?>&selyear=<?= base64_encode($_REQUEST['selyear']) ?>&selmonth=<?= base64_encode($_REQUEST['selmonth']) ?>" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-2x" title="Export details in excel"></i></a>
            </div>
          </div>
        </form>
        <br/>
      <form class="form-horizontal" role="form">
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/>
       <table  width="98%" id="target_grid" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
			  <th>Target No.</th>
              <th>Emp Name</th>
              <th>Month</th>
              <th>Year</th>
              <th>Party Name</th>
              <th>Status</th>
              <th>View</th>
            </tr>
          </thead>
          </table>
      </div>
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