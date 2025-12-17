<?php
require_once("dbconnect_cansaledms.php");
require_once("../includes/common_function.php");
require_once("../includes/globalvariables.php");
$req_uid = base64_decode($_REQUEST['userid']);
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
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='pragma' content='no-cache'>	
 <title><?=siteTitle?></title>
 <script src="../js/jquery-1.10.1.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link href="../css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
  <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function(){
	$('.dt_table').dataTable({
		searching: false,
		paging: false,
		info: false,
		ordering: false
	});
	$('.selectpicker').selectpicker({
		width : "320px",
	});
});
</script>
<style type="text/css">
.mb-0 > a {
  display: block;
  position: relative;
}
.mb-0 > a:after {
  content: "\f078"; /* fa-chevron-down */
  font-family: 'FontAwesome';
  position: absolute;
  right: 0;
}
.mb-0 > a[aria-expanded="true"]:after {
  content: "\f077"; /* fa-chevron-up */
}


.col-md-3{
	 padding-left:20px;
	 padding-right:5px;
 }
td {
    border: 1px solid black;
    border-radius: 5px;
    -moz-border-radius: 5px;
    padding: 5px;
}
.alert-link{color:#843534}@-webkit-keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}@-o-keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}@keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}.progress{height:20px;margin-bottom:20px;overflow:hidden;background-color:#f5f5f5;border-radius:4px;-webkit-box-shadow:inset 0 1px 2px rgba(0,0,0,.1);box-shadow:inset 0 1px 2px rgba(0,0,0,.1)}.progress-bar{float:left;width:0%;height:100%;font-size:12px;line-height:20px;color:#fff;text-align:center;background-color:#337ab7;-webkit-box-shadow:inset 0 -1px 0 rgba(0,0,0,.15);box-shadow:inset 0 -1px 0 rgba(0,0,0,.15);-webkit-transition:width .6s ease;-o-transition:width .6s ease;transition:width .6s ease}.progress-bar-striped,.progress-striped .progress-bar{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);-webkit-background-size:40px 40px;background-size:40px 40px}.progress-bar.active,.progress.active .progress-bar{-webkit-animation:progress-bar-stripes 2s linear infinite;-o-animation:progress-bar-stripes 2s linear infinite;animation:progress-bar-stripes 2s linear infinite}.progress-bar-success{background-color:#5cb85c}.progress-striped .progress-bar-success{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-bar-info{background-color:#5bc0de}.progress-striped .progress-bar-info{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-bar-warning{background-color:#f0ad4e}.progress-striped .progress-bar-warning{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-bar-danger{background-color:#d9534f}.progress-striped .progress-bar-danger{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}
</style>
<style type="text/css">
    div.dropdown-menu.open
    {
        max-width:200px !important;
        overflow:hidden;
    }
    ul.dropdown-menu.inner
    {
        max-width:200px !important;
        overflow-y:auto;
    }
</style>
<link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
     <h3 align="center"><i class="fa fa-bullseye"></i> Dealer Target Dashboard</h3>
	  <form class="form-horizontal" role="form" name="form1" action="" method="post">
      <div class="row">
            <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3"><label class="col-xs-8 col-sm-6 col-md-6  col-lg-6">Year</label>
                <select name="selyear" id="selyear" class="form-control" onChange="document.form1.submit();">
					<?php 
                    for($i=0; $i<3; $i++){ 
                        $year = date('Y', strtotime(date("Y"). ' - '.$i.' year'));
                    ?>
                    <option value="<?=$year?>"<?php if($_REQUEST["selyear"]==$year){ echo "selected";}?>><?=$year?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3"><label class="col-xs-8 col-sm-6 col-md-6 col-lg-6">Month</label>
                <select name="selmonth" id="selmonth" class="form-control required" onChange="document.form1.submit();" required>
                	<option value="">--Please Select--</option>
					<?php 
					$nmonth = 12;
                    for($j=0; $j<$nmonth; $j++){ 
                        if($_REQUEST["selyear"]==date("Y") || $_REQUEST["selyear"]==""){$month = date ( 'F' , strtotime ( "-".$j." month"	 , strtotime ( date("Y-F") ) ));}else{$month = date('F', strtotime(date("Y-F"). ' + '.$j.' month'));}
                    ?>
                    <option value="<?=$month?>"<?php if($_REQUEST["selmonth"]==$month){ echo "selected";}?>><?=$month?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3"><label class="col-xs-8 col-sm-6 col-md-6 col-lg-6">Party Name</label>
                <select name="party_code" id="party_code" class="form-control selectpicker" data-live-search="true" onChange="document.form1.submit();">

                    <option value="" selected="selected">All</option>

                    <?php 

					$sql_chl="select * from access_location where uid='$req_uid' and status='Y' AND id_type IN ('DL')";

					$res_chl=mysqli_query($link1,$sql_chl);

					while($result_chl=mysqli_fetch_array($res_chl)){

	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));

	                      if($party_det[id_type]!='HO'){

                          ?>

                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['party_code'])echo "selected";?> >

                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>

                    </option>

                    <?php

						  }

					}

                    ?>

                 </select>
            </div>
            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><label class="col-xs-6 col-sm-6 col-md-6 col-lg-6">&nbsp;</label><br/>
            	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
            </div>
          </div>
	  
      <br/>

