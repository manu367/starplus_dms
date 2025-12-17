<?php
require_once("../config/config.php");
$reqid = base64_decode($_REQUEST['id']);
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
    <link rel="stylesheet" href="../css/timeline.css">
</head>
<body>
	<div class="container-fluid">
   		<div class="row content">
            <?php
            include("../includes/leftnav2.php");
            ?>
            <div class="col-sm-9 tab-pane fade in active" id="home">
            	<h2 align="center"><i class="fa fa-users"></i> User Work Activities</h2>
              	<form class="form-horizontal" role="form" name="form1" action="" method="post">
                	<div class="row">
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">From Date</label>
                            <input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>">
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
                            <input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>">
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">&nbsp;</label><br/>
                        	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                            <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                        </div>
                      </div>
              		</form>
<?php 
if($reqid){
	$res_timeline = mysqli_query($link1,"SELECT * FROM daily_activities WHERE userid ='".$reqid."' AND update_date >= '".$_REQUEST["fdate"]."' AND update_date <= '".$_REQUEST["tdate"]."' ORDER BY id DESC")or die("ER1 ".mysqli_error($link1));
	$a = array();
	$b = array();
	while($row_timeline = mysqli_fetch_array($res_timeline)){
		$b["taskname"] = $row_timeline["activity_type"];
		$b["taskaction"] = $row_timeline["action_taken"];
		$b["refno"] = $row_timeline["ref_no"];
		$b["updatedate"] = $row_timeline["update_on"];
		$a[$row_timeline["update_date"]][] = $b;
	}
	//echo "<pre>";
	//print_r($a);
	///// make task name icon array
	$task_icon = array(	
		"ACCOUNT" => "fa-sitemap",
		"Account Group" => "fa-object-group",
		"Account Head" => "fa-bandcamp",
		"Account Ledger" => "fa-balance-scale",
		"Account Voucher" => "fa-building",
		"ADMIN USER" => "fa-users",
		"CITY" => "fa-map-marker",
		"Combo" => "fa-cubes",
		"COMBO INVOICE" => "fa-book",
		"CORPORATE INVOICE" => "fa-book",
		"CUSTOMER" => "fa-user-o",
		"DEPARTMENT" => "fa-building-o",
		"DESIGNATION" => "fa-vcard-o",
		"GRN" => "fa-ship",
		"IMEI ATTACH" => "fa-upload",
		"LEAD" => "fa-child",
		"Ledger Extension" => "fa-sitemap",
		"LOCATION" => "fa-bank",
		"LP" => "fa-ship",
		"MAPPING" => "fa-map-signs",
		"OPS" => "fa-cubes",
		"PO" => "fa-shopping-basket",
		"PO APPROVAL" => "fa-address-card",
		"PRICE" => "fa-inr",
		"PRODUCT" => "fa-cube",
		"PRODUCT SUB CAT" => "fa-cog",
		"QUOTE" => "fa-quora",
		"RETAIL INVOICE" => "fa-book",
		"RP" => "fa-pencil-square-o",
		"Scheme" => "fa-tags",
		"SEGMENT" => "fa-suitcase",
		"Serial ATTACH" => "fa-upload",
		"Serial No. ATTACH" => "fa-upload",
		"Serial Nos. ATTACH" => "fa-upload",
		"SO" => "fa-shopping-basket",
		"STATE" => "fa-map-marker",
		"STN" => "fa-truck",
		"STN Delivery Challan" => "fa-truck",
		"STN Distribution" => "fa-truck",
		"STN INVOICE" => "fa-truck",
		"STOCK MOVEMENT" => "fa-truck",
		"Stock Transfer APPROVAL" => "fa-truck",
		"SUB DEPARTMENT" => "fa-building-o",
		"SUB-LOCATION" => "fa-bank",
		"TA DA APPROVAL" => "fa-address-card",
		"TAX" => "fa-percent",
		"VPO" => "fa-ship"
		);
	$task_css = array(		
		"ACCOUNT" => "success",
		"Account Group" => "warning",
		"Account Head" => "info",
		"Account Ledger" => "danger",
		"Account Voucher" => "success",
		"ADMIN USER" => "warning",
		"CITY" => "info",
		"Combo" => "danger",
		"COMBO INVOICE" => "success",
		"CORPORATE INVOICE" => "success",
		"CUSTOMER" => "warning",
		"DEPARTMENT" => "info",
		"DESIGNATION" => "danger",
		"GRN" => "success",
		"IMEI ATTACH" => "success",
		"LEAD" => "warning",
		"Ledger Extension" => "info",
		"LOCATION" => "danger",
		"LP" => "success",
		"MAPPING" => "success",
		"OPS" => "warning",
		"PO" => "info",
		"PO APPROVAL" => "danger",
		"PRICE" => "success",
		"PRODUCT" => "success",
		"PRODUCT SUB CAT" => "warning",
		"QUOTE" => "info",
		"RETAIL INVOICE" => "danger",
		"RP" => "success",
		"Scheme" => "success",
		"SEGMENT" => "warning",
		"Serial ATTACH" => "info",
		"Serial No. ATTACH" => "danger",
		"Serial Nos. ATTACH" => "success",
		"SO" => "success",
		"STATE" => "warning",
		"STN" => "info",
		"STN Delivery Challan" => "danger",
		"STN Distribution" => "success",
		"STN INVOICE" => "success",
		"STOCK MOVEMENT" => "warning",
		"Stock Transfer APPROVAL" => "info",
		"SUB DEPARTMENT" => "danger",
		"SUB-LOCATION" => "success",
		"TA DA APPROVAL" => "success",
		"TAX" => "warning",
		"VPO" => "info"
		);
?>
<div class="">
    <!-- Page header -->
    <div class="page-header">
        <h2>Activities <small>of <?=$reqid?></small></h2>
    </div>
    <!-- /Page header -->
    <!-- Timeline -->
    <div class="timeline">
        <!-- Line component -->
        <div class="line text-muted"></div>
		<?php 
		foreach($a as $time => $val){
		?>
        <!-- Separator -->
        <div class="separator text-muted">
            <time><?=date("j F, Y", strtotime($time))?></time>
        </div>
        <!-- /Separator -->
        <?php 
		for($j=0;$j<count($val);$j++){ 
		?>
        <!-- Panel -->
        <article class="panel panel-<?=$task_css[$val[$j]["taskname"]]?>">
            <!-- Icon -->
            <div class="panel-heading icon">
                <i class="fa <?=$task_icon[$val[$j]["taskname"]]?>"></i>
            </div>
            <!-- /Icon -->
            <!-- Heading -->
            <div class="panel-heading">
                <h2 class="panel-title"><?=$val[$j]["taskname"]."  -> ".$val[$j]["taskaction"]?></h2>
            </div>
            <!-- /Heading -->
    
            <!-- Body -->
            <!--<div class="panel-body">
                
            </div>-->
            <!-- /Body -->
            <!-- List group -->
            <!--<ul class="list-group">
                <li class="list-group-item">Like</li>
                <li class="list-group-item">list</li>
                <li class="list-group-item">groups</li>
                <li class="list-group-item">and</li>
                <li class="list-group-item">tables</li>
            </ul>-->
    		<!-- Footer -->
            <div class="panel-footer">
                <small>Updated at&nbsp;&nbsp;&nbsp;<?=date("j F, Y, g:i a", strtotime($val[$j]["updatedate"]))?></small>
            </div>
            <!-- /Footer -->
        </article>
        <!-- /Panel -->
        <?php }?>
		<?php }?>    
        <!-- Panel -->
        <article class="panel panel-info panel-outline">
    
            <!-- Icon -->
            <div class="panel-heading icon">
                <i class="fa fa-info-circle" aria-hidden="true"></i>
            </div>
            <!-- /Icon -->
    
            <!-- Body -->
            <div class="panel-body">
                That is all.
            </div>
            <!-- /Body -->
    
        </article>
        <!-- /Panel -->
    
    </div>
    <!-- /Timeline -->

</div>
</div>
<?php }?>
 <div class="form-group">
    <div class="col-md-12" style="text-align:center;" > 
       <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='emp_history.php?empcode=<?=base64_encode($reqid);?><?=$pagenav?>'">
    </div>  
</div>           	</div>
      		</div>
		</div>
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>