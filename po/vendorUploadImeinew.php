<?php
require_once("../config/config.php");
require_once("../includes/serial_logic_function.php");
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
	$post_po = base64_decode($_POST['po_no']);
	$post_partcode = base64_decode($_POST['prodcode']);
    if ($_FILES["attchfile"]["name"]) {
        require_once "../includes/simplexlsx.class.php";
        $xlsx = new SimpleXLSX($_FILES['attchfile']['tmp_name']);
        list($cols) = $xlsx->dimension();
		///// pick perticular part details
		$vendor_data = mysqli_fetch_assoc(mysqli_query($link1,"SELECT qty FROM billing_model_data WHERE challan_no='".$post_po."' AND prod_code= '".$post_partcode."'"));
		///////////
		$vendor_master = mysqli_fetch_assoc(mysqli_query($link1,"SELECT from_location,to_location,challan_no,sale_date FROM billing_master WHERE challan_no='".$post_po."'"));
		//// check req_qty vs uploading qty<br>
		$total_count='0';
		$arr_invalid = array();
		$arr_invalsr = array();
		$total_count=count($xlsx->rows()); ///// count no. of rows in uploading sheet /////////////////////////////////////////// 
		if(intval($vendor_data['qty']) == ($total_count -1)){
			//// check serial no. length validation
			foreach( $xlsx->rows() as $k => $r) {
	 			if ($k == 0) continue; // skip first row 
	  			for( $i = 0; $i < count($k); $i++)
	  			{
		  			/// check excel row data
	      			if($r[0]=='' && $r[1]==''){
			  
		  			}
					else if($r[0]=="EOF"){
					   $eof="1";
					}else{
						///// check serial no. length
						if($r[0]){
							//// serial no. dynamically check from product master written by shekhar on 23 JAN 23
							$check_length = getAnyDetails($post_partcode,"serial_length","productcode","product_master",$link1);
							$serial_length = strFilter($r[0], $check_length, $check_length);
							//$serial_length = strFilter($r[0], 17, 17);
							if(!$serial_length){
								$arr_invalid[] = $r[0]; 
							}
							////// check serial no. validation with its product code & model code written by shekhar on 20 dec 2022
							$resp = getValidateSerialPartcode($r[0],$post_partcode,$link1);
							if($resp!="Y"){
								$arr_invalsr[] = $r[0]." -- ".$resp;
							}
						}
					}
				}
			}
			if($arr_invalid){
				//$msg = "Serial nos. not having 17 digits";
				$msg = "Serial nos. not having defined digits in product master";
				///// move to parent page
				$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_invalid];
				header("location:statusgrn_view.php?id=".$_POST['po_no']."".$pagenav);
				exit;
			}else if($arr_invalsr){
				$upd_cnt = 0;
				$msg = "Serial nos. validation failed";
				///// move to parent page
				$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_invalsr];
				header("location:statusgrn_view.php?id=".$_POST['po_no']."".$pagenav);
				exit;		
			}else{
				foreach( $xlsx->rows() as $k => $r) {
					if ($k == 0) continue; // skip first row 
					for( $i = 0; $i < count($k); $i++)
					{
						/// check excel row data
						if($r[0]=='' && $r[1]==''){
				  
						}
						else if($r[0]=="EOF"){
						   $eof="1";
						}else{
							////Make Variable for each element of excel//////		
							$imei1="".$r[0];
							$stocktype="".$r[1];
							$froml = $vendor_master['from_location'];
							$tol = $vendor_master['to_location'];
							$owner_no = $vendor_master['to_location'];
							$po_no = $vendor_master['challan_no'];
							//$flag = "Y";
							// check imei1 and imei2 is already uploaded  /////////////////////////////////////////////////
							$imeidata = mysqli_fetch_assoc(mysqli_query($link1,"SELECT id FROM billing_imei_data WHERE (imei1='".$imei1."')"));
							if ($imeidata['id'] != "") {
								$msg_imei = "Serial is Already Uploaded".$imei1;
								header("Location:statusgrn_view.php?id=".$_POST['po_no']."&msg=$msg_imei" . $pagenav);
								exit; 
							} 
							else {
								// check imei1 and imei2  at prod_code
								if ($post_partcode != "" && $imei1 != "" ) {	
									// insert imei into billing_imei_data and billing_imei
									$sql = "INSERT INTO billing_imei_data set from_location='" .$froml . "',to_location='" . $tol . "',owner_code='" .$owner_no . "',prod_code='" .$post_partcode. "',doc_no='" . $post_po . "',imei1= '" .$imei1."', imei2='" . $imei2 . "' , stock_type = '".$stocktype."', transaction_date='".$vendor_master["sale_date"]."', import_date='".$vendor_master["sale_date"]."' ";
									$result = mysqli_query($link1, $sql);                       
									//// check if query is not executed
									if (!$result) {
										$flag1 = FALSE;
										$msg = "2";
									}else{
										////// update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 01 JULY 2022
										if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM serial_stock WHERE serial_no='".$imei1."'"))>0){
											$res_upd_ss = mysqli_query($link1,"UPDATE serial_stock SET location_code='".$owner_no."', prod_code='".$post_partcode."', rem_qty='1', stock_type='".$stocktype."', ref_no='".$post_po."', ref_date='".$vendor_master["sale_date"]."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."' WHERE serial_no='".$imei1."'");
											if (!$res_upd_ss) {
												$flag1 = false;
												$error_msg = "Error details4.1: " . mysqli_error($link1) . ".";
											}
										}else{
											$res_inst_ss = mysqli_query($link1,"INSERT INTO serial_stock SET location_code='".$owner_no."', prod_code='".$post_partcode."', serial_no='".$imei1."',inside_qty='1', rem_qty='1', stock_type='".$stocktype."', ref_no='".$post_po."', ref_date='".$vendor_master["sale_date"]."',import_date='".$vendor_master["sale_date"]."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."'");
											if (!$res_inst_ss) {
												$flag1 = false;
												$error_msg = "Error details4.2: " . mysqli_error($link1) . ".";
											}
										}
										////// end of script update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 01 JULY 2022
										$msg='1';
										$f_name = $now . $_FILES["attchfile"]["name"];
										// insert into upload file data
										$result3  =  mysqli_query($link1, "update billing_model_data set file_name='" . $f_name . "',imei_attach='Y' , upload_date = '".$today."'  where challan_no='" . $post_po . "' and prod_code = '".$post_partcode. "'");				
										//// check if query is not executed
										if (!$result3) {
											$flag1 = false;
											$error_msg = "Error details4: " . mysqli_error($link1) . ".";
										}
									}
									///////start script of insert in battery charging table if product is battery written by shekhar on 21 june 2022
									$check_btr = mysqli_fetch_assoc(mysqli_query($link1,"SELECT a.productcategory,b.cat_name FROM product_master a, product_cat_master b WHERE a.productcategory=b.catid AND a.productcode='".$post_partcode."'"));
									if(strtoupper($check_btr["cat_name"])=="BATTERY"){
										$res_btrchg = mysqli_query($link1,"INSERT INTO battery_charging_status SET doc_no='".$post_po."', prod_code ='".$post_partcode."', serial_no ='".$imei1."', status='NOT CHARGE', import_date='".$vendor_master["sale_date"]."', entry_date='".$datetime."', entry_by='".$_SESSION["userid"]."'");
										//// check if query is not executed
										if (!$res_btrchg) {
											$flag1 = FALSE;
											$error_msg = "Error details4.1: " . mysqli_error($link1) . ".";
										}
									}
									/////// end script of insert in battery charging table if product is battery written by shekhar on 21 june 2022
								} else {
									$msg = '2';
								}
							} //// end of else 
						}  /////////////  end of else /////////////////////////////////////
					}  /////////  end of for loop //////////////////////////////
				}   //////////////  end of foreach loop //////////////////////////////////////////////////////////////	
			}
		} ///////////////   end of main if condition /////////////////////////////////////////////////////////////////////
		else {
			$msg_imei = "Uploading Qty does no match with Requested Qty";
			header("Location:statusgrn_view.php?id=".$_POST['po_no']."&msg=$msg_imei" . $pagenav);
			exit; 
		}
		if ($msg == '1') {
			///// check query are successfully executed
			if ($flag1) {
				// move file into folder
				move_uploaded_file($_FILES["attchfile"]["tmp_name"], "../upload/bill_upload/" . $now . $_FILES["attchfile"]["name"]);
				$f_name = $now . $_FILES["attchfile"]["name"];
				// insert into upload file data
				$status= mysqli_fetch_array(mysqli_query($link1, "select count(id) as vid from billing_model_data where imei_attach = 'Y'  and challan_no = '".$post_po."' "));
				$po_no = mysqli_fetch_array(mysqli_query($link1, "select count(id) as vid from billing_model_data where  challan_no = '".$post_po."' "));
				if($status['vid']  == $po_no['vid'])
				{
					$result4  =  mysqli_query($link1, "update billing_master set file_name='" . $f_name . "',imei_attach='Y'  where challan_no='" . $post_po . "'");
						  
				}					
				mysqli_commit($link1);
				$msg = "File is uploaded Successfully!";
				header("Location:statusgrn_view.php?id=".$_POST['po_no']."&msg=$msg" . $pagenav);
				exit;
			} else {
				mysqli_rollback($link1);
				$msg = "File is not uploaded Properly.Serial no. already exit!";
			}
		} elseif ($msg == '2') {
			$msg = "File is not uploaded Properly.Serial no. not exit!";
			header("Location:statusgrn_view.php?id=".$_POST['po_no']."&msg=$msg" . $pagenav);
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
                    <h2 align="center"><i class="fa fa-upload"></i> Upload<?=$imeitag?><br>
					<?php echo base64_decode($_GET['po_no']);  ?></h2><div style="display:inline-block;float:right"><a href="../templates/Vendor_serial.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div><br/>
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
                                            <input type="hidden" name="po_no" value="<?=$_GET['po_no']?>">
											<input type="hidden" name="prodcode" value="<?=$_GET['prodcode']?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" align="center">
                                         <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if ($_POST['Submit'] == 'Update') { ?>disabled<?php } ?>>
                                   &nbsp;&nbsp;&nbsp;
                                    <input title="Back" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href = 'statusgrn_view.php?id=<?=$_REQUEST['po_no']?><?=$pagenav?>'" type="button">
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