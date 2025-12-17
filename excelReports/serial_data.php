<?php 
print("\n");
print("\n");
$filter_str = "1";
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND DATE(create_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND DATE(create_date) <= '".$_REQUEST['tdate']."'";
}
$sql=mysqli_query($link1,"SELECT * FROM serial_upload_data WHERE ".$filter_str)or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Serial No</strong></td>
<td><strong>Product Code</strong></td>
<td><strong>Model Code</strong></td>
<td><strong>Product Name</strong></td>
<td><strong>Dealer Code</strong></td>
<td><strong>Dealer Name</strong></td>
<td><strong>Create Date</strong></td>
<td><strong>Update Date</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['serial_no']?></td>
<td align="left"><?=$row_loc['product_code']?></td>
<td align="left"><?=$row_loc['model_code']?></td>
<td align="left"><?=$row_loc['product_name']?></td>
<td align="left"><?=$row_loc['dealer_code']?></td>
<td align="left"><?=$row_loc['dealer_name']?></td>
<td align="left"><?=$row_loc['create_date']?></td>
<td align="left"><?=$row_loc['update_date']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>
