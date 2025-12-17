<?php
require_once("../config/config.php");
////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Save"){
	mysqli_autocommit($link1, false);
	$flag = true;
	
	//// Check duplicate department ////
	$name = ucwords($leave_type);
	if(mysqli_num_rows(mysqli_query($link1,"select id from hrms_leave_master where type='".$name."' and no_of_days = '".$nod."' and emp_type = '".$emp_type."' "))==0){
		///////// main queries //////////
		$add_dept="INSERT INTO hrms_leave_master set  type = '".$name."', no_of_days ='".$nod."', from_date = '".$effective_date."', emp_type = '".$emp_type."', status='".$status."',create_date='".date("Y-m-d H:i:s")."',create_by='".$_SESSION['userid']."'";
		$res_dept=mysqli_query($link1,$add_dept);	
		$dptid = mysqli_insert_id($link1); 
		/// check if query is execute or not//
		if(!$res_dept){
			$flag = false;
			$err_msg = "Error 1". mysqli_error($link1) . ".";
		}
	}else{
		$flag = false;
		$msg = $name." leave is already exist.";
		///// move to parent page
		header("location:leave_list.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	}
				
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$dptid,"LEAVE","ADD",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Leave is successfully added.";
		///// move to parent page
		header("location:leave_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:leave_list.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	} 
    mysqli_close($link1);
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
 <script src="../js/bootstrap-datepicker.js"></script>
 <script>
 	$(document).ready(function() {
		$('#effective_date').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});
	});
 </script>
 
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-coffee"></i> Add Leave </h2><br><br>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
                     
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Type of Leave <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<input type="text" name="leave_type" id="leave_type" class="form-control required" required />
                  </div>    
              </div>  
          </div>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">No. of Days <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<input type="text" name="nod" id="nod" class="form-control digits required" required />
                  </div>    
              </div>  
          </div>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Effetcive Date <span class="red_small">*</span></label> 
                  <div class="col-md-6 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <input type="text" class="form-control span2 required" name="effective_date"  id="effective_date" required>
                        </div>
                  </div>    
              </div>  
          </div>      
     
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Employment Type <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<select name="emp_type" id="emp_type" class="form-control required" required>
                        <option value="" <?php if(isset($_REQUEST['emp_type'])){ if($_REQUEST['emp_type'] == "") { echo 'selected'; }}?>> -- Select -- </option>
                        <option value="Bench" <?php if(isset($_REQUEST['emp_type'])){ if($_REQUEST['emp_type'] == "Bench") { echo 'selected'; }}?>>Bench</option>
                        <option value="On Notice Period" <?php if(isset($_REQUEST['emp_type'])){ if($_REQUEST['emp_type'] == "On Notice Period") { echo 'selected'; }}?>>On Notice Period</option>
                        <option value="Permanent" <?php if(isset($_REQUEST['emp_type'])){ if($_REQUEST['emp_type'] == "Permanent") { echo 'selected'; }}?> >Permanent</option>
                        <option value="Probation" <?php if(isset($_REQUEST['emp_type'])){ if($_REQUEST['emp_type'] == "Probation") { echo 'selected'; }}?>>Probation</option>
                        <option value="Stipend" <?php if(isset($_REQUEST['emp_type'])){ if($_REQUEST['emp_type'] == "Stipend") { echo 'selected'; }}?>>Stipend</option>
                        <option value="Training" <?php if(isset($_REQUEST['emp_type'])){ if($_REQUEST['emp_type'] == "Training") { echo 'selected'; }}?>>Training</option>
                		</select>     
                  </div>    
              </div>  
          </div>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Status</label> 
                  <div class="col-md-6">
                  	<select name="status" id="status" class="form-control"  >
                        <option value="1"<?php if(isset($_REQUEST['status'])){if($_REQUEST['status']==1){ echo "selected";}}?>>Active</option>
                        <option value="2"<?php if(isset($_REQUEST['status'])){if($_REQUEST['status']==2){ echo "selected";}}?>>Deactive</option>
                     </select>
                  </div>    
              </div>  
          </div>
          
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <button class="btn <?=$btncolor?>" type="submit" name="submit" value="Save"> Save </button>  
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onclick="window.location.href='leave_list.php?<?=$pagenav?>'">
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