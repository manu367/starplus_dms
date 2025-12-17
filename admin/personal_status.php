<?php
require_once("../config/config.php");
$userid = $_REQUEST['pk'];

///// get info from admin user table /////
$user = mysqli_fetch_assoc(mysqli_query($link1, "SELECT * FROM admin_users WHERE username = '".$userid."' "));

////// get details of selected user////
if(mysqli_num_rows(mysqli_query($link1,"SELECT * FROM hrms_employe_master where loginid='".$user['username']."'"))>0){
	$res_locdet1 = mysqli_query($link1,"SELECT * FROM hrms_employe_master where loginid = '".$user['username']."'")or die(mysqli_error($link1));
	$row_emp = mysqli_fetch_array($res_locdet1);
}else{
	$res_locdet2 = mysqli_query($link1,"SELECT * FROM admin_users where username = '".$userid."'")or die(mysqli_error($link1));
	$row_usr = mysqli_fetch_array($res_locdet2);
}


?>
<div class="row">
	<div class="col-sm-12">
		<div align="center" >
		  <div> 
			<?php
				$profile_img = mysqli_fetch_assoc(mysqli_query($link1,"SELECT profile_img_name, profile_img_path FROM admin_users  WHERE username = '".$userid."' "));
				if($profile_img['profile_img_path']==""){
					$img = "../img/usrpwd.png";
				}else{
					$img = $profile_img['profile_img_path'];
				}
		   ?>
		   <img style="height: 220px;width: 220px;" alt="Profile Pic." class="img-thumbnail" src="<?=$img;?>">
		  </div>
		  <br>
		  <p align="center"><h4 style="color:#FF0000"><?=$user['name']."  (".$user['username'].")";?></h4></p>
		</div>
	</div>
</div>
<br>
<?php if($row_emp['empid']!=""){ ?>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default text-left">
			<div class="panel-body" align="center" >
				<p style="text-align: center;color: blue;font-weight: 800;">General Information</p>
				<p class="alert-info"><span class="pull-left"><strong>Address </strong></span> <span class="pull-right"><?=$row_emp['address'];?></span></p>
				<p class="alert-warning"><span class="pull-left"><strong>City </strong></span> <span class="pull-right"><?php $cit = getAnyDetails($row_emp['city'],"city","id","district_master",$link1); if($cit!=""){ echo $cit; }else{ echo $row_emp['city']; }?></span></p>
				<p class="alert-info"><span class="pull-left"><strong>State </strong></span> <span class="pull-right"><?php $sta = getAnyDetails($row_emp['state'],"state","statecode","state_master",$link1); if($sta!=""){ echo $sta; }else{ echo $row_emp['state']; } ?></span></p>
			</div>
		</div>
	</div>  
</div>

<table class="table table-bordered" width="100%" style="font-size:13px">
   <tbody>
	 <tr>
	   <td width="20%"><label class="control-label">Employee Name</label></td>
	   <td width="30%"><?=$row_emp['empname']?></td>
	   <td width="20%"><label class="control-label">Email</label></td>
	   <td width="30%"><?=$row_emp['email']?></td>
	 </tr>
	 <tr>
	   <td><label class="control-label">Company Name</label></td>
	   <td><?php echo getAnyDetails($row_emp['companyid'],"cname" ,"companyid","hrms_company_master",$link1);?></td>
	   <td><label class="control-label">Reporting manager</label></td>
	   <td><?php echo getAnyDetails($row_emp['managerid'],"empname","loginid","hrms_employe_master",$link1);?></td>
	 </tr>
	 <tr>
	   <td><label class="control-label">Date of Birth</label></td>
	   <td><?php echo dt_format($row_emp['date_of_birth']);?></td>
	   <td><label class="control-label">Anniversary Date</label></td>
	   <td><?php echo dt_format($row_emp['anniversary_date']);?></td>
	 </tr>
	 <tr>
	   <td><label class="control-label">Contact No.</label></td>
	   <td><?php echo $row_emp['phone'];?></td>
	   <td><label class="control-label">Address</label></td>
	   <td><?php echo $row_emp['address'];?></td>
	 </tr>
	 <tr>
	   <td><label class="control-label">Date of Joining</label></td>
	   <td><?php echo dt_format($row_emp['joining_date']);?></td>
	   <td><label class="control-label">EMP Code</label></td>
	   <td><?php echo $row_emp['sapid'];?></td>
	 </tr>
	  <tr>
	   <td><label class="control-label">Alternate Email</label></td>
	   <td><?php echo $row_emp['aletrnate_email'];?></td>
	   <td><label class="control-label">Alternate Number</label></td>
	   <td><?php echo $row_emp['alternate_no'];?></td>
	 </tr>
	  <tr>
	   <td><label class="control-label">Password</label></td>
	   <td>
	   	<input style="border-color: #fff;border: 0px;" type="password" id="pass_change" name="pass_change" value="<?php echo $row_emp['password'];?>" /> 
		<i class='fa fa-eye' style="color:#dc3545;font-size: 20px;" onmouseover="showPass(1)" onmouseout="showPass(0)" title="View Password" ></i>
	   </td>
	   <td><label class="control-label"></label></td>
	   <td></td>
	 </tr>
   </tbody>
 </table>
 
 <?php }else{ ?>
 
 <?php /*?><div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default text-left">
			<div class="panel-body" align="center" >
				<p style="text-align: center;color: blue;font-weight: 800;">General Information</p>
				<p class="alert-info"><span class="pull-left"><strong>Address </strong></span> <span class="pull-right"><?=$row_usr['addrs'];?></span></p>
				<p class="alert-warning"><span class="pull-left"><strong>City </strong></span> <span class="pull-right"><?php $cit = getAnyDetails($row_usr['city'],"city","id","district_master",$link1); if($cit!=""){ echo $cit; }else{ echo $row_usr['city']; }?></span></p>
				<p class="alert-info"><span class="pull-left"><strong>State </strong></span> <span class="pull-right"><?php $sta = getAnyDetails($row_usr['state'],"state","statecode","state_master",$link1); if($sta!=""){ echo $sta; }else{ echo $row_usr['state']; } ?></span></p>
			</div>
		</div>
	</div>  
</div><?php */?>

<table class="table table-bordered" width="100%" style="font-size:13px">
   <tbody>
	 <tr>
	   <td width="20%"><label class="control-label">Name</label></td>
	   <td width="30%"><?=$row_usr['name']?></td>
	   <td width="20%"><label class="control-label">Email</label></td>
	   <td width="30%"><?=$row_usr['emailid']?></td>
	 </tr>
	  <tr>
	   <td><label class="control-label">Password</label></td>
	   <td>
	   	<input style="border-color: #fff;border: 0px;" type="password" id="pass_change" name="pass_change" value="<?php echo $row_usr['password'];?>" /> 
		<i class='fa fa-eye' style="color:#dc3545;font-size: 20px;" onmouseover="showPass(1)" onmouseout="showPass(0)" title="View Password" ></i>
	   </td>
	   <td><label class="control-label">Contact No.</label></td>
	   <td><?php echo $row_usr['phone'];?></td>
	 </tr>
   </tbody>
 </table>
 
 <?php } ?>