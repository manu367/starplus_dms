<?php
require_once("../config/config.php");
@extract($_POST);
///// extract lat long
$latlong = explode(",",base64_decode($_REQUEST["latlong"]));
////// we hit save button

 if (isset($_POST['upd']) && $_POST['upd']=='Save'){
     //// Make System generated PO no.//////
	$flag = true;
	///// Insert Master Data
	 $query1= "INSERT INTO dealer_visit set userid='".$_SESSION['userid']."',party_code='".$partycode."', remark='".$remark."',visit_date='".$today."',visit_city='',dealer_type='Old',address='',latitude='".$latlong[0]."',longitude='".$latlong[1]."',pjp_id='".$_REQUEST['task_id']."',ip='".$ip."'";
	$result = mysqli_query($link1,$query1)or die ("ER1".mysqli_error($link1));
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
    }
	if($_REQUEST['task_id']){
   		mysqli_query($link1,"UPDATE pjp_data SET task_acheive=task_acheive+1 WHERE id='".$_REQUEST['task_id']."'");
		$_REQUEST['task_id'] = "";
		unset($_REQUEST);
   }
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Visit Details successfully  updated.";
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
 <script src="../js/bootstrap-select.min.js"></script>

 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
});

</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
<script type="text/javascript" src="../js/ajax.js"></script>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/jquery-1.10.1.min.js"></script>

</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-handshake-o"></i> Update Dealer Visit Details </h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post" onSubmit="return validate();">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Dealer<span style="color:#F00">*</span></label>
              <div class="col-md-6">
               
                  <select name="from_location" id="from_location" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y'";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
	                      if($party_det[id_type]!='HO'){
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if(isset($_REQUEST['from_location']) && $result_chl['location_id']==$_REQUEST['from_location'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                    </option>
                    <?php
						  }
					}
                    ?>
                 </select>
              </div>
               <div class="col-md-3">
               <button type="button" id="addDealer" class="btn btn-info" data-dismiss="modal" onClick="window.location.href='asp_add.php?op=add&pid=39&hid=FN09'">Add New Dealer</button>
               </div>
            </div>
          </div>
         
       
        <div class="form-group">
          <div class="col-md-10">
          <div class="col-md-6">
              <label class="col-md-6 control-label">Remark</label>
              <div class="col-md-6">
               <textarea  name="remark" id="remark" class="form-control"></textarea>
                
              </div></div>
              
          </div>
        </div>
        
        <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Save" title="Save">
                <input type="hidden" name="parentcode" id="parentcode" value="<?=$_REQUEST['to_location']?>"/>
                <input type="hidden" name="partycode" id="partycode" value="<?=$_REQUEST['from_location']?>"/>
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='../salesforce/calender_event.php?<?=$pagenav?>'">
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
