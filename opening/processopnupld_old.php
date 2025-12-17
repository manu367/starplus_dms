<?php
require_once("../config/config.php");

///// after hitting the process button ///
if($_POST['upd']=="Process"){
	//// Make System generated Document no.//////
	$res_po=mysqli_query($link1,"select max(temp_no) as no from opening_stock_master where location_code='".$_POST['locationcode']."'");
	$row_po=mysqli_fetch_array($res_po);
	$c_nos=$row_po['no']+1;
	$doc_no=$_POST['locationcode']."OPS".$c_nos;
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg="";
	///// get parent location details
	$parentloc=getLocationDetails($_POST['locationcode'],"state,id_type",$link1);
	$parentlocdet=explode("~",$parentloc);
    //// pick data from temp table
	$basecost=0.00;
    $res1=mysqli_query($link1,"select count(prod_code) as qty , prod_code, location_code from temp_opn_upload where location_code='".$_POST['locationcode']."' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."' and file_id='".$_POST['fileid']."' and flag='' group by prod_code");
	while($row1=mysqli_fetch_assoc($res1)){
	    // checking row value of product and qty should not be blank
		if($row1['prod_code']!='' && $row1['qty']!='' && $row1['qty']!=0) {
			//// getting product price
			$prodprice=explode("~",getProductPrice($row1['prod_code'],$parentlocdet[2],$parentlocdet[1],$link1));
			/////////// insert data
			$value=$prodprice[0]*$row1['qty'];
		   $query2="insert into opening_stock_data set doc_no='".$doc_no."', prod_code='".$row1['prod_code']."', okqty='".$row1['qty']."',damageqty='0',missingqty='0', price='".$prodprice[0]."', value='".$value."', mrp='".$prodprice[1]."',uom='PCS'";
		   $result = mysqli_query($link1, $query2);
		   $basecost+=$value;
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $err_msg = "Error Code1:";
           }
		   ///// update stock in inventory //
		  if(mysqli_num_rows(mysqli_query($link1,"select partcode from stock_status where partcode='".$row1['prod_code']."' and asc_code='".$_POST['locationcode']."'"))>0){
			 ///if product is exist in inventory then update its qty 
			 $result=mysqli_query($link1,"update stock_status set qty=qty+'".$row1['qty']."',okqty=okqty+'".$row1['qty']."',updatedate='".$datetime."' where partcode='".$row1['prod_code']."' and asc_code='".$_POST['locationcode']."'");
		  }
		  else{
			 //// if product is not exist then add in inventory
			 $result=mysqli_query($link1,"insert into stock_status set asc_code='".$_POST['locationcode']."',partcode='".$row1['prod_code']."',qty=qty+'".$row1['qty']."',okqty='".$row1['qty']."',uom='PCS',updatedate='".$datetime."'");
		  }
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $err_msg = "Error Code2:";
           }
		   ///// update stock ledger table
		   $flag=stockLedger($doc_no,$_POST['opendate'],$row1['prod_code'],$_POST['locationcode'],$_POST['locationcode'],$_POST['locationcode'],"IN","OK","Opening Stock",$row1['qty'],$prodprice[0],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   //// update flag in temp table
		   ////////////////////////Select imei details from temp table to insert data in billing imei data table//
		    $res3=mysqli_query($link1,"select id, location_code, prod_code, imei1, imei2 from temp_opn_upload where location_code='".$row1['location_code']."' and prod_code='".$row1['prod_code']."' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."' and file_id='".$_POST['fileid']."'");	
		    while($row3=mysqli_fetch_array($res3)) {
				/// check imei is already bill or not
			   $res_imei=mysqli_query($link1,"select owner_code from billing_imei_data where imei1='".$row3['imei1']."' order by id desc");
			   $checkimei=mysqli_fetch_assoc($res_imei);
			   if(mysqli_num_rows($res_imei)==0){						
				  //////////////insert in billing imei data////////////////////////
    		   $result=mysqli_query($link1,"insert into billing_imei_data  set from_location='".$row3['location_code']."',to_location='".$row3['location_code']."',owner_code='".$row3['location_code']."',prod_code='".$row3['prod_code']."' ,doc_no='".$doc_no."',imei1='".$row3['imei1']."',stock_type='".$row3['imei2']."'");
				//// check if query is not executed
			   if (!$result) {
				   $flag = false;
				   $err_msg = "Error Code3:". mysqli_error($link1) . ".";
			   }
			   }else{
				   $flag = false;
                   $err_msg = "Error Code3.1: Some Serial Nos. are already in Database";
			   }
				//////////////update flag of inserted data///////////////////////
			   $result=mysqli_query($link1,"update temp_opn_upload set flag='Y' where id='".$row3['id']."'");
			   //// check if query is not executed
			   if (!$result) {
				   $flag = false;
				   $err_msg = "Error Code4:";
			   }
			}
		}// close if loop of checking row value of product and qty should not be blank
		
	}
	///// Insert Master Data
	 $query1= "INSERT INTO opening_stock_master set location_code='".$_POST['locationcode']."',doc_no='".$doc_no."',temp_no='".$c_nos."',ref_no='".$refno."',requested_date='".$_POST['opendate']."',entry_date='".$today."',entry_time='".$currtime."',status='Received',stock_value='".$basecost."',create_by='".$_SESSION['userid']."',ip='".$ip."',remark='".$remark."',file_name='".$_POST['fname']."',imei_attach='Y'";
	$result = mysqli_query($link1,$query1);
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code5:";
    }
	$result=mysqli_query($link1,"delete from temp_opn_upload where flag='' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."'");
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code6:";
    }
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$doc_no,"OPS","ADD",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Opening Stock Challan is successfully entered with ref. no.".$doc_no;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed ".$err_msg.". Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
    header("location:uploadOpening.php?msg=".$msg."".$pagenav);
    exit;
}
if($_POST['cancel']=='Cancel'){
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg="";
	$result=mysqli_query($link1,"delete from temp_opn_upload where flag='' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."'");
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code7:";
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
    header("location:uploadOpening.php?msg=".$msg."".$pagenav);
    exit;
}
include("../includes/footer.php");
include("../includes/connection_close.php");
?>