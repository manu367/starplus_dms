<?php
require_once("../config/config.php");
$so = mysqli_query($link1,"select * from sf_quote_master where quote_no = '".$_REQUEST['id']."'")or die(mysqli_error($link1));
$row = mysqli_fetch_assoc($so);
////// final submit form ////
@extract($_POST);
if($_POST['Submit']=='Upload'){
	$folder="doc_attach/quote";
	///// attachment 
	if ($_FILES["att_file"]["size"]>2097152){
		$msgg="File size should be less than or equal to 2 mb";
		header("Location:quote_attach.php?msg=$msgg&sts=fail&page=quote");
	}
	else{ 
		$file_name = $_FILES['att_file']['name'];
		$file_tmp =$_FILES['att_file']['tmp_name'];
		$up=move_uploaded_file($file_tmp,"../".$folder."/".time().$file_name);
		$path1="../".$folder."/".time().$file_name;	
		$img_name1=time().$file_name;
		
		if($up){
			$result = mysqli_query($link1,"INSERT INTO sf_tbl_party_document set path='".$path1."', file_name='".$img_name1."', party_id='".$_REQUEST['party']."', transaction_id='".$_REQUEST['id']."', create_dt = '".$today."', create_time = '".$currtime."', create_by = '".$_SESSION['userid']."', ip = '".$ip."', status = '18' ")or die(mysqli_error($link1));
				
			$msgg="File Uploaded successfully.";		
			header("Location:quote_list.php?msg=$msgg&sts=success&page=quote".$pagenav);	
		}else{
			$msgg="Please select File from system.";		
			header("Location:quote_attach.php?msg=$msgg&sts=fail&page=quote".$pagenav);	
		}
	}
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

 <script type="text/javascript">

$(document).ready(function(){
        $("#frm1").validate();
});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-paperclip"></i> Quote Attach </h2>
      <h4 align="center"> Quote No - <?=$row["quote_no"]?></h4><br/><br/>
    
   <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data">   
   <div class="panel-group">
     <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">File Upload</div>
      <div class="panel-body">
      	<div class="form-group">
            <div class="col-md-10"><label class="col-md-8 control-label">Attach File (Allowed jpg, png, gif, jpeg and Upload upto 2 MB)  <span class="red_small">*</span></label>
              <div class="col-md-4">              
               <input type="file" class="form-control required" name="att_file" id="att_file" accept="image/*" required/>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="upload" value="Upload" title="" <?php if($_POST['Submit']=='Upload'){?>disabled<?php }?>>
              <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='quote_list.php?<?=$pagenav?>'">
            </div>
          </div>
      </div><!--close panel body-->
    </div><!--close panel-->
  </div><!--close panel group-->
  </form>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>