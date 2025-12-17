<?php
require_once("../config/config.php");
$empid=base64_decode($_REQUEST['emp_id']);
$month=base64_decode($_REQUEST['emp_month']);
$year=base64_decode($_REQUEST['emp_year']);
######333  emp details ################################33
$po_sql="SELECT * FROM hrms_employe_master where loginid='".$empid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
###########  fetch gross earning details ////////////////////////////////
$gross_det = mysqli_fetch_array(mysqli_query($link1,"select  gross_earnings , gross_deduction ,  final_grossamt from hrms_salary_details where  empid = '".$empid."'  "));
$mangername = mysqli_fetch_array(mysqli_query($link1 , "select empname from hrms_employe_master where loginid = '".$po_row['managerid']."' "));
#########3 salary details ##################################################################
$info_salary = mysqli_fetch_assoc(mysqli_query($link1, " SELECT * FROM hrms_salary_details WHERE  empid = '".$empid."' "));

#########leave summary details //
$leave_det = mysqli_fetch_array(mysqli_query($link1,"select * from leave_summary where  emp_code = '".$empid."'  and month = '".$month."' and year= '".$year."'  "));
###########33 salary logic ////////////////////
$totalleave= 0;
$deductionval=0;
$singledaysalaryval= 0;
$singledaysalaryval = intval($gross_det['final_grossamt'])/30 ;

$totalleave = $leave_det['cl']+$leave_det['pl']+$leave_det['sl']+$leave_det['ml']+$leave_det['ss']+$leave_det['absent'];
$deductionval = $singledaysalaryval * $totalleave;


////// final submit form ////
@extract($_POST);
if($_POST){
    if($_POST['Submit']=='Save'){
	 mysqli_autocommit($link1, false);
	 $flag = true;	
	 
	  ///// check employe code is already exist for this month and year
	    $check = mysqli_query($link1," select id from hrms_salary_save where empid = '".$loccode."' and salary_year = '".$salary_year."' and salary_month = '".$salary_month."'  ");
		 if(mysqli_num_rows($check) ==0){
		   $sql1 = "insert into  hrms_salary_save SET  final_grossamt = '".$finalamt."', gross_deduction = '".$gross_deduction."', gross_earnings = '".$gross_earning."', others_deduction = '".$others_deduction."', deduct_pf = '".$deduct_pf."'  , deduct_esi = '".$deduct_esi."' , deduct_misc = '".$deduct_misc."' ,deduct_absent = '".$deduct_absent."' , deduct_vehcile_loan = '".$deduct_vehcile_loan."' , deduct_latemark = '".$deduct_latemark."' , deduct_installment_advance_taken = '".$deduct_installment_advance_taken."' , deduct_loan = '".$deduct_loan."' ,deduct_advances = '".$deduct_advances."' , others_pay = '".$others_pay."' , medical_exp_payable = '".$medical_exp_payable."' , family_allowance = '".$family_allowance."' ,  special_furn_allowance = '".$special_furn_allowance."' , 	maintainence_allowance = '".$maintainence_allowance."' , special_pay = '".$special_pay."' , dearness_allowances = '".$dearness_allowances."' ,travelling_allowances = '".$travelling_allowances."', medical_reimbursement = '".$medical_reimbursement."', incentive = '".$incentive."', leave_reimbursement = '".$leave_reimbursement."', previous_claims = '".$previous_claims."'  , out_station_lodging = '".$out_station_lodging."' , out_station_travelling_claims = '".$out_station_travelling_claims."' ,performance_bonus = '".$performance_bonus."' , birthday_bonus = '".$birthday_bonus."' , bonus = '".$bonus."' , award = '".$award."' , local_travelling_claims = '".$local_travelling_claims."' ,mobile_expenses = '".$mobile_expenses."' , conveyance_allowances = '".$conveyance_allowances."' , medi_educ_allowances = '".$medi_educ_allowances."' , special_allowances = '".$special_allowances."' ,  hra = '".$hra."' , 	basic_pay = '".$basic_pay."' ,create_by = '".$_SESSION['userid']."' ,  empid = '".$loccode."', salary_month = '".$salary_month."' , salary_year = '".$salary_year."' , cl = '".$cl."' , pl = '".$pl."' ,ml = '".$ml."' , sl = '".$sl."' , ss= '".$ss."' ,work_days = '".$work_days."' , late = '".$late."' , absent = '".$absent."' , final_salary = '".$final_salary."'   ";	
		
		  
		  $res2=mysqli_query($link1,$sql1);	
			/// check if query is execute or not//
			if(!$res2){
				$flag = false;
				$err_msg = "Error 2". mysqli_error($link1) . ".";
			}
			  ////// return message
				$msg="You have successfully added salary details of Empid ".$loccode;
		  
		 }
		 else {
			 
				    $flag= false;
					$msg="You already generated salary  of Empid $loccode for this month and year " ;
			 
			 }
			 	///// check all query are successfully executed
				if ($flag) {
					////// insert in activity table////
					dailyActivity($_SESSION['userid'],$loccode,"EMPLOYEE SALARY","Save",$ip,$link1,"");		
					mysqli_commit($link1);
					
					///// move to parent page
					header("location:salary_generate.php?msg=".$msg."&sts=success".$pagenav);
					exit;
				} else {
					mysqli_rollback($link1);
					///// move to parent page
					header("location:salary_generate.php?msg=".$msg."&sts=fail".$pagenav);
					exit;
				} 
				mysqli_close($link1);
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

 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
});

