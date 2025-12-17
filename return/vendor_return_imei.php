<?php
require_once("../config/config.php");
$docno = base64_decode($_GET['challan_no']);
$froml = base64_decode($_GET['from']);
$tol = base64_decode($_GET['to']);
$owner_no = base64_decode($_GET['to']);

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
	///////// 
    if ($_FILES["attchfile"]["name"]) {
    	require_once "../includes/simplexlsx.class.php";
        $xlsx = new SimpleXLSX($_FILES['attchfile']['tmp_name']);
        list($cols) = $xlsx->dimension();
		///// pick perticular part details
		$vendor_data = mysqli_fetch_assoc(mysqli_query($link1," select sum(req_qty) as qty from vendor_order_data where po_no =  '".$docno."' "));
		
		//// check qty vs uploading qty
		$total_count = '0';     
		$total_count = count($xlsx->rows()); ///// count no. of rows in uploading sheet ///////////////////////////////////////////	
		if($vendor_data['qty'] == ($total_count -1)){
		///////////////////////////////////////// IMEI Duplicate ent
			$imei_dup1=array();
			$imei_dup2=array(); 
			foreach( $xlsx->rows() as $k => $r) {
				if ($k == 0) continue; // skip first row 
				for( $i = 0; $i < count($k); $i++){
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
			if($imei_count1==$imei_count3 && $imei_count2==$imei_count4){
				foreach( $xlsx->rows() as $k => $r) {
					if ($k == 0) continue; // skip first row 
					for($i = 0; $i < count($k); $i++){
						/// check excel row data
						if($r[0]=='' && $r[1]=='' && $r[2]==''){		  
						}
						else if($r[0]=="EOF"){
						   $eof="1";
						}else{							  
							////Make Variable for each element of excel//////		
							$imei1="".$r[0];
							$imei2="".$r[1];
							$partcode_check="".$r[2];
							$flag = "Y";
							
							$part_code = mysqli_fetch_assoc(mysqli_query($link1, "select prod_code from vendor_order_data where prod_code='" .$partcode_check  . "' and po_no='" . $docno . "' group by prod_code "));
							if ($part_code['prod_code'] != "") {
								$partcode = $r[2];
							} else {
								$msg_partcode = "Product code not exit!";
								header("Location:vendor_return.php?challan_no=" . $docno . "&msg=$msg_partcode" . $pagenav);
								exit;
							}
							// check imei1 and imei2
							$imeidata = mysqli_fetch_assoc(mysqli_query($link1, "select id,owner_code from billing_imei_data where (imei1='" . $imei1 . "' or imei2='" .$imei1 . "' or imei1='" . $imei2 . "' or imei2='" .$imei2 . "')  and prod_code ='" . $partcode. "'  order by id desc"));
							
							if ($imeidata['id'] != "" && $imeidata['owner_code']==$owner_no) {
								
							} else {
								$msg_imei = "IMEI Does not exist in database. ".$imei1."-".$froml."-".$imeidata['owner_code'];
								header("Location:vendor_return.php?challan_no=" . $docno . "&msg=$msg_imei" . $pagenav);
								exit;
							}
							// check imei1 and imei2  at prod_code
							if ($partcode != "" && $imei1 != "" && $imei2 != "") {			                 	               
								// insert imei into billing_imei_data and billing_imei
								$sql = "INSERT INTO billing_imei_data set from_location='" .$tol . "',to_location='" . $froml . "',owner_code='" .$froml . "',prod_code='" .$partcode. "',doc_no='" . $docno . "',imei1=" . $imei1 . ",imei2='" . $imei2 . "',flag='" . $flag . "' ";
								$result = mysqli_query($link1, $sql);                       
								//// check if query is not executed
								if (!$result) {
									$flag1 = FALSE;
									$msg = "2";
								}else{
									$msg='1';
									// move file into folder
									move_uploaded_file($_FILES["attchfile"]["tmp_name"], "../upload/return_upload/" . $now . $_FILES["attchfile"]["name"]);
									$f_name = $now . $_FILES["attchfile"]["name"];
																		
									// insert into upload file data
									$result3  =  mysqli_query($link1, "update vendor_order_data set file_name='" . $f_name . "',imei_attach='Y', upload_date='".$today."' where po_no='" . $docno . "' and prod_code = '".$partcode. "'  ");
									//// check if query is not executed
									if (!$result3) {
										$flag = false;
										$error_msg = "Error details4: " . mysqli_error($link1) . ".";
									}
								}
							}
							else {
								$msg = '2';
							}
						}  /////////////  end of else /////////////////////////////////////
					}  /////////  end of for loop //////////////////////////////
				}   //////////////  end of foreach loop //////////////////////////////////////////////////////////////	
			}
			else {
				$msg_imei = "Dupliate IMEI in Excel Sheet!";
				header("Location:vendor_return.php?challan_no=" . $docno . "&msg=$msg_imei" . $pagenav);
				exit;
			}
		} ///////////////   end of main if condition /////////////////////////////////////////////////////////////////////
		else {
 			$msg_imei = "Uploading Qty does no match with Requested Qty";
         	header("Location:vendor_return.php?op=edit&id='". $docno."' &msg=$msg_imei" . $pagenav);
         	exit; 
		}
        if ($msg == '1') {
        	///// check query are successfully executed
            if ($flag1) {
		  		$po_no = mysqli_num_rows(mysqli_query($link1, "select id from vendor_order_data where po_no = '".$docno."' and imei_attach=''"));
		  		if($po_no==0){
		    		$result4  =  mysqli_query($link1, "update vendor_order_master set file_name='" . $f_name . "',imei_attach='Y',upload_date='".$today."'  where po_no ='" . $docno . "'   ");
				}				
                mysqli_commit($link1);
                $msg = "File is uploaded Successfully!";
             	header("Location:vendor_return.php?op=edit&id='". $docno."' &msg=$msg" . $pagenav);
                exit;
			} else {
                mysqli_rollback($link1);
                $msg = "File is not uploaded Properly.IMEI already exit!";
            }
		} elseif ($msg == '2') {
            $msg = "File is not uploaded Properly.IMEI not exit!";
      		header("Location:vendor_return.php?op=edit&id='". $docno."' &msg=$msg" . $pagenav);
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
                    <h2 align="center"><i class="fa fa-upload"></i>Purchase Return  Upload<?=$imeitag?><br>
					<?php echo base64_decode($_GET['challan_no']);  ?></h2><div style="display:inline-block;float:right"><a href="../templates/po_BILL_retrun.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div><br/>
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
                                            <input type="hidden" name="challan_no" value="<?= $_GET['challan_no'] ?>">
											<input type="hidden" name="prodcode" value="<?= $_GET['prodcode'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" align="center">
                                    <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if ($_POST['Submit'] == 'Update') { ?>disabled<?php } ?>>&nbsp;&nbsp;&nbsp;
                                    <input title="Back" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href = 'vendor_return.php?op=edit&id=<?= ($_GET['po_no'])?><?= $pagenav ?>'" type="button">
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