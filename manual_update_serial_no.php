<?php
//require_once("config/dbconnect.php");
require_once("includes/common_function.php");
require_once("includes/globalvariables.php");
//////////////////////////
$i=0;
$res1 = mysqli_query($link1,"SELECT * FROM `update_serial_data` WHERE `update_by` LIKE 'Backend' AND `update_date` = '2023-03-25%'");
while($row1 = mysqli_fetch_assoc($res1)){
	////// get serial no. latest details
	$res_qry = mysqli_query($link1,"SELECT * FROM billing_imei_data WHERE imei1 ='".$row1['serial_no']."' ORDER BY id DESC");
	if(mysqli_num_rows($res_qry)>0){
		$row_qry = mysqli_fetch_assoc($res_qry);
		$partcode = $row_qry["prod_code"];
		$fromloc = $row_qry["from_location"];
		$toloc = $row_qry["to_location"];
		$owncode = $row_qry["owner_code"];
		$stocktype = $row_qry["stock_type"];
		$impdate = $row_qry["import_date"];
		$old_val = "";
		////// check change type
		if($row1['change_type']=="PARTCODE"){
			$partcode = $row1['new_value'];
			$old_val = $row_qry["prod_code"];
		}
		else if($row1['change_type']=="OWNER"){
			$owncode = $row1['new_value'];
			$old_val = $row_qry["owner_code"];
		}
		else if($row1['change_type']=="STOCK TYPE"){
			$stocktype = $row1['new_value'];
			$old_val = $row_qry["stock_type"];
		}else{
			//// nothing to do
		}		
		$refid = $row1["id"];
		$refno = "SERDATAUPD-".$refid;
		//// insert into billing imei data
		$res_qry1 = mysqli_query($link1,"INSERT INTO billing_imei_data SET from_location='".$fromloc."', to_location='".$toloc."', owner_code='".$owncode."', prod_code='".$partcode."', doc_no = '".$refno."', imei1='".$row1['serial_no']."', stock_type = '".$stocktype."', transaction_date='".$today."', import_date='".$impdate."'");
		if (!$res_qry1) {
			$flag = false;
			$err_msg = "ER1 : ".mysqli_error($link1).".";
		}
		///// update serial no.
		if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM serial_stock WHERE serial_no='".$row1['serial_no']."'"))>0){
			$res_upd_ss = mysqli_query($link1,"UPDATE serial_stock SET location_code='".$owncode."', prod_code='".$partcode."', rem_qty='1', stock_type='".$stocktype."', ref_no='".$refno."', ref_date='".$today."', update_by='Backend', update_date='".$datetime."' WHERE serial_no='".$row1['serial_no']."'");
			if (!$res_upd_ss) {
				$flag1 = false;
				$err_msg = "ER3 : ".mysqli_error($link1).".";
			}
		}else{
			$res_inst_ss = mysqli_query($link1,"INSERT INTO serial_stock SET location_code='".$owncode."', prod_code='".$partcode."', serial_no='".$row1['serial_no']."',inside_qty='1', rem_qty='1', stock_type='".$stocktype."', ref_no='".$refno."', ref_date='".$today."',import_date='".$impdate."', update_by='Backend', update_date='".$datetime."'");
			if (!$res_inst_ss) {
				$flag1 = false;
				$err_msg = "ER4 : ".mysqli_error($link1).".";
			}
		}								  
		////// insert in activity table////
		$flag=dailyActivity("Backend",$refno,"SERIAL DATA","UPDATE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		$i++;
	}
}
echo $i." records updated";
?>