<?php
session_start();
session_destroy();
$msg='3';
header("Location:index.php?msg=".$msg);
?>