function changeval(){
	
	var finalamt = document.getElementById("finalamt").value;
	if(document.getElementById("cl").value) {
	var cl_leave = document.getElementById("cl").value;
	}else {
	var cl_leave = 0;	
		}
	if(document.getElementById("ml").value) {	
	var ml_leave = document.getElementById("ml").value;
	}else {
	var ml_leave = 0;	
		}
	if(document.getElementById("pl").value) {		
	var pl_leave = document.getElementById("pl").value;
	}else {
	var pl_leave = 0 ;	
		}
	if(document.getElementById("sl").value) {	
	var sl_leave = document.getElementById("sl").value;
	}else  {
		var sl_leave = 0 ;
	}
	if(document.getElementById("ss").value) {	
	var ss_leave = document.getElementById("ss").value;
	}else {
		var ss_leave = 0 ;
		}
	if(document.getElementById("absent").value) {	
	var absent_leave = document.getElementById("absent").value;
	}else {
		var absent_leave = 0 ;
		}
	var perdaysal = parseInt(finalamt)/30;
	var sum = parseInt(cl_leave)+parseInt(ml_leave)+parseInt(pl_leave)+parseInt(sl_leave)+parseInt(ss_leave)+parseInt(absent_leave);
	var deductionval = perdaysal * sum;
	var final_sal = parseInt(finalamt)-parseInt(deductionval);
	document.getElementById("final_salary").value = parseInt(final_sal);
	
	}
