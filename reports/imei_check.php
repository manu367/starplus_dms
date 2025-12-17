<?php 
print("\n");
print("\n");

?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>IMEI1</strong></td>
<td><strong>IMEI2</strong></td>
<td><strong>Invoice No</strong></td>
<td><strong>Sale Date</strong></td>
<td><strong>Owner</strong></td>
<td><strong>Model</strong></td>

</tr>
<?php
$i=1;
$imei=$_REQUEST['imei'];
$ex=explode(',', $imei);
for($j=0; $j<count($ex); $j++)
{
	
$sql=mysqli_query($link1,"Select a.* , b.sale_date from billing_imei_data a, billing_master b where imei1='".$ex[$j]."' or imei2='".$ex[$j]."' and a.doc_no=b.challan_no")or die("er1".mysqli_error($link1));
if(mysqli_num_rows($sql)>0)
			{	
			$row=mysqli_fetch_assoc($sql);
			 $chek_owner=mysqli_fetch_assoc(mysqli_query($link1,"select a.owner_code,a.doc_no, a.prod_code, b.sale_date from billing_imei_data a, billing_master b where (a.imei1='".$row['imei1']."' or a.imei2='".$row['imei2']."') and a.doc_no=b.challan_no order by a.id desc limit 0,1"));
				 
				  $chek_rcvin=mysqli_fetch_assoc(mysqli_query($link1,"select status from billing_master where challan_no='".$chek_owner['doc_no']."'"));
				  if($chek_rcvin['status']==""){
				  $chek_rcvin2=mysqli_fetch_assoc(mysqli_query($link1,"select status from opening_stock_master where doc_no='".$chek_owner['doc_no']."'"));
					  $checkstatus=$chek_rcvin2['status'];
				  }else{
					 $checkstatus=$chek_rcvin['status'];
				  }
			      if($chek_owner['owner_code']==$row['owner_code'] && $checkstatus=="Received" &&  $row['type']!='RETAIL' ){
					  $locdet=explode("~",getLocationDetails($chek_owner['owner_code'],"name,city,state,id_type",$link1));
	              	  $proddet=str_replace("~",",",getProductDetails($chek_owner['prod_code'],"productname,productcolor",$link1));
				  }
				  else
				  {
					  $locdet=explode("~",getLocationDetails($chek_owner['owner_code'],"name,city,state,id_type",$link1));
	              	  $proddet=str_replace("~",",",getProductDetails($chek_owner['prod_code'],"productname,productcolor",$link1));
				  }
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row['imei1']?></td>
<td align="left"><?=$row['imei2']?></td>
<td align="left"><?=$chek_owner['doc_no']?></td>
<td align="left"><?=dt_format($chek_owner['sale_date']);?></td>
<td align="left"><?=$locdet[0]?></td>
<td align="left"><?=$proddet?></td>

</tr>
<?php
	
}
else
{?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$ex[$j]?></td>
<td align="left"></td>
<td align="left"></td>
<td align="left"></td>
<td colspan="2" align="center"><span style="color:#FF0000;"><?php echo "Record Not Found";?></span></td>


</tr>
<?php }$i+=1;	}
?>
</table>