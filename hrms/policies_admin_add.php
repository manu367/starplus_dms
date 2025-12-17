<?php
require_once("../config/config.php");
$id = base64_decode($_REQUEST['id']);

////// final submit form ////
@extract($_POST);
if($_POST['request']=="Request"){
	mysqli_autocommit($link1, false);
	$flag = true;
		
	//// Check duplicate department ////
	if($_FILES['policy_doc']["name"]!=''){	
	   //// upload doc into folder ////
		$file_name =$_FILES['policy_doc']['name'];
		$file_tmp =$_FILES['policy_doc']['tmp_name'];
		$file_path="../doc_attach/policy_doc/$today.$file_name";
		move_uploaded_file($file_tmp,$file_path);	
	} // end of file upload	
		
	// insert all details of leave into table //
	$sql="INSERT INTO hrms_policy_master set subject ='".$subject."', msg = '".$policy_note."', release_date = '".$release_date."' , filename = '".$today.'.'.$file_name."', filepath = '".$file_path."', type = '".$policy_type."' ,  status  = 'active' ,  create_by = '".$_SESSION['userid']."', create_date  = '".$today."'";
	
	$res_leave =  mysqli_query($link1,$sql)or die("ER1".mysqli_error($link1));
	$dptid = mysqli_insert_id($link1);
	/// check if query is execute or not//
	if(!$res_leave){
		$flag = false;
		$err_msg = "Error 1". mysqli_error($link1) . ".";
	}
		
				
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$dptid,"POLICY","ADD",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Policy is successfully released.";
		///// move to parent page
		header("location:policies_admin_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:policies_admin_list.php?msg=".$msg."&sts=fail".$pagenav);
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
 <script src="../js/bootstrap-datepicker.js"></script>
 <script>
 	$(document).ready(function(){
		$('#release_date').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});
	});
 </script>
 <script src="../js/editor.js"></script>
 <link href="../css/editor.css" type="text/css" rel="stylesheet"/>
 
 <script type="text/javascript">
 	$(document).ready(function() {
		$("#txtEditor1").Editor();
	});
	function setHtmlAreaValue1(){
		document.request.policy_note.value =  $("#txtEditor1").Editor("getText");
	}
 </script>

</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-file-text-o"></i> Add Policy </h2><br><br>
      <form name="request" id="request" class="form-horizontal" action="" method="post" enctype="multipart/form-data" >
      
      	  <div class="form-group">
            <div class="col-md-12"><label class="col-md-2 control-label">Subject <span class="red_small">*</span></label>
              <div class="col-md-8" >
              	<input type="text" name="subject" id="subject" class="form-control required" required>					
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-2 control-label">Message <span class="red_small">*</span></label>
              <div class="col-md-8" >
              	<textarea name="policy_note" id="txtEditor1" class="form-control required" placeholder="" required> </textarea>			
              </div>
            </div>
          </div>
          
      	  <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-2 control-label">Policy Type <span class="red_small">*</span></label> 
                  <div class="col-md-8">
                  <select name="policy_type" id="policy_type" class="form-control required" required >
                      <option value=""  <?php if(isset($_REQUEST['policy_type']) == "") { echo 'selected'; }?> > -- Please Select -- </option>
                      <option value="AR" <?php if(isset($_REQUEST['policy_type']) == "AR") { echo 'selected'; }?> >Awards & Recognition</option>
                      <option value="BP" <?php if(isset($_REQUEST['policy_type']) == "BP") { echo 'selected'; }?> >Benefit Plan</option>
             		  <option value="OTH" <?php if(isset($_REQUEST['policy_type']) == "OTH") { echo 'selected'; }?> >Other</option>
                  </select>
                  </div>    
              </div>  
          </div>
        
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-2 control-label">Release Date <span class="red_small">*</span></label> 
                  <div class="col-md-8 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <input type="text" class="form-control span2" name="release_date"  id="release_date" onChange="date_range();" >
                        </div>
                  </div>    
              </div>  
          </div>  
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-2 control-label"> Policy Document </label> 
                  <div class="col-md-8">
                  	<input type="file" name="policy_doc" id="policy_doc" class="form-control"  />
                  	<!--accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel,image/*,application/pdf,.xls,.doc,.docx,.txt "-->
                  </div>    
              </div>  
          </div>
         
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <button class="btn <?=$btncolor?>" type="submit" name="request" value="Request" onClick="setHtmlAreaValue1()" > Request </button>  
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onclick="window.location.href='policies_admin_list.php?<?=$pagenav?>'">
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