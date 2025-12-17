<?php 
print("\n");
print("\n");
$res_state = mysqli_query($link1,"SELECT zone,state,statecode FROM state_master WHERE 1 ORDER BY state")or die("er1".mysqli_error($link1));
?>
<table width="50%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td width="5%"><strong>S.No.</strong></td>
<td  width="25%"><strong>State Name</strong></td>
<td width="10%"><strong>State GST Code</strong></td>
<td width="10%"><strong>Entry Date</strong></td>
</tr>
<?php
$i=1;
while($row_state = mysqli_fetch_array($res_state)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_state['state']?></td>
<td align="left"><?=$row_state['statecode']?></td>
<td align="left"><?=$row_state['zone']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>