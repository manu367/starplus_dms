<?php
require_once("../config/config.php");
//$doc_no = base64_decode($_REQUEST['challan_no']);
$doc_no = base64_decode($_REQUEST['challan_no']);
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
        $billing_data = mysqli_fetch_assoc(mysqli_query($link1, "SELECT from_location,to_location,po_no,sale_date FROM `billing_master` WHERE challan_no='" . $doc_no . "'"));
		/////// gettotal qty
		$totosqty = 0;
		$opening_data = mysqli_query($link1, "SELECT prod_code,SUM(okqty+damageqty) AS qty FROM `billing_model_data` WHERE challan_no='" . $doc_no . "'");
		while($row_os = mysqli_fetch_assoc($opening_data)){
			$is_serialize = getAnyDetails($row_os["prod_code"],"is_serialize","productcode","product_master",$link1);
			if($is_serialize=="Y"){
				$totosqty += $row_os["qty"];
			}
		}
        list($cols) = $xlsx->dimension();		
		///////////////////////////////////////// IMEI Duplicate ent
		$imei_dup1=array();
		$imei_dup2=array();
		$ser_dup1=array();
		$ser_exist=array();
		$arr_part = array();
		$ser_partcnotmatch = array(); 
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
					$ser_dup1[$r[1]] += 1;
					// check imei1 and imei2 	
					$imeidata = mysqli_fetch_assoc(mysqli_query($link1, "SELECT id,owner_code,prod_code FROM billing_imei_data WHERE imei1='".$r[1]."' ORDER BY id DESC"));
					if($imeidata['prod_code']!=$r[0]){
						$ser_partcnotmatch[] = $r[1];
					} 
					if ($imeidata['id'] != "" && $imeidata['owner_code']==$billing_data['from_location']) {
						
					}else {
						$ser_exist[] = $r[1];
					}
					//$imei_dup2[]="".$r[2];
					$arr_part[$r[0]][$r[2]] +=1;
				}
			}/////for loop closed				
		}/////////////froeach loop cloded
		if($ser_partcnotmatch){
			//$msg = "Serial nos. not having 17 digits";
			$msg = "Serial nos. product not matched with uploaded";
			///// move to parent page
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$ser_partcnotmatch];
			header("location:vendor_return_serial.php?challan_no=" . $_REQUEST['challan_no'].$pagenav);
			exit;
		}else if($ser_exist){
			
			$msg = "Serial nos. does not exist in database";
			///// move to parent page
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$ser_exist];
			header("location:vendor_return_serial.php?challan_no=" . $_REQUEST['challan_no'].$pagenav);
			exit;		
		}else{
		$imei_count1=count($imei_dup1);
		//$imei_count2=count($imei_dup2);
		$imei_count3=count(array_unique($imei_dup1));
		//$imei_count4=count(array_unique($imei_dup2));
		if($imei_count1==$imei_count3)
		{
			if($imei_count3 != round($totosqty)){
				$msg = "Please upload total serial nos. Uploaded Qty-> ".$imei_count3." and Challan Qty-> ".$totosqty;
				header("Location:vendor_return_serial.php?challan_no=" . $_REQUEST['challan_no'] . "&msg=$msg" . $pagenav);
				exit;
			}
			$vendor_datanew = mysqli_query($link1,"SELECT SUM(FLOOR(okqty)) AS totokqty,SUM(FLOOR(damageqty)) AS totdmqty ,prod_code FROM billing_model_data WHERE challan_no='".$doc_no."' AND imei_attach = '' GROUP BY prod_code");
			while($row_chkqty = mysqli_fetch_array($vendor_datanew)){
				$arr_part[$row_chkqty["prod_code"]]["OK"] = ($arr_part[$row_chkqty["prod_code"]]["OK"] == "")?0:$arr_part[$row_chkqty["prod_code"]]["OK"];
				$arr_part[$row_chkqty["prod_code"]]["DAMAGE"] = ($arr_part[$row_chkqty["prod_code"]]["DAMAGE"] == "")?0:$arr_part[$row_chkqty["prod_code"]]["DAMAGE"];
				if($row_chkqty["totokqty"]==$arr_part[$row_chkqty["prod_code"]]["OK"] && $row_chkqty["totdmqty"]==$arr_part[$row_chkqty["prod_code"]]["DAMAGE"]){
				}else{
					$msg = "Uploading Serial No. qty does not match with document qty for product .".$row_chkqty["prod_code"];
					///// move to parent page
					$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg,"invalid"=>array($row_chkqty["prod_code"])];
					header("location:vendor_return_serial.php?challan_no=" . $_REQUEST['challan_no'] . "".$pagenav);
					exit;
				}
			}
			$msg = 1;
			foreach ($xlsx->rows() as $k => $r) {
            	if ($k == 0 || $k == 1)
                continue; // skip first row 
            	for ($i = 0; $i < count($k); $i++) {
                	/// check excel row data
					if ($r[0] == '' && $r[1] == '' && $r[2] == '' ) {
						
					} else if ($r[0] == "EOF") {
						$eof = "1";
					} else {
						// Make Variable for each element of excel
						$partcode = "";
						$imei1 = $r[1];
						$imei2 = $r[2];//// stock type
						$msg_partcode = "";
						$msg_imei = "";
						$smg = "";
						$froml = $billing_data['from_location'];
						$tol = $billing_data['to_location'];
						$owner_no = $billing_data['to_location'];
						$po_no = $billing_data['po_no'];
						$flag = "Y";
						// check partcode 
						$part_code = mysqli_fetch_assoc(mysqli_query($link1, "select prod_code from billing_model_data where prod_code='" . $r[0] . "' AND challan_no='".$doc_no."' AND prod_cat!='C'"));
						if ($part_code['prod_code'] != "") {
							$partcode = $r[0];
						} else {
							$msg_partcode = "Product code not exit!";
							header("Location:vendor_return_serial.php?challan_no=" . $_REQUEST['challan_no'] . "&msg=$msg_partcode" . $pagenav);
							exit;
						}
                    	// check imei1 and imei2  at prod_code

                    if ($partcode != "" && $imei1 != "" && $imei2 != "") {		
                        $owner_code1 =mysqli_query($link1, "select owner_code,import_date from billing_imei_data where prod_code='" . $partcode . "' and imei1='" . $r[1] . "' order by id desc");	
							$row1 = mysqli_fetch_array($owner_code1);								
                       			 	if(rtrim($row1['owner_code']," ")==$froml){		               
                            // insert imei into billing_imei_data and billing_imei
                  			$sql = "INSERT INTO billing_imei_data set from_location='" . $froml . "',to_location='" . $tol . "',prod_code='" . $partcode . "',doc_no='" . $doc_no . "',imei1='" . $imei1 . "',stock_type='" . $imei2 . "',flag='" . $flag . "', transaction_date='".$billing_data["sale_date"]."', import_date='".$row1["import_date"]."'";
				
                         $result = mysqli_query($link1, $sql);                       
                            //// check if query is not executed
                            if (!$result) {
                                $flag1 = FALSE;
                                $msg *= 0;
								$m = "1";
                            }else{
								////// update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 22 JULY 2022
								if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM serial_stock WHERE serial_no='".$imei1."'"))>0){
									$res_upd_ss = mysqli_query($link1,"UPDATE serial_stock SET location_code='".$tol."', prod_code='".$partcode."', rem_qty='1', stock_type='".$imei2."', ref_no='".$doc_no."', ref_date='".$billing_data["sale_date"]."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."' WHERE serial_no='".$imei1."'");
									if (!$res_upd_ss) {
										$flag1 = false;
										$error_msg = "Error details4.1: " . mysqli_error($link1) . ".";
										$msg *= 0;
										$m = "2";
									}
								}else{
									$res_inst_ss = mysqli_query($link1,"INSERT INTO serial_stock SET location_code='".$tol."', prod_code='".$partcode."', serial_no='".$imei1."',inside_qty='1', rem_qty='1', stock_type='".$imei2."', ref_no='".$doc_no."', ref_date='".$billing_data["sale_date"]."',import_date='".$row1['import_date']."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."'");
									if (!$res_inst_ss) {
										$flag1 = false;
										$error_msg = "Error details4.2: " . mysqli_error($link1) . ".";
										$msg *= 0;
										$m = "3";
									}
								}
								////// end of script update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 22 JULY 2022
                                $msg *=1;
                            }
                        } 
						else {
						
                            $msg *= 0;
							$m = "4<br/>".$r[1];
                        }
						
                    } else {
                        $msg *= 0;
						$m = "5";
                    }
                }
            }
        }
		      }
			  else {
			  	$arr_dupli = array();
				foreach($ser_dup1 as $serialno => $val){ 
					if($val>1){ 
						$arr_dupli[] = $serialno;
					}
				}
			    $msg = "Dupliate Serial no. in Excel Sheet";
				$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_dupli];
				header("location:vendor_return_serial.php?challan_no=" . $_REQUEST['challan_no'].$pagenav);
				exit;		
				//header("Location:vendor_return_serial.php?challan_no=" . $doc_no . "&msg=$msg_imei" . $pagenav);
				//exit; 
			  }
		
        if ($msg == 1) {
            ///// check both master and data query are successfully executed
            if ($flag1) {
                // move file into folder
                move_uploaded_file($_FILES["attchfile"]["tmp_name"], "../upload/return_upload/" . $now . $_FILES["attchfile"]["name"]);
                $f_name = $now . $_FILES["attchfile"]["name"];
                // insert into upload file data
                mysqli_query($link1, "update billing_master set file_name='" . $f_name . "',imei_attach='Y' where challan_no='" . $doc_no . "'");
				mysqli_query($link1, "update billing_model_data set file_name='" . $f_name . "',imei_attach='Y' where challan_no='" . $doc_no . "' AND prod_cat!='C'");
                mysqli_commit($link1);
                $msg = "File is uploaded Successfully!";
                header("Location:vendor_return.php?msg=$msg" . $pagenav);
                exit;
            } else {
                mysqli_rollback($link1);
                $msg = "File is not uploaded Properly.Serial no. already exit!";
            }
        } elseif ($msg == 0) {
			mysqli_rollback($link1);
            $msg = "File is not uploaded Properly.Serial no. not exit!! ".$m;
            header("Location:vendor_return_serial.php?challan_no=" . $_REQUEST['challan_no'] . "&msg=$msg" . $pagenav);
           exit;
        } 
         mysqli_close($link1);
		}
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
                    <h2 align="center"><i class="fa fa-upload"></i>Vendor Return Upload<?=$imeitag?><br>
					<?php echo $doc_no;  ?></h2><div style="display:inline-block;float:right"><a href="../templates/UPLOAD_RETURN_SERIAL.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div><br/>
                    <div class="form-group"  id="page-wrap" style="margin-left:10px;">
                        <?php if ($_REQUEST['msg']) { ?><br>
                            <h4 align="center" style="color:#FF0000"><?= $_REQUEST['msg'] ?></h4>
                        <?php } ?>  
                        <?php
		if(isset($_SESSION["logres"]) && $_SESSION["logres"]){
		echo '<div class="py-2 overflow-hidden" style="background:#f1f1f1;padding:15px;line-height:20px;color:#e51111;margin:15px;font-size:12px;">';
		echo '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.$_SESSION["logres"]["msg"];
		echo '<br/><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.implode(" , ",$_SESSION["logres"]["invalid"]);
		echo '</div>';
		}
		unset($_SESSION["logres"]);
		?>
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
                                            <input type="text" class="form-control required" name="billfile"  id="billfile" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" align="center">
                                    <input title="Back" class="btn btn-primary" value="Back" onClick="window.location.href = 'vendor_return.php?<?= $pagenav ?>'" type="button">&nbsp;&nbsp;&nbsp;
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