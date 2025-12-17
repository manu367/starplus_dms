<?php
require_once("../config/config.php");
$id = base64_decode($_REQUEST['id']);

$info = mysqli_fetch_array(mysqli_query($link1, "SELECT * FROM hrms_request_master WHERE id = '".$id."' and type in ('IR','VR','LR') "));

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
 
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-align-justify"></i> View Application </h2>
      <?php /*?><h5 align="center"> ( Activity No. -  <?=$info['activity_no'];?> ) </h5><?php */?>
      <?php if($_REQUEST['msg']!=''){?>
      	<h4 align="center">
        	<span 
			<?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span>
        </h4>
	  <?php }?>
      <br>     
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data" >
                               
          <div class="panel-group">
            <div class="panel panel-info table-responsive">
                <div class="panel-heading heading1"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Application Details</div>
                 <div class="panel-body">
                 	<?php 
					if($info['type'] == "IR"){
						$main_info1 = mysqli_fetch_array(mysqli_query($link1, "SELECT * FROM hrms_request_icard WHERE sno = '".$info['request_no']."' "));
						if($main_info1 !=""){
					?>	
					<table class="table table-bordered" width="100%">
                        <tbody>
                          <tr>
                            <td width="20%"><label class="control-label">Application For</label></td>
                            <td width="80%"><?=$info['name'];?></td>
                          </tr>
                          <tr>
                            <td width="20%"><label class="control-label">Manager Name</label></td>
                            <td width="80%"><?=getAnyDetails($main_info1['mgr_id'],'empname','loginid','hrms_employe_master',$link1)." | ".$main_info1['mgr_id'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Emergency No.</label></td>
                            <td colspan="3"><?=$main_info1['emergency_no'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Entry Date</label></td>
                            <td colspan="3"><?=dt_format($main_info1['update_date']);?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Remark</label></td>
                            <td colspan="3"><?=$main_info1['remark'];?></td>
                          </tr>
                        </tbody>
                    </table>
                    <?php	
					}}else if($info['type'] == "VR"){
						$main_info2 = mysqli_fetch_array(mysqli_query($link1, "SELECT * FROM hrms_request_vcard WHERE sno = '".$info['request_no']."' "));
						if($main_info2 !=""){
					?>	
					<table class="table table-bordered" width="100%">
                        <tbody>
                          <tr>
                            <td width="20%"><label class="control-label">Application For</label></td>
                            <td width="80%"><?=$info['name'];?></td>
                          </tr>
                          <tr>
                            <td width="20%"><label class="control-label">Manager Name</label></td>
                            <td width="80%"><?=getAnyDetails($main_info2['mgr_id'],'empname','loginid','hrms_employe_master',$link1)." | ".$main_info2['mgr_id'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Email ID.</label></td>
                            <td colspan="3"><?=$main_info2['email'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Mobile No.</label></td>
                            <td colspan="3"><?=$main_info2['mobile_no'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Entry Date</label></td>
                            <td colspan="3"><?=dt_format($main_info2['update_date']);?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Remark</label></td>
                            <td colspan="3"><?=$main_info2['remark'];?></td>
                          </tr>
                        </tbody>
                    </table>	
                    <?php    
					}}else if($info['type'] == "LR"){
						$main_info3 = mysqli_fetch_array(mysqli_query($link1, "SELECT * FROM hrms_request_loan WHERE sno = '".$info['request_no']."' "));
						if($main_info3 !=""){
					?>	
					<table class="table table-bordered" width="100%">
                        <tbody>
                          <tr>
                            <td width="20%"><label class="control-label">Application For</label></td>
                            <td width="80%"><?=$info['name'];?></td>
                          </tr>
                          <tr>
                            <td width="20%"><label class="control-label">Manager Name</label></td>
                            <td width="80%"><?=getAnyDetails($main_info3['mgr_id'],'empname','loginid','hrms_employe_master',$link1)." | ".$main_info3['mgr_id'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Date of Joining</label></td>
                            <td colspan="3"><?=dt_format($main_info3['doj']);?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Email ID.</label></td>
                            <td colspan="3"><?=$main_info3['email'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Mobile No.</label></td>
                            <td colspan="3"><?=$main_info3['phone'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Requested Amount </label></td>
                            <td colspan="3"><?=$main_info3['requested_amt'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Approved Amount </label></td>
                            <td colspan="3"><?=$main_info3['approved_amt'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Entry Date</label></td>
                            <td colspan="3"><?=dt_format($main_info3['update_date']);?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Remark</label></td>
                            <td colspan="3"><?=$main_info3['remark'];?></td>
                          </tr>
                        </tbody>
                    </table>
                    <?php	
					}}else {}
					?>
                                    
                </div><!--close panel body-->
            </div><!--close panel-->
            <br><br>
              <div class="panel panel-info table-responsive">
             <div class="panel-heading heading1"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Application Status</div>
                 <div class="panel-body">
               <table class="table table-bordered" width="100%">
                <tbody>
                      <tr>
                        <td width="20%"><label class="control-label">Status </label></td>
                        <td width="80%" colspan="3"><?=$info['status'];?></td>
                      </tr>
                      <tr>
                        <td width="20%"><label class="control-label">Approve By </label></td>
                        <td width="80%" colspan="3"><?=$info['approve_by'];?></td>
                      </tr>
                      <tr>
                        <td width="20%"><label class="control-label">Approve Date </label></td>
                        <td width="80%" colspan="3"><?=dt_format($info['approve_date']);?></td>
                      </tr>
                      
                    </tbody>
                  </table>
                </div><!--close panel body-->
            </div><!--close panel-->
          </div>
                    
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='application_emp_list.php?<?=$pagenav?>'">
              </div>  
          </div>
         
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