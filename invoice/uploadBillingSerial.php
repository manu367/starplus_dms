<?php
////// Function ID ///////
$fun_id = array("u"=>array(131)); // User:
require_once("../config/config.php");
require_once("../includes/serial_logic_function.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

function unixstamp( $excelDateTime ) {
    $d = floor( $excelDateTime ); // seconds since 1900
    $t = $excelDateTime - $d;
    return ($d > 0) ? ( $d - 25569 ) * 86400 + $t * 86400 : $t * 86400;
}
//////////////// after hitting upload button
@extract($_POST);
if ($_POST['Submit']=="Upload"){
	if ($_FILES["attchfile"]["name"]) {
		require_once "../includes/simplexlsx.class.php";
		$xlsx = new SimpleXLSX( $_FILES['attchfile']['tmp_name'] );	
		///check directory
		$dirct = "../upload_serialbill/".date("Y-m");
		if (!is_dir($dirct)) {
			mkdir($dirct, 0777, 'R');
		}
		move_uploaded_file($_FILES["attchfile"]["tmp_name"],$dirct."/".$now.$_FILES["attchfile"]["name"]);
		$f_name=$now.$_FILES["attchfile"]["name"];
		//////insert into upload file data////////////
		mysqli_query($link1,"DELETE FROM temp_bill_upload WHERE flag='' AND update_by='".$_SESSION['userid']."' AND browserid='".$browserid."'");
		mysqli_query($link1,"INSERT INTO upload_file_data SET file_name='".$f_name."', entry_date='".$today."', entry_time='".$currtime."'");
		$file_id=mysqli_insert_id($link1);
		/////
		mysqli_autocommit($link1, false);
		$flag = true;
		$err_msg="";
		////
		list($cols) = $xlsx->dimension();
		$arr_invalid = array();
		$arr_invalsr = array();	
		$ser_dup1 = array();
		$ser_billed = array();
		$total_ser = 0;
		foreach( $xlsx->rows() as $k => $r) {
	 		if ($k == 0 || $k == 1) continue; // skip first row 
	  		for( $i = 0; $i < count($k); $i++)
	  		{
		  		/// check excel row data
	      		if($r[0]=="EOF"){
			  		$eof="1";
		  		}
		  		else if($r[0]=='' || $r[1]=='' || $r[2]=='' || $r[3]=='' || $r[4]=='' || $r[5]==''){
		       
		  		}else{
	      			////Make Variable for each element of excel//////
		  			$partcode = $r[0];
		  			$serialno = $r[1];
		  			$invoiceno = $r[2];
					$invoicedt = $r[3];
					$partycode = $r[4];
					$oth = $r[5];
					$ser_dup1[$r[1]] += 1;
					$total_ser += 1;
		  			///// check serial no. length
					if($r[1]){
						//// serial no. dynamically check from product master written by shekhar on 23 JAN 23
						$check_length = getAnyDetails($r[0],"serial_length","productcode","product_master",$link1);
						//$serial_length = strFilter($r[1], 17, 17);
						$serial_length = strFilter($r[1], $check_length, $check_length);
						if(!$serial_length){
							$arr_invalid[] = $r[1]; 
							$flag = false;
						}
						////// check serial no. validation with its product code & model code written by shekhar on 20 dec 2022
						/*$resp = getValidateSerialPartcode($r[1],$r[0],$link1);
						if($resp!="Y"){
							$arr_invalsr[] = $r[1]." -- ".$resp;
						}*/
					}
					///check serial no. is in stock or not
					$res_imei=mysqli_query($link1,"SELECT owner_code,prod_code FROM billing_imei_data WHERE imei1='".$serialno."' ORDER BY id DESC");
	   				$row_imei=mysqli_fetch_assoc($res_imei);
					$num_imei=mysqli_num_rows($res_imei);
					if($row_imei['owner_code']==$biillingFrom || $num_imei==0){
						$sql="INSERT INTO temp_bill_upload SET bill_from='".$biillingFrom."', bill_to='".$partycode."', prod_code='".$partcode."', imei1='".$serialno."', imei2='".$oth."', invoiceno='".$invoiceno."', invoicedate='".$invoicedt."', update_by='".$_SESSION['userid']."', browserid='".$browserid."', file_id='".$file_id."'";
						$res = mysqli_query($link1,$sql);
						//// check if query is not executed
						if (!$res) {
							$flag = false;
							$err_msg = "Error Code1: ".mysqli_error($link1);
						}
					}else{
						$ser_billed[] = $serialno;
	      				$flag = false;
						$err_msg = "Serial no. are already billed.";
					}	
		  		}
	  		}
		}//Close For loop
		//// check duplicate
		if(count($ser_dup1)!=$total_ser){
			mysqli_rollback($link1);
			$arr_dupli = array();
			foreach($ser_dup1 as $serialno => $val){ 
				if($val>1){ 
					$arr_dupli[] = $serialno;
				}
			}
			$msg = "Dupliate Serial No. in Excel Sheet";
			///// move to parent page
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_dupli];
			header("location:uploadBillingSerial.php?".$pagenav);
			exit;
		}
		if ($flag) {
			mysqli_commit($link1);
		} else {
			mysqli_rollback($link1);
		}
		if($ser_billed){
			$msg = "Serial no. are already billed.";
			///// move to parent page
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$ser_billed];
			header("location:uploadBillingSerial.php?".$pagenav);
			exit;
		}else if($arr_invalid){
			$upd_cnt = 0;
			//$msg = "Serial nos. not having 17 digits";
			$msg = "Serial nos. not having defined digits in product master";
			///// move to parent page
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_invalid];
			header("location:uploadBillingSerial.php?".$pagenav);
			exit;
		}else if($arr_invalsr){
			$upd_cnt = 0;
			$msg = "Serial nos. validation failed";
			///// move to parent page
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_invalsr];
			header("location:uploadBillingSerial.php?".$pagenav);
			exit;		
		}else{
			//// check excel file is completely uploaded///
			if($eof=='1'){
				mysqli_query($link1,"UPDTAE upload_file_data SET status='1' WHERE id='".$file_id."'");
				header("Location:showtempserbilldata.php?msg=sucess&f_name=".$f_name."&file_id=".$file_id."&fromloc=".$biillingFrom."&rmk=".base64_encode($remark)."".$pagenav);
				exit;
			}
			else{
				////////////delete all un-valid data from temp table////////////////
				mysqli_query($link1,"DELETE FROM temp_bill_upload WHERE flag='' AND update_by='".$_SESSION['userid']."' AND browserid='".$browserid."'");
				$msg="File is not uploaded Properly.Please Upload it again.";	
				header("Location:uploadBillingSerial.php?msg=".$msg."".$pagenav);
				exit;
			}
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
	$("#frm1").validate();
});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/fileupload.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
    	<h2 align="center"><i class="fa fa-upload"></i> Upload Billing Serialwise</h2>
		<div style="display:inline-block;float:left"><a href="../admin/excelexport.php?rname=<?=base64_encode("productmaster")?>&rheader=<?=base64_encode("Product Master")?>&brand=<?=base64_encode($_REQUEST['brand'])?>&product_cat=<?=base64_encode($_REQUEST['product_cat'])?>&product_sub_cat=<?=base64_encode($_REQUEST['product_sub_cat'])?>&product=<?=base64_encode($_REQUEST['product'])?>" title="Export Product details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Product details in excel"></i></a>Download Product master list<br/>
