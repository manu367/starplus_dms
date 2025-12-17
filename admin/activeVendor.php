<?php
require_once("../config/config.php");
if($_REQUEST[status]=='Active' || $_REQUEST[status]=='active'){
$status='Deactive';
}else if($_REQUEST[status]=='Deactive'  || $_REQUEST[status]=='deactive'){
$status='Active';
}
$query="update  vendor_master set status='$status' where sno='".$_GET['a']."'";
$result=mysqli_query($link1,$query) or die(mysqli_error($link1));
if($result){
header("Location:vendor_master.php");
}
?>

