<?php
session_start();
require_once("../config/config.php");
if($_REQUEST[status]=='Active' || $_REQUEST[status]=='active'){
$status='Deactive';
}else if($_REQUEST[status]=='Deactive'  || $_REQUEST[status]=='deactive'){
$status='Active';
}
$query="update  tax_master set status='$status' where id='".$_GET['a']."'";
$result=mysql_query($query) or die(mysql_error());
if($result){
header("Location:tax_master.php");
}
?>

