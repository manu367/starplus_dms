<?php
require_once("../config/config.php");
////// if we hit process button
if ($_POST) {
	if ($_POST['upd'] == 'Save') {
		////// 
		$get_refKey = base64_decode($_POST["refToken"]);
		/////// get data
		$res_paym = mysqli_query($link1, "SELECT id FROM payment_receive WHERE status = 'Approve' AND id IN ('".$get_refKey."') AND collection_flag = '' ORDER BY payment_date");
		$i = 0;
        while ($row_paym = mysqli_fetch_assoc($res_paym)){
			$postVerifyAcc = $_POST["verify_acc".$row_paym["id"]];
			//echo "<br/>";
			$acc_det = explode(" - ",$postVerifyAcc);
			$postVerifyAmt = $_POST["verify_amt".$row_paym["id"]];
			$postVerifyRmk = $_POST["remark".$row_paym["id"]];
			///// check all mandatory data should be post 
			if($acc_det[0]!="" && $postVerifyAmt!=""){
				///// update payment details
				mysqli_query($link1,"UPDATE payment_receive SET collection_flag='Y', collection_accid='".$acc_det[1]."', collection_account='".$acc_det[0]."', collection_amt='".$postVerifyAmt."', collection_rmk='".$postVerifyRmk."', collection_date = '".$datetime."' WHERE id='".$row_paym["id"]."'");
				$i++;
			}
			
		}
		if($i>0){
			$msg="Collection is successfully verified.";
		}else{
			$msg="You have not selected any data.";
		}
		///// move to parent page
		header("Location:collection_sheet.php?msg=".$msg."".$pagenav);
		exit;
	}
}
?>