<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
require_once("dbconnect_cansaledms.php");
require_once("../includes/common_function.php");
require_once("../includes/globalvariables.php");
$req_uid = base64_decode($_REQUEST['userid']);
@extract($_POST);
////// filters value/////
$filter_str = "";
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND year = '".substr($_REQUEST['fdate'],0,4)."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND month = '".substr($_REQUEST['tdate'],5,2)."'";
}
/*if($_REQUEST['user_id'] !=''){
	$filter_str	.= " AND user_id = '".$_REQUEST['user_id']."'";
}*/
//////End filters value////
$team = getTeamMembers($_REQUEST['user_id'],$link1);
if($team){
	$team = $team.",'".$_REQUEST['user_id']."'"; 
}else{
	$team = "'".$_REQUEST['user_id']."'"; 
}
//// 
function getPerformace($per){
	if($per>90){
		$tag = "Performance - Top Performer";
	}else if($per<=90 && $per>75){
		$tag = "Performance - Inter Mediate";
	}else if($per<=75 && $per>=60){
		$tag = "Performance - Low Performer";
	}else{
		$tag = "Performance - Non Performer";
	}
	return $tag;
}
function getPerformaceBar($per){
	if($per>90){
		$cls = "success";
		$txt = "Top-Performer";
		$txt2 = "1";
	}else if($per<=90 && $per>75){
		$cls = "info";
		$txt = "Inter-Mediate";
		$txt2 = "2";
	}else if($per<=75 && $per>=60){
		$cls = "warning";
		$txt = "Low-Performer";
		$txt2 = "3";
	}else{
		$cls = "danger";
		$txt = "Non-Performer";
		$txt2 = "4";
	}
	return $cls."~".$txt."~".$txt2;
}
//// function to get acheivement
function getAcheivement($taskType,$fromDate,$toDate,$prodCode,$saleType,$team,$link1){
	if($taskType == "Dealer Visit"){
		//// calculate from old dealer vist
		$p = 0 ; $s = 0;
		if($saleType=="P" || $saleType==""){
			$row_oldcnt =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS olddealer FROM dealer_visit WHERE userid IN (".$team.") AND visit_date >= '".$fromDate."' AND visit_date <= '".$toDate."' AND dealer_type='Old'"));
			$p = $row_oldcnt["olddealer"];
		}
		//// calculate from new dealer vist
		if($saleType=="S"  || $saleType==""){
			$row_newcnt =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS newdealer FROM dealer_visit WHERE userid IN (".$team.") AND visit_date >= '".$fromDate."' AND visit_date <= '".$toDate."' AND dealer_type='New'"));
			$s = $row_newcnt["newdealer"];
		}
		//$resp = $row_oldcnt["olddealer"]."~".$row_newcnt["newdealer"];
		$resp = $p + $s;
	}
	else if($taskType == "Feedback"){
		/// get feedback count
		$row_fb =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS feedback FROM query_master WHERE entry_by IN (".$team.") AND entry_date >= '".$fromDate."' AND entry_date <= '".$toDate."'"));
		//if($row_fb["feedback"]){ echo $ach = $row_fb["feedback"];}else{echo $ach = 0;}
		$resp = $row_fb["feedback"];
	}
	else if($taskType == "Sale Order"){
		/// get sale order count (pri)
		$o = 0; $n = 0;
		if($saleType=="P" || $saleType==""){
			$row_so_pri =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT SUM(req_qty) AS socnt FROM purchase_order_data WHERE po_no IN (SELECT po_no FROM purchase_order_master WHERE sale_type LIKE 'PRIMARY' AND create_by IN (".$team.") AND entry_date >= '".$fromDate."' AND entry_date <= '".$toDate."' AND status IN ('Approved','Processed')) AND prod_code IN (SELECT productcode FROM product_master WHERE productsubcat IN (SELECT psubcatid FROM product_sub_category WHERE prod_sub_cat='".$prodCode."'))"));
		//if($row_so["socnt"]){ echo $ach = $row_so["socnt"];}else{echo $ach = 0;}
			$o = $row_so_pri["socnt"]; 
		}
		/// get sale order count (sec)
		if($saleType=="S" || $saleType==""){
			$row_so_sec =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT SUM(req_qty) AS socnt FROM purchase_order_data WHERE po_no IN (SELECT po_no FROM purchase_order_master WHERE sale_type LIKE 'SECONDARY' AND create_by IN (".$team.") AND entry_date >= '".$fromDate."' AND entry_date <= '".$toDate."' AND status IN ('Approved','Processed')) AND prod_code IN (SELECT productcode FROM product_master WHERE productsubcat IN (SELECT psubcatid FROM product_sub_category WHERE prod_sub_cat='".$prodCode."'))"));
			$n = $row_so_sec["socnt"];
		}
		//$resp = $row_so_pri["socnt"]."~".$row_so_sec["socnt"];
		$resp = $o + $n;
	}
	else if($taskType == "Collection"){
		/// get collection count
		$row_col =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT SUM(amount) AS collection FROM party_collection WHERE user_id IN (".$team.") AND entry_date >= '".$fromDate."' AND entry_date <= '".$toDate."'"));
		//if($row_col["collection"]){ echo $ach = $row_col["collection"];}else{echo $ach = 0;}
		$resp = $row_col["collection"];	
	}
	else if($taskType == "BTL Activity"){
		/// get BTL count
		$row_btl =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS btl FROM activity_master WHERE user_id IN (".$team.") AND activity_date >= '".$fromDate."' AND activity_date <= '".$toDate."' AND activity_type='BTL Activity'"));
		$resp = $row_btl["btl"];	
	}
	else if($taskType == "Meeting"){
		/// get Meeting count
		$row_meet =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS meet FROM activity_master WHERE user_id IN (".$team.") AND activity_date >= '".$fromDate."' AND activity_date <= '".$toDate."' AND activity_type='Meeting'"));
		$resp = $row_meet["meet"];	
	}
	else if($taskType == "Dealer Activeness"){
		/// get dealer activeness count
		$row_delact =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(sno) AS act FROM asc_master WHERE create_by IN (".$team.") AND is_inactive='1'"));
		$resp = $row_delact["act"];	
	}
	else{
		$resp = 0;	
	}
	return $resp;
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
	////// from date
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
	/////// to date
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
});	
$(function() {	
	$("#fdate").change(function(event) {
		//alert(this.value);
		var lastday = function(y,m){
			return  new Date(y, m +1, 0).getDate();
		}
		var seldate = this.value;
		var spl = seldate.split("-");
		var mnth = parseInt(spl[1])-1;
		var lastday = lastday(spl[0],mnth);
		var enddt = spl[0]+"-"+spl[1]+"-"+lastday;
		//alert(enddt);
		//$('#tdate').datepicker("update", enddt);
		$('#tdate').datepicker({
			format: "yyyy-mm-dd",
			startDate: seldate,
			endDate: enddt,
			autoclose: true,
		});
		$('#tdate').val(enddt);
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
     <h3 align="center"><i class="fa fa-bullseye"></i> Sales Force Dashboard</h3>
	  <form class="form-horizontal" role="form" name="form1" action="" method="post">
      <div class="row">
            <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3"><label class="col-xs-8 col-sm-6 col-md-6  col-lg-6">From Date</label>
                <input type="text" class="form-control span2" name="fdate" autocomplete="off" id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>">
            </div>
            <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3"><label class="col-xs-8 col-sm-6 col-md-6 col-lg-6">To Date</label>
                <input type="text" class="form-control span2" name="tdate" autocomplete="off" id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>">
            </div>
            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3"><label class="col-xs-8 col-sm-6 col-md-6 col-lg-6">Emp Name</label>
                <select name="user_id" id="user_id" class="form-control selectpicker" data-live-search="true" >
                        <option value="">--Please select--</option>
                        <?php
						if($req_uid=="admin"){
							$sql = mysqli_query($link1, "SELECT username,name,oth_empid FROM admin_users where 1 AND status='Active' order by name");
						}else{
							$sql = mysqli_query($link1, "SELECT username,name,oth_empid FROM admin_users where 1 AND (reporting_manager='".$req_uid."' or username='".$req_uid."') AND status='Active' order by name");
						}
                        while ($row = mysqli_fetch_assoc($sql)) {
                        ?>
                        <option value="<?= $row['username']; ?>" <?php if ($_REQUEST['user_id'] == $row['username'] || $req_uid == $row['username']) { echo "selected";}?>><?= $row['name']." | ".$row['username']." ".$row['oth_empid'];?></option>
                        <?php } ?>
                    </select>
            </div>
            <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3"><label class="col-xs-8 col-sm-6 col-md-6 col-lg-6">Type</label>
                <select name="sale_type" id="sale_type" class="form-control">
                	<option value="">All</option>
                    <option value="P"<?php if ($_REQUEST['sale_type'] == "P") { echo "selected";}?>>Primary</option>
                    <option value="S"<?php if ($_REQUEST['sale_type'] == "S") { echo "selected";}?>>Secondry</option>
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
if($_REQUEST['user_id']){
$i=1;
$res_mng = mysqli_query($link1,"SELECT username, name, oth_empid, designationid FROM admin_users WHERE username='".$_REQUEST['user_id']."' AND status='Active'");
$row_mng = mysqli_fetch_assoc($res_mng);
?>
<div id="accordion">
	<div class="card">
    	<div class="card-header bg-danger" id="heading-<?=$i?>">
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
                <tr align="center" class="alert-danger">
                    <th width="15%" height="20"><strong>Product Sub Category</strong></th>
                    <th width="10%"><strong>Task Name</strong></th>
                    <!--<th width="15%"><strong>Remark</strong></th>-->
                    <th width="10%" style="text-align:right"><strong>Target</strong></th>
                    <th width="10%" style="text-align:right"><strong>Achievement</strong></th>
                    <th width="10%" style="text-align:right"><strong>Shortfall</strong></th>
                    <th width="15%" style="text-align:right"><strong>Achievement %</strong></th>
                </tr>
            </thead>
            <tbody style="font-size: 12px !important">
            <?php
			$ach_per1 = 0;
			$top_perf = array();
			$med_perf = array();
			$low_perf = array();
			$non_perf = array();
			$sql_tar = "SELECT SUM(target_val) AS target_val, task_name, prod_code,GROUP_CONCAT(remark) AS ps FROM sf_target_data WHERE status='Active' ".$filter_str." AND user_id IN (".$team.") group by task_name,prod_code,month,year";
			$res_tar = mysqli_query($link1, $sql_tar);
			while($row_tar = mysqli_fetch_assoc($res_tar)){
				////achive
				$resp = getAcheivement($row_tar['task_name'],$_POST['fdate'],$_POST['tdate'],$row_tar['prod_code'],$_POST['sale_type'],$team,$link1);
				$tarval = 0;
				if($row_tar['task_name']=="Sale Order"){
					$p_target =0; $s_target =0; $ps_sum = 0;
					//////
					$fetch_ps = explode(",",$row_tar['ps']);
					for($a=0; $a<count($fetch_ps); $a++){
						$explod_rmk = explode(" ",$fetch_ps[$a]);
						///check selected filter
						if($_POST['sale_type']=="P" || $_POST['sale_type']==""){
							$p_target = str_replace("P-","",$explod_rmk[0]);
						}
						if($_POST['sale_type']=="S" || $_POST['sale_type']==""){
							$s_target = str_replace("S-","",$explod_rmk[1]);
						}	
						$ps_sum += $p_target + $s_target;
					}
					$tarval = $ps_sum;
				}else if($row_tar['task_name']=="Dealer Visit"){
					$p_target =0; $s_target =0; $ps_sum = 0;
					$fetch_ps = explode(",",$row_tar['ps']);
					for($a=0; $a<count($fetch_ps); $a++){
						$explod_rmk = explode(" ",$fetch_ps[$a]);
						///check selected filter
						if($_POST['sale_type']=="P" || $_POST['sale_type']==""){
							$p_target = str_replace("Old-","",$explod_rmk[0]);
						}
						if($_POST['sale_type']=="S" || $_POST['sale_type']==""){
							$s_target = str_replace("New-","",$explod_rmk[1]);
						}
						$ps_sum += $p_target + $s_target;
					}
					$tarval = $ps_sum;
				}else{
					$tarval = $row_tar['target_val'];
				}
				$shorfall = $tarval-$resp;
			?>
            	<tr>
                	<td><?=$row_tar['prod_code']?></td>
                    <td><?=$row_tar['task_name']?></td>
                    <?php /*?><td><?=$row_tar['remark']?></td><?php */?>
                    <td style="text-align:right"><?=$tarval?></td>
                    <td style="text-align:right"><?=round($resp)?>
                    <?php if($row_tar['task_name']=="Sale Order"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashsaleorder")?>&rheader=<?=base64_encode("Sales Order")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_mng["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Dealer Visit"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashdealervisit")?>&rheader=<?=base64_encode("Dealer Visit")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_mng["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Collection"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashcollection")?>&rheader=<?=base64_encode("Collection")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_mng["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Feedback"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashfeedback")?>&rheader=<?=base64_encode("Feedback")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_mng["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Dealer Activeness" || $row_tar['task_name']=="Meeting" || $row_tar['task_name']=="BTL Activity"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashactivity")?>&rheader=<?=base64_encode($row_tar['task_name'])?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_mng["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else{?>
                    	
                    <?php }?>
                    </td>
                    <td style="text-align:right"><?php if($shorfall>0){ echo round($shorfall);}?></td>
                    <td style="text-align:right"><?php if($tarval){$perc = number_format((($resp/$tarval)*100),'2','.',''); echo $perc."%";}else{ echo $perc=0;}?></td>
                 </tr>
			<?php
				$ach_per1 += $perc;
			}
			?>
            
            </tbody>
        </table>
        <table class="" width="100%" border="0" cellpadding="2"  cellspacing="0">
        <tr>
        <td align="center"  width="50%" style="border-right:none">
		<?php
			if($ach_per1>100){ $extsale = 10;}else{ $extsale = 0;}
			//// get emp performance
			$res_empperf = mysqli_query($link1,"SELECT * FROM emp_performance WHERE username='".$row_mng["username"]."'");
			$row_empperf = mysqli_fetch_assoc($res_empperf);
            $perf = $ach_per1 + $extsale + $row_empperf["achive_dealer_target"] + $row_empperf["achive_dealer_meet"] + $row_empperf["achive_daily_meet"] + $row_empperf["achive_reporting"] + $row_empperf["achive_market_share"];
            $tag = getPerformace($perf);
            $bar = explode("~",getPerformaceBar($perf));
            echo $tag;
			///// make array count performance wise
			if($bar[2]=="1"){ 
				$top_perf[]=$row_mng["name"]." | ".$row_mng["oth_empid"]." | ".$row_mng["username"]." (".$perf."%)";
			}else if($bar[2]=="2"){
				$med_perf[]=$row_mng["name"]." | ".$row_mng["oth_empid"]." | ".$row_mng["username"]." (".$perf."%)";
			}else if($bar[2]=="3"){
				$low_perf[]=$row_mng["name"]." | ".$row_mng["oth_empid"]." | ".$row_mng["username"]." (".$perf."%)";
			}else{
				$non_perf[]=$row_mng["name"]." | ".$row_mng["oth_empid"]." | ".$row_mng["username"]." (".$perf."%)";
			}
            ?>
        </td>
        <td align="center" width="50%"  style="border-left:none">
        		<div class="progress" style="margin-bottom:0px;margin-top:5px;">
                <div class="progress-bar progress-bar-<?=$bar[0]?> progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:<?=$perf?>%" title="<?=$perf?>% Achieve">
                  <?=$perf?>% <?=$bar[1]?>
                </div>
              </div></td>
        </tr>
        </table>
        </div>
    	<div id="collapse-<?=$i?>" class="collapse" data-parent="#accordion" aria-labelledby="heading-<?=$i?>">
      		<div class="card-body">
            	<?php
				$res_ch1 = mysqli_query($link1,"SELECT username, name, oth_empid, designationid FROM admin_users WHERE reporting_manager='".$row_mng["username"]."' AND status='Active'");
				if(mysqli_num_rows($res_ch1)>0){
				?>
        		<div id="accordion-<?=$i?>">
				<?php
                $j=1;
                while($row_ch1 = mysqli_fetch_assoc($res_ch1)){
                ?>
            		<div class="card">
            			<div class="card-header bg-success" id="heading-<?=$i?>-<?=$j?>">
                        	<?php /*?><a href="#" style="float: right;" title="Export details in excel" onClick='alert("comming soon")' class="text-success"><i class="fa fa-file-excel-o fa-lg" style="color: #fff;" title="Export details in excel"></i></a><?php */?>
              				<h5 class="mb-0">
                				<a class="collapsed text-white" role="button" data-toggle="collapse" href="#collapse-<?=$i?>-<?=$j?>" aria-expanded="false" aria-controls="collapse-<?=$i?>-<?=$j?>">
                  				<?=$row_ch1["name"]." | ".$row_ch1["oth_empid"]." | ".$row_ch1["username"]?><br/><i>(<?php echo getAnyDetails($row_ch1["designationid"],"designame","designationid","hrms_designation_master",$link1)?>)</i>
                				</a>
              				</h5>
            			</div>
                        <div class="card-body table-responsive">
						<table id="myTable2" class="dt_table" width="100%" border="0" cellpadding="2"  cellspacing="0">
                            <thead>
                                <tr align="center" class="alert-success">
                                    <th width="15%"><strong>Product Sub Category</strong></th>
                                    <th width="10%"><strong>Task Name</strong></th>
                                    <!--<th width="15%"><strong>Remark</strong></th-->
                                    <th width="10%" style="text-align:right"><strong>Target</strong></th>
                                    <th width="10%" style="text-align:right"><strong>Achievement</strong></th>
                                    <th width="10%" style="text-align:right"><strong>Shortfall</strong></th>
                                    <th width="15%" style="text-align:right"><strong>Achievement %</strong></th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px !important">
							<?php
							$team = getTeamMembers($row_ch1["username"],$link1);
							if($team){
								$team .= $team.",'".$row_ch1["username"]."'"; 
							}else{
								$team .= "'".$row_ch1["username"]."'"; 
							}
							$ach_per2 = 0;
                            $sql_tar = "SELECT SUM(target_val) AS target_val, task_name, prod_code,GROUP_CONCAT(remark) AS ps FROM sf_target_data WHERE status='Active' ".$filter_str." AND user_id IN (".$team.") group by task_name,prod_code,month,year";
                            $res_tar = mysqli_query($link1, $sql_tar);
                            while($row_tar = mysqli_fetch_assoc($res_tar)){
                                ////achive
								$resp = getAcheivement($row_tar['task_name'],$_POST['fdate'],$_POST['tdate'],$row_tar['prod_code'],$_POST['sale_type'],$team,$link1);
								$tarval = 0;
								if($row_tar['task_name']=="Sale Order"){
									$p_target =0; $s_target =0; $ps_sum = 0;
									//////
									$fetch_ps = explode(",",$row_tar['ps']);
									for($a=0; $a<count($fetch_ps); $a++){
										$explod_rmk = explode(" ",$fetch_ps[$a]);
										///check selected filter
										if($_POST['sale_type']=="P" || $_POST['sale_type']==""){
											$p_target = str_replace("P-","",$explod_rmk[0]);
										}
										if($_POST['sale_type']=="S" || $_POST['sale_type']==""){
											$s_target = str_replace("S-","",$explod_rmk[1]);
										}	
										$ps_sum += $p_target + $s_target;
									}
									$tarval = $ps_sum;
								}else if($row_tar['task_name']=="Dealer Visit"){
									$p_target =0; $s_target =0; $ps_sum = 0;
									$fetch_ps = explode(",",$row_tar['ps']);
									for($a=0; $a<count($fetch_ps); $a++){
										$explod_rmk = explode(" ",$fetch_ps[$a]);
										///check selected filter
										if($_POST['sale_type']=="P" || $_POST['sale_type']==""){
											$p_target = str_replace("Old-","",$explod_rmk[0]);
										}
										if($_POST['sale_type']=="S" || $_POST['sale_type']==""){
											$s_target = str_replace("New-","",$explod_rmk[1]);
										}
										$ps_sum += $p_target + $s_target;
									}
									$tarval = $ps_sum;
								}else{
									$tarval = $row_tar['target_val'];
								}
                                $shorfall = $tarval-$resp;
                            ?>
                                <tr>
                                    <td><?=$row_tar['prod_code']?></td>
                                    <td><?=$row_tar['task_name']?></td>
                                    <?php /*?><td><?=$row_tar['remark']?></td><?php */?>
                                    <td style="text-align:right"><?=$tarval?></td>
                                    <td style="text-align:right"><?=round($resp)?>
                                    <?php if($row_tar['task_name']=="Sale Order"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashsaleorder")?>&rheader=<?=base64_encode("Sales Order")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_ch1["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Dealer Visit"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashdealervisit")?>&rheader=<?=base64_encode("Dealer Visit")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_ch1["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Collection"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashcollection")?>&rheader=<?=base64_encode("Collection")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_ch1["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Feedback"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashfeedback")?>&rheader=<?=base64_encode("Feedback")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_ch1["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Dealer Activeness" || $row_tar['task_name']=="Meeting" || $row_tar['task_name']=="BTL Activity"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashactivity")?>&rheader=<?=base64_encode($row_tar['task_name'])?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_ch1["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else{?>
                    	
                    <?php }?>
                                    </td>
                                    <td style="text-align:right"><?php if($shorfall>0){ echo round($shorfall);}?></td>
                                    <td style="text-align:right"><?php if($tarval){$perc = number_format((($resp/$tarval)*100),'2','.',''); echo $perc."%";}else{ echo $perc=0;}?></td>
                                 </tr>
                            <?php
							$ach_per2 += $perc;
                            }
                            ?>
                            </tbody>
                          </table>
                          <table class="" width="100%" border="0" cellpadding="2"  cellspacing="0">
        <tr>
        <td align="center"  width="50%" style="border-right:none">
		<?php
			if($ach_per2>100){ $extsale = 10;}else{ $extsale = 0;}
			//// get emp performance
			$res_empperf = mysqli_query($link1,"SELECT * FROM emp_performance WHERE username='".$row_ch1["username"]."'");
			$row_empperf = mysqli_fetch_assoc($res_empperf);
            $perf = $ach_per2 + $extsale + $row_empperf["achive_dealer_target"] + $row_empperf["achive_dealer_meet"] + $row_empperf["achive_daily_meet"] + $row_empperf["achive_reporting"] + $row_empperf["achive_market_share"];
            $tag = getPerformace($perf);
            $bar = explode("~",getPerformaceBar($perf));
            echo $tag;
			///// make array count performance wise
			if($bar[2]=="1"){ 
				$top_perf[]=$row_ch1["name"]." | ".$row_ch1["oth_empid"]." | ".$row_ch1["username"]." (".$perf."%)";
			}else if($bar[2]=="2"){
				$med_perf[]=$row_ch1["name"]." | ".$row_ch1["oth_empid"]." | ".$row_ch1["username"]." (".$perf."%)";
			}else if($bar[2]=="3"){
				$low_perf[]=$row_ch1["name"]." | ".$row_ch1["oth_empid"]." | ".$row_ch1["username"]." (".$perf."%)";
			}else{
				$non_perf[]=$row_ch1["name"]." | ".$row_ch1["oth_empid"]." | ".$row_ch1["username"]." (".$perf."%)";
			}
            ?>
            
        </td>
        <td align="center" width="50%"  style="border-left:none">
        		<div class="progress" style="margin-bottom:0px;margin-top:5px;">
                <div class="progress-bar progress-bar-<?=$bar[0]?> progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:<?=$perf?>%" title="<?=$perf?>% Achieve">
                  <?=$perf?>% <?=$bar[1]?>
                </div>
              </div></td>
        </tr>
        </table>
                        </div>  
            			<div id="collapse-<?=$i?>-<?=$j?>" class="collapse" data-parent="#accordion-<?=$i?>" aria-labelledby="heading-<?=$i?>-<?=$j?>">
              				<div class="card-body">
                                <?php
								$res_ch2 = mysqli_query($link1,"SELECT username, name, oth_empid, designationid FROM admin_users WHERE reporting_manager='".$row_ch1["username"]."' AND status='Active'");
								if(mysqli_num_rows($res_ch2)>0){
								?>  
                  				<div id="accordion-<?=$i?>-<?=$j?>">
								<?php
                                $k=1;
								
                                while($row_ch2 = mysqli_fetch_assoc($res_ch2)){
                                ?>
                    				<div class="card">
                      					<div class="card-header bg-info" id="heading-<?=$i?>-<?=$j?>-<?=$k?>">
                                        <?php /*?><a href="#" style="float: right;" title="Export details in excel" onClick='alert("comming soon")' class="text-success"><i class="fa fa-file-excel-o fa-lg" style="color: #fff;" title="Export details in excel"></i></a><?php */?>
                        					<h5 class="mb-0">
                          						<a class="collapsed text-white" role="button" data-toggle="collapse" href="#collapse-<?=$i?>-<?=$j?>-<?=$k?>" aria-expanded="false" aria-controls="collapse-<?=$i?>-<?=$j?>-<?=$k?>">
                            					<?=$row_ch2["name"]." | ".$row_ch2["oth_empid"]." | ".$row_ch2["username"]?><br/><i>(<?php echo getAnyDetails($row_ch2["designationid"],"designame","designationid","hrms_designation_master",$link1)?>)</i>
                          						</a>
                        					</h5>
                      					</div>
                                        <div class="card-body table-responsive">
               		  					<table id="myTable2" class="dt_table" width="100%" border="0" cellpadding="2"  cellspacing="0">
                                            <thead>
                                                <tr align="center" class="alert-info">
                                                    <th width="15%"><strong>Product Sub Category</strong></th>
                                                    <th width="10%"><strong>Task Name</strong></th>
                                                    <!--<th width="15%"><strong>Remark</strong></th>-->
                                                    <th width="10%" style="text-align:right"><strong>Target</strong></th>
                                                    <th width="10%" style="text-align:right"><strong>Achievement</strong></th>
                                                    <th width="10%" style="text-align:right"><strong>Shortfall</strong></th>
                                                    <th width="15%" style="text-align:right"><strong>Achievement %</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody style="font-size: 12px !important">
                                            <?php
											$team = getTeamMembers($row_ch2["username"],$link1);
											if($team){
												$team .= $team.",'".$row_ch2["username"]."'"; 
											}else{
												$team .= "'".$row_ch2["username"]."'"; 
											}
											$ach_per3 = 0;
											$sql_tar = "SELECT SUM(target_val) AS target_val, task_name, prod_code,GROUP_CONCAT(remark) AS ps FROM sf_target_data WHERE status='Active' ".$filter_str." AND user_id IN (".$team.") group by task_name,prod_code,month,year";
											$res_tar = mysqli_query($link1, $sql_tar);
											while($row_tar = mysqli_fetch_assoc($res_tar)){
												////achive
												$resp = getAcheivement($row_tar['task_name'],$_POST['fdate'],$_POST['tdate'],$row_tar['prod_code'],$_POST['sale_type'],$team,$link1);												$tarval = 0;
												if($row_tar['task_name']=="Sale Order"){
													$p_target =0; $s_target =0; $ps_sum = 0;
													//////
													$fetch_ps = explode(",",$row_tar['ps']);
													for($a=0; $a<count($fetch_ps); $a++){
														$explod_rmk = explode(" ",$fetch_ps[$a]);
														///check selected filter
														if($_POST['sale_type']=="P" || $_POST['sale_type']==""){
															$p_target = str_replace("P-","",$explod_rmk[0]);
														}
														if($_POST['sale_type']=="S" || $_POST['sale_type']==""){
															$s_target = str_replace("S-","",$explod_rmk[1]);
														}	
														$ps_sum += $p_target + $s_target;
													}
													$tarval = $ps_sum;
												}else if($row_tar['task_name']=="Dealer Visit"){
													$p_target =0; $s_target =0; $ps_sum = 0;
													$fetch_ps = explode(",",$row_tar['ps']);
													for($a=0; $a<count($fetch_ps); $a++){
														$explod_rmk = explode(" ",$fetch_ps[$a]);
														///check selected filter
														if($_POST['sale_type']=="P" || $_POST['sale_type']==""){
															$p_target = str_replace("Old-","",$explod_rmk[0]);
														}
														if($_POST['sale_type']=="S" || $_POST['sale_type']==""){
															$s_target = str_replace("New-","",$explod_rmk[1]);
														}
														$ps_sum += $p_target + $s_target;
													}
													$tarval = $ps_sum;
												}else{
													$tarval = $row_tar['target_val'];
												}
												$shorfall = $tarval-$resp;
											?>
												<tr>
													<td><?=$row_tar['prod_code']?></td>
													<td><?=$row_tar['task_name']?></td>
													<?php /*?><td><?=$row_tar['remark']?></td><?php */?>
													<td style="text-align:right"><?=$tarval?></td>
													<td style="text-align:right"><?=round($resp)?>
                                                    <?php if($row_tar['task_name']=="Sale Order"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashsaleorder")?>&rheader=<?=base64_encode("Sales Order")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_ch2["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Dealer Visit"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashdealervisit")?>&rheader=<?=base64_encode("Dealer Visit")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_ch2["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Collection"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashcollection")?>&rheader=<?=base64_encode("Collection")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_ch2["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Feedback"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashfeedback")?>&rheader=<?=base64_encode("Feedback")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_ch2["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Dealer Activeness" || $row_tar['task_name']=="Meeting" || $row_tar['task_name']=="BTL Activity"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashactivity")?>&rheader=<?=base64_encode($row_tar['task_name'])?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_ch2["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else{?>
                    	
                    <?php }?>
                                                    </td>
													<td style="text-align:right"><?php if($shorfall>0){ echo round($shorfall);}?></td>
													<td style="text-align:right"><?php if($tarval){$perc = number_format((($resp/$tarval)*100),'2','.',''); echo $perc."%";}else{ echo $perc=0;}?></td>
												 </tr>
											<?php
											$ach_per3 += $perc;
											}
											?>	
                                            </tbody>
                                          </table>
                                          <table class="" width="100%" border="0" cellpadding="2"  cellspacing="0">
        <tr>
        <td align="center"  width="50%" style="border-right:none">
		<?php
			if($ach_per3>100){ $extsale = 10;}else{ $extsale = 0;}
			//// get emp performance
			$res_empperf = mysqli_query($link1,"SELECT * FROM emp_performance WHERE username='".$row_ch2["username"]."'");
			$row_empperf = mysqli_fetch_assoc($res_empperf);
            $perf = $ach_per3 + $extsale + $row_empperf["achive_dealer_target"] + $row_empperf["achive_dealer_meet"] + $row_empperf["achive_daily_meet"] + $row_empperf["achive_reporting"] + $row_empperf["achive_market_share"];
            $tag = getPerformace($perf);
            $bar = explode("~",getPerformaceBar($perf));
            echo $tag;
			///// make array count performance wise
			if($bar[2]=="1"){ 
				$top_perf[]=$row_ch2["name"]." | ".$row_ch2["oth_empid"]." | ".$row_ch2["username"]." (".$perf."%)";
			}else if($bar[2]=="2"){
				$med_perf[]=$row_ch2["name"]." | ".$row_ch2["oth_empid"]." | ".$row_ch2["username"]." (".$perf."%)";
			}else if($bar[2]=="3"){
				$low_perf[]=$row_ch2["name"]." | ".$row_ch2["oth_empid"]." | ".$row_ch2["username"]." (".$perf."%)";
			}else{
				$non_perf[]=$row_ch2["name"]." | ".$row_ch2["oth_empid"]." | ".$row_ch2["username"]." (".$perf."%)";
			}
            ?>
            
        </td>
        <td align="center" width="50%"  style="border-left:none">
        		<div class="progress" style="margin-bottom:0px;margin-top:5px;">
                <div class="progress-bar progress-bar-<?=$bar[0]?> progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:<?=$perf?>%" title="<?=$perf?>% Achieve">
                  <?=$perf?>% <?=$bar[1]?>
                </div>
              </div></td>
        </tr>
        </table>      
                      					</div>	
                      					<div id="collapse-<?=$i?>-<?=$j?>-<?=$k?>" class="collapse" data-parent="#accordion-<?=$i?>" aria-labelledby="heading-<?=$i?>-<?=$j?>-<?=$k?>">
                          					<div class="card-body">
                                                <?php
												$res_ch3 = mysqli_query($link1,"SELECT username, name, oth_empid, designationid FROM admin_users WHERE reporting_manager='".$row_ch2["username"]."' AND status='Active'");
												if(mysqli_num_rows($res_ch3)>0){
												?>  
                              					<div id="accordion-<?=$i?>-<?=$j?>-<?=$k?>">
												<?php
                                                $l=1;
                                                while($row_ch3 = mysqli_fetch_assoc($res_ch3)){
                                                ?>
                                					<div class="card">
                                  						<div class="card-header bg-warning" id="heading-<?=$i?>-<?=$j?>-<?=$k?>-<?=$l?>">
                                                        <?php /*?><a href="#" style="float: right;" title="Export details in excel" onClick='alert("comming soon")' class="text-success"><i class="fa fa-file-excel-o fa-lg" style="color: #fff;" title="Export details in excel"></i></a><?php */?>
                                    						<h5 class="mb-0">
                                      						<a class="collapsed text-white" role="button" data-toggle="collapse" href="#collapse-<?=$i?>-<?=$j?>-<?=$k?>-<?=$l?>" aria-expanded="false" aria-controls="collapse-<?=$i?>-<?=$j?>-<?=$k?>-<?=$l?>">
                                        <?=$row_ch3["name"]." | ".$row_ch3["oth_empid"]." | ".$row_ch3["username"]?><br/><i>(<?php echo getAnyDetails($row_ch3["designationid"],"designame","designationid","hrms_designation_master",$link1)?>)</i>
                                      						</a>
                                    						</h5>
                                  						</div>
                                  						<div id="collapse-<?=$i?>-<?=$j?>-<?=$k?>-<?=$l?>" class="collapse" data-parent="#accordion-<?=$i?>-<?=$j?>-<?=$k?>" aria-labelledby="heading-<?=$i?>-<?=$j?>-<?=$k?>-<?=$l?>">
                                    					<div class="card-body table-responsive">
                                      <table id="myTable2" class="dt_table" width="100%" border="0" cellpadding="2"  cellspacing="0">
                                        <thead>
                                            <tr align="center" class="alert-warning">
                                                <th width="15%"><strong>Product Sub Category</strong></th>
                                                <th width="10%"><strong>Task Name</strong></th>
                                                <!--<th width="15%"><strong>Remark</strong></th>-->
                                                <th width="10%" style="text-align:right"><strong>Target</strong></th>
                                                <th width="10%" style="text-align:right"><strong>Achievement</strong></th>
                                                <th width="10%" style="text-align:right"><strong>Shortfall</strong></th>
                                                <th width="15%" style="text-align:right"><strong>Achievement %</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 12px !important">
                                        <?php
											$team = getTeamMembers($row_ch3["username"],$link1);
											if($team){
												$team .= $team.",'".$row_ch3["username"]."'"; 
											}else{
												$team .= "'".$row_ch3["username"]."'"; 
											}
											$ach_per4 = 0;
											$sql_tar = "SELECT SUM(target_val) AS target_val, task_name, prod_code,GROUP_CONCAT(remark) AS ps FROM sf_target_data WHERE status='Active' ".$filter_str." AND user_id IN (".$team.") group by task_name,prod_code,month,year";
											$res_tar = mysqli_query($link1, $sql_tar);
											while($row_tar = mysqli_fetch_assoc($res_tar)){
												////achive
												$resp = getAcheivement($row_tar['task_name'],$_POST['fdate'],$_POST['tdate'],$row_tar['prod_code'],$_POST['sale_type'],$team,$link1);
												$tarval = 0;
												if($row_tar['task_name']=="Sale Order"){
													$p_target =0; $s_target =0; $ps_sum = 0;
													//////
													$fetch_ps = explode(",",$row_tar['ps']);
													for($a=0; $a<count($fetch_ps); $a++){
														$explod_rmk = explode(" ",$fetch_ps[$a]);
														///check selected filter
														if($_POST['sale_type']=="P" || $_POST['sale_type']==""){
															$p_target = str_replace("P-","",$explod_rmk[0]);
														}
														if($_POST['sale_type']=="S" || $_POST['sale_type']==""){
															$s_target = str_replace("S-","",$explod_rmk[1]);
														}	
														$ps_sum += $p_target + $s_target;
													}
													$tarval = $ps_sum;
												}else if($row_tar['task_name']=="Dealer Visit"){
													$p_target =0; $s_target =0; $ps_sum = 0;
													$fetch_ps = explode(",",$row_tar['ps']);
													for($a=0; $a<count($fetch_ps); $a++){
														$explod_rmk = explode(" ",$fetch_ps[$a]);
														///check selected filter
														if($_POST['sale_type']=="P" || $_POST['sale_type']==""){
															$p_target = str_replace("Old-","",$explod_rmk[0]);
														}
														if($_POST['sale_type']=="S" || $_POST['sale_type']==""){
															$s_target = str_replace("New-","",$explod_rmk[1]);
														}
														$ps_sum += $p_target + $s_target;
													}
													$tarval = $ps_sum;
												}else{
													$tarval = $row_tar['target_val'];
												}
												$shorfall = $tarval-$resp;
											?>
												<tr>
													<td><?=$row_tar['prod_code']?></td>
													<td><?=$row_tar['task_name']?></td>
													<?php /*?><td><?=$row_tar['remark']?></td><?php */?>
													<td style="text-align:right"><?=$tarval?></td>
													<td style="text-align:right"><?=round($resp)?>
                                                    <?php if($row_tar['task_name']=="Sale Order"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashsaleorder")?>&rheader=<?=base64_encode("Sales Order")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_ch3["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Dealer Visit"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashdealervisit")?>&rheader=<?=base64_encode("Dealer Visit")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_ch3["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Collection"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashcollection")?>&rheader=<?=base64_encode("Collection")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_ch3["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Feedback"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashfeedback")?>&rheader=<?=base64_encode("Feedback")?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_ch3["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else if($row_tar['task_name']=="Dealer Activeness" || $row_tar['task_name']=="Meeting" || $row_tar['task_name']=="BTL Activity"){?>
                    <a href="../admin/excelexport.php?rname=<?=base64_encode("masterdashactivity")?>&rheader=<?=base64_encode($row_tar['task_name'])?>&fdate=<?=base64_encode($_POST['fdate'])?>&tdate=<?=base64_encode($_POST['tdate'])?>&userid=<?=base64_encode($row_ch3["username"])?>&psc=<?=base64_encode($row_tar['prod_code'])?>" style="float: left;" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-lg" title="Export details in excel"></i></a>
                    <?php }else{?>
                    	
                    <?php }?>
                                                    </td>
													<td style="text-align:right"><?php if($shorfall>0){ echo round($shorfall);}?></td>
													<td style="text-align:right"><?php if($tarval){$perc = number_format((($resp/$tarval)*100),'2','.',''); echo $perc."%";}else{ echo $perc="NA";}?></td>
												 </tr>
											<?php
											$ach_per4 += $perc;
											}
											?>
                                        </tbody>
                                      </table>
                                      <table class="" width="100%" border="0" cellpadding="2"  cellspacing="0">
        <tr>
        <td align="center"  width="50%" style="border-right:none">
		<?php
			if($ach_per4>100){ $extsale = 10;}else{ $extsale = 0;}
			//// get emp performance
			$res_empperf = mysqli_query($link1,"SELECT * FROM emp_performance WHERE username='".$row_ch3["username"]."'");
			$row_empperf = mysqli_fetch_assoc($res_empperf);
            $perf = $ach_per4 + $extsale + $row_empperf["achive_dealer_target"] + $row_empperf["achive_dealer_meet"] + $row_empperf["achive_daily_meet"] + $row_empperf["achive_reporting"] + $row_empperf["achive_market_share"];
            $tag = getPerformace($perf);
            $bar = explode("~",getPerformaceBar($perf));
            echo $tag;
			///// make array count performance wise
			if($bar[2]=="1"){ 
				$top_perf[]=$row_ch3["name"]." | ".$row_ch3["oth_empid"]." | ".$row_ch3["username"]." (".$perf."%)";
			}else if($bar[2]=="2"){
				$med_perf[]=$row_ch3["name"]." | ".$row_ch3["oth_empid"]." | ".$row_ch3["username"]." (".$perf."%)";
			}else if($bar[2]=="3"){
				$low_perf[]=$row_ch3["name"]." | ".$row_ch3["oth_empid"]." | ".$row_ch3["username"]." (".$perf."%)";
			}else{
				$non_perf[]=$row_ch3["name"]." | ".$row_ch3["oth_empid"]." | ".$row_ch3["username"]." (".$perf."%)";
			}
            ?>
            
        </td>
        <td align="center" width="50%"  style="border-left:none">
        		<div class="progress" style="margin-bottom:0px;margin-top:5px;">
                <div class="progress-bar progress-bar-<?=$bar[0]?> progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:<?=$perf?>%" title="<?=$perf?>% Achieve">
                  <?=$perf?>% <?=$bar[1]?>
                </div>
              </div></td>
        </tr>
        </table>
                                    					</div>
                                  						</div>
                                					</div>
                                <?php $l++;}?>
                          
                       	 						</div>
                                                <?php }?>
                      						</div>
                    					</div>
                    					
                    				</div>
									<?php
                                        $k++;
                                    }
                                    ?>
              					</div>
                                <?php }?>
            				</div>
          				</div>
        			</div>      
      			<?php $j++;}?>
      			</div>
                <?php }?>
    		</div>
  		</div>
        <div class="table-responsive">
        <table id="myTable2" class="dt_table" width="100%" border="0" cellpadding="2"  cellspacing="0">
            <thead>
                <tr align="center">
                    <th width="25%" height="20" class="alert-success"><strong>Top Performer</strong></th>
                    <th width="25%" class="alert-info"><strong>Inter Mediate</strong></th>
                    <th width="25%" class="alert-warning"><strong>Low Performer</strong></th>
                    <th width="25%" class="alert-danger"><strong>Non Performer</strong></th>
                </tr>
            </thead>
            <tbody style="font-size: 12px !important">
            	<?php
				////// get max array
				$arr_perfor = max($top_perf,$med_perf,$low_perf,$non_perf);
				for($p=0; $p<count($arr_perfor); $p++){
				?>
                <tr>
                    <td><?=$top_perf[$p]?></td>
                    <td><?=$med_perf[$p]?></td>
                    <td><?=$low_perf[$p]?></td>
                    <td><?=$non_perf[$p]?></td>
                </tr>    
                <?php 
				}
				?>
            </tbody>
    	</table>
        </div>
  	</div>
</div>
<?php }?>                
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