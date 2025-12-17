<?php
require_once("../config/dbconnect.php");

$sql="select payment_date,doc_no from payment_receive";
$res_sql=mysqli_query($link1,$sql);
while($row=mysqli_fetch_array($res_sql)){

mysqli_query($link1,"update party_ledger set doc_date='".$row['payment_date']."' where doc_no='".$row['doc_no']."'");	
	
}

?>