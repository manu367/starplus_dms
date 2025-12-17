<?php
require_once("../config/config.php");
require_once("../includes/serial_logic_function.php");
$invoice = base64_decode($_REQUEST['id']);

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

    if($_FILES["attchfile"]["name"]) {
        require_once "../includes/simplexlsx.class.php";
        $xlsx = new SimpleXLSX($_FILES['attchfile']['tmp_name']);
        $billing_data = mysqli_fetch_assoc(mysqli_query($link1, "SELECT po_from,po_to,po_no,entry_date,receive_date FROM `vendor_order_master` WHERE po_no='" . $_POST['challan_no'] . "'"));
        list($cols) = $xlsx->dimension();
		
			///////////////////////////////////////// IMEI Duplicate ent
	              $imei_dup1=array();
				  $arr_invalid = array();
				  $arr_invalsr = array();
				  $arr_part = array();
				   //$imei_dup2=array(); 
						foreach( $xlsx->rows() as $k => $r) {
						 if ($k == 0 || $k == 1) continue; // skip first row 
						  for( $i = 0; $i < count($k); $i++)
						  {
							  /// check excel row data
							  if($r[0]=='' && $r[1]=='' && $r[2]==''){
							  }
							  else if($r[0]=="EOF"){
								   $eof="1";
							  }else{
								 ////Make Variable for each element of excel//////		
								 	
										$imei_dup1[]="".$r[1];
										//$imei_dup2[]="".$r[2];
										///// check serial no. length
										if($r[1]){
											//// serial no. dynamically check from product master written by shekhar on 23 JAN 23
											$check_length = getAnyDetails($r[0],"serial_length","productcode","product_master",$link1);
											//$serial_length = strFilter($r[1], 17, 17);
											$serial_length = strFilter($r[1], $check_length, $check_length);
											if(!$serial_length){
												$arr_invalid[] = $r[1]; 
											}
											////// check serial no. validation with its product code & model code written by shekhar on 20 dec 2022
											$resp = getValidateSerialPartcode($r[1],$r[0],$link1);
											if($resp!="Y"){
												$arr_invalsr[] = $r[1]." -- ".$resp;
											}
											$arr_part[$r[0]] +=1;
										}
									}
								}/////for loop closed
					
					}/////////////froeach loop cloded
					if($arr_invalid){
						$upd_cnt = 0;
						//$msg = "Serial nos. not having 17 digits";
						$msg = "Serial nos. not having defined digits in product master";
						///// move to parent page
						$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_invalid];
						header("location:localPurchaseList.php?".$pagenav);
						exit;
					}else if($arr_invalsr){
			$upd_cnt = 0;
			$msg = "Serial nos. validation failed";
			///// move to parent page
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_invalsr];
			header("location:localPurchaseList.php?".$pagenav);
			exit;		
		}else{
				$imei_count1=count($imei_dup1);
				//$imei_count2=count($imei_dup2);
				$imei_count3=count(array_unique($imei_dup1));
				//$imei_count4=count(array_unique($imei_dup2));
				if($imei_count1==$imei_count3)
				{
					///// check if prod qty should be matched with data
					$res_chkqty = mysqli_query($link1,"SELECT prod_code,SUM(qty) AS qty FROM vendor_order_data WHERE po_no='".$_POST['challan_no']."' AND prod_cat!='C' GROUP BY prod_code");
					while($row_chkqty = mysqli_fetch_assoc($res_chkqty)){
						if($row_chkqty["qty"]!=$arr_part[$row_chkqty["prod_code"]]){
							$msg = "Uploading Serial No. qty does not match with document qty for product .".$row_chkqty["prod_code"];
							///// move to parent page
							$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg];
							header("location:localPurchaseList.php?".$pagenav);
							exit;
						}
					}
        			foreach ($xlsx->rows() as $k => $r) {
            if ($k == 0 || $k == 1)
                continue; // skip first row 
            for ($i = 0; $i < count($k); $i++) {

                /// check excel row data

                if ($r[0] == '' && $r[1] == '' && $r[2] == '') {
                    
                } else if ($r[0] == "EOF") {
                    $eof = "1";
                } else {

                    // Make Variable for each element of excel
                    $partcode = "";
                    $imei1 = "";
                    //$imei2 = "";
					$stocktype = "".$r[2];
                    $msg_partcode = "";
                    $msg_imei = "";
                    $smg = "";
                    $froml = $billing_data['po_from'];
                    $tol = $billing_data['po_to'];
                    $owner_no = $billing_data['po_to'];
                    $po_no = $billing_data['po_no'];
                    $flag = "Y";
                    // check partcode 
                    $part_code = mysqli_fetch_assoc(mysqli_query($link1, "select prod_code from vendor_order_data where prod_code='".$r[0]."' AND po_no='".$_POST['challan_no']."'"));
                    if ($part_code['prod_code'] != "") {
                        $partcode = $r[0];
                    } else {
                        $msg_partcode = "Product code not exit on selected document no. ".$r[0];
                        header("Location:poUploadImei.php?challan_no=" . $_POST['challan_no'] . "&msg=$msg_partcode" . $pagenav);
                        exit;
                    }

                   // check imei1 and imei2 	
					$imeidata = mysqli_num_rows(mysqli_query($link1, "select id,owner_code from billing_imei_data where imei1='" . $r[1] . "'  and prod_code ='" . $partcode . "'"));
					
					if ($imeidata == 0) {
                        $imei1 = "" . $r[1];
             		   //$imei2 = "" . $r[2];
					   	$stocktype = "".$r[2];
                    } else {
                        $msg_imei = "Serial no. already exist in database! ".$r[1];
                        header("Location:poUploadImei.php?challan_no=" . $_POST['challan_no'] . "&msg=$msg_imei" . $pagenav);
                        exit;
                    }

                    // check imei1 and imei2  at prod_code

                    if ($partcode != "" && $imei1 != "") {	               
                            // insert imei into billing_imei_data and billing_imei
                  			$sql = "INSERT INTO billing_imei_data set from_location='" . $froml . "',to_location='" . $tol . "',owner_code='" . $owner_no . "',prod_code='" . $partcode . "',doc_no='" . $_POST['challan_no'] . "',imei1='" . $imei1 . "',imei2='" . $imei2 . "',flag='" . $flag . "', stock_type='".$stocktype."', transaction_date='".$billing_data["entry_date"]."', import_date='".$billing_data["entry_date"]."'";
							
                         	$result = mysqli_query($link1, $sql);                       
                            //// check if query is not executed
                            if (!$result) {
                                $flag1 = FALSE;
                                $msg = "2";
                            }else{
								////// update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 01 JULY 2022
								if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM serial_stock WHERE serial_no='".$imei1."'"))>0){
									$res_upd_ss = mysqli_query($link1,"UPDATE serial_stock SET location_code='".$owner_no."', prod_code='".$partcode."', rem_qty='1', stock_type='".$stocktype."', ref_no='".$_POST['challan_no']."', ref_date='".$billing_data["receive_date"]."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."' WHERE serial_no='".$imei1."'");
									if (!$res_upd_ss) {
										$flag = false;
										$error_msg = "Error details4.1: " . mysqli_error($link1) . ".";
									}
								}else{
									$res_inst_ss = mysqli_query($link1,"INSERT INTO serial_stock SET location_code='".$owner_no."', prod_code='".$partcode."', serial_no='".$imei1."',inside_qty='1', rem_qty='1', stock_type='".$stocktype."', ref_no='".$_POST['challan_no']."', ref_date='".$billing_data["receive_date"]."',import_date='".$billing_data["receive_date"]."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."'");
									if (!$res_inst_ss) {
										$flag = false;
										$error_msg = "Error details4.2: " . mysqli_error($link1) . ".";
									}
								}
								////// end of script update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 01 JULY 2022
                                $msg='1';
                            }
							///////start script of insert in battery charging table if product is battery written by shekhar on 21 june 2022
							$check_btr = mysqli_fetch_assoc(mysqli_query($link1,"SELECT a.productcategory,b.cat_name FROM product_master a, product_cat_master b WHERE a.productcategory=b.catid AND a.productcode='".$partcode."'"));
							if(strtoupper($check_btr["cat_name"])=="BATTERY"){
								$res_btrchg = mysqli_query($link1,"INSERT INTO battery_charging_status SET doc_no='".$_POST['challan_no']."', prod_code ='".$partcode."', serial_no ='".$imei1."', status='NOT CHARGE', import_date='".$billing_data["receive_date"]."', entry_date='".$datetime."', entry_by='".$_SESSION["userid"]."'");
								//// check if query is not executed
								if (!$res_btrchg) {
									$flag = FALSE;
									$error_msg = "Error details4.1: " . mysqli_error($link1) . ".";
								}
							}
							/////// end script of insert in battery charging table if product is battery written by shekhar on 21 june 2022
						
                    } else {
                        $msg = '2';
                    }
                }
            }
        }
		      }
			  else {
			    $msg_imei = "Dupliate Serial no. in Excel Sheet";
				header("Location:poUploadImei.php?challan_no=" . $_POST['challan_no'] . "&msg=$msg_imei" . $pagenav);
				exit; 
			  }
		
        if ($msg == '1') {
            ///// check both master and data query are successfully executed
            if ($flag1) {
                // move file into folder
                move_uploaded_file($_FILES["attchfile"]["tmp_name"], "../upload/bill_upload/" . $now . $_FILES["attchfile"]["name"]);
                $f_name = $now . $_FILES["attchfile"]["name"];
                // insert into upload file data
                mysqli_query($link1, "update vendor_order_master set file_name='" . $f_name . "',imei_attach='Y' where po_no='" . $_POST['challan_no'] . "'");
				
				////// update in model data
			    mysqli_query($link1,"UPDATE vendor_order_data SET imei_attach='Y', file_name='" . $f_name . "' WHERE po_no='" . $_POST['challan_no'] . "'");
							
                mysqli_commit($link1);
                $msg = "File is uploaded Successfully!";
                header("Location:localPurchaseList.php?msg=$msg" . $pagenav);
                exit;
            } else {
                mysqli_rollback($link1);
                $msg = "File is not uploaded Properly.Serial no. already exit!";
            }
        } elseif ($msg == '2') {
            $msg = "File is not uploaded Properly.Serial no. not exit!";
            header("Location:poUploadImei.php?challan_no=" . $_POST['challan_no'] . "&msg=$msg" . $pagenav);
            exit;
        } 
         mysqli_close($link1);
		 }
    }
	header("Location:poUploadImei.php?challan_no=" . $_POST['challan_no'] . "&msg=$msg" . $pagenav);
    exit;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= siteTitle ?></title>
        <script src="../js/jquery-1.10.1.min.js"></script>
        <link href="../css/font-awesome.min.css" rel="stylesheet">
        <link href="../css/abc.css" rel="stylesheet">
        <script src="../js/bootstrap.min.js"></script>
        <link href="../css/abc2.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/bootstrap-select.min.css">
        <script src="../js/bootstrap-select.min.js"></script>
        <script>
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
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <link rel="stylesheet" href="../css/datepicker.css">
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
                    <h2 align="center"><i class="fa fa-upload"></i>Upload Serial No.<br>
					<?php echo $invoice;  ?></h2><div style="display:inline-block;float:right"><a href="../templates/UPLOAD_Serial.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div><br/>
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
                                            <input type="hidden" name="challan_no" value="<?= $invoice ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" align="center">
                                    <input title="Back" class="btn btn-primary" value="Back" onClick="window.location.href = 'localPurchaseList.php?<?= $pagenav ?>'" type="button">&nbsp;&nbsp;&nbsp;
                                    <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Upload" title="" <?php if ($_POST['Submit'] == 'Upload') { ?>disabled<?php } ?>>
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