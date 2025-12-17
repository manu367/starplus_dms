<?php
////// Function ID ///////
$fun_id = array("u"=>array(133)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

@extract($_POST);
////// if we hit process button
if($_POST){
	if($_POST['Submit']=='Save'){
		///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
		$messageIdent = md5($_POST['Submit'].$party_code);
		//and check it against the stored value:
		$sessionMessageIdent = isset($_SESSION['msgclaimreq'])?$_SESSION['msgclaimreq']:'';
		if($messageIdent!=$sessionMessageIdent){//if its different:
			//save the session var:
			$_SESSION['msgclaimreq'] = $messageIdent;
			##########  transcation parameter ########################33
			mysqli_autocommit($link1, false);
			$flag = true;
			$err_msg = "";
			
			//////// check mandatory fields
			if($party_code != "" && $claim_type != ""){
				////// select
				$sql1 ="SELECT MAX(tempid) AS qa FROM claim_master WHERE YEAR(entry_date) = '".date("Y")."'";
				$res1 = mysqli_query($link1,$sql1)or die("ER1 making ref no. ".mysqli_error($link1));
				$row1 = mysqli_fetch_array($res1);
				$cod1 = $row1['qa']+1;
				/// make 6 digit padding
				$pad1 = str_pad($cod1,6,"0",STR_PAD_LEFT);
				//// make logic of claim no.
				$claimno = "CL/".date("Ymd")."/".$pad1;
				///////
				$total_qty = 0;
				$total_amt = 0.00;
				///// claim data table update
				foreach($claim_subject as $j=>$value){
					if($claim_subject[$j]){
						$sql_data = "INSERT INTO claim_data SET claim_no='".$claimno."', claim_subject='".$claim_subject[$j]."', claim_desc='".$claim_desc[$j]."', claim_date='".$claim_date[$j]."' ,qty='".$claim_qty[$j]."', amount='".$claim_amt[$j]."'";
						$res_data = mysqli_query($link1,$sql_data);
					   //// check if query is not executed
						if (!$res_data) {
							 $flag = false;
							 $err_msg = "Error Code0.2: ".mysqli_error($link1);
						}
						$total_qty += $claim_qty[$j];
						$total_amt += $claim_amt[$j];
					}
				}
				////// entry in master table 
				$sql_master = "INSERT INTO claim_master SET claim_no='".$claimno."', tempid='".$cod1."', claim_type='".$claim_type."', party_id='".$party_code."', entry_date='".$today."' ,entry_time='".$currtime."', entry_by='".$_SESSION['userid']."', entry_ip='".$ip."', total_qty='".$total_qty."', total_amount='".$total_amt."', status='Pending' ,remark=''";
				$res_master = mysqli_query($link1,$sql_master);
			   //// check if query is not executed
				if (!$res_master) {
					 $flag = false;
					 $err_msg = "Error Code0.1: ".mysqli_error($link1);
				}
				///check directory
				$dirct = "../claim_doc/".date("Y-m");
				if (!is_dir($dirct)) {
					mkdir($dirct, 0777, 'R');
				}
				///// Insert in document attach detail by picking each data row one by one
				foreach($document_name as $k=>$val){
					////////////////upload file
					$filename = "fileupload".$k;
					$file_name = $_FILES[$filename]["name"];
					if($file_name){
						//$file_basename = substr($file_name, 0, strripos($file_name, '.')); // get file extention
						$file_ext = substr($file_name, strripos($file_name, '.')); // get file name
						//////upload image
						if ($_FILES[$filename]["error"] > 0){
							$code=$_FILES[$filename]["error"];
						}
						else{
							// Rename file
							$newfilename = str_replace("/","_",$claimno)."_".$today.$now.$file_ext;
							move_uploaded_file($_FILES[$filename]["tmp_name"],$dirct."/".$newfilename);
							$file = $dirct."/".$newfilename;
							//chmod ($file, 0755);
						}
						$sql_inst = "INSERT INTO document_attachment set ref_no='".$claimno."', ref_type='Claim Document',document_name='".ucwords($document_name[$k])."', document_path='".$file."', document_desc='".ucwords($document_desc[$k])."' , updatedate='".$datetime."'";
						$res_inst = mysqli_query($link1,$sql_inst);
						 //// check if query is not executed
						if (!$res_inst) {
							 $flag = false;
							 $err_msg = "Error Code0.11:".mysqli_error($link1);
						}
					}
				}
				$main_status = "Pending";
				///// App steps
				$app_steps = mysqli_fetch_assoc(mysqli_query($link1,"SELECT approval_steps FROM process_approval_step WHERE process_name='CLAIM' AND status='1'"));
				$arr_steps = explode(",",$app_steps['approval_steps']);
				///entry for each steps
				for($j=0; $j<count($arr_steps); $j++){
					/////
					if($j==0){ 
					   	///// get status
						$main_status = getAnyDetails($arr_steps[$j],"process_name","process_id","approval_step_master",$link1)." ".$main_status;
						$currstatus = "Pending";}else{ $currstatus = "";}
					$result5 = mysqli_query($link1,"INSERT INTO approval_status_matrix SET ref_no ='".$claimno."', process_name = 'CLAIM', process_id='".$arr_steps[$j]."', current_status='".$currstatus."'");
					if (!$result5) {
						$flag = false;
						$err_msg = "Error Code5: ".mysqli_error($link1);
					}
				}
				////// update main status in master table
				$main_master = mysqli_query($link1,"UPDATE claim_master SET status='".$main_status."' WHERE claim_no ='".$claimno."'");
				if (!$main_master) {
					$flag = false;
					$err_msg = "Error Code6: ".mysqli_error($link1);
				}
				////// insert in activity table////
				$flag=dailyActivity($_SESSION['userid'],$claimno,"CLAIM","ADD",$ip,$link1,$flag);
			}
			else {
			   $flag = false;
			   $err_msg = "Mandatory field was missing";
			}
			//// check both master and data query are successfully executed
			if ($flag) {
				mysqli_commit($link1);
				$msg = "Claim is successfully created with ref. no.".$claimno;
				$cflag = "success";
            	$cmsg = "Success";
				/////// send email
				$useremail = explode("~",getAnyDetails($party_code,"name,email","asc_code","asc_master",$link1));
				$usercc = mysqli_fetch_assoc(mysqli_query($link1,"SELECT GROUP_CONCAT(emailid) AS ccemail FROM admin_users WHERE utype='5' AND status='Active'"));
				if($useremail){
					require_once("claim_email_notification.php");
					$email_to = $useremail[1];
					$email_cc = $usercc;
					$email_bcc = "shekhar@candoursoft.com";
					$email_subject = $claim_type." Claim Generated with ref. no. ".$claimno;
					$email_title = "Claim Generation";
					$email_from = "";
					$emailmsg = "You have created a <b>".$claim_type."</b> claim with reference no. <b>".$claimno."</b> of amount <b>".$total_amt."</b>";
					$url = $root."/email_template.php?msg=".urlencode($emailmsg)."&uname=".urlencode($useremail[0])."&title=".urlencode($email_title);
					$message = file_get_contents($url);
					/////////////////////////////////mail function////////////////////
					$resp = send_mail_fun($message,$email_subject,$email_to,$email_cc,$email_bcc,$emailfrom);	
				}
			} else {
				mysqli_rollback($link1);
				$msg = "Request could not be processed. Please try again.".$err_msg;
				$cflag = "danger";
            	$cmsg = "Failed";
			} 
			mysqli_close($link1);
			///// move to parent page
			header("location:claim_list.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
			exit;
		}else{
			//you've sent this already!
			$msg = "Re-submission was detected.";
			$cflag = "warning";
			$cmsg = "Warning";
			///// move to parent page
			header("location:claim_list.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
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
<title><?= siteTitle ?></title>
<script src="../js/jquery.js"></script>
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
    $("#form1").validate({
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
	$('#claim_date0').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?=$today?>",
		todayHighlight: true,
		autoclose: true
	});
});
function makeClaimDate(ind){
	$('#claim_date'+ind).datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?=$today?>",
		todayHighlight: true,
		autoclose: true,
	});
}
</script>
<script language="javascript" type="text/javascript"> 
function HandleBrowseClick(ind){
    var fileinput = document.getElementById("browse"+ind);
    fileinput.click();
}
function Handlechange(ind){
	var fileinput = document.getElementById("browse"+ind);
	var textinput = document.getElementById("filename"+ind);
	textinput.value = fileinput.value;
}
///// add new row for claim summary
$(document).ready(function() {
	$("#add_row2").click(function() {		
		var numi = document.getElementById('rowno2');
		var itm = "claim_subject[" + numi.value+"]";
		var preno=document.getElementById('rowno2').value;
		var num = (document.getElementById("rowno2").value -1)+2;
		numi.value = num;
		if ((document.getElementById(itm).value != "") || ($("#addr_claim" + numi.value + ":visible").length == 0)) {
			var r = '<tr id="addr_claim'+num+'"><td><i class="fa fa-close fa-lg" onClick="fun_remove2('+num+');"></i><input type="text" class="form-control entername cp required" required name="claim_subject['+num+']" id="claim_subject['+num+']" value=""></td><td><textarea class="form-control addressfield cp required" required name="claim_desc['+num+']" id="claim_desc['+num+']" style="resize:vertical"></textarea></td><td><input type="text" class="form-control required" required name="claim_date['+num+']" id="claim_date'+num+'" value="<?=$today?>"></td><td><input type="text" class="form-control required digits" required name="claim_qty['+num+']" id="claim_qty['+num+']" value="1"></td><td><input type="text" class="form-control required number" required name="claim_amt['+num+']" id="claim_amt['+num+']" value=""></td></tr>';
			$('#itemsTable2').append(r);
			makeClaimDate(num);
		}
	});
});
function fun_remove2(con){
	var c = document.getElementById('addr_claim' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('rowno2').value = con;
}
///// add new row for document attachment
$(document).ready(function() {
	$("#add_row3").click(function() {		
		var numi = document.getElementById('rowno3');
		var itm = "document_name[" + numi.value+"]";
		var preno=document.getElementById('rowno3').value;
		var num = (document.getElementById("rowno3").value -1)+2;
		numi.value = num;
		if ((document.getElementById(itm).value != "") || ($("#addr_doc" + numi.value + ":visible").length == 0)) {
			var r = '<tr id="addr_doc'+num+'"><td><i class="fa fa-close fa-lg" onClick="fun_remove3('+num+');"></i><input type="text" class="form-control entername cp" name="document_name['+num+']" id="document_name['+num+']" value=""></td><td><input type="text" class="form-control entername  cp" name="document_desc['+num+']"  id="document_desc['+num+']" value=""></td><td><div style="display:inline-block; float:left"><input type="file" id="browse'+num+'" name="fileupload'+num+'" style="display: none" onChange="Handlechange('+num+');" accept="image/*"/><input type="text" id="filename'+num+'" readonly="true" style="width:300px;" class="form-control"/></div><div style="display:inline-block; float:left">&nbsp;&nbsp;<input type="button" value="Click to upload attachment" id="fakeBrowse'+num+'" onclick="HandleBrowseClick('+num+');" class="btn btn-warning"/></div></td></tr>';
			$('#itemsTable3').append(r);
		}
	});
});
function fun_remove3(con){
	var c = document.getElementById('addr_doc' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('rowno3').value = con;
}
function confirmDel(store){
	var where_to= confirm("Are you sure to delete this document?");
	if (where_to== true)
	{
		//alert(window.location.href)
		var url="<?php echo $url ?>";
		window.location=url+store;
	}
	else
	{
		return false;
	}
}
</script>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<link href="../css/loader.css" rel="stylesheet"/>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      		<h2 align="center"><i class="fa fa-clipboard"></i> Add New Claim</h2><br/>
      			<div class="form-group"  id="page-wrap" style="margin-left:10px;">
          			<form  name="form1" class="form-horizontal" action="" method="post" id="form1" enctype="multipart/form-data">
                    <div class="panel-group">
    				<div class="panel panel-info">
        				<div class="panel-heading">Party Information</div>
         				<div class="panel-body">
        				<div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Party Name <span class="red_small">*</span></label>
              					<div class="col-md-7">
               						<select name="party_code" id="party_code" required class="form-control selectpicker required" data-live-search="true" onChange="document.form1.submit()">
                                    	<option value="" selected="selected">Please Select </option>
										<?php
                                        $sql_parent = "select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y'";
                                        $res_parent = mysqli_query($link1, $sql_parent);
                                        while ($result_parent = mysqli_fetch_array($res_parent)) {   
                                            $party_det = mysqli_fetch_array(mysqli_query($link1, "select name , city, state,id_type from asc_master where asc_code='" . $result_parent['location_id'] . "'"));
											if($party_det['name']){
                                        ?>
                                        <option value="<?= $result_parent['location_id']?>" <?php if ($result_parent['location_id'] == $_REQUEST['party_code']) echo "selected"; ?> ><?= $party_det['name'] . " | " . $party_det['city'] . " | " . $party_det['state'] . " | " . $result_parent['location_id']?></option>
                                     	<?php
											}
                                        }
                                        ?>
                            		</select>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Claim Type <span class="red_small">*</span></label>
              					<div class="col-md-7">
          							<select name="claim_type" id="claim_type" required class="form-control selectpicker required" data-live-search="true" onChange="document.form1.submit()">
                                    	<option value="" selected="selected">Please Select </option>
										<?php
                                        $sql_claim = "select id,claim_type from claim_type_master where status='1'";
                                        $res_claim = mysqli_query($link1, $sql_claim);
                                        while ($row_claim = mysqli_fetch_array($res_claim)) {   
                                        ?>
                                        <option value="<?= $row_claim['claim_type']?>" <?php if ($row_claim['claim_type'] == $_REQUEST['claim_type']) echo "selected"; ?> ><?= $row_claim['claim_type']?></option>
                                     	<?php
                                        }
                                        ?>
                            		</select>
      							</div>
            				</div>
          				</div>
                        <?php
						$res_clm_bgt = mysqli_query($link1,"SELECT budget_year, budget_yearly FROM claim_budget WHERE party_id='".$_REQUEST['party_code']."' AND  claim_type='".$_REQUEST['claim_type']."' AND status='1'");
						while($row_clm_bgt = mysqli_fetch_assoc($res_clm_bgt)){
						?>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Budget Year</label>
              					<div class="col-md-7">
               						<input type="text" class="form-control" name="claim_bgt_year" id="claim_bgt_year" readonly value="<?=$row_clm_bgt['budget_year']?>">
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Claim Budget</label>
              					<div class="col-md-7">
          							<input type="text" class="form-control number" name="claim_bgt" id="claim_bgt" readonly value="<?=$row_clm_bgt['budget_yearly']?>">
      							</div>
            				</div>
          				</div>
                        <?php }?>
          				</div>
                        </div>
                        <div class="panel panel-info table-responsive">
        				<div class="panel-heading">Claim Summary</div>
         				<div class="panel-body">
		    
			 			<div class="form-group">
                			<div class="col-sm-12">
                				<table class="table table-bordered" width="100%" id="itemsTable2">
                    				<thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th width="20%">Subject</th>
                                            <th width="25%">Description</th>
                                            <th width="15%">Date</th>
                                            <th width="20%">Nos.</th>
                                            <th width="20%">Amount</th>
                                        </tr>
                    				</thead>
                    				<tbody>
                        				<tr id="addr_claim0">
                                            <td><input type="text" class="form-control entername cp required" required name="claim_subject[0]" id="claim_subject[0]" value=""></td>
                                            <td><textarea class="form-control addressfield cp required" required name="claim_desc[0]" id="claim_desc[0]" style="resize:vertical"></textarea></td>
                                            <td><input type="text" class="form-control required" required name="claim_date[0]" id="claim_date0" value="<?=$today?>"></td>
                                            <td><input type="text" class="form-control required digits" required name="claim_qty[0]" id="claim_qty[0]" value="1"></td>
                                            <td><input type="text" class="form-control required number" required name="claim_amt[0]" id="claim_amt[0]" value=""></td>
                        				</tr>
                    				</tbody>
                				</table>   
                			</div>
                		</div>
                		<div class="form-group">
           					<div class="col-sm-4" style="display:inline-block; float:left">
           						<a id="add_row2" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add More Line</a>
                                <input type="hidden" name="rowno2" id="rowno2" value="0"/>
                          	</div>
          				</div>
                        
                        </div>
                        </div>
                   		<div class="panel panel-info table-responsive">
        				<div class="panel-heading">Supporting Document</div>
         				<div class="panel-body">
		    
			 			<div class="form-group">
                			<div class="col-sm-12">
                				<table class="table table-bordered" width="100%" id="itemsTable3">
                    				<thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th width="25%">Document Name</th>
                                            <th width="20%">Description</th>
                                            <th width="50%">Attachment</th>
                                        </tr>
                    				</thead>
                    				<tbody>
                        				<tr id="addr_doc0">
                                            <td><input type="text" class="form-control entername cp" name="document_name[0]"  id="document_name[0]" value=""></td>
                                            <td><input type="text" class="form-control entername cp" name="document_desc[0]"  id="document_desc[0]" value=""></td>
                                            <td>
                                                <div style="display:inline-block; float:left">
                                                <input type="file" class="required" id="browse0" name="fileupload0" style="display: none" onChange="Handlechange(0);" accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf"/>
                                                <input type="text" id="filename0" readonly style="width:300px;" class="form-control "/>
                                                </div><div style="display:inline-block; float:left">&nbsp;&nbsp;
                                                <input type="button" value="Click to upload attachment" id="fakeBrowse0" onClick="HandleBrowseClick(0);" class="btn btn-warning"/>
                                                </div>
                            				</td>
                        				</tr>
                    				</tbody>
                				</table>   
                			</div>
                		</div>
                		<div class="form-group">
           					<div class="col-sm-4" style="display:inline-block; float:left">
           						<a id="add_row3" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add More Attachment</a>
                                <input type="hidden" name="rowno3" id="rowno3" value="0"/>
                          	</div>
            				<div class="col-md-8" style="display:inline-block; float:right" align="left">
                                <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Save" title="" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
                                <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='claim_list.php?<?=$pagenav?>'">
            				</div>
          				</div>
                        
                        </div>
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
?>
</body>
</html>

<script type="text/javascript">
	///// function for checking duplicate Product value
	function checkDuplicate(fldIndx1, enteredsno) { 
	 document.getElementById("save").disabled = false;
		if (enteredsno != '') {
			var check2 = "document_name[" + fldIndx1 + "]";
			var flag = 1;
			for (var i = 0; i <= fldIndx1; i++) {
				var check1 = "document_name[" + i + "]";
				if (fldIndx1 != i && (document.getElementById(check2).value == document.getElementById(check1).value )){
					if ((document.getElementById(check2).value == document.getElementById(check1).value)) {
						alert("Duplicate Document Selection.");
						document.getElementById(check2).value = '';
						document.getElementById(check2).style.backgroundColor = "#F66";
						flag *= 0;
					}
					else {
						document.getElementById(check2).style.backgroundColor = "#FFFFFF";
						flag *= 1;
						///do nothing
					}
				}
			}//// close for loop
			if (flag == 0) {
				return false;
			} else {
				return true;
			}
		}	
	}
	</script>