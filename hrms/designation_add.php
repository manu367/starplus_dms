<?php
////// Function ID ///////
$fun_id = array("a"=>array(84));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Save"){
	mysqli_autocommit($link1, false);
	$flag = true;
	
	//// Check duplicate department ////
	$name = ucwords($dname);
	if(mysqli_num_rows(mysqli_query($link1,"select designationid from hrms_designation_master where designame='".$name."' "))==0){
		///////// main queries //////////
		$add_dept="INSERT INTO hrms_designation_master set  designame ='".$name."',band='".$band."',status='".$status."',createdate='".date("Y-m-d H:i:s")."',createby='".$_SESSION['userid']."'";
		$res_dept=mysqli_query($link1,$add_dept);	
		$dptid = mysqli_insert_id($link1); 
		/// check if query is execute or not//
		if(!$res_dept){
			$flag = false;
			$err_msg = "Error 1". mysqli_error($link1) . ".";
		}
	}else{
		$flag = false;
		$msg = $name." Designation Name is already exist.";
		///// move to parent page
		header("location:designation_list.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	}
				
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$dptid,"DESIGNATION","ADD",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Designation is successfully added.";
		///// move to parent page
		header("location:designation_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:designation_list.php?msg=".$msg."&sts=fail".$pagenav);
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
      <h2 align="center"><i class="fa fa-vcard-o"></i> Add Designation </h2><br><br>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
     
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Designation Name <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<input type="text" name="dname" id="dname" class="form-control required" required/>
                  </div>    
              </div>  
          </div>
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Band <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                    <select name="band" id="band" class="form-control required" required>
                    	<option value="">--Please Select--</option>
                        <option value="1"<?php if(isset($_REQUEST['band'])){if($_REQUEST['band']==1){ echo "selected";}}?>>I</option>
                        <option value="2"<?php if(isset($_REQUEST['band'])){if($_REQUEST['band']==2){ echo "selected";}}?>>II</option>
                        <option value="3"<?php if(isset($_REQUEST['band'])){if($_REQUEST['band']==3){ echo "selected";}}?>>III</option>
                        <option value="4"<?php if(isset($_REQUEST['band'])){if($_REQUEST['band']==4){ echo "selected";}}?>>IV</option>
                        <option value="5"<?php if(isset($_REQUEST['band'])){if($_REQUEST['band']==5){ echo "selected";}}?>>V</option>
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
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onClick="window.location.href='designation_list.php?<?=$pagenav?>'">
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