<?php
require_once("../config/config.php");

////// get details of selected location////
if(isset($_SESSION['userid'])){ $userid = $_SESSION['userid'];}else{ $userid = "";}
$res_locdet=mysqli_query($link1,"SELECT * FROM hrms_employe_master where loginid='".$userid ."'")or die(mysqli_error($link1));
$row_locdet=mysqli_fetch_array($res_locdet);

?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
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
	 function showPass(val){
		if(val == 1){
			document.getElementById("pass_change").type = "text";
		}else{
			document.getElementById("pass_change").type = "password";
		}
	 }
</script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <!--- <h2 align="center"><i class="fa fa-address-card-o"></i> My Profile </h2> ---> <br>
      <form class="form-horizontal" role="form">
        
        <div class="row">
            <div class="col-sm-12">
                <div align="center" >
                  <div> 
					<?php
						$profile_img = mysqli_fetch_assoc(mysqli_query($link1,"SELECT profile_img_name, profile_img_path FROM admin_users  WHERE username = '".$_SESSION['userid']."' "));
						if($profile_img['profile_img_path']==""){
							$img = "../img/usrpwd.png";
						}else{
							$img = $profile_img['profile_img_path'];
						}
				   ?>
				   <img style="height: 220px;width: 220px;" alt="Profile Pic." class="img-thumbnail" src="<?=$img;?>">
                  </div>
                  <br>
                  <p align="center"><h4 style="color:#FF0000"><?=$row_locdet['empname']."  (".$row_locdet['loginid'].")";?></h4></p>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default text-left">
                    <div class="panel-body" align="center" >
                        <p style="text-align: center;color: blue;font-weight: 800;">General Information</p>
                        <p class="alert-info"><span class="pull-left"><strong>Address </strong></span> <span class="pull-right"><?=$row_locdet['address'];?></span></p>
                        <p class="alert-warning"><span class="pull-left"><strong>City </strong></span> <span class="pull-right"><?php $cit = getAnyDetails($row_locdet['city'],"city","id","district_master",$link1); if($cit!=""){ echo $cit; }else{ echo $row_locdet['city']; }?></span></p>
                        <p class="alert-info"><span class="pull-left"><strong>State </strong></span> <span class="pull-right"><?php $sta = getAnyDetails($row_locdet['state'],"state","statecode","state_master",$link1); if($sta!=""){ echo $sta; }else{ echo $row_locdet['state']; } ?></span></p>
                    </div>
                </div>
            </div>  
        </div>
        
        <table class="table table-bordered" width="100%" style="font-size:13px">
           <tbody>
             <tr>
               <td width="20%"><label class="control-label">Employee Name</label></td>
               <td width="30%"><?=$row_locdet['empname']?></td>
               <td width="20%"><label class="control-label">Email</label></td>
               <td width="30%"><?=$row_locdet['email']?></td>
             </tr>
             <tr>
               <td><label class="control-label">Company Name</label></td>
               <td><?php echo getAnyDetails($row_locdet['companyid'],"cname" ,"companyid","hrms_company_master",$link1);?></td>
               <td><label class="control-label">Reporting manager</label></td>
               <td><?php echo getAnyDetails($row_locdet['managerid'],"empname","loginid","hrms_employe_master",$link1);?></td>
             </tr>
             <tr>
               <td><label class="control-label">Date of Birth</label></td>
               <td><?php echo dt_format($row_locdet['date_of_birth']);?></td>
               <td><label class="control-label">Anniversary Date</label></td>
               <td><?php echo dt_format($row_locdet['anniversary_date']);?></td>
             </tr>
             <tr>
               <td><label class="control-label">Contact No.</label></td>
               <td><?php echo $row_locdet['phone'];?></td>
               <td><label class="control-label">Address</label></td>
               <td><?php echo $row_locdet['address'];?></td>
             </tr>
             <tr>
               <td><label class="control-label">Date of Joining</label></td>
               <td><?php echo dt_format($row_locdet['joining_date']);?></td>
               <td><label class="control-label">EMP Code</label></td>
               <td><?php echo $row_locdet['sapid'];?></td>
             </tr>
			  <tr>
               <td><label class="control-label">Alternate Email</label></td>
               <td><?php echo $row_locdet['aletrnate_email'];?></td>
               <td><label class="control-label">Alternate Number</label></td>
               <td><?php echo $row_locdet['alternate_no'];?></td>
             </tr>
			  <tr>
               <td><label class="control-label">Password</label></td>
               <td>
				<input style="border-color: #fff;border: 0px;" type="password" id="pass_change" name="pass_change" value="<?php echo $row_locdet['password'];?>" /> 
				<i class='fa fa-eye' style="color:#dc3545;font-size: 20px;" onMouseOver="showPass(1)" onMouseOut="showPass(0)" title="View Password" ></i>
			   </td>
               <td><label class="control-label"></label></td>
               <td></td>
             </tr>
           </tbody>
         </table>
        
      </form>
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>