<?php
require_once("../config/config.php");

////// final submit form ////
@extract($_POST);
if($_POST['Submit']=="Upload"){
mysqli_autocommit($link1, false);
$flag = true;
if ($_FILES["file"]["error"] > 0)
{
$code=$_FILES["file"]["error"];
}
else
{
move_uploaded_file($_FILES["file"]["tmp_name"],
"../ExcelExportAPI/salary_sheet/".$today.$_FILES["file"]["name"]);
$filen=$today.$_FILES["file"]["name"];
$file="../ExcelExportAPI/salary_sheet/".$today.$_FILES["file"]["name"];
chmod ($file, 0755);
}

$month = $_POST['month'];
$year = $_POST['year'];

$filename=$filen;
$filepath=$file;
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

 $indentityType = PHPExcel_IOFactory::identify($filepath);
                $object = PHPExcel_IOFactory::createReader($indentityType);
                $object->setReadDataOnly(true);
                $objPHPExcel = $object->load($filepath);
         
                $sheet = $objPHPExcel->getSheet(0); //we specify the sheet to use
                $highestRow = $sheet->getHighestRow();//we select all the rows used in the sheet 
                $highestCol = $sheet->getHighestColumn();// we select all the columns used in the sheet
                $indexCol = PHPExcel_Cell::columnIndexFromString($highestCol); //////// count no. of column 
				$highest = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow(); //////////////// count no of rows in excel
				
                //importing files to the database
                for($row =2; $row <= $highest; $row++)
                {
                    $userId = $sheet->getCellByColumnAndRow(1,$row)->getValue();
					$bankName = $sheet->getCellByColumnAndRow(2,$row)->getValue();
					$bankNo = $sheet->getCellByColumnAndRow(3,$row)->getValue();
					$basicPay = $sheet->getCellByColumnAndRow(4,$row)->getValue();
					$hra = $sheet->getCellByColumnAndRow(5,$row)->getValue();
					$spacialAllow = $sheet->getCellByColumnAndRow(6,$row)->getValue();
					$eduMed = $sheet->getCellByColumnAndRow(7,$row)->getValue();
					$conveAllown = $sheet->getCellByColumnAndRow(8,$row)->getValue();
					$mobileExp = $sheet->getCellByColumnAndRow(9,$row)->getValue();
					$localTravClaim = $sheet->getCellByColumnAndRow(10,$row)->getValue();
					$award = $sheet->getCellByColumnAndRow(11,$row)->getValue();
					$diwaliBonus = $sheet->getCellByColumnAndRow(12,$row)->getValue();
					$birthBonus = $sheet->getCellByColumnAndRow(13,$row)->getValue();
					$performenceBonus = $sheet->getCellByColumnAndRow(14,$row)->getValue();
					$advance = $sheet->getCellByColumnAndRow(15,$row)->getValue();
					$outStationTravelClaim = $sheet->getCellByColumnAndRow(16,$row)->getValue();
					$outStationLodge = $sheet->getCellByColumnAndRow(17,$row)->getValue();
					$priviousClaim = $sheet->getCellByColumnAndRow(18,$row)->getValue();
					$leaveRembers = $sheet->getCellByColumnAndRow(19,$row)->getValue();
					$incentive = $sheet->getCellByColumnAndRow(20,$row)->getValue();
					$loan = $sheet->getCellByColumnAndRow(21,$row)->getValue();
					$medRembers = $sheet->getCellByColumnAndRow(22,$row)->getValue();
					$travelAllownse = $sheet->getCellByColumnAndRow(23,$row)->getValue();
					$dearnessAllownse = $sheet->getCellByColumnAndRow(24,$row)->getValue();
					$specialPay = $sheet->getCellByColumnAndRow(25,$row)->getValue();
					$maintainAllownse = $sheet->getCellByColumnAndRow(26,$row)->getValue();
					$specialFurnAllownse = $sheet->getCellByColumnAndRow(27,$row)->getValue();
					$familyAllownse = $sheet->getCellByColumnAndRow(28,$row)->getValue();
					$mediExpPayble = $sheet->getCellByColumnAndRow(29,$row)->getValue();
					$intrestMediClaim = $sheet->getCellByColumnAndRow(30,$row)->getValue();
					$softLoan = $sheet->getCellByColumnAndRow(31,$row)->getValue();
					$gratuity = $sheet->getCellByColumnAndRow(32,$row)->getValue();
					$pf_12 = $sheet->getCellByColumnAndRow(33,$row)->getValue();
					$insurance = $sheet->getCellByColumnAndRow(34,$row)->getValue();
					$instalmentAdvanTaken = $sheet->getCellByColumnAndRow(35,$row)->getValue();
					$instalmentAgainstStaff = $sheet->getCellByColumnAndRow(36,$row)->getValue();
					$lateMarkDedection = $sheet->getCellByColumnAndRow(37,$row)->getValue();
					$mobileDedection = $sheet->getCellByColumnAndRow(38,$row)->getValue();
					$absentDedection = $sheet->getCellByColumnAndRow(39,$row)->getValue();
					$vehLoan = $sheet->getCellByColumnAndRow(40,$row)->getValue();
					$miscDedection = $sheet->getCellByColumnAndRow(41,$row)->getValue();
					$staffScheam = $sheet->getCellByColumnAndRow(42,$row)->getValue();
					$esi = $sheet->getCellByColumnAndRow(43,$row)->getValue();
					$esiArears = $sheet->getCellByColumnAndRow(44,$row)->getValue();
					$schoolFee = $sheet->getCellByColumnAndRow(45,$row)->getValue();
					$faRecovry = $sheet->getCellByColumnAndRow(46,$row)->getValue();
					$pfOnLeaveEncas = $sheet->getCellByColumnAndRow(47,$row)->getValue();
					$penaltyAny = $sheet->getCellByColumnAndRow(48,$row)->getValue();
					$staffLoan = $sheet->getCellByColumnAndRow(49,$row)->getValue();
					$loanInstallm = $sheet->getCellByColumnAndRow(50,$row)->getValue();
					$vpf = $sheet->getCellByColumnAndRow(51,$row)->getValue();
					$fwFund = $sheet->getCellByColumnAndRow(52,$row)->getValue();
					$other = $sheet->getCellByColumnAndRow(53,$row)->getValue();
					$grossEarning = $sheet->getCellByColumnAndRow(54,$row)->getValue();
					$grossDedection = $sheet->getCellByColumnAndRow(55,$row)->getValue();
					$netAmount = $sheet->getCellByColumnAndRow(56,$row)->getValue();
					$elThisMonth = $sheet->getCellByColumnAndRow(57,$row)->getValue();
					$clThisMonth = $sheet->getCellByColumnAndRow(58,$row)->getValue();
					$elTaken = $sheet->getCellByColumnAndRow(59,$row)->getValue();
					$clTaken = $sheet->getCellByColumnAndRow(60,$row)->getValue();
					$lateMarked = $sheet->getCellByColumnAndRow(61,$row)->getValue();
					$leaveBalence = $sheet->getCellByColumnAndRow(62,$row)->getValue();
					
					
                    //inserting query into data base
					
					$sql="INSERT INTO hrms_salary_upload  set update_date = '".$today."' , update_time = '".$currtime."' , update_by = '".$_SESSION['userid']."'  , salary_month='".$month."' , salary_year = '".$year."' , emp_id =  '".$userId."' , bank_name = '".$bankName."' , bank_ac_no = '".$bankNo."' , basic_pay = '".$basicPay."' , hra = '".$hra."' , spl_allow = '".$spacialAllow."' , edu_med_allow = '".$eduMed."' , conve_allown = '".$conveAllown."' , mobile_exp = '".$mobileExp."' , local_trav_claim = '".$localTravClaim."' , award = '".$award."' , diwali_bonus = '".$diwaliBonus."' , birth_bonus = '".$birthBonus."' , performence_bonus = '".$performenceBonus."' , advance = '".$advance."' , out_station_travel_claim = '".$outStationTravelClaim."' , out_station_lodge = '".$outStationLodge."' , privious_claim = '".$priviousClaim."' , leave_rembers = '".$leaveRembers."' , incentive = '".$incentive."' , loan = '".$loan."' , med_rembers = '".$medRembers."' , travel_allownse = '".$travelAllownse."' , dearness_allownse = '".$dearnessAllownse."' , special_pay = '".$specialPay."' , maintain_allownse = '".$maintainAllownse."' , special_furn_allownse = '".$specialFurnAllownse."' , family_allownse = '".$familyAllownse."' , medi_exp_payble = '".$mediExpPayble."' , intrest_medi_claim = '".$intrestMediClaim."' , soft_loan_amt = '".$softLoan."' ,	gratuity = '".$gratuity."' , pf_12 = '".$pf_12."' , insurance = '".$insurance."' , instalment_advan_taken = '".$instalmentAdvanTaken."' , instalment_against_staff = '".$instalmentAgainstStaff."' , late_mark_dedection = '".$lateMarkDedection."' , mobile_dedection = '".$mobileDedection."' , absent_dedection = '".$absentDedection."' , veh_loan = '".$vehLoan."' , misc_dedection = '".$miscDedection."' , staff_scheam = '".$staffScheam."' , esi = '".$esi."' , esi_arears = '".$esiArears."' , school_fee = '".$schoolFee."' , fa_recovry = '".$faRecovry."' , pf_on_leave_encas = '".$pfOnLeaveEncas."' , penalty_any = '".$penaltyAny."' , staff_loan = '".$staffLoan."' ,  loan_installm = '".$loanInstallm."' , vpf = '".$vpf."' , fw_fund = '".$fwFund."' , other = '".$other."' , gross_earning = '".$grossEarning."' , gross_dedection = '".$grossDedection."' , net_amount = '".$netAmount."' , el_this_month = '".$elThisMonth."' , cl_this_month = '".$clThisMonth."' , el_taken = '".$elTaken."' , cl_taken  = '".$clTaken."' , late_marked = '".$lateMarked."' , leave_balence = '".$leaveBalence."' ,  file_name = '".$filename."' , file_path = '".$filepath."'";
								
				    $result =	mysqli_query($link1,$sql);
					//// check if query is not executed
					if (!$result) {
					   $flag = false;
					   echo "Error details : " . mysqli_error($link1) . ".";
					}		   	   
			   }////// end of for loop
	   
	   if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Successfully Uploaded ";
		///// move to parent page
		header("location:admin_salary_sheet_view.php?msg=".$msg."&sts=success"."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:admin_salary_sheet_view.php?msg=".$msg."&sts=fail"."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;
	} 
    mysqli_close($link1);            
}///// end of if condition

