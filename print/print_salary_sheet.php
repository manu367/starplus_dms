<?php
require_once("../config/config.php");
$empId = base64_decode($_REQUEST['emp_id']);
$empMonth = base64_decode($_REQUEST['emp_month']);
$empYear = base64_decode($_REQUEST['emp_year']);

$emp_sql = "SELECT * FROM hrms_salary_upload where emp_id = '".$empId."' and salary_month = '".$empMonth."' and salary_year = '".$empYear."' ";
$emp_res = mysqli_query($link1,$emp_sql);
$emp_row = mysqli_fetch_assoc($emp_res);

$emp_info = mysqli_fetch_assoc ( mysqli_query ( $link1, "SELECT empname, departmentid, designationid, address, city, state FROM hrms_employe_master WHERE loginid = '".$empId."' " ) );

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Document Printing</TITLE>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<link href="../css/font-awesome.min.css" rel="stylesheet">
<STYLE>
P.page {
	PAGE-BREAK-AFTER: always
}
BODY {
	FONT-SIZE: 1px;
	FONT-FAMILY: 'ARIAL'
}
TABLE {
	BORDER-RIGHT: medium none;
	BORDER-LEFT-COLOR: black;
	BORDER-TOP-COLOR: black;
	BORDER-BOTTOM: medium none
}
TABLE.l {
	BORDER-TOP: medium none
}
TABLE.t {
	BORDER-LEFT: medium none
}
TABLE.none {
	BORDER-RIGHT: medium none;
	BORDER-TOP: medium none;
	BORDER-LEFT: medium none;
	BORDER-BOTTOM: medium none
}
TD.none {
	BORDER-RIGHT: medium none;
	BORDER-TOP: medium none;
	BORDER-LEFT: medium none;
	BORDER-BOTTOM: medium none
}
TD {
	BORDER-TOP: medium none;
	FONT-SIZE: 8pt;
	BORDER-BOTTOM-COLOR: black;
	BORDER-LEFT: medium none;
	BORDER-RIGHT-COLOR: black
}
TD.r {
	BORDER-BOTTOM: medium none
}
TD.b {
	BORDER-RIGHT: medium none
	
}
TD.l {
	BORDER-RIGHT: medium none
	
}
TD.bl {
	BORDER-RIGHT: medium none;
	BORDER-BOTTOM: thin outset
}
@media Print {
.scrbtn {
	DISPLAY: none
}
}
.style6 {
	font-family: "Courier New", Courier, monospace
}
.style8 {
	font-family: "Courier New", Courier, monospace;
	font-weight: bold;
}
.style9 {
	font-size: 10pt;
	font-weight: bold;
}
</STYLE>
</HEAD>
<BODY bottomMargin=0 leftMargin=40 topMargin=0 onload=vbscript:window.print()>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<TABLE width=800 align="center" cellPadding=0 cellSpacing=0>
  <TBODY>
    <TR>
      <TD vAlign=top>
	  <TABLE cellSpacing=0 cellPadding=0 width="100%" border=1>
          <TBODY>
            <TR>
              <TD>
                  <div>
                        <img src="../img/inner_logo.png" style="float:left;margin-top:5px; ">
                        <div style="margin:10px 0px;" class="style9"> &nbsp;&nbsp;&nbsp;<?php echo "SALARY STRUCTURE";?> </div>
                        <div style="margin-left:300px;">
                        	&nbsp;&nbsp;&nbsp;<?=$emp_info['address'];?><br/>&nbsp;&nbsp;&nbsp;<?=$emp_info['city']." , ".$emp_info['state'];?><br/>&nbsp;&nbsp;&nbsp;
                        </div> 
                  </div>
              </TD>
            </TR>
          </TBODY>
        </TABLE>
        <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
          <TBODY>
          	<TR>
              <TD colspan="4" style="padding-left:5px; width:25%; color:#009;">
              	<FONT size=2><B>   Employee Details </B></FONT>
              </TD>
            </TR>
          	<TR>
              <TD style="padding-left:5px; width:20%;">
              	<FONT size=2><B> Employee Name : </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:30%;" >
             	<?=$emp_info['empname']." | ".$empId;?>
              </TD>
              <TD style="padding-left:5px; width:20%;">
             	<FONT size=2><B> Father's Name : </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:30%;"  >
             	
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:20%;">
              	<FONT size=2><B>  Department : </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:30%;" >
             	<?php echo getAnyDetails($emp_info['departmentid'],'dname','departmentid','hrms_department_master',$link1); ?>
              </TD>
              <TD style="padding-left:5px; width:20%;">
             	<FONT size=2><B> Designation : </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:30%;"  >
             	<?php echo getAnyDetails($emp_info['designationid'],'designame','designationid','hrms_designation_master',$link1); ?>
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:20%;">
              	<FONT size=2><B>  ID Card No. : </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:30%;" >
             	
              </TD>
              <TD style="padding-left:5px; width:20%;">
             	<FONT size=2><B> PF No. : </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:30%;"  >
             	
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:20%;">
              	<FONT size=2><B>   Leave WP : </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:30%;" >
             	
              </TD>
              <TD style="padding-left:5px; width:20%;">
             	<FONT size=2><B> Pay Count : </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:30%;"  >
             	
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:20%;">
              	<FONT size=2><B>   Bank Name : </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:30%;" >
             	<?=$emp_row['bank_name'];?>
              </TD>
              <TD style="padding-left:5px; width:20%;">
              	<FONT size=2><B>  Bank A/C No. : </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:30%;" >
             	<?=$emp_row['bank_ac_no'];?>
              </TD>
            </TR>
            
          </TBODY>
        </TABLE>
        
        <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
          <TBODY>
          	<TR>
              <TD colspan="6" style="padding-left:5px; color:#009;">
              	<FONT size=2><B> Earnings </B></FONT>
              </TD>
            </TR>
          	<TR>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Basic Pay</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['basic_pay'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>HRA</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['hra'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Special Allowance</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['spl_allow'];?>
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>  Educational & Med Allowances </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['edu_med_allow'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B> Conveyance Allowances </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['conve_allown'];?>
              </TD>
               <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B> Mobile Expences </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['mobile_exp'];?>
              </TD>
            </TR>
                        
          </TBODY>
        </TABLE>
        
        <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
          <TBODY>
          	<TR>
              <TD colspan="6" style="padding-left:5px; color:#009;">
              	<FONT size=2><B> Option - 1 </B></FONT>
              </TD>
            </TR>
          	<TR>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Local Travelling Claims</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['local_trav_claim'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B> Award </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['award'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Diwali Bonus</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['diwali_bonus'];?>
              </TD>
            </TR>
          </TBODY>
        </TABLE>
        
         <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
          <TBODY>
          	<TR>
              <TD colspan="6" style="padding-left:5px; color:#009;">
              	<FONT size=2><B> Option - 2 </B></FONT>
              </TD>
            </TR>
          	<TR>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Birthday Bonus</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['birth_bonus'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Performance Bonus</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['performence_bonus'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Advances</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['advance'];?>
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Out Station Travelling Claims</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['out_station_travel_claim'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Out Station Lodging</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['out_station_lodge'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Previous Claims</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['privious_claim'];?>
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Leave Reimbersement</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['leave_rembers'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Incentive</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['incentive'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Loan</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['loan'];?>
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Medical Reimbersement</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['med_rembers'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Travelling Allowances</B></FONT>
              </TD>
              <TD colspan="3" style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['travel_allownse'];?>
              </TD>
            </TR>
          </TBODY>
        </TABLE>
        
        <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
          <TBODY>
          	<TR>
              <TD colspan="6" style="padding-left:5px; color:#009;">
              	<FONT size=2><B> Option - 3 </B></FONT>
              </TD>
            </TR>
          	<TR>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Dearness Allowances</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['dearness_allownse'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Special Pay</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['special_pay'];?>
              </TD>
               <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Maintainence Allowances</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['maintain_allownse'];?>
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Special Furn. Allowances</B></FONT>
              </TD>
              <TD colspan="5" style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['special_furn_allownse'];?>
              </TD>
            </TR>
          </TBODY>
        </TABLE>
        
        <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
          <TBODY>
          	<TR>
              <TD colspan="6" style="padding-left:5px; color:#009;">
              	<FONT size=2><B> Option - 4 </B></FONT>
              </TD>
            </TR>
          	<TR>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Faimily Allowances</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['family_allownse'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Medi. Exp. Payble</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['medi_exp_payble'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Interest on Medical Claim</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['intrest_medi_claim'];?>
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Soft Loan</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['soft_loan_amt'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Gratuity</B></FONT>
              </TD>
              <TD colspan="3" style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['gratuity'];?>
              </TD>
            </TR>
          </TBODY>
        </TABLE>
        
        <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
          <TBODY>
          	<TR>
              <TD colspan="6" style="padding-left:5px; color:#009;">
              	<FONT size=2><B> Deductions </B></FONT>
              </TD>
            </TR>
          	<TR>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>PF@12%</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['pf_12'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Insurance</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['insurance'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Installment Advance Taken</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['instalment_advan_taken'];?>
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Installment Againest Staff Scheme</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['instalment_against_staff'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>VPF</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['vpf'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Late Mark Deductions</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['late_mark_dedection'];?>
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Mobile Deductions</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['mobile_dedection'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Absent Deductions</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['absent_dedection'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Vehicle Lone</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['veh_loan'];?>
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Misc. Deductions</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['misc_dedection'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Staff Scheme</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['staff_scheam'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>ESI</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['esi'];?>
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>ESI on Arrears</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['esi_arears'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>School Fee</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['school_fee'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>FA Recovery</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['fa_recovry'];?>
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>PF on Leave Encashment</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['pf_on_leave_encas'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Penality If Any</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['penalty_any'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>Staff Loan</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['staff_loan'];?>
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Loan Installment</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['loan_installm'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B>FW Fund</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['fw_fund'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B>Others</B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['other'];?>
              </TD>
            </TR>
          </TBODY>
        </TABLE>
        
        <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
          <TBODY>
          	<TR>
              <TD colspan="6" style="padding-left:5px; color:#009;">&nbsp;
              	
              </TD>
            </TR>
          	<TR>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B> Gross Earning </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<?=$emp_row['gross_earning'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B> Gross Deductions </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['gross_dedection'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B> Net Amount </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<?=$emp_row['net_amount'];?>
              </TD>
            </TR>
            <TR>
              <TD colspan="6" style="padding-left:5px; color:#009;">&nbsp;
              	
              </TD>
            </TR>
          </TBODY>
        </TABLE>
        
        <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
          <TBODY>
          	<TR>
              <TD colspan="6" style="padding-left:5px; color:#009;">
              	<FONT size=2><B> Leave Status </B></FONT>
              </TD>
            </TR>
          	<TR>
              <TD style="padding-left:5px; width:16.66%;">
              	<FONT size=2><B> EL on <?=$empYear."/".$empMonth;?></B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >
             	<FONT size=2><B> CL on <?=$empYear."/".$empMonth;?> </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B> EL Taken </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<FONT size=2><B> CL Taken </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">
             	<FONT size=2><B> Late Marked </B></FONT>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >
             	<FONT size=2><B> Leave Balance </B></FONT>
              </TD>
            </TR>
            <TR>
              <TD style="padding-left:5px; width:16.66%;">&nbsp;
              	<?=$emp_row['el_this_month'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;" >&nbsp;
             	<?=$emp_row['cl_this_month'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">&nbsp;
             	<?=$emp_row['el_taken'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >&nbsp;
             	<?=$emp_row['cl_taken'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;">&nbsp;
             	<?=$emp_row['late_marked'];?>
              </TD>
              <TD style="padding-left:5px; width:16.66%;"  >&nbsp;
             	<?=$emp_row['leave_balence'];?>
              </TD>
            </TR>
            <TR>
              <TD colspan="6" style="padding-left:5px; color:#009;">&nbsp;
              	
              </TD>
            </TR>
          </TBODY>
        </TABLE>
        
        <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
          <TBODY>
          	<TR>
              <TD colspan="6" style="padding-left:5px;">
              	<FONT size=2><B> * Late Marked is adjusted in Leave Balance </B></FONT>
              </TD>
            </TR>
          	<TR>
               <TD colspan="6" style="padding-left:5px;"> Salary will be transferred to your salary account </TD>
            </TR>
            <TR>
               <TD colspan="6" style="padding-left:5px;"> This is a computer generated slip and doesnot need any signature </TD>
            </TR>
            <TR>
              <TD colspan="6" style="padding-left:5px; color:#009;">&nbsp;
              	
              </TD>
            </TR>
          </TBODY>
        </TABLE>
        <br><br>
         
		</TD>
    </TR>
  </TBODY>
</TABLE>
</BODY>
</HTML>
