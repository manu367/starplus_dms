<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$state=base64_decode($_REQUEST['state']);
$cityname=base64_decode($_REQUEST['city_name']);

## selected cityname
if($cityname!=""){
	$cityy="city='".$cityname."'";
}else{
	$cityy="1";
}
## selected state
if($state!=""){
	$statename="state='".$state."'";
}else{
	$statename="1";
}
//////End filters value/////

$sql=mysqli_query($link1,"Select * from district_master where $cityy and $statename order by state,city")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>State</strong></td>
<td><strong>City Name</strong></td>
<td><strong>Status</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['state']?></td>
<td align="left"><?=$row_loc['city']?></td>
<td align="left"><?=$row_loc['status']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>