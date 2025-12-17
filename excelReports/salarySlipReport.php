<?php
print("\n");
print("\n");
////// filters value/////
//// date function
function dt_format1($dt_sel)
{
 return substr($dt_sel,8,2)."-".substr($dt_sel,5,2)."-".substr($dt_sel,0,4);
}
//// time function
function time_format($t_sel)
{
 return  substr($t_sel,11,2).''.substr($t_sel,13,3).':'.substr($t_sel,17,3);
}


$fromdate = base64_decode($_REQUEST['fromDate']);
$todate = base64_decode($_REQUEST['toDate']);


//////End filters value/////

 $sqldata = "Select * from hrms_salary_upload where update_date BETWEEN '" . $fromdate . "' and '" . $todate . "'   order by sno desc";

$sql = mysqli_query($link1, $sqldata);
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>S.No.</strong></td>
        <th>Employee Name</th>       
        <th>Basic Pay</th>
        <th>Special Allowance</th>
        <th>Education & Medical Allowance</th>
        <th>Convenience Allowance</th>
        <th>Mobile Expense</th>
        <th>Local Travelling Claim</th>
        <th>Award</th>
        <th>Diwali Bonus</th>
        <th>Birthday Bonus</th>
        <th>Performance Bonus</th>
        <th>Advance</th>
        <th>Out Station Travel Claim</th>
        <th>Out Station Lodding</th>
        <th>Previous Claim</th>
        <th>Leave Reimbersment</th>
        <th>Incentive</th>
        <th>Loan</th>
        <th>Medical Reimbersment</th>
        <th>Travel Allowance</th>
        <th>Clearance Allowance</th>
        <th>Spcial Pay</th>
        <th>Maintenance Allowance</th>
        <th>Furniture Allowance</th>
        <th>Family Allowance</th>
        <th>Medical Expense Payable</th>
        <th>Interest Medical Claim</th>
        <th>Loan Amount</th>
        <th>Gratuity</th>
        <th>PF</th>
        <th>Insurance</th>
        <th>Installement Advance Taken</th>
        <th>Installement Against Staff</th>
        <th>Late Mark  Deduction</th>
        <th>Mobile  Deduction</th>
        <th>Absent  Deduction</th>
        <th>Vehicle Loan</th>
        <th>Misc. Deduction</th>
        <th>Staff Secheme</th>
        <th>ESI</th>
        <th>ESI Arears</th>
        <th>School Fee</th>
        <th>Recovery</th>
        <th>Pf on Leaves</th>
        <th>Penalty Any</th>
        <th>Staff Loan</th>
        <th>Penalty AnLoan Installment</th>
        <th>VPF</th>
        <th>FW Fund</th>
        <th>Other</th>
        <th>Gross Earning</th>
        <th>Gross Deduction</th>
        <th>Final Salary</th>
        
       
    </tr>
    <?php
    $i = 1;
    while ($row = mysqli_fetch_assoc($sql)) {
		$empname = mysqli_fetch_assoc(mysqli_query($link1, "Select name from admin_users where username  ='" . $row['emp_id'] . "'"));
        $username = mysqli_fetch_assoc(mysqli_query($link1, "Select name from admin_users where username='" . $row['update_by'] . "'"));
        ?>
        <tr>
            <td align="left"><?=$i;?></td>
            <td><?=$empname['name']."(".$row['emp_id'].")"; ?></td>         
             <td><?=$row['basic_pay']; ?></td>   
             <td><?=$row['spl_allow']; ?></td>
             <td><?=$row['edu_med_allow']; ?></td>
             <td><?=$row['conve_allown']; ?></td>
             <td><?=$row['mobile_exp']; ?></td>
             <td><?=$row['local_trav_claim']; ?></td>  
             <td><?=$row['award']; ?></td>
             <td><?=$row['diwali_bonus']; ?></td>
             <td><?=$row['birth_bonus']; ?></td>
             <td><?=$row['performence_bonus']; ?></td>
             <td><?=$row['advance']; ?></td>
            <td><?=$row['out_station_travel_claim']; ?></td>
            <td><?=$row['out_station_lodge']; ?></td>
            <td><?=$row['privious_claim']; ?></td>
            <td><?=$row['leave_rembers']; ?></td>  
            <td><?=$row['incentive']; ?></td>
            <td><?=$row['loan']; ?></td>
            <td><?=$row['med_rembers']; ?></td>
            <td><?=$row['travel_allownse']; ?></td>
            <td><?=$row['dearness_allownse']; ?></td>
            <td><?=$row['special_pay']; ?></td>
            <td><?=$row['maintain_allownse']; ?></td>
            <td><?=$row['special_furn_allownse']; ?></td>
            <td><?=$row['family_allownse']; ?></td>
            <td><?=$row['medi_exp_payble']; ?></td> 
            <td><?=$row['intrest_medi_claim']; ?></td> 
            <td><?=$row['soft_loan_amt']; ?></td> 
            <td><?=$row['gratuity']; ?></td> 
            <td><?=$row['pf_12']; ?></td>  
            <td><?=$row['insurance']; ?></td>
            <td><?=$row['instalment_advan_taken']; ?></td>
            <td><?=$row['instalment_against_staff']; ?></td>
            <td><?=$row['late_mark_dedection']; ?></td>
            <td><?=$row['mobile_dedection']; ?></td>
            <td><?=$row['absent_dedection']; ?></td> 
            <td><?=$row['veh_loan']; ?></td> 
            <td><?=$row['misc_dedection']; ?></td> 
            <td><?=$row['staff_scheam']; ?></td> 
            <td><?=$row['esi']; ?></td> 
            <td><?=$row['esi_arears']; ?></td>
            <td><?=$row['school_fee']; ?></td>
            <td><?=$row['fa_recovry']; ?></td>
            <td><?=$row['pf_on_leave_encas']; ?></td>
            <td><?=$row['penalty_any']; ?></td>
            <td><?=$row['staff_loan']; ?></td>
            <td><?=$row['loan_installm']; ?></td>
            <td><?=$row['vpf']; ?></td>  
            <td><?=$row['fw_fund']; ?></td>  
            <td><?=$row['other']; ?></td>   
            <td><?=$row['gross_earning']; ?></td>  
            <td><?=$row['gross_dedection']; ?></td> 
            <td><?=$row['net_amount']; ?></td> 
                    
          
              

        </tr>
        <?php
        $i+=1;
    }
    ?>
</table>
