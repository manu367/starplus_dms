<?php
require_once("../config/config.php");
$rs2=mysqli_query($link1,"select * from vendor_master where sno='".$_REQUEST['sno']."'") or die(mysqli_error($link1));
$row=mysqli_fetch_array($rs2);
$today=date("Y-m-d");
if($_REQUEST['Update']){
	$name=ucfirst($_REQUEST['name']);
	$city=ucfirst($_REQUEST['city']);
	$state=ucfirst($_REQUEST['state']);
	$country=ucfirst($_REQUEST['country']);
mysqli_query($link1,"update vendor_master set name='$name',phone='".$_REQUEST['phone']."',email='$_REQUEST[email]',city='$city',state='$state',country='$country',address='".$_REQUEST['address']."',bill_address='".$_REQUEST['bill_address1']."',ship_address='".$_REQUEST['ship_address1']."',gstin_no='".$_REQUEST['gst_no']."',tax_reg='".$_REQUEST['tax_reg']."',fax='".$_REQUEST['fax']."',vendor_origin='".$_REQUEST['vendor_origin']."',mode_of_ship='".$_REQUEST['mode_of_ship']."',update_date='$today',updated_by='".$_SESSION['userid']."' where sno='".$_REQUEST['sno']."'")or die("error".mysql_error());
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <title>Edit Vendor Details</title>
 <script>
//////// Enter Number Only/////////
function onlyNumbers(evt){  
var e = event || evt; // for trans-browser compatibility
var charCode = e.which || e.keyCode;  
if (charCode > 31 && (charCode < 48 || charCode > 57))
{
return false;
}
return true;
}
///////Phone No. length////
function phoneN(){
// alert(field);
doc=document.form1.phone;
if((isNaN(doc.value)) || (doc.value.length !=10)){
alert("Enter Valid Mobile No. Mobile No. must be in 10 digit.");
doc.value='';
doc.focus();
doc.select();
}
}
function asc_email(field) {
var x =/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
if (!x.test(field)){
alert("Enter the correct Email Addraess.");
document.getElementById("email").value="";
field.focus();
field.select();
}
}
</script>
<style>
.red_small{
	color:red;
}
</style>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center">Edit in Vendor Details</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" >
          <form  name="a" class="form-horizontal" action="" method="post" onSubmit="return chk_data()">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Vendor Name<span class="red_small">*</span></label>
              <div class="col-md-7">
                
          <input name="name" type="text" class="form-control" size="40" value="<?=$row['name']?>" required />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Contact No<span class="red_small">*</span></label>
              <div class="col-md-7">
                
          <input name="phone" type="text" class="form-control" size="40" value="<?=$row['phone']?>"  maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" required />
      
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Email:<span class="red_small">*</span></label>
              <div class="col-md-7">
                 <input name="email"  id="email" type="text" class="form-control" size="40" value="<?=$row['email']?>" required  onBlur="return asc_email(this.value);"/>
                 
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">City<span class="red_small">*</span></label>
              <div class="col-md-7">
               <input name="city" type="text" value="<?=$row['city']?>" class="form-control" size="40" required  />
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">State<span class="red_small">*</span></label>
              <div class="col-md-7">
                <input name="state" type="text" value="<?=$row['state']?>" class="form-control" size="40" required  />
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-4 control-label">Country<span class="red_small">*</span></label>
              <div class="col-md-7">
                <input name="country" type="text" value="<?=$row['country']?>" class="form-control" size="40" required  />
              </div>
            </div>
            
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Billing  Address<span class="red_small">*</span></label>
              <div class="col-md-7">
               <textarea name="bill_address1" cols="40" rows="2" required class="form-control"><?=$row['bill_address']?></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Shipping Address</label>
              <div class="col-md-7">
               <textarea name="ship_address1" cols="40" rows="2" required class="form-control"><?=$row['ship_address']?></textarea>
            </div>
          </div>
             </div>
             <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Vendor Origin<span class="red_small">*</span></label>
              <div class="col-md-7">
               <select name="vendor_origin" id="vendor_origin" required class="form-control">
                                 <option value="">--Please Select--</option>
                                 <option value="Foreign" <?php if($row['vendor_origin']=='Foreign') echo 'selected'; ?>>Foreign</option>
        </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Mode of Shipment<span class="red_small">*</span></label>
              <div class="col-md-7">
               <select name="mode_of_ship" id="mode_of_ship" required class="form-control">
          <option value="">--Please Select--</option>                   
          <option value="By Air" <?php if($row['mode_of_ship']=='By Air') echo 'selected'; ?>>By Air</option>
          <option value="By Road" <?php if($row['mode_of_ship']=='By Road') echo 'selected'; ?>>By Road</option>
          <option value="By Train" <?php if($row['mode_of_ship']=='By Train') echo 'selected'; ?>>By Train</option>
          <option value="By Ship" <?php if($row['mode_of_ship']=='By Ship') echo 'selected'; ?>>By Ship</option>
        </select>
            </div>
          </div>
             </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">GST No.</label>
              <div class="col-md-7">
               <input name="gst_no" type="text" id="gst_no" value="<?=$row['gstin_no']?>" class="form-control" size="40"  />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Tax Registration</label>
              <div class="col-md-7">
              <input name="tax_reg" type="text" value="<?=$row['tax_reg']?>" class="form-control" size="40"   />
            </div>
          </div>
             </div>
              <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Fax</label>
              <div class="col-md-7">
               <input name="fax" type="text" value="<?=$row['fax']?>" class="form-control" size="40"  />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Vendor Address<span class="red_small">*</span></label>
              <div class="col-md-7">
               <textarea name="address" cols="40" rows="2" required class="form-control"><?=$row['address']?></textarea>
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