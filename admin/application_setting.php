<?php
//////////////////////////
require_once("../config/config.php");
//// get existing setting details
$res = mysqli_query($link1,"SELECT * FROM app_config")or die("error1".mysqli_error($link1));
$row = mysqli_fetch_assoc($res);
//////
if($_POST["appset"]=="updsetting"){
	if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM app_config WHERE id ='".$row['id']."'"))>0){
		$res = mysqli_query($link1,"UPDATE app_config SET user_limit='".$_POST['userlimit']."', start_date='".$_POST['startdate']."', expiry_date='".$_POST['expdate']."', renewal_date='".$_POST['renewdate']."', plan_name='".$_POST['plan']."', module_name='".implode(",",$_POST['module'])."', status='".$_POST['status']."' WHERE id ='".$row['id']."'");
	}else{
		$res = mysqli_query($link1,"INSERT INTO app_config SET user_limit='".$_POST['userlimit']."', start_date='".$_POST['startdate']."', expiry_date='".$_POST['expdate']."', renewal_date='".$_POST['renewdate']."', plan_name='".$_POST['plan']."', module_name='".implode(",",$_POST['module'])."', status='".$_POST['status']."'");
	}
	if(!$res){
		$msg = "Something went wrong. Error Code1: ".mysqli_error($link1);
		$cflag="danger";
		$cmsg = "Failed";
	}else{
		$msg = "Application settings are successfully updated.";
		$cflag="success";
		$cmsg = "Success";
	}
	///// move to parent page
	header("location:application_setting.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
}
///// for multi select autoselected if any
$array_module = array();
$arr_module = explode(",",$row["module_name"]);
for($i=0; $i<count($arr_module); $i++){
	$array_module[$arr_module[$i]] = "Y";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>APP SETTINGS</title>
  	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<script src="../js/jquery.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/bootstrap.min.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
 	<link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/datepicker.css">
	<script src="../js/bootstrap-datepicker.js"></script>
    <script>
	$(document).ready(function(){
    	$("#frm1").validate();

		$('.multiselect-ui').multiselect({
			includeSelectAllOption: false,
			//buttonWidth:"523",
			enableFiltering: true,
			enableCaseInsensitiveFiltering: true,
			maxHeight: 300
		});

		$('#startdate').datepicker({
			format: "yyyy-mm-dd",
			//startDate: "<?//=$today?>",
			endDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true
		});
		$('#expdate').datepicker({
			format: "yyyy-mm-dd",
			startDate: "<?=$today?>",
			//endDate: "<?//=$today?>",
			todayHighlight: true,
			autoclose: true
		});
		$('#renewdate').datepicker({
			format: "yyyy-mm-dd",
			startDate: "<?=$today?>",
			//endDate: "<?//=$today?>",
			todayHighlight: true,
			autoclose: true
		});
	});
	</script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css">
	<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
</head>
<body>
<div class="container-fluid">
  	<div class="row content">
    	<?php 
		include("../includes/leftnav2.php");
		?>
        <div class="col-sm-9 tab-pane fade in active" id="home">
  		<h2 align="center"><i class="fa fa-cog" aria-hidden="true"></i> Application Setings</h2>
        <?php if(isset($_REQUEST['msg'])){?>
        <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      	<?php }?>
        <div class="row">
        	<div class="col-sm-3 col-md-3 col-lg-3">
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="fa fa-paper-plane" aria-hidden="true"></i> Settings</div>
                    <div class="panel-body">     
                        <form action="" method="post" id="frm1">
                        	<div class="form-group">
                                <label for="plan">Plan:</label>
                                <select name="plan" id="plan" required class="form-control required selectpicker"  data-live-search="true">
                    				<option value="">--Please Select--</option>
        							<option value="Basic"<?php if($row['plan_name']=="Basic"){ echo "selected";}?>>Basic</option>
                                    <option value="Enterprise"<?php if($row['plan_name']=="Enterprise"){ echo "selected";}?>>Enterprise</option>
                 				</select>
                            </div>
                            <div class="form-group">
                                <label for="userlimit">User Limit:</label>
                                <input type="text" class="form-control digits required" required id="userlimit" placeholder="Enter user limit" name="userlimit" value="<?=$row["user_limit"]?>">
                            </div>
                            <div class="form-group">
                                <label for="module">Module:</label>
                                <select name="module[]" id="module" class="form-control multiselect-ui" multiple="multiple">
                                	<option value="DMS"<?php if($array_module["DMS"]=="Y"){ echo "selected";}?>>DMS</option>
                                    <option value="SFA"<?php if($array_module["SFA"]=="Y"){ echo "selected";}?>>SFA</option>
                                    <option value="HRMS"<?php if($array_module["HRMS"]=="Y"){ echo "selected";}?>>HRMS</option>
                                    <option value="CLAIM"<?php if($array_module["CLAIM"]=="Y"){ echo "selected";}?>>CLAIM</option>
                                    <option value="SCHEME"<?php if($array_module["SCHEME"]=="Y"){ echo "selected";}?>>SCHEME</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="startdate">Start Date:</label>
                                <input type="text" class="form-control required" name="startdate" id="startdate" required value="<?=$row["start_date"]?>">
                            </div>
                            <div class="form-group">
                                <label for="expdate">Expiry Date:</label>
                                <input type="text" class="form-control required" name="expdate" id="expdate" required value="<?=$row["expiry_date"]?>">
                            </div>
                            <div class="form-group">
                                <label for="renewdate">Renew Date:</label>
                                <input type="text" class="form-control required" name="renewdate" id="renewdate" required value="<?=$row["renewal_date"]?>">
                            </div>
                            <div class="form-group">
                                <label for="status">Status:</label>
                                <select name='status' id='status' class="form-control required" required>
                                    <option value="1" <?php if($row['status'] =='1') {echo 'selected'; }?>>Activate</option>
                                    <option value="0" <?php if($row['status'] =='0') {echo 'selected'; }?>>Deactivate</option>
                                 </select>
                            </div>
                            <button type="submit" name="appset" value="updsetting" class="btn btn-primary">Submit</button>
                        </form>  
                    </div>
                </div>
            </div>
            <div class="col-sm-3 col-md-3 col-lg-3">
            </div>
        </div>
    </div>
    </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>     
</body>
</html>