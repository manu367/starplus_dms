<?php
require_once("../config/config.php");
$pkid = $_REQUEST['pk'];
///// get info from asc_master table /////
$user = mysqli_fetch_assoc(mysqli_query($link1, "SELECT * FROM asc_master WHERE sno = '".$pkid."'"));
$mappedloc = mysqli_fetch_assoc(mysqli_query($link1, "SELECT uid FROM mapped_master WHERE mapped_code = '".$user["asc_code"]."' AND status='Y'"));
?>
<div class="row">
	<div class="col-sm-12">
		<div align="center">
			<p align="center"><h4 style="color:#FF0000"><?=$user['name']."  (".$user['asc_code'].")";?></h4></p>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default text-left">
			<div class="panel-body" align="center" >
				<p class="alert-info"><span class="pull-left"><strong>Address </strong></span> <span class="pull-right"><?=$user['addrs'];?></span></p>
				<p class="alert-warning"><span class="pull-left"><strong>City </strong></span> <span class="pull-right"><?php echo $user['city']?></span></p>
				<p class="alert-info"><span class="pull-left"><strong>State </strong></span> <span class="pull-right"><?php echo $user['state']?></span></p>
			</div>
		</div>
	</div>  
</div>
<table class="table table-bordered" width="100%" style="font-size:13px">
   <tbody>
   	 <tr>
	   <td><label class="control-label">Circle</label></td>
	   <td><?php echo $user['circle'];?></td>
	   <td><label class="control-label">Segment</label></td>
	   <td><?php echo $user['segment'];?></td>
	 </tr>
	 <tr>
	   <td width="20%"><label class="control-label">Contact Person</label></td>
	   <td width="30%"><?=$user['contact_person']?></td>
	   <td width="20%"><label class="control-label">Contact No.</label></td>
	   <td width="30%"><?=$user['phone']?></td>
	 </tr>
	 <tr>
	   <td><label class="control-label">Email</label></td>
	   <td><?php echo $user['email'];?></td>
	   <td><label class="control-label">Landline</label></td>
	   <td><?php echo $user['landline'];?></td>
	 </tr>
	 <tr>
	   <td><label class="control-label">Landmark</label></td>
	   <td><?php echo $user['landmark'];?></td>
	   <td><label class="control-label">Pincode</label></td>
	   <td><?php echo $user['pincode'];?></td>
	 </tr>
	 <tr>
	   <td><label class="control-label">PAN</label></td>
	   <td><?php echo $user['pan_no'];?></td>
	   <td><label class="control-label">GSTIN</label></td>
	   <td><?php echo $user['gstin_no'];?></td>
	 </tr>
	  <tr>
	   <td><label class="control-label">Mapped Location</label></td>
	   <td colspan="3"><?php echo str_replace("~",", ",getAnyDetails($mappedloc['uid'],"name,city,state,id_type,asc_code","asc_code","asc_master",$link1));?></td>
	 </tr>
   </tbody>
 </table>