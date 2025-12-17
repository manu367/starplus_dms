<?php
require_once("../config/dbconnect.php");
session_start();
echo "<script src='../js/bootstrap-select.min.js'></script>";
echo "~";
echo "<link rel='stylesheet' href='../css/bootstrap-select.min.css'>";
echo "~";
switch($_REQUEST["action"]){
 case getProdDropDown:
 $indx=$_REQUEST['value1'];
?>
<select class='selectpicker form-control' data-live-search='true' name='prod_code[<?=$indx?>]' id='prod_code[<?=$indx?>]' required><option value=''>--None--</option><?php $model_query="select model from model_master where status='Active'";$check1=mysql_query($model_query);while($br = mysql_fetch_array($check1)){?><option data-tokens='<?php echo $br['model'];?>' value='<?php echo $br['model'];?>'><?php echo $br['model'];?></option><?php }?></select>~<?=$indx?>
<?php
break;
}
?>
