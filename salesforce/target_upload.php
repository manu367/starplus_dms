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
		move_uploaded_file($_FILES["attchfile"]["tmp_name"],"../upload/target_upload/".$now.$_FILES["attchfile"]["name"]);
		$f_name=$now.$_FILES["attchfile"]["name"];
		//////insert into upload file data////////////
		mysqli_query($link1,"insert into upload_file_data set file_name='".$f_name."',entry_date='".$today."',entry_time='".$currtime."'");
		$file_id=mysqli_insert_id($link1);
		$docno = date("YmdHis");
		
		$r=array();
		$err_msg = "";
		list($cols) = $xlsx->dimension();
		foreach( $xlsx->rows() as $k => $r) {
	 		if ($k == 0) continue; // skip first row 
	  		for( $i = 0; $i < count($k); $i++)
	  		{
		  		/// check excel row data
	      		if($r[0]=='' && $r[1]=='' && $r[2]=='' && $r[3]=='' && $r[4]=='' && $r[5]=='' && $r[6]==''){
		  		}
		  		else{
					////Make Variable for each element of excel//////
					$target_year = trim($r[0]);
					$target_month = trim($r[1]);
					$target_psc = $r[2];
					$target_uid = trim($r[3]);
					$target_empid = trim($r[4]);
					$target_task = $r[5];
					$target_val = trim($r[6]);
					$target_remark = $r[7];
					if($target_task=="Dealer Visit" || $target_task=="Collection" || $target_task=="Feedback" || $target_task=="Sale Order" || $target_task=="BTL Activity" || $target_task=="Meeting" || $target_task=="Dealer Activeness"){
						if($target_month!="" && $target_year!="" && $target_uid!=""){
							
							//////// 
							$sql = "INSERT INTO sf_target_data SET target_no = '', prod_code='".$target_psc."', target_val ='".$target_val."',month ='".$target_month."',year ='".$target_year."',emp_id ='".$target_empid."',user_id ='".$target_uid."',task_name='".$target_task."',remark='".$target_remark."', status = 'Active'";
							$res1 = mysqli_query($link1,$sql);
							if(!$res1){
								$flag = false;
								$err_msg = "Error 1". mysqli_error($link1) . ".";
							}
						}
					}
		  		}
	  		}
		}//Close For loop
		$chk = 0;
		////// make target no. for master table
		$res_tardata = mysqli_query($link1,"SELECT month, year, user_id, emp_id, SUM(target_val) as tarval FROM sf_target_data WHERE target_no='' GROUP BY month,year,user_id");
		while($row_tardata=mysqli_fetch_assoc($res_tardata)){
			/////// generate target no //////////
			$tar_qr = mysqli_fetch_array(mysqli_query($link1, "SELECT MAX(temp_no) AS tn FROM sf_target_master WHERE user_id='".$row_tardata["user_id"]."'"));
			$temp_id = $tar_qr[0];
			/// make 3 digit padding
			$pad = str_pad(++$temp_id,3,"0",STR_PAD_LEFT);	
			$targetid = "TR/".$row_tardata["year"].$row_tardata["month"]."/".strtoupper($row_tardata["user_id"])."/".$pad;
			// insert all details of target into target master table //
			$sql_master = "INSERT INTO sf_target_master SET target_no ='".$targetid."', temp_no = '".$temp_id."', month = '".$row_tardata["month"]."', year = '".$row_tardata["year"]."', target_type = 'MIX', period_type = 'Monthly', user_id = '".$row_tardata["user_id"]."', emp_id = '".$row_tardata["emp_id"]."', entry_screen = 'FRONT UPLOAD', status = 'Active', remark = 'Target Uploader', target_val = '".$row_tardata["tarval"]."', create_date  = '".$today."', create_by  = '".$_SESSION['userid']."'";
			$res_master =  mysqli_query($link1,$sql_master);
			if(!$res_master){
				$flag = false;
				$err_msg = "Error 2". mysqli_error($link1) . ".";
			}
			///// update target no. in data table
			$res3 = mysqli_query($link1,"UPDATE sf_target_data SET target_no = '".$targetid."' WHERE user_id='".$row_tardata["user_id"]."' AND month='".$row_tardata["month"]."' AND year='".$row_tardata["year"]."' AND target_no=''");
			if(!$res3){
				$flag = false;
				$err_msg = "Error 3". mysqli_error($link1) . ".";
			}
			$chk++;
		}
		if($chk>0){
			$msg = "sucessfully uploaded";
		}else{
			$msg = "something went wrong ".$err_msg;
		}
	    mysqli_query($link1,"UPDATE upload_file_data SET status='1' WHERE id='".$file_id."'");
		header("location:target_list.php?msg=".$msg."&sts=success".$pagenav);
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
 <script src="../js/jquery-1.10.1.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 
 <script>
  $(document).ready(function(){
	  $("#frm1").validate();
  });
  // When the document is ready
 </script>
 <script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script src="../js/fileupload.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-upload"></i> Upload Target</h2>
      
      <div style="display:inline-block;float:left"><a href="../excelReports/prodsubcatmaster.php?status=&prod_cat=" title="Export Product Sub-category details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Product Sub-category details in excel"></i></a><br/>Download Product Sub-category list</div>
      
		<div style="display:inline-block;float:right"><a href="../templates/UPLOAD_TARGET.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div><br/>
      
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
                5. BTL Activity<br/>
                6. Meeting<br/>
                7. Dealer Activeness<br/>    
              </div>
              
            </div>
          </div>
         <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Upload'){?>disabled<?php }?>>
              <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onClick="window.location.href='target_list.php?<?=$pagenav?>'">
            </div>
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