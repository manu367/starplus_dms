<?php
require_once("../config/config.php");
function unixstamp($excelDateTime) {
    $d = floor($excelDateTime); // seconds since 1900
    $t = $excelDateTime - $d;
    return ($d > 0) ? ( $d - 25569 ) * 86400 + $t * 86400 : $t * 86400;
}

//////////////// after hitting upload button
@extract($_POST);
if ($_POST['Submit'] == "Upload") {
    //// initialization
    $flag1 = TRUE;
    mysqli_autocommit($link1, false);
    $error_msg = "";

    if ($_FILES["attchfile"]["name"]) {
        require_once "../includes/simplexlsx.class.php";
        $xlsx = new SimpleXLSX($_FILES['attchfile']['tmp_name']);
        list($cols) = $xlsx->dimension();
		///// pick perticular part details
	$vendor_data = mysqli_fetch_assoc(mysqli_query($link1,"select req_qty  from vendor_order_data where po_no='".$_POST['po_no']."' and 
	prod_code= '".$_POST['prodcode']."'  "));
	///////////
	$vendor_master = mysqli_fetch_assoc(mysqli_query($link1,"select * from vendor_order_master where po_no='".$_POST['po_no']."' "));
		//// check req_qty vs uploading qty<br>
		$total_count='0';     
		$total_count=count($xlsx->rows());
		 $vendor_data['req_qty'] ;
		///// count no. of rows in uploading sheet /////////////////////////////////////////// 
	if($vendor_data['req_qty'] == ($total_count -1)){
	///////////////////////////////////////// IMEI Duplicate ent
	              $imei_dup1=array();
				   $imei_dup2=array(); 
						foreach( $xlsx->rows() as $k => $r) {
						 if ($k == 0) continue; // skip first row 
						  for( $i = 0; $i < count($k); $i++)
						  {
							  /// check excel row data
							  if($r[0]=='' && $r[1]=='' && $r[2]==''){
							  }
							  else if($r[0]=="EOF"){
								   $eof="1";
							  }else{
								 ////Make Variable for each element of excel//////		
										$imei_dup1[]="".$r[0];
										$imei_dup2[]="".$r[1];
									}
								}/////for loop closed
					
					}/////////////froeach loop cloded
				$imei_count1=count($imei_dup1);
				$imei_count2=count($imei_dup2);
				$imei_count3=count(array_unique($imei_dup1));
				$imei_count4=count(array_unique($imei_dup2));
				
				
				if($imei_count1==$imei_count3 && $imei_count2==$imei_count4)
				{ //check duplicate imei
///////////////////////////////////////// IMEI Duplicate  end
						foreach( $xlsx->rows() as $k => $r) {
							 if ($k == 0) continue; // skip first row 
							  for( $i = 0; $i < count($k); $i++)
							  {
								  /// check excel row data
								  if($r[0]=='' && $r[1]=='' && $r[2]==''){
									  
								  }
								  else if($r[0]=="EOF"){
									   $eof="1";
								  }else{
								  
									 ////Make Variable for each element of excel//////		
											$imei1="".$r[0];
											$imei2="".$r[1];
											$stocktype="".$r[2];
											$froml = $vendor_master['po_to'];
											$tol = $vendor_master['po_from'];
											$owner_no = $vendor_master['po_from'];
											$po_no = $vendor_master['po_no'];
											$flag = "Y";
						 
						 
											
											// check imei1 and imei2 	 is already uploaded  /////////////////////////////////////////////////
									
										//$imeidata = mysqli_fetch_assoc(mysqli_query($link1,"select id from billing_imei_data where (imei1='".$imei1."' or imei2='".$imei2."' or imei1='".$imei2."' or imei2='".$imei1."') and (owner_code ='".$froml."' and prod_code ='" .$_POST['prodcode'] . "')"));
										
										$imeidata = mysqli_fetch_assoc(mysqli_query($link1,"select id from billing_imei_data where (imei1='".$imei1."' or imei2='".$imei2."' or imei1='".$imei2."' or imei2='".$imei1."') "));
										
											if ($imeidata['id'] != "") {
											   $msg_imei = "IMEI is Already Uploaded";
											   header("Location:localPurchaseDetails.php?op=edit&id='".base64_encode($_POST['po_no'])."'&msg=$msg_imei" . $pagenav);
											   exit; 
											} 
											
											else {
											// check imei1 and imei2  at prod_code
						
										  if ($_POST['prodcode'] != "" && $imei1 != "" && $imei2 != "") {		                 	               
													// insert imei into billing_imei_data and billing_imei
													$sql = "INSERT INTO billing_imei_data set from_location='" .$tol . "',to_location='" . $froml . "',owner_code='" .$froml . "',prod_code='" .$_POST['prodcode']. "',doc_no='" . $_POST['po_no'] . "',imei1=" . $imei1 . ",imei2='" . $imei2 . "',flag='" . $flag . "' , stock_type = '".$stocktype."' ";
										
											   $result = mysqli_query($link1, $sql);                       
													//// check if query is not executed
													if (!$result) {
														$flag1 = FALSE;
														$msg = "2";
													}else{
														$msg='1';
															 // move file into folder
										move_uploaded_file($_FILES["attchfile"]["tmp_name"], "../upload/bill_upload/" . $now . $_FILES["attchfile"]["name"]);
										$f_name = $now . $_FILES["attchfile"]["name"];
														  // insert into upload file data
									$result3  =  mysqli_query($link1, "update vendor_order_data set file_name='" . $f_name . "',imei_attach='Y' , upload_date = '".$today."'  where po_no='" . $_POST['po_no'] . "' and prod_code = '".$_POST['prodcode']. "'  ");
										
											//// check if query is not executed
									if (!$result3) {
										$flag = false;
										$error_msg = "Error details4: " . mysqli_error($link1) . ".";
									}
													}
											
											} else {
												$msg = '2';
											}
											
								//////////////////////////////////////////////////////////////////////////////////////////////////////
										} //// end of else 
										
									}  /////////////  end of else /////////////////////////////////////
								}  /////////  end of for loop //////////////////////////////
						}   //////////////  end of foreach loop //////////////////////////////////////////////////////////////	
				
				}//////check Ducplicate imei
				else {
				        $msg_imei = "Dupliate IMEI in Excel Sheet";
						header("Location:localPurchaseDetails.php?op=edit&id='".base64_encode($_POST['po_no'])."'&msg=$msg_imei" . $pagenav);
						exit; 
				}
} ///////////////   end of main if condition /////////////////////////////////////////////////////////////////////
	else {
 		$msg_imei = "Uploading Qty does no match with Requested Qty";
         header("Location:localPurchaseDetails.php?op=edit&id='".base64_encode($_POST['po_no'])."'&msg=$msg_imei" . $pagenav);
         exit; 

		}

        if ($msg == '1') {
            ///// check query are successfully executed
            if ($flag1) {
                // move file into folder
                move_uploaded_file($_FILES["attchfile"]["tmp_name"], "../upload/bill_upload/" . $now . $_FILES["attchfile"]["name"]);
                $f_name = $now . $_FILES["attchfile"]["name"];
                // insert into upload file data
          $status= mysqli_fetch_array(mysqli_query($link1, "select count(id) as vid from vendor_order_data where imei_attach = 'Y'  and po_no = '".$_POST['po_no']."' "));
		  $po_no = mysqli_fetch_array(mysqli_query($link1, "select count(id) as vid from vendor_order_data where  po_no = '".$_POST['po_no']."' "));
		  if($status['vid']  == $po_no['vid'])
		  {
		    $result4  =  mysqli_query($link1, "update vendor_order_master set file_name='" . $f_name . "',imei_attach='Y' , upload_date = '".$today."'  where po_no='" . $_POST['po_no'] . "'   ");
					  
		  }					
                mysqli_commit($link1);
                $msg = "File is uploaded Successfully!";
             header("Location:localPurchaseDetails.php?op=edit&id='".base64_encode($_POST['po_no'])."'&msg=$msg" . $pagenav);
                exit;
            } else {
                mysqli_rollback($link1);
                $msg = "File is not uploaded Properly.IMEI already exit!";
            }
        } elseif ($msg == '2') {
            $msg = "File is not uploaded Properly.IMEI not exit!";
      header("Location:localPurchaseDetails.php?op=edit&id='".base64_encode($_POST['po_no'])."'&msg=$msg" . $pagenav);
           exit;
        } 
         mysqli_close($link1);
}
}
			
		
       

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= siteTitle ?></title>
        <script src="../js/jquery.min.js"></script>
        <link href="../css/font-awesome.min.css" rel="stylesheet">
        <link href="../css/abc.css" rel="stylesheet">
        <script src="../js/bootstrap.min.js"></script>
        <link href="../css/abc2.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/bootstrap-select.min.css">
        <script src="../js/bootstrap-select.min.js"></script>
        <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
        <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#myTable').dataTable();
            });
            $(document).ready(function() {
                $("#frm1").validate();
            });
            // When the document is ready
            $(document).ready(function() {
                $('#billdate').datepicker({
                    format: "yyyy-mm-dd",
                    //startDate: "<?= $row['sale_date'] ?>",
                    //endDate: "<?= $today ?>",
                    todayHighlight: true,
                    autoclose: true
                });
            });
        </script>
        <script src="../js/frmvalidate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/common_js.js"></script>
        <link rel="stylesheet" href="../css/datepicker.css">
        <script src="../js/jquery-1.10.1.min.js"></script>
        <script src="../js/bootstrap-datepicker.js"></script>
        <script src="../js/fileupload.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row content">
                <?php
                include("../includes/leftnav2.php");
                ?>
                <div class="col-sm-9 tab-pane fade in active" id="home">
                    <h2 align="center"><i class="fa fa-upload"></i>Local Purchase Upload Item Wise IMEI <br>
					Partcode-(<?php echo $_GET['prodcode'];?>)</h2><div style="display:inline-block;float:right"><a href="../templates/Vendor_imei.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div><br/>
                    <div class="form-group"  id="page-wrap" style="margin-left:10px;">
                        <?php if ($_REQUEST['msg']) { ?><br>
                            <h4 align="center" style="color:#FF0000"><?= $_REQUEST['msg'] ?></h4>
                        <?php } ?>    
                        <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
                            <div class="form-group">
                                <div class="col-md-12"><label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <label class="input-group-btn">
                                                <span class="btn btn-primary">
                                                    Browse&hellip; <input type="file" name="attchfile" class="form-control required" required style="display:none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                                </span>
                                            </label>
                                            <input type="text" class="form-control" name="billfile"  id="billfile" readonly>
                                            <input type="hidden" name="po_no" value="<?= $_GET['po_no'] ?>">
											<input type="hidden" name="prodcode" value="<?= $_GET['prodcode'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" align="center">
                                    <input title="Back" class="btn btn-primary" value="Back" onClick="window.location.href = 'localPurchaseDetails.php?op=edit&id=<?=base64_encode($_GET['po_no'])?><?= $pagenav ?>'" type="button">&nbsp;&nbsp;&nbsp;
                                    <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Upload" title="" <?php if ($_POST['Submit'] == 'Update') { ?>disabled<?php } ?>>
                                </div>
                            </div> 
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include("../includes/footer.php");
        include("../includes/connection_close.php");
        ?>
    </body>
</html>