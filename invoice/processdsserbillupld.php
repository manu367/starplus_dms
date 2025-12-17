<?php
require_once("../config/config.php");
///// after hitting the process button ///
if($_POST['upd']=="Process"){
	/////start transaction variables
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg="";
	$dupli_inv = array();
	$remark = $_POST['rmk'];
	///////// get party wise and invoice wise record
	$res1 = mysqli_query($link1,"SELECT bill_from, bill_to, invoiceno, invoicedate FROM temp_bill_upload WHERE bill_from='".$_POST['billfrom']."' AND update_by='".$_SESSION['userid']."' AND browserid='".$browserid."' AND file_id='".$_POST['fileid']."' AND flag='' GROUP BY bill_to,invoiceno");
	while($row1=mysqli_fetch_assoc($res1)){
		////// check invoice no. is exist or not
		if(mysqli_num_rows(mysqli_query($link1,"SELECT challan_no FROM billing_master WHERE challan_no='".$row1['invoiceno']."'"))==0){
			///// get parent location details
			$parentloc=getLocationDetails($_POST['billfrom'],"addrs,disp_addrs,state,id_type",$link1);
			$parentlocdet=explode("~",$parentloc);
			///// get child location details
			$childloc=getLocationDetails($row1['bill_to'],"addrs,disp_addrs,state,id_type",$link1);
			$childlocdet=explode("~",$childloc);
			if($delivery_address){$deli_addrs=$delivery_address;}else{$deli_addrs=$childlocdet[1];}	
			//// pick details from temp table
			$basecost=0.00;
			$taxcost=0.00;
			$discountcost=0.00;
			$totalcost=0.00;
			$res2 = mysqli_query($link1,"SELECT prod_code, COUNT(imei1) AS qty FROM temp_bill_upload WHERE bill_from='".$_POST['billfrom']."' AND bill_to='".$row1['bill_to']."' AND invoiceno='".$row1['invoiceno']."' AND update_by='".$_SESSION['userid']."' AND browserid='".$browserid."' AND file_id='".$_POST['fileid']."' AND flag='' GROUP BY prod_code");
			while($row2 = mysqli_fetch_assoc($res2)){
				// checking row value of product and qty should not be blank
				if($row2['prod_code']!='' && $row1['bill_to']!='' && $row2['qty']!='' && $row2['qty']!=0) {
					$prodprice=explode("~",getProductPrice($row2['prod_code'],$childlocdet[3],$childlocdet[2],$link1));
					///////////////////// fetch hsn code ///////////////////////////
					$hsn = mysqli_query($link1, "SELECT hsn_code FROM product_master WHERE productcode = '".$row2['prod_code']."'");
					$hsncode= mysqli_fetch_array($hsn);
					///////////
					if ($childlocdet[2] == $parentlocdet[2] )
					{
						$tax = mysqli_query($link1, "SELECT cgst, sgst FROM tax_hsn_master WHERE hsn_code = '".$hsncode['hsn_code']."' ");
						$taxcal = mysqli_fetch_array($tax);
						$value = $prodprice[0]*$row2['qty'];
						$cgst_amt = $value*$taxcal['cgst']/100;
						$sgst_amt = $value*$taxcal['sgst']/100;
						$taxamt = $sgst_amt +$cgst_amt;
						$totalval = $value+$taxamt ;
					}
					else{
						$tax = mysqli_query($link1, "SELECT igst FROM tax_hsn_master WHERE hsn_code = '".$hsncode['hsn_code']."' ");
						$taxcal = mysqli_fetch_array($tax);
						$value = $prodprice[0]*$row2['qty'];
						$igst_amt = $value*$taxcal['igst']/100;
						$taxamt = $igst_amt;
						$totalval = $value+$taxamt ;
					}
					$query2 = "INSERT INTO billing_model_data SET from_location='".$_POST['billfrom']."', prod_code='".$row2['prod_code']."', qty='".$row2['qty']."', okqty='".$row2['qty']."', mrp='".$prodprice[1]."', price='".$prodprice[0]."', hold_price='".$prodprice[0]."', value='".$value."',cgst_per='".$taxcal['cgst']."', sgst_per='".$taxcal['sgst']."', igst_per='".$taxcal['igst']."', sgst_amt='".$sgst_amt."', cgst_amt='".$cgst_amt."', igst_amt='".$igst_amt."', tax_amt='".$taxamt."', totalvalue='".$totalval."',challan_no='".$row1['invoiceno']."', sale_date='".$row1['invoicedate']."',entry_date='".$today."',imei_attach='Y',file_name='UPLOAD'";
					$result2 = mysqli_query($link1, $query2);
					//// check if query is not executed
					if (!$result2) {
						$flag = false;
						$err_msg = "Error Code2: ".mysqli_error($link1);
					}
					/////insert serial no.
					$res3 = mysqli_query($link1,"SELECT id, bill_from, bill_to, prod_code, imei1, invoiceno, invoicedate FROM temp_bill_upload WHERE bill_from='".$_POST['billfrom']."' AND bill_to='".$row1['bill_to']."' AND invoiceno='".$row1['invoiceno']."' AND update_by='".$_SESSION['userid']."' AND browserid='".$browserid."' AND file_id='".$_POST['fileid']."' AND flag=''");
					while($row3 = mysqli_fetch_assoc($res3)){
						$result4 =mysqli_query($link1,"INSERT INTO billing_imei_data SET from_location='".$row3['bill_from']."',to_location='".$row3['bill_to']."',owner_code='', prod_code='".$row3['prod_code']."',doc_no='".$row3['invoiceno']."',imei1='".$row3['imei1']."',stock_type='".$row3['imei2']."',transaction_date='".$row3["invoicedate"]."',import_date='".$row3['invoicedate']."'");
						//// check if query is not executed
						if (!$result4) {
							$flag = false;
							$err_msg = "Error Code4:". mysqli_error($link1) . ".";
						}
						$result5 = mysqli_query($link1,"UPDATE temp_bill_upload SET flag='Y' WHERE id='".$row3['id']."'");
						//// check if query is not executed
						if (!$result5) {
						   $flag = false;
						   $err_msg = "Error Code5:". mysqli_error($link1) . ".";
						}
					}
					$basecost+=$value;
					$taxcost+=$taxamt;
					$totalcost+=$totalval;
					$totaligstamt+=$igst_amt;
					$totalcgstamt+=$cgst_amt;
					$totalsgstamt+=$sgst_amt;
				}// close if loop of checking row value of product and qty should not be blank
			}///close while loop
			///// Insert Master Data
			$splitcompltetax = explode("~",$_POST['taxD']);
			$caltax = number_format(($basecost-$_POST['discountD'])*($splitcompltetax[0]/100),'2','.','');
			$totalcost = $basecost-$_POST['discountD']+$caltax;
			if($_POST['discountD']!='' && $_POST['discountD']!=0.00 && $_POST['discountD']!=0){$disc_type="TD";}else{ $disc_type="NONE";}
			if($_POST['taxD']){$tx_type="TT";}else{ $tx_type="NONE";}
			$query1= "INSERT INTO billing_master SET from_location='".$_POST['billfrom']."', to_location='".$row1['bill_to']."', challan_no='".$row1['invoiceno']."', po_no='BILL_UPLOAD', sale_date='".$row1['invoicedate']."', entry_date='".$today."', entry_time='".$currtime."', entry_by='".$_SESSION['userid']."', status='Pending', type='CORPORATE', document_type='INVOICE', basic_cost='".$basecost."', discount_amt='". $taxcost."', tax_cost='". $taxcost."',total_cost='".$totalcost."',bill_from='".$_POST['billfrom']."',bill_topty='".$row1['bill_to']."', from_addrs='".$parentlocdet[0]."',disp_addrs='".$parentlocdet[1]."',to_addrs='".$childlocdet[0]."',deliv_addrs='".$deli_addrs."',billing_rmk='".$remark."',file_name='".$_POST['fname']."' ,total_sgst_amt='".$totalsgstamt."',  total_cgst_amt='".$totalcgstamt."', total_igst_amt='".$totaligstamt."',imei_attach='Y'";
			$result1 = mysqli_query($link1,$query1);
			//// check if query is not executed
			if (!$result1) {
				$flag = false;
				$err_msg = "Error Code1: ".mysqli_error($link1);
			}
			////// maintain party ledger////
			$flag=partyLedger($_POST['billfrom'],$row1['bill_to'],$row1['invoiceno'],$row1['invoicedate'],$today,$currtime,$_SESSION['userid'],"CORPORATE INVOICE",$totalcost,"DR",$link1,$flag);
			////// insert in activity table////
			$flag = dailyActivity($_SESSION['userid'],$row1['invoiceno'],"CORPORATE INVOICE","ADD",$ip,$link1,$flag);
		}else{
			$dupli_inv[] = $row1['invoiceno'];
			$flag = false;
		 	$err_msg = "Invoices are already in system.";
		}
	}
	$result3 = mysqli_query($link1,"DELETE FROM temp_bill_upload WHERE flag='Y' AND update_by='".$_SESSION['userid']."' AND browserid='".$browserid."'");
	//// check if query is not executed
	if (!$result3) {
		 $flag = false;
		 $err_msg = "Error Code3: ".mysqli_error($link1);
	}
	///// check both master and data query are successfully executed
	if ($flag) {
		mysqli_commit($link1);
		$msg = "Invoices are successfully uploaded";
	} else {
		mysqli_rollback($link1);
		if($dupli_inv){
			$msg = "Request could not be processed ".$err_msg." ".implode(",",$dupli_inv);
		}else{
			$msg = "Request could not be processed ".$err_msg.". Please try again.";
		}
	} 
	mysqli_close($link1);
	///// move to parent page
	header("location:uploadBillingSerialDS.php?msg=".$msg."".$pagenav);
	exit;
}
if($_POST['cancel']=='Cancel'){
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg="";
	$result=mysqli_query($link1,"DELETE FROM temp_bill_upload WHERE flag='' AND update_by='".$_SESSION['userid']."' AND browserid='".$browserid."'");
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code01: ".mysqli_error($link1);
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
   	header("location:uploadBillingSerialDS.php?msg=".$msg."".$pagenav);
    exit;
}
include("../includes/footer.php");
include("../includes/connection_close.php");
?>