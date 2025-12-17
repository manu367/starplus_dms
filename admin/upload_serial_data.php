<?php
require_once("../config/config.php");
@extract($_POST);
if($_POST) {
	if($_POST['Submit'] == "Upload") {
		//// start transaction
    	mysqli_autocommit($link1, false);
		$flag = true;
		$error_msg = "";
      	if($_FILES["attchfile"]["error"] > 0) {
        	$code = $_FILES["attchfile"]["error"];
      	}else {
        	// Rename file
        	$file_ext = substr($_FILES["attchfile"]["name"], strripos($_FILES["attchfile"]["name"], '.')); // get file name
        	// print_r($file_ext);
        	// exit;
        	$newfilename = $expld_id[0] . "_" . $todayt . $now . $file_ext;
        	move_uploaded_file($_FILES["attchfile"]["tmp_name"], "../upload_serialdata/" . $newfilename);
        	$filename = "../upload_serialdata/" . $newfilename;
        	// print_r($filename);
        	// 		exit;
        	chmod($filename, 0755);
      	}
      	////////////////////////////////////////////////// code to import file/////////////////////////////////////////////////////////////
      	error_reporting(E_ALL ^ E_NOTICE);
      	$path = '../ExcelExportAPI/Classes/';
      	set_include_path(get_include_path() . PATH_SEPARATOR . $path); 
      	//we specify the path" using linux"
      	function __autoload($classe)
      	{
        	$var = str_replace
        	(
          '_',
        DIRECTORY_SEPARATOR,
          $classe
        ) . '.php';
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
		////// fetch excel data
    	for ($row = 2; $row <= $highest; $row++) {
			
			$serialno = $sheet->getCellByColumnAndRow(1, $row)->getValue();
			$productcode = $sheet->getCellByColumnAndRow(2, $row)->getValue();
			$modelcode = $sheet->getCellByColumnAndRow(3, $row)->getValue();
			$productname = $sheet->getCellByColumnAndRow(4, $row)->getValue();
			$dealercode = $sheet->getCellByColumnAndRow(5, $row)->getValue();
			$dealername = $sheet->getCellByColumnAndRow(6, $row)->getValue();
			if($serialno){
			//// check serial no. is exist or not
 	     	if(mysqli_num_rows(mysqli_query($link1, "SELECT id from serial_upload_data where serial_no='" . $serialno . "'")) > 0) {
        		$res1 = mysqli_query($link1, "update serial_upload_data set product_code	='" . $productcode . "',model_code='" . $modelcode . "',product_name	='" . $productname . "',dealer_code	='" . $dealercode . "',dealer_name	='" . $dealername . "', update_by = '" . $_SESSION['userid'] . "',update_date='" . $datetime . "',update_ip='" . $ip . "' where serial_no='" . $serialno . "'");
				///// check if query is execut or not
				if(!$res1){
					$flag = false;
					$error_msg = "ER1 ".mysqli_error($link1);
				}
      		}else{
        		$res2 = mysqli_query($link1, "INSERT INTO serial_upload_data set serial_no='" . $serialno . "',product_code	='" . $productcode . "',model_code='" . $modelcode . "',product_name	='" . $productname . "',dealer_code	='" . $dealercode . "',dealer_name	='" . $dealername . "', create_by='" . $_SESSION['userid'] . "',create_date='" . $datetime . "',create_ip='" . $ip . "'");
				///// check if query is execut or not
				if(!$res2){
					$flag = false;
					$error_msg = "ER2 ".mysqli_error($link1);
				}
      		}
			}
    	}//// close for loop
		///// check all queries are executed
        if($flag){
          mysqli_commit($link1);
          $cflag = "success";
          $cmsg = "Success";
          $msg = "Successfully Uploaded ";
      } else {
          mysqli_rollback($link1);
          $cflag = "danger";
          $cmsg = "Failed";
          $msg = "Please try again ! " . $error_msg;
      }
      mysqli_close($link1);
      ///// move to parent page
      header("location:uploaded_serial_data.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
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
  <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
  <script src="../js/jquery.js"></script>
  <script src="../js/jquery.min.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/bootstrap.min.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-select.min.css">
    <script src="../js/bootstrap-select.min.js"></script>
    <script src="../js/jquery.validate.js"></script>
	<link href="../css/loader.css" rel="stylesheet"/>
    <script src="../js/fileupload.js"></script>
  </head>
  <body>
  <div class="container-fluid">
    <div class="row content">
    <?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="<?=$screenwidth?>">
       <h2 align="center"><i class="fa fa-upload"></i> Upload Serial Data</h2><div style="display:inline-block;float:right"><a href="../templates/serial_upload_data.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div>
        <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
        <?php if($_REQUEST['msg']){?>
            <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
            </div>
            <?php 
                unset($_POST);
             }?>
	  		<form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
              <div class="col-md-4">
              	<div class="input-group">
                    <label class="input-group-btn">
                        <span class="btn<?=$btncolor?>">
                            Browse&hellip; <input type="file" name="attchfile" id="attchfile" class="form-control required" required style="display:none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                        </span>
                    </label>
                    <input type="text" class="form-control" name="billfile"  id="billfile" readonly>
              </div>
            </div>
          </div>
         <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label"></label>
              <div class="col-md-4">
              		<span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file<br/> Do not delete any column of excel sheet . Fill only appropriate columns.</span>
              </div>
            </div>
          </div> 
        
         <div class="form-group">
            <div class="col-md-12" align="center">
              <button class='btn<?=$btncolor?>' id="save" type="submit" name="Submit" value="Upload" <?php if(isset($_POST['Submit'])){ if($_POST['Submit']=='Update'){?>disabled<?php }}?>><i class="fa fa-upload fa-lg"></i>&nbsp;&nbsp;Upload</button>&nbsp;&nbsp;
              <button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='uploaded_serial_data.php?<?=$pagenav?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
              
            </div>
        </div> 
    </form>
        </div>
        <!--End form group--> 
      </div>
      <!--End col-sm-9--> 
    </div>
    <!--End row content--> 
  </div>
  <!--End container fluid-->
  <div id="loader"></div>
  <?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
