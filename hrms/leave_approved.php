<?php
require_once("../config/config.php");
/////get status//
$id = base64_decode($_REQUEST['id']);
$userid = base64_decode($_REQUEST['user']);
////// Fetch informations //////
$sel_usr="select * from hrms_leave_request where id='".$id."' ";
$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
$sel_result=mysqli_fetch_assoc($sel_res12);


if($_POST){
	@extract($_POST);
	if(isset($_POST['updButton'])){
	//// transcation /////////////////////////////////////////////////////////////
	mysqli_autocommit($link1, false);		
   	if ($_POST['updButton']=='Update'){	
		$flag = true;
       $err_msg = "";		
	   ////// update status on basis of status selection////////////////////////////////////////////////////
	   	   
	       $usr_upd= "update hrms_leave_request set  status ='".$status."', approve_by  ='".$_SESSION['userid']."', approve_date='".$today."'  , remark = '".$remark."' where id ='".$refid."' ";
 
          $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
		  
		  /// check if query is execute or not//
					if(!$res_upd){
						$flag = false;
						$err_msg = "Error1". mysqli_error($link1) . ".";
					}
		  
		  
		  
		 
	
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$refid,"Leave Request",$status,$_SERVER['REMOTE_ADDR'],$link1,'');
	////// return message
    $cflag = "success";
    $cmsg = "Success";
	}
	 ///// check  query are successfully executed
                    if ($flag) {		
                        mysqli_commit($link1);                 
						$cflag = "success";
						$cmsg = "Success";					
						$msg="You have successfully updated details of leave ";
                    } else {
                        mysqli_rollback($link1);               
						$cflag = "danger";
						$cmsg = "Failed";
						$msg = "Requested could not be processed ,".$err_msg;
                    }
                    mysqli_close($link1);
	   ///// move to parent page
   header("location:leave_approve_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <script src="../js/jquery-1.10.2.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-lightbulb-o"></i> Leave Approval View </h2><br><br>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
      
      	<div class="panel-group">
            <div class="panel panel-success table-responsive">
                <div class="panel-heading"><i class="fa fa-user fa-lg"></i>&nbsp;&nbsp;Employee Details</div>
                 <div class="panel-body">
                  <table class="table table-bordered" width="100%">
                    <tbody>
                      <tr>
                        <td width="20%"><label class="control-label">Employee Name</label></td>
                        <td width="30%"><?php echo getAnyDetails($sel_result['empid'],"empname","loginid","hrms_employe_master",$link1);?></td>
                        <td width="20%"><label class="control-label">Employee Id</label></td>
                        <td width="30%"><?php echo $sel_result['empid'];?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">From Date</label></td>
                        <td><?php echo dt_format($sel_result['from_date']); ?></td>
                        <td><label class="control-label">To Date</label></td>
                        <td><?php echo dt_format($sel_result['to_date']); ?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Leave Duration</label></td>
                        <td><?php echo $sel_result['leave_duration']; ?></td>
                        <td><label class="control-label">Leave Type</label></td>
                        <td><?php echo $sel_result['leave_type']; ?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Apply Date</label></td>
                        <td><?php echo dt_format($sel_result['entry_date']); ?></td>
                        <td><label class="control-label">Apply Time</label></td>
                        <td><?php echo $sel_result['entry_time']; ?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Purpose</label></td>
                        <td colspan="3"><?php echo $sel_result['purpose'];?></td>
                      </tr>
                      <tr>
                        <td><label class="control-label">Description</label></td>
                        <td colspan="3"><?php echo $sel_result['description'];?></td>
                      </tr>
                      
                    </tbody>
                  </table>
                </div><!--close panel body-->
            </div><!--close panel-->
             <br><br>
			 
			  <?php if($sel_result['approve_by'] == '') {?>
			 <div class="panel panel-success table-responsive">
            <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Approve / Reject</div>
               <div class="panel-body">
               <table class="table table-bordered" width="100%">
	          <tbody>
              <tr>
                <td width="20%" align="center"><label class="control-label">Status<span class="red_small">*</span></label></td>
                <td width="30%"><select id="status" name="status" class="form-control required"  style="width:200px" required><option value="">Please Select</option><option value="4">Approve</option><option value="5">Reject</option></select></td>
				<td width="20%" align="center"><label class="control-label">Remark<span class="red_small">*</span></label></td>
                <td width="30%"><textarea id="remark" name="remark" class="form-control required" required></textarea></td>
                </tr>
				<tr>
                <td width="100%" align="center" colspan="8">
				 
              <button class='btn<?=$btncolor?>' id="upd" type="submit" name="updButton" value="Update"><i class="fa fa-retweet fa-lg"></i>&nbsp;&nbsp;Save</button>
              <input type="hidden" name="refid"  id="refid" value="<?=$sel_result['id']?>" />
              <button title="Back" type="button" class="btn<?=$btncolor?>"  onClick="window.location.href='leave_approve_list.php?<?=$pagenav?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
                   
                 </td>
                </tr>
            </tbody>
             </table>
			 </div><!--close panel body-->
           </div><!--close panel-->
		   <?php }?>
		   
		   <?php if($sel_result['status'] == '4' || $sel_result['status'] == '5') {?>
              <div class="panel panel-success table-responsive">
             <div class="panel-heading"><i class="fa fa-check fa-lg"></i>&nbsp;&nbsp;Approve / Reject</div>
                 <div class="panel-body">
               <table class="table table-bordered" width="100%">
                <tbody>
                 <tr>
                        <td width="20%"><label class="control-label">Approve Date</label></td>
                        <td width="30%"><input name="last_date1" id="last_date1" type="text" value="<?php if($sel_result['approve_date']!='0000-00-00'){ echo dt_format($sel_result['approve_date']); }else{ echo ""; }?>"  readonly class=" form-control"/></td>
                        <td width="20%"><label class="control-label">Approve By</label></td>
                        <td width="30%"><input name="status" id="status" type="text" value="<?php if($sel_result['approve_by'] != ""){ echo getAnyDetails($sel_result['approve_by'],"empname","loginid","hrms_employe_master",$link1); }else{ echo ""; } ?>"  readonly class=" form-control"/></td>
                      </tr>
                      <tr>
                        <td width="20%"><label class="control-label">Status</label></td>
                        <td width="30%"><input name="status" id="status" type="text" value="<?php if($row['status']=='4'){ echo "Approved"; }else if($row['status']=='3'){ echo "Pending for Approval"; }else{ echo $row['status']; } ?>"  readonly class=" form-control"/></td>
                        <td width="20%"><label class="control-label">Approval Remark</label></td>
                        <td width="30%"><textarea id="remark" name="remark" class="form-control required" required readonly="readonly"><?=$sel_result['remark']?></textarea></td>
                      </tr>
                    
                    </tbody>
                  </table>
                </div><!--close panel body-->
            </div><!--close panel-->			
          </div>
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onClick="window.location.href='leave_approve_list.php?id=<?=base64_encode($userid);?>&user=<?=base64_encode($userid);?><?=$pagenav?>'">
              </div>  
          </div>
		  <?php }?>        
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
   