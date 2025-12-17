<?php
require_once("../config/config.php");
$id = base64_decode($_REQUEST['id']);
////// Fetch informations //////
$sel_usr="select * from hrms_leave_request where id='".$id."' ";
$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
$sel_result=mysqli_fetch_assoc($sel_res12);

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
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-lightbulb-o"></i> Leave View </h2><br><br>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
      
      	<div class="panel-group">
            <div class="panel panel-info table-responsive">
                <div class="panel-heading heading1"><i class="fa fa-user fa-lg"></i>&nbsp;&nbsp;Employee Details</div>
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
              <div class="panel panel-info table-responsive">
             <div class="panel-heading heading1"><i class="fa fa-check fa-lg"></i>&nbsp;&nbsp;Approve / Reject</div>
                 <div class="panel-body">
               <table class="table table-bordered" width="100%">
                <tbody>
                 <tr>
                        <td width="20%"><label class="control-label">Approve Date</label></td>
                        <td width="30%"><input name="last_date1" id="last_date1" type="text" value="<?php if($sel_result['approve_date']!='0000-00-00'){ echo dt_format($sel_result['approve_date']); }else{ echo ""; }?>"  readonly class=" form-control"/></td>
                        <td width="20%"><label class="control-label">Approve By</label></td>
                        <td width="30%"><input name="status" id="status" type="text" value="<?php if($sel_result['approve_by'] != ""){ echo $sel_result['approve_by']; }else{ echo ""; } ?>"  readonly class=" form-control"/></td>
                      </tr>
                      <tr>
                        <td width="20%"><label class="control-label">Status</label></td>
                        <td width="30%"><input name="status" id="status" type="text" value="<?php if($sel_result['status'] == '4') { echo "Approve";} else if($sel_result['status'] == '5') { echo "Reject" ;} else {  echo "";}?>"  readonly class=" form-control"/></td>
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
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onclick="window.location.href='leave_apply_list.php?<?=$pagenav?>'">
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