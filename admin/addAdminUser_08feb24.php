<?php
require_once("../config/config.php");
$id = base64_decode($_REQUEST['id']);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='edit'){
	$sel_usr="select * from admin_users where username='".$id."'";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
   if ($_POST['add']=='ADD'){
   $msg="";
   ///// check if email id , mobile no. and emp id should be unique
   $res_exist = mysqli_query($link1,"SELECT uid, oth_empid, phone, emailid FROM admin_users WHERE phone='".$phone."' OR emailid='".$email."'")or die("error1".mysqli_error($link1));
   if(mysqli_num_rows($res_exist)==0){
	if($_FILES['profile_img']["name"]!=''){	
	   //// upload doc into folder ////
		$file_name =$_FILES['profile_img']['name'];
		$file_tmp =$_FILES['profile_img']['tmp_name'];
		$img_n = $today.$file_name;
		$file_path="../doc_attach/profile_img_doc/$today.$file_name";
		move_uploaded_file($file_tmp,$file_path);	
	} // end of file upload	
   
	$query_code="select MAX(uid) as qc from admin_users";
    $result_code=mysqli_query($link1,$query_code)or die("error2".mysqli_error($link1));
    $arr_result2=mysqli_fetch_array($result_code);
    $code_id=$arr_result2[0];
    $pad=str_pad(++$code_id,3,"0",STR_PAD_LEFT);	
    $admiCode=substr(strtoupper(BRANDNAME),0,2)."USR".$pad; 
	//$pwd=$admiCode."@123";
	$desig = explode("~",$designation);
	$usr_add="INSERT INTO admin_users set username ='".$admiCode."',password ='".$pwd."',oth_empid='".$emp_id."',name= '".$usrname."',utype='".$u_type."',phone='".$phone."',emailid= '".$email."',city='".$locationcity."',state='".$locationstate."',status='".$status."',reporting_manager='".$reporting_manager."',  profile_img_name = '".$img_n."', profile_img_path = '".$file_path."',create_by='".$_SESSION["userid"]."', createdate='".date("Y-m-d H:i:s")."',designationid='".$desig[0]."',band='".$desig[1]."',department='".$department."',subdepartment='".$subdepartment."',additional_otp_login='".$two_step_login."'";

    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1)); 
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$admiCode,"ADMIN USER","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
	////// return message
	$msg="You have successfully created an user with ref. no. ".$admiCode;
	}else{
   		$row_exist = mysqli_fetch_assoc($res_exist);
		if(strtoupper($row_exist["oth_empid"])==strtoupper($emp_id)){
			$msg.="Empolyee id is already exist.";
		}
		if($row_exist["phone"]==$phone){
			$msg.="Mobile no. is already exist.";
		}
		if(strtoupper($row_exist["emailid"])==strtoupper($email)){
			$msg.="Email id is already exist.";
		}
   }
   }
   else if ($_POST['upd']=='Update'){
   	$msg="";
   ///// check if email id , mobile no. and emp id should be unique
   $res_exist = mysqli_query($link1,"SELECT uid, oth_empid, phone, emailid FROM admin_users WHERE (oth_empid='".$emp_id."' OR phone='".$phone."' OR emailid='".$email."') AND username != '".$usrid2."'")or die("error1".mysqli_error($link1));
   $row_exist = mysqli_fetch_assoc($res_exist);
   //echo $sel_result["emailid"]."==".$email." && ".$sel_result["phone"]."==".$phone." && ".$sel_result["oth_empid"]."==".$emp_id;
   //if((mysqli_num_rows($res_exist)==0) || ($sel_result["emailid"]==$email && $sel_result["phone"]==$phone && $sel_result["oth_empid"]==$emp_id)){
   //if(mysqli_num_rows($res_exist)==0){
   if(1==1){
    if($_FILES['profile_img']["name"]!=''){	
		$check_old_img = mysqli_fetch_assoc(mysqli_query($link1, "SELECT profile_img_name, profile_img_path FROM admin_users WHERE username = '".$usrid2."' "));
		if($check_old_img['profile_img_path']!=""){
			///// remove old image from folder by vikas ///////////
			unlink($check_old_img['profile_img_path']);
			///// remove old image from db //////////////
			mysqli_query($link1, "UPDATE admin_users SET profile_img_name = '', profile_img_path = '' WHERE username = '".$usrid2."' ");
		}
	   //// upload doc into folder ////
		$file_name =$_FILES['profile_img']['name'];
		$file_tmp =$_FILES['profile_img']['tmp_name'];
		$img_n = $today.$file_name;
		$file_path="../doc_attach/profile_img_doc/$today.$file_name";
		move_uploaded_file($file_tmp,$file_path);	
	} // end of file upload	
    if($status=="deactive"){ $applogot = ",app_logout='1'";}
	$desig = explode("~",$designation);
    $usr_upd="update admin_users set password ='".$pwd."' ,name= '".$usrname."',oth_empid='".$emp_id."',utype='".$u_type."',phone= '".$phone."',emailid= '".$email."',city='".$locationcity."',state='".$locationstate."',status='".$status."',reporting_manager='".$reporting_manager."', profile_img_name = '".$img_n."', profile_img_path = '".$file_path."', updatedate='".date("Y-m-d H:i:s")."',designationid='".$desig[0]."',band='".$desig[1]."',department='".$department."',subdepartment='".$subdepartment."' ".$applogot.", fencing_latitude='".$fence_lat."',fencing_longitude='".$fence_long."',additional_otp_login='".$two_step_login."' where username = '".$usrid2."'";
    $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$usrid2,"ADMIN USER","UPDATE",$ip,$link1,"");
	////// return message
	$msg="You have successfully updated user details for ".$usrid2;
   }else{
   		
		if(strtoupper($row_exist["oth_empid"])==strtoupper($emp_id)){
			$msg.="Empolyee id is already exist.<br/>";
		}
		if($row_exist["phone"]==$phone){
			$msg.="Mobile no. is already exist.<br/>";
		}
		if(strtoupper($row_exist["emailid"])==strtoupper($email)){
			$msg.="Email id is already exist.<br/>";
		}
   }
   }
   ///// move to parent page
    header("location:adminusermgt.php?msg=".urlencode($msg)."".$pagenav);
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
 <script>

