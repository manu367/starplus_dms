<?php
require_once("../config/config.php");
$main_location = base64_decode($_REQUEST['locid']);
if($_POST){
	if(isset($_POST['Submit'])){
   		if ($_POST['Submit']=='Save'){
			@extract($_POST);
			///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
			$messageIdent = md5($ship_to);
			//and check it against the stored value:
    		$sessionMessageIdent = isset($_SESSION['messageIdentMA'])?$_SESSION['messageIdentMA']:'';
			if($messageIdent!=$sessionMessageIdent){//if its different:
				//save the session var:
            	$_SESSION['messageIdentMA'] = $messageIdent;
				////// count max no. of location in selected state
			   $query_code="SELECT COUNT(id) as qa FROM delivery_address_master WHERE location_code='".$main_location."'";
			   $result_code=mysqli_query($link1,$query_code)or die("ER2".mysqli_error($link1));
			   $arr_result2=mysqli_fetch_array($result_code);
			   $code_id=$arr_result2[0];
			   /// make 3 digit padding
			   $pad=str_pad(++$code_id,2,"0",STR_PAD_LEFT);
			   //// make logic of location code
			   $newlocationcode=strtoupper($main_location)."-AD".$pad;
				////////////insert in sub location		
    			$usr_add="INSERT INTO delivery_address_master SET location_code ='".$main_location."', address_code = '".$newlocationcode."', party_name='".$party_name."', address='".$ship_to."', city = '".$locationcity."', state = '".$locationstate."', pincode='".$pincode."', landmark='".$landmark."', contact_person='".$contact_person."',contact_no='".$contact_no."', email_id='".$email."', gstin='".$gst_no."', status='Active', create_by='".$_SESSION['userid']."', create_date='".$datetime."'";
    			$res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
				//////
				dailyActivity($_SESSION['userid'],$newlocationcode,"LOCATION-ADDRESS","ADD",$ip,$link1,"");
				////// return message
				$msg="You have successfully created a ship to address for location ".$main_location;
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
    	header("location:location_shipto_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."&locid=".base64_encode($main_location)."".$pagenav);
    	exit;
	}
}
if($_POST['updshipto']=="Update"){
	$flag = "";
	@extract($_POST);
	$ref_no = base64_decode($_POST['ref_no']);
	$sql_doc = "UPDATE delivery_address_master SET status='".$status."', pincode='".$pincode."', landmark='".$landmark."', contact_person='".$contact_person."',contact_no='".$contact_no."', email_id='".$email."', gstin='".$gst_no."', update_by='".$_SESSION['userid']."', update_date='".$datetime."' WHERE id='".$ref_no."'";
	$res_doc = mysqli_query($link1,$sql_doc);
	//// check if query is not executed
	if(!$res_doc){
		$flag = false;
		$error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}else{
		$flag = true;
		dailyActivity($_SESSION['userid'],$newlocationcode,"LOCATION-ADDRESS","UPDATE",$ip,$link1,"");
	}
	///// check both query are successfully executed
	if ($flag) {
		$cflag = "success";
		$cmsg = "Success";
		////// return message
		$msg="You have successfully updated a ship to address for location ".$main_location;
	} else {
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
	header("location:location_shipto_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."&locid=".base64_encode($main_location)."".$pagenav);
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
$(document).ready(function(){
	$("#frm1").validate();
});
////// function for open model to see sub location details
function openActionModel(docid){
	$.get('edit_location_shipto.php?doc_id='+docid, function(html){
		 $('#actionModel .modal-title').html('<i class="fa fa-pencil-square-o fa-lg faicon"></i> View / Edit Sub Location Details');
		 $('#actionModel .modal-body').html(html);
			var showbtn = '<input type="submit" class="btn btn-primary" name="updshipto" id="updshipto" value="Update" <?php if($_POST['updshipto']=='Update'){?>disabled<?php }?>><button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>';
		 $('#actionModel .modal-footer').html(showbtn);
		 $('#actionModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
}
/////////// function to get city on the basis of state
 function get_citydiv(){
	  var name=$('#locationstate').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{state:name},
		success:function(data){
	    $('#citydiv').html(data);
	    }
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
        	<h2 align="center"><i class="fa fa-address-card"></i> Location Ship To Master</h2>
            <h4 align="center" style="color:#FF0000">You are creating/viewing Ship To Address of <?=$main_location?></h4>
	    	<?php if(isset($_REQUEST['msg'])){
			$_SESSION['messageIdentMA'] = "";
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
                        <div class="panel-heading"><i class="fa fa-plus" aria-hidden="true"></i> Add New Ship To</div>
                        <div class="panel-body">
                        	<form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
							<div class="form-group">
                				<div class="col-sm-6"><strong>Location</strong></div>
                                <div class="col-sm-6"><?=$main_location?></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>Party Name <span class="red_small">*</span></strong></div>
                                <div class="col-sm-6"><input name="party_name" type="text" class="form-control mastername required" required id="party_name"></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>Ship To/ Address <span class="red_small">*</span></strong></div>
                                <div class="col-sm-6"><textarea name="ship_to" id="ship_to" class="form-control addressfield required" required style="resize:vertical"></textarea></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>State <span class="red_small">*</span></strong></div>
                                <div class="col-sm-6">
               	<select name='locationstate' id='locationstate' class='form-control required' onchange='get_citydiv();' required>
                	<option value=''>--Select State--</option>
					<?php
                    $state_query = "select distinct(state) from state_master where 1 order by state";
                    $state_res = mysqli_query($link1, $state_query);
                    while ($row_res = mysqli_fetch_array($state_res)) {
                    ?>
                    <option value="<?=$row_res['state']?>"<?php if($row_res['state']==$sel_result['state']){ echo "selected";}?>><?php echo $row_res['state'];?></option>
                    <?php }?>
                </select></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>City <span class="red_small">*</span></strong></div>
                                <div class="col-sm-6" id="citydiv"></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>Pincode <span class="red_small">*</span></strong></div>
                                <div class="col-sm-6"><input type="text" name="pincode" minlength="6" maxlength="6" required class="digits form-control" id="pincode"></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>Landmark</strong></div>
                                <div class="col-sm-6"><input type="text" name="landmark" id="landmark" class="form-control addressfield"></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>GSTIN</strong></div>
                                <div class="col-sm-6"><input type="text" name="gst_no" id="gst_no" class="form-control alphanumeric" minlength="15" maxlength="15"></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>Contact Person <span class="red_small">*</span></strong></div>
                                <div class="col-sm-6"><input name="contact_person" type="text" class="form-control mastername required" required id="contact_person"></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>Contact No. <span class="red_small">*</span></strong></div>
                                <div class="col-sm-6"><input name="contact_no" type="text" class="digits form-control" id="contact_no" required minlength="10" maxlength="15"></div>
                            </div>
                            <div class="form-group">
                				<div class="col-sm-6"><strong>Email <span class="red_small">*</span></strong></div>
                                <div class="col-sm-6"><input name="email" type="email" class="email required form-control" id="email" required></div>
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
                        <div class="panel-heading"><i class="fa fa-list" aria-hidden="true"></i> Ship To Addresses List</div>
                        <div class="panel-body">
                			<table  width="100%" id="subloc-grid" class="table-striped table-bordered table-hover" align="center" cellpadding="4" cellspacing="0" border="1">
                                <thead>
                                    <tr class="<?=$tableheadcolor?>">
                                        <th width="5%">S.No</th>
                                        <th width="65%">Address</th>
                                        <th width="15%">Status</th>
                                        <th width="15%">View/Edit</th>
                                	</tr>
                                </thead>
                                <tbody>
                                	<?php
									$i=1;
									$res_sl = mysqli_query($link1,"SELECT id, address, status FROM delivery_address_master WHERE location_code='".$main_location."'");
									while($row_sl = mysqli_fetch_assoc($res_sl)){
									?>
                                	<tr>
                                    	<td><?=$i?></td>
                                        <td><?=$row_sl["address"]?></td>
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