?>
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 
 <script>
	
 </script>
 
 
 <link rel="stylesheet" href="../css/datepicker.css">
 <script src="../js/bootstrap-datepicker.js"></script>
<style>
.red_small{
	color:red;
}
.warning,.warning2 {
    color:#d2232a;
    -webkit-border-radius: 12px; 
    border-radius: 12px;
    background-color:#ffdd97;
    padding:5px;
    width:100%;
    display:none;
}
</style>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
   	<div class="form-group">
        <div class="col-md-12">
            <h2 align="center"><i class="fa fa-inr"></i>&nbsp;Pay Slip Details Upload</h2>	
            <div style="display:inline-block;float:right;margin-right:50px;"><a href="../templates/SALARY_SHEET_UPLOAD.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"></a></div>
        </div>
    </div>
    <br></br> 
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      <?php if($_REQUEST['msg']){?><br>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
        <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
        
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Month <span class="red_small">*</span></label>
              <div class="col-md-4">
                <select name="month"  id= "month" class="form-control required"  onChange="document.frm1.submit();" required >
					<option value="" <?php if($_REQUEST['month'] == "") { echo "selected" ;} ?> > -- Please Select -- </option>
                    <option value="01" <?php if($_REQUEST['month']=='01')echo "selected";?>>JAN</option>
                    <option value="02" <?php if($_REQUEST['month']=='02')echo "selected";?>>FEB</option>
                    <option value="03" <?php if($_REQUEST['month']=='03')echo "selected";?>>MAR</option>
                    <option value="04" <?php if($_REQUEST['month']=='04')echo "selected";?>>APR</option>
                    <option value="05" <?php if($_REQUEST['month']=='05')echo "selected";?>>MAY</option>
                    <option value="06" <?php if($_REQUEST['month']=='06')echo "selected";?>>JUN</option>
                    <option value="07" <?php if($_REQUEST['month']=='07')echo "selected";?>>JUL</option>
                    <option value="08" <?php if($_REQUEST['month']=='08')echo "selected";?>>AUG</option>
                    <option value="09" <?php if($_REQUEST['month']=='09')echo "selected";?>>SEP</option>
                    <option value="10" <?php if($_REQUEST['month']=='10')echo "selected";?>>OCT</option>
                    <option value="11" <?php if($_REQUEST['month']=='11')echo "selected";?>>NOV</option>
                    <option value="12" <?php if($_REQUEST['month']=='12')echo "selected";?>>DEC</option>	 
                </select>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Year <span class="red_small">*</span></label>
              <div class="col-md-4">
              	<select name="year"  id= "year" class="form-control required" required>
				  <option value="" <?php if($_REQUEST['year']==""){ echo "selected";}?> > -- Please Select -- </option>
	          	  <?php
					  $currrent_year=date('Y');
					  $last_year=$currrent_year-1;
					  $sec_last_year=$currrent_year-2;
		 		  ?>
                  <option value="<?=$sec_last_year?>" <?php if($_REQUEST['year']==$sec_last_year)echo "selected";?>><?=$sec_last_year?></option>
          		  <option value="<?=$last_year?>" <?php if($_REQUEST['year']==$last_year)echo "selected";?>><?=$last_year?></option>
          		  <option value="<?=$currrent_year?>" <?php if($_REQUEST['year']==$currrent_year)echo "selected";?>><?=$currrent_year?></option>
            	</select>
              </div>
            </div>
          </div>
          		        
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Attach File <span class="red_small">*</span></label>
              <div class="col-md-4">
                  <div>
                       <span>
                        <input type="file"  name="file"  required class="form-control"   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/ > 
                    </span>       
                </div>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-12" style="text-align:center;">
             	<span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span>
            </div>
          </div>
          <br><br>
         <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
              &nbsp;&nbsp;
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='admin_salary_sheet_view.php?<?=$pagenav?>'">
            </div>
          </div> 
          <br><br>
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