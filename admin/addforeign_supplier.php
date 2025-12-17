<?php
////// Function ID ///////
$fun_id = array("a"=>array(59));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$today=date("Y-m-d");
if($_REQUEST['add']){
$rs2=mysqli_query($link1,"select max(temp) as cnt from vendor_master") or die(mysql_error());
	$row_cnt=mysqli_fetch_array($rs2);
	$new_temp=$row_cnt[0]+1;
	$id='VNDR'.$new_temp;
	$name=ucfirst($_REQUEST['name']);
	$city=ucfirst($_REQUEST['city']);
	$state=ucfirst($_REQUEST['state']);
	$country=ucfirst($_REQUEST['country']);
mysqli_query($link1,"insert into vendor_master set `id`='$id', `temp`='$new_temp',name='$name',phone='$_REQUEST[phone]',city='$city',state='$state',country='$country',address='".$_REQUEST['address']."' ,email='".$_REQUEST['email']."',status='Active',bill_address='".$_REQUEST['bill_address1']."',ship_address='".$_REQUEST['ship_address1']."',vendor_origin='".$_REQUEST['vendor_origin']."',mode_of_ship='".$_REQUEST['mode_of_ship']."',gstin_no='".$_REQUEST['gst_no']."',tax_reg='".$_REQUEST['tax_reg']."',fax='".$_REQUEST['fax']."',created_date='$today',created_by='".$_SESSION['userid']."'")or die("error".mysql_error());
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
	$(document).ready(function(){
		$('#myTable').dataTable();
	});
	$(document).ready(function(){
        $("#frm1").validate();
    });
 </script>
 <title>Add New Vendor</title>
 <script language="javascript" type="text/javascript">
window.focus();
function chk_data()
{
	if((document.form1.name.value)=="")
    {
	alert("Please Enater the vendor Name.");
	document.form1.name.focus();
	return false;
    }
if((document.form1.phone.value)=="")
    {
	alert("Please Enter Contact No .");
	document.form1.phone.focus();
	return false;
    }
if((document.form1.email.value)=="")
    {
	alert("Please Enter Email Id .");
	document.form1.email.focus();
	return false;
    }	
if((document.form1.city.value)=="")
    {
	alert("Please Enter City .");
	document.form1.city.focus();
	return false;
    }
if((document.form1.state.value)=="")
    {
	alert("Please Enter State .");
	document.form1.state.focus();
	return false;
    }
if((document.form1.country.value)=="")
    {
	alert("Please Enter Country.");
	document.form1.country.focus();
	return false;
    }
if((document.form1.address.value)=="")
    {
	alert("Please Enter Address.");
	document.form1.address.focus();
	return false;
    }
if((document.form1.bill_address1.value)=="")
    {
	alert("Please Enter Billing Address.");
	document.form1.bill_address1.focus();
	return false;
    }
if((document.form1.vendor_origin.value)=="")
    {
	alert("Please Select Vendor Origin.");
	document.form1.vendor_origin.focus();
	return false;
    }
if((document.form1.mode_of_ship.value)=="")
    {
	alert("Please Select Vendor Shipment.");
	document.form1.mode_of_ship.focus();
	return false;
    }
	
}
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
// alert(field);
doc=document.form1.phone;
if(doc.value!=''){
   if((isNaN(doc.value)) || (doc.value.length !=10)){
      alert("Enter Valid Mobile No. Mobile No. must be in 10 digit.");
      doc.value='';
      doc.focus();
      doc.select();
   }
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
      <h2 align="center">Enter The New Vendor Details</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="form1" class="form-horizontal" action="" method="post" id="form1" onSubmit="return chk_data()">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Vendor Name<span class="red_small">*</span></label>
              <div class="col-md-7">
                
          <input name="name" type="text" class="form-control" size="40" value="" required />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Contact No<span class="red_small">*</span></label>
              <div class="col-md-7">
                
          <input name="phone" type="text" class="form-control" size="40" value=""  maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" required />
      
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Email:<span class="red_small">*</span></label>
              <div class="col-md-7">
                 <input name="email"  id="email" type="text" class="form-control" size="40" value="" required  onBlur="return asc_email(this.value);"/>
                 
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-4 control-label">Country<span class="red_small">*</span></label>
              <div class="col-md-7">
                <input name="country" type="text" value="" class="form-control" size="40" required  />
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">State<span class="red_small">*</span></label>
              <div class="col-md-6" >
			  <input name="state" id="state" class="form-control " required  type="text" >                 
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-4 control-label">City<span class="red_small">*</span></label>
              <div class="col-md-6">
             <input name="city" id="city" class="form-control " required  type="text" >             
              </div>
            </div>         
          </div>
		   <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Billing  Address<span class="red_small">*</span></label>
              <div class="col-md-7">
               <textarea name="bill_address1" cols="40" rows="2" required class="form-control"></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Shipping Address</label>
              <div class="col-md-7">
               <textarea name="ship_address1" cols="40" rows="2" class="form-control"></textarea>
            </div>
          </div>
             </div>
             <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Vendor Origin<span class="red_small">*</span></label>
              <div class="col-md-7">
               <select name="vendor_origin" id="vendor_origin" required class="form-control">
                                 <option value="">--Please Select--</option>
                        
                                 <option value="Foreign">Foreign</option>
        </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Mode of Shipment<span class="red_small">*</span></label>
              <div class="col-md-7">
               <select name="mode_of_ship" id="mode_of_ship" required class="form-control">
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
            <div class="col-md-6"><label class="col-md-5 control-label">GST No.</label>
              <div class="col-md-7">
               <input name="gst_no" type="gst_no" value="" class="form-control" size="40" />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Tax Registration</label>
              <div class="col-md-7">
              <input name="tax_reg" type="text" value="" class="form-control" size="40"   />
            </div>
          </div>
             </div>
              <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Fax</label>
              <div class="col-md-7">
               <input name="fax" type="text" value="" class="form-control" size="40" />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Vendor Address<span class="red_small">*</span></label>
              <div class="col-md-7">
               <textarea name="address" cols="40" rows="2" required class="form-control"></textarea>
            </div>
          </div>
             </div>   
             
            
             
          <div class="form-group">
            <div class="col-md-12" align="center">
              
              <input type="submit" class="btn btn-primary" name="add" id="" value="ADD" title="Add Vendor" <?php if($_POST['add']=='ADD'){?>disabled<?php }?>>
             
              
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