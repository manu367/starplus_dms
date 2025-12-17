<?php
require_once("../config/config.php");
$array_lead = array();
$res_statuscnt = mysqli_query($link1,"SELECT COUNT(lid) as lead_cnt, status FROM sf_lead_master group by status");
while($row_statuscnt = mysqli_fetch_assoc($res_statuscnt)){
	$array_lead[$row_statuscnt["status"]] = $row_statuscnt["lead_cnt"];
}
///// filter value
if($_REQUEST["status"]=="All" || $_REQUEST["status"]==""){
	$filter_status = "1";
}else{
	$filter_status = "status = '".$_REQUEST["status"]."'";
}

?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
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
</script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-child"></i> Lead Details</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	    <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label"> Status</label>	  
			<div class="col-md-5" align="left">
			   <select name="status" id="status" class="form-control" onChange="document.frm1.submit();">
                <option value="">Select Lead Status</option>
                <option value="All"<?php if($_REQUEST['status']=="All"){echo "selected='selected'";}?>>All (<?=array_sum($array_lead)?>)</option>
                <?php 
                    $st=mysqli_query($link1,"select * from sf_status_master where display_for='lead' order by status_name");
                    while($r=mysqli_fetch_assoc($st)){
                        if($array_lead[$r['id']]){ $leadcnt = $array_lead[$r['id']];}else{ $leadcnt = 0;}
                ?>
                <option value="<?php echo $r['id'];?>"<?php if($r['id']==$_REQUEST['status']){echo "selected='selected'";}?>><?php echo $r['status_name']." (".$leadcnt.")";?></option>
                <?php } ?>
        </select> 
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
			   
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">

            </div>
          </div>
	    </div><!--close form group-->
	  </form>
      <form class="form-horizontal table-responsive" role="form">
        <button title="Add New Lead" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='lead_add.php?op=add<?=$pagenav?>'"><span>Add Lead</span></button><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="99%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Lead Id</th>
              <th><a href="#" name="name" title="asc" ></a>Party Name</th>
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Priority</th>
              <th data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Create Date</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
              <th data-hide="phone,tablet">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno = 0;
			//echo "select * from sf_lead_master where ".$filter_status." and create_location='".$_SESSION['mapped_location']."' order by lid desc";
			$act = mysqli_query($link1,"select * from sf_lead_master where ".$filter_status." and create_location='".$_SESSION['mapped_location']."' order by lid desc");
			while($arow=mysqli_fetch_assoc($act)){
			$sno=$sno+1;
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo ucfirst($arow['reference']);?></td>
              <td><?php echo  ucwords(($arow['partyid']));?></td>
              <td><?php echo getProcessStatus($arow['priority'],$link1);?></td>
              <td><?php echo dt_format($arow['tdate']);?></td>
              <td><?php echo get_status($arow['status'],$link1);?></td>
              <td align="center"><a class='btn btn-success' href='lead_view.php?id=<?php echo $arow['lid'];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>' title='View'><i class='fa fa-eye'></i></a>
               <a class='btn btn-primary' href='doc_attahment.php?id=<?php echo $arow['lid'];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>' title='Attachemnt'><i class='fa fa-upload'></i></a>
                                          <?php if($arow['status']!=17){ ?>  <a class='btn btn-danger' onClick="delete_lead('<?php echo $arow['reference'];?>')" title='Delete'><i class='fa fa-trash-o'></i></a><?php }?>
                                            <a class='btn btn-warning' href='lead_edit.php?id=<?php echo $arow['lid'];?>&tab=0&page=lead&status=<?=$_REQUEST["status"]?><?=$pagenav?>'  title='edit'><i class='fa fa-edit'></i></a>
                                            <a class='btn btn-default' href='#' onClick="openModel('<?=base64_encode($arow['reference']);?>');" title='Status History'><i class='fa fa-history'></i></a>
                                         <?php if($arow['status']!=17){ ?>   <a><select name="action" id="action<?php echo $sno;?>" class="">
                                                <option value="">Select Action</option>
                                                <?php if($arow['status']!="14" && $arow['status']!="15" && $arow['status']!="16"){ ?><option value="Approval">Lead Approval</option><?php }?>
                                                <option value="change_status">Change Status</option>
                                                <option value="Share Quote">Share Quote</option>
                                               </select></a>
                                            <a class="btn btn-primary"  onclick="go_page('<?php echo $arow['lid'];?>','<?php echo $sno;?>');">GO</a></td>
                                            <?php }?>
            </tr>
            <?php }?>
          </tbody>
          </table>
      <!--</div>-->
      </form>
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
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>