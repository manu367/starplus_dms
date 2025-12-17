<?php
require_once("../config/config.php");
function unixstamp( $excelDateTime ) {
    $d = floor( $excelDateTime ); // seconds since 1900
    $t = $excelDateTime - $d;
    return ($d > 0) ? ( $d - 25569 ) * 86400 + $t * 86400 : $t * 86400;
}
//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="Upload"){
	if($_FILES["attchfile"]["name"]) {
		require_once "../includes/simplexlsx.class.php";
		$xlsx = new SimpleXLSX( $_FILES['attchfile']['tmp_name'] );	
		///check directory
		$dirct = "../upload/sale_upload/".date("Y-m");
		if (!is_dir($dirct)) {
			mkdir($dirct, 0777, 'R');
		}
		move_uploaded_file($_FILES["attchfile"]["tmp_name"],$dirct."/".$now.$_FILES["attchfile"]["name"]);
		$f_name=$now.$_FILES["attchfile"]["name"];
		//////insert into upload file data////////////
		mysqli_query($link1,"insert into upload_file_data set file_name='".$f_name."',entry_date='".$today."',entry_time='".$currtime."'");
		$file_id=mysqli_insert_id($link1);
		list($cols) = $xlsx->dimension();
		//////////////
		$err_msg = "";
		$arr_party = array();
		$arr_err_pty = array();
		$arr_prod = array();
		$arr_err_prod = array();
		$arr_invalid = array();
		$arr_invalsr = array();	
		$ser_dup1=array();
		$total_ser = 0;
		///// get from location details
		$fromlocname=str_replace("~",",",getLocationDetails($from_loc,"name,city,state",$link1));
		/////////
		foreach( $xlsx->rows() as $k => $r) {
	 		if ($k == 0) continue; // skip first row 
	  		for( $i = 0; $i < count($k); $i++)
	  		{
		  		/// check excel row data
	      		if($r[0]=='' && $r[1]=='' && $r[2]=='' && $r[3]=='' && $r[4]=='' && $r[5]=='' && $r[6]==''){
		       
		  		}else{
	      			////Make Variable for each element of excel//////
					$line_no = $k;
					$to_loc_code = trim($r[0]);
					$to_loc_name = trim($r[1]);
					$product_code = trim($r[2]);
					$product_name = trim($r[3]);
					$document_no = trim($r[4]);
					$document_date = trim($r[5]);
					$serial_no1 = trim($r[6]);
					$serial_no2 = trim($r[7]);
					$ser_dup1[$serial_no1] += 1;
					$total_ser += 1;
					///// check to location code and product code is valid or not
					$party_name = explode("~",getAnyDetails($to_loc_code,"asc_code,name,city,state","sap_code","asc_master",$link1));
					$product_nm = explode("~",getAnyDetails($product_code,"productcode,productname,serial_length","sap_code","product_master",$link1));//// serial no. dynamically check from product master written by shekhar on 23 JAN 23
					///// check party
					if($party_name[0]!=""){
						$arr_party[$to_loc_code] = $party_name[0]."~".$party_name[1].",".$party_name[2].",".$party_name[3];
					}else{
						$arr_err_pty[] = "Line ".$line_no."- ".$to_loc_code;
					}
					//// check claim type
					if($product_nm[0]!=""){
						$arr_prod[$product_code] = $product_nm[0]."~".$product_nm[1];
					}else{
						$arr_err_prod[] = "Line ".$line_no."- ".$product_code;
					}
		  			///// check serial no. length
					if($serial_no1){
						$check_length = $product_nm[2];
						//$serial_length = strFilter($r[1], 17, 17);
						$serial_length = strFilter($serial_no1, $check_length, $check_length);
						if(!$serial_length){
							$arr_invalid[] = $serial_no1; 
						}
						////// check serial no. validation with its product code & model code written by shekhar on 20 dec 2022
						/*$resp = getValidateSerialPartcode($serial_no1,$product_code,$link1);
						if($resp!="Y"){
							$arr_invalsr[] = $serial_no1." -- ".$resp;
						}*/
					}
	      			//$sql="INSERT INTO temp_opn_upload set location_code='".$from_loc."',sub_location='".$stock_in."',prod_code='".$partcode."',imei1='".$imei1."',imei2='".$imei2."',open_date='".$openingdate."',update_by='".$_SESSION['userid']."',browserid='".$browserid."',file_id='".$file_id."'";
          			//mysqli_query($link1,$sql);
		  		}
	  		}
		}//Close For loop
		//// check duplicate
		if(count($ser_dup1)!=$total_ser){
			$arr_dupli = array();
			foreach($ser_dup1 as $serialno => $val){ 
				if($val>1){ 
					$arr_dupli[] = $serialno;
				}
			}
			$msg = "Dupliate Serial No. in Excel Sheet";
			///// move to parent page
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_dupli];
			header("location:upload_primary_sale.php?".$pagenav);
			exit;
		}
		if($arr_invalid){
			$upd_cnt = 0;
			//$msg = "Serial nos. not having 17 digits";
			$msg = "Serial nos. not having defined digits in product master";
			///// move to parent page
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_invalid];
			header("location:upload_primary_sale.php?".$pagenav);
			exit;
		}else if($arr_err_pty){
			$upd_cnt = 0;
			$msg = "Invalid To Party Code";
			///// move to parent page
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_err_pty];
			header("location:upload_primary_sale.php?".$pagenav);
			exit;		
		}else if($arr_err_prod){
			$upd_cnt = 0;
			$msg = "Invalid Product Code";
			///// move to parent page
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_err_prod];
			header("location:upload_primary_sale.php?".$pagenav);
			exit;		
		}else{
			foreach( $xlsx->rows() as $k => $r) {
	 		if ($k == 0) continue; // skip first row 
	  		for( $i = 0; $i < count($k); $i++)
	  		{
		  		/// check excel row data
	      		if($r[0]=='' && $r[1]=='' && $r[2]=='' && $r[3]=='' && $r[4]=='' && $r[5]=='' && $r[6]==''){
		       
		  		}else{
	      			////Make Variable for each element of excel//////
					$line_no = $k;
					$to_loc_code = trim($r[0]);
					$to_loc_name = trim($r[1]);
					$product_code = trim($r[2]);
					$product_name = trim($r[3]);
					$document_no = trim($r[4]);
					$document_date = trim($r[5]);
					$serial_no1 = trim($r[6]);
					$serial_no2 = trim($r[7]);
					//////
					if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM sale_uploader WHERE from_location ='".$from_loc."' AND to_location_sapcode = '".$to_loc_code."' AND doc_no = '".$document_no."' AND serial_no1='".$serial_no1."'"))==0){
						////// party sys code
						$pty_sys_code = explode("~",$arr_party[$to_loc_code]);
						////// product sys code
						$prd_sys_code = explode("~",$arr_prod[$product_code]);
						////// insert in sale upload
						$sql = "INSERT INTO sale_uploader SET sale_type  ='PRIMARY', from_location = '".$from_loc."', from_location_name = '".$fromlocname."', to_location = '".$pty_sys_code[0]."',to_location_sapcode = '".$to_loc_code."', to_location_name = '".$to_loc_name."', prod_code = '".$prd_sys_code[0]."',prod_code_sapcode='".$product_code."', prod_name = '".$product_name."', disp_qty='1', serial_no1='".$serial_no1."', serial_no2 = '".$serial_no2."', doc_no = '".$document_no."', doc_date='".$document_date."', status='Dispatched', entry_date = '".$datetime."', entry_by = '".$_SESSION['userid']."',entry_rmk='".$remark."'";
						$res1 = mysqli_query($link1,$sql);
						if(!$res1){
							$flag = false;
							$err_msg = "Error 1: ". mysqli_error($link1) . ".";
						}
						//////////////insert in billing imei data////////////////////////
						$res2 = mysqli_query($link1,"INSERT INTO billing_imei_data SET from_location='".$from_loc."',to_location='".$pty_sys_code[0]."',owner_code='".$pty_sys_code[0]."',prod_code='".$prd_sys_code[0]."',doc_no='".$document_no."',imei1='".$serial_no1."',stock_type='OK',transaction_date='".$document_date."',import_date='".$document_date."'");
						//// check if query is not executed
						if (!$res2) {
							$flag = false;
							$err_msg = "Error 2:". mysqli_error($link1) . ".";
						}else{
							////// update in serial stock table only one entry of one serial will maintain in this table, written by shekhar on 22 JULY 2022
							if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM serial_stock WHERE serial_no='".$serial_no1."'"))>0){
								$res_upd_ss = mysqli_query($link1,"UPDATE serial_stock SET location_code='".$pty_sys_code[0]."', prod_code='".$prd_sys_code[0]."', rem_qty='1', stock_type='OK', ref_no='".$document_no."', ref_date='".$document_date."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."' WHERE serial_no='".$serial_no1."'");
								if (!$res_upd_ss) {
									$flag = false;
									$err_msg = "Error 3.1: " . mysqli_error($link1) . ".";
									$msg = "2";
								}
							}else{
								$res_inst_ss = mysqli_query($link1,"INSERT INTO serial_stock SET location_code='".$pty_sys_code[0]."', prod_code='".$prd_sys_code[0]."', serial_no='".$serial_no1."',inside_qty='1', rem_qty='1', stock_type='OK', ref_no='".$document_no."', ref_date='".$document_date."',import_date='".$document_date."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."'");
								if (!$res_inst_ss) {
									$flag = false;
									$err_msg = "Error 3.2: " . mysqli_error($link1) . ".";
									$msg = "2";
								}
							}
						}
					}else{
						$flag = false;
						$err_msg = "Error 4: Document no. ".$document_no." is already in system for party ".$to_loc_code." from ".$from_loc;
					}
					
					
				}
			}
			}
			mysqli_query($link1,"UPDATE upload_file_data SET status='1' WHERE id='".$file_id."'");
			////// insert in activity table////
			dailyActivity($_SESSION['userid'],$file_id,"PRIMARY SALE","UPLOAD",$ip,$link1,"");	
			///// check all query are successfully executed
			$addon = "";
			if($err_msg) {		
				$addon = "With some error ".$err_msg;
			}
			$msg = "Primary Sale is successfully uploaded. ".$addon;
			///// move to parent page
			header("location:upload_primary_sale.php?msg=".$msg."&sts=success".$pagenav);
			exit;
		}
   	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=siteTitle?></title>
