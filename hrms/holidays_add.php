<?php
require_once("../config/config.php");
////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Save"){
	mysqli_autocommit($link1, false);
	$flag = true;
	
	//// Check duplicate department ////
	$name = ucwords($description);
	if(mysqli_num_rows(mysqli_query($link1,"select sno from hrms_holidays_master where description='".$name."' and date = '".$holiday_date."' "))==0){
		///////// main queries //////////
		$add_dept="INSERT INTO hrms_holidays_master set  date = '".$holiday_date."', description ='".$name."',status='".$status."',create_date='".date("Y-m-d H:i:s")."',create_by='".$_SESSION['userid']."'";
		$res_dept=mysqli_query($link1,$add_dept);	
		$dptid = mysqli_insert_id($link1); 
		/// check if query is execute or not//
		if(!$res_dept){
			$flag = false;
			$err_msg = "Error 1". mysqli_error($link1) . ".";
		}
	}else{
		$flag = false;
		$msg = $name." holiday is already exist.";
		///// move to parent page
		header("location:holidays_list.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	}
				
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$dptid,"HOLIDAY","ADD",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Holiday is successfully added.";
		///// move to parent page
		header("location:holidays_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:holidays_list.php?msg=".$msg."&sts=fail".$pagenav);
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
		$('#holiday_date').datepicker({
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
      <h2 align="center"><i class="fa fa-plane"></i> Add Holiday </h2><br><br>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
                     
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Date <span class="red_small">*</span></label> 
                  <div class="col-md-6 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <input type="text" class="form-control span2 required" name="holiday_date"  id="holiday_date" required>
                        </div>
                  </div>    
              </div>  
          </div>      
     
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Description <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                    <textarea name="description" id="description" class="form-control addressfield required" required ></textarea>
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
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onclick="window.location.href='holidays_list.php?<?=$pagenav?>'">
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