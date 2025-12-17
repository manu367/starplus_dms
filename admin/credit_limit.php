<?php
require_once("../config/config.php");
$pkid = base64_decode($_REQUEST['id']);
////// get details of selected entry////
$res=mysqli_query($link1,"SELECT * FROM credit_limit_history WHERE id='".$pkid."'")or die(mysqli_error($link1));
$row2=mysqli_fetch_array($res);
////// final submit form ////
@extract($_POST);
if($_POST['Submit']=="Update"){
	if($row2['asc_code']!="" && $row2['parent_code']!=""){
		///// Update approval entries
		if(mysqli_query($link1,"UPDATE credit_limit_history SET status='".$app_status."', app_status='".$app_status."', approved_limit='".$approved_limit."', app_by = '".$_SESSION['userid']."',app_date='".$datetime."', app_remark='".$remark."', app_ip='".$ip."' WHERE id='".$pkid."' ")or die("ER1".mysqli_error($link1))){
			if($app_status=="Approved"){
				// Update Column cr_limit of table current_cr_status
				$sql_upd = "UPDATE current_cr_status SET cr_limit='".$approved_limit."',last_updated='".$datetime."' WHERE asc_code = '".$row2['asc_code']."' AND parent_code ='".$row2['parent_code']."'";
				$res_upd = mysqli_query($link1,$sql_upd)or die("error3".mysqli_error($link1));
			}
			////// insert in activity table////
			dailyActivity($_SESSION['userid'],$row2['asc_code'],"Update Credit Limit","UPDATE",$ip,$link1,"");
			//return message
			$msg="You have successfully ".$app_status." credit limit of location ".$row2['asc_code'];
			$cflag = "success";
			$cmsg = "Success";
	   }else{
			////// return message
			$msg="Something went wrong. Please try again.";
			$cflag = "warning";
			$cmsg = "Warning";
	   }
   }else{
		////// return message
		$msg="Request could not process. Please try again.";
		$cflag = "warning";
		$cmsg = "Warning";
   }
	///// move to parent page
   	header("Location:creditlimit.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script> 
<script>
	$(document).ready(function(){
        $("#frm1").validate();
	});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9">
      		<h2 align="center"><i class="fa fa-gavel"></i> Approval of Credit Limit</h2>
      		<div class="form-group"  id="page-wrap" style="margin-left:10px;">
          		<form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post">
           		<div class="panel-group">
                    <div class="panel-group">
                        <div class="panel panel-success table-responsive">
                            <div class="panel-heading">Requested Details</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>Location Name</strong></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><?=str_replace("~",", ",getAnyDetails($row2["asc_code"],"name,city,state,asc_code","asc_code","asc_master",$link1));?></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>In Favour Of</strong></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><?=str_replace("~",", ",getAnyDetails($row2['parent_code'],"name,city,state,asc_code","asc_code","asc_master",$link1));?></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>Requested Limit</strong></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><?=$row2["credit_limit"]?></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>Approved Limit</strong></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><?=$row2["approved_limit"]?></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>Status</strong></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><span class="text-danger"><?=$row2["status"]?></span></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>Previous Limit</strong></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><?=$row2["previous_limit"]?></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>Entry By</strong></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><?=str_replace("~",", ",getAnyDetails($row2['entry_by'],"name,oth_empid,username","username","admin_users",$link1));?></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>Entry Date</strong></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><?=$row2["entry_date"]?></div>
                                </div>
                                 <div class="form-group">
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>Entry Remark</strong></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><?=$row2["entry_remark"]?></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3">&nbsp;</div>
                                    <div class="col-sm-3 col-md-3 col-lg-3">&nbsp;</div>
                                </div>
                            </div><!--close panel body-->
                        </div><!--close panel-->
                        <?php if($row2['app_status']==""){?>
                        <div class="panel panel-success table-responsive">
                            <div class="panel-heading">Approval Action</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>Approved Limit <span class="red_small">*</span></strong></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><input type="text" name="approved_limit" class="form-control required number" value="<?php echo $row2['credit_limit']; ?>" id="approved_limit" required/></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>Approval Action <span class="red_small">*</span></strong></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><select name='app_status' id='app_status' class="form-control required"  required/>		
                    <option value="Approved"<?php if($_REQUEST['app_status']=="Approved"){ echo "selected";}?>>Approved</option>
                    <option value="Rejected"<?php if($_REQUEST['app_status']=="Rejected"){ echo "selected";}?>>Rejected</option>
                 </select></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>Remark</strong></div>
                                    <div class="col-sm-9 col-md-9 col-lg-9"><textarea name="remark" class="form-control addressfield" id="remark"></textarea></div>
                                </div>
                                <div class="form-group">
                                	<div class="col-md-12" align="center">
                                    	<input type="submit" class="btn btn-primary" name="Submit" id="save" value="Update" title="Take Approval Action">             
                                      	<input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='creditlimit.php?<?=$pagenav?>'">
                                    </div>
                              	</div>
                                
                            </div><!--close panel body-->
                        </div><!--close panel-->
                        <?php }else{?>
                        <div class="panel panel-success table-responsive">
                            <div class="panel-heading">Approval Details</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>Status</strong></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><?=$row2["app_status"]?></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>Approval By</strong></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><?=str_replace("~",", ",getAnyDetails($row2['app_by'],"name,oth_empid,username","username","admin_users",$link1));?></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>Approval Date</strong></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><?=$row2["app_date"]?></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><strong>Approval Remark</strong></div>
                                    <div class="col-sm-3 col-md-3 col-lg-3"><?=$row2["app_remark"]?></div>
                                </div>
                                <div class="form-group">
                                	<div class="col-md-12" align="center">            
                                      	<input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='creditlimit.php?<?=$pagenav?>'">
                                    </div>
                              	</div>
                            </div><!--close panel body-->
                        </div><!--close panel-->
                        <?php }?>
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