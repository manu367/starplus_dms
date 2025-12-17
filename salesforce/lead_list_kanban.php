<?php
require_once("../config/config.php");
///// if action taken
if($_POST['updtrf']=="Update" && $_POST['ref_no']!=""){
	@extract($_POST);
	$refid = base64_decode($ref_no);
	$refleadid = base64_decode($leadref);
	$sql_doc = "UPDATE sf_lead_master SET dept_id='".$dept."', update_by='".$_SESSION["userid"]."', update_dt='".$datetime."' WHERE lid='".$refid."'";
	$res_doc = mysqli_query($link1,$sql_doc);
	//// check if query is not executed
	if (!$res_doc) {
		$flag = false;
		$error_msg = "Error details1: " . mysqli_error($link1) . ".";
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	}else{
		mysqli_query($link1,"INSERT INTO sf_ticket_master SET lead_id='".$refleadid."', subject='Lead Transfer', internal_note='".$dept."',client_note='".$transferremark."', ticket_dt='".$today."', ticket_ip='".$ip."', ticket_loggedby='".$_SESSION['userid']."', ticket_time='".$currtime."', type='Internal Note'");
		dailyActivity($_SESSION['userid'],$refleadid,"LEAD","TRANSFER",$ip,$link1,"");
		$cflag = "success";
		$cmsg = "Success";
		$msg = "Lead ".$refleadid." is transfer to ".$dept;
	}
	header("location:lead_list_kanban.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
}
///// filter value
if($_REQUEST["status"]=="All" || $_REQUEST["status"]==""){
	$filter_status = "1";
}else{
	$filter_status = "status = '".$_REQUEST["status"]."'";
}
if($_REQUEST['fdate']!=''){
	$filter_status .= " AND tdate >= '".$_REQUEST["fdate"]."'";
}
if($_REQUEST['tdate']!=''){
	$filter_status .= " AND tdate <= '".$_REQUEST["tdate"]."'";
}
if($_REQUEST['state']!=''){
	$filter_status .= " AND party_state = '".$_REQUEST["state"]."'";
}
if($_REQUEST['party_code']!=''){
	$filter_status .= " AND partyid = '".$_REQUEST["party_code"]."'";
}
$array_newlead = array();
$array_qualifiedlead = array();
$array_quotelead = array();
$array_wonlead = array();
$array_lostlead = array();
$array_leadinfo = array();
$res_leadno = mysqli_query($link1,"SELECT * FROM sf_lead_master WHERE 1");
while($row_leadno = mysqli_fetch_assoc($res_leadno)){
	$array_leadinfo[$row_leadno['reference']] = $row_leadno['partyid']."~".$row_leadno['party_state']."~".$row_leadno['party_contact']."~".$row_leadno['party_email']."~".$row_leadno['priority']."~".$row_leadno['intial_remark']."~".$row_leadno['tdate']."~".$row_leadno['lid'];
	////// check quote and so is raised or not
	$res_quote = mysqli_query($link1,"SELECT GROUP_CONCAT(quote_no) AS quote_no, GROUP_CONCAT(so_ref_no) AS so_ref_no FROM sf_quote_master WHERE lead_ref_no='".$row_leadno['reference']."'");
	$row_quote = mysqli_fetch_assoc($res_quote);
	//// check status if open(7)/Active(18)/cold call(26)
	if($row_leadno['status']==7 || $row_leadno['status']==18 || $row_leadno['status']==26){
		$array_newlead[] = $row_leadno['reference'];
	}
	//// check status if Approve(14)/Hot(36)/warm(37)
	if($row_leadno['status']==36 || $row_leadno['status']==37 || $row_leadno['status']==14){
		$array_qualifiedlead[] = $row_leadno['reference'];
	}
	//// check status if Request For Quote(27)/Quote In Process(28)/Quote Sent(29)/Proposal(30)/Proposal Stage I(31)/Proposal Stage II(32)/Proposal Stage III(33)/Proposal Stage IV(34)/Proposal Stage V(35)
	if(($row_leadno['status']==27 || $row_leadno['status']==28 || $row_leadno['status']==29 || $row_leadno['status']==30 || $row_leadno['status']==31 || $row_leadno['status']==32 || $row_leadno['status']==33 || $row_leadno['status']==34 || $row_leadno['status']==35) && $row_quote['quote_no']!=''){
		$array_quotelead[] = $row_leadno['reference'];
	}
	//// check so ref no. should be there in quote
	if($row_quote['so_ref_no']!=''){
		$array_wonlead[] = $row_leadno['reference'];
	}
	//// check status if Cancelled(17)/Close(9) with out any further discussion
	if($row_leadno['status']==17 || ($row_leadno['status']==9 && $row_quote['quote_no']=='' && $row_quote['so_ref_no']=='')){
		$array_lostlead[] = $row_leadno['reference'];
	}
}
$task_css = array("success","warning","danger","info","success","warning","danger","info","success","warning","danger","info","success","warning","danger","info");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="../js/jquery.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel='stylesheet' href='../css/4.1.1/bootstrap.min.css'>
<title><?=siteTitle?></title>
<script type="text/javascript">
$(document).ready(function(){
	$("#frm2").validate({
	  submitHandler: function (form) {
		if(!this.wasSent){
			this.wasSent = true;
			$(':submit', form).val('Please wait...')
							  .attr('disabled', 'disabled')
							  .addClass('disabled');
			//spinner.show();				  
			form.submit();
		} else {
			return false;
		}
	  }
	});
});		
function openModel(docid){
	$.get('lead_transfer.php?id=' + docid, function(html){
		 $('#courierModel .modal-body').html(html);
		 $('#courierModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
	 $("#close_btn").html('<input type="submit" class="btn<?=$btncolor?>" name="updtrf" id="updtrf" title="Save this" value="Update" <?php if($_POST['updtrf']=='Update'){?>disabled<?php }?>/>&nbsp;<button type="button" id="btnCancel" class="btn btn-success" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>');
}
</script>
<style type="text/css">
.card {
    margin-bottom: 1.5rem;
    box-shadow: 0 .25rem .5rem rgba(0, 0, 0, .025)
}

.card-border-primary {
    border-top: 4px solid #2979ff
}

.card-border-secondary {
    border-top: 4px solid #efefef
}

.card-border-success {
    border-top: 4px solid #00c853
}

.card-border-info {
    border-top: 4px solid #3d5afe
}

.card-border-warning {
    border-top: 4px solid #ff9100
}

.card-border-danger {
    border-top: 4px solid #ff1744
}

.card-border-light {
    border-top: 4px solid #f8f9fa
}

.card-border-dark {
    border-top: 4px solid #6c757d
}
.card-header {
    border-bottom-width: 1px
}
.card-actions a {
    color: #495057;
    text-decoration: none
}

.card-actions svg {
    width: 16px;
    height: 16px
}

.card-actions .dropdown {
    line-height: 1.4
}

.card-title {
    font-weight: 500;
    margin-top: .1rem
}

.card-subtitle {
    font-weight: 400
}

.card-table {
    margin-bottom: 0
}

.card-table tr td:first-child,
.card-table tr th:first-child {
    padding-left: 1.25rem
}

.card-table tr td:last-child,
.card-table tr th:last-child {
    padding-right: 1.25rem
}

.card-img-top {
    height: 100%
}
</style>
</head>
<body>
	<div class="container-fluid">
  		<div class="row content">
			<?php 
    			include("../includes/leftnav2.php");
    		?>
    <div class="col-sm-9 container p-0" id="home">
      			<h2 align="center"><i class="fa fa-child"></i> Lead List</h2>
<?php if(isset($_REQUEST['msg'])){?>
<div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
</div>
<?php }?>
        <div class="row">
            <div class="col-12 col-lg-6 col-xl-3">
                <div class="card card-border-danger">
                    <div class="card-header alert-danger">
                        <div class="card-actions float-right">
                            <div class="dropdown show">
                                <a href="#" data-toggle="dropdown" data-display="static" title="Add new lead">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal align-middle">
                                        <circle cx="12" cy="12" r="1"></circle>
                                        <circle cx="19" cy="12" r="1"></circle>
                                        <circle cx="5" cy="12" r="1"></circle>
                                    </svg>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#" onClick="window.location.href='lead_add.php?op=add<?=$pagenav?>'">Add New Lead</a>
<!--                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>-->
                                </div>
                            </div>
                        </div>
                        <h5 class="card-title">New&nbsp;<span class="badge"><?=count($array_newlead)?></span></h5>
                        <h6 class="card-subtitle text-muted">Here all new leads comes.</h6>
                    </div>
                    <div class="card-body p-3">
						<?php
						$leadinfo = array();
						foreach($array_newlead as $newlead){
							////$row_leadno['partyid']."~".$row_leadno['party_state']."~".$row_leadno['party_contact']."~".$row_leadno['party_email']."~".$row_leadno['priority']."~".$row_leadno['intial_remark']."~".$row_leadno['tdate']."~".$row_leadno['lid']
							$leadinfo = explode("~",$array_leadinfo[$newlead]);
						?>
                        <div class="card mb-3 bg-light">
                            <div class="card-body p-2">
                            	<div class="card-actions float-right">
                                    <div class="dropdown show">
                                        <a href="#" data-toggle="dropdown" data-display="static" title="Action">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal align-middle">
                                                <circle cx="12" cy="12" r="1"></circle>
                                                <circle cx="19" cy="12" r="1"></circle>
                                                <circle cx="5" cy="12" r="1"></circle>
                                            </svg>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#" onClick="window.location.href='lead_edit.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'">Edit Lead</a>
       										<a class="dropdown-item" href="#" onClick="window.location.href='lead_status_update.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'">Update Status</a>
                                            <a class="dropdown-item" href="#" onClick="window.location.href='quote_add.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'">Share Quote</a>
                                            <a class="dropdown-item" href="#" onClick="openModel('<?=base64_encode($leadinfo[7]);?>')">Transfer</a>
                                        </div>
                                    </div>
                                </div>
                                <p style="font-size:14px"><?=$newlead?><br/>
								   <strong><?=$leadinfo[5]?></strong><br/>
								   <?=$leadinfo[0]?><br/>
                                   <?=$leadinfo[1]?><br/>
                                   <?=$leadinfo[6]?><br/>
                                   <i class="fa fa-phone fa-sm"></i>&nbsp;<?=$leadinfo[2]?><br/>
                                   <?=daysDifference($today, $leadinfo[6])." Days"?>
                                </p>
                                <div class="float-right mt-n1">
                                    <img src="https://bootdey.com/img/Content/avatar/avatar6.png" width="32" height="32" class="rounded-circle" alt="Avatar">
                                </div>
                                <a class="btn btn-outline-danger btn-sm" href="lead_view.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>">View</a>&nbsp;&nbsp;
                                <a href="mailto:<?=$leadinfo[3]?>" target="_blank"><i class="fa fa-envelope fa-lg text-info" title="<?=$leadinfo[3]?>"></i></a>&nbsp;&nbsp;
                                <a href="https://wa.me/<?=$leadinfo[2]?>" target="_blank"><i class="fa fa-whatsapp fa-lg text-success" title="<?=$leadinfo[2]?>"></i></a>
                            </div>
                        </div>
                        <?php }?>
                        <a href="#" class="btn btn-primary btn-block" onClick="window.location.href='lead_add.php?op=add<?=$pagenav?>'">Add new</a>

                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-3">
                <div class="card card-border-warning">
                    <div class="card-header alert-warning">
                        <h5 class="card-title">Qualified&nbsp;<span class="badge"><?=count($array_qualifiedlead)?></span></h5>
                        <h6 class="card-subtitle text-muted">Here all qualified leads comes.</h6>
                    </div>
                    <div class="card-body">
						<?php
						$leadinfo = array();
						foreach($array_qualifiedlead as $qualifiedlead){
							////$row_leadno['partyid']."~".$row_leadno['party_state']."~".$row_leadno['party_contact']."~".$row_leadno['party_email']."~".$row_leadno['priority']."~".$row_leadno['intial_remark']."~".$row_leadno['tdate']."~".$row_leadno['lid']
							$leadinfo = explode("~",$array_leadinfo[$qualifiedlead]);
						?>
                        <div class="card mb-3 bg-light">
                            <div class="card-body p-2">
                            	<div class="card-actions float-right">
                                    <div class="dropdown show">
                                        <a href="#" data-toggle="dropdown" data-display="static" title="Action">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal align-middle">
                                                <circle cx="12" cy="12" r="1"></circle>
                                                <circle cx="19" cy="12" r="1"></circle>
                                                <circle cx="5" cy="12" r="1"></circle>
                                            </svg>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#" onClick="window.location.href='lead_edit.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'">Edit Lead</a>
       										<a class="dropdown-item" href="#" onClick="window.location.href='lead_status_update.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'">Update Status</a>
                                            <a class="dropdown-item" href="#" onClick="window.location.href='quote_add.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'">Share Quote</a>
                                            <a class="dropdown-item" href="#" onClick="openModel('<?=base64_encode($leadinfo[7]);?>')">Transfer</a>
                                        </div>
                                    </div>
                                </div>
                                <p style="font-size:14px"><?=$qualifiedlead?><br/>
								   <strong><?=$leadinfo[5]?></strong><br/>
								   <?=$leadinfo[0]?><br/>
                                   <?=$leadinfo[1]?><br/>
                                   <?=$leadinfo[6]?><br/>
                                   <?=$leadinfo[2]?><br/>
                                   <?=daysDifference($today, $leadinfo[6])." Days"?>
                                </p>
                                <div class="float-right mt-n1">
                                    <img src="https://bootdey.com/img/Content/avatar/avatar6.png" width="32" height="32" class="rounded-circle" alt="Avatar">
                                </div>
                                <a class="btn btn-outline-warning btn-sm" href="lead_view.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>">View</a>&nbsp;&nbsp;
                                <a href="mailto:<?=$leadinfo[3]?>" target="_blank"><i class="fa fa-envelope fa-lg text-info" title="<?=$leadinfo[3]?>"></i></a>&nbsp;&nbsp;
                                <a href="https://wa.me/<?=$leadinfo[2]?>" target="_blank"><i class="fa fa-whatsapp fa-lg text-success" title="<?=$leadinfo[2]?>"></i></a>
                            </div>
                        </div>
                        <?php }?>
                        <!--<a href="#" class="btn btn-primary btn-block">Add new</a>-->

                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-3">
                <div class="card card-border-primary">
                    <div class="card-header alert-primary">
                        <h5 class="card-title">Quote&nbsp;<span class="badge"><?=count($array_quotelead)?></span></h5>
                        <h6 class="card-subtitle text-muted">Here all leads comes for which quote is sent.</h6>
                    </div>
                    <div class="card-body">
						<?php
						$leadinfo = array();
						foreach($array_quotelead as $quotelead){
							////$row_leadno['partyid']."~".$row_leadno['party_state']."~".$row_leadno['party_contact']."~".$row_leadno['party_email']."~".$row_leadno['priority']."~".$row_leadno['intial_remark']."~".$row_leadno['tdate']."~".$row_leadno['lid']
							$leadinfo = explode("~",$array_leadinfo[$quotelead]);
						?>
                        <div class="card mb-3 bg-light">
                            <div class="card-body p-2">
                            	<div class="card-actions float-right">
                                    <div class="dropdown show">
                                        <a href="#" data-toggle="dropdown" data-display="static" title="Action">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal align-middle">
                                                <circle cx="12" cy="12" r="1"></circle>
                                                <circle cx="19" cy="12" r="1"></circle>
                                                <circle cx="5" cy="12" r="1"></circle>
                                            </svg>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#" onClick="window.location.href='lead_edit.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'">Edit Lead</a>
       										<a class="dropdown-item" href="#" onClick="window.location.href='lead_status_update.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'">Update Status</a>
                                            <a class="dropdown-item" href="#" onClick="window.location.href='quote_add.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'">Share Quote</a>
                                        </div>
                                    </div>
                                </div>
                                <p style="font-size:14px"><?=$quotelead?><br/>
								   <strong><?=$leadinfo[5]?></strong><br/>
								   <?=$leadinfo[0]?><br/>
                                   <?=$leadinfo[1]?><br/>
                                   <?=$leadinfo[6]?><br/>
                                   <?=$leadinfo[2]?><br/>
                                   <?=daysDifference($today, $leadinfo[6])." Days"?>
                                </p>
                                <div class="float-right mt-n1">
                                    <img src="https://bootdey.com/img/Content/avatar/avatar2.png" width="32" height="32" class="rounded-circle" alt="Avatar">
                                </div>
                                <a class="btn btn-outline-primary btn-sm" href="lead_view.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>">View</a>&nbsp;&nbsp;
                                <a href="mailto:<?=$leadinfo[3]?>" target="_blank"><i class="fa fa-envelope fa-lg text-info" title="<?=$leadinfo[3]?>"></i></a>&nbsp;&nbsp;
                                <a href="https://wa.me/<?=$leadinfo[2]?>" target="_blank"><i class="fa fa-whatsapp fa-lg text-success" title="<?=$leadinfo[2]?>"></i></a>
                            </div>
                        </div>
                        <?php }?>
                        <!--<a href="#" class="btn btn-primary btn-block">Add new</a>-->

                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-3">
                <div class="card card-border-success">
                    <div class="card-header alert-success">
                        <h5 class="card-title">Won&nbsp;<span class="badge"><?=count($array_wonlead)?></span></h5>
                        <h6 class="card-subtitle text-muted">Here all leads comes for which sales order is raised.</h6>
                    </div>
                    <div class="card-body">
						<?php
						$leadinfo = array();
						foreach($array_wonlead as $wonlead){
							////$row_leadno['partyid']."~".$row_leadno['party_state']."~".$row_leadno['party_contact']."~".$row_leadno['party_email']."~".$row_leadno['priority']."~".$row_leadno['intial_remark']."~".$row_leadno['tdate']."~".$row_leadno['lid']
							$leadinfo = explode("~",$array_leadinfo[$wonlead]);
						?>
                        <div class="card mb-3 bg-light">
                            <div class="card-body p-2">
                            	<div class="card-actions float-right">
                                    <div class="dropdown show">
                                        <a href="#" data-toggle="dropdown" data-display="static" title="Action">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal align-middle">
                                                <circle cx="12" cy="12" r="1"></circle>
                                                <circle cx="19" cy="12" r="1"></circle>
                                                <circle cx="5" cy="12" r="1"></circle>
                                            </svg>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#" onClick="window.location.href='lead_edit.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'">Edit Lead</a>
       										<a class="dropdown-item" href="#" onClick="window.location.href='lead_status_update.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'">Update Status</a>
                                            <a class="dropdown-item" href="#" onClick="window.location.href='quote_add.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'">Share Quote</a>
                                        </div>
                                    </div>
                                </div>
                                <p style="font-size:14px"><?=$wonlead?><br/>
								   <strong><?=$leadinfo[5]?></strong><br/>
								   <?=$leadinfo[0]?><br/>
                                   <?=$leadinfo[1]?><br/>
                                   <?=$leadinfo[6]?><br/>
                                   <?=$leadinfo[2]?><br/>
                                   <?=daysDifference($today, $leadinfo[6])." Days"?>
                                </p>
                                <div class="float-right mt-n1">
                                    <img src="https://bootdey.com/img/Content/avatar/avatar6.png" width="32" height="32" class="rounded-circle" alt="Avatar">
                                </div>
                                <a class="btn btn-outline-success btn-sm" href="lead_view.php?id=<?php echo $leadinfo[7];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>">View</a>&nbsp;&nbsp;
                                <a href="mailto:<?=$leadinfo[3]?>" target="_blank"><i class="fa fa-envelope fa-lg text-info" title="<?=$leadinfo[3]?>"></i></a>&nbsp;&nbsp;
                                <a href="https://wa.me/<?=$leadinfo[2]?>" target="_blank"><i class="fa fa-whatsapp fa-lg text-success" title="<?=$leadinfo[2]?>"></i></a>
                            </div>
                        </div>
                        <?php }?>
                       <!-- <a href="#" class="btn btn-primary btn-block">Add new</a>-->
                    </div>
                </div>
            </div>
        </div>

    </div>
    
</div>
</div>    
<!-- Start Modal view -->
<div class="modal modalTH fade" id="courierModel" role="dialog">
	<form class="form-horizontal" role="form" id="frm2" name="frm2" method="post">	
		<div class="modal-dialog modal-dialogTH modal-lg">
  			<!-- Modal content-->
  			<div class="modal-content">
    			<div class="modal-header">
                	<h2 class="modal-title" align="center"><i class='fa fa-sign-out faicon'></i>&nbsp; &nbsp;Lead Transfer</h2>
      				<button type="button" class="close" data-dismiss="modal">&times;</button>
      				
    			</div>
    			<div class="modal-body modal-bodyTH">
     				<!-- here dynamic task details will show -->
    			</div>
    			<div class="modal-footer" id="close_btn">
      
    			</div> 
  			</div>
		</div>
	</form>        
</div><!--close Modal view -->    
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script src='../js/bootstrap.bundle.min.js'></script>
</body>
</html>