<?php
session_start();
session_destroy();
$msg='4';
header("Location:../index.php?msg=".$msg);
exit;
?>