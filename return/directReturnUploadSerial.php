<?php
require_once("../config/config.php");
require_once("../includes/serial_logic_function.php");
function unixstamp($excelDateTime) {
    $d = floor($excelDateTime); // seconds since 1900
    $t = $excelDateTime - $d;
    return ($d > 0) ? ( $d - 25569 ) * 86400 + $t * 86400 : $t * 86400;
}
$challan_no = base64_decode($_REQUEST["id"]);
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
		$vendor_data = mysqli_fetch_assoc(mysqli_query($link1,"select sum(qty) as qty  from billing_model_data where challan_no='".$_POST['challan_no']."' "));
		///////////
		$vendor_master = mysqli_fetch_assoc(mysqli_query($link1,"select * from billing_master where challan_no='".$_POST['challan_no']."' "));
		//// check qty vs uploading qty<br>
		$total_count='0';     
		$total_count=count($xlsx->rows()); ///// count no. of rows in uploading sheet ///////
		if($vendor_data['qty'] == ($total_count -3)){
			///////////////////////////////////////// IMEI Duplicate ent
			$imei_dup1=array();
			$imei_dup2=array(); 
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
				header("location:direct_return.php?".$pagenav);
				exit;
			}else if($arr_invalsr){
				$upd_cnt = 0;
				$msg = "Serial nos. validation failed";
				///// move to parent page
				$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_invalsr];
				header("location:direct_return.php?".$pagenav);
				exit;		
			}else{
				$imei_count1=count($imei_dup1);
				//$imei_count2=count($imei_dup2);
				$imei_count3=count(array_unique($imei_dup1));
				//$imei_count4=count(array_unique($imei_dup2));
				if($imei_count1==$imei_count3)
				{
					foreach( $xlsx->rows() as $k => $r){
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
								$imei1="".$r[1];
								$imei2="".$r[2];
								$partcode_check="".$r[0];
								$froml = $vendor_master['from_location'];
								$tol = $vendor_master['to_location'];
								$owner_no = $vendor_master['to_location'];
								//// $po_no = $vendor_master['po_no'];
								$flag = "Y";										
								$part_code = mysqli_fetch_assoc(mysqli_query($link1, "select prod_code  from billing_model_data where prod_code='" .$partcode_check  . "' and challan_no='" . $_POST['challan_no'] . "' group by prod_code "));
								if ($part_code['prod_code'] != "") {
									$partcode = $r[0];
								} else {
									$msg_partcode = "Product code not exit!";
									header("Location:direct_return.php?challan_no=" . $_POST['challan_no'] . "&msg=$msg_partcode" . $pagenav);
									exit;
								}
								if($partcode != "" && $imei1 != "" && $imei2 != "") {		
									$owner_code1 =mysqli_query($link1, "select id from billing_imei_data where imei1='" . $imei1 . "'");	
									$row1 = mysqli_num_rows($owner_code1);
									if($row1==0){			                 	               
										//insert imei into billing_imei_data and billing_imei
										$sql = "INSERT INTO billing_imei_data set from_location='" .$froml . "',to_location='" . $tol . "',owner_code='" .$owner_no . "',prod_code='" .$partcode. "',doc_no='" . $_POST['challan_no'] . "',imei1='". $imei1 . "',stock_type='" . $imei2 . "',flag='" . $flag . "', transaction_date='".$vendor_master["sale_date"]."', import_date='".$vendor_master["entry_date"]."' ";
										$result = mysqli_query($link1, $sql);                       
										//// check if query is not executed
										if (!$result) {
											$flag1 = false;
											$error_msg = "Error details4.1: " . mysqli_error($link1) . ".";
											$msg = "2";
											$m = "1";
										}else{
											////// update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 13 DEC 2022
											if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM serial_stock WHERE serial_no='".$imei1."'"))>0){
												$res_upd_ss = mysqli_query($link1,"UPDATE serial_stock SET location_code='".$tol."', prod_code='".$partcode."', rem_qty='1', stock_type='".$imei2."', ref_no='".$_POST['challan_no']."', ref_date='".$billing_data["sale_date"]."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."' WHERE serial_no='".$imei1."'");
												if (!$res_upd_ss) {
													$flag1 = false;
													$error_msg = "Error details4.1.1: " . mysqli_error($link1) . ".";
													$msg = "2";
													$m = "2";
												}
											}else{
												$res_inst_ss = mysqli_query($link1,"INSERT INTO serial_stock SET location_code='".$tol."', prod_code='".$partcode."', serial_no='".$imei1."',inside_qty='1', rem_qty='1', stock_type='".$imei2."', ref_no='".$_POST['challan_no']."', ref_date='".$billing_data["sale_date"]."',import_date='".$vendor_master['entry_date']."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."'");
												if (!$res_inst_ss) {
													$flag1 = false;
													$error_msg = "Error details4.2: " . mysqli_error($link1) . ".";
													$msg = "2";
													$m = "3";
												}
											}
											////// end of script update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 13 DEC 2022
											$msg='1';
										}
									}
									else {
										$msg = '2';
										$m = "4";
									}
								}
								else {
									$msg = '2';
									$m = "5";
								}
							}  /////////////  end of else /////////////////////////////////////
						}  /////////  end of for loop //////////////////////////////
					}//////////////  end of foreach loop //////////////////////////////////////////////////////////////	
				}
				else {
					$msg_imei = "Dupliate Serial no. in Excel Sheet";
					header("Location:direct_return.php?challan_no=" . $_POST['challan_no'] . "&msg=$msg_imei" . $pagenav);
					exit;
				}
			} ///////////////   end of main if condition /////////////////////////////////////////////////////////////////////
		}
		else {
			$msg_imei = "Uploading Qty does not match with Requested Qty";
			header("Location:direct_return.php?op=edit&id='". $_POST['challan_no']."' &msg=$msg_imei" . $pagenav);
			exit; 
		}
		if ($msg == '1') {
			///// check query are successfully executed
			if ($flag1) {
				// move file into folder
				move_uploaded_file($_FILES["attchfile"]["tmp_name"], "../upload/return_upload/" . $now . $_FILES["attchfile"]["name"]);
				$f_name = $now . $_FILES["attchfile"]["name"];
				// insert into upload file data
				mysqli_query($link1, "update billing_master set file_name='" . $f_name . "',imei_attach='Y' where challan_no='" . $_POST['challan_no'] . "'");
				mysqli_query($link1, "update billing_model_data set file_name='" . $f_name . "',imei_attach='Y' where challan_no='" . $_POST['challan_no'] . "'");
				mysqli_commit($link1);
				$msg = "File is uploaded Successfully!";
				header("Location:direct_return.php?op=edit&id='". $_POST['challan_no']."' &msg=$msg" . $pagenav);
				exit;
			} else {
				mysqli_rollback($link1);
				$msg = "File is not uploaded Properly.Serial already exit! ".$m;
			}
		} else if ($msg == '2') {
			$msg = "File is not uploaded Properly.Serial not exit! ".$m;
			header("Location:direct_return.php?op=edit&id='". $_POST['challan_no']."' &msg=$msg" . $pagenav);
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
        </script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script src="../js/fileupload.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row content">
                <?php
                include("../includes/leftnav2.php");
                ?>
                <div class="col-sm-9 tab-pane fade in active" id="home">
                    <h2 align="center"><i class="fa fa-upload"></i>Direct Return Upload<?=$imeitag?><br>
					<?php echo $challan_no  ?></h2><div style="display:inline-block;float:right"><a href="../templates/po_BILL_retrun.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div><br/>
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
                                            <input type="hidden" name="challan_no" value="<?= $challan_no ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" align="center">
                                    <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if ($_POST['Submit'] == 'Update') { ?>disabled<?php } ?>>&nbsp;&nbsp;&nbsp;
                                    <input title="Back" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href = 'direct_return.php?op=edit&id=<?= ($_REQUEST['id'])?><?= $pagenav ?>'" type="button">
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