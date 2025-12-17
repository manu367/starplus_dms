<?php
require_once("../config/dbconnect.php");
require_once("../includes/globalvariables.php");
session_start();
//////////////////  save payment from SAP
if($_POST['paymentFromSAP']) {
	$claimno = base64_decode($_POST['clmid']);
	////// get claim details
    $sql = "SELECT * FROM claim_master WHERE claim_no='".$claimno."'";
    $res = mysqli_query($link1, $sql);
    $row = mysqli_fetch_array($res);
    ////// check payment is received or not
	if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM payment_receive WHERE against_ref_no='".$claimno."'"))==0){
		$res_po = mysqli_query($link1,"SELECT COUNT(id) AS no FROM payment_receive WHERE from_location='".$row['party_id']."'");
		$row_po = mysqli_fetch_array($res_po);
		$c_nos = $row_po['no']+1;
		$pad1 = str_pad($c_nos,3,"0",STR_PAD_LEFT);  
		$doc_no = "RECP/23/".$row['party_id']."/".$pad1;
		///// Insert Master Data
		$query1= "INSERT INTO payment_receive SET doc_no='".$doc_no."',against_ref_no='".$claimno."',from_location='".$row['party_id']."',to_location='CAHOUP001',amount='".$row['total_amount']."',rec_amount='".$row['total_amount']."',status='Approve', payment_mode='Transfer', bank_name='".$bank_name."', bank_branch='".$bank_branch."', dd_cheque_no='".$dd_ch_no."', dd_cheque_dt='".$dd_ch_dt."', receipt_no='".$rec_no."', transaction_id='".$trans_id."', remark='FROM SAP', payment_date='".$today."',entry_dt='".$today."',entry_time='".$currtime."',entry_by='".$_SESSION['userid']."',ip='".$ip."',address='',latitude='".$latlong[0]."',longitude='".$latlong[1]."',pjp_id=''";
		$result = mysqli_query($link1,$query1);
		
		echo "1~Payment is successfully received";
		
	}else{
		echo "0~Payment is already received";
	}
}
?>