<?php
require_once("../config/config.php");

///// after hitting the process button ///
if($_POST['upd']=="Process"){
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg="";
    $invdet=mysqli_fetch_assoc(mysqli_query($link1,"SELECT from_location,to_location,sale_date FROM stock_movement_master WHERE doc_no='".$_POST['invno']."'"));
   ////////////////////////Select imei details from temp table to insert data in billing imei data table//
	$res3=mysqli_query($link1,"SELECT id, prod_code, imei1, imei2 FROM temp_imei_upload WHERE inv_no='".$_POST['invno']."' AND update_by='".$_SESSION['userid']."' AND browserid='".$browserid."' AND file_id='".$_POST['fileid']."'");	
	while($row3=mysqli_fetch_array($res3)) {
		/// check imei is already bill or not
	   $res_imei=mysqli_query($link1,"SELECT owner_code,import_date FROM billing_imei_data WHERE imei1='".$row3['imei1']."' ORDER BY id DESC");
	   $checkimei=mysqli_fetch_assoc($res_imei);
	   if(mysqli_num_rows($res_imei)==0 || $checkimei['owner_code']==$invdet['from_location']){						
		  //////////////insert in billing imei data////////////////////////
	   $result=mysqli_query($link1,"INSERT INTO billing_imei_data SET from_location='".$invdet['from_location']."',to_location='".$invdet['to_location']."',owner_code='".$_POST['invno']."',prod_code='".$row3['prod_code']."',doc_no='".$_POST['invno']."',imei1='".$row3['imei1']."',stock_type='".$row3['imei2']."',transaction_date='".$invdet["sale_date"]."',import_date='".$checkimei['import_date']."'");
		//// check if query is not executed
	   if (!$result) {
		   $flag = false;
		   $err_msg = "Error Code1:". mysqli_error($link1) . ".";
	   }
	   }else{
		   $flag = false;
		   $err_msg = "Error Code1: Serial is not available";
	   }
		//////////////update flag of inserted data///////////////////////
	   $result=mysqli_query($link1,"update temp_imei_upload set flag='Y' where id='".$row3['id']."'");
	   //// check if query is not executed
	   if (!$result) {
		   $flag = false;
		   $err_msg = "Error Code2:";
	   }
	}
	/// update status in matser
	$result=mysqli_query($link1,"UPDATE stock_movement_master SET file_name='".$_POST['fname']."', serial_attach='Y' WHERE doc_no='".$_POST['invno']."'");
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code4:";
    }
	$result=mysqli_query($link1,"delete from temp_imei_upload where flag='' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."'");
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code3:";
    }
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$_POST['invno'],"SERIAL ATTACH","UPLOAD",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Serial nos. are successfully attached with ref. no. ".$_POST['invno'];
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed ".$err_msg.". Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
    header("location:stock_move_list.php?msg=".$msg."".$pagenav);
    exit;
}
if($_POST['cancel']=='Cancel'){
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg="";
	$result=mysqli_query($link1,"delete from temp_imei_upload where flag='' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."'");
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code4:";
	}
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "All Excel Uploaded Data has been deleted.";
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed ".$err_msg.". Please try again.";
	}
	mysqli_close($link1);
	///// move to parent page
    header("location:stock_move_list.php?msg=".$msg."".$pagenav);
    exit;
}
include("../includes/footer.php");
include("../includes/connection_close.php");
?>