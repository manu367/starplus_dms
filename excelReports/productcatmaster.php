<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$status = base64_decode($_REQUEST['status']);
## selected brand
if($status){$selstatus="status='".$_REQUEST['status']."'";}else{$selstatus="1";}
//////End filters value/////
$sql=mysqli_query($link1,"SELECT * FROM product_cat_master WHERE ".$selstatus."")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Product Cat Name</strong></td>
<td><strong>Product Cat Code</strong></td>
<td><strong>Short Code</strong></td>
<td><strong>Status</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
if($row['status'] == '1'){ $status = "Active";}else { $status = "Deactive";}
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['cat_name']?></td>
<td align="left"><?=$row_loc['product_code']?></td>
<td align="left"><?=$row_loc['short_code']?></td>
<td align="left"><?=$status?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>
