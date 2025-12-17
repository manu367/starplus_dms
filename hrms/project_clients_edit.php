<?php
require_once("../config/config.php");
$id = base64_decode($_REQUEST['id']);

////// find project information ////
$info = mysqli_fetch_array ( mysqli_query ( $link1, " SELECT * FROM  hrms_project_master WHERE project_id = '".$id."' " ) );

////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Update"){
	mysqli_autocommit($link1, false);
	$flag = true;
		
	//// Check duplicate department ////
	$projectname = ucfirst($project_name);
	$clientname = ucwords($client_name);
	if(mysqli_num_rows(mysqli_query($link1,"select id from hrms_project_master where project_name = '".$projectname."' and client = '".$clientname."' ")) == 1){
		///////// main queries //////////
		$add_proj = "UPDATE hrms_project_master SET  client = '".$clientname."', client_mobile = '".$client_mobile."', client_email = '".$client_email."', project_name = '".$projectname."', project_description = '".$project_description."', manage_by = '".$manage_by."', tentative_date = '".$tentative_date."', update_by = '".$_SESSION['userid']."', update_date = '".$today."', status = '".$status."' WHERE project_id = '".$id."' ";
		
		$res_proj = mysqli_query($link1,$add_proj);	 
		/// check if query is execute or not//
		if(!$res_proj){
			$flag = false;
			$err_msg = "Error 1". mysqli_error($link1) . ".";
		}
	}else{
		$flag = false;
		$msg = "Project with same Client is already exist.";
		///// move to parent page
		header("location:project_clients_list.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	}
				
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$id,"PROJECT","UPDATE",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Project is successfully updated.";
		///// move to parent page
		header("location:project_clients_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:project_clients_list.php?msg=".$msg."&sts=fail".$pagenav);
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
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script>
 	$(document).ready(function(){
		$("#frm1").validate();
	}); 
	
 	$(document).ready(function() {
		$('#tentative_date').datepicker({
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
      <h2 align="center"><i class="fa fa-gear"></i> Edit Project </h2>
      <h5 align="center"> ( Project Code -  <?=$id;?> ) </h5><br>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
                     
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Project Name <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<input type="text" name="project_name" id="project_name" class="form-control required" required value="<?=$info['project_name']?>" >			
                  </div>    
              </div>  
          </div>  
     
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Description <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                    <textarea rows="4" name="project_description" id="project_description" class="form-control addressfield required" required ><?=$info['project_description']?></textarea>
                  </div>    
              </div>  
          </div>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Tentative Date <span class="red_small">*</span></label> 
                  <div class="col-md-6 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <input type="text" class="form-control span2 required" name="tentative_date"  id="tentative_date" required value="<?=$info['tentative_date']?>" >
                        </div>
                  </div>    
              </div>  
          </div> 
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Client Name <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<input type="text" name="client_name" id="client_name" class="form-control required" value="<?=$info['client']?>" required >			
                  </div>    
              </div>  
          </div>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Client Mobile <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<input type="text" name="client_mobile" id="client_mobile" class="form-control required number" value="<?=$info['client_mobile']?>" required >			
                  </div>    
              </div>  
          </div>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Client Email </label> 
                  <div class="col-md-6">
                  	<input type="text" name="client_email" id="client_email" class="form-control email" value="<?=$info['client_email']?>"  >			
                  </div>    
              </div>  
          </div>
          
          <?php 
		  	$qr1 = mysqli_query($link1 ," SELECT DISTINCT(managerid) FROM hrms_employe_master WHERE  status = 'active' order by empname DESC");
		  ?>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Manager <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<select name="manage_by" id="manage_by" class="form-control required" required >
                        <option value="" <?php if($info['manage_by'] == ""){ echo "selected"; } ?> > -- Please Select -- </option>
                        <?php while($row1 = mysqli_fetch_array($qr1)){ if($row1[0]!=""){ ?>
                        <option value="<?=$row1[0];?>" <?php if($info['manage_by'] == $row1[0]){ echo "selected"; } ?> > <?php echo getAnyDetails($row1[0],'empname','loginid','hrms_employe_master',$link1)." | ".$row1[0];?> </option>
                        <?php }} ?>
                    </select>
                  </div>    
              </div>  
          </div> 
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Status</label> 
                  <div class="col-md-6">
                  	<select name="status" id="status" class="form-control"  >
                        <option value="Active"<?php if(isset($info['status'])){if($info['status']=="Active"){ echo "selected";}}?>>Active</option>
                        <option value="Deactive"<?php if(isset($info['status'])){if($info['status']=="Deactive"){ echo "selected";}}?>>Deactive</option>
                    </select>
                  </div>    
              </div>  
          </div>
          
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <button class="btn <?=$btncolor?>" type="submit" name="submit" value="Update"> Update </button>  
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onclick="window.location.href='project_clients_list.php?<?=$pagenav?>'">
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