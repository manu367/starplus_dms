<?php
////// Function ID ///////
$fun_id = array("a"=>array(76));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$accessLocation=getAccessLocation($_SESSION['userid'],$link1);
$getid=base64_decode($_REQUEST['id']);
////// get details of selected city////
$rs=mysqli_query($link1,"select * from customer_master where id='$getid'")or die(mysqli_error($link1));
$row2=mysqli_fetch_array($rs);

$today=date("Y-m-d");
@extract($_POST);
////// if we hit process button
if($_POST){
if($_REQUEST['add']){
////// pick max no. of Customer
     // insert all details of location //
  $sql="Update customer_master set customername='".$name."',status='".$locationstatus."',updateby='".$_SESSION['userid']."',updatedate='".$today."' , gstin = '".$gstin."'  where customerid='".$customercode."'";
   $result=mysqli_query($link1,$sql);
   //// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code0.1:";
    }
   ////// insert in activity table////
   $flag=dailyActivity($_SESSION['userid'],$customercode,"CUSTOMER","EDIT",$ip,$link1,$flag);
header("Location:customer_details.php");
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
	alert("Please Enater the Customer Name.");
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
	alert("Please Select City .");
	document.form1.city.focus();
	return false;
    }
if((document.form1.state.value)=="")
    {
	alert("Please Select State .");
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
if((document.form1.location.value)=="")
    {
	alert("Please Select Location.");
	document.form1.location.focus();
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
		//alert(result);
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
      <h2 align="center">Edit Customer Details</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="form1" class="form-horizontal" action="" method="post" id="form1" onSubmit="return chk_data()">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Customer Name<span class="red_small">*</span></label>
              <div class="col-md-7">
                
          <input name="name"  id= "name"  type="text" class="form-control" size="40" value="<?=$row2['customername']?>" required />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Contact No<span class="red_small">*</span></label>
              <div class="col-md-7">
                
          <input name="phone" id = "phone" type="text" class="form-control" size="40" value="<?=$row2['contactno']?>"  maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" readonly/>
      
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Email:<span class="red_small">*</span></label>
              <div class="col-md-7">
                 <input name="email"  id="email" type="text" class="form-control" size="40" value="<?=$row2['emailid']?>" required readonly onBlur="return asc_email(this.value);"/>
                 
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-4 control-label">Country<span class="red_small">*</span></label>
              <div class="col-md-7">
                <input name="country" id="country" type="text" readonly value="<?=$row2['country']?>" class="form-control" size="40" required  />
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">State<span class="red_small">*</span></label>
              <div class="col-md-7" >
               <input type="text" name="state" id="state" class="form-control" value="<?=$row2['state']?>" readonly />
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-4 control-label">City<span class="red_small">*</span></label>
            <div class="col-md-7">
               <input type="text" name="city" id="city" class="form-control" value="<?=$row2['city']?>" readonly />
              </div>
            </div>         
          </div> 
		  
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Location<span class="red_small">*</span></label>
              <div class="col-md-7" >
			  <?php $sql_parent = "select name , city, state,id_type,asc_code from asc_master where asc_code='".$row2['mapplocation']."'";
                    $res_parent = mysqli_query($link1, $sql_parent);
					$party_det = mysqli_fetch_array($res_parent);?>
                <input type="text" name="location" id="location" class="form-control" value="<?= $party_det['name'] . " | " . $party_det['asc_code'] ?>" readonly />
               
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-4 control-label">Address<span class="red_small">*</span></label>
              <div class="col-md-7">
              <textarea name="address" id="address" class="form-control required" required onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);" readonly  onContextMenu="return false" style="resize:vertical"><?=$row2['address']?></textarea>
                
              </div>
            </div>   
          </div> 
          <div class="form-group">
           <div class="col-md-6"><label class="col-md-5 control-label"><strong>Customer Type<span class="red_small">*</span></strong></label>
            <div class="col-md-7">
               <input type="text" name="cat" id="cat" class="form-control" value="<?=$row2['category']?>" readonly />
                </div>
                </div>
            <div class="col-md-6"><label class="col-md-4 control-label"><strong>Customer Status<span class="red_small">*</span></strong></label>
            <div class="col-md-7">
		      <select name="locationstatus" id="locationstatus" class="form-control">
                    <option value="Active"<?php if($row2['status']=='Active'){ echo "selected";}?>>Active</option>
                    <option value="deactive"<?php if($row2['status']=='deactive'){ echo "selected";}?>>Deactive</option>
                </select><input type="hidden" name="customercode" id="customercode" value="<?=$row2['customerid']?>">
                </div>
                </div>
				</div>
			
<?php if($row2['gstin'] == ''){?>	
				<div class="form-group">
           <div class="col-md-6"><label class="col-md-5 control-label"><strong>GSTIN</strong></label>
            <div class="col-md-7">
               <input type="text" name="gstin" id="gstin" class="alphanumeric form-control " minlength="15" maxlength="15" value="<?=$row2['gstin']?>"  />
                </div>
                </div>
            <div class="col-md-6"><label class="col-md-4 control-label"><strong></strong></label>
            <div class="col-md-7">
		      
                </div>
                </div></div>
<?php }?>
				
          <div class="form-group">
            <div class="col-md-12" align="center">
              
              <input type="submit" class="btn btn-primary" name="add" id="" value="EDIT" title="Add Vendor" <?php if($_POST['add']=='EDIT'){?>disabled<?php }?>>
             
              
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='customer_details.php'">
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