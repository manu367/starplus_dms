<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables


$fromdate=base64_decode($_REQUEST[fromdate]);
$todate=base64_decode($_REQUEST[todate]);
$location=base64_decode($_REQUEST[loc]);

## selected  Status
if($fromdate=='' || $todate=='')
{
	$sql_date='1';
}
else
{
    $sql_date="(expense_date>='".$fromdate."' and expense_date<='".$todate."')";
}


if($location == ''){
$location = '';
}
else {
  $location = "and location_code = '".$location."' ";
}

//////End filters value/////
$sql=mysqli_query($link1,"SELECT * FROM locationwise_expense  where $sql_date $location and entry_by = '".$_SESSION['userid']."' order by id desc ")or die("er1".mysqli_error($link1));

?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Document No.</strong></td>
<td><strong>Location</strong></td>
<td><strong>Expense Date</strong></td>
<td><strong>Narration</strong></td>
<td><strong>Amount</strong></td>
<td><strong>Payment Mode</strong></td>
<td><strong>Status</strong></td>
<td><strong>Bank Name</strong></td>
<td><strong>Bank Branch</strong></td>
<td><strong>Bank Transfer Mode</strong></td>
<td><strong>IFSC Code</strong></td>
<td><strong>Account No.</strong></td>
<td><strong>Cheque No.</strong></td>
<td><strong>Cheque Date</strong></td>
<td><strong>Reference No.</strong></td>
<td><strong>Create By</strong></td>
<td><strong>Create Date</strong></td>
<td><strong>Update By</strong></td>
<td><strong>Update Date</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['doc_no']?></td>
<td align="left"><?=getLocationDetails($row_loc['location_code'],"name",$link1)?></td>
<td align="left"><?=$row_loc['expense_date']?></td>
<td align="right"><?=$row_loc['narration']?></td>
<td align="right"><?=$row_loc['amount']?></td>
<td align="left"><?=$row_loc['payment_mode']?></td>
<td align="left"><?=$row_loc['status']?></td>
<td align="left"><?=$row_loc['bankname']?></td>
<td align="left"><?=$row_loc['bank_branch']?></td>
<td align="left"><?=$row_loc['bank_transfermode']?></td>
<td align="left"><?=$row_loc['ifsc_code']?></td>
<td align="left"><?=$row_loc['account_no']?></td>
<td align="left"><?=$row_loc['dd_chequeno']?></td>
<td align="left"><?=$row_loc['dd_date']?></td>
<td align="left"><?=$row_loc['transcation_id']?></td>
<td align="left"><?=getAdminDetails($row_loc['entry_by'],"name",$link1)?></td>
<td align="left"><?=$row_loc['entry_date']?></td>
<td align="left"><?=getAdminDetails($row_loc['update_by'],"name",$link1)?></td>
<td align="left"><?=$row_loc['update_date']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>