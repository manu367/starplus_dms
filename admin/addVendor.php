<?php
////// Function ID ///////
$fun_id = array("a"=>array(59));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$today=date("Y-m-d");
if($_REQUEST['add']){
$rs2=mysqli_query($link1,"select max(temp) as cnt from vendor_master") or die(mysqli_error($link1));
	$row_cnt=mysqli_fetch_array($rs2);
	$new_temp=$row_cnt[0]+1;
	$id='VNDR'.$new_temp;
	$name=ucfirst($_REQUEST['name']);
	$city=ucfirst($_REQUEST['city']);
	$state=ucfirst($_REQUEST['state']);
	$country=ucfirst($_REQUEST['country']);
	mysqli_query($link1,"INSERT INTO vendor_master SET `id`='".$id."', `temp`='".$new_temp."',name='".$name."',phone='".$_REQUEST['phone']."',city='".$city."',state='".$state."',country='".$country."',address='".$_REQUEST['bill_address1']."' ,email='".$_REQUEST['email']."',status='Active',bill_address='".$_REQUEST['bill_address1']."',ship_address='".$_REQUEST['ship_address1']."',vendor_origin='".$_REQUEST['vendor_origin']."',mode_of_ship='".$_REQUEST['mode_of_ship']."',gstin_no='".$_REQUEST['gst_no']."',tax_reg='".$_REQUEST['tax_reg']."',fax='".$_REQUEST['fax']."',created_date='$today',created_by='".$_SESSION['userid']."',pincode='" . $_REQUEST['pincode'] . "',pan_no='" . $_REQUEST['pan_no'] . "',msme_cert_no='" . $_REQUEST['msme_cert_no'] . "',contact_person='" . $_REQUEST['contact_person'] . "',payment_term='" . $_REQUEST['payment_term'] . "',tcs_applicable='" . $_REQUEST['tcs_applicable'] . "',gst_registration='" . $_REQUEST['gst_registration'] . "',proprietor_type='" . $_REQUEST['proprietor_type'] . "',bank_ac_no='" . $_REQUEST['bank_ac_no'] . "',bank_name='" . $_REQUEST['bank_name'] . "',bank_branch='" . $_REQUEST['bank_branch'] . "',bank_ifsc='" . $_REQUEST['bank_ifsc'] . "'")or die("error".mysqli_error($link1));
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
 <script language="JavaScript" src="../js/ajax.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script>
	$(document).ready(function(){
        $("#form1").validate();
    });
 </script>
 <title>Add New Vendor</title>
 <script language="javascript" type="text/javascript">
/////////////////////////////// getting city ///////////////////////////////////////////////////////////////////
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
      <h2 align="center">Enter The New Vendor Details</h2>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="form1" class="form-horizontal" action="" method="post" id="form1">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Vendor Name<span class="red_small">*</span></label>
              <div class="col-md-7">
          		<input name="name" id="name" type="text" class="form-control required mastername" required />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Contact Person<span class="red_small">*</span></label>
                <div class="col-md-7">
                  <input name="contact_person" id="contact_person" type="text" class="form-control mastername required" required />
                </div>
              </div>
            
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Email:<span class="red_small">*</span></label>
              <div class="col-md-7">
                 <input name="email"  id="email" type="text" class="form-control required email" required/>
                 
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Contact No<span class="red_small">*</span></label>
              <div class="col-md-7">
                
          		<input name="phone" type="text" class="form-control digits required" minlength="10" maxlength="15" required />
      
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
                  <input name="pincode" id="pincode" type="text" class="form-control digits required" required minlength="6" maxlength="6" />
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
                    <option data-tokens="<?php echo $br['state'];?>" value="<?php echo $br['state'];?>"><?php echo $br['state'];?></option>
                    <?php }?>
                  </select>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-4 control-label">City<span class="red_small">*</span></label>
              <div class="col-md-7" id="citydiv">
             <select name="city" id="city" class="form-control required selectpicker" required data-live-search="true" >
                    <option value="">--Select City--</option>
					</select>
              </div>
            </div>         
          </div>
		   <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Billing  Address<span class="red_small">*</span></label>
              <div class="col-md-7">
               <textarea name="bill_address1" cols="40" rows="2" required class="form-control required addressfield"></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Shipping Address</label>
              <div class="col-md-7">
               <textarea name="ship_address1" cols="40" rows="2" class="form-control addressfield"></textarea>
            </div>
          </div>
             </div>
             <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Vendor Origin<span class="red_small">*</span></label>
              <div class="col-md-7">
               <select name="vendor_origin" id="vendor_origin" required class="form-control required">
                                 <option value="">--Please Select--</option>
                                 <option value="Domestic">Domestic</option> 
                            
        </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Mode of Shipment<span class="red_small">*</span></label>
              <div class="col-md-7">
               <select name="mode_of_ship" id="mode_of_ship" required class="form-control required">
          <option value="">--Please Select--</option>
          <option value="By Air">By Air</option>
          <option value="By Road">By Road</option>
          <option value="By Train">By Train</option>
          <option value="By Ship">By Ship</option>
        </select>
            </div>
          </div>
             </div>
          <div class="form-group">
              <div class="col-md-6"><label class="col-md-5 control-label">PAN Number</label>
                <div class="col-md-7">
                  <input name="pan_no" id="pan_no" type="text" class="form-control alphanumeric" minlength="10" maxlength="10" />
                </div>
              </div>
              <div class="col-md-6"><label class="col-md-4 control-label">GST No.</label>
                <div class="col-md-7">
                  <input name="gst_no" id="gst_no" type="text" class="form-control required alphanumeric" required minlength="15" maxlength="15" />
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
                  <input name="msme_cert_no" id="msme_cert_no" type="text" class="form-control alphanumeric" />
                </div>
              </div>
              <div class="col-md-6"><label class="col-md-4 control-label">GST Registration</label>
                <div class="col-md-7">
                  <select name="gst_registration" id="gst_registration" class="form-control">
                    <option value="">--Please Select--</option>
                    <option value="Regular">Regular</option>
                    <option value="Composition">Composition</option>
                    <option value="Unregistered">Unregistered</option>
                    <option value="Consumer">Consumer</option>
                  </select>
                </div>
              </div>
              
            </div>
            <!-- end -->

            <!-- start -->
            <div class="form-group">
              <div class="col-md-6"><label class="col-md-5 control-label">Payment Terms</label>
                <div class="col-md-7">
                  <textarea name="payment_term" id="payment_term" cols="40" rows="2" class="form-control addressfield"></textarea>
                </div>
              </div>
              <div class="col-md-6"><label class="col-md-4 control-label">Account Number</label>
                <div class="col-md-7">
                  <input name="bank_ac_no" id="bank_ac_no" type="text" class="form-control alphanumeric" minlength="5" maxlength="20">
                </div>
              </div>
            </div>
            <!-- end -->
            <!-- start -->
            <div class="form-group">
              <div class="col-md-6"><label class="col-md-5 control-label">Bank Name</label>
                <div class="col-md-7">
                  <input name="bank_name" type="text" class="form-control mastername"/>
                </div>
              </div>
              <div class="col-md-6"><label class="col-md-4 control-label">Branch Location</label>
                <div class="col-md-7">
                  <input name="bank_branch" id="bank_branch" type="text" class="form-control addressfield"/>
                </div>
              </div>
            </div>
            <!-- end -->

            <!-- start -->
            <div class="form-group">
              <div class="col-md-6"><label class="col-md-5 control-label">IFSC Code </label>
                <div class="col-md-7">
                  <input name="bank_ifsc" id="bank_ifsc" class="form-control alphanumeric" minlength="5" maxlength="20"/>
                </div>
              </div>
			 <div class="col-md-6"><label class="col-md-4 control-label">Proprietor Type</label>
                <div class="col-md-7">
                  <input name="proprietor_type" id="proprietor_type" type="text" class="form-control mastername" />
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
                    <option value="Y">Y</option>
                    <option value="N">N</option>
                  </select>
                </div>
              </div>
            </div>
             
            
             
          <div class="form-group">
            <div class="col-md-12" align="center">
              
              <input type="submit" class="btn btn-primary" name="add" id="add" value="ADD" title="Add Vendor" <?php if($_POST['add']=='ADD'){?>disabled<?php }?>>
             
              
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