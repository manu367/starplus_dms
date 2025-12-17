<?php
require_once("../config/config.php");
$quote = mysqli_query($link1,"select * from sf_quote_master where quote_no = '".$_REQUEST['id']."'");
$lrow = mysqli_fetch_assoc($quote);
////// final submit form ////
@extract($_POST);
if($_POST){
	if($_POST['Submit']=='Update'){
		mysqli_query($link1,"update sf_quote_master set  status='".$status."', approve_by='".$_SESSION['userid']."', approve_remark='".$remark."' where quote_no = '".$_REQUEST['id']."'")or die(mysqli_error($link1));
		
		dailyActivity($_SESSION['userid'],$lrow['quote_no'],"QUOTE","APPROVAL",$ip,$link1,"");
		$msgg="Quote"." ".$lrow['quote_no']." is updated successfully with status"." ". get_status($status,$link1);
		header("Location:quote_list.php?msg=$msgg&sts=success".$pagenav);
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
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">

 <script type="text/javascript">
$(document).ready(function(){
        $("#frm1").validate();
});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-child"></i> Quote Approval</h2>
      <h4 align="center"> Quote No - <?=$_REQUEST['id']?></h4><br/><br/>
   <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">   
   <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Quote Details</div>
        <div class="panel-body">
         <table class="table table-bordered" width="100%">
         	<thead>
            	<tr>
                	<th>S.No.</th>
                    <th>Quote No.</th>
                    <th>Sales Executive</th>
                    <th>Party(Customer)</th>
                    <th>Party Address</th>
                    <!---<th>Lead Source</th>--------->
                    <th>Create Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
			$sno = 0;
			$so = mysqli_query($link1,"select * from sf_quote_master  where quote_no ='".$_REQUEST['id']."'")or die(mysqli_error($link1));
			while($qrow = mysqli_fetch_assoc($so)){
				$sno = $sno+1;
			?>	
              <tr>
                <td><?php echo $sno;?></td>
                <td><?php echo $qrow['quote_no'];?></td>
                <td><?php echo ucwords(getAdminDetails($qrow['sales_executive'],"name",$link1));?></td>
                <td><?php echo $qrow['party_id'];?></td>
                <td><?php echo $qrow['address'];?></td>
                <!---<td><?php // echo get_leadsource($qrow['lead_source'],$link1);?></td>---->
                <td style=" text-align:center;"><?php echo  ($qrow['create_dt']);?></td>
                <td><?php echo get_status($qrow['status'],$link1);?></td>
              </tr>
              <?php
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
  	<div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Approval Action</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="50%"><label class="control-label">Action Taken <span class="red_small">*</span></label></td>
                <td width="50%">
                 			<input type="radio" name="status" id="approve" value="14" required/>
 							<label for="approve"><strong>Approve</strong></label>
 							<input type="radio" name="status" id="reject" value="15" required />
 							<label for="reject"><strong>Reject</strong></label>
                            <input type="radio" name="status" id="hold" value="16"  required="required"/>
 							<label for="hold"><strong>Hold</strong></label>
                </td>
              </tr>
              <tr>
                <td><label class="control-label">Remark <span class="red_small">*</span></label></td>
                <td><textarea name="remark" id="remark" required class="required addressfield form-control" style="resize:vertical;width:300px;"></textarea></td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Update" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>&nbsp;
                  <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='quote_list.php?<?=$pagenav?>'">
                  </td>
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