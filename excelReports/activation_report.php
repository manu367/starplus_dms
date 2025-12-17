<?php 
///////////state////////////
if($_REQUEST['state']==''){
$state="";
}else{
$state="and sale_location like '$_REQUEST[state]'";
}
///////////////////////////
//////////operator/////////
if($_REQUEST['operator']==''){
$opr="";
}else{
$opr="and operator LIKE '$_REQUEST[operator]'";
}
///////////////////////////
//////////model/////////
if($_REQUEST['model']==''){
$mod="";
}else{
$mod="and model LIKE '$_REQUEST[model]'";
}
///////////////////////////
///////////keyword///////////
if($_REQUEST['srch']==''){
$srch="";
}else{
$srch=" and (imei1 like '%$_REQUEST[srch]%' or imei2 like '%$_REQUEST[srch]%' or mobile_no like '%$_REQUEST[srch]%')";
}
///////////////////////////
$sql_stat="(sale_date BETWEEN '$_REQUEST[from]' AND '$_REQUEST[to]') $state $opr $srch $mod";
$sql_model=mysqli_query($link1,"select * from tertiary_imei_sale_import where $sql_stat order by sale_date desc");
?>
<style type="text/css">
<!--
.Header {
	font-size: 10px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #FFFFFF;
	text-align:center;
	background-color:#339966;
}
.row1 {
	font-size: 10px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #330033;
	background-color:#F4FAFF;
}
.row2 {
	font-size: 10px;
	font-family: Verdana, Arial, Helvetica, sans-serif; 
	color:; 
	background-color:#FFFFFF;
}
-->
</style>
<div>
<table border="1" cellpadding="0" cellspacing="0">
  <tr class="Header">	
    <td>Sno</td>
    <td>Model</td>
    <td>IMEI1</td>
    <td>IMEI2</td>
    <td>Activation Date</td>
    <td>Location</td>
    <td>Mobile No.</td>
    <td>Operator</td>
    </tr>
     <?php  $i=1;
  while($row = mysqli_fetch_array($sql_model))
    {
		?>
     <tr align="left" >
    <td><?=$i?></td>
    <td><?=$row['model'];?></td>
    <td><?=$row['imei1'];?></td>
    <td><?=$row['imei2'];?></td>
    <td><?=$row['sale_date'];?></td>
    <td><?=$row['sale_location'];?></td>
    <td><?=$row['mobile_no'];?></td>
    <td><?=$row['operator'];?>    
    </tr>
 <?php
 $i++;
  }?>
</table>
</div>