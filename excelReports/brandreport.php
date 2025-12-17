<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$status=base64_decode($_REQUEST['status']);

## selected status
if($status!=""){
	$newstatus="status='".$status."'";
}else{
	$newstatus="1";
}

//////End filters value/////

$sql=mysqli_query($link1,"Select * from make_master where $newstatus  order by make")or die("er1".mysqli_error($link1));
?>
<table width="50%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td width="3%"><strong>Brand</strong></td>
<!--<td><strong>Create Date</strong></td>-->
<td><strong>Status</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left" width="3%"><?=$i?></td>
<td align="left" width="20%"><?=$row_loc['make']?></td>
<?php /*?><td align="left" width="10%"><?=$row_loc['release_date']?></td><?php */?>
<td align="left" width="10%"><?php if($row_loc['status'] == '1'){ echo "Active";} else { echo "Deactive";}?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>