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
	move_uploaded_file($_FILES["attchfile"]["tmp_name"],"../upload/delivery_matrix/".$now.$_FILES["attchfile"]["name"]);
	$f_name=$now.$_FILES["attchfile"]["name"];
	//////insert into upload file data////////////
	mysqli_query($link1,"insert into upload_file_data set file_name='".$f_name."',entry_date='".$today."',entry_time='".$currtime."'");
	$file_id=mysqli_insert_id($link1);
	list($cols) = $xlsx->dimension();
	$eof = 0;	
	foreach( $xlsx->rows() as $k => $r) {
	 if ($k == 0) continue; // skip first row 
	  for( $i = 0; $i < count($k); $i++)
	  {
		  /// check excel row data
	      if($r[0]=='' || $r[1]=='' || $r[2]==''){
			  
		  }
		  else{
	      ////Make Variable for each element of excel//////
		  $from_loc = $r[0];
		  $to_loc = $r[1];
		  $deliv_day = $r[2];
	  
	     $sql="INSERT IGNORE INTO product_delivery_matrix set from_location='".$from_loc."',to_location='".$to_loc."',productcategory='".$prod_cat."',delivery_days='".$deliv_day."',create_date='".$datetime."',create_by='".$_SESSION['userid']."'";
          mysqli_query($link1,$sql);
		  $eof++;
		  }
	  }
	}//Close For loop
	//// check excel file is completely uploaded///
	if($eof>0){
	    mysqli_query($link1,"update upload_file_data set status='1' where id='".$file_id."'");
		$msg = "File is successfully uploaded";
		header("Location:product_delivery.php?msg=".$msg."".$pagenav);
		exit;
    }
	else{
		$msg="File is not uploaded Properly.Please Upload it again.";	
		header("Location:product_delivery.php?msg=".$msg."".$pagenav);
        exit;
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
 
 <script>
  $(document).ready(function(){
	  $("#frm1").validate();
  });
  	// When the document is ready
  </script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
 <link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/bootstrap-datepicker.js"></script>
<script src="../js/fileupload.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-upload"></i> Upload Delivery Matrix</h2><div style="display:inline-block;float:left"><a href="excelexport.php?rname=<?=base64_encode("citymaster")?>&rheader=<?=base64_encode("City Master")?>&state=<?=base64_encode($_GET['locationstate'])?>&city_name=<?=base64_encode($_GET['cityname'])?>" title="Export locations details in excel"><i class="fa fa-file-excel-o fa-4x" title="Export locations details in excel"></i></a></div><div style="display:inline-block;float:right"><a href="../templates/PRODUCT_DELIVERY.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
        <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Product Category <span class="red_small">*</span></label>
              <div class="col-md-4">
              <select name="prod_cat" id="prod_cat" class="form-control custom-select">
                  <option value=""<?php if($selpc==''){ echo "selected";}?>>All</option>
                  <?php
                	$res_pro = mysqli_query($link1,"select catid,cat_name from product_cat_master order by cat_name"); 
                	while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                  <option value="<?=$row_pro['catid']?>"<?php if($row_pro['catid']==$_REQUEST["prod_cat"]){ echo 'selected'; }?>><?=$row_pro['cat_name']?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>       
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
              <div class="col-md-4">
                  <div class="input-group">
                    <label class="input-group-btn">
                        <span class="btn btn-primary">
                            Browse&hellip; <input type="file" name="attchfile" class="form-control required" required style="display:none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                        </span>
                    </label>
                    <input type="text" class="form-control" name="billfile"  id="billfile" readonly>
                </div>
              </div>
              <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
            </div>
          </div>
         <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
              &nbsp;&nbsp;&nbsp;
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='product_delivery.php?<?=$pagenav?>'">
            </div>
          </div> 
            <div class="row">
              <div class="col-sm-10" align="center">
              	<strong>Recomendation</strong>: From / To Location must be as present in system. For better understand, download excel which is given top left of this page.
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