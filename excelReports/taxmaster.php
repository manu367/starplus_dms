<?php 
print("\n");
print("\n");
$sql=mysqli_query($link1,"Select * from tax_hsn_master order by create_date desc ")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Chapter No.</strong></td>
<td><strong>HSN Code</strong></td>
<td><strong>HSN Description</strong></td>
<td><strong>CGST %</strong></td>
<td><strong>SGST %</strong></td>
<td><strong>IGST %</strong></td>
<td><strong>Status</strong></td>

</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['chapter_no']?></td>
<td align="left"><?=$row_loc['hsn_code']?></td>
<td align="left"><?=($row_loc['hsn_description'])?></td>
<td align="left"><?=$row_loc['cgst']?></td>
<td align="left"><?=$row_loc['sgst']?></td>
<td align="left"><?=$row_loc['igst']?></td>
<td align="left"><?=$row_loc['status']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>