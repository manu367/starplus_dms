<?php
require_once("../config/config.php");
$getid=base64_decode($_REQUEST['id']);
////// get details of selected product////
$res_bill=mysqli_query($link1,"SELECT * FROM billing_master where challan_no='".$getid."'")or die(mysqli_error($link1));
$row=mysqli_fetch_array($res_bill);
////// final submit form ////
if($_POST){
  @extract($_POST);	
  //// press update button
  if($_POST['Submit']=="Update"){
  
  include("../mail/mail_invoice.php");
	/*mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg="";
	//// update invoice for dispatch	
      $query1="UPDATE billing_master set status='Dispatched', diesel_code='".$couriername."' ,docket_no='".$docketno."', dc_date='".$dispatchdate."',dc_time='".$currtime."',disp_rmk='".$remark."',trans_mode='".$transmode."',deliv_addrs='".$deliveryaddrs."',logistic_person='".$contactperson."',logistic_contact='".$contactno."',vehical_no='".$carrierno."' ,ewayno = '".$ewayno."'  where challan_no='".$getid."'";

	$result = mysqli_query($link1,$query1);
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code1:";
    }
	 ////// insert in activity table////
	 $action=$row['type']." ".$row['document_type'];
	$flag=dailyActivity($_SESSION['userid'],$getid,$action,"DISPATCH",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "You have successfully updated logistic details for invoice ".$getid;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed ".$err_msg.". Please try again.";
	} 
    mysqli_close($link1);
   }else{
	////// return message
	$msg="Something went wrong. Please try again.";
   }
	///// move to parent page
    header("Location:updateInvLogistic.php?msg=".$msg."".$pagenav);
	exit;	 */
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
	// When the document is ready
$(document).ready(function () {
	$('#dispatchdate').datepicker({
		format: "yyyy-mm-dd",
		startDate: "<?=$row['sale_date']?>",
        endDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
});
 </script>
<style>
.red_small{
	color:red;
}
</style>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
 <link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/bootstrap-datepicker.js"></script>
 <script language="javascript" type="text/javascript">
//////// Enter Number Only/////////
function onlyNumbers(evt){  
var e = event || evt; // for trans-browser compatibility
var charCode = e.which || e.keyCode;  
if (charCode > 31 && (charCode < 48 || charCode > 57) &&  charCode!=43)
{
return false;
}
return true;
}
///////Phone No. length////
function phoneN(){
doc=document.frm1.contactno;
if(doc.value!=''){
   if((isNaN(doc.value)) || (doc.value.length !=10)){
      alert("Enter Valid Mobile No. Mobile No. must be in 10 digit.");
      doc.value='';
      doc.focus();
      doc.select();
   }
}
}
</script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-car"></i>&nbsp;&nbsp;Update Dispatch</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post" >
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Dispatch To<span class="red_small">*</span></label>
              <div class="col-md-4">
              <input type="text" name="fromlocation" id="fromlocation" class="form-control"  disabled value="<?php echo $row['from_location']; ?>"  required />
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Invoice No.<span class="red_small">*</span></label>
              <div class="col-md-4">
              <input type="text" name="challanno" id="challanno" class="form-control" disabled value="<?php echo $row['challan_no']; ?>"  required />
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Invoice Date<span class="red_small">*</span></label>
              <div class="col-md-4">
              <input type="text" name="invdate" id="invdate" class="form-control"  disabled value="<?php echo $row['sale_date']; ?>"  required />
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Invoice Value<span class="red_small">*</span></label>
              <div class="col-md-4">
              <input type="text" name="invamt" id="invamt" class="form-control"  disabled value="<?php echo $row['total_cost']; ?>"  required />
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Logistic Name<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select name="couriername" id="couriername" class="required form-control" required>
                <option value="">--Select--</option>
                <?php
				$res_courier=mysqli_query($link1,"select couriername,couriercode,city,state from diesl_master where status='Active'");
				while($row_courier=mysqli_fetch_assoc($res_courier)){
				?>
                <option value="<?=$row_courier['couriercode']?>"><?=$row_courier['couriername'].",".$row_courier['city'].",".$row_courier['state']?></option>
                <?php
				}
				?>
              </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Docket No.<span class="red_small">*</span></label>
              <div class="col-md-4">
              <input type="text" name="docketno" id="docketno" class="required form-control" required />
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Dispatch Date<span class="red_small">*</span></label>
              <div class="col-md-4 input-append date">
                  <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="dispatchdate"  id="dispatchdate" style="width:280px;"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                  </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Logistic Person<span class="red_small">*</span></label>
              <div class="col-md-4">
              <input type="text" name="contactperson" id="contactperson" class="required form-control" required />
              </div>
            </div>
          </div>
		   <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Transportation Mode<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select name="transmode" id="transmode" class="form-control" >
                <option value="">--Select--</option>
               <option value="By Road">By Road</option>
			   <option value="By Air">By Air</option>
			   <option value="By Transport">By Transport</option>
			    <option value="By Hand Held">By Hand Held</option>
              </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Contact No.<span class="red_small">*</span></label>
              <div class="col-md-4">
              <input type="text" name="contactno" id="contactno" class="required form-control"  onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" required />
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Carrier No.<span class="red_small">*</span></label>
              <div class="col-md-4">
              <input type="text" name="carrierno" id="carrierno" class="required form-control" required />
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">E-Way Bill No</label>
              <div class="col-md-4">
              <input type="text" name="ewayno" id="ewayno" class=" form-control"  />
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Delivery Address<span class="red_small">*</span></label>
              <div class="col-md-4">
              <textarea name="deliveryaddrs" id="deliveryaddrs" class="required form-control" required><?=$row['deliv_addrs']?></textarea>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Remark<span class="red_small">*</span></label>
              <div class="col-md-4">
              <textarea name="remark" id="remark" class="required form-control" required></textarea>
              </div>
            </div>
          </div>
         <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Update" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
              &nbsp;&nbsp;&nbsp;<input type="hidden" name="id" id="id" value="<?=base64_encode($row['challan_no'])?>"/>
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='updateInvLogistic.php?<?=$pagenav?>'">
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
