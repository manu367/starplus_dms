<?php
session_start();
session_destroy();
$msg='2';
header("Location:index.php?msg=".$msg);
?>