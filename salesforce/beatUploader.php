<?php
require_once("../config/config.php"); 
function unixstamp( $excelDateTime ) {
    $d = floor( $excelDateTime ); // seconds since 1900
    $t = $excelDateTime - $d;
    return ($d > 0) ? ( $d - 25569 ) * 86400 + $t * 86400 : $t * 86400;
}
//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="Upload"){
	if ($_FILES["attchfile"]["name"]) {
		require_once "../includes/simplexlsx.class.php";
		$xlsx = new SimpleXLSX( $_FILES['attchfile']['tmp_name'] );	
		move_uploaded_file($_FILES["attchfile"]["tmp_name"],"../upload/beat_upload/".$now.$_FILES["attchfile"]["name"]);
		$f_name=$now.$_FILES["attchfile"]["name"];
		//////insert into upload file data////////////
		mysqli_query($link1,"insert into upload_file_data set file_name='".$f_name."',entry_date='".$today."',entry_time='".$currtime."'");
		$file_id=mysqli_insert_id($link1);
		$docno = date("YmdHis");
		
		$r=array();
		list($cols) = $xlsx->dimension();
		foreach( $xlsx->rows() as $k => $r) {
	 		if ($k == 0) continue; // skip first row 
	  		for( $i = 0; $i < count($k); $i++)
	  		{
		  		/// check excel row data
	      		if($r[0]=='' && $r[1]=='' && $r[2]=='' && $r[3]=='' && $r[4]==''){
		  		}
		  		else{
	      			////Make Variable for each element of excel//////
				  	$plandate = $r[0];
				  	$empid = $r[1];
				  	$areatovisit =$r[2];
					$task =$r[3];
					$task_tgtcnt =$r[4];
	  				//////// 
	      			$sql = "INSERT INTO pjp_data SET document_no = '".$docno."', pjp_name='BEAT UPLOAD', plan_date ='".$plandate."',task ='".$task."',assigned_user ='".$empid."',visit_area ='".$areatovisit."',entry_date ='".$today."',entry_by='".$_SESSION['userid']."',file_name='".$f_name."',task_count='".$task_tgtcnt."'";
          			mysqli_query($link1,$sql);
		  		}
	  		}
		}//Close For loop
	    mysqli_query($link1,"UPDATE upload_file_data SET status='1' WHERE id='".$file_id."'");
		header("Location:beatUploader.php?msg=sucessfully uploaded".$pagenav);
		exit;	
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
  $(document).ready(function(){
	  $("#frm1").validate();
  });
  	// When the document is ready
  </script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/fileupload.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-upload"></i> Upload Beat Scheduler</h2><div style="display:inline-block;float:right"><a href="../templates/UPLOAD_BEAT_SCHEDULER.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
        <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
              <div class="col-md-4">
                  <div class="input-group">
                    <label class="input-group-btn">
                        <span class="btn btn-primary">
                            Browse&hellip; <input type="file" name="attchfile" id="attchfile" class="form-control required" required style="display:none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                        </span>
                    </label>
                    <input type="text" class="form-control" name="beatfile"  id="beatfile" readonly>
                </div>
              </div>
              <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">NOTE<span class="red_small">**</span></label>
              <div class="col-md-6 text-danger">
              	Below mentioned <strong>Task Name</strong> must be filled in template only.<br/>
                1. Dealer Visit<br/>
				2. Collection<br/>
                3. Feedback<br/>
                4. Sale Order<br/>    
              </div>
              
            </div>
          </div>
         <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
            </div>
          </div>
          <br/>
          <br/>
          <br/>
          <br/>
          <div class="form-group">
            <div class="col-md-12" align="center"><button title="Check Beat Scheduled" type="button" class="btn btn-primary" onClick="window.location.href='beatScheduled.php?<?=$pagenav?>'"><span>Check Beat Scheduled</span></button></div>
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