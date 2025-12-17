<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables

$status=base64_decode($_REQUEST['status']);
$bom_model=base64_decode($_REQUEST['bom_model']);

if($status!=""){
	$status="status='".$status."'";
}else{
	$status="1";
}

if($bom_model!=""){
	$bom_model="bom_modelcode='".$bom_model."'";
}else{
	$bom_model="1";
}
//////End filters value/////

// echo "Select * from combo_master where $status and $bom_model";
// exit;
$sql=mysqli_query($link1,"Select * from combo_master where $status and $bom_model")or die("er1".mysqli_error($link1));
?>

<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td align="center"><strong>Combo Model Name</strong></td>
<td align="center"><strong>Combo Model Code</strong></td>
<td align="center"><strong>Combo Model HSN</strong></td>
<td align="center"><strong>Combo Partcode</strong></td>
<td align="center"><strong>Combo Partname</strong></td>
<td align="center"><strong>Combo Product Quantity</strong></td>
<td align="center"><strong>Status</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['bom_modelname']?></td>
<td align="center"><?=$row_loc['bom_modelcode']?></td>
<td align="center"><?=$row_loc['bom_hsn']?></td>
<td align="center"><?=$row_loc['bom_partcode']?></td>
<td align="left"><?=getAnyDetails($row_loc['bom_partcode'],"productname","productcode" ,"product_master" ,$link1);?></td>
<td align="center"><?=$row_loc['bom_qty']?></td>
<?php
if($row_loc['status']=='1'){
    $status="Active";
}
else
{
    $status="Deactive";
}
?>
<td align="center"><?=$status?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>