<?php
//if($_REQUEST['party_code']){
$i=1;
$res_mng = mysqli_query($link1,"SELECT username, name, oth_empid, designationid FROM admin_users WHERE username='".$req_uid."' AND status='Active'");
$row_mng = mysqli_fetch_assoc($res_mng);
?>
<div id="accordion">
	<div class="card">
    	<div class="card-header bg-info" id="heading-<?=$i?>">
        <?php /*?><a href="#" style="float: right;" title="Export details in excel" onClick='alert("comming soon")' class="text-success"><i class="fa fa-file-excel-o fa-lg" style="color: #fff;" title="Export details in excel"></i></a><?php */?>
      		<h5 class="mb-0">
        		<a role="button" data-toggle="collapse" href="#collapse-<?=$i?>" aria-expanded="true" aria-controls="collapse-<?=$i?>" class="text-white" style="text-decoration:none">
          		<?=$row_mng["name"]." | ".$row_mng["oth_empid"]." | ".$row_mng["username"]?><br/>
                <i>(<?php echo getAnyDetails($row_mng["designationid"],"designame","designationid","hrms_designation_master",$link1)?>)</i>
        		</a>
      		</h5>
            
    	</div>
        <div class="card-body table-responsive">
        <table id="myTable2" class="dt_table" width="100%" border="0" cellpadding="2"  cellspacing="0">
            <thead>
                <tr align="center" class="alert-success">
                  <th width="30%" height="20">Party Name</th>
                    <th width="30%"><strong>Product Sub Category</strong></th>
                    <th width="20%"><strong>Month</strong></th>
                    <th width="20%"><strong>Year</strong></th>
                    <th width="30%" style="text-align:right"><strong>Target</strong></th>
                </tr>
            </thead>
            <tbody style="font-size: 12px !important">
            <?php
			if($_REQUEST['party_code']){ $pty = " AND party_code = '".$_REQUEST['party_code']."'";}else{ $pty = "";}
			$sql_tar = "SELECT party_code,prod_code,month,year, SUM(target_val) AS tarval FROM dealer_target WHERE status='Active' ".$filter_str." ".$pty." AND user_id='".$req_uid."' group by party_code,prod_code,month,year";
			$res_tar = mysqli_query($link1, $sql_tar);
			while($row_tar = mysqli_fetch_assoc($res_tar)){
			?>
            	<tr>
            	    <td><?=str_replace("~",",",getLocationDetails($row_tar['party_code'],"name,city",$link1))."<br/>".$row_tar['party_code']?></td>
                	<td><?=$row_tar['prod_code']?></td>
                    <td><?=$row_tar['month']?></td>
                    <td><?=$row_tar['year']?></td>
                    <td style="text-align:right"><?=$row_tar["tarval"]?></td>
                 </tr>
			<?php
			}
			?>
            </tbody>
        </table>
        </div>
  	</div>
</div>
<?php //}?>                
     </form>
     <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onClick="window.location.href='myTarget.php?usercode=<?=$req_uid?>'">
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>