<?php
require_once("../config/config.php");

///// after hitting the process button ///
if($_POST['upd']=="Process"){
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg="";
	/// pick invoice one by one
	$resinv=mysqli_query($link1,"select bill_from,invoiceno,invoicedate from temp_bill_upload where bill_from='".$_POST['billfrom']."' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."' and file_id='".$_POST['fileid']."' and flag='' and invoiceno!='' group by invoiceno,invoicedate");
	while($rowinv=mysqli_fetch_assoc($resinv)){
	//// Make System generated Invoice no.//////
	                             
	$res_cnt=mysqli_query($link1,"select inv_str,inv_counter from document_counter where location_code='".$rowinv['bill_from']."'");
	if(mysqli_num_rows($res_cnt)){
	$row_cnt=mysqli_fetch_array($res_cnt);
	$invcnt=$row_cnt['inv_counter']+1;
	$pad=str_pad($invcnt,4,0,STR_PAD_LEFT);
	$invno=$row_cnt['inv_str'].$pad;
	///// get parent location details
	$parentloc=getLocationDetails($_POST['billfrom'],"addrs,disp_addrs,state,id_type",$link1);
	$parentlocdet=explode("~",$parentloc);
	///// get child location details
	$childloc=getLocationDetails($_POST['billto'],"addrs,disp_addrs,state,id_type",$link1);
	$childlocdet=explode("~",$childloc);
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
	$totalcgst=0.00;
	$totalsgst=0.00;
	$totaligst=0.00;
    $res1=mysqli_query($link1,"select count(prod_code) as qty , prod_code, bill_from, price  from temp_bill_upload where bill_from='".$_POST['billfrom']."' and invoiceno='".$rowinv['invoiceno']."' and invoicedate='".$rowinv['invoicedate']."' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."' and file_id='".$_POST['fileid']."' and flag='' group by prod_code");
	while($row1=mysqli_fetch_assoc($res1)){
		// checking row value of product and qty should not be blank
		//$getstk=getCurrentStock($_POST['billfrom'],$row1['prod_code'],"okqty",$link1);
		//// check stock should be available ////
		/*if($getstk < $row1['qty']){ 
		   $flag = false;
           $err_msg = "Error Code3: Stock is not available";
		}
	    else{}*/
	    // checking row value of product and qty should not be blank
		if($row1['prod_code']!='' && $row1['qty']!='' && $row1['qty']!=0) {
			$tax_per=explode("~", $taxType[$k]);
			//// getting product price
			//$prodprice=explode("~",getProductPrice($row1['prod_code'],$childlocdet[3],$childlocdet[2],$link1));
			############# Check State for billing
			
			$from_state=mysqli_fetch_array(mysqli_query($link1,"select state from asc_master where asc_code='".$_POST['billfrom']."'"));
			
			$to_state=mysqli_fetch_array(mysqli_query($link1,"select state from asc_master where asc_code='".$_POST['billto']."'"));
			########################################
			$hsn_code = mysqli_fetch_assoc(mysqli_query($link1, "select hsn_code from product_master where productcode='" . $row1['prod_code'] . "'"));
			$tax_hsn=mysqli_fetch_array(mysqli_query($link1,"select cgst,sgst,igst from tax_hsn_master where hsn_code='".$hsn_code['hsn_code']."'"));
####### Calculate Item COst ###########
$value=$row1['price']*$row1['qty'];
######################################
////////////////////////////////////////////////
######## Tax validation with state ###############
if($from_state['state']==$to_state['state']){
$cgst_per=$tax_hsn['sgst'];
$sgst_per=$tax_hsn['cgst'];
$igst_per='0.00';
$cgst_amt = ($value*$cgst_per)/100;
$sgst_amt = ($value*$sgst_per)/100;
$igst_amt ='0.00';
$totalval=($value+$cgst_amt+$sgst_amt);
}
else{
$cgst_per='0.00';
$sgst_per='0.00';
$igst_per=$tax_hsn['igst'];
$igst_amt = ($value*$igst_per)/100;
$cgst_amt ='0.00';
$sgst_amt ='0.00';
$totalval=($value+$igst_amt);
}

			/////////// insert data
		    $query2="insert into billing_model_data set from_location='".$_POST['billfrom']."', prod_code='".$row1['prod_code']."', qty='".$row1['qty']."', okqty='".$row1['qty']."',mrp='".$prodprice[1]."', price='".$row1['price']."', hold_price='".$row1['price']."', value='".$value."',sgst_per='".$sgst_per."', sgst_amt='".$sgst_amt."',cgst_per='".$cgst_per."', cgst_amt='".$cgst_amt."',igst_per='".$igst_per."', igst_amt='".$igst_amt."',discount='".$discount."', totalvalue='".$totalval."',challan_no='".$invno."' ,sale_date='".$_POST['billdate']."',entry_date='".$today."'";
		   $result = mysqli_query($link1, $query2);
		   $basecost+=$value;
		   $totalcgst+=$cgst_amt;
		   $totalsgst+=$sgst_amt;
		   $totaligst+=$igst_amt;
		   $discountcost+=$discount;
		   $totalcost+=$totalval;
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $err_msg = "Error Code4:";
           }
		   //// update stock of from loaction
		   $result=mysqli_query($link1, "update stock_status set okqty=okqty-'".$row1['qty']."',updatedate='".$datetime."' where asc_code='".$_POST['billfrom']."' and partcode='".$row1['prod_code']."'");
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $err_msg = "Error Code5:";
           }
		   ///// update stock ledger table
		   $flag=stockLedger($invno,$_POST['billdate'],$row1['prod_code'],$_POST['billfrom'],$_POST['billto'],$_POST['billfrom'],"OUT","OK","Corporate Invoice",$row1['qty'],$row1['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   //// update flag in temp table
		   ////////////////////////Select imei details from temp table to insert data in billing imei data table//
		    $res3=mysqli_query($link1,"select id, bill_from, bill_to, prod_code, imei1, imei2 from temp_bill_upload where bill_from='".$row1['bill_from']."' and invoiceno='".$rowinv['invoiceno']."' and invoicedate='".$rowinv['invoicedate']."' and prod_code='".$row1['prod_code']."' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."' and file_id='".$_POST['fileid']."'");	
		    while($row3=mysqli_fetch_array($res3)) {						
	           /// check imei is already bill or not
			  
			   $res_imei=mysqli_query($link1,"select owner_code from billing_imei_data where imei1='".$row3['imei1']."' and  prod_code='".$row3['prod_code']."' order by id desc");
			   $checkimei=mysqli_fetch_assoc($res_imei);
			   if($checkimei['owner_code']==$row3['bill_from']){		
				  //////////////insert in billing imei data////////////////////////
			   $result=mysqli_query($link1,"insert into billing_imei_data  set from_location='".$row3['bill_from']."',to_location='".$row3['bill_to']."',owner_code='".$row3['bill_to']."',prod_code='".$row3['prod_code']."' ,doc_no='".$invno."',imei1='".$row3['imei1']."',imei2='".$row3['imei2']."'");
				//// check if query is not executed
			   if (!$result) {
				   $flag = false;
				   $err_msg = "Error Code6:". mysqli_error($link1) . ".";
			   }
			   }else{
				   $flag = false;
                   $err_msg = "Error Code6.1: IMEI is not available";
			   }
				//////////////update flag of inserted data///////////////////////
			   $result=mysqli_query($link1,"update temp_bill_upload set flag='Y' where id='".$row3['id']."'");
			   //// check if query is not executed
			   if (!$result) {
				   $flag = false;
				   $err_msg = "Error Code7:";
			   }
			}
		}// close if loop of checking row value of product and qty should not be blank
		
	}
	///// Insert Master Data
	//$splitcompltetax=explode("~",$_POST['taxD']);
	//$caltax=number_format(($basecost-$_POST['discountD'])*($splitcompltetax[0]/100),'2','.','');
	//$totalcost=$basecost-$_POST['discountD']+$caltax;
	if($_POST['discountD']!='' && $_POST['discountD']!=0.00 && $_POST['discountD']!=0){$disc_type="TD";}else{ $disc_type="NONE";}
	if($_POST['taxD']){$tx_type="TT";}else{ $tx_type="NONE";}
	 $query1= "INSERT INTO billing_master set from_location='".$_POST['billfrom']."', to_location='".$_POST['billto']."', challan_no='".$invno."',po_no='BILL_UPLOAD', sale_date='".$_POST['billdate']."',ref_no='".$rowinv['invoiceno']."',ref_date='".$rowinv['invoicedate']."', entry_date='".$today."', entry_time='".$currtime."', entry_by='".$_SESSION['userid']."', status='Dispatched', type='CORPORATE', document_type='INVOICE', discountfor='".$disc_type."', taxfor='".$tx_type."',basic_cost='".$basecost."',discount_amt='".$_POST['discountD']."',total_cgst_amt='".$totalcgst."',total_sgst_amt='".$totalsgst."',total_igst_amt='".$totaligst."',total_cost='".$totalcost."',tax_type='".$splitcompltetax[1]."',tax_header='".$splitcompltetax[2]."',tax='".$splitcompltetax[0]."',bill_from='".$_POST['billfrom']."',bill_topty='".$_POST['billto']."',from_addrs='".$parentlocdet[0]."',disp_addrs='".$parentlocdet[1]."',to_addrs='".$childlocdet[0]."',deliv_addrs='".$deli_addrs."',billing_rmk='".$remark."',file_name='".$_POST['fname']."',dc_date='".$today."',dc_time='".$currtime."',disp_rmk='BILL_UPLOAD',imei_attach='Y'";
	$result = mysqli_query($link1,$query1);
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code1:";
    }
	$result=mysqli_query($link1,"delete from temp_bill_upload where flag='' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."' and invoiceno='".$rowinv['invoiceno']."' and invoicedate='".$rowinv['invoicedate']."'");
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
	}
	else{
		$msg = "Request could not be processed invoice series not found. Please try again.";
	}
	}
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Invoices are successfully created";
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed ".$err_msg.". Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
    header("location:srdBillUpd.php?msg=".$msg."".$pagenav);
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
    header("location:srdBillUpd.php?msg=".$msg."".$pagenav);
    exit;
}
include("../includes/footer.php");
include("../includes/connection_close.php");
?>