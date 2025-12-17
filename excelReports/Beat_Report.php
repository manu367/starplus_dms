<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$from_date = base64_decode($_REQUEST['fdate']);
$to_date = base64_decode($_REQUEST['tdate']);
//$from_loc = base64_decode($_REQUEST['floc']);


/*if($from_loc=='' )
{  
	$from_party="a.from_location in (".$locstr.")";
}

else
{
	$from_party="(a.from_location='".$from_loc."') ";
}
*/


if($from_date=='' || $to_date=='')
{
	$sql_date='1';
}

else
{
	$sql_date="(plan_date>='".$from_date."' and plan_date<='".$to_date."')";
}

//////End filters value/////
$sql=mysqli_query($link1,"Select * from pjp_data where $sql_date")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Employee Code</strong></td>
<td><strong>Employee Name</strong></td>
<td><strong>Plan Date</strong></td>
<td><strong>PJP Name</strong></td>
<td><strong>Task Name</strong></td>
<td><strong>Visit City</strong></td>
<td><strong>Beat Count</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){

$admin_detail=explode("~",getAdminDetails($row_loc['assigned_user'],"name",$link1));
 ?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['assigned_user']?></td>
<td align="left"><?=$admin_detail[0]?></td>
<td align="left"><?=$row_loc['plan_date']?></td>
<td align="left"><?=$row_loc['pjp_name']?></td>
<td align="left"><?=$row_loc['task']?></td>
<td align="left"><?=$row_loc['visit_area']?></td>

<?php
$pjp_count='0';

if($row_loc['task']=='Dealer Visit'){
	
	$sql_dealer="select count(id) as pjp from dealer_visit where pjp_id='".$row_loc['id']."'";
	$rs_dealer=mysqli_query($link1,$sql_dealer);
	$row_dealer=mysqli_fetch_array($rs_dealer);
	$pjp_count=$row_dealer['pjp'];
}

else if($row_loc['task']=='Collection'){
	
	$sql_payment="select count(id) as pjp from payment_receive where pjp_id='".$row_loc['id']."'";
	$rs_payment=mysqli_query($link1,$sql_payment);
	$row_payment=mysqli_fetch_array($rs_payment);
	$pjp_count=$row_payment['pjp'];
}

else if($row_loc['task']=='Feedback'){
	
	$sql_feedback="select count(id) as pjp from query_master where pjp_id='".$row_loc['id']."'";
	$rs_feedback=mysqli_query($link1,$sql_feedback);
	$row_feedback=mysqli_fetch_array($rs_feedback);
	$pjp_count=$row_feedback['pjp'];
}

else if($row_loc['task']=='Sale Order'){
	
	$sql_order="select count(id) as pjp from purchase_order_master where pjp_id='".$row_loc['id']."'";
	$rs_order=mysqli_query($link1,$sql_order);
	$row_order=mysqli_fetch_array($rs_order);
	$pjp_count=$row_order['pjp'];
}


?>

<td align="left"><?=$pjp_count?></td>

</tr>
<?php
$i+=1;		
}
?>
</table>