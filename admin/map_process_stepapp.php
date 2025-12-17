<?php 
////// Function ID ///////
$fun_id = array("a"=>array(103)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
//////////////////////////
$rid = base64_decode($_REQUEST['id']);
$processname = base64_decode($_REQUEST['refname']);
@extract($_POST);
############# if form 2 is submitted #################
if(isset($_POST['submitTab3'])){
	// Update work Rights
	$rrr="skill";
	if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM process_approval_step WHERE id='".$rid."'"))>0){
		mysqli_query($link1,"UPDATE process_approval_step SET approval_steps='".implode(',',$_REQUEST[$rrr])."', update_by='".$_SESSION['userid']."', update_date='".$datetime."' WHERE id='".$rid."'")or die(mysqli_error($link1));
	}else{
		mysqli_query($link1,"INSERT INTO process_approval_step SET process_name='".$processname."', approval_steps='".implode(',',$_REQUEST[$rrr])."', status='1', entry_by='".$_SESSION['userid']."', entry_date='".$datetime."'")or die(mysqli_error($link1));
	}
	dailyActivity($_SESSION['userid'], $processname, "APPROVAL STEPS", "UPDATE", $ip, $link1, "");
}
else{

}
///// get user basic details
$res_user = mysqli_query($link1,"SELECT approval_steps FROM process_approval_step WHERE process_name='".$processname."'");
$row_user = mysqli_fetch_assoc($res_user);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title><?=siteTitle?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <!-- Bootstrap 4 -->
    <link href="../css/5.1.3_dist_css_bootstrap.min.css" rel="stylesheet">
    <link href="../css/dd.css" rel="stylesheet" />
    <script src="../js/jquery-3.3.1.min.js"></script>
    <script src="../js/jquery-ui.min.js"></script>

 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <link href="../css/abc2.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid">
	<div class="row content">
    <?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>" style="margin-left:20%">
    	<h2 align="center"><i class="fa sitemap"></i> Map Approval Sequence</h2>
      	<h4 align="center">
        	<?=$processname;?>
        	<?php if(isset($_POST['submitTab3'])=='Save'){ ?>
        <br/>
        <span style="color:#FF0000">Approval sequences are mapped.</span>
        <?php } ?>
      </h4>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">

        <div class="row m-0" id="add_new_view_row">
            <div class="col-md-4 border-right sortable_left">
                <input type="text" class="form-control mb-0" placeholder="Filter menu here..." id="myInput" onKeyUp="myFunction_dashboard()" />
                <ul id="sortable1" class="pl-0 menu_list js-sortable-group js-drop-target bg-light p-3">
                	<?php
					$assign_skill = explode(",",$row_user['approval_steps']);
					$j=1;
					$res_workcat = mysqli_query($link1,"SELECT * FROM approval_step_master WHERE status='1' ORDER BY process_id");
					while($row_workcat=mysqli_fetch_assoc($res_workcat)){
						//// check access skill
						if(in_array($row_workcat['process_id'], $assign_skill)){ $numacc_skill=1;
						
						}else{
					?>
                    <li class="ui-state-default">
                        <div id="r_<?=$j?>" class="p-2 alert alert-secondary fade show alert_box course d-block" optiondata="<?=$row_workcat['process_name']?>">
                            <i class="fas fa-grip-vertical d_icon"></i><?=$row_workcat['process_name']?><input type="hidden" id="skill" name="skill[]" value="<?=$row_workcat['process_id']?>"/>
                        </div>
                    </li>
					<?php  
						$j++;
						}
					}?>
                </ul>
            </div>

          <div class="col-md-4 sortable_right">
            	<form method="post" action="">
                <input type="text" class="form-control mb-0" placeholder="Filter menu here..." id="myInput2" onKeyUp="myFunction_dashboard2()" />
                <ul id="sortable2" class="pl-0 menu_list js-sortable-group js-drop-target bg-light p-3">
                	<?php
					$assign_skill = explode(",",$row_user['approval_steps']);
					for($k=0; $k<count($assign_skill); $k++){
						if($assign_skill[$k]){
						$res_1 = mysqli_query($link1,"SELECT process_id,process_name FROM approval_step_master WHERE process_id='".$assign_skill[$k]."' AND status='1'");
						$row_1=mysqli_fetch_assoc($res_1);
					?>
                    <li class="ui-state-default">
                        <div id="r_<?=($k+1)?>" class="p-2 alert alert-secondary fade show alert_box course d-block" optiondata="<?=$row_workcat['process_name']?>">
                            <i class="fas fa-grip-vertical d_icon"></i><?=$row_1['process_name']?><input type="hidden" id="skill" name="skill[]" value="<?=$row_1['process_id']?>"/></div>
                    </li>
                    <?php  
						}
					}
					?>
                </ul>
                <div class="form-buttons" align="center">
                <button class='btn<?=$btncolor?>' id="submitTab3" type="submit" name="submitTab3" value="Save"><i class="fa fa-save fa-lg"></i>&nbsp;&nbsp;Save</button>
                <button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='process_approval.php?<?=$pagenav?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
                </div>
                </form>
            </div>
        </div>





</div>
        </div>
      </div>
    </div>
    <script src="../js/custom-js.js"></script>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>

</html>