<?php
require_once("../config/config.php");
//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="Upload"){
	//ini_set('max_execution_time', 500);
	//ini_set('memory_limit', '256M');
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg = "";
	///check directory
	$dirct = "../upload_reward/".date("Y-m");
	if (!is_dir($dirct)) {
		mkdir($dirct, 0777, 'R');
	}
	/////// define array for update margin after margin % change
	$arr_psc = array();
	/////////
	if ($_FILES["file"]["error"] > 0)
	{
		$code=$_FILES["file"]["error"];
	}
	else
	{
		move_uploaded_file($_FILES["file"]["tmp_name"],$dirct."/".$now.$_FILES["file"]["name"]);
		$file = $dirct."/".$now.$_FILES["file"]["name"];
		$file2 = $now;
		//chmod ($file, 0755);
	}
	$filename=$file;
	////////////////////////////////////////////////// code to import file/////////////////////////////////////////////////////////////
	error_reporting(E_ALL ^ E_NOTICE);
 	$path = '../ExcelExportAPI/Classes/';
    set_include_path(get_include_path() . PATH_SEPARATOR . $path);//we specify the path" using linux"
        function __autoload($classe)
        {
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
   /*echo '<script>alert("le fichier a t charg avec succes !");</script>';*/
	$sheet = $objPHPExcel->getSheet(0); //we specify the sheet to use
	$highestRow = $sheet->getHighestRow();//we select all the rows used in the sheet 
	$highestCol = $sheet->getHighestColumn();// we select all the columns used in the sheet
	$indexCol = PHPExcel_Cell::columnIndexFromString($highestCol); //////// count no. of column 
	$highest = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow(); //////////////// count no of rows in excell
	//Checking data
	$loctype = explode("~",$locationtype);
	$arr_err = array();
	//// insert data in data table /////////////////////////////////////////////////////////////	
	for($row=2;$row <=$highest;$row++){		
		////// excel file columm value
		$sno = trim($sheet->getCellByColumnAndRow(0,$row)->getValue());
		$state = trim($sheet->getCellByColumnAndRow(1,$row)->getValue());
		$partcode = trim($sheet->getCellByColumnAndRow(2,$row)->getValue());
		$reward_point = trim($sheet->getCellByColumnAndRow(3,$row)->getValue());
		$parent_reward = trim($sheet->getCellByColumnAndRow(4,$row)->getValue());
		$action = "";
		///////// check all details should be filled
		if($state !='' && $partcode !='' && $reward_point != ''){
			######## first check state  ##############################3
			$check_state= mysqli_query($link1 , "SELECT sno FROM state_master WHERE state = '".$state."'");
			if(mysqli_num_rows($check_state) >0) {
				######## second check partcode  ##############################3
			    $check_partcode= mysqli_query($link1 , "SELECT id FROM product_master WHERE productcode = '".$partcode."'");
			   	if(mysqli_num_rows($check_partcode) >0) {
					###########  check for duplicate entry #################################################
					$check_dup = mysqli_query($link1 , "SELECT id,reward_point FROM reward_points_master WHERE state = '".$state."' AND id_type = '".$loctype[0]."' AND partcode = '".$partcode."' AND status = 'A'");								 
					if(mysqli_num_rows($check_dup) == 0) {	
			  			$result_res	 = mysqli_query($link1,"INSERT INTO reward_points_master SET state = '".$state."', id_type = '".$loctype[0]."', partcode = '".$partcode."', reward_point ='".$reward_point."',parent_party_reward='".$parent_reward."', status='A', create_by='".$_SESSION['userid']."', create_on='".$datetime."'");
						##########  check if query is not executed
						if (!$result_res) {
							$flag = false;
							$arr_err[] = $sno." Error1 From DB";
						} 
						$id = mysqli_insert_id($link1);
						$action = "ADD";
						$hist_price = $reward_point;
					}  
					########## duplicate condition ends ####################################3
					else {
						$get_id = mysqli_fetch_assoc($check_dup);
						$result_res2 = mysqli_query($link1,"UPDATE reward_points_master SET reward_point ='".$reward_point."',parent_party_reward='".$parent_reward."', update_by='".$_SESSION['userid']."', update_on='".$datetime."' WHERE state = '".$state."' AND  id_type = '".$loctype[0]."' AND partcode = '".$partcode."' AND status = 'A'");
						##########  check if query is not executed
						if (!$result_res2) {
							$flag = false;
							$arr_err[] = $sno." Error2 From DB";
						}
						$id = $get_id['id'];
						$action = "UPDATE";
						$hist_price = $get_id["reward_point"];
					}
					////// insert into price histroy 
					if(($id)>0)
					{
						//return message
					}
					else{
						////// return message
						$flag = false;
						$arr_err[] = $sno." Error3 From DB";
			   		}								  
				} ########## brnad condition ends ####################################3
				else {
					$flag = false;
					$arr_err[] = $sno." System Partcode is not matched in DB"; 			        
				}
			} ########## product category ends #####################################################
			else {				         
				$flag = false;
				$arr_err[] = $sno." State is not matched in DB";            
			}
		} ############ if check condition ends /////////////////////////////////
		else {	
			$flag = false;
			$arr_err[] = $sno." Mandatory data is blank on this row";
		}
	} ############### end of for loop 	
	$flag=dailyActivity($_SESSION['userid'],$file2,"REWARD POINT","UPLOAD",$ip,$link1,$flag);	
	if ($flag) {	
		mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
		$msg = "Reward point is successfully Uploaded";
	}
	else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.";
		$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_err];
	}
	mysqli_close($link1);
	header("location:reward_points_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;								
} ############### end of if condition
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=siteTitle?></title>
<script src="../js/jquery.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script src="../js/fileupload.js"></script>
<script language="javascript" type="text/javascript">
	$(document).ready(function(){
	var spinner = $('#loader');
    $("#frm1").validate({
		submitHandler: function (form) {
			if (!this.wasSent) {
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
});
</script>
<link href="../css/loader.css" rel="stylesheet"/>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
		<div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      		<h2 align="center"><i class="fa fa-upload"></i>&nbsp;Upload Reward Points</h2>
            <div style="display:inline-block;float:left"><a href="excelexport.php?rname=<?=base64_encode("state_master_excel")?>&rheader=<?=base64_encode("State Master")?>" title="Export States details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export States details in excel"></i></a>Download State Master list
            </div>
            <div style="display:inline-block;float:right"><a href="../templates/UPLOAD_REWARD_POINTS.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div><br></br>
      		<div class="form-group"  id="page-wrap" style="margin-left:10px;">
      		<?php if($_REQUEST['msg']){?><br>
      			<div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            		<strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>
                </div>
      		<?php }?>
        	<form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
		  		<div class="form-group">
            		<div class="col-md-12"><label class="col-md-4 control-label">Location Type<span class="red_small">*</span></label>
              			<div class="col-md-4">
                  			<select  name="locationtype" id="locationtype" class="form-control required" required >
								 <option value="">--Please Select-</option>
								 <?php
								$type_query="SELECT locationname,locationtype,seq_id FROM location_type where status='A' and locationtype NOT IN ('HO','SS') ORDER BY seq_id";
								$check_type=mysqli_query($link1,$type_query);
								while($br_type = mysqli_fetch_array($check_type)){
								?>
								<option value="<?=$br_type['locationtype']."~".$br_type['seq_id']?>"<?php if($_REQUEST['locationtype']==$br_type['locationtype']."~".$br_type['seq_id']){ echo "selected";}?>><?php echo $br_type['locationname']?></option>
								<?php }?>
							</select>
              			</div>
            		</div>
          		</div>
                <div class="form-group">
            		<div class="col-md-12"><label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
              			<div class="col-md-4">
                  			<div class="input-group">
                            	<label class="input-group-btn">
                        		<span class="btn btn-primary">Browse&hellip; <input type="file" name="file" class="form-control"  style="display:none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></span>
                                </label>
                    			<input type="text" class="form-control required" name="file"  id="file"  required readonly>
                			</div>
              			</div>
              			<div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
            		</div>
          		</div>
			 	<div class="form-group">
					<div class="col-md-12" align="center">&nbsp;&nbsp;&nbsp;
				  		<input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
				  		<input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='reward_points_master.php?<?=$pagenav?>'">
					</div>
			  	</div> 
		   	</form>
		 	</div>
		</div>
	</div>
</div>
<div id="loader"></div>
 <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
 ?>
</body>
</html>