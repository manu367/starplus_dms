<?php
require_once("../config/config.php");
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='edit'){
	$sel_usr="select * from admin_users where username='$_REQUEST[id]' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
   if ($_POST['add']=='ADD'){
	$query_code="select MAX(uid) as qc from admin_users";
    $result_code=mysqli_query($link1,$query_code)or die("error2".mysqli_error($link1));
    $arr_result2=mysqli_fetch_array($result_code);
    $code_id=$arr_result2[0];
    $pad=str_pad(++$code_id,3,"0",STR_PAD_LEFT);	
    $admiCode=substr(strtoupper(BRANDNAME),0,2)."USR".$pad; 
	//$pwd=$admiCode."@123";

	$usr_add="INSERT INTO admin_users set username ='".$admiCode."',password ='".$pwd."',name= '".$usrname."',utype='".$u_type."',phone='".$phone."',emailid= '".$email."',status='".$status."',createdate='".date("Y-m-d H:i:s")."'";

    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1)); 
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$admiCode,"ADMIN USER","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
	////// return message
	$msg="You have successfully created an user with ref. no. ".$admiCode;
   }
   else if ($_POST['upd']=='Update'){
    $usr_upd="update admin_users set password ='".$pwd."' ,name= '".$usrname."',utype='".$u_type."',phone= '".$phone."',emailid= '".$email."',status='".$status."',updatedate='".date("Y-m-d H:i:s")."' where username = '".$usrid2."'";
    $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$usrid2,"ADMIN USER","UPDATE",$ip,$link1,"");
	////// return message
	$msg="You have successfully updated user details for ".$usrid2;
   }
   ///// move to parent page
    header("location:adminusermgt.php?msg=".$msg."".$pagenav);
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
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
});
$(document).ready(function(){
        $("#frm1").validate();
});
</script>
<script src="../js/frmvalidate.js"></script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script language="javascript" type="text/javascript">
function checkPWD(val){
  var val;
  var upperCase= new RegExp('[A-Z]');
  var lowerCase= new RegExp('[a-z]');
  var numbers = new RegExp('[0-9]');
 
  if(val.match(upperCase) && val.match(lowerCase) &&  val.match(numbers))  
  {
	  //$("#passwordErrorMsg").html("OK")
	  $("#passwordErrorMsg").html("")
  
  }
  else
  {
	  $("#passwordErrorMsg").html("Your password must be between 6 and 20 characters. It must contain a mixture of upper and lower case letters, and at least one number or symbol.");
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
doc=document.frm1.phone;
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
      <h2 align="center"><i class="fa fa-users"></i> Admin/Users Details</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">User Name</label>
              <div class="col-md-5">
                 <input type="text" name="usrname" class="required form-control" id="usrname" value="<?=$sel_result['name']?>" required/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label"><?php if($_REQUEST['op']!='add'){ ?>User Id<?php }?></label>
              <div class="col-md-5">
               <?php if($_REQUEST['op']!='add'){ ?> <input type="text" name="uid" id="uid" class="form-control" value="<?php echo $sel_result['username'];?> " required readonly/><?php }?>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Password</label>
              <div class="col-md-5">
                 <input type="text" name="pwd" class="required form-control" id="pwd" value="<?=$sel_result['password']?>" required/>
                 <span id="passwordErrorMsg" style="color:#F00"></span>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">User Type</label>
              <div class="col-md-5">
                <select name='u_type' id='u_type' class="required form-control" required>
                    <option value="">--Please Select--</option>
                    <?php
					$res_utype=mysqli_query($link1,"select * from usertype_master where status='A' order by refid")or die("erro1".mysqli_error($link1));
					while($row_utype=mysqli_fetch_assoc($res_utype)){
					?>
                    <option value="<?=$row_utype[refid]?>"<?php if($sel_result['utype'] ==$row_utype[refid]) { echo 'selected'; }?>><?=$row_utype[typename]?></option>
                    <?php
					}
					?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Mobile No.</label>
              <div class="col-md-5">
                 <input type="text" name="phone" id="phone" class="digits form-control" maxlength="11"   value="<?=$sel_result['phone']?>"  onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" required/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Email-Id</label>
              <div class="col-md-5">
                <input type="email" class="form-control email" name="email" id="email" value="<?=$sel_result['emailid']?>"  required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Status</label>
              <div class="col-md-5">
                 <select name='status' id='status' class="form-control">
                    <option value="active" <?php if($sel_result['status'] =='active') {echo 'selected'; }?>>Activate</option>
                    <option value="deactive" <?php if($sel_result['status'] =='deactive') {echo 'selected'; }?>>Deactivate</option>
                 </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="col-md-5">
                &nbsp;
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='add'){ ?>
              <input type="submit" class="btn btn-primary" name="add" id="add" value="ADD" title="Add User">
              <?php }else{
			  if($_SESSION['utype']==7 && $sel_result['username']==$_SESSION['userid']) { ?>
			  <input name="usr_permission" type="button" id="usr_permission" class="btn btn-primary" onClick="window.location='update_permission_user.php?userid=<?=$sel_result['username']?>&userlevel=<?=$sel_result['utype']?>&u_name=<?=$sel_result['name']?>&page=<?=$page?>&srch=<?=$_REQUEST[srch]?><?=$pagenav?>'" value="Update Rights"/> <?php }
			  else {
			  ?>
              <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Update" title="Update User Details">
			  
              <input name="usr_permission" type="button" id="usr_permission" class="btn btn-primary" onClick="window.location='update_permission_loc.php?userid=<?=$sel_result['username']?>&userlevel=<?=$sel_result['utype']?>&u_name=<?=$sel_result['name']?>&page=<?=$page?>&srch=<?=$_REQUEST[srch]?><?=$pagenav?>'" value="Update Rights"/>
              <?php } }?>
              <input type="hidden" name="usrid2"  id="usrid2" value="<?=$sel_result['username']?>" />
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='adminusermgt.php?<?=$pagenav?>'">
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