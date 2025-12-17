<?php
require_once("../config/config.php");

////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Save"){
	mysqli_autocommit($link1, false);
	$flag = true;
	$file_name = "";
	$file_path = "";
	
	if($_FILES['activity_doc']["name"]!=''){	
	    //// upload doc into folder ////
		$file_name =$_FILES['activity_doc']['name'];
		$file_tmp =$_FILES['activity_doc']['tmp_name'];
		$file_path="../doc_attach/activity_doc/$today.$file_name";
		move_uploaded_file($file_tmp,$file_path);	
	}	
		
	////// count max no. of activity in selected state
	$query_code="select MAX(temp_no) as tn from hrms_activities_master where 1 ";
	$result_code=mysqli_query($link1,$query_code)or die("ER2".mysqli_error($link1));
	$arr_result2=mysqli_fetch_array($result_code);
	$code_id=$arr_result2[0];
	/// make 4 digit padding
	$pad=str_pad(++$code_id,6,"0",STR_PAD_LEFT);		
	$activate_no = "A".$pad; 
		
	///// INSERT INTO hrms_activities_master  TABLE////
	$act_add="insert into hrms_activities_master set temp_no  ='".$code_id."', activity_no ='".$activate_no."', activity_desc='".$activity."',  activity_detail ='".$activity_details."', file_name ='".$file_name."',  file_path = '".$file_path."'  , 	status = 'Active' , create_by = '".$_SESSION['userid']."'  ,create_date = '".$today."'  ";

	$res_add=mysqli_query($link1,$act_add)or die("error3".mysqli_error($link1));

	/// check if query is execute or not//
	if(!$res_add){
		$flag = false;
		$err_msg = "Error 1". mysqli_error($link1) . ".";
	}	
	
									
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$activate_no,"ACTIVITY","ADD",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Activity is successfully added.";
		///// move to parent page
		header("location:activities_emp_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:activities_emp_list.php?msg=".$msg."&sts=fail".$pagenav);
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
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script>
	$(document).ready(function(){
		$("#frm1").validate();
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
      <h2 align="center"><i class="fa fa-cogs"></i> Add Activity </h2>
      <?php if($_REQUEST['msg']!=''){?>
      	<h4 align="center">
        	<span 
			<?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span>
        </h4>
	  <?php }?>
      <br><br>
     
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data" >
                     
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label"> Activity <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<input type="text" name="activity" id="activity" class="form-control required" required />
                  </div>    
              </div>  
          </div>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label"> Activity Details <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<textarea class="form-control addressfield required" id="activity_details" name="activity_details" rows="10" required ></textarea>
                  </div>    
              </div>  
          </div>
          
    	  <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label"> Attachment </label> 
                  <div class="col-md-6">
                  	<input type="file" name="activity_doc" id="activity_doc" class="form-control" />
                  </div>    
              </div>  
          </div>
       
                    
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <button class="btn <?=$btncolor?>" type="submit" name="submit" value="Save"> Save </button>  
                  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='activities_emp_list.php?<?=$pagenav?>'">
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