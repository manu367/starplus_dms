<?php
session_start();
require_once("../config/config.php");
$getid=base64_decode($_REQUEST[id]);

if($_REQUEST[status]=='Active' || $_REQUEST[status]=='active'){
$status='Deactive';
}else if($_REQUEST[status]=='Deactive'  || $_REQUEST[status]=='deactive'){
$status='Active';
}
echo "update  fos_master set status='$status' where id='".$getid."'";
$query="update  fos_master set status='$status' where id='".$getid."'";
$result=mysqli_query($link1,$query) or die(mysqli_error($link1));
if($result){
header("Location:fos_master.php");
}
?>
