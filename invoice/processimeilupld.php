<?php
require_once("../config/config.php");

///// after hitting the process button ///
if($_POST['upd']=="Process"){
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg="";
	$arr_srInDb = array();

    $invdet=mysqli_fetch_assoc(mysqli_query($link1,"select from_location,to_location,sale_date from billing_master where challan_no='".$_POST['invno']."'"));
   ////////////////////////Select imei details from temp table to insert data in billing imei data table//
	
	$res3=mysqli_query($link1,"select id, prod_code, imei1, imei2 from temp_imei_upload where inv_no='".$_POST['invno']."' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."' and file_id='".$_POST['fileid']."'");	
	while($row3=mysqli_fetch_array($res3)) {
		/// check imei is already bill or not
	
	   $res_imei=mysqli_query($link1,"select owner_code,import_date,prod_code from billing_imei_data where imei1='".$row3['imei1']."' order by id desc");
	   $checkimei=mysqli_fetch_assoc($res_imei);
	   ////// prod code check condition is applied on 28 feb 23 by shekhar
	   if($checkimei['owner_code']==$invdet['from_location'] && $checkimei["prod_code"]==$row3['prod_code']){
		  //////////////insert in billing imei data////////////////////////
	   $result=mysqli_query($link1,"insert into billing_imei_data  set from_location='".$invdet['from_location']."',to_location='".$invdet['to_location']."',owner_code='".$invdet['to_location']."',prod_code='".$row3['prod_code']."',doc_no='".$_POST['invno']."',imei1='".$row3['imei1']."',stock_type='".$row3['imei2']."',transaction_date='".$invdet["sale_date"]."',import_date='".$checkimei['import_date']."'");
		//// check if query is not executed
	   if (!$result) {
		   $flag = false;
		   $err_msg = "Error Code1:". mysqli_error($link1) . ".";
	   }else{
	   		////// update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 22 JULY 2022
			if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM serial_stock WHERE serial_no='".$row3['imei1']."'"))>0){
				$res_upd_ss = mysqli_query($link1,"UPDATE serial_stock SET location_code='".$invdet['to_location']."', prod_code='".$row3['prod_code']."', rem_qty='1', stock_type='".$row3['imei2']."', ref_no='".$_POST['invno']."', ref_date='".$invdet["sale_date"]."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."' WHERE serial_no='".$row3['imei1']."'");
				if (!$res_upd_ss) {
					$flag1 = false;
					$error_msg = "Error details4.1: " . mysqli_error($link1) . ".";
					$msg = "2";
				}
			}else{
				$res_inst_ss = mysqli_query($link1,"INSERT INTO serial_stock SET location_code='".$invdet['to_location']."', prod_code='".$row3['prod_code']."', serial_no='".$row3['imei1']."',inside_qty='1', rem_qty='1', stock_type='".$row3['imei2']."', ref_no='".$_POST['invno']."', ref_date='".$invdet["sale_date"]."',import_date='".$checkimei['import_date']."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."'");
				if (!$res_inst_ss) {
					$flag1 = false;
					$error_msg = "Error details4.2: " . mysqli_error($link1) . ".";
					$msg = "2";
				}
			}
			////// end of script update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 22 JULY 2022
	   }
	   }else{
		   $flag = false;
		   $arr_srInDb[] = $row3['imei1'];
		   $err_msg = "Error Code1: Serial is not available or duplicate in this file";
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
	$result=mysqli_query($link1,"UPDATE billing_master set po_no='IMEI_UPLOAD', status='Pending', dc_date='".$today."',dc_time='".$currtime."',file_name='".$_POST['fname']."',imei_attach='Y' where challan_no='".$_POST['invno']."'");
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code4:";
    }
	/// update status in data
	$resultd=mysqli_query($link1,"UPDATE billing_model_data SET file_name='".$_POST['fname']."',imei_attach='Y' where challan_no='".$_POST['invno']."' AND prod_cat!='C'");
	//// check if query is not executed
	if (!$resultd) {
	     $flag = false;
         $err_msg = "Error Code4d:";
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
        $msg = "SERIAL Nos. are successfully attached with ref. no. ".$_POST['invno'];
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed ".$err_msg.". Please try again.";
		$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_srInDb];
	} 
    mysqli_close($link1);
	///// move to parent page
    header("location:retailbillinglist.php?msg=".$msg."".$pagenav);
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
    header("location:retailbillinglist.php?msg=".$msg."".$pagenav);
    exit;
}
include("../includes/footer.php");
include("../includes/connection_close.php");
?>