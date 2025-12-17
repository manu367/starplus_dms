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
	header("location:lead_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
$array_lead = array();
$res_statuscnt = mysqli_query($link1,"SELECT COUNT(lid) as lead_cnt, status FROM sf_lead_master group by status");
while($row_statuscnt = mysqli_fetch_assoc($res_statuscnt)){
	$array_lead[$row_statuscnt["status"]] = $row_statuscnt["lead_cnt"];
}
$arr_state = array();
$arr_party = array();
$res_mkdata = mysqli_query($link1,"SELECT partyid, party_state FROM sf_lead_master where 1");
while($row_mkdata = mysqli_fetch_assoc($res_mkdata)){
	$arr_state[] = $row_mkdata["party_state"];
	$arr_party[] = $row_mkdata["partyid"];
}
$arr_state = array_unique($arr_state);
$arr_party = array_unique($arr_party);

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
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="../css/bootstrap-select.min.css">
<script src="../js/bootstrap-select.min.js"></script>
<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function(){
    $('#myTable').dataTable({
		 "searching": false,
		 "ordering": false,
		 "lengthChange": false,
	});
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
function delete_lead(id){
	//alert(id);
 	var x = confirm("Do you want to delete this lead ?");
  	if(x){
		window.location="lead_delete.php?id="+id+"&str=del_lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>"; 
   	}
}
function go_page(lid,id){
	if(document.getElementById('action'+id).value!=''){
		var action=document.getElementById('action'+id).value;
 		if(action=='change_status'){
	 		window.location.href='lead_status_update.php?id='+lid+"&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>";
		} else if(action=='Approval'){
	 		window.location.href='lead_approval.php?id='+lid+"&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>";
		}else if(action=='Share Quote'){
	 		window.location.href='quote_add.php?id='+lid+"&tab=0&status=<?=$_REQUEST["status"]?><?=$pagenav?>";
		} 
	}
}
////// function for open modal to check process steps of jobs
function openModel(docid){
	$.get('lead_status_history.php?id=' + docid, function(html){
		 $('#courierModel .modal-body').html(html);
		 $('#courierModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
	 $("#close_btn").html('<button type="button" id="btnCancel" class="btn btn-success" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>');
}
$(document).ready(function(){
    $('.filterable .btn-filter').click(function(){
        var $panel = $(this).parents('.filterable'),
        $filters = $panel.find('.filters input'),
        $tbody = $panel.find('.table tbody');
        if ($filters.prop('disabled') == true) {
            $filters.prop('disabled', false);
            $filters.first().focus();
        } else {
            $filters.val('').prop('disabled', true);
            $tbody.find('.no-result').remove();
            $tbody.find('tr').show();
        }
    });

    $('.filterable .filters input').keyup(function(e){
        /* Ignore tab key */
        var code = e.keyCode || e.which;
        if (code == '9') return;
        /* Useful DOM data and selectors */
        var $input = $(this),
        inputContent = $input.val().toLowerCase(),
        $panel = $input.parents('.filterable'),
        column = $panel.find('.filters th').index($input.parents('th')),
        $table = $panel.find('.table'),
        $rows = $table.find('tbody tr');
        /* Dirtiest filter function ever ;) */
        var $filteredRows = $rows.filter(function(){
            var value = $(this).find('td').eq(column).text().toLowerCase();
            return value.indexOf(inputContent) === -1;
        });
        /* Clean previous no-result if exist */
        $table.find('tbody .no-result').remove();
        /* Show all rows, hide filtered ones (never do that outside of a demo ! xD) */
        $rows.show();
        $filteredRows.hide();
        /* Prepend no-result row if all rows are filtered */
        if ($filteredRows.length === $rows.length) {
            $table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="'+ $table.find('.filters th').length +'">No result found</td></tr>'));
        }
    });
});
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
function openModel2(docid){
	$.get('lead_transfer.php?id=' + docid, function(html){
		 $('#courierModel2 .modal-body').html(html);
		 $('#courierModel2').modal({
			show: true,
			backdrop:"static"
		});
	 });
	 $("#courierModel2 #close_btn").html('<input type="submit" class="btn<?=$btncolor?>" name="updtrf" id="updtrf" title="Save this" value="Update" <?php if($_POST['updtrf']=='Update'){?>disabled<?php }?>/>&nbsp;<button type="button" id="btnCancel" class="btn btn-success" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>');
}
function getEmailLead(){
	$("#implead").attr("disabled","disabled");
	$.ajax({
		type:'post',
		url:'import_lead_from_email.php',
		data:{impemail:"Y"},
		success:function(data){
			if(data==0){
				$('#emailleadimp').html("Lead imported successfully");
				$("#implead").removeAttr("disabled");
				window.location="lead_list.php?msg=Lead imported successfully<?=$pagenav?>"; 
			}else if(data==1){
				$('#emailleadimp').html("No lead to import.");
				$("#implead").removeAttr("disabled");
				window.location="lead_list.php?msg=No lead to import<?=$pagenav?>"; 
			}else{
				$('#emailleadimp').html("Something went wrong.");
				$("#implead").removeAttr("disabled");
				window.location="lead_list.php?msg=Something went wrong<?=$pagenav?>"; 
			}
		}
	});
}
</script>
<style type="text/css">
.filterable {
    margin-top: 15px;
}
.filterable .panel-heading .pull-right {
    margin-top: -20px;
}
.filterable .filters input[disabled] {
    background-color: transparent;
    border: none;
    cursor: auto;
    box-shadow: none;
    padding: 0;
    height: auto;
}
.filterable .filters input[disabled]::-webkit-input-placeholder {
    color: #333;
}
.filterable .filters input[disabled]::-moz-placeholder {
    color: #333;
}
.filterable .filters input[disabled]:-ms-input-placeholder {
    color: #333;
}
table.dataTable thead th{
padding-left:5px;
}
</style>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
	<div class="container-fluid">
  		<div class="row content">
			<?php 
    			include("../includes/leftnav2.php");
    		?>
    		<div class="col-sm-9 tab-pane fade in active" id="home">
      			<h2 align="center"><i class="fa fa-child"></i> Lead List</h2>
      			<?php if($_REQUEST['msg']){?><br>
      			<h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      			<?php unset($_REQUEST['msg']);}?>
                <!--<h4 align="center" style="color:#FF0000" id="emailleadimp">&nbsp;</h4>-->
	  			<form class="form-horizontal" role="form" name="form1" action="" method="post">
                	<div class="row">
                        <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-9">From Date</label>
                            <input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo date("Y-m-01");}?>">
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-9">To Date</label>
                            <input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>">
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-6">State</label>
                            <select name="state" id="state" class="form-control selectpicker" data-live-search="true" >
                                    <option value="">--Please select--</option>
                                    <?php
									foreach($arr_state as $statename){
										if($statename!=""){
									?>
									<option value="<?=$statename?>"<?php if($statename == $_REQUEST['state']) echo "selected"; ?> ><?=$statename?></option>
								  <?php
								  		}
									}
									?>
                            	</select>
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Party Name</label>
                            <select name="party_code" id="party_code" class="form-control selectpicker" data-live-search="true" >
                                    <option value="">--Please select--</option>
                                    <?php
									foreach($arr_party as $partyname){
										if($partyname!=""){
									?>
									<option value="<?=$partyname?>"<?php if($partyname == $_REQUEST['party_code']) echo "selected"; ?> ><?=$partyname?></option>
								  <?php
								  		}
									}
									?>
                            	</select>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-2">&nbsp;</label><br/>
                        	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                            <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1"><br/>
                        	<?php /*?><a href="excelexport.php?rname=<?= base64_encode("dealerVisitReport") ?>&rheader=<?= base64_encode("Dealer Visit") ?>&user_id=<?= base64_encode($_REQUEST['username']) ?>&fromDate=<?= base64_encode($_REQUEST['fdate']) ?>&toDate=<?= base64_encode($_REQUEST['tdate']) ?>" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-2x" title="Export details in excel"></i></a><?php */?>
                        </div>
                      </div>
	  			</form>
                <br/>
                <div class="btn-group btn-group-lg">
                	<button type="button" class="btn btn-primary" onClick="window.location.href='lead_list.php?status=<?=$pagenav?>'">All&nbsp;<span class="badge"><?=array_sum($array_lead)?></span></button>
                <?php 
				$k=0;
				foreach($array_lead as $status => $stscnt){
				?>	
               		<button type="button" class="btn btn-<?=$task_css[$k]?>" onClick="window.location.href='lead_list.php?status=<?=$status?><?=$pagenav?>'"><?=get_status($status,$link1)?>&nbsp;<span class="badge"><?=$stscnt?></span></button>
                 <?php $k++;}?>     
                 </div>
				<button title="Add New Lead" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='lead_add.php?op=add<?=$pagenav?>'"><span>Add Lead</span></button>
                &nbsp;
                <button title="Import Lead From Email" type="button" class="btn btn-primary" style="float:right;" onClick="getEmailLead()" id="implead"><span>Import Lead From Email</span></button>
                
                    <div class="panel panel-primary filterable">
                        <div class="panel-heading">
                            <h3 class="panel-title">Lead List</h3>
                          <div class="pull-right">
                            <button class="btn btn-default btn-xs btn-filter"><i class="fa fa-filter"></i> Filter</button>
                            </div>
                        </div>
                        <table width="99%" id="myTable" class="table table-striped table-bordered table-hover" align="center">
                            <thead>
                                <tr class="filters">
                                    <th>S.No.</th>
                                    <th><input type="text" class="form-control" placeholder="Lead Id" disabled></th>
                                    <th><input type="text" class="form-control" placeholder="Party Name" disabled></th>
                                    <th><input type="text" class="form-control" placeholder="State" disabled></th>
                                    <th><input type="text" class="form-control" placeholder="Contact No." disabled></th>
                                    <th><input type="text" class="form-control" placeholder="Priority" disabled></th>
                                    <th><input type="text" class="form-control" placeholder="Create Date" disabled></th>
                                    <th><input type="text" class="form-control" placeholder="Status" disabled></th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
							$sno = 0;
							$act = mysqli_query($link1,"SELECT * FROM sf_lead_master WHERE ".$filter_status." order by lid desc");
							while($arow=mysqli_fetch_assoc($act)){
								$sno=$sno+1;
							?>
                                <tr>
                                    <td><?php echo $sno;?></td>
                                    <td><button type="button" class="btn btn-info" onClick="window.location.href='lead_view.php?id=<?php echo $arow['lid'];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'" title="View Lead Details"><?php echo ucfirst($arow['reference']);?></button></td>
                                    <td><?php echo ucwords(($arow['partyid']));?></td>
                                    <td><?php echo ucwords(($arow['party_state']));?></td>
                                    <td><?php echo $arow['party_contact'];?></td>
                                    <td><?php echo getProcessStatus($arow['priority'],$link1);?></td>
                                    <td><?php echo dt_format($arow['tdate']);?></td>
                                    <td><?php echo get_status($arow['status'],$link1);?></td>
                                    <td>
                                    <div class="btn-group btn-group-sm">
               							<button type="button" class="btn btn-success" onClick="openModel2('<?=base64_encode($arow['lid']);?>');" title="Transfer Lead"><i class='fa fa-sign-out'></i></button>
                                        <button type="button" class="btn btn-info" onClick="window.location.href='doc_attahment.php?id=<?php echo base64_encode($arow['lid']);?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'" title="Attach Document"><i class='fa fa-upload'></i></button>
                                        <button type="button" class="btn btn-warning" onClick="window.location.href='lead_edit.php?id=<?php echo $arow['lid'];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'" title="Edit this lead"><i class='fa fa-edit'></i></button>
                                       <?php /*?> <button type="button" class="btn btn-primary" onClick="openModel('<?=base64_encode($arow['reference']);?>');" title="Lead history"><i class='fa fa-history'></i></button><?php */?>
                                        <?php if($arow['status']!=17){ ?>
                                        <button type="button" class="btn btn-danger" onClick="delete_lead('<?php echo $arow['reference'];?>')" title="Delete this lead"><i class='fa fa-trash-o'></i></button>
                                        
                                        <button type="button" class="btn btn-success" onClick="window.location.href='lead_status_update.php?id=<?php echo $arow['lid'];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'" title="Update lead status"><i class='fa fa-cogs'></i></button>
                                        <button type="button" class="btn btn-info" onClick="window.location.href='quote_add.php?id=<?php echo $arow['lid'];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'" title="Share Quote"><i class='fa fa-quora'></i></button>
                                        <?php if($arow['status']!="14" && $arow['status']!="15" && $arow['status']!="16"){ ?>
                                        <button type="button" class="btn btn-warning" onClick="window.location.href='lead_approval.php?id=<?php echo $arow['lid'];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'" title="Lead Approval"><i class='fa fa-gavel'></i></button>
                                        <?php }?>
                                        
                                        <?php }?>
                     				</div>                                   	</td>
                                </tr>
                             <?php }?>    
                            </tbody>
                        </table>
                    </div>
               
                
    		</div>
  		</div>
	</div>
	
    <!-- Start Modal view -->
	<div class="modal modalTH fade" id="courierModel" role="dialog">
		<div class="modal-dialog modal-dialogTH modal-lg">
  			<!-- Modal content-->
  			<div class="modal-content">
    			<div class="modal-header">
      				<button type="button" class="close" data-dismiss="modal">&times;</button>
      				<h2 class="modal-title" align="center"><i class='fa fa-history faicon'></i>&nbsp; &nbsp;Lead Status History</h2>
    			</div>
    			<div class="modal-body modal-bodyTH">
     				<!-- here dynamic task details will show -->
    			</div>
    			<div class="modal-footer" id="close_btn">
      
    			</div> 
  			</div>
		</div>
	</div><!--close Modal view --> 
<!-- Start Modal view -->
<div class="modal modalTH fade" id="courierModel2" role="dialog">
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
</body>
</html>