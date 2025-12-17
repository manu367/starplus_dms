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
//////End filters value/////
/////////////////////////
/*function getLast12Months(){
	$a = array();
	for ($i = 0; $i < 12; $i++) 
	{
	   $months[] = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));
	   $mnthname[] = date("Y-M", strtotime( date( 'Y-m-01' )." -$i months"));
	}
	$a[] = $months;
	$a[] = $mnthname;
	return $a;
}
$last_12month = getLast12Months();
////// make last 12 month string for sale trend location wise
$last_12monthstr = "'".implode("','",$last_12month[1])."'";*/
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.js"></script>
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
	$('#myTable2').dataTable({
		searching: false,
		paging: false,
		info: false,
		ordering: false
	});
	$('.selectpicker').selectpicker({
		width : "300px"
	});
});	
 </script>
 <style type="text/css">
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

 </head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
     <h2 align="center"><i class="fa fa-bullseye"></i> Target Dashboard</h2>
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
                <select name="selmonth" id="selmonth" class="form-control required" onChange="document.form1.submit();" required>
                	<option value="">--Please Select--</option>
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
							$sql = mysqli_query($link1, "SELECT username,name,oth_empid FROM admin_users where 1 order by name");
						}else{
							$sql = mysqli_query($link1, "SELECT username,name,oth_empid FROM admin_users where 1 AND (reporting_manager='".$_SESSION["userid"]."' OR username='".$_SESSION["userid"]."') order by name");
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
                <a href="../reports/excelexport.php?rname=<?= base64_encode("targetReport") ?>&rheader=<?= base64_encode("Target Report") ?>&user_id=<?= base64_encode($_REQUEST['user_id']) ?>&selyear=<?= base64_encode($_REQUEST['selyear']) ?>&selmonth=<?= base64_encode($_REQUEST['selmonth']) ?>" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-2x" title="Export details in excel"></i></a>
            </div>
          </div>
	  </form>
      <br/>
      <div class="form-group" id="page-wrap" style="margin-left:10px;">
        <div class="row">
            <div class="col-md-12">
           	  <table id="myTable2" width="100%" border="0" cellpadding="2"  cellspacing="0">
    				<thead>
                    	<tr align="center" class="<?=$tableheadcolor?>">
                    	  <th height="20" colspan="8" style="text-align:center">Target Vs Achievement</th>
                   	  	</tr>
                    	<tr align="center" class="<?=$tableheadcolor?>">
                            <th width="15%" height="20"><strong>Employee</strong></th>
                            <th width="15%"><strong>Product Sub Category</strong></th>
                            <th width="10%"><strong>Task Name</strong></th>
                            <th width="20%"><strong>Remark</strong></th>
      						<th width="10%" style="text-align:right"><strong>Target</strong></th>
                            <th width="15%" style="text-align:right"><strong>Achievement</strong></th>
                            <th width="15%" style="text-align:right"><strong>Achievement %</strong></th>
    					</tr>
                  	</thead>
                    <?php if($_REQUEST['selmonth']!="" && $_REQUEST['selyear']!=""){ ?>
                  	<tbody>
                    	<?php
						
						if($_SESSION["userid"]=="admin"){
							$invcnt_res = mysqli_query($link1,"SELECT * FROM sf_target_data WHERE 1 ".$filter_str);
						}else{
							$invcnt_res = mysqli_query($link1,"SELECT * FROM sf_target_data WHERE 1 ".$filter_str." AND (user_id IN (SELECT username FROM admin_users WHERE reporting_manager='".$_SESSION["userid"]."') OR user_id='".$_SESSION["userid"]."')");
						}
						while($row_data = mysqli_fetch_assoc($invcnt_res)){
							$month_name = date("F", mktime(0, 0, 0, $row_data["month"], 10));
						?>
                    	<tr>
                            <td height="20"><?=getAnyDetails($row_data['user_id'],'name','username','admin_users',$link1)." | ".$row_data['emp_id'];?></td>
                            <td><?=$row_data["prod_code"]?></td>
                            <td><?=$row_data["task_name"]?></td>
                            <td><?=$row_data["remark"]?></td>
                            <td align="right"><?=$row_data["target_val"]?></td>
                            <td align="right"><?php
				$ach = 0;
				if($row_data['task_name']=="Dealer Visit"){
					//// calculate from old dealer vist
					$row_oldcnt =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS olddealer FROM dealer_visit WHERE userid='".$row_data["user_id"]."' AND MONTH(visit_date) = '".$row_data["month"]."' AND YEAR(visit_date) = '".$row_data["year"]."' AND dealer_type='Old'"));
					//// calculate from new dealer vist
					$row_newcnt =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS newdealer FROM dealer_visit WHERE userid='".$row_data["user_id"]."' AND MONTH(visit_date) = '".$row_data["month"]."' AND YEAR(visit_date) = '".$row_data["year"]."' AND dealer_type='New'"));
					echo "Old Visit -> ".$row_oldcnt["olddealer"];
					echo "<br>";
					echo "New Visit -> ".$row_newcnt["newdealer"];
					$ach = $row_oldcnt["olddealer"]+$row_newcnt["newdealer"];
				}
				else if($row_data['task_name']=="Feedback"){
					/// get feedback count
					$row_fb =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS feedback FROM query_master WHERE entry_by='".$row_data["user_id"]."' AND MONTH(entry_date) = '".$row_data["month"]."' AND YEAR(entry_date) = '".$row_data["year"]."'"));
					if($row_fb["feedback"]){ echo $ach = $row_fb["feedback"];}else{echo $ach = 0;}
					
				}
				else if($row_data['task_name']=="Sale Order"){
					/// get sale order count
					$row_so =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT SUM(req_qty) AS socnt FROM purchase_order_data WHERE po_no IN (SELECT po_no FROM purchase_order_master WHERE  create_by='".$row_data["user_id"]."' AND MONTH(entry_date) = '".$row_data["month"]."' AND YEAR(entry_date) = '".$row_data["year"]."' AND status IN ('Approved','Processed')) AND prod_code IN (SELECT productcode FROM product_master WHERE productsubcat IN (SELECT psubcatid FROM product_sub_category WHERE prod_sub_cat='".$row_data["prod_code"]."'))"));
					if($row_so["socnt"]){ echo $ach = $row_so["socnt"];}else{echo $ach = 0;}
				}
				else if($row_data['task_name']=="Collection"){
					/// get collection count
					$row_col =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT SUM(amount) AS collection FROM party_collection WHERE user_id='".$row_data["user_id"]."' AND MONTH(entry_date) = '".$row_data["month"]."' AND YEAR(entry_date) = '".$row_data["year"]."'"));
					if($row_col["collection"]){ echo $ach = $row_col["collection"];}else{echo $ach = 0;}	
				}
				else{
					echo $ach = 0;
				}
				
				?> </td>
    				  		<td align="right"><?php $per = round(($ach/$row_data['target_val'])*100); echo $per."%";
							if($per > 75){
								$cls = "success";
								$txt = "";
							}else if($per > 50 && $per <= 75){
								$cls = "info";
								$txt = "";
							}else if($per > 25 && $per <= 50){
								$cls = "warning";
								$txt = "";
							}else{
								$cls = "danger";
								$txt = "";
							}
							?><br/>
                            <div class="progress">
                                <div class="progress-bar progress-bar-<?=$cls?> progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:<?=$per?>%" title="<?=$per?>% Achieve">
                                  <?=$per?>% <?=$txt?>
                                </div>
                              </div>
                            </td>
  				  		</tr>
                        <?php 
						}
						?>
                  	</tbody>
                    <?php }?>
  				</table>
            </div>
        </div>


      </div><!--End form group-->
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>