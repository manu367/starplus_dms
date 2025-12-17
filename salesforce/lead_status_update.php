<?php
require_once("../config/config.php");
$so = mysqli_query($link1,"select * from sf_lead_master  where lid='".$_REQUEST['id']."'")or die(mysqli_error($link1));
$row = mysqli_fetch_assoc($so);
////// final submit form ////
@extract($_POST);
if($_POST){
	if($_POST['Submit']=='Save'){
		mysqli_query($link1,"UPDATE sf_lead_master set status='".$status."', intial_remark='".$remark."', update_by='".$_SESSION['userid']."' where lid='".$_REQUEST['id']."'")or die(mysqli_error($link1));

		dailyActivity($_SESSION['userid'],$row['reference'],"LEAD","STATUS CHANGE",$ip,$link1,"");

		set_history($row['partyid'], $status, $row['reference'], "Lead Status Change",$_SESSION['userid'],$link1);
		$msgg="Lead"." ".$row['reference']." is updated successfully with status ".get_status($status,$link1);
		header("Location:lead_list.php?msg=$msgg&sts=success&page=lead".$pagenav);
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
      <h2 align="center"><i class="fa fa-child"></i> Change Lead Status</h2>
      <h4 align="center">Lead No. : <?=$row["reference"]?></h4>
      <br/>
   <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">   
   <div class="panel-group">
     <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Change Status</div>
      <div class="panel-body">
      	<div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Status <span style="color:red">*</span></label>
              <div class="col-md-6">              
               <select name="status" id="status" class="form-control required" required>
                  <option value="">Select Status</option>
                  <?php $st=mysqli_query($link1,"select * from sf_status_master where display_for='lead' order by status_name");
                        while($r=mysqli_fetch_assoc($st))
                        {
                  ?>
                  <option value="<?php echo $r['id'];?>"<?php if($r['id']==$row['status']){echo "selected='selected'";}?>><?php echo $r['status_name'];?></option>
                  <?php } ?>
              </select>  
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Remark <span style="color:red">*</span></label>
              <div class="col-md-6">              
               <textarea name="remark" id="remark" class="form-control addressfield required" required></textarea> 
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Save" title="" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='lead_list.php?<?=$pagenav?>'">
            </div>
          </div>
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