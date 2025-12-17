<?php
print("\n");
print("\n");
////// filters value/////
$user_id = base64_decode($_REQUEST['user_id']);
$fromdate = base64_decode($_REQUEST['fromDate']);
$todate = base64_decode($_REQUEST['toDate']);

//////End filters value///// 
if($user_id){
	$empdet = explode("~",getAnyDetails($user_id,"name,oth_empid,phone,emailid,designationid,department,subdepartment","username","admin_users",$link1));
?>
<br/>
<table width="100%" border="1" class="table table-bordered">
  <tr>
    <td width="20%" colspan="2"><strong>Employee Name</strong></td>
    <td width="30%" colspan="2"><?=$empdet[0]?></td>
    <td width="20%" colspan="2"><strong>Employee Code</strong></td>
    <td width="30%" colspan="2"><?=$empdet[1]?></td>
    </tr>
  <tr>
    <td colspan="2"><strong>Designation</strong></td>
    <td colspan="2"><?=getAnyDetails($empdet[4],"designame","designationid","hrms_designation_master",$link1)?></td>
    <td colspan="2"><strong>Department</strong></td>
    <td colspan="2"><?=getAnyDetails($empdet[5],"dname","departmentid","hrms_department_master",$link1)." | ".getAnyDetails($empdet[6],"subdept","subdeptid","hrms_subdepartment_master",$link1)?></td>
    </tr>
  <tr>
    <td colspan="2"><strong>Contact Details</strong></td>
    <td colspan="2"><?=$empdet[2]." , ".$empdet[3];?></td>
    <td colspan="2"><strong>Expense Period</strong></td>
    <td colspan="2"><?="From:-".$fromdate." To:-".$todate;?></td>
  </tr>
</table>
<table width="100%" border="1" class="table table-bordered">
  <thead>
  <tr style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
    <th width="5%">S.No.</th>
    <th width="10%">Expense Date</th>
    <th width="10%">Entry Date</th>
    <th width="25%">Description of Expenses</th>
    <th width="15%">Amount</th>
    <th width="12%">Approval By</th>
    <th width="13%">Approval Date</th>
    <th width="10%">Approval Status</th>
  </tr>
  </thead>
  <tbody>
  <?php
	$i = 1;
	$totamt = 0.00;
	$res_tada = mysqli_query($link1,"SELECT * FROM ta_da WHERE userid ='".$user_id."' AND entry_date >= '".$fromdate."' AND entry_date <= '".$todate."' AND courier_exp!=0.00 ORDER BY id DESC")or die("ER1 ".mysqli_error($link1));
	if(mysqli_num_rows($res_tada)>0){
	while($row_tada = mysqli_fetch_array($res_tada)){
		$appdet = explode("~",getAnyDetails($row_tada["system_ref_no"],"action_by,action_date,action_time","ref_no","approval_activities",$link1));
	?>
  <tr>
    <td><?=$i?></td>
    <td align="center"><?=$row_tada["expense_date"]?></td>
    <td align="center"><?=$row_tada["entry_date"]?></td>
    <td><?=$row_tada["remark"]?></td>
    <td align="right"><?=$row_tada["courier_exp"]?></td>
    <td><?=$appdet[0]?></td>
    <td align="center"><?=$appdet[1]?></td>
    <td><?=$row_tada["status"]?></td>
  </tr>
  <?php
		$i++;
		$totamt +=$row_tada["courier_exp"];
    }
  ?>
  <tr>
    <td colspan="4" align="right"><strong>Total</strong></td>
    <td align="right"><strong><?=number_format($totamt,"2",".","");?></strong></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php 
  }else{
  ?>
  <tr>
    <td colspan="8" align="center">No data found</td>
  </tr>
  <?php
  }
  ?>
  </tbody>
</table>

	
<?php
}
?>
