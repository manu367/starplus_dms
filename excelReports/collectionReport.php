<?php 
print("\n");
print("\n");
$acessloc = getAccessLocation($_SESSION['userid'],$link1);
////// filters value/////
//// extract all encoded variables

$fdate=base64_decode($_REQUEST['fdate']);
$locationName=base64_decode($_REQUEST['locationName']);

## selected location
if($locationName!=""){
	$loc_name="to_location='".$locationName."'";
}else{
	$loc_name="1";
}
//////End filters value/////

$sql=mysqli_query($link1,"SELECT * FROM payment_receive WHERE DATE(collection_date) = '".$fdate."'  AND to_location IN (".$acessloc.") AND ".$loc_name." AND collection_flag = 'Y'")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td>Doc. No.</td>         
<td>Against Ref. No.</td>
<td>Location Name</td>
<td>Received Amt</td>
<td>Received Date</td>
<td>Payment Mode</td>
<td>Verified Account</td>
<td>Verified Amt</td>
<td>Verified Date</td>
<td>Remark</td>
</tr>
<?php
$i=1;
while($row_paym = mysqli_fetch_array($sql)){
?>
<tr>
<td><?=$row_paym["doc_no"]?></td>
<td><?=$row_paym["against_ref_no"]?></td>   
<td><?=getLocationDetails($row_paym['from_location'],"name" ,$link1) ."(" . $row_paym['from_location'].")" ; ?></td>        
<td><?=$row_paym["rec_amount"]?></td>
<td><?=$row_paym["entry_dt"]?></td>
<td><?=$row_paym["payment_mode"]?></td>
<td><?=$row_paym["collection_account"]."-".$row_paym["collection_accid"]?></td>
<td><?=$row_paym["collection_amt"]?></td>
<td><?=substr($row_paym["collection_date"],0,7)?></td>								
<td><?=$row_paym["collection_rmk"]?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>