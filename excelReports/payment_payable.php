<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$location=base64_decode($_REQUEST[loc]);
## selected location
if($location!=""){
	$loc="to_location='".$location."'";
}else{
	$locstr=getAccessLocation($_SESSION['userid'],$link1);
	$loc="to_location in (".$locstr.")";
}
//////End filters value/////
$sql = mysqli_query($link1,"SELECT * FROM billing_master where ".$loc." and status='Received' and is_adjust!='Y'")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Payable Party</strong></td>
<td><strong>Payable Party City</strong></td>
<td><strong>Payable Party State</strong></td>
<td><strong>Receivable Party</strong></td>
<td><strong>Receivable Party City</strong></td>
<td><strong>Receivable Party State</strong></td>
<td><strong>Document No.</strong></td>
<td><strong>Document Date</strong></td>
<td><strong>Document Status</strong></td>
<td><strong>Amount</strong></td>
<td><strong>Aging</strong></td>
</tr>
<?php
$i=1;
$new_loc = "";
$old_loc = "";
$new_loc2 = "";
$old_loc2 = "";
while($row_loc = mysqli_fetch_array($sql)){
	$new_loc = $row_loc['to_location'];
	$new_loc2 = $row_loc['from_location'];
	if($old_loc != $new_loc){
		$locdet=explode("~",getLocationDetails($row_loc['to_location'],"name,city,state,id_type",$link1));
	}
	if($old_loc2 != $new_loc2){
		$locdet2=explode("~",getLocationDetails($row_loc['from_location'],"name,city,state,id_type",$link1));
	}
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$locdet[0]?></td>
<td align="left"><?=$locdet[1]?></td>
<td align="left"><?=$locdet[2]?></td>
<td align="left"><?=$locdet2[0]?></td>
<td align="left"><?=$locdet2[1]?></td>
<td align="left"><?=$locdet2[2]?></td>
<td align="left"><?=$row_loc['challan_no']?></td>
<td align="left"><?=$row_loc['entry_date']?></td>
<td align="left"><?=$row_loc['status']?></td>
<td align="right"><?=$row_loc['total_cost']?></td>
<td align="right"><?=daysDifference($today,$row_loc['dc_date']);?></td>
</tr>
<?php
$old_loc = $row_loc['to_location'];
$old_loc2 = $row_loc['from_location'];
$i+=1;		
}
?>
</table>