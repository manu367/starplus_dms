<?php
require_once("../config/config.php");
$main_location = base64_decode($_REQUEST['locid']);
if($_POST){
	if(isset($_POST['Submit'])){
   		if ($_POST['Submit']=='Save'){
			@extract($_POST);
			///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
			$messageIdent = md5($sub_location);
			//and check it against the stored value:
    		$sessionMessageIdent = isset($_SESSION['messageIdent'])?$_SESSION['messageIdent']:'';
			if($messageIdent!=$sessionMessageIdent){//if its different:
				//save the session var:
            	$_SESSION['messageIdent'] = $messageIdent;
				////// count max no. of location in selected state
			   $query_code="SELECT COUNT(id) as qa FROM sub_location_master WHERE main_location='".$main_location."'";
			   $result_code=mysqli_query($link1,$query_code)or die("ER2".mysqli_error($link1));
			   $arr_result2=mysqli_fetch_array($result_code);
			   $code_id=$arr_result2[0];
			   /// make 3 digit padding
			   $pad=str_pad(++$code_id,2,"0",STR_PAD_LEFT);
			   //// make logic of location code
			   $newlocationcode=strtoupper($main_location)."/SL/".$pad;
				////////////insert in sub location		
    			$usr_add="INSERT INTO sub_location_master SET main_location ='".$main_location."', sub_location = '".$newlocationcode."', cost_center='".$cost_centre."', sub_location_name='".$sub_location."', sub_location_type='".$segment."', contact_person='".$contact_person."',contact_no='".$contact_no."', contact_email='".$email."', remark='".$remark."', status='Active', create_by='".$_SESSION['userid']."', create_date='".$datetime."'";
    			$res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
				//////
				dailyActivity($_SESSION['userid'],$newlocationcode,"SUB-LOCATION","ADD",$ip,$link1,"");
				////// return message
				$msg="You have successfully created a sub location ".$sub_location;
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
    	header("location:sub_location_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."&locid=".base64_encode($main_location)."".$pagenav);
    	exit;
	}
}
if($_POST['updsubloc']=="Update"){
	$flag = "";
	@extract($_POST);
	$ref_no = base64_decode($_POST['ref_no']);
	$sql_doc = "UPDATE sub_location_master SET sub_location_name='".$sub_location."', contact_person='".$contact_person."',contact_no='".$contact_no."', contact_email='".$email."', remark='".$remark."', status='".$status."', update_by='".$_SESSION['userid']."', update_date='".$datetime."' WHERE id='".$ref_no."'";
	$res_doc = mysqli_query($link1,$sql_doc);
	//// check if query is not executed
	if(!$res_doc){
		$flag = false;
		$error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}else{
		$flag = true;
		dailyActivity($_SESSION['userid'],$ref_no,"SUB-LOCATION","UPDATE",$ip,$link1,"");
	}
	///// check both query are successfully executed
	if ($flag) {
		$cflag = "success";
		$cmsg = "Success";
		////// return message
		$msg="You have successfully updated a sub location ".$sub_location;
	} else {
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
	header("location:sub_location_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."&locid=".base64_encode($main_location)."".$pagenav);
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
  <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function(){
    $('#subloc-grid').dataTable();
});
////// function for open model to see sub location details
function openActionModel(docid){
	$.get('edit_sub_location.php?doc_id='+docid, function(html){
		 $('#actionModel .modal-title').html('<i class="fa fa-pencil-square-o fa-lg faicon"></i> View / Edit Sub Location Details');
		 $('#actionModel .modal-body').html(html);
			var showbtn = '<input type="submit" class="btn btn-primary" name="updsubloc" id="updsubloc" value="Update" <?php if($_POST['updsubloc']=='Update'){?>disabled<?php }?>><button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>';
		 $('#actionModel .modal-footer').html(showbtn);
		 $('#actionModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
}
</script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
		<?php 
    	include("../includes/leftnav2.php");
    	?>
		<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
        	<h2 align="center"><i class="fa fa-bank"></i> Sub-Locations Master</h2>
            <h4 align="center" style="color:#FF0000">You are creating/viewing sub-location of <?=$main_location?></h4>
	    	<?php if(isset($_REQUEST['msg'])){
			$_SESSION['messageIdent'] = "";
			?>
            <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
            </div>
            <?php }?>
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel panel-info">
                        <div class="panel-heading"><i class="fa fa-plus" aria-hidden="true"></i> Add New Sub-Location</div>
                        <div class="panel-body">
                        	<form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
							<div class="form-group">
                				<div class="col-sm-6"><strong>Main Location</strong></div>
                                <div class="col-sm-6"><?=$main_location?></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>Cost Centre <span class="red_small">*</span></strong></div>
                                <div class="col-sm-6"><input type="text" name="cost_centre" class="required mastername form-control cp" id="cost_centre" required/></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>Sub Location <span class="red_small">*</span></strong></div>
                                <div class="col-sm-6"><input type="text" name="sub_location" class="required mastername form-control cp" id="sub_location" required/></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>Segment<span class="red_small">*</span></strong></div>
                                <div class="col-sm-6"><select name="segment" id="segment" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <?php
					$seg_sql = "SELECT * FROM segment_master WHERE status='A' ORDER BY segment";
					$seg_res = mysqli_query($link1,$seg_sql);
					while($seg_row = mysqli_fetch_array($seg_res)){
					?>
                	<option value="<?=$seg_row['segment']?>"<?php if($_REQUEST['segment']==$seg_row['segment']){ echo "selected";}?>><?php echo $seg_row['segment']?></option>
                	<?php }?>
                </select></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>Contact Person <span class="red_small">*</span></strong></div>
                                <div class="col-sm-6"><input name="contact_person" type="text" class="form-control required" required id="contact_person"></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>Contact No. <span class="red_small">*</span></strong></div>
                                <div class="col-sm-6"><input name="contact_no" type="text" class="digits form-control" id="contact_no" required minlength="10" maxlength="10"></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>Email <span class="red_small">*</span></strong></div>
                                <div class="col-sm-6"><input name="email" type="email" class="email required form-control" id="email" required></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>Remark</strong></div>
                                <div class="col-sm-6"><textarea name="remark" id="remark" class="form-control addressfield" style="resize:vertical"></textarea></div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" align="center">
                                  <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Save" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
                                  <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='asp_details.php?<?=$pagenav?>'">
                                </div>
                              </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="panel panel-success">
                        <div class="panel-heading"><i class="fa fa-list" aria-hidden="true"></i> Sub Location List</div>
                        <div class="panel-body">
                			<table  width="100%" id="subloc-grid" class="table-striped table-bordered table-hover" align="center" cellpadding="4" cellspacing="0" border="1">
                                <thead>
                                    <tr class="<?=$tableheadcolor?>">
                                        <th width="5%">S.No</th>
                                        <th width="65%">Sub Location</th>
                                        <th width="15%">Status</th>
                                        <th width="15%">View/Edit</th>
                                	</tr>
                                </thead>
                                <tbody>
                                	<?php
									$i=1;
									$res_sl = mysqli_query($link1,"SELECT id, sub_location_name, status FROM sub_location_master WHERE main_location='".$main_location."'");
									while($row_sl = mysqli_fetch_assoc($res_sl)){
									?>
                                	<tr>
                                    	<td><?=$i?></td>
                                        <td><?=$row_sl["sub_location_name"]?></td>
                                        <td><?=$row_sl["status"]?></td>
                                        <td align="center"><a href="#" class="btn <?=$btncolor?>" title="Sub location info" onClick="openActionModel('<?=$row_sl['id']?>')"><i class="fa fa-info-circle" title="Sub location info"></i></a></td>
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
	<div class="modal-dialog modal-dialogTH">
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