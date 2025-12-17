<?php
//require_once("config/dbconnect.php");
///// save google api resp
if ($_POST['gapi']) {
    $res_api = mysqli_query($link1,"INSERT INTO google_api_response SET userid='".$_POST['userid']."', emp_id='".$_POST['empid']."', entry_date='".$_POST["entrydate"]."', api_name='DISTANCE', respdata1='".$_POST['resp1']."', respdata2='".$_POST['resp2']."', respdata3='".$_POST['resp3']."', resdata='', entry_by='CRON'");
}
?>