<script src="../js/jquery-1.10.1.min.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="../css/bootstrap-select.min.css">
<script src="../js/bootstrap-select.min.js"></script>
<script>
	$(document).ready(function(){
	  	var spinner = $('#loader');
		$("#frm1").validate({
			submitHandler: function (form) {
				if (!this.wasSent) {
					this.wasSent = true;
					$(':submit', form).val('Please wait...')
							.attr('disabled', 'disabled')
							.addClass('disabled');
					spinner.show();
					form.submit();
				} else {
					return false;
				}
			}
		});
  	});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script src="../js/fileupload.js"></script>
<link href="../css/loader.css" rel="stylesheet"/>
</head>
<body>
	<div class="container-fluid">
  		<div class="row content">
		<?php 
        include("../includes/leftnav2.php");
        ?>
        <div class="col-sm-9 tab-pane fade in active" id="home">
     		<h2 align="center"><i class="fa fa-upload"></i> Upload Primary Sale</h2>
			<div style="display:inline-block;float:left">
            <a href="../admin/excelexport.php?rname=<?=base64_encode("productmaster")?>&rheader=<?=base64_encode("Product Master")?>&brand=<?=base64_encode($_GET['brand'])?>&product_cat=<?=base64_encode($_GET['product_cat'])?>&product_sub_cat=<?=base64_encode($_GET['product_sub_cat'])?>&product=<?=base64_encode($_GET['product'])?>" title="Export Product details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Product details in excel"></i></a> Download Product master list <br/><br/>
            <a href="../admin/excelexport.php?rname=<?=base64_encode("locationmasterselinfo")?>&rheader=<?=base64_encode("Location Master")?>&locstate=<?=base64_encode($_POST['locationstate'])?>&loccity=<?=base64_encode($_POST['locationcity'])?>&loctype=<?=base64_encode("DS")?>&locstatus=<?=base64_encode($_POST['locationstatus'])?>" title="Export location details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export location details in excel"></i> Download Distributor list</a> <br/><br/>
            <a href="../admin/excelexport.php?rname=<?=base64_encode("locationmasterselinfo")?>&rheader=<?=base64_encode("Location Master")?>&locstate=<?=base64_encode($_POST['locationstate'])?>&loccity=<?=base64_encode($_POST['locationcity'])?>&loctype=<?=base64_encode("DL")?>&locstatus=<?=base64_encode($_POST['locationstatus'])?>" title="Export location details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export location details in excel"></i> Download Dealer list</a>            
            </div>
            
			<div style="display:inline-block;float:right"><a href="../templates/UPLOAD_PRIMARY_SALE.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div><br/>
      		<div class="form-group"  id="page-wrap" style="margin-left:10px;">
			<?php if($_REQUEST['msg']){?><br>
            <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
            <?php }?>
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
            		<div class="col-md-12"><label class="col-md-4 control-label">From Location <span class="red_small">*</span></label>
              			<div class="col-md-4">
                            <select name="from_loc" id="from_loc" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                                <option value="">--Select--</option>
                                <?php 
                                $sql_parent="select * from access_location where uid='$_SESSION[userid]' and status='Y' AND id_type IN ('HO','BR')";
                                $res_parent=mysqli_query($link1,$sql_parent);
                                while($result_parent=mysqli_fetch_array($res_parent)){
                                  $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_parent[location_id]'"));
                                ?>
                                <option data-tokens="<?=$party_det['name']." | ".$result_parent['uid']?>" value="<?=$result_parent['location_id']?>" <?php if($result_parent['location_id']==$_REQUEST['from_loc'])echo "selected";?>><?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_parent['location_id']?></option>
                                <?php
                                }
                                ?>
                            </select>
              			</div>
            		</div>
          		</div>
          		
          		<div class="form-group">
            		<div class="col-md-12"><label class="col-md-4 control-label">Remark</label>
              			<div class="col-md-4">
              				<textarea name="remark" id="rmk" class="form-control addressfield" style="resize:vertical"></textarea>
              			</div>
            		</div>
          		</div>
          
          		<div class="form-group">
            		<div class="col-md-12"><label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
              			<div class="col-md-4">
                  			<div class="input-group">
                    			<label class="input-group-btn">
                        			<span class="btn btn-primary">
                            			Browse&hellip; <input type="file" name="attchfile" class="form-control required" required style="display:none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                        			</span>
                    			</label>
                    			<input type="text" class="form-control" name="opnfile"  id="opnfile" readonly>
                			</div>
              			</div>
              			<div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
            		</div>
          		</div>
         		<div class="form-group">
            		<div class="col-md-12" align="center">
              			<input type="submit" class="btn btn-primary" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
              			&nbsp;&nbsp;&nbsp;
              			<input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='sale_uploader.php?<?=$pagenav?>'">
            		</div>
          		</div> 
    		</form>
    		</div>
    	</div>
		</div>
	</div>
<div id="loader"></div>    
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>