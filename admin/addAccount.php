<?php
////// Function ID ///////
$fun_id = array("a"=>array(73)); 
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
////// final submit form ////
if($_POST['Submit']=="Save"){
	////// check account should not be duplicate
	if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM account_master WHERE account_no='".$_POST['accountNo']."'"))==0){
		////// insert query
		$res_ins = mysqli_query($link1,"INSERT INTO account_master SET account_name = '".$_POST['accountName']."', account_no = '".$_POST['accountNo']."',  account_type = '".$_POST['accountType']."', address = '".$_POST['address']."', status = '".$_POST['status']."', ref_remark = '".$_POST['refRemark']."', create_by = '".$_SESSION["userid"]."', create_date = '".$datetime."'");
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$_POST['accountNo'],"ACCOUNT","ADD",$ip,$link1,"");	
		//return message
		$msg="You have successfully added a new account like ".$_POST['accountName']." - ".$_POST['accountNo'];
	}
	else{
		////// return message
		$msg="Entered account is already exist. Please add new account only.";
	}
	///// move to parent page
	header("Location:account_master.php?msg=".$msg."".$pagenav);
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=siteTitle?></title>
<script src="../js/jquery.min.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css"> 
<script>
$(document).ready(function(){
	$("#frm1").validate();
});
</script>
<style>
.red_small{
	color:red;
}
</style>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
	<div class="container-fluid">
  		<div class="row content">
		<?php 
    	include("../includes/leftnav2.php");
    	?>
    		<div class="col-sm-9">
      			<h2 align="center"><i class="fa fa-sitemap"></i>&nbsp;&nbsp;Add New Account</h2><br/><br/>
      			<div class="form-group"  id="page-wrap" style="margin-left:10px;">
          		<form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post">
          			<div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Account Type<span class="red_small">*</span></label>
              				<div class="col-md-6">
                 				<select name="accountType" id="accountType" class="form-control required" required>
                  					<option value=''>--Please Select--</option>
                  					<?php
										$sql_mod = "SELECT mode FROM payment_mode WHERE status = 'A' ORDER BY mode";
										$res_mod = mysqli_query($link1,$sql_mod) or die(mysqli_error($link1));
										while($row_mod = mysqli_fetch_array($res_mod)){
									?>
				  					<option value="<?=$row_mod['mode']?>"<?php if($_REQUEST['accountType']==$row_mod['mode']){echo "selected";}?>><?=$row_mod['mode']?></option>
									<?php 
										}
                					?>
                				</select>
              				</div>
            			</div>
          			</div>
          			<div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Account Name<span class="red_small">*</span></label>
              				<div class="col-md-6">
                 				<input type="text" name="accountName" class="form-control required mastername"  id="accountName" required/>
              				</div>
            			</div>
          			</div>
                    <div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Account No.<span class="red_small">*</span></label>
              				<div class="col-md-6">
                 				<input type="text" name="accountNo" class="form-control required alphanumeric"  id="accountNo" required/>
              				</div>
            			</div>
          			</div>
                    <div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Address</label>
              				<div class="col-md-6">
                 				<textarea name="address" id="address" style="resize:none" class="form-control addressfield"></textarea>
              				</div>
            			</div>
          			</div>
                    <div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Ref. Remark</label>
              				<div class="col-md-6">
                 				<textarea name="refRemark" id="refRemark" style="resize:none" class="form-control addressfield"></textarea>
              				</div>
            			</div>
          			</div>
          			<div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Status<span class="red_small">*</span></label>
              				<div class="col-md-6">
                 				<select name='status' id='status' class="form-control required" required/>
                                    <option value="A" selected>Active</option>
                                    <option value="D">Deactive</option>
                 				</select>
              				</div>
            			</div>
          			</div>
          			<div class="form-group">
            			<div class="col-md-12" align="center">
              				<input type="submit" class="btn btn-primary" name="Submit" id="save" value="Save" title="Add Account"> 
              				<input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='account_master.php?<?=$pagenav?>'">
            			</div>
          			</div>
    			</form>
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