<?php
require_once("../config/config.php");
$id = base64_decode($_REQUEST['id']);
/////// notice info //////////
$info = mysqli_fetch_assoc(mysqli_query($link1, "SELECT * FROM hrms_notice WHERE sno = '".$id."' "));

////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Update"){
	mysqli_autocommit($link1, false);
	$flag = true;
	$file_path = $info['file_path'];
	
	//// Check duplicate department ////
	if($_FILES['notice_doc']["name"]!=''){	
	   //// upload doc into folder ////
		$file_name =$_FILES['notice_doc']['name'];
		$file_tmp =$_FILES['notice_doc']['tmp_name'];
		$file_path="../doc_attach/notice_doc/$today.$file_name";
		move_uploaded_file($file_tmp,$file_path);
		///// deleteing old attached file ///////	
		unlink($info['file_path']);
	} // end of file upload	
		
	/////// checking for duplicate entry /////////
	if(mysqli_num_rows(mysqli_query($link1, "SELECT sno FROM hrms_notice WHERE subject ='".$subject."' or msg = '".$notice_msg."' ")) == 1){	
		// insert all details of notice into table //
		$sql = "UPDATE hrms_notice SET subject ='".$subject."', msg = '".$notice_msg."', file_path = '".$file_path."', emp_id = '".$_SESSION['userid']."', status  = '".$status."' WHERE sno = '".$id."' ";
		
		$res =  mysqli_query($link1,$sql)or die("ER 1".mysqli_error($link1));
		$dptid = mysqli_insert_id($link1);
		/// check if query is execute or not//
		if(!$res){
			$flag = false;
			$err_msg = "Error 1". mysqli_error($link1) . ".";
		}
	}else{
		$msg = "This Notice Subject or Message is already exist.";
		///// move to parent page
		header("location:notice_list.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	}	
				
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$dptid,"NOTICE","UPDATE",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Notice is successfully added.";
		///// move to parent page
		header("location:notice_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:notice_list.php?msg=".$msg."&sts=fail".$pagenav);
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
 
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-bell"></i> Add Notice </h2><br><br>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data" >
      
      	  <div class="form-group">
            <div class="col-md-12"><label class="col-md-2 control-label">Subject <span class="red_small">*</span></label>
              <div class="col-md-8" >
              	<input type="text" name="subject" id="subject" class="form-control required" value="<?=$info['subject']?>" required>					
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-2 control-label">Message <span class="red_small">*</span></label>
              <div class="col-md-8" >
              	<textarea rows="4" name="notice_msg" id="notice_msg" class="form-control required" required><?=$info['msg']?></textarea>			
              </div>
            </div>
          </div>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-2 control-label"> Notice Doc. </label> 
                  <div class="col-md-8">
					<span>  
                  	<input type="file" name="notice_doc" id="notice_doc" class="form-control"  />
					</span>	
                  </div>    
              </div>  
          </div>
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-2 control-label">Status </label> 
                  <div class="col-md-8">
                  		<select class="form-control" name="status" id="status" >
                        	<option value="Active" <?php if($info['status'] == "Active"){ echo "selected"; } ?> > Active </option>
                            <option value="Deactive" <?php if($info['status'] == "Deactive"){ echo "selected"; } ?> > Deactive </option>
                        </select>
                  </div>    
              </div>  
          </div>  
         
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <button class="btn <?=$btncolor?>" type="submit" name="submit" value="Update" > Update </button>  
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onclick="window.location.href='notice_list.php?<?=$pagenav?>'">
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