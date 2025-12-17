<?php
require_once("../config/config.php");
@extract($_POST);
if($_POST) {
	if($_POST['Submit'] == "Upload") {
		////// start transaction
    	mysqli_autocommit($link1, false);
    	$flag = true;
    	$err_msg = "";
      	if($_FILES["attchfile"]["error"] > 0) {
        	$code = $_FILES["attchfile"]["error"];
      	}else{
        	// Rename file
			$expld_id = explode(".",$_FILES["attchfile"]["name"]);
        	$file_ext = substr($_FILES["attchfile"]["name"], strripos($_FILES["attchfile"]["name"], '.')); // get file name
        	$newfilename = $expld_id[0] . "_" . $todayt . $now . $file_ext;
        	move_uploaded_file($_FILES["attchfile"]["tmp_name"], "../upload_partcode/" . $newfilename);
        	$filename = "../upload_partcode/" . $newfilename;
        	chmod($filename, 0755);
      	}
      	////////////////////////////////////////////////// code to import file/////////////////////////////////////////////////////////////
      	error_reporting(E_ALL ^ E_NOTICE);
      	$path = '../ExcelExportAPI/Classes/';
      	set_include_path(get_include_path() . PATH_SEPARATOR . $path); 
      	//we specify the path" using linux"
      	function __autoload($classe)
      	{
        	$var = str_replace('_',DIRECTORY_SEPARATOR,$classe).'.php';
        	require_once($var);
      	}
      	$indentityType = PHPExcel_IOFactory::identify($filename);
      	$object = PHPExcel_IOFactory::createReader($indentityType);
      	$object->setReadDataOnly(true);
      	$objPHPExcel = $object->load($filename);
      	$sheet = $objPHPExcel->getSheet(0); //we specify the sheet to use
      	$highestRow = $sheet->getHighestRow(); //we select all the rows used in the sheet 
     	$highestCol = $sheet->getHighestColumn(); // we select all the columns used in the sheet
      	$indexCol = PHPExcel_Cell::columnIndexFromString($highestCol); //////// count no. of column 
      	$highest = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow(); //////////////// count no of rows in excel
		////// retrive data from excel
      	for($row = 2; $row <= $highest; $row++) {
        	$productcategory = $sheet->getCellByColumnAndRow(1, $row)->getValue();
        	$productsubcategory = $sheet->getCellByColumnAndRow(2, $row)->getValue();
			$hsncode = $sheet->getCellByColumnAndRow(3, $row)->getValue();
			echo  $productname = $sheet->getCellByColumnAndRow(4, $row)->getValue();
			echo "<br/>";
			$brand = $sheet->getCellByColumnAndRow(5, $row)->getValue();
			$modelname = $sheet->getCellByColumnAndRow(6, $row)->getValue();
			$productcolor = $sheet->getCellByColumnAndRow(7, $row)->getValue();
			$productdesc = $sheet->getCellByColumnAndRow(8, $row)->getValue();
			$producttype = $sheet->getCellByColumnAndRow(9, $row)->getValue();
			$division = $sheet->getCellByColumnAndRow(10, $row)->getValue();
			$grossweight = $sheet->getCellByColumnAndRow(11, $row)->getValue();
			$netweight = $sheet->getCellByColumnAndRow(12, $row)->getValue();
			$scrapweight = $sheet->getCellByColumnAndRow(13, $row)->getValue();
			$prorata = $sheet->getCellByColumnAndRow(14, $row)->getValue();
			$batteryrating = $sheet->getCellByColumnAndRow(15, $row)->getValue();
			$wsdays = $sheet->getCellByColumnAndRow(16, $row)->getValue();
			$storageperiod = $sheet->getCellByColumnAndRow(17, $row)->getValue();
			$wsterm = $sheet->getCellByColumnAndRow(18, $row)->getValue();
			$is_serialized = $sheet->getCellByColumnAndRow(19, $row)->getValue();
			$prodcode = $sheet->getCellByColumnAndRow(20, $row)->getValue();
			$modelcode = $sheet->getCellByColumnAndRow(21, $row)->getValue();
			$othspec1 = $sheet->getCellByColumnAndRow(22, $row)->getValue();
			$othspec2 = $sheet->getCellByColumnAndRow(23, $row)->getValue();
			
		}
		if($flag){
			mysqli_commit($link1);
			$cflag = "success";
			$cmsg = "Success";
			$msg = "Successfully Uploaded ".$highest;
		}else{
			mysqli_rollback($link1);
			$cflag = "danger";
			$cmsg = "Failed";
			$msg = "Please try again ! " . $err_msg;
		}
		mysqli_close($link1);
		///// move to parent page
		//header("location:model_master.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
		//exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=siteTitle?></title>
<link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
<script src="../js/jquery.js"></script>
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
</script>
<script src="../js/jquery.validate.js"></script>
<script src="../js/fileupload.js"></script>
</head>
<body>
	<div class="container-fluid">
    	<div class="row content">
			<?php 
            include("../includes/leftnav2.php");
            ?>
            <div class="<?=$screenwidth?>">
       			<h2 align="center"><i class="fa fa-upload"></i> Upload PartCode</h2>
                <div style="display:inline-block;float:left"><a href="../excelReports/prodsubcatmasteridwise.php?status=<?=base64_encode($selstatus)?>&prod_cat=<?=base64_encode($selpc)?>" title="Export product category/sub category id details in excel"><i class="fa fa-file-excel-o fa-2x faicon excelicon" title="Export product category/sub category id details in excel"></i></a>Download Product category/sub category id list<br/><br/><br/>
                <a href="excelexport.php?rname=<?=base64_encode("brandidreport")?>&rheader=<?=base64_encode("Brand Master")?>&status=<?=base64_encode($_GET['status'])?>" title="Export Brand id details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Brand id details in excel"></i></a>Download Brand id list
                </div>
                
                <div style="display:inline-block;float:right">
                	<a href="../templates/partcodeuploadtemplate.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a>
                </div>
	  			<form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
          		<div class="form-group">
            		<div class="col-md-12"><label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
              			<div class="col-md-4">
              				<div class="input-group"><label class="input-group-btn"><span class="btn<?=$btncolor?>">
                            Browse&hellip; <input type="file" name="attchfile" id="attchfile" class="form-control required" required style="display:none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"></span></label>
                    			<input type="text" class="form-control required" required name="billfile"  id="billfile" readonly>
              				</div>
            			</div>
          			</div>
         			<div class="form-group">
            			<div class="col-md-12"><label class="col-md-4 control-label"></label>
              				<div class="col-md-4">
              					<span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file<br/>Red color columns are mandatory to fill</span>
              				</div>
            			</div>
          			</div> 
                    <div class="form-group">
            			<div class="col-md-12" align="left">
             				<span class="red_small">&nbsp;&nbsp;ALERT: Do not delete any column of excel sheet . Fill only appropriate columns. And before proceeding file check once all data is correct or not.</span>
              			</div>
        			</div> 
         			<div class="form-group">
            			<div class="col-md-12" align="center">
             				<button class='btn<?=$btncolor?>' id="save" type="submit" name="Submit" value="Upload" <?php if(isset($_POST['Submit'])){ if($_POST['Submit']=='Upload'){?>disabled<?php }}?>><i class="fa fa-upload fa-lg"></i>&nbsp;&nbsp;Upload</button>&nbsp;&nbsp;
              				<button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='model_master.php?<?=$pagenav?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
              			</div>
        			</div> 
    			</form>
      		</div>
      		<!--End col-sm-9--> 
    	</div>
    	<!--End row content--> 
  	</div>
  	<!--End container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
