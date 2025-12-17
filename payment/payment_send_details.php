<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM payment_send where doc_no='".$docid."' and  from_location = '".$_REQUEST['location']."' ";
$po_res=mysqli_query($link1,$po_sql);
$p_row=mysqli_fetch_assoc($po_res);

$app="SELECT * FROM approval_activities where ref_no='".$docid."'";
$app_res=mysqli_query($link1,$app);
$ap_row=mysqli_fetch_assoc($app_res);

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
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
});
</script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-rupee"></i> Payment Details</h2><br/>
   <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Payment Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">From Location</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($p_row['from_location'],"name,city,state",$link1));?></td>
                <td width="20%"><label class="control-label">To Location</label></td>
                <td width="30%"><?php echo str_replace("~",",",getVendorDetails($p_row['to_location'],"name,city,state",$link1));?></td>
              </tr>
              <tr>
                <td><label class="control-label">Document No.</label></td>
                <td><?php echo $p_row['doc_no'];?></td>
                <td><label class="control-label">Amount</label></td>
                <td><?php echo $p_row['amount'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Pay Amount</label></td>
                <td><?php echo $p_row['rec_amount'];?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $p_row['status'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Payment Mode</label></td>
                <?php $mode=mysqli_query($link1,"select * from payment_mode where id='".$p_row['payment_mode']."'"); $mrow=mysqli_fetch_assoc($mode);?>
                <td><?php echo $mrow['mode'];?></td>
                <td><label class="control-label">Bank Name</label></td>
                <td><?php echo $p_row['bank_name'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Bank Branch</label></td>
                <td><?php echo $p_row['bank_branch'];?></td>
                <td><label class="control-label">DD/Cheque No</label></td>
                <td><?php echo $p_row['dd_cheque_no'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">DD/Cheque Date</label></td>
                <td><?php echo dt_format($p_row['dd_cheque_dt']);?></td>
                <td><label class="control-label">Receipt/Ref No.</label></td>
                <td><?php echo $p_row['receipt_no'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Transaction Id</label></td>
                <td><?php echo $p_row['transaction_id'];?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $p_row['remark'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo getAdminDetails($p_row['entry_by'],"name",$link1);?></td>
                <td><label class="control-label">Entry Date</label></td>
                <td><?php echo dt_format($p_row['entry_dt']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry Time</label></td>
                <td><?php echo $p_row['entry_time'];?></td>
                <td><label class="control-label">IP</label></td>
                <td><?php echo $p_row['ip'];?></td>
              </tr>
              
               <tr>
                <td><label class="control-label">Update Date & Time </label></td>
                <td><?php $dt=explode(' ',$p_row['update_dt_time']); echo dt_format($dt[0]).", ".$dt[1];?></td>
                <td><label class="control-label">Update By</label></td>
                <td><?php echo $p_row['update_by'];?></td>
              </tr>
              
              <tr>
                <td><label class="control-label">Approve By</label></td>
                <td><?php echo getAdminDetails($ap_row['action_by'],"name",$link1);?></td>
                <td><label class="control-label">Approve Date</label></td>
                <td><?php echo dt_format($ap_row['action_date']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Approve Time</label></td>
                <td><?php echo $ap_row['action_time'];?></td>
                <td><label class="control-label">Approve Remark</label></td>
                <td><?php echo $ap_row['action_remark'];?></td>
              </tr>
			  <tr>
                <td><label class="control-label">Payment Date</label></td>
                <td><?php echo dt_format($p_row['payment_date']);?></td>
                <td><label class="control-label"></label></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
  </div><!--close panel group-->
  <br><br>
  <div class="row" align="center">
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='paymentsendlist.php?<?=$pagenav?>'">
  </div>
  <br><br>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div>
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>