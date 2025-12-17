<?php
////// Function ID ///////
$fun_id = array("a"=>array(53));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
/////////check approval is applicable or not
//$apply_app = getAnyDetails(base64_decode($_REQUEST['pid']),"apply_approval","tabid","tab_master",$link1);
//////////////// after hitting upload button
@extract($_POST);
if($_POST){
if($_POST['Submit']=="Upload"){
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg = "";
	$expld_bommodel = explode("~",$bom_model);
	if(mysqli_num_rows(mysqli_query($link1,"select id from combo_master where bom_modelcode='".$expld_bommodel[0]."' and status='1'"))==0){
	if($apply_app=="Y"){ $current_status = 3;}else{ $current_status = 1;}
	if ($_FILES["attchfile"]["error"] > 0){
		$code = $_FILES["attchfile"]["error"];
	}
	else{
		// Rename file
		$file_ext = substr($_FILES["attchfile"]["name"], strripos($_FILES["attchfile"]["name"], '.')); // get file name
		$newfilename = $expld_bommodel[0]."_".$todayt.$now.$file_ext;
		move_uploaded_file($_FILES["attchfile"]["tmp_name"],"../upload_combo/".$newfilename);
		$filename="../upload_combo/".$newfilename;
		chmod ($filename, 0755);
	}
	////////////////////////////////////////////////// code to import file/////////////////////////////////////////////////////////////
	error_reporting(E_ALL ^ E_NOTICE);
 	$path = '../ExcelExportAPI/Classes/';
    set_include_path(get_include_path() . PATH_SEPARATOR . $path);//we specify the path" using linux"
    function __autoload($classe){
		$var = str_replace
            (
                '_', 
                DIRECTORY_SEPARATOR,
                $classe
            ) . '.php' ;
            require_once($var);
	}
	$indentityType = PHPExcel_IOFactory::identify($filename);
    $object = PHPExcel_IOFactory::createReader($indentityType);
	$object->setReadDataOnly(true);
	$objPHPExcel = $object->load($filename);
	$sheet = $objPHPExcel->getSheet(0); //we specify the sheet to use
	$highestRow = $sheet->getHighestRow();//we select all the rows used in the sheet 
	$highestCol = $sheet->getHighestColumn();// we select all the columns used in the sheet
	$indexCol = PHPExcel_Cell::columnIndexFromString($highestCol); //////// count no. of column 
	$highest = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow(); //////////////// count no of rows in excel
	$one_record = 0;
	//importing files to the database
	///// pick max bom id
	$max_bomid = mysqli_fetch_array(mysqli_query($link1,"SELECT MAX(bomid) from combo_master"));
	$next_bomid = $max_bomid[0]+1;
	for($row =2 ;$row <= $highest;$row++){
		$bompartcode = $sheet->getCellByColumnAndRow(1,$row)->getValue();
		$bomqty = $sheet->getCellByColumnAndRow(2,$row)->getValue();
		$convf = $sheet->getCellByColumnAndRow(3,$row)->getValue();
		//// converson factor ////
		if($convf=="" || $convf==0|| $convf==0.000000){ $cf = 1; }else{ $cf = $convf; }
		if($bompartcode!='' && $bomqty!='' && $bomqty!=0){
       		/////////////////////// check whether partcode exist in partcode master or not //////////////////////
			$checkpart = mysqli_query($link1,"SELECT id FROM product_master where productcode = '".$bompartcode."' and status='Active'");
			if(mysqli_num_rows($checkpart) > 0){
				//// fetch some part details
				//$partdet = mysqli_fetch_assoc($checkpart);
				////////////// insert in bom master
				$res_bom = mysqli_query($link1 , "INSERT INTO combo_master set bomid='".$next_bomid."',bom_modelcode ='".$expld_bommodel[0]."',bom_modelname='".$bom_model_name."',bom_hsn='".$bom_model_hsn."',bom_partcode='".$bompartcode."',bom_qty='".$bomqty."',bom_unit='NOS',purchase_unit='NOS',conversion_factor='".$cf."', status='".$current_status."',createdate='".$today."',createby='".$_SESSION['userid']."'");
				//// check if query is not executed
				if (!$res_bom) {
					$flag = false;
					$error_msg = "Error 1: " . mysqli_error($link1) . ".";
				}
				$one_record++;
			}
			else {
				$flag = false;
				$error_msg = "Error 2: Partcode not found in DB.";
			}
			
		}
	}////// end of for loop 
	///// insert in upload table
	$res_upload = mysqli_query($link1,"INSERT INTO upload_file set ref_no='".$expld_bommodel[0]."',ref_type='Combo UPLOAD',file_name='".$filename."',updatedate='".$datetime."',updateby='".$_SESSION['userid']."'");
	//// check if query is not executed
	if (!$res_upload) {
		$flag = false;
		$error_msg = "Error 3: " . mysqli_error($link1) . ".";
	}
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$expld_bommodel[0]."-".$next_bomid,"Combo","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	if ($flag) {
		if($one_record > 0){
			mysqli_commit($link1);
			$cflag = "success";
			$cmsg = "Success";
			$msg = "File is successfully Uploaded.";
		}else{
			$cflag = "warning";
			$cmsg = "Warning";
			$msg = "You are uploading blank file.";
		}
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.".$error_msg;
	} 
    mysqli_close($link1);
	}else{
		$msg = "Combo is already active for Model ".$bom_model_name;
		$cflag="warning";
		$cmsg = "Warning";
	}
	///// move to parent page
	header("location:combo_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  	exit;        
}///// end of if condition
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
  	<script>
  $(document).ready(function(){
	var spinner = $('#loader');
        $("#frm1").validate({
		  submitHandler: function (form) {
			if(!this.wasSent){
				this.wasSent = true;
				$(':submit', form).val('Please wait...')
								  .attr('disabled', 'disabled')
								  .addClass('disabled');
				spinner.show();				  
				form.submit();
			} else {
				return false;
			}
          }
		});
		$('.selectpicker').selectpicker({
		  liveSearch: true
		});
	});
	///// check BOM Model is already active or not
	$(document).ready(function() {
		$("#bom_model").keyup(function(){
			var bm=$('#bom_model').val();
			$.ajax({
			  type:"post",
			  url:"../includes/getAzaxFields.php",
			  data:{bommodel:bm},
			  success:function(data){
				  if(parseInt(data) > 0){
					  $("#error_msg").html("Combo is already in active status for this model.");
					  $("#save").attr("disabled","disabled");
				  }else{
					  $("#error_msg").html("");
					  $("#save").removeAttr("disabled");
				  }
			  }
			});
		});
	});
	</script>
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
       <h2 align="center"><i class="fa fa-upload"></i> Upload Combo</h2><div style="display:inline-block;float:right"><a href="../templates/combomaster.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div>
        <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
	  		<form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Combo Model Name <span class="red_small">*</span></label>
              <div class="col-md-4">
              	<input name="bom_model_name" id="bom_model_name" class="form-control required"/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Combo Model <span class="red_small">*</span></label>
              <div class="col-md-4">
              	<input name="bom_model" id="bom_model" class="form-control required alphanumeric"/><span id="error_msg" class="red_small"><?=$msg?></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Combo Model HSN<span class="red_small">*</span></label>
              <div class="col-md-4">
              	<input name="bom_model_hsn" id="bom_model_hsn" class="form-control required digits" maxlength="10"/>
              </div>
            </div>
          </div>
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
            <div class="col-md-12"><label class="col-md-4 control-label">Recommendation</label>
              <div class="col-md-8">
              		<span class="red_small">We have given the data format in attached template. Please follow the same.<br/>PART CODE must be entered as system generated.<br/>There should not be any special character in excel columns.<br/><!--You can fill Combo QTY and CONVERSION FACTOR up to 6 decimal places.--></span>
              </div>
            </div>
          </div> 
         <div class="form-group">
            <div class="col-md-12" align="center">
              <button class='btn<?=$btncolor?>' id="save" type="submit" name="Submit" value="Upload" <?php if(isset($_POST['Submit'])){ if($_POST['Submit']=='Update'){?>disabled<?php }}?>><i class="fa fa-upload fa-lg"></i>&nbsp;&nbsp;Upload</button>
              &nbsp;&nbsp;
              <button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='combo_master.php?<?=$pagenav?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
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
