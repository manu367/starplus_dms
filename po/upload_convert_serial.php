<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM stockconvert_master WHERE doc_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
$doc_no = $docid;
$conv_type = $po_row["stock_type"];
$convertFrom = explode(" to ",$conv_type);
if($convertFrom[0] == 'okqty'){
	 $typestock = "OK";
}else if ($convertFrom[0] == 'missing'){
	$typestock = "MISSING";
	
}else if($convertFrom[0] == 'damage' || $convertFrom[0] == 'broken'){
	$typestock = "DAMAGE";
}else {
	
}
if($convertFrom[1] == 'okqty'){
	 $contypestock = "OK";
}else if ($convertFrom[1] == 'missing'){
	$contypestock = "MISSING";
	
}else if($convertFrom[1] == 'damage' || $convertFrom[1] == 'broken'){
	$contypestock = "DAMAGE";
}else {
	
}
///////////////////
function unixstamp($excelDateTime) {
    $d = floor($excelDateTime); // seconds since 1900
    $t = $excelDateTime - $d;
    return ($d > 0) ? ( $d - 25569 ) * 86400 + $t * 86400 : $t * 86400;
}
//////////////// after hitting upload button
@extract($_POST);
if ($_POST['Submit'] == "Upload") {
	if($doc_no!="" && $docid!=""){
		//// initialization
		$flag1 = TRUE;
		mysqli_autocommit($link1, false);
		$error_msg = "";
		if ($_FILES["attchfile"]["name"]) {
			require_once "../includes/simplexlsx.class.php";
			$xlsx = new SimpleXLSX($_FILES['attchfile']['tmp_name']);
			list($cols) = $xlsx->dimension();		
			///////////////////////////////////////// IMEI Duplicate ent
			$imei_dup1=array();
			$imei_dup2=array();
			$ser_dup1=array();
			$ser_exist=array();
			$match_part=array();
			$ser_partcnotmatch = array(); 
			$stktypnotmatch = array(); 
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
						$imeidata = mysqli_fetch_assoc(mysqli_query($link1, "SELECT id,owner_code,prod_code,stock_type FROM billing_imei_data WHERE imei1='".$r[1]."' ORDER BY id DESC"));
						if($imeidata['prod_code']!=$r[0]){
							$ser_partcnotmatch[] = $r[1];
						} 
						if($imeidata['stock_type']!=$typestock){
							$stktypnotmatch[] = $r[1];
						}
						if ($imeidata['id'] != "" && $imeidata['owner_code']==$po_row['location_code']) {
							
						}
						else {
							$ser_exist[] = $r[1];
						}
						//$imei_dup2[]="".$r[2];
						$match_part[$r[0]][$r[2]] +=1; 
						
					}
				}/////for loop closed				
			}/////////////froeach loop cloded
			///// check partcode
			$chkflg = 1;
			$prtarr = array();
			$res_scd = mysqli_query($link1,"SELECT prod_code, qty, to_prod_code FROM stockconvert_data WHERE doc_no='".$doc_no."'");
			while($row_scd = mysqli_fetch_assoc($res_scd)){
				$chkqty = $match_part[$row_scd["prod_code"]][$row_scd["to_prod_code"]];
				if($chkqty==round($row_scd["qty"])){
					$chkflg *= 1;
				}else{
					$chkflg *= 0;
					$prtarr[] = $row_scd["prod_code"]." to ".$row_scd["to_prod_code"];
				}
			}
			if($chkflg==0){
				$msg = "Serial nos. product not matched with document product";
				///// move to parent page
				$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$prtarr];
				header("location:upload_convert_serial.php?id=".$id."".$pagenav);
				exit;
			}
			if($ser_partcnotmatch){
				//$msg = "Serial nos. not having 17 digits";
				$msg = "Serial nos. product not matched with uploaded";
				///// move to parent page
				$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$ser_partcnotmatch];
				header("location:upload_convert_serial.php?id=".$id."".$pagenav);
				exit;
			}else if($ser_exist){
				
				$msg = "Serial nos. does not exist in database";
				///// move to parent page
				$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$ser_exist];
				header("location:upload_convert_serial.php?id=".$id."".$pagenav);
				exit;		
			}else if($stktypnotmatch){
				
				$msg = "Serial nos. stock type is mismatched";
				///// move to parent page
				$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$stktypnotmatch];
				header("location:upload_convert_serial.php?id=".$id."".$pagenav);
				exit;		
			}else{
			$imei_count1=count($imei_dup1);
			//$imei_count2=count($imei_dup2);
			$imei_count3=count(array_unique($imei_dup1));
			//$imei_count4=count(array_unique($imei_dup2));
			if($imei_count1==$imei_count3)
			{
				$msg2 = 1;
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
							$partcode = $r[0];
							$imei1 = $r[1];
							$imei2 = $r[2];//// change partcode
							$msg_partcode = "";
							$msg_imei = "";
							$smg = "";
							$froml = $po_row['location_code'];
							$tol = $po_row['location_code'];
							$owner_no = $po_row['location_code'];
							$doc_no = $po_row['doc_no'];
							$flag = "Y";
							
							// check imei1 and imei2  at prod_code
	
						if ($partcode != "" && $imei1 != "" && $imei2 != "") {		
							$owner_code1 =mysqli_query($link1, "select owner_code,import_date from billing_imei_data where prod_code='" . $partcode . "' and imei1='" . $r[1] . "' order by id desc");	
								$row1 = mysqli_fetch_array($owner_code1);								
								if(rtrim($row1['owner_code']," ")==$froml){
								$topartcode = mysqli_fetch_assoc(mysqli_query($link1,"SELECT to_prod_code FROM stockconvert_data WHERE doc_no='".$doc_no."' AND prod_code='".$partcode."'"));	               
								// insert imei into billing_imei_data and billing_imei
								$result1 = mysqli_query($link1,"INSERT INTO billing_imei_data SET from_location='".$froml."',to_location='".$tol."',owner_code='".$owner_no."',prod_code='".$topartcode["to_prod_code"]."',doc_no='".$doc_no."',imei1='".$imei1."',stock_type='".$contypestock."',transaction_date='".$po_row['entry_date']."',import_date='".$row1['import_date']."'");
								//// check if query is not executed
								if (!$result1) {
									$flag1 = false;
									$error_msg = "Error details4.0: " . mysqli_error($link1) . ".";
									$msg2  *= 0;
									$m = "1";
								}else{
									////// update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 22 JULY 2022
									if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM serial_stock WHERE serial_no='".$imei1."'"))>0){
										$res_upd_ss = mysqli_query($link1,"UPDATE serial_stock SET location_code='".$owner_no."', prod_code='".$topartcode["to_prod_code"]."', rem_qty='1', stock_type='".$contypestock."', ref_no='".$doc_no."', ref_date='".$po_row['entry_date']."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."' WHERE serial_no='".$imei1."'");
										if (!$res_upd_ss) {
											$flag1 = false;
											$error_msg = "Error details4.1: " . mysqli_error($link1) . ".";
											$msg2  *= 0;
											$m = "2";
										}
									}else{
										$res_inst_ss = mysqli_query($link1,"INSERT INTO serial_stock SET location_code='".$owner_no."', prod_code='".$topartcode["to_prod_code"]."', serial_no='".$imei1."',inside_qty='1', rem_qty='1', stock_type='".$contypestock."', ref_no='".$doc_no."', ref_date='".$po_row['entry_date']."',import_date='".$row1['import_date']."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."'");
										if (!$res_inst_ss) {
											$flag1 = false;
											$error_msg = "Error details4.2: " . mysqli_error($link1) . ".";
											$msg2  *= 0;
											$m = "3";
										}
									}
									////// end of script update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 22 JULY 2022
									$msg2  *= 1;
								}
							} 
							else {
							
								$msg2  *= 0;
								$m = "4<br/>".$r[1];
							}
							
						} else {
							$msg2  *= 0;
							$m = "5";
						}
					}
				}
			}
						$flag1 = dailyActivity($_SESSION['userid'],$doc_no,"Serial ATTACH","UPLOAD",$ip,$link1,$flag1);
				  }
				  else {
					$arr_dupli = array();
					foreach($ser_dup1 as $serialno => $val){ 
						if($val>1){ 
							$arr_dupli[] = $serialno;
						}
					}
					$msg_imei = "Dupliate Serial no. in Excel Sheet";
					$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_dupli];
					header("location:upload_convert_serial.php?id=".$id."".$pagenav);
					exit;		
					//header("Location:upload_convert_serial.php?challan_no=" . $docid . "&msg=$msg_imei" . $pagenav);
					//exit; 
				  }
			if ($msg2 == 1) {
				///// check both master and data query are successfully executed
				if ($flag1) {
					// move file into folder
					move_uploaded_file($_FILES["attchfile"]["tmp_name"], "../upload/stc_upload/" . $now . $_FILES["attchfile"]["name"]);
					$f_name = $now . $_FILES["attchfile"]["name"];
					// insert into upload file data
					mysqli_query($link1, "update stockconvert_master set file_name='" . $f_name . "',serial_attach='Y' where doc_no='" . $doc_no . "'");
					mysqli_query($link1, "update stockconvert_data set file_name='" . $f_name . "',serial_attach='Y' where doc_no='" . $doc_no . "'");
					mysqli_commit($link1);
					$msg = "File is uploaded Successfully!";
					header("Location:stockconvert_list.php?id=".$id."&msg=$msg" . $pagenav);
					exit;
				} else {
					mysqli_rollback($link1);
					$msg = "File is not uploaded Properly.Serial no. already exit!";
				}
			} elseif ($msg2 == 0) {
				$msg = "File is not uploaded Properly.Serial no. not exit!! ".$m;
				header("Location:upload_convert_serial.php?id=" . $id . "&msg=$msg" . $pagenav);
			    exit;
			} 
			 mysqli_close($link1);
			}
		}
	}else{
		$msg = "Document no. not found.";
		header("Location:upload_convert_serial.php?id=" . $id . "&msg=$msg" . $pagenav);
		exit;
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
                    <h2 align="center"><i class="fa fa-upload"></i>Stock Convert Serial Upload</h2>
                    <div style="display:inline-block;float:right"><a href="../templates/UPLOAD_STC_SERIAL.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div><br/>
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
                                <div class="col-md-12"><label class="col-md-4 control-label">Location Name <span class="red_small">*</span></label>
                                  <div class="col-md-4">
                                  <textarea name="billto" id="billto" class="form-control required" required readonly style="resize:none"><?php echo str_replace("~",",",getLocationDetails($po_row['location_code'],"name,city,state",$link1));?></textarea>
                                  </div>
                                </div>
                              </div>
                              <div class="form-group">
                                <div class="col-md-12"><label class="col-md-4 control-label">Document No.<span class="red_small">*</span></label>
                                  <div class="col-md-4">
                                       <input type="text" name="invno" id="invno" class="form-control required" required value="<?php echo $po_row['doc_no'];?>" readonly/>
                                       <input type="hidden" name="id" id="id" class="form-control" value="<?php echo base64_encode($po_row['doc_no']);?>" readonly/>
                                  </div>
                                </div>
                              </div>
                              <div class="form-group">
                                <div class="col-md-12"><label class="col-md-4 control-label">Document Date</label>
                                  <div class="col-md-4">
                                  <input type="text" name="invdate" id="invdate" class="form-control" value="<?php echo $po_row['entry_date'];?>" readonly/>
                                  </div>
                                </div>
                              </div>
                              <div class="form-group">
                                <div class="col-md-12"><label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
                                  <div class="col-md-4">
                                      <div class="input-group">
                                        <label class="input-group-btn">
                                            <span class="btn btn-primary">
                                                Browse&hellip; <input type="file" name="attchfile" id="attchfile" class="form-control required" required style="display:none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
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
                                  <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Upload'){?>disabled<?php }?>>
                                  &nbsp;&nbsp;&nbsp;
                                  <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='stockconvert_list.php?<?=$pagenav?>'">
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