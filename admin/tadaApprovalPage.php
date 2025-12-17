<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM ta_da where system_ref_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql)or die("er1".mysqli_error($link1));
$po_row=mysqli_fetch_assoc($po_res);
$se = explode("~",getAdminDetails($po_row['userid'],"name,phone,emailid,band",$link1));
////// final submit form ////
@extract($_POST);
if($_POST){
  if($_POST['Submit']=='Update'){
	  $decodepono=base64_decode($refno);
	  if($actiontaken=="Approved" || $actiontaken=="Approved with deduction"){ $app_amt = $approved_amt; }else{ $app_amt = 0.00;}
	  ///// update po status ///////////
	  mysqli_query($link1,"UPDATE ta_da set status='".$actiontaken."', approved_amt='".$app_amt."' where system_ref_no = '".$decodepono."'")or die("ER1".mysqli_error($link1));
	  ////// insert in approval table////
	 approvalActivity($decodepono,$po_row["entry_date"],"TA DA",$_SESSION['userid'],$actiontaken,$today,$currtime,$remark,$ip,$link1,"");
     ////// insert in activity table////
	 dailyActivity($_SESSION['userid'],$decodepono,"TA DA APPROVAL","APPROVAL",$ip,$link1,"");
	 ////// return message
	 $msg="You have successfully taken approval (".$actiontaken.") action for TA DA ".$decodepono;
  }else{
	 ////// return message
	 $msg="Something went wrong. Please try again.";
  }
  ///// move to parent page
  header("Location:ta_da_approval.php?msg=".$msg."".$pagenav);
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
 <script type="text/javascript">
$(document).ready(function(){
        $("#frm1").validate();
});
</script>
 <style>
/* The Modal (background) */
.modal {
		display: none; /* Hidden by default */
		position: fixed; /* Stay in place */
		z-index: 1; /* Sit on top */
		padding-top: 50px; /* Location of the box */
		left: 0;
		top: 0;
		width: 100%; /* Full width */
		height: 100%; /* Full height */
		overflow: auto; /* Enable scroll if needed */
		background-color: rgb(0,0,0); /* Fallback color */
		background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
	}

	/* Modal Content */
	.modal-content {
		background-color: #fefefe;
		margin: auto;
		padding: 20px;
		border: 1px solid #888;
		width: 50%;
		height: 50%;
		margin-top: 20px;
	}

	/* The Close Button */
	.close {
		color: #aaaaaa;
		float: right;
		font-size: 28px;
		font-weight: bold;
	}

	.close:hover,
	.close:focus {
		color: #000;
		text-decoration: none;
		cursor: pointer;
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
      <h2 align="center"><i class="fa fa-address-card"></i> TA DA Approval</h2>
      <h4 align="center"><?php echo $docid;?></h4>
      <h4 align="center"><?php echo $po_row["entry_date"]." ".$po_row["entry_time"];?></h4>
   <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">   
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">Sales Executive Information</div>
        <div class="panel-body">
         <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Executive Name</label></td>
                <td colspan="3"><?php echo $se[0];?></td>
                </tr>
              <tr>
                <td><label class="control-label">Executive Phone</label></td>
                <td width="30%"><?php echo $se[1];?></td>
                <td width="20%"><label class="control-label">Executive Email</label></td>
                <td width="30%"><?php echo $se[2];?></td>
              </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Expense Information</div>
      <div class="panel-body">
      <?php
	  ////// check hotel city
	  $hotelcity = getAnyDetails($po_row['hotel_city'],"city_type","city","district_master",$link1); 
	  if($hotelcity=="M"){
	  	$hotelclassid = "5";
	  }else if($hotelcity=="C"){
	  	$hotelclassid = "6";
	  }else{
	  	$hotelclassid = "7";
	  }
	  /////
	  ////// check travel/food city
	  $travelcity = getAnyDetails($po_row['to_location'],"city_type","city","district_master",$link1); 
	  if($travelcity=="M"){
	  	$travelclassid = "5";
		$foodclassid = "5";
	  }else if($travelcity=="C"){
	  	$travelclassid = "6";
		$foodclassid = "6";
	  }else{
	  	$travelclassid = "7";
		$foodclassid = "7";
	  }
	  /////
	  ?>
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Food Expense</label></td>
                <td width="15%"><?php echo $po_row['food_exp'];?></td>
                <td width="15%"><?php
				if($se[3]=="1" || $se[3]=="2"){
					echo "Actual (Max Allowed)";
				}else{
                	$row_tadalimit = mysqli_fetch_assoc(mysqli_query($link1,"SELECT exp_limit FROM expense_limit_master WHERE band='".$se[3]."' AND exp_type='FOOD' AND class_type IN ('".$foodclassid."')"));
					echo $row_tadalimit["exp_limit"]." (Max Allowed)";
				}
				?></td>
                <td width="20%"><label class="control-label">Food Expense Img</label></td>
                <td width="30%"><?php if($po_row['food_exp_img']){?><img src="<?='../salesapi/tadaimg/'.substr($po_row['entry_date'],0,7).'/'.$po_row['food_exp_img']; ?>" alt="" width="100" height="200" class="img-responsive" id="image1" onClick="getThisValue(1)"/><?php }else{ echo "No image uploaded";}?></td>
              </tr>
              <tr>
                <td><label class="control-label">Logistic Expense</label></td>
                <td><?php echo $po_row['courier_exp'];?></td>
                <td>&nbsp;</td>
                <td><label class="control-label">Logistic Expense Img</label></td>
                <td><?php if($po_row['courier_exp_img']){?><img src="<?='../salesapi/tadaimg/'.substr($po_row['entry_date'],0,7).'/'.$po_row['courier_exp_img']; ?>" alt="" width="100" height="200" class="img-responsive" id="image2" onClick="getThisValue(2)"/><?php }else{ echo "No image uploaded";}?></td>
              </tr>
                <tr>
                <td><label class="control-label">Travel Expense</label></td>
                <td><?php echo $po_row['localconv_exp'];?></td>
                <td><?php
				if($se[3]=="1" || $se[3]=="2"){
					echo "Actual (Max Allowed)";
				}else{
                	$row_tadalimit = mysqli_fetch_assoc(mysqli_query($link1,"SELECT exp_limit FROM expense_limit_master WHERE band='".$se[3]."' AND exp_type='TRAVEL' AND class_type IN ('".$travelclassid."')"));
					echo $row_tadalimit["exp_limit"]." (Max Allowed)";
				}
				?></td>
                <td><label class="control-label">Travel Expense Img</label></td>
                <td><?php if($po_row['localconv_exp_img']){?><img src="<?='../salesapi/tadaimg/'.substr($po_row['entry_date'],0,7).'/'.$po_row['localconv_exp_img']; ?>" alt="" width="100" height="200" class="img-responsive" id="image3" onClick="getThisValue(3)"/><?php }else{ echo "No image uploaded";}?></td>
              </tr>
               <tr>
                <td><label class="control-label">Non-Travel Expense</label></td>
                <td><?php echo $po_row['mobile_exp'];?></td>
                <td>&nbsp;</td>
                <td><label class="control-label">Non-Travel Expense Img</label></td>
                <td><?php if($po_row['mobile_exp_img']){?><img src="<?='../salesapi/tadaimg/'.substr($po_row['entry_date'],0,7).'/'.$po_row['mobile_exp_img']; ?>" alt="" width="100" height="200" class="img-responsive" id="image4" onClick="getThisValue(4)"/><?php }else{ echo "No image uploaded";}?></td>
              </tr>
               <tr>
                <td><label class="control-label">Other Expense</label></td>
                <td><?php echo $po_row['other_exp'];?></td>
                <td>&nbsp;</td>
                <td><label class="control-label">Other Expense Img</label></td>
                <td><?php if($po_row['other_exp_img']){?><img src="<?='../salesapi/tadaimg/'.substr($po_row['entry_date'],0,7).'/'.$po_row['other_exp_img']; ?>" alt="" width="100" height="200" class="img-responsive" id="image5" onClick="getThisValue(5)"/><?php }else{ echo "No image uploaded";}?></td>
              </tr>
              <tr>
                <td><label class="control-label">Hotel Expense</label></td>
                <td><?php echo $po_row['hotel_exp'];?></td>
                <td><?php
				if($se[3]=="1" || $se[3]=="2"){
					echo "Actual (Max Allowed)";
				}else{
                	$row_tadalimit = mysqli_fetch_assoc(mysqli_query($link1,"SELECT exp_limit FROM expense_limit_master WHERE band='".$se[3]."' AND exp_type='HOTEL' AND class_type IN ('".$hotelclassid."')"));
					echo $row_tadalimit["exp_limit"]." (Max Allowed)";
				}
				?></td>
                <td><label class="control-label">Hotel Expense Img</label></td>
                <td><?php if($po_row['hotel_exp_img']){?><img src="<?='../salesapi/tadaimg/'.substr($po_row['entry_date'],0,7).'/'.$po_row['hotel_exp_img']; ?>" alt="" width="100" height="200" class="img-responsive" id="image6" onClick="getThisValue(6)"/><?php }else{ echo "No image uploaded";}?></td>
              </tr>
               <tr>
                 <td><label class="control-label">Total Amount</label></td>
                 <td colspan="2"><?php echo $po_row['total_amt'];?></td>
                 <td><label class="control-label">Remark</label></td>
                 <td><?php echo $po_row['remark'];?></td>
                 </tr>
                 <tr>
                 <td><label class="control-label">Approved Amount</label></td>
                 <td colspan="2"><?php echo $po_row['approved_amt'];?></td>
                 <td><label class="control-label">&nbsp;</label></td>
                 <td>&nbsp;</td>
                 </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
  <div class="panel panel-info table-responsive">
      <div class="panel-heading">Approval Action</div>
      <div class="panel-body">
        <?php if($po_row['status']=="Pending" || $po_row['status']=="Hold" || $po_row['status']=="Esclate to HOD"){ ?>
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="50%"><label class="control-label">Approved Amount <span class="red_small">*</span></label></td>
                <td width="50%">
                 <input name="approved_amt" id="approved_amt" type="text" class="required form-control number" required value="<?php echo $po_row['total_amt'];?>" style="width:300px;"/>
                </td>
              </tr>
              <tr>
                <td width="50%"><label class="control-label">Action Taken <span class="red_small">*</span></label></td>
                <td width="50%">
                 <select name="actiontaken" id="actiontaken" class="required form-control" required style="width:300px;">
                  <option value="Approved">Approved</option>
                  <option value="Approved with deduction">Approved with deduction</option>
                  <option value="Rejected">Rejected</option>
                  <option value="Hold">Hold</option>
                  <option value="Esclate to HOD">Esclate to HOD</option>
                </select>
                </td>
              </tr>
              <tr>
                <td><label class="control-label">Remark <span class="red_small">*</span></label></td>
                <td><textarea name="remark" id="remark" required class="required form-control" onkeypress = "return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical;width:300px;"></textarea></td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Update" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>&nbsp;
                  <input name="refno" id="refno" type="hidden" value="<?=base64_encode($docid)?>"/>
                  <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='ta_da_approval.php?<?=$pagenav?>'">
                  </td>
                </tr>
            </tbody>
          </table>
          <?php }else{ }?>
          <table class="table table-bordered" width="100%"> 
            <thead>
              <tr>
                <th width="20%">Action Date & Time</th>
                <th width="30%">Action Taken By</th>
                <th width="20%">Action</th>
                <th width="30%">Action Remark</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$res_poapp=mysqli_query($link1,"SELECT * FROM approval_activities where ref_no='".$po_row['system_ref_no']."'")or die("ERR1".mysqli_error($link1)); 
			while($row_poapp=mysqli_fetch_assoc($res_poapp)){
			?>
              <tr>
                <td><?php echo $row_poapp['action_date']." ".$row_poapp['action_time'];?></td>
                <td><?php echo getAdminDetails($row_poapp['action_by'],"name",$link1);?></td>
                <td><?php echo $row_poapp['action_taken']?></td>
                <td><?php echo $row_poapp['action_remark']?></td>
              </tr>
              <tr>
                <td colspan="4" align="center"><input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='ta_da_approval.php?<?=$pagenav?>'"></td>
                </tr>
            </tbody>
          </table>
          <?php }?>
      </div><!--close panel body-->
    </div><!--close panel-->
  </div><!--close panel group-->
  </form>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<!-- The Modal -->
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
		<span class="close">&times;</span>
        <p id="img" style="text-align: center;"></p>
    </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>
// Get the modal
var modal = document.getElementById('myModal');

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
function getThisValue(i) {
	var img = $("#image"+i).attr('src');
    $("#img").html('<img src="'+img+'" width="550px"/>');
    modal.style.display = "block";
}
// When the user clicks on <span> (x), close the modal
span.onclick = function() {
	modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
	if (event.target == modal) {
		modal.style.display = "none";
	}
}
        
//        $(document).on('click',"#myBtn1",function(){
//           
//        });
</script>
</body>
</html>