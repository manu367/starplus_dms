<?php
require_once("../config/config.php");

////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Upload"){
	mysqli_autocommit($link1, false);
	$flag = true;
	
	$check_file = "../doc_attach/my_documents/".$today."".$_SESSION['userid']."".$_FILES['attach_doc']['name'];
	$fname = ucwords($doc_name);
	//// check duplicate entry
	if(mysqli_num_rows(mysqli_query($link1, "SELECT id FROM hrms_my_document_master WHERE  (file_name = '".$fname."' or file_path = '".$check_file."') and entry_by = '".$_SESSION['userid']."' ")) == 0){	
		if($_FILES['attach_doc']["name"]!=''){	
		   //// upload doc into folder ////
		 	$file_name =$_FILES['attach_doc']['name'];
			$file_tmp =$_FILES['attach_doc']['tmp_name'];
			$file_path = "../doc_attach/my_documents/".$today."".$_SESSION['userid']."".$file_name;
			move_uploaded_file($file_tmp,$file_path);	
			
	        ///// INSERT INTO emp_resign  TABLE////
            $usr_add = "insert into hrms_my_document_master set file_name  ='".$fname."', file_path ='".$file_path."', entry_date='".$today."',  entry_by ='".$_SESSION['userid']."' ";
         	$res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
	
	        /// check if query is execute or not//
			if(!$res_add){
				$flag = false;
				$err_msg = "Error 1". mysqli_error($link1) . ".";
			}	
		}
	}else{
		$flag = false;
		$msg = "Please Select Other File Name Or Document, This is already exist.";
		///// move to parent page
		header("location:my_document_list.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	}
	/// ensert id find
	$dptid = mysqli_insert_id($link1);	
		
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$dptid,"DOCUMENT","ADD",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Document is successfully uploaded.";
		///// move to parent page
		header("location:my_document_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:my_document_list.php?msg=".$msg."&sts=fail".$pagenav);
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
      <h2 align="center"><i class="fa fa-file-o"></i> Add Document </h2>
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
                  <label class="col-md-4 control-label"> Doc. Name <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<input type="text" name="doc_name" id="doc_name" class="form-control required" required />
                  </div>    
              </div>  
          </div>
          
    	  <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label"> Attached Document <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                  	<input type="file" name="attach_doc" id="attach_doc" class="form-control required"  required />
                  </div>    
              </div>  
          </div>
                    
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <button class="btn <?=$btncolor?>" type="submit" name="submit" value="Upload"> Upload </button>  
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onclick="window.location.href='my_document_list.php?<?=$pagenav?>'">
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