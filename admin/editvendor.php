<?php
////// Function ID ///////
$fun_id = array("a"=>array(59));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$rs2=mysqli_query($link1,"select * from vendor_master where sno='".$_REQUEST['sno']."'") or die(mysqli_error($link1));
$row=mysqli_fetch_array($rs2);
$today=date("Y-m-d");
if($_REQUEST['Update']){
	$name=ucfirst($_REQUEST['name']);
	$city=ucfirst($_REQUEST['city']);
	$state=ucfirst($_REQUEST['state']);
	$country=ucfirst($_REQUEST['country']);
mysqli_query($link1,"update vendor_master set name='".$name."',phone='".$_REQUEST['phone']."',email='".$_REQUEST['email']."',city='".$city."',state='".$state."',country='".$country."',address='".$_REQUEST['bill_address1']."',bill_address='".$_REQUEST['bill_address1']."',ship_address='".$_REQUEST['ship_address1']."',gstin_no='".$_REQUEST['gst_no']."',tax_reg='".$_REQUEST['tax_reg']."',fax='".$_REQUEST['fax']."',vendor_origin='".$_REQUEST['vendor_origin']."',mode_of_ship='".$_REQUEST['mode_of_ship']."',update_date='".$today."',updated_by='".$_SESSION['userid']."',pincode='" . $_REQUEST['pincode'] . "',pan_no='" . $_REQUEST['pan_no'] . "',msme_cert_no='" . $_REQUEST['msme_cert_no'] . "',contact_person='" . $_REQUEST['contact_person'] . "',payment_term='" . $_REQUEST['payment_term'] . "',tcs_applicable='" . $_REQUEST['tcs_applicable'] . "',gst_registration='" . $_REQUEST['gst_registration'] . "',proprietor_type='" . $_REQUEST['proprietor_type'] . "',bank_ac_no='" . $_REQUEST['bank_ac_no'] . "',bank_name='" . $_REQUEST['bank_name'] . "',bank_branch='" . $_REQUEST['bank_branch'] . "',bank_ifsc='" . $_REQUEST['bank_ifsc'] . "' where sno='".$_REQUEST['sno']."'")or die("error".mysqli_error($link1));
echo "<center><br>Vendor Details has been Updated.<br></center>";
header("Location:vendor_master.php");
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
 <title>Edit Vendor Details</title>
 <script>
$(document).ready(function(){
	$("#form1").validate();
});
function getCity(val){
    if(val!="")
	{
	var strSubmit ="action=getCity&value="+val;
	var strURL = "../includes/getField.php";	
	var strResultFunc="displayCity";
	xmlhttpPost(strURL,strSubmit,strResultFunc);
	return false;	
	}	
}
function displayCity(result){
    if(result!="" && result!=0){
	document.getElementById('citydiv').innerHTML=result;
    }
}
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
      <h2 align="center">Edit in Vendor Details</h2>
      <div class="form-group"  id="page-wrap" >
          <form name="form1" id="form1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Vendor Name<span class="red_small">*</span></label>
              <div class="col-md-7">
          		<input name="name" id="name" type="text" class="form-control required mastername" required value="<?=$row['name']?>"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Contact Person<span class="red_small">*</span></label>
                <div class="col-md-7">
                  <input name="contact_person" id="contact_person" type="text" class="form-control mastername required" required value="<?= $row['contact_person'] ?>"/>
                </div>
              </div>
            
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Email:<span class="red_small">*</span></label>
              <div class="col-md-7">
                 <input name="email"  id="email" type="text" class="form-control required email" required value="<?= $row['email'] ?>"/>
                 
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Contact No<span class="red_small">*</span></label>
              <div class="col-md-7">
                
          		<input name="phone" type="text" class="form-control digits required" minlength="10" maxlength="15" required value="<?= $row['phone'] ?>"/>
      
              </div>
            </div>
          </div>
          <div class="form-group">
              <div class="col-md-6"><label class="col-md-5 control-label">Country<span class="red_small">*</span></label>
                  <div class="col-md-7">
                    <input name="country" type="text" value="India" readonly class="form-control required" required/>
                  </div>
                </div>
              <div class="col-md-6"><label class="col-md-4 control-label">PIN Code<span class="red_small">*</span></label>
                <div class="col-md-7">
                  <input name="pincode" id="pincode" type="text" class="form-control digits required" required minlength="6" maxlength="6" value="<?= $row['pincode'] ?>"/>
                </div>
              </div>
              
            </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">State<span class="red_small">*</span></label>
              <div class="col-md-7" >
                 <select name="state" id="state" class="form-control required selectpicker" required data-live-search="true" onChange="return getCity(this.value);">
                    <option value="">--None--</option>
                    <?php 
				$state="select distinct(state) from district_master where status='A'";
			        $check1=mysqli_query($link1,$state);
			        while($br = mysqli_fetch_array($check1)){?>
                    <option data-tokens="<?php echo $br['state'];?>" value="<?php echo $br['state'];?>" <?php if($row['state']==$br['state']){ echo "selected";}?>><?php echo $br['state'];?></option>
                    <?php }?>
                  </select>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-4 control-label">City<span class="red_small">*</span></label>
              <div class="col-md-7" id="citydiv">
             <select name="city" id="city" class="form-control required selectpicker" required data-live-search="true" >
                    <option value="">--Select City--</option>
					<?php $model_query="SELECT distinct(city) FROM district_master where state='".$row['state']."' order by city";
                    $check1=mysqli_query($link1,$model_query);
                    while($br = mysqli_fetch_array($check1)){
                    ?>
                    <option value='<?=$br['city']?>'<?php if($row['city']==$br['city']){ echo "selected";}?>><?php echo $br['city'];?></option>
                    <?php }?>
              </select>
              </div>
            </div>         
          </div>
		   <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Billing  Address<span class="red_small">*</span></label>
              <div class="col-md-7">
               <textarea name="bill_address1" cols="40" rows="2" required class="form-control required addressfield"><?= $row['bill_address'] ?></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Shipping Address</label>
              <div class="col-md-7">
               <textarea name="ship_address1" cols="40" rows="2" class="form-control addressfield"><?= $row['ship_address'] ?></textarea>
            </div>
          </div>
             </div>
             <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Vendor Origin<span class="red_small">*</span></label>
              <div class="col-md-7">
               <select name="vendor_origin" id="vendor_origin" required class="form-control required">
                                 <option value="">--Please Select--</option>
                                 <option value="Domestic"<?php if ($row['vendor_origin'] == 'Domestic') echo 'selected';?>>Domestic</option> 
                            
        </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Mode of Shipment<span class="red_small">*</span></label>
              <div class="col-md-7">
               <select name="mode_of_ship" id="mode_of_ship" required class="form-control required">
          	<option value="">--Please Select--</option>
          	<option value="By Air" <?php if ($row['mode_of_ship'] == 'By Air') echo 'selected'; ?>>By Air</option>
            <option value="By Road" <?php if ($row['mode_of_ship'] == 'By Road') echo 'selected'; ?>>By Road</option>
            <option value="By Train" <?php if ($row['mode_of_ship'] == 'By Train') echo 'selected'; ?>>By Train</option>
            <option value="By Ship" <?php if ($row['mode_of_ship'] == 'By Ship') echo 'selected'; ?>>By Ship</option>
        </select>
            </div>
          </div>
             </div>
          <div class="form-group">
              <div class="col-md-6"><label class="col-md-5 control-label">PAN Number</label>
                <div class="col-md-7">
                  <input name="pan_no" id="pan_no" type="text" class="form-control alphanumeric" minlength="10" maxlength="10" value="<?= $row['pan_no'] ?>"/>
                </div>
              </div>
              <div class="col-md-6"><label class="col-md-4 control-label">GST No.</label>
                <div class="col-md-7">
                  <input name="gst_no" id="gst_no" type="text" class="form-control required alphanumeric" required minlength="15" maxlength="15" value="<?= $row['gstin_no'] ?>"/>
                </div>
              </div>
              
              
            </div>
            <!-- end -->


            <!-- start -->
            
            <!-- end -->
            <!-- start -->
            <div class="form-group">
              <div class="col-md-6"><label class="col-md-5 control-label">MSME Certificate No.</label>
                <div class="col-md-7">
                  <input name="msme_cert_no" id="msme_cert_no" type="text" class="form-control alphanumeric" value="<?= $row['msme_cert_no'] ?>"/>
                </div>
              </div>
              <div class="col-md-6"><label class="col-md-4 control-label">GST Registration</label>
                <div class="col-md-7">
                  <select name="gst_registration" id="gst_registration" class="form-control">
                    <option value="">--Please Select--</option>
                    <option value="Regular" <?php if ($row['gst_registration'] == 'Regular') echo 'selected'; ?>>Regular</option>
                    <option value="Composition" <?php if ($row['gst_registration'] == 'Composition') echo 'selected'; ?>>Composition</option>
                    <option value="Unregistered" <?php if ($row['gst_registration'] == 'Unregistered') echo 'selected'; ?>>Unregistered</option>
                    <option value="Consumer" <?php if ($row['gst_registration'] == 'Consumer') echo 'selected'; ?>>Consumer</option>
                  </select>
                </div>
              </div>
              
            </div>
            <!-- end -->

            <!-- start -->
            <div class="form-group">
              <div class="col-md-6"><label class="col-md-5 control-label">Payment Terms</label>
                <div class="col-md-7">
                  <textarea name="payment_term" id="payment_term" cols="40" rows="2" class="form-control addressfield"><?= $row['payment_term'] ?></textarea>
                </div>
              </div>
              <div class="col-md-6"><label class="col-md-4 control-label">Account Number</label>
                <div class="col-md-7">
                  <input name="bank_ac_no" id="bank_ac_no" type="text" class="form-control alphanumeric" minlength="5" maxlength="20" value="<?= $row['bank_ac_no'] ?>"/>
                </div>
              </div>
            </div>
            <!-- end -->
            <!-- start -->
            <div class="form-group">
              <div class="col-md-6"><label class="col-md-5 control-label">Bank Name</label>
                <div class="col-md-7">
                  <input name="bank_name" type="text" class="form-control mastername" value="<?= $row['bank_name'] ?>"/>
                </div>
              </div>
              <div class="col-md-6"><label class="col-md-4 control-label">Branch Location</label>
                <div class="col-md-7">
                  <input name="bank_branch" id="bank_branch" type="text" class="form-control addressfield" value="<?= $row['bank_branch'] ?>"/>
                </div>
              </div>
            </div>
            <!-- end -->

            <!-- start -->
            <div class="form-group">
              <div class="col-md-6"><label class="col-md-5 control-label">IFSC Code </label>
                <div class="col-md-7">
                  <input name="bank_ifsc" id="bank_ifsc" class="form-control alphanumeric" minlength="5" maxlength="20" value="<?= $row['bank_ifsc'] ?>"/>
                </div>
              </div>
			 <div class="col-md-6"><label class="col-md-4 control-label">Proprietor Type</label>
                <div class="col-md-7">
                  <input name="proprietor_type" id="proprietor_type" type="text" class="form-control mastername" value="<?= $row['proprietor_type'] ?>"/>
                </div>
              </div>
              
            </div>
            <!-- end -->
            <!-- start -->
            <div class="form-group">

              <div class="col-md-6"><label class="col-md-5 control-label">TCS Applicable </label>
                <div class="col-md-7">
                  <select name="tcs_applicable" id="tcs_applicable" class="form-control">
                    <option value="">--Please Select--</option>
                    <option value="Y" <?php if ($row['tcs_applicable'] == 'Y') echo 'selected'; ?>>Y</option>
                    <option value="N" <?php if ($row['tcs_applicable'] == 'N') echo 'selected'; ?>>N</option>
                  </select>
                </div>
              </div>
            </div>
             
          
          <div class="form-group">
            <div class="col-md-12" align="center">
              
              <input type="submit" class="btn btn-primary" name="Update" id="" value="Update" title="Edit Vendor" <?php if($_POST['Update']=='Update'){?>disabled<?php }?>>
             
              
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='vendor_master.php'">
            </div>
          </div>
    </form>
      </div>

    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
?>
</body>
</html>