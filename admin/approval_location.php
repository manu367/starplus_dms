<?php
////// Function ID ///////
$fun_id = array("a"=>array(24)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

//////////////////////////
$docid=base64_decode($_REQUEST['id']);
//// po to vendor details
$sql_locdet="SELECT * FROM asc_master where sno='".$docid."'";
$res_locdet=mysqli_query($link1,$sql_locdet);
$row_locdet=mysqli_fetch_assoc($res_locdet);

////// final submit form ////
@extract($_POST);
if(isset($_POST['save'])){
	/////if user hit approve button
	if($_POST['save']=="Approve"){
	
		/////////////// update status active(1) in loaction master ////////////
		$upd_status="update asc_master set status='Active' where sno='".$docid."' ";
    	$result=mysqli_query($link1,$upd_status);
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$row_locdet['asc_code'],"LOCATION","APPROVE",$ip,$link1,$flag);
		////// return message
		$msg = "You have taken Approve action for ".$row_locdet['locationname'];
		$cflag = "success";
		$cmsg = "Success";
	}else{
		///// nothing to do
		////// return message
		$msg="Something went wrong. Please try again.";
		$cflag = "danger";
		$cmsg = "Failed";
	}
	///// move to parent page
    header("Location:asp_details.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
 <script language="javascript" type="text/javascript">



	$(document).ready(function(){
		var spinner = $('#loader');
        $("#frm1").validate({
		  submitHandler: function (form) {
				if(!this.wasSent){
					this.wasSent = true;
					$(':submit', form).val('Please wait...')
									  .attr('disabled', 'disabled')
									  .addClass('disabled');
					spinner.show();				  
					form.submit();
				} else {
					return false;
				}
          }
		});
    });
 </script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css">
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
  </head>
  <body>
  <div class="container-fluid">
    <div class="row content">
    <?php 
        include("../includes/leftnav2.php");
    ?>
      <div class="<?=$screenwidth?>">
        <h2 align="center"><i class="fa <?=$fa_icon?>"></i> <?=$locationstr?> Approval</h2>
      	<h4 align="center"><?=$row_locdet['name']."  (".$row_locdet['asc_code'].")";?></h4>
        <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
	  <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
   <div class="panel-group">
    <div class="panel panel-success table-responsive">
        <div class="panel-heading"><i class="fa fa-id-badge fa-lg"></i>&nbsp;&nbsp;Location Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Circle</label></td>
                <td width="30%"><?=$row_locdet['circle']?></td>

                <td width="20%"><label class="control-label">Location Type</label></td>
                <td width="30%"><?=$row_locdet['id_type']?></td>
              </tr>
			  <tr>
                <td width="20%"><label class="control-label">State</label></td>
                 <td width="30%"><?=$row_locdet['state']?></td>

                <td width="20%"><label class="control-label">Location Name</label></td>
                <td width="30%" class="alert-info"><?=$row_locdet['name']?></td>
              </tr>
              <tr>
                <td width="20%"><label class="control-label">City</label></td>
                 <td width="30%"><?=$row_locdet['city']?></td>
                <td width="20%"><label class="control-label">Contact Person</label></td>
                <td width="30%"><?=$row_locdet['contact_person']?></td>
              </tr>
              <tr>
                <td width="20%"><label class="control-label">Email</label></td>
                <td width="30%"><?=$row_locdet['email']?></td>
                <td width="20%"><label class="control-label">Pincode</label></td>
                <td width="30%"><?=$row_locdet['pincode']?></td>
              </tr>
              <tr>
                <td width="20%"><label class="control-label">Phone Number1</label></td>
                <td width="30%"><?=$row_locdet['landline']?></td>
                <td width="20%"><label class="control-label">Phone Number2</label></td>
                <td width="30%"><?=$row_locdet['phone']?></td>
              </tr>
            
           
              <tr>
                <td width="20%"><label class="control-label">Billing Address</label></td>
                <td width="80%" colspan="3"><?php echo $row_locdet['addrs'];?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
  <div class="panel panel-success table-responsive">
     <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Action Taken</div>
         <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
			<tr>
            	<td align="right"><label class="control-label">Remark</label></td>
            	<td><textarea name="remark" id="remark" required class="required form-control addressfield" ></textarea></td>
            </tr>
			<tr>
                <td align="center" colspan="2">
			
				 <input title="save" type="submit" class="btn<?=$btncolor?>" id = "save"  name= "save" value="Approve" >
				
			
                  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='asp_details.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
                 </td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    </div><!--close panel group-->
	</form>
        </div>
        <!--End form group--> 
      </div>
      <!--End col-sm-9--> 
    </div>
    <!--End row content--> 
  </div>
  <!--End container fluid-->
  <div id="loader"></div>
  <?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