<a href="../admin/excelexport.php?rname=<?=base64_encode("locationmaster")?>&rheader=<?=base64_encode("Location Master")?>&locstate=<?=base64_encode($_REQUEST['state'])?>&loccity=<?=base64_encode($_REQUEST['city'])?>&loctype=<?=base64_encode($_REQUEST['type'])?>&locstatus=<?=base64_encode($_REQUEST['status'])?>" title="Export Party details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Party details in excel"></i></a>Download Party master list        
        </div>
		<div style="display:inline-block;float:right"><a href="../templates/UPLOAD_SERIAL_BILLING.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div><br/><br/>
      	<div class="form-group"  id="page-wrap" style="margin-left:10px;">
      	<?php if($_REQUEST['msg']){?><br/>
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
              			<select name="biillingFrom" id="biillingFrom" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                			<option value="">--Select--</option>
							<?php 
                            $sql_parent = "SELECT * FROM access_location WHERE uid='".$_SESSION['userid']."' AND status='Y' AND id_type IN ('HO','BR')";
                            $res_parent = mysqli_query($link1,$sql_parent);
                            while($result_parent = mysqli_fetch_array($res_parent)){
                            	$party_det = mysqli_fetch_array(mysqli_query($link1,"SELECT name, city, state, id_type FROM asc_master WHERE asc_code='".$result_parent['location_id']."'"));
                            ?>
                			<option value="<?=$result_parent['location_id']?>"<?php if($result_parent['location_id']==$_REQUEST['biillingFrom'])echo "selected";?>><?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_parent['location_id']?></option>
							<?php
                            }
                            ?>
              			</select>
              		</div>
            	</div>
          	</div>
          	<?php /*?><div class="form-group">
            	<div class="col-md-12"><label class="col-md-4 control-label">From Cost Centre(Godown)<span style="color:#F00">*</span></label>
                	<div class="col-md-4">
                        <select name="stock_from" id="stock_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                        	<option value="" selected="selected">Please Select </option>
                            <?php                                 
                            $smfm_sql = "SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code='".$_REQUEST['biillingFrom']."'";
                            $smfm_res = mysqli_query($link1,$smfm_sql);
                            while($smfm_row = mysqli_fetch_array($smfm_res)){
                            ?>
                            <option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['stock_from'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
                            <?php
                            }
                            ?>
                            <?php                                 
                            $smf_sql = "SELECT sub_location, sub_location_name FROM sub_location_master WHERE main_location='".$_REQUEST['locationname']."' AND status='Active'";
                            $smf_res = mysqli_query($link1,$smf_sql);
                            while($smf_row = mysqli_fetch_array($smf_res)){
                            ?>
                            <option value="<?=$smf_row['sub_location']?>" <?php if($smf_row['sub_location']==$_REQUEST['stock_from'])echo "selected";?>><?=$smf_row['sub_location_name']." | ".$smf_row['sub_location']?></option>
                            <?php
                            }
                            ?>
                        </select>
                   	</div>
                </div>
            </div><?php */?>
          	<?php /*?><div class="form-group">
            	<div class="col-md-12"><label class="col-md-4 control-label">To Location <span class="red_small">*</span></label>
              		<div class="col-md-4">
              			<select name="biillingTo" id="biillingTo" required class="form-control selectpicker required" data-live-search="true">
                			<option value="">--Select--</option>
                			<?php 
							$sql_chl="select * from mapped_master where uid='".$_REQUEST['biillingFrom']."' and status='Y'";
							$res_chl=mysqli_query($link1,$sql_chl);
							while($result_chl=mysqli_fetch_array($res_chl)){
								  $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='".$result_chl['mapped_code']."'"));
								  if($party_det[id_type]!='HO'){
                          	?>
                    		<option value="<?=$result_chl['mapped_code']?>"<?php if($result_chl['mapped_code']==$_REQUEST['biillingTo'])echo "selected";?>><?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?></option>
                    		<?php
						  		}
							}
                    		?>
                        </select>
                    </div>
                </div>
            </div><?php */?>  
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
                    		<label class="input-group-btn"><span class="btn btn-primary">
                            Browse&hellip; <input type="file" name="attchfile" class="form-control required" required style="display:none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                        	</span></label>
                    		<input type="text" class="form-control required" required name="opnfile" id="opnfile" readonly>
                		</div>
              		</div>
              		<div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
            	</div>
          	</div>
         	<div class="form-group">
            	<div class="col-md-12" align="center">
              		<input type="submit" class="btn btn-primary" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Upload'){?>disabled<?php }?>>
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