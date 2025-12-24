<?php
require_once("../config/config.php");

$keyword = $_REQUEST['keyword'];

$sql = mysqli_query($link1,     "
  SELECT DISTINCT imei1 
  FROM billing_imei_data 
  WHERE imei1 LIKE '%$keyword%' 
     OR imei2 LIKE '%$keyword%' 
  LIMIT 10
");

if (mysqli_num_rows($sql) > 0) {
    while ($row = mysqli_fetch_assoc($sql)) {
        echo '<a href="javascript:void(0)" class="list-group-item serial-item">'
            . htmlspecialchars($row['imei1']) .
            '</a>';
    }
} else {
    echo '<div class="list-group-item text-danger">No record found</div>';
}
?>
