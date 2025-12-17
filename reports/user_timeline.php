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
            	<h2 align="center"><i class="fa fa-users"></i> User Time Line</h2>
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
	$res_timeline = mysqli_query($link1,"SELECT * FROM user_track WHERE userid ='".$_REQUEST["username"]."' AND entry_date >= '".$_REQUEST["fdate"]."' AND entry_date <= '".$_REQUEST["tdate"]."' ORDER BY id DESC")or die("ER1 ".mysqli_error($link1));
	$a = array();
	$b = array();
	while($row_timeline = mysqli_fetch_array($res_timeline)){
		$b["taskname"] = $row_timeline["task_name"];
		$b["taskaction"] = $row_timeline["task_action"];
		$b["latitude"] = $row_timeline["latitude"];
		$b["longitude"] = $row_timeline["longitude"];
		$b["address"] = $row_timeline["address"];
		$b["travel_km"] = $row_timeline["travel_km"];
		$b["updatedate"] = ($row_timeline["update_date"]);
		$a[$row_timeline["entry_date"]][] = $b;
	}
	//echo "<pre>";
	//print_r($a);
	///// make task name icon array
	$task_icon = array("Collection" => "fa-money","Dealer Visit Old" => "fa-address-card","Deviation" => "fa-line-chart","Feedback" => "fa-pencil-square-o","Lead" => "fa-users","New Dealer" => "fa-user-plus","Sales Order" => "fa-shopping-basket","TADA" => "fa-book","User Attendance" => "fa-street-view","User Profile" => "fa-id-badge","User Profile Pic" => "fa-picture-o","Ticket" => "fa-ticket","Follow-up" => "fa-headphones");
	$task_css = array("Collection" => "success","Dealer Visit Old" => "warning","Deviation" => "danger","Feedback" => "info","Lead" => "success","New Dealer" => "info","Sales Order" => "warning","TADA" => "danger","User Attendance" => "success","User Profile" => "warning","User Profile Pic" => "success","Ticket" => "success","Follow-up" => "success");
?>
<div class="">
    <!-- Page header -->
    <div class="page-header">
        <h2>Timeline <small>of <?=$_REQUEST["username"]?></small></h2>
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
			$cordinate ="'".$val[$j]["latitude"].", ".$val[$j]["longitude"]."'";
			$center_loc = $val[$j]["latitude"].", ".$val[$j]["longitude"];
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
            <div class="panel-body">
                <div style="float:left; display:inline-block"><?=$val[$j]["address"]."<i>(<b>Latitude:</b> ".$val[$j]["latitude"].", <b>Longitude:</b> ".$val[$j]["longitude"].")</i>"?></div>
                <div style="float:right; display:inline-block"><a href="https://www.google.com/maps/dir/<?=$cordinate?>/@<?=$center_loc?>,13z" target="_blank" class="btn <?=$btncolor?>" title="check on google map"><i class="fa fa-map-marker" title="check on google map"></i></a></div>
            </div>
            <!-- /Body -->
    		<!-- Footer -->
            <div class="panel-footer">
                <small>Updated at&nbsp;&nbsp;&nbsp;<?=date("j F, Y, g:i a", strtotime($val[$j]["updatedate"]))?></small>
            </div>
            <!-- /Footer -->
            <!-- List group -->
            <!--<ul class="list-group">
                <li class="list-group-item">Like</li>
                <li class="list-group-item">list</li>
                <li class="list-group-item">groups</li>
                <li class="list-group-item">and</li>
                <li class="list-group-item">tables</li>
            </ul>-->
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
            	</div>
      		</div>
		</div>
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>