$(document).ready(function(){
        $("#frm1").validate();
});
</script>
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
 ////// get sub department
$(document).ready(function(){
	$('#department').change(function(){
		var dptid=$('#department').val();
	  	$.ajax({
	    	type:'post',
			url:'../includes/getAzaxFields.php',
			data:{deptid:dptid},
			success:function(data){
	   	 		$('#subdptdiv').html(data);
	    	}
	  	});
    });
});
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
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">User Name</label>
              <div class="col-md-5">
                 <input type="text" name="usrname" class="required form-control" id="usrname" value="<?=$sel_result['name']?>" required/>
              </div>
            </div>
            <?php if($_REQUEST['op']!='add'){ ?>
            <div class="col-md-6"><label class="col-md-5 control-label">User Id</label>
              <div class="col-md-5">
               <input type="text" name="uid" id="uid" class="form-control" value="<?php echo $sel_result['username'];?>" required readonly/>
              </div>
            </div>
            <?php }else{?>
            <div class="col-md-6"><label class="col-md-5 control-label">Emp Id</label>
              <div class="col-md-5">
               <input type="text" name="emp_id" id="emp_id" class="form-control alphanumeric" value="<?php echo $sel_result['oth_empid'];?>"/>
              </div>
            </div>
            <?php }?>
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
                    <option value="<?=$row_utype['refid']?>"<?php if($sel_result['utype'] ==$row_utype['refid']) { echo 'selected'; }?>><?=$row_utype['typename']." ( ".$row_utype['utype']." )";?></option>
                    <?php
					}
					?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Designation</label>
              <div class="col-md-5">
                 <select name='designation' id='designation' class="required form-control" required>
                    <option value="">--Please Select--</option>
                    <?php
					$res_desig=mysqli_query($link1,"select * from hrms_designation_master where status='1' order by designationid")or die("erro1".mysqli_error($link1));
					while($row_desig=mysqli_fetch_assoc($res_desig)){
					?>
                    <option value="<?=$row_desig['designationid']."~".$row_desig['band']?>"<?php if($sel_result['designationid'] ==$row_desig['designationid']) { echo 'selected'; }?>><?=$row_desig['designame'];?></option>
                    <?php
					}
					?>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">&nbsp;</label>
              <div class="col-md-5">
                
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Department</label>
              <div class="col-md-5">
                <select name='department' id='department' class="required form-control" required>
                    <option value="">--Please Select--</option>
                    <?php
					$res_dept=mysqli_query($link1,"select * from hrms_department_master where status='1' order by dname")or die("erro1".mysqli_error($link1));
					while($row_dept=mysqli_fetch_assoc($res_dept)){
					?>
                    <option value="<?=$row_dept['departmentid']?>"<?php if($sel_result['department'] ==$row_dept['departmentid']) { echo 'selected'; }?>><?=$row_dept['dname'];?></option>
                    <?php
					}
					?>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Sub-Department</label>
              <div class="col-md-5" id="subdptdiv">
               <select name='subdepartment' id='subdepartment' class="required form-control" required>
                    <option value="">--Please Select--</option>
                    <?php
					$res_sdept=mysqli_query($link1,"select * from hrms_subdepartment_master where status='1' AND departmentid='".$sel_result['department']."' order by department,subdept")or die("erro1".mysqli_error($link1));
					while($row_sdept=mysqli_fetch_assoc($res_sdept)){
					?>
                    <option value="<?=$row_sdept['subdeptid']?>"<?php if($sel_result['subdepartment'] ==$row_sdept['subdeptid']) { echo 'selected'; }?>><?=$row_sdept['subdept'];?></option>
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
            <div class="col-md-6"><label class="col-md-5 control-label">Profile Img.</label>
              <div class="col-md-5">
                <input type="file" name="profile_img" id="profile_img" class="form-control" accept="image/*" />
				<div style="color:red;font-size: 12px;margin-top: 5px;">Use (220px X 220px) Image Only</div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">State</label>
              <div class="col-md-5">
                 <select name='locationstate' id='locationstate' class='form-control required' onchange='get_citydiv();' required>
                	<option value=''>--Select State--</option>
					<?php
                    $state_query = "select distinct(state) from state_master where 1 order by state";
                    $state_res = mysqli_query($link1, $state_query);
                    while ($row_res = mysqli_fetch_array($state_res)) {
                    ?>
                    <option value="<?=$row_res['state']?>"<?php if($row_res['state']==$sel_result['state']){ echo "selected";}?>><?php echo $row_res['state'];?></option>
                    <?php }?>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Base Location</label>
              <div class="col-md-5" id="citydiv">
              	<select  name='locationcity' id='locationcity' class='form-control required' required>
                	<option value=''>--Please Select--</option>
    				<?php
                    $city_query = "SELECT distinct city FROM district_master where state='".$sel_result['state']."' order by city";
					$city_res = mysqli_query($link1, $city_query);
					while ($row_city = mysqli_fetch_array($city_res)) {
					?>
					<option value="<?=$row_city['city']?>"<?php if($row_city['city']==$sel_result['city']){ echo "selected";}?>><?php echo $row_city['city'];?></option>
					<?php
                    }
					?>
    				<option value='Others'<?php if($sel_result['city']=="Others"){ echo "selected";}?>>Others</option>
                </select>
              </div>
            </div>
          </div>
          <?php if($_REQUEST['op']!='add'){ ?>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Emp Id</label>
              <div class="col-md-5">
                 <input type="text" name="emp_id" id="emp_id" class="form-control" value="<?php echo $sel_result['oth_empid'];?>"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Reporting Manager</label>
              <div class="col-md-5">
                	<select name="reporting_manager" id="reporting_manager" class="form-control selectpicker" data-live-search="true">
                        <option value="">--please select--</option>
                        <?php
						$sql = mysqli_query($link1, "Select name,username,oth_empid from admin_users where status='active' and username!='".$sel_result['username']."' ORDER BY name");
						while ($row = mysqli_fetch_assoc($sql)) {
						?>
						<option value="<?=$row['username'];?>" <?php if ($sel_result['reporting_manager'] == $row['username']) { echo "selected";}?>><?= $row['name']." | ".$row['username']." ".$row['oth_empid'];?></option>
                        <?php } ?>
                      </select>
              </div>
            </div>
          </div>
           <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Fencing Latitude</label>
              <div class="col-md-5">
                 <input type="text" name="fence_lat" id="fence_lat" class="form-control number" value="<?php echo $sel_result['fencing_latitude'];?>"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Fencing Longitude</label>
              <div class="col-md-5">
                	<input type="text" name="fence_long" id="fence_long" class="form-control number" value="<?php echo $sel_result['fencing_longitude'];?>"/>
              </div>
            </div>
          </div>
          <?php }else{?>
          <div class="form-group">
            
            <div class="col-md-6"><label class="col-md-5 control-label">Reporting Manager</label>
              <div class="col-md-5">
                	<select name="reporting_manager" id="reporting_manager" class="form-control">
                        <option value="">--please select--</option>
                        <?php
						$sql = mysqli_query($link1, "Select name,username,oth_empid from admin_users where status='active' and username!='".$sel_result['username']."' ORDER BY name");
						while ($row = mysqli_fetch_assoc($sql)) {
						?>
						<option value="<?=$row['username'];?>" <?php if ($_REQUEST['reporting_manager'] == $row['username']) { echo "selected";}?>><?= $row['name']." | ".$row['username']." ".$row['oth_empid'];?></option>
                        <?php } ?>
                      </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">&nbsp;</label>
              <div class="col-md-5">
            
              </div>
            </div>
          </div>
          <?php } ?>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Two step login<br/><span class="small">(with OTP)</span></label>
              <div class="col-md-5">
                 <select name="two_step_login" id="two_step_login" class="form-control">
                    <option value="N"<?php if($sel_result['additional_otp_login']=="N"){ echo "selected";}?>>N</option>
                    <option value="Y"<?php if($sel_result['additional_otp_login']=="Y"){ echo "selected";}?>>Y</option>
                  </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">&nbsp;</label>
              <div class="col-md-5">
            
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='add'){ ?>
              <input type="submit" class="btn <?=$btncolor?>" name="add" id="add" value="ADD" title="Add User">
              <?php }else{?>
              <input type="submit" class="btn <?=$btncolor?>" name="upd" id="upd" value="Update" title="Update User Details">
              <input name="usr_permission" type="button" id="usr_permission" class="btn <?=$btncolor?>" onClick="window.location='update_permission.php?userid=<?=base64_encode($sel_result['username'])?>&userlevel=<?=$sel_result['utype']?>&u_name=<?=base64_encode($sel_result['name'])?>&page=<?=$page?>&srch=<?=$_REQUEST['srch']?><?=$pagenav?>'" value="Update Rights"/>
              <?php }?>
              <input type="hidden" name="usrid2"  id="usrid2" value="<?=$sel_result['username']?>" />
              <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='adminusermgt.php?<?=$pagenav?>'">
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