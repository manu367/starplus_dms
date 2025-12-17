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
        <th>Empid</th>
        <th>Bank Name</th>        
        <th>Bank Account No.</th>
        <th>Basic Pay</th>
        <th>Gross Earning</th>
        <th>Gross Deduction</th>
        <th>Final Salary</th>
        <th>EL Taken</th>
        <th>CL Taken</th>
        <th>Late Marked </th>
        <th>Leave Balance </th>
        <th>Update by</th>
        <th>Update Date</th>
       
    </tr>
    <?php
    $i = 1;
    while ($row = mysqli_fetch_assoc($sql)) {
		$empname = mysqli_fetch_assoc(mysqli_query($link1, "Select name from admin_users where username  ='" . $row['emp_id'] . "'"));
        $username = mysqli_fetch_assoc(mysqli_query($link1, "Select name from admin_users where username='" . $row['update_by'] . "'"));
        ?>
        <tr>
            <td align="left"><?=$i;?></td>
            <td><?=$empname['name']; ?></td>
            <td><?=$row['emp_id']; ?></td>
            <td><?=$row['bank_name']; ?></td>
             <td><?=$row['bank_ac_no']; ?></td>
             <td><?=$row['basic_pay']; ?></td>            
            <td><?=$row['gross_earning']; ?></td>  
            <td><?=$row['gross_dedection']; ?></td> 
            <td><?=$row['net_amount']; ?></td> 
             <td><?=$row['el_taken']; ?></td> 
            <td><?=$row['cl_taken']; ?></td>
            <td><?=$row['late_marked']; ?></td>
            <td><?=$row['leave_balence']; ?></td>
            <td><?=$username['name']; ?></td>
            <td><?=dt_format1($row['update_date']); ?></td>         
          
              

        </tr>
        <?php
        $i+=1;
    }
    ?>
</table>
