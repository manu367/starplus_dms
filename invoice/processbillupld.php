<?php
require_once("../config/config.php");

///// after hitting the process button ///
if($_POST['upd']=="Process"){
	//// Make System generated Invoice no.//////
	$res_cnt=mysqli_query($link1,"select inv_str,inv_counter from document_counter where location_code='".$_POST['billfrom']."'");
	if(mysqli_num_rows($res_cnt)){
	$row_cnt=mysqli_fetch_array($res_cnt);
	$invcnt=$row_cnt['inv_counter']+1;
	$pad=str_pad($invcnt,4,0,STR_PAD_LEFT);
	$invno=$row_cnt['inv_str'].$pad;
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg="";
	///// get parent location details
	$parentloc=getLocationDetails($_POST['billfrom'],"addrs,disp_addrs,state,id_type",$link1);
	$parentlocdet=explode("~",$parentloc);
	///// get child location details
	$childloc=getLocationDetails($_POST['billto'],"addrs,disp_addrs,state,id_type",$link1);
	$childlocdet=explode("~",$childloc);
	 $childlocdet[2];
	if($delivery_address){$deli_addrs=$delivery_address;}else{$deli_addrs=$childlocdet[1];}
	/// update invoice counter /////
	$result=mysqli_query($link1,"update document_counter set inv_counter=inv_counter+1,update_by='".$_SESSION['userid']."',updatedate='".$datetime."' where location_code='".$_POST['billfrom']."'");
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code2:";
    }	
	//// pick details from temp table
	$basecost=0.00;
    $taxcost=0.00;
    $discountcost=0.00;
    $totalcost=0.00;
    $res1=mysqli_query($link1,"select imei1 , prod_code, bill_from from temp_bill_upload where bill_from='".$_POST['billfrom']."' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."' and file_id='".$_POST['fileid']."' and flag='' group by prod_code");
	while($row1=mysqli_fetch_assoc($res1)){
		// checking row value of product and qty should not be blank
		/*$getstk=getCurrentStock($_POST['billfrom'],$row1['prod_code'],"okqty",$link1);
		//// check stock should be available ////
		if($getstk < $row1['qty']){ 
		   $flag = false;
           $err_msg = "Error Code3: Stock is not available";
		}
	    else{}*/
	    // checking row value of product and qty should not be blank
		if($row1['prod_code']!='' && $row1['imei1']!='' && $row1['imei1']!=0) {
			//// getting product price
			$prodprice=explode("~",getProductPrice($row1['prod_code'],$childlocdet[3],$childlocdet[2],$link1));
		///////////////////// fetch hsn code ///////////////////////////
		 $hsn = mysqli_query($link1, "select hsn_code  from product_master where productcode = '".$row1['prod_code']."' ");
		  $hsncode= mysqli_fetch_array($hsn);
		
		//////////////////////////////////////////////////////////////////////////////
			/////////// insert data
			if ($childlocdet[2] == $parentlocdet[2] )
			{
			 	$tax=mysqli_query($link1, "select cgst, sgst from tax_hsn_master where hsn_code = '".$hsncode[hsn_code]."' ");
			  	$taxcal= mysqli_fetch_array($tax);
			 	$value=$prodprice[0]*$row1['imei1'];
			 	$cgst_amt = $value*$taxcal[cgst]/100;
			 	$sgst_amt = $value*$taxcal[sgst]/100;
				$taxamt =$sgst_amt +$cgst_amt;
				$totalval = $value+$taxamt ;
			}
			else{
				$tax =mysqli_query($link1, "select igst from tax_hsn_master where hsn_code = '".$hsncode[hsn_code]."' ");
			 	$taxcal= mysqli_fetch_array($tax);
			  	$value=$prodprice[0]*$row1['imei1'];
			 	$igst_amt = $value*$taxcal[igst]/100;
				$taxamt = $igst_amt;
				$totalval = $value+$taxamt ;
			}
		   $query2="insert into billing_model_data set from_location='".$_POST['billfrom']."', prod_code='".$row1['prod_code']."', qty='".$row1['imei1']."', okqty='".$row1['imei1']."',mrp='".$prodprice[1]."', price='".$prodprice[0]."', hold_price='".$prodprice[0]."', value='".$value."',cgst_per='$taxcal[cgst]', sgst_per='$taxcal[sgst]', igst_per='$taxcal[igst]', sgst_amt='".$sgst_amt."', cgst_amt='".$cgst_amt."', igst_amt='".$igst_amt."' , tax_amt='".$taxamt."', totalvalue='".$totalval."',challan_no='".$invno."' ,sale_date='".$_POST['billdate']."',entry_date='".$today."'";
		   $result = mysqli_query($link1, $query2);
		   $basecost+=$value;
		   $taxcost+=$taxamt;
		   $totalcost+=$totalval;
		    $totaligstamt+=$igst_amt;
		    $totalcgstamt+=$cgst_amt;
			$totalsgstamt+=$sgst_amt;
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $err_msg = "Error Code4:";
           }
		   //// update stock of from loaction
		  $result=mysqli_query($link1, "update stock_status set okqty=okqty-'".$row1['imei1']."',updatedate='".$datetime."' where asc_code='".$_POST['billfrom']."' and sub_location='".$_POST['billfrom']."' and partcode='".$row1['prod_code']."'");
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $err_msg = "Error Code5:";
           }
		   ///// update stock ledger table
		   $flag=stockLedger($invno,$_POST['billdate'],$row1['prod_code'],$_POST['billfrom'],$_POST['billto'],$_POST['billfrom'],"OUT","OK","Corporate Invoice",$row1['imei1'],$prodprice[0],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   //// update flag in temp table
		   ////////////////////////Select imei details from temp table to insert data in billing imei data table//
		}// close if loop of checking row value of product and qty should not be blank
		
	}
	///// Insert Master Data
	$splitcompltetax=explode("~",$_POST['taxD']);
	$caltax=number_format(($basecost-$_POST['discountD'])*($splitcompltetax[0]/100),'2','.','');
	$totalcost=$basecost-$_POST['discountD']+$caltax;
	if($_POST['discountD']!='' && $_POST['discountD']!=0.00 && $_POST['discountD']!=0){$disc_type="TD";}else{ $disc_type="NONE";}
	if($_POST['taxD']){$tx_type="TT";}else{ $tx_type="NONE";}
	 $query1= "INSERT INTO billing_master set from_location='".$_POST['billfrom']."', to_location='".$_POST['billto']."', challan_no='".$invno."',po_no='BILL_UPLOAD', sale_date='".$_POST['billdate']."', entry_date='".$today."', entry_time='".$currtime."', entry_by='".$_SESSION['userid']."', status='Pending', type='CORPORATE', document_type='INVOICE',basic_cost='".$basecost."',discount_amt='". $taxcost."',tax_cost='". $taxcost."',total_cost='".$totalcost."',bill_from='".$_POST['billfrom']."',bill_topty='".$_POST['billto']."',from_addrs='".$parentlocdet[0]."',disp_addrs='".$parentlocdet[1]."',to_addrs='".$childlocdet[0]."',deliv_addrs='".$deli_addrs."',billing_rmk='".$remark."',file_name='".$_POST['fname']."' ,total_sgst_amt='".$totalsgstamt."',  total_cgst_amt='".$totalcgstamt."', total_igst_amt='".$totaligstamt."' ";
	$result = mysqli_query($link1,$query1);
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code1:";
    }
	$result=mysqli_query($link1,"delete from temp_bill_upload where flag='' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."'");
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code1.1:";
    }

	//// update cr bal of child location
	$result=mysqli_query($link1,"update current_cr_status set cr_abl=cr_abl-'".$totalcost."',total_cr_limit=total_cr_limit-'".$totalcost."', last_updated='".$datetime."' where parent_code='".$_POST['billfrom']."' and asc_code='".$_POST['billto']."'");
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code8:";
	}
	////// maintain party ledger////
	$flag=partyLedger($_POST['billfrom'],$_POST['billto'],$invno,$_POST['billdate'],$today,$currtime,$_SESSION['userid'],"CORPORATE INVOICE",$totalcost,"DR",$link1,$flag);
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$invno,"CORPORATE INVOICE","ADD",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Invoice is successfully created with ref. no. ".$invno;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed ".$err_msg.". Please try again.";
	} 
    mysqli_close($link1);
	}
	else{
		$msg = "Request could not be processed invoice series not found. Please try again.";
	}
	///// move to parent page
    header("location:uploadBilling.php?msg=".$msg."".$pagenav);
    exit;
}
if($_POST['cancel']=='Cancel'){
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg="";
	$result=mysqli_query($link1,"delete from temp_bill_upload where flag='' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."'");
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code10:";
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
   header("location:uploadBilling.php?msg=".$msg."".$pagenav);
    exit;
}
include("../includes/footer.php");
include("../includes/connection_close.php");
?>