</script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
   <h2 align="center"><i class="fa fa-inr"></i> Salary Generate View</h2><br/>
    <div class="form-group"  id="page-wrap" style="margin-left:10px;">
   <form  name="frm8" id="frm8" class="form-horizontal" action="" method="post">
            
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Employee Details</div>
         <div class="panel-body">
         
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Employee Name </label>
              <div class="col-md-6" >
                 <input type="text" name="emp_name" id="emp_name" value="<?=$po_row['empname']?>" class="form-control" readonly />
                          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Employee Code</label>
              <div class="col-md-6">
               <input type="text" name="emp_name" id="emp_name" value="<?=$po_row['loginid']?>" class="form-control" readonly />
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Joining Date</label>
              <div class="col-md-6" >
                 <input type="text" name="emp_name" id="emp_name" value="<?=$po_row['joining_date']?>" class="form-control" readonly />
                          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Manager</label>
              <div class="col-md-6" >
               <input type="text" name="emp_name" id="emp_name" value="<?=$mangername['empname']?>" class="form-control" readonly />
              </div>
            </div>
          </div>
          
          <div class="form-group">
           		 <div class="col-md-6"><label class="col-md-6 control-label">Basic Pay</label>
            	  <div class="col-md-6">
              	  <input name="basic_pay" id="basic_pay" class="number form-control"  value="<?=$info_salary['basic_pay']?>" readonly/>
              	</div>
           	 </div>
            <div class="col-md-6"><label class="col-md-6 control-label">HRA</label>
              <div class="col-md-6">
                <input name="hra" id="hra" class="number form-control" value="<?=$info_salary['hra']?>" readonly />
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Special Allowances</label>
              <div class="col-md-6">
               <input name="special_allowances" id="special_allowances" class="number form-control" value="<?=$info_salary['special_allowances']?>" readonly/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Medical & Eductional Allowances </label>
              <div class="col-md-6">
               <input name="medi_educ_allowances" id="medi_educ_allowances" class="number form-control"  value="<?=$info_salary['medi_educ_allowances']?>" readonly/>
             		 </div>
           		   </div>            
         	      </div>
             <div class="form-group">
          	  <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Conveyance Allowances</label> 
                  <div class="col-md-6 ">
                  	<input name="conveyance_allowances" id="conveyance_allowances" class="number form-control" value="<?=$info_salary['conveyance_allowances']?>" readonly />	
                  </div>    
              </div>  
              <div class="col-md-6" > 
                  <label class="col-md-6 control-label"> Mobile Expenses</label> 
                  <div class="col-md-6 ">
                  	<input name="mobile_expenses" id="mobile_expenses" class="number form-control"  value="<?=$info_salary['mobile_expenses']?>" readonly/>	
                  </div>    
              </div>  
          </div>  
          
            <div class="form-group">
          	  <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Local Travelling Claims</label> 
                  <div class="col-md-6 ">
                  	<input name="local_travelling_claims" id="local_travelling_claims" class="number form-control" value="<?=$info_salary['local_travelling_claims']?>" readonly />	
                  </div>    
              </div>  
              <div class="col-md-6" > 
                <label class="col-md-6 control-label">Award </label> 
                <div class="col-md-6">
                  <input name="award" id="award" class="number form-control" value="<?=$info_salary['service_period']?>"  readonly/>
                    </div>    
                 </div>  
               </div> 
            <div class="form-group">
             <div class="col-md-6"><label class="col-md-6 control-label">Bonus</label>
              <div class="col-md-6">
               <input name = "bonus"  id= "bonus"  value="<?=$info_salary['bonus']?>" class="number form-control " readonly />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Birthday Bonus</label>
              <div class="col-md-6">
                  <input name = "birthday_bonus"  id= "birthday_bonus" value="<?=$info_salary['birthday_bonus']?>"  class="number form-control " readonly >
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Performance Bonus</label>
              <div class="col-md-6">
               <input name = "performance_bonus"    id= "performance_bonus" class="number form-control" value="<?=$info_salary['performance_bonus']?>" readonly >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Out Station Travelling Claims</label>
              <div class="col-md-6">
                  <input name = "out_station_travelling_claims" id= "out_station_travelling_claims" class="number form-control" value="<?=$info_salary['out_station_travelling_claims']?>" readonly >
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Out Station Lodging</label>
              <div class="col-md-6">
               <input name = "out_station_lodging"  id= "out_station_lodging" class="number form-control" value="<?=$info_salary['out_station_lodging']?>"  readonly>
              </div>
            </div>
           
            <div class="col-md-6"><label class="col-md-6 control-label">Previous Claims</label>
              <div class="col-md-6">
                  <input name = "previous_claims"  id= "previous_claims" class="number form-control"  value="<?=$info_salary['previous_claims']?>"  readonly>
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Leave Reimbursement</label>
              <div class="col-md-6">
               <input name = "leave_reimbursement"  id= "leave_reimbursement" class="number form-control"  value="<?=$info_salary['leave_reimbursement']?>" readonly >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Incentive</label>
              <div class="col-md-6">
                  <input name = "incentive"  id= "incentive" class="number form-control"  value="<?=$info_salary['incentive']?>" readonly>
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Medical Reimbursement</label>
              <div class="col-md-6">
               <input name = "medical_reimbursement" id= "medical_reimbursement" class="number form-control" value="<?=$info_salary['medical_reimbursement']?>" readonly>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Travelling Allowances</label>
              <div class="col-md-6">
                  <input name = "travelling_allowances"  id= "travelling_allowances" class="number form-control" value="<?=$info_salary['travelling_allowances']?>" readonly>
              </div>
            </div>
          </div> 
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Dearness Allowances </label>
              <div class="col-md-6">
               <input name = "dearness_allowances"  id= "dearness_allowances" class="number form-control" value="<?=$info_salary['dearness_allowances']?>" readonly>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Special Pay </label>
              <div class="col-md-6">
                  <input name = "special_pay"  id= "special_pay" class="number form-control" value="<?=$info_salary['special_pay']?>"  readonly>
              </div>
            </div>
          </div> 
           
            <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Maintainence Allowance</label>
              <div class="col-md-6">
               <input name = "maintainence_allowance"  id= "maintainence_allowance" class="number form-control" value="<?=$info_salary['maintainence_allowance']?>" readonly>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Special Furniture Allowance</label>
              <div class="col-md-6">
                 <input name = "special_furn_allowance"  id= "special_furn_allowance" class="number form-control" value="<?=$info_salary['special_furn_allowance']?>" readonly> 
              </div>
             </div>
            </div> 
            
            <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Family Allowance</label>
              <div class="col-md-6">
               <input name = "family_allowance"  id= "family_allowance" class="number form-control" value="<?=$info_salary['family_allowance']?>" readonly>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Medical Exp Payable</label>
              <div class="col-md-6">
                 <input name = "medical_exp_payable"  id= "medical_exp_payable" class="number form-control" value="<?=$info_salary['medical_exp_payable']?>" readonly> 
              </div>
             </div>
            </div> 
            
            <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Others Pay</label>
              <div class="col-md-6">
               <input name = "others_pay"  id= "others_pay" class="number form-control" value="<?=$info_salary['others_pay']?>" readonly>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label"></label>
              <div class="col-md-6">
               
              </div>
             </div>
            </div> 
            
          <div class="form-group">
           		 <div class="col-md-6"><label class="col-md-6 control-label">Advances Deduction</label>
            	  <div class="col-md-6">
              	  <input name="deduct_advances" id="deduct_advances" class="number form-control"  value="<?=$info_salary['deduct_advances']?>" readonly/>
              	</div>
           	 </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Loan Deduction</label>
              <div class="col-md-6">
                <input name="deduct_loan" id="deduct_loan" class="number form-control" value="<?=$info_salary['deduct_loan']?>" readonly />
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Installment Advance Taken Deduction</label>
              <div class="col-md-6">
               <input name="deduct_installment_advance_taken" id="deduct_installment_advance_taken" class="number form-control" value="<?=$info_salary['deduct_installment_advance_taken']?>" readonly />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Late Mark Deduction</label>
              <div class="col-md-6">
               <input name="deduct_latemark" id="deduct_latemark" class="number form-control"  value="<?=$info_salary['deduct_latemark']?>" readonly/>
             		 </div>
           		   </div>            
         	      </div>
             <div class="form-group">
          	  <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Vehcile_Loan Deduction</label> 
                  <div class="col-md-6 ">
                  	<input name="deduct_vehcile_loan" id="deduct_vehcile_loan" class="number form-control" value="<?=$info_salary['deduct_vehcile_loan']?>" readonly/>	
                  </div>    
              </div>  
              <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Absent Deduction</label> 
                  <div class="col-md-6 ">
                  	<input name="deduct_absent" id="deduct_absent" class="number form-control"  value="<?=$info_salary['deduct_absent']?>" readonly/>	
                  </div>    
              </div>  
          </div>  
          
            <div class="form-group">
          	  <div class="col-md-6" > 
                  <label class="col-md-6 control-label">Miscellanous Deduction</label> 
                  <div class="col-md-6 ">
                  	<input name="deduct_misc" id="deduct_misc" class="number form-control" value="<?=$info_salary['deduct_misc']?>" readonly/>	
                  </div>    
              </div>  
              <div class="col-md-6" > 
                <label class="col-md-6 control-label">ESI Deduction</label> 
                <div class="col-md-6">
                  <input name="deduct_esi" id="deduct_esi" class="number form-control" value="<?=$info_salary['deduct_esi']?>" readonly/>
                    </div>    
                 </div>  
               </div> 
            <div class="form-group">
             <div class="col-md-6"><label class="col-md-6 control-label">PF Deduction</label>
              <div class="col-md-6">
               <input name = "deduct_pf"  id= "deduct_pf"  value="<?=$info_salary['deduct_pf']?>" class="number form-control "  readonly/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Others Deduction</label>
              <div class="col-md-6">
                  <input name = "others_deduction"  id= "others_deduction" value="<?=$info_salary['others_deduction']?>"  class="number form-control " readonly>
              </div>
            </div>
          </div>   
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Gross Earning</label>
              <div class="col-md-6" >
                 <input type="text" name="gross_earning" id="gross_earning" value="<?=$gross_det['gross_earnings']?>" class="form-control" readonly />
                          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Gross Deduction</label>
              <div class="col-md-6" >
               <input type="text" name="gross_deduction" id="gross_deduction" value="<?=$gross_det['gross_deduction']?>" class="form-control" readonly />
              </div>
            </div>
          </div>
         
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Final Gross Amount</label>
              <div class="col-md-6" >
                 <input type="text" name="finalamt" id="finalamt" value="<?=$gross_det['final_grossamt']?>" class="form-control" readonly />
                          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label"></label>
              <div class="col-md-6" >
             
              </div>
            </div>
          </div>
         
        </div><!--close panel body-->
    </div>
   
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Leave Information</div>
      <div class="panel-body">
      <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">CL Leave </label>
              <div class="col-md-6" >
                 <input type="text" name="cl" id="cl" value="<?=$leave_det['cl']?>" class="form-control" onBlur="changeval();"  />
                          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">ML Leave</label>
              <div class="col-md-6">
               <input type="text" name="ml" id="ml" value="<?=$leave_det['ml']?>" class="form-control"  onBlur="changeval();" />
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Pl Leave</label>
              <div class="col-md-6" >
                 <input type="text" name="pl" id="pl" value="<?=$leave_det['pl']?>" class="form-control" onBlur="changeval();" />
                          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">SL Leave</label>
              <div class="col-md-6" >
               <input type="text" name="sl" id="sl" value="<?=$leave_det['sl']?>" class="form-control"  onBlur="changeval();"/>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">SS Leave</label>
              <div class="col-md-6" >
                 <input type="text" name="ss" id="ss" value="<?=$leave_det['ss']?>" class="form-control" onBlur="changeval();" />
                          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Working Days</label>
              <div class="col-md-6" >
               <input type="text" name="work_days" id="work_days" value="<?=$leave_det['work_days']?>" class="form-control"  />
              </div>
            </div>
          </div>
         
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Absent</label>
              <div class="col-md-6" >
                 <input type="text" name="absent" id="absent" value="<?=$leave_det['absent']?>" class="form-control" onBlur="changeval();" />
                          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Late</label>
              <div class="col-md-6" >
                <input type="text" name="late" id="late" value="<?=$leave_det['late']?>" class="form-control" onBlur="changeval();" />
              </div>
            </div>
          </div>
      </div><!--close panel body-->
    </div><!--close panel-->
    
    
    
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Salay</div>
      <div class="panel-body">
         <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Final Salary</label>
              <div class="col-md-6" >
                 <input type="text" name="final_salary" id="final_salary" value="<?=$gross_det['final_grossamt']-$deductionval?>" class="form-control" readonly  />
                          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label"></label>
              <div class="col-md-6">
              
              </div>
            </div>
          </div>
          
           <div class="row" align="center">
            <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="Submit" value="Save" title="save" >
             <input type="hidden" name="loccode" id="" value="<?=$empid?>"/>
             <input type="hidden" name="salary_month" id="salary_month" value="<?=$month?>"/>
             <input type="hidden" name="salary_year" id="salary_year" value="<?=$year?>"/>
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='salary_generate.php?<?=$pagenav?>'">
             </div>
          
      </div><!--close panel body-->
    </div><!--close panel-->
   
    <br><br>


 
  </form>
 </div>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>