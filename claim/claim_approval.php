<?php
////// Function ID ///////
$fun_id = array("u"=>array(134)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

$docid=base64_decode($_REQUEST['id']);
$processId=base64_decode($_REQUEST['process_id']);
//// next process id
function nextProcessId($processname,$link1){
	////// get process approval steps
	$res2 = mysqli_query($link1,"SELECT approval_steps FROM process_approval_step WHERE process_name = '".$processname."' AND status='1'");
	$row2 = mysqli_fetch_assoc($res2);
	$x = explode(",",$row2['approval_steps']);
	//// make next id array
	$next_prev_pid = array();
	for($i=0; $i<count($x); $i++){
		$next_prev_pid["nextpid"][$x[$i]] = $x[$i+1];
	}
	////make previous id array
	$y = array_reverse($x);
	for($j=0; $j<count($y); $j++){
		$next_prev_pid["prevpid"][$y[$j]] = $y[$j+1];
	}
	return $next_prev_pid;
}
//// get last process step
function lastProcessInfo($taskid,$processname,$link1){
	////// get partcode process steps
	$res2 = mysqli_query($link1,"SELECT approval_status FROM approval_status_matrix WHERE ref_no = '".$taskid."' AND process_name='".$processname."' ORDER BY id DESC");
	$row2 = mysqli_fetch_assoc($res2);
	return $row2;
}

$sql_master = "SELECT * FROM claim_master WHERE claim_no='".$docid."'";
$res_master = mysqli_query($link1,$sql_master);
$row_master = mysqli_fetch_assoc($res_master);
@extract($_POST);
////// if we hit process button
if($_POST){
	if($_POST['Submit']=='Save'){
		///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
		$messageIdent = md5($_POST['Submit'].$row_master["claim_no"]);
		//and check it against the stored value:
		$sessionMessageIdent = isset($_SESSION['msgclaimapp'])?$_SESSION['msgclaimapp']:'';
		if($messageIdent!=$sessionMessageIdent){//if its different:
			//save the session var:
			$_SESSION['msgclaimapp'] = $messageIdent;
			##########  transcation parameter ########################33
			mysqli_autocommit($link1, false);
			$flag = true;
			$err_msg = "";
			
			//////// check mandatory fields
			if($app_status != "" && $row_master["claim_no"]!=""){
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
							$newfilename = str_replace("/","_",$row_master["claim_no"])."_".$today.$now.$file_ext;
							move_uploaded_file($_FILES[$filename]["tmp_name"],$dirct."/".$newfilename);
							$file = $dirct."/".$newfilename;
							//chmod ($file, 0755);
						}
						$sql_inst = "INSERT INTO document_attachment set ref_no='".$row_master["claim_no"]."', ref_type='Claim Document',document_name='".ucwords($document_name[$k])."', document_path='".$file."', document_desc='".ucwords($document_desc[$k])."' , updatedate='".$datetime."'";
						$res_inst = mysqli_query($link1,$sql_inst);
						 //// check if query is not executed
						if (!$res_inst) {
							 $flag = false;
							 $err_msg = "Error Code0.11:".mysqli_error($link1);
						}
					}
				}				
				///// get approval step details
				$res_proc = mysqli_query($link1,"SELECT process_name FROM approval_step_master WHERE process_id = '".$processId."'");
				$row_proc = mysqli_fetch_assoc($res_proc);
				///save in history
				$flag = approvalActivity($row_master["claim_no"],$row_master["entry_date"],$row_proc['process_name'],$_SESSION['userid'],$app_status,$today,$currtime,$app_remark,$ip,$link1,$flag);
				
				///// update approval matrix
				$res2 = mysqli_query($link1,"UPDATE approval_status_matrix SET current_status = '".$row_proc['process_name']." ".$app_status."', approval_status= '".$app_status."',last_updateby='".$_SESSION['userid']."' WHERE ref_no='".$row_master["claim_no"]."' AND process_name='CLAIM' AND process_id='".$processId."'");
				//// check if query is not executed
				if (!$res2) {
					$flag = false;
					$err_msg = "Error Code0.12: ".mysqli_error($link1);
				}
				if($app_status=="Rejected"){
					$res_upd2 = mysqli_query($link1,"UPDATE claim_master SET status='Rejected' WHERE claim_no='".$row_master["claim_no"]."'");
					//// check if query is not executed
					if (!$res_upd2) {
						$flag = false;
						$err_msg = "Error Code0.14: ".mysqli_error($link1);
					}
				}else if($app_status=="Resend"){
					$res_upd2 = mysqli_query($link1,"UPDATE claim_master SET status='Resend' WHERE claim_no='".$row_master["claim_no"]."'");
					//// check if query is not executed
					if (!$res_upd2) {
						$flag = false;
						$err_msg = "Error Code0.15: ".mysqli_error($link1);
					}
					/////update
					$res_upd = mysqli_query($link1,"UPDATE approval_status_matrix SET current_status = '', last_updateby='".$_SESSION['userid']."' WHERE ref_no='".$row_master["claim_no"]."' AND process_name='CLAIM'");
					//// check if query is not executed
					if (!$res_upd) {
						$flag = false;
						$err_msg = "Error Code0.16: ".mysqli_error($link1);
					}
					$res_upd = mysqli_query($link1,"UPDATE approval_status_matrix SET current_status = 'Pending', last_updateby='".$_SESSION['userid']."' WHERE ref_no='".$row_master["claim_no"]."' AND process_name='CLAIM' ORDER BY id ASC LIMIT 1");
					//// check if query is not executed
					if (!$res_upd) {
						$flag = false;
						$err_msg = "Error Code0.17: ".mysqli_error($link1);
					}				
				}else{
					///// get next process id
					$arr_pids = nextProcessId("CLAIM",$link1);
					$nextpid = $arr_pids["nextpid"][$processId];
					if($nextpid=="" || $nextpid==NULL){
						$nextpid = $arr_pids["nextpid"][$processId];
					}
					/////check if there is no change in approval steps master
					if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM approval_status_matrix WHERE ref_no='".$row_master["claim_no"]."' AND process_name='CLAIM' AND process_id='".$nextpid."'"))==0){
						$picknextmatrixrow = mysqli_fetch_assoc(mysqli_query($link1,"SELECT process_id FROM approval_status_matrix WHERE ref_no='".$row_master["claim_no"]."' AND current_status='' ORDER BY id ASC"));
						$nextpid = $picknextmatrixrow['process_id'];
					}
					if($nextpid!=0 && $nextpid != ""){
						/////update
						$res_upd = mysqli_query($link1,"UPDATE approval_status_matrix SET current_status = 'Pending', last_updateby='".$_SESSION['userid']."' WHERE ref_no='".$row_master["claim_no"]."' AND process_name='CLAIM' AND process_id='".$nextpid."'");
						//// check if query is not executed
						if (!$res_upd) {
							$flag = false;
							$err_msg = "Error Code0.13: ".mysqli_error($link1);
						}						
					}
					////// update claim status
					$resp_last = lastProcessInfo($row_master["claim_no"],"CLAIM",$link1);
					if($resp_last["approval_status"]=="Approved"){
						$main_status = "Approved";
						$res_upd2 = mysqli_query($link1,"UPDATE claim_master SET status='Approved' WHERE claim_no='".$row_master["claim_no"]."'");
						//// check if query is not executed
						if (!$res_upd2) {
							$flag = false;
							$err_msg = "Error Code0.14: ".mysqli_error($link1);
						}					
					}else{
						$main_status = "Pending";
						$main_status = getAnyDetails($nextpid,"process_name","process_id","approval_step_master",$link1)." ".$main_status;
						$res_upd2 = mysqli_query($link1,"UPDATE claim_master SET status='".$main_status."' WHERE claim_no='".$row_master["claim_no"]."'");
						//// check if query is not executed
						if (!$res_upd2) {
							$flag = false;
							$err_msg = "Error Code0.14: ".mysqli_error($link1);
						}	
					}
				}
				////// insert in activity table////
				$flag=dailyActivity($_SESSION['userid'],$row_master["claim_no"],"CLAIM APPROVAL",$app_status,$ip,$link1,$flag);
			}
			else {
			   $flag = false;
			   $err_msg = "Mandatory field was missing";
			}
			//// check both master and data query are successfully executed
			if ($flag) {
				mysqli_commit($link1);
				$msg = "Claim is successfully ".$app_status." for ref. no.".$row_master["claim_no"];
				$cflag = "success";
            	$cmsg = "Success";
				/////// send email
				$useremail = explode("~",getAnyDetails($row_master["party_id"],"name,email","asc_code","asc_master",$link1));
				$usercc = mysqli_fetch_assoc(mysqli_query($link1,"SELECT GROUP_CONCAT(emailid) AS ccemail FROM admin_users WHERE utype='5' AND status='Active'"));
				if($useremail){
					require_once("claim_email_notification.php");
					$email_to = $useremail[1];
					$email_cc = $usercc;
					$email_bcc = "shekhar@candoursoft.com";
					$email_subject = $row_master["claim_type"]." Claim Approval with ref. no. ".$row_master["claim_no"];
					$email_title = "Claim Approval";
					$email_from = "";
					$emailmsg = "Your <b>".$row_master["claim_type"]."</b> claim with reference no. <b>".$row_master["claim_no"]."</b> of amount <b>".$row_master["total_amount"]."</b> is marked as <b>".$main_status."</b>";
					$url = $root."/email_template.php?msg=".urlencode($emailmsg)."&uname=".urlencode($useremail[0])."&title=".urlencode($email_title);
					$message = file_get_contents($url);
					/////////////////////////////////mail function////////////////////
					//$resp = send_mail_fun($message,$email_subject,$email_to,$email_cc,$email_bcc,$emailfrom);	
				}
			} else {
				mysqli_rollback($link1);
				$msg = "Request could not be processed. Please try again.".$err_msg;
				$cflag = "danger";
            	$cmsg = "Failed";
			} 
			mysqli_close($link1);
			///// move to parent page
			header("location:claim_approval_list.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
			exit;
		}else{
			//you've sent this already!
			$msg = "Re-submission was detected.";
			$cflag = "warning";
			$cmsg = "Warning";
			///// move to parent page
			header("location:claim_approval_list.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
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
	$('#dcl').click(function(){
		var chkbox = $('#dcl').is(':checked'); 
		if(chkbox){
			$('#save').removeAttr('disabled');
		}else{
			$('#save').attr('disabled', 'disabled');
		}
    });
});	
function HandleBrowseClick(ind){
    var fileinput = document.getElementById("browse"+ind);
    fileinput.click();
}
function Handlechange(ind){
	var fileinput = document.getElementById("browse"+ind);
	var textinput = document.getElementById("filename"+ind);
	textinput.value = fileinput.value;
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
</script>	
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<style type="text/css">
 .stepwizard-step p {
  margin-top: 10px;
}
.stepwizard-row {
  display: table-row;
}
.stepwizard {
  display: table;
  width: 100%;
  position: relative;
}
.stepwizard-step button[disabled] {
  opacity: 1 !important;
  filter: alpha(opacity=100) !important;
}
.stepwizard-row:before {
  top: 14px;
  bottom: 0;
  position: absolute;
  content: " ";
  width: 100%;
  height: 1px;
  background-color: #ccc;
  z-order: 0;
}
.stepwizard-step {
  display: table-cell;
  text-align: center;
  position: relative;
  /*width: 70px*/
}

.stepwizard-step p {
  position: absolute;
  width: 100%;
  text-align: center;
}
@-webkit-keyframes spinner-border{to{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}@keyframes spinner-border{to{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}.spinner-border{display:inline-block;width:2rem;height:2rem;vertical-align:-.125em;border:.25em solid currentcolor;border-right-color:transparent;border-radius:50%;-webkit-animation:.75s linear infinite spinner-border;animation:.75s linear infinite spinner-border}.spinner-border-sm{width:1rem;height:1rem;border-width:.2em}@-webkit-keyframes spinner-grow{0%{-webkit-transform:scale(0);transform:scale(0)}50%{opacity:1;-webkit-transform:none;transform:none}}@keyframes spinner-grow{0%{-webkit-transform:scale(0);transform:scale(0)}50%{opacity:1;-webkit-transform:none;transform:none}}.spinner-grow{display:inline-block;width:2rem;height:2rem;vertical-align:-.125em;background-color:currentcolor;border-radius:50%;opacity:0;-webkit-animation:.75s linear infinite spinner-grow;animation:.75s linear infinite spinner-grow}.spinner-grow-sm{width:1rem;height:1rem}@media (prefers-reduced-motion:reduce){.spinner-border,.spinner-grow{-webkit-animation-duration:1.5s;animation-duration:1.5s}}
</style>
<link href="../css/loader.css" rel="stylesheet"/>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      		<h2 align="center"><i class="fa fa-clipboard"></i> Claim Details</h2><br/>
      			<div class="form-group"  id="page-wrap" style="margin-left:10px;">
          			<form  name="form1" class="form-horizontal" action="" method="post" id="form1" enctype="multipart/form-data">
                    <div class="panel-group">
    				<div class="panel panel-info">
        				<div class="panel-heading">Party Information</div>
         				<div class="panel-body">
        				<div class="form-group">
            				<div class="col-md-6"><label class="col-md-5">Party Name</label>
              					<div class="col-md-7">
               						<?=str_replace("~"," , ",getAnyDetails($row_master["party_id"],"name,city,state,asc_code","asc_code","asc_master",$link1))?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Claim Type</label>
              					<div class="col-md-7">
          							<?=$row_master["claim_type"]?>
      							</div>
            				</div>
          				</div>
                        <div class="form-group">
            				<div class="col-md-6 alert-warning"><label class="col-md-5">Claim No.</label>
              					<div class="col-md-7">
               						<?=$row_master["claim_no"]?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Status</label>
              					<div class="col-md-7">
          							<?=$row_master["status"]?>
      							</div>
            				</div>
          				</div>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5">Entry By</label>
              					<div class="col-md-7">
               						<?=getAnyDetails($row_master["entry_by"],"name","username","admin_users",$link1)." ".$row_master["entry_by"]?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Entry Date</label>
              					<div class="col-md-7">
          							<?=$row_master["entry_date"]." ".$row_master["entry_time"]?>
      							</div>
            				</div>
          				</div>
                        <?php if($row_master["update_by"]){?>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5">Edited By</label>
              					<div class="col-md-7">
               						<?=getAnyDetails($row_master["update_by"],"name","username","admin_users",$link1)." ".$row_master["update_by"]?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Edited Date</label>
              					<div class="col-md-7">
          							<?=$row_master["update_date"]." ".$row_master["update_time"]?>
      							</div>
            				</div>
          				</div>
                        <?php }?>
                        <div class="form-group">
            				<div class="col-md-6 alert-success"><label class="col-md-5">Requested Claim Amount</label>
              					<div class="col-md-7">
               						<?=$row_master["total_amount"]?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Claim Budget</label>
              					<div class="col-md-7">
          							<?php
									$str = "";
									$claim_bgt = 0.00;
									$res_clm_bgt = mysqli_query($link1,"SELECT budget_year, budget_yearly FROM claim_budget WHERE party_id='".$row_master["party_id"]."' AND  claim_type='".$row_master["claim_type"]."' AND status='1'");
									while($row_clm_bgt = mysqli_fetch_assoc($res_clm_bgt)){
										if($str){
											$str .= ", ".$row_clm_bgt["budget_yearly"]." (<b>Year:</b> ".$row_clm_bgt["budget_year"].")";
										}else{
											$str = $row_clm_bgt["budget_yearly"]." (<b>Year:</b> ".$row_clm_bgt["budget_year"].")";
										}
										if($row_clm_bgt["budget_year"]==date("Y")){
											$claim_bgt += $row_clm_bgt["budget_yearly"];
										}
									}
									echo $str;
									?>
      							</div>
            				</div>
          				</div>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5">Already Claimed</label>
              					<div class="col-md-7">
               						<?php
									$already_claim = 0.00; 
									$res_already_clm = mysqli_query($link1,"SELECT SUM(total_amount) AS clmamt FROM claim_master WHERE party_id='".$row_master["party_id"]."' AND  claim_type='".$row_master["claim_type"]."' AND status='Approved' AND YEAR(entry_date) LIKE '".date("Y")."'");
									$row_already_clm = mysqli_fetch_assoc($res_already_clm);
									if($row_already_clm['clmamt']){ echo $already_claim = $row_already_clm['clmamt'];}else{ echo $already_claim;}
									?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Pending Claim</label>
              					<div class="col-md-7">
          							<?php 
									$pending_claim = 0.00;
									$res_pend_clm = mysqli_query($link1,"SELECT SUM(total_amount) AS clmamt FROM claim_master WHERE party_id='".$row_master["party_id"]."' AND  claim_type='".$row_master["claim_type"]."' AND status NOT IN ('Approved','Rejected') AND YEAR(entry_date) LIKE '".date("Y")."' AND claim_no!='".$row_master["claim_no"]."'");
									$row_pend_clm = mysqli_fetch_assoc($res_pend_clm);
									if($row_pend_clm['clmamt']){ echo $pending_claim = $row_pend_clm['clmamt'];}else{ echo $pending_claim;}
									?>
      							</div>
            				</div>
          				</div>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5">Rejected Claim</label>
              					<div class="col-md-7">
               						<?php 
									$reject_claim = 0.00;
									$res_rej_clm = mysqli_query($link1,"SELECT SUM(total_amount) AS clmamt FROM claim_master WHERE party_id='".$row_master["party_id"]."' AND  claim_type='".$row_master["claim_type"]."' AND status='Rejected' AND YEAR(entry_date) LIKE '".date("Y")."'");
									$row_rej_clm = mysqli_fetch_assoc($res_rej_clm);
									if($row_rej_clm['clmamt']){ echo $reject_claim = $row_rej_clm['clmamt'];}else{ echo $reject_claim;}
									?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Available Limit</label>
              					<div class="col-md-7">
          							<?php 
									echo $avl_claim = $claim_bgt - ($already_claim + $pending_claim);
									?><br/>
                                    <span style="font-size:9px">(Claim Budget-Already Claimed-Pending Claim)</span>
      							</div>
            				</div>
          				</div>
                        
                        
          				</div>
                    </div>
                    <div class="panel panel-info">
        				<div class="panel-heading">Claim Approval Hierarchy</div>
         				<div class="panel-body">
                            <div class="stepwizard">
                              <div class="stepwizard-row">
                                <?php 
								$i=1;
								$res3 = mysqli_query($link1,"SELECT process_id, current_status, approval_status FROM approval_status_matrix WHERE ref_no='".$row_master["claim_no"]."' AND process_name = 'CLAIM'");
								while($row3 = mysqli_fetch_assoc($res3)){
									$id_type = explode("~",getAnyDetails($row3['process_id'],"id_type,utype","process_id","approval_step_master",$link1));
									$res_mapusr = mysqli_query($link1,"SELECT a.username, a.name FROM admin_users a, access_location b WHERE a.username=b.uid AND a.utype='".$id_type[1]."' AND a.status='active' AND b.status='Y' AND b.location_id='".$row_master["party_id"]."'");
									$row_mapusr = mysqli_fetch_array($res_mapusr);
									$stss = "";
									if($row3['approval_status']=="Approved"){ 
										$btnclass = "btn-success"; 
										$stss = "Approved"; 
										$icn = "<i class='fa fa-check-circle-o fa-lg'></i>";
									} else if($row3['approval_status']=="Rejected"){ 
										$btnclass = "btn-danger";
										$stss = "Rejected";
										$icn = "<i class='fa fa-ban fa-lg'></i>";
									} else if($row3['approval_status']=="Resend"){ 
										$btnclass = "btn-info";
										$stss = "Resend";
										$icn = "<i class='fa fa-reply fa-lg'></i>";
									} else if($row3['current_status']=="Pending"){ 
										$btnclass = "btn-warning"; 
										$stss = "Pending";
										$icn = "<span class='spinner-grow spinner-grow-sm' role='status' aria-hidden='true'></span>";
									} else{ 
										$btnclass = "btn-default"; 
										$stss = "Pending";
										$icn = "<i class='fa fa-clock-o fa-lg'></i>";
									}
									
								?>
                                <div class="stepwizard-step">
                                  <button type="button" class="btn <?=$btnclass?>" disabled><?=$icn?>&nbsp;<?=$i?>.&nbsp;<?=$stss?></button>
                                  <p><?=$row_mapusr[1].",".$id_type[0]?><br/><?php echo $row3['current_status'];?></p>
                                </div>
                                <?php $i++;}?>
                                <br/>
                                <br/>
                                <br/>
                                <br/>
                              </div>
                            </div>
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
                                    <?php
									$i=0;
									$sql_data = "SELECT * FROM claim_data WHERE claim_no='".$docid."'";
									$res_data = mysqli_query($link1,$sql_data);
									while($row_data = mysqli_fetch_assoc($res_data)){
									?>
                    				
                        				<tr id="addr_claim<?=$i?>">
                                            <td><input type="text" readonly class="form-control entername cp required" required name="claim_subject[<?=$i?>]" id="claim_subject[<?=$i?>]" value="<?=$row_data['claim_subject']?>"></td>
                                            <td><textarea readonly class="form-control addressfield cp required" required name="claim_desc[<?=$i?>]" id="claim_desc[<?=$i?>]" style="resize:vertical"><?=$row_data['claim_desc']?></textarea></td>
                                            <td><input readonly type="text" class="form-control required" required name="claim_date[<?=$i?>]" id="claim_date0" value="<?=$row_data['claim_date']?>"></td>
                                            <td><input readonly type="text" class="form-control required digits" required name="claim_qty[<?=$i?>]" id="claim_qty[<?=$i?>]" value="<?=$row_data['qty']?>"></td>
                                            <td><input readonly type="text" class="form-control required number" required name="claim_amt[<?=$i?>]" id="claim_amt[<?=$i?>]" value="<?=$row_data['amount']?>"></td>
                        				</tr>
                    				
                                    <?php
									$i++;
									}
									?>
                                    </tbody>
                				</table>   
                			</div>
                		</div>                        
                        </div>
                    </div>
                    <?php 
						$res_bill = mysqli_query($link1,"SELECT * FROM billing_master WHERE ref_no='".$docid."'");
						if(mysqli_num_rows($res_bill)>0){
							$row_bill = mysqli_fetch_assoc($res_bill);
						?>
                        <div class="panel panel-info">
        				<div class="panel-heading">Invoice Summary</div>
         				<div class="panel-body">
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Plant Code <span class="red_small">*</span></label>
              					<div class="col-md-7">
               						<input type="text" name="plant_code" id="plant_code" value="<?=str_replace("~"," , ",getAnyDetails($row_bill["to_location"],"name,city,state,asc_code","asc_code","asc_master",$link1))?>" class="form-control mastername" autocomplete="off" readonly/>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Party GSTIN <span class="red_small">*</span></label>
              					<div class="col-md-7">
          							<input type="text" class="form-control alphanumeric required" required name="party_gstin" id="party_gstin" autocomplete="off" readonly value="<?=$row_bill['from_gst_no']?>"/>
      							</div>
            				</div>
          				</div>
		    			<div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Invoice No. <span class="red_small">*</span></label>
              					<div class="col-md-7">
               						<input type="text" name="invoice_no" id="invoice_no" class="form-control mastername" autocomplete="off" readonly value="<?=$row_bill['challan_no']?>"/>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Invoice Date <span class="red_small">*</span></label>
              					<div class="col-md-7">
                                     <input type="text" class="form-control span2" name="invoicedate" id="invoicedate" autocomplete="off" readonly value="<?=$row_bill['sale_date']?>"/>
                                </div>
            				</div>
          				</div>
			 			<div class="form-group">
                			<div class="col-sm-12">
                				<table class="table table-bordered" width="100%" id="itemsTable21">
                    				<thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th width="25%">Product</th>
                                            <th width="8%">HSN</th>
                                            <th width="8%">Qty</th>
                                            <th width="10%">Price</th>
                                            <th width="10%">Discount</th>
                                            <th width="12%">Taxable Val</th>
                                            <th width="15%">GST</th>
                                            <th width="12%">Total</th>
                                        </tr>
                    				</thead>
                    				<tbody>
                                    	<?php 
										$k=0;
										$sql_invdata = "SELECT * FROM billing_model_data WHERE challan_no='".$row_bill['challan_no']."'";
										$res_invdata = mysqli_query($link1,$sql_invdata);
										while($row_invdata = mysqli_fetch_assoc($res_invdata)){
											$proddet=explode("~",getProductDetails($row_invdata['prod_code'],"productname,productcode",$link1));
										?>
                        				<tr id="addr_inv<?=$k?>">
                                            <td><input type="text" class="form-control" name="prod_code[<?=$k?>]" id="prod_code[<?=$k?>]" autocomplete="off" value="<?=$proddet[0].", ".$proddet[1]?>" style="text-align:left" readonly></td>
                                            <td><input type="text" class="form-control" name="hsn[<?=$k?>]" id="hsn[<?=$k?>]" autocomplete="off" value="<?=$row_invdata['combo_code']?>" style="text-align:left" readonly></td>
                                            <td><input type="text" class="form-control number" name="bill_qty[<?=$k?>]" id="bill_qty[<?=$k?>]" value="<?=$row_invdata['qty']?>" autocomplete="off" style="text-align:right" readonly></td>
                                            <td><input type="text" class="form-control number" name="price[<?=$k?>]" id="price[<?=$k?>]" value="<?=$row_invdata['price']?>" autocomplete="off" style="text-align:right" readonly></td>
                                            <td><input type="text" class="form-control number" name="rowdiscount[<?=$k?>]" id="rowdiscount[<?=$k?>]" value="<?=$row_invdata['discount']?>" autocomplete="off" style="text-align:right" readonly></td>
                                            <td><input type="text" class="form-control" name="rowsubtotal[<?=$k?>]" id="rowsubtotal[<?=$k?>]" autocomplete="off" value="<?=$row_invdata['value']-$row_invdata['discount']?>" style="text-align:right" readonly></td>
                                            <td><?php if($row_bill['from_state']==$row_bill['to_state']){ ?>
                                              	<div class="row">
                                                	<div class="col-md-4">
                                               		<input type="text" class="form-control" name="rowsgstper[<?=$k?>]" id="rowsgstper[<?=$k?>]" value="<?=$row_invdata['sgst_per']?>" readonly style="width:50px;text-align:right;padding: 4px">
                                                	</div>
                                                	<div class="col-md-4">
                                                	<input type="text" class="form-control" name="rowsgstamount[<?=$k?>]" id="rowsgstamount[<?=$k?>]" value="<?=$row_invdata['sgst_amt']?>" readonly style="width:80px;text-align:right;padding: 4px">					
                                                </div>
                                                </div>
                                                
                                                
                                                <div class="row">
                                                	<div class="col-md-4">
                                                    <input type="text" class="form-control" name="rowcgstper[<?=$k?>]" id="rowcgstper[<?=$k?>]" value="<?=$row_invdata['cgst_per']?>" readonly style="width:50px;text-align:right;padding: 4px">
                                                    </div>
                                                	<div class="col-md-4">
                                                    <input type="text" class="form-control" name="rowcgstamount[<?=$k?>]" id="rowcgstamount[<?=$k?>]" value="<?=$row_invdata['cgst_amt']?>" readonly style="width:80px;text-align:right;padding: 4px">
                                                    </div>
                                               </div>
                                                <?php }else{?>
                                                <div class="row">
                                                	<div class="col-md-4">
                                                	<input type="text" class="form-control" name="rowigstper[<?=$k?>]" id="rowigstper[<?=$k?>]" value="<?=$row_invdata['igst_per']?>" readonly style="width:50px;text-align:right;padding: 4px">
                                                	</div>
                                                    <div class="col-md-4">
                                                	<input type="text" class="form-control" name="rowigstamount[<?=$k?>]" id="rowigstamount[<?=$k?>]" value="<?=$row_invdata['igst_amt']?>" readonly style="width:60px;text-align:right;padding: 4px">
                                                    </div>
                                                </div>
                                                <?php }?></td>
                                            <td><input type="text" class="form-control" name="total_val[<?=$k?>]" id="total_val[<?=$k?>]" value="<?=$row_invdata['totalvalue']?>" autocomplete="off" readonly  style="text-align:right"></td>
                        				</tr>
                                        <?php 
										$tot_qty += $row_invdata['qty'];
										
										}?>
                                        <tr>
                        				  <td colspan="2" align="right"><strong>Total</strong></td>
                        				  <td align="right"><strong><?=$tot_qty?></strong></td>
                        				  <td>&nbsp;</td>
                        				  <td align="right"><strong><?=$row_bill['discount_amt']?></strong></td>
                        				  <td align="right"><strong><?=$row_bill['basic_cost']-$row_bill['discount_amt']?></strong></td>
                        				  <td align="right"><strong><?=$row_bill['total_sgst_amt']+$row_bill['total_cgst_amt']+$row_bill['total_igst_amt']?></strong></td>
                        				  <td align="right"><strong><?=$row_bill['total_cost']?></strong></td>
                      				  	</tr>
                    				</tbody>
                				</table>   
               			  </div>
                	    </div>
                        </div>
                        </div>
                        <?php }?>
                   	<div class="panel panel-info table-responsive">
        				<div class="panel-heading">Uploaded Supporting Document</div>
         				<div class="panel-body">
		    
			 			<div class="form-group">
                			<div class="col-sm-12">
                				<table class="table table-bordered" width="100%" id="itemsTable2">
                    				<thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th width="30%">Document Name</th>
                                            <th width="30%">Description</th>
                                            <th width="40%">Attachment</th>
                                        </tr>
                    				</thead>
                    				<tbody>
                                    	<?php
										$j=0;
										$sql_data_doc = "SELECT * FROM document_attachment WHERE ref_no='".$docid."'";
										$res_data_doc = mysqli_query($link1,$sql_data_doc);
										while($row_data_doc = mysqli_fetch_assoc($res_data_doc)){
										?>
                        				<tr id="addr_doc<?=$j?>">
                                            <td><input type="text" readonly class="form-control entername cp"  id="upd_document_name[<?=$j?>]" value="<?=$row_data_doc['document_name']?>"></td>
                                            <td><input type="text" readonly class="form-control entername cp"  id="upd_document_desc[<?=$j?>]" value="<?=$row_data_doc['document_desc']?>"></td>
                                            <td><a href="<?=$row_data_doc['document_path']?>" target="_blank" class="btn <?=$btncolor?>" title="Attachment"><i class="fa fa-paperclip" title="Attachment"></i></a></td>
                        				</tr>
                                        <?php
										$j++;
										}
										?>
                    				</tbody>
                				</table>   
                			</div>
                		</div>

						</div>
                    </div>
                    <?php
					$res_poapp = mysqli_query($link1,"SELECT * FROM approval_activities where ref_no='".$docid."'")or die("ERR1".mysqli_error($link1)); 
					if(mysqli_num_rows($res_poapp)>0){
					?>
                    <div class="panel panel-info table-responsive">
        				<div class="panel-heading">Approval History</div>
         				<div class="panel-body">
		    
			 			<div class="form-group">
                			<div class="col-sm-12">
                				<table class="table table-bordered" width="100%" id="itemsTable3">
                    				<thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th width="20%">Action Date & Time</th>
                                            <th width="30%">Action Taken By</th>
                                            <th width="20%">Action</th>
                                            <th width="30%">Action Remark</th>
                                        </tr>
                    				</thead>
                    				<tbody>
                        				<?php
										while($row_poapp=mysqli_fetch_assoc($res_poapp)){
										?>
										  <tr>
											<td><?php echo $row_poapp['action_date']." ".$row_poapp['action_time'];?></td>
											<td><?php echo getAdminDetails($row_poapp['action_by'],"name",$link1);?></td>
											<td><?php echo $row_poapp['req_type']." ".$row_poapp['action_taken']?></td>
											<td><?php echo $row_poapp['action_remark']?></td>
										  </tr>
                                        <?php
										}
										?>  
                    				</tbody>
                				</table>   
                			</div>
                		</div>
                    	</div> 
                    </div>
                    <?php 
					}
					?>
                    <div class="panel panel-info table-responsive">
        				<div class="panel-heading">Other Document</div>
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
            				<div class="col-md-6"><label class="col-md-5">Approval Status <span class="red_small">*</span></label>
              					<div class="col-md-7">
               						<select name="app_status" id="app_status" class="form-control required" required>
                                    	<option value="">--Please Select--</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Rejected">Rejected</option>
                                        <option value="Resend">Resend</option>
                                    </select>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Remark</label>
              					<div class="col-md-7">
          							<textarea class="form-control addressfield cp required" required name="app_remark" id="app_remark" style="resize:vertical"></textarea>
      							</div>
            				</div>
          				</div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <input type="checkbox" name="declaration" id="dcl" class="checkbox-inline"/>&nbsp;<span class="text-danger">I hereby declare that all the information furnished above is true to the best of my belief</span>
                            </div>
          				</div>
                		<div class="form-group">
           					<div class="col-sm-4" style="display:inline-block; float:left">
           						<a id="add_row3" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add More Attachment</a>
                                <input type="hidden" name="rowno3" id="rowno3" value="0"/>
                          	</div>
            				<div class="col-md-8" style="display:inline-block; float:right" align="left">
                                <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Save" title="" disabled/>
                                <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='claim_approval_list.php?<?=$pagenav?>'">
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