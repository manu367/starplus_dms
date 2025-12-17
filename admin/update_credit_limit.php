<?php
require_once("../config/config.php");
$location_code = base64_decode($_REQUEST['locid']);
$res_locdet = mysqli_query($link1,"SELECT * FROM current_cr_status WHERE asc_code='".$location_code."'")or die(mysqli_error($link1));
$row_locdet = mysqli_fetch_array($res_locdet);
@extract($_POST);
if($_POST){
	if(isset($_POST['Submit'])){
   		if ($_POST['Submit']=='Request'){
			$messageIdent = md5($location_code);
			//and check it against the stored value:
    		$sessionMessageIdent = isset($_SESSION['messageIdentCL'])?$_SESSION['messageIdentCL']:'';
			if($messageIdent!=$sessionMessageIdent){//if its different:
				//save the session var:
            	$_SESSION['messageIdentCL'] = $messageIdent;

				////////////insert	
    			$usr_add="INSERT INTO credit_limit_history SET  asc_code = '".$location_code."', parent_code = '".$row_locdet['parent_code']."', credit_limit='".$update_cr."', previous_limit='".$row_locdet['cr_limit']."', status='Pending for approval', entry_by = '".$_SESSION['userid']."', entry_date='".$datetime."', entry_remark='".$remark."', entry_ip='".$ip."'";
    			$res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
				//////
				dailyActivity($_SESSION['userid'],$location_code,"Update Credit Limit","REQUEST",$ip,$link1,"");
				////// return message
				$msg="You have successfully created an entry of credit limit for location ".$location_code." Now,it is pending for approval";
				$cflag = "success";
				$cmsg = "Success";
			}else {
        		//you've sent this already!
				$msg="You have saved this already ";
				$cflag = "warning";
				$cmsg = "Warning";
    		}
		}
		///// move to parent page
    	header("location:update_credit_limit.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."&locid=".base64_encode($location_code)."".$pagenav);
    	exit;
	}
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
$(document).ready(function(){
    $('#crlimitloc-grid').dataTable();
});
////// function for open model to see sub location details
function openActionModel(docid){
	$.get('view_crlimit_history.php?doc_id='+docid, function(html){
		 $('#actionModel .modal-title').html('<i class="fa fa-pencil-square-o fa-lg faicon"></i> Credit Limit Request Details');
		 $('#actionModel .modal-body').html(html);
			var showbtn = '<button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>';
		 $('#actionModel .modal-footer').html(showbtn);
		 $('#actionModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
}
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
	<div class="container-fluid">
		<div class="row content">
			<?php 
    		include("../includes/leftnav2.php");
    		?>
			<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
        		<h2 align="center"><i class="fa fa-address-card"></i> Update Credit Limit</h2>
            	<h4 align="center" style="color:#FF0000">You are updating credit limit of location <br/><?=str_replace("~"," , ",getAnyDetails($location_code,"name,city,state","asc_code","asc_master",$link1));?>(<?=$location_code?>)</h4>
	    	 	<?php 
				if(isset($_REQUEST['msg'])){
					$_SESSION['messageIdentCL'] = "";
				?>
            	<div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
                	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                	<strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
            	</div>
            	<?php }?> 
            	<div class="row">
                	<div class="col-sm-5">
                    	<div class="panel panel-info">
                        	<div class="panel-heading"> Update Credit Limit</div>
                        	<div class="panel-body">
                        		<form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
									<div class="form-group">
                						<div class="col-sm-5"><strong>In Favour Of<br/><span class="small">(Parent Location)</span></strong></div>
                                		<div class="col-sm-7">
                                        	<?php
											echo str_replace("~",", ",getAnyDetails($row_locdet['parent_code'],"name,city,state,asc_code","asc_code","asc_master",$link1));
											?>
                                    		<?php /*?><input name="parent_code" type="text" class="form-control mastername required" required id="parent_code" readonly value="<?=$row_locdet['parent_code']?>"/><?php */?>
                                    	</div>
                            		</div>
									<div class="form-group">
                						<div class="col-sm-5"><strong>Current Credit Limit</strong></div>
                                 		<div class="col-sm-7">
                                    		<input name="current_cr" type="text" class="form-control number required" required id="current_cr" readonly value="<?=$row_locdet['cr_limit']?>"/></div>
                            		</div>
									<div class="form-group">
                						<div class="col-sm-5"><strong>Update Credit Limit <span class="red_small">*</span></strong></div>
                                		<div class="col-sm-7">
                                    		<input name="update_cr" type="text" class="form-control number required" required id="update_cr" value=""/>
                                        </div>
                            		</div>
                                    <div class="form-group">
                						<div class="col-sm-5"><strong>Remark <span class="red_small">*</span></strong></div>
                                		<div class="col-sm-7"><textarea name="remark" id="remark" class="form-control addressfield required" required style="resize:vertical"></textarea></div>
                            		</div>
                            		<div class="form-group">
                                		<div class="col-md-12" align="center">
                                  			<input type="submit" class="btn <?=$btncolor?>" name="Submit" id="Update" value="Request" title="Create Request" <?php if($_POST['Submit']=='Request'){?>disabled<?php }?>>&nbsp;&nbsp;<input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='asp_details.php?<?=$pagenav?>'">
                                		</div>
                              		</div>
                            	</form>
                        	</div>
                    	</div>
                	</div>
                	<div class="col-sm-7">
                    	<div class="panel panel-warning">
                        	<div class="panel-heading"><i class="fa fa-history" aria-hidden="true"></i> Update Credit Limit History</div>
                        	<div class="panel-body">
                			<table  width="100%" id="crlimitloc-grid" class="table-striped table-bordered table-hover" align="center" cellpadding="4" cellspacing="0" border="1">
                                <thead>
                                    <tr class="<?=$tableheadcolor?>">
                                        <th width="3%">S.No</th>
                                        <th width="32%">Update On</th>
                                        <th width="15%">Reuested Limit</th>
                                        <th width="15%">Approved Limit</th>
                                        <th width="20%">Status</th>
                                        <th width="15%">View</th>
                                	</tr>
                                </thead>
                                <tbody>
                                	<?php
									$i=1;
									$res_sl = mysqli_query($link1,"SELECT id, credit_limit, approved_limit, status, entry_date FROM credit_limit_history WHERE asc_code='".$location_code."' ORDER BY id DESC");
									while($row_sl = mysqli_fetch_assoc($res_sl)){
									?>
                                	<tr>
                                    	<td><?=$i?></td>
                                        <td><?=$row_sl["entry_date"]?></td>
                                        <td><?=$row_sl["credit_limit"]?></td>
                                        <td><?=$row_sl["approved_limit"]?></td>
                                        <td><?=$row_sl["status"]?></td>
                                        <td align="center"><a href="#" class="btn <?=$btncolor?>" title="Credit limit request info" onClick="openActionModel('<?=$row_sl['id']?>')"><i class="fa fa-info-circle" title="Credit limit request info"></i></a></td>
                                    </tr>
                                    <?php
										$i++;
									}
									?>
                                </tbody>
                        	</table>
                        </div>
                    </div>
                </div>
            </div>
    	</div><!--close tab pane-->
	</div><!--close row content-->
</div><!--close container fluid-->
<!-- Start Model Mapped Modal -->
<div class="modal modalTH fade" id="actionModel" role="dialog">
	<form class="form-horizontal" role="form" id="frm2" name="frm2" method="post">
	<div class="modal-dialog modal-dialogTH modal-lg">
  		<!-- Modal content-->
  		<div class="modal-content">
    		<div class="modal-header">
      			<button type="button" class="close" data-dismiss="modal">&times;</button>
      			<h4 class="modal-title" align="center"></h4>
    		</div>
    		<div class="modal-body modal-bodyTH">
     			<!-- here dynamic task details will show -->
    		</div>
    		<div class="modal-footer">
      
    		</div>
    	</div>
	</div>
	</form>
</div>
<!--close Model Mapped modal-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>