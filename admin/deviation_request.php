<?php
require_once("../config/config.php");
@extract($_POST);
///// extract lat long
$latlong = explode(",",base64_decode($_REQUEST["latlong"]));

////// get pjp details
$rs=mysqli_query($link1,"select visit_area from pjp_data where id='".$_REQUEST['task_id']."'")or die(mysqli_error($link1));
$row2=mysqli_fetch_array($rs);
////// we hit save button

 if (isset($_POST['Submit']) && $_POST['Submit']=='Save'){
     //// Make System generated PO no.//////
	$flag = true;
	///// Insert Master Data
	 $query1= "INSERT INTO deviation_request set task_type = 'Dealer Visit' , sch_visit='".$sch_visit."',change_visit='".$chng_visit."', remark='".$remark."',entry_by='".$_SESSION['userid']."',entry_date='".$datetime."',entry_ip='".$ip."',app_status='Pending For Approval',pjp_id='".$_REQUEST['task_id']."'";
	$result = mysqli_query($link1,$query1)or die ("ER1".mysqli_error($link1));
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
    }
	
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Deviation request is successfully raised.";
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	
	///// move to parent page
  header("location:../salesforce/calender_event.php?msg=".$msg."".$pagenav);
   exit;
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">

<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>

</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-handshake-o"></i> Deviation Request </h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-5 control-label">Scheduled Visit</label>
            <div class="col-md-4">
                <input type="text" name="sch_visit" class="form-control"  id="sch_visit" value="<?=$row2["visit_area"]?>" readonly/>
	    	</div>
            </div>
          </div>
           <div class="form-group"> 
		   <div class="col-md-12"><label class="col-md-5 control-label">Change Visit <span class="red_small">*</span></label>
              <div class="col-md-4">
                <input type="text" name="chng_visit" class="form-control required"  id="chng_visit" required/>
              </div>
            </div>
           </div>
           <div class="form-group"> 
		   <div class="col-md-12"><label class="col-md-5 control-label">Remark</label>
              <div class="col-md-4">
                <textarea type="text" name="remark" class="form-control"  id="remark" style="resize:vertical"></textarea>
              </div>
            </div>
           </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Save" title="Raise Request">&nbsp;&nbsp;
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='../salesforce/calender_event.php?<?=$pagenav?>'">
            </div>
          </div>
    </form>
      </div>
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
