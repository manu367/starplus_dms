<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
// echo "SELECT * FROM activity_master where id='".$docid."'";
// exit;
$po_sql="SELECT * FROM activity_master where id='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
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
      <h2 align="center"><i class="fa fa-users"></i> User Activities</h2>
      <h4 align="center"><?php echo $po_row["ref_no"];?></h4>
	 <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
   <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Activity Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
              	<td width="20%"><label class="control-label">Activity Type</label></td>
                <td width="30%"><?php echo  $po_row['activity_type'];?></td>
                <td width="20%"><label class="control-label">Party Name</label></td>
                <td width="30%"><i><?php echo $po_row['party_name'];?></td>
              </tr>

              <tr>
                <td><label class="control-label">Employee Name</label></td>
                <td><i><?php echo getAdminDetails($po_row['user_id'],"name",$link1);?></i></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $po_row['status'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo getAdminDetails($po_row['entry_by'],"name,oth_empid,username",$link1);?></td>
                <td><label class="control-label">Entry Date</label></td>
                <td><?php echo $po_row['entry_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Initial Remark</label></td>
                <td><?php echo $po_row['intial_remark'];?></td>
                <td><label class="control-label">Initial Attachment</label></td>
                <td><img src="../salesapi/activityimg/<?=substr($po_row['entry_date'],0,7).'/'.$po_row['initial_attach']?>" alt="" id="image" style="width: 30%;"/></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>

    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Activity History</div>
        <table class="table table-bordered" width="100%"> 
            <thead>
              <tr>
                <th width="25%">Remark</th>
                <th width="10%">Status</th>
                <th width="25%">Attachment</th>
                <th width="20%">Entry By</th>
                <th width="20%">Entry Date</th>
              </tr>
            </thead>
            <tbody>
                
            <?php
           
			$res_poapp=mysqli_query($link1,"SELECT * FROM activity_history where ref_no='".$po_row['ref_no']."'")or die("ERR1".mysqli_error($link1)); 
			while($row_poapp=mysqli_fetch_assoc($res_poapp)){
			?>
              <tr>
                <td><?php echo $row_poapp['remark'];?></td>
                <td><?php echo $row_poapp['status']?></td>
                <td><img src="../salesapi/activityimg/<?=substr($po_row['entry_date'],0,7).'/'.$row_poapp['attachment']?>" alt="" id="image" style="width: 50%;"/></td>
                <td><?php echo getAdminDetails($row_poapp['entry_by'],"name,oth_empid,username",$link1)?></td>
                <td><?php echo $row_poapp['entry_date']?></td>
              </tr>
             <?php }?>
            </tbody>
     </table>
     
    </div><!--close panel-->
    <br><br>
               <tr>
               <td colspan="4" align="center"><input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='userActivity.php?<?=$pagenav?>'"></td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
  </div><!--close panel group-->
  </form>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>