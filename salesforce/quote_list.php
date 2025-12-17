<?php
require_once("../config/config.php");
$array_lead = array();
$res_statuscnt = mysqli_query($link1,"SELECT COUNT(quote_id) as quote_cnt, status FROM sf_quote_master group by status");
while($row_statuscnt = mysqli_fetch_assoc($res_statuscnt)){
	$array_lead[$row_statuscnt["status"]] = $row_statuscnt["quote_cnt"];
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
function delete_quote(id){
 	var x = confirm("Do you want to cancel this quote ?");
  	if(x){
		window.location="quote_delete.php?id="+id+"&str=del_quote&status=<?=$_REQUEST["status"]?><?=$pagenav?>"; 
   	}
}
function go_page(quote_id,id){
	if(document.getElementById('action'+id).value!=''){
		var action=document.getElementById('action'+id).value;
 		if(action=='change_status'){
	 		window.location.href='quote_status_update.php?id='+quote_id+"&tab=0&page=quote&status=<?=$_REQUEST["status"]?><?=$pagenav?>";
		} else if(action=='Approval'){
	 		window.location.href='quote_approval.php?id='+quote_id+"&tab=0&page=quote&status=<?=$_REQUEST["status"]?><?=$pagenav?>";
		}
		else if(action=='Make SO'){
			 window.location.href='add_so.php?id='+id+ "&quote_no=" + quote_id+"<?=$pagenav?>";
		}else{
		}
	}
}
////// function for open model to view planning details
function openModel(docid){
 	var doc_no =  atob(docid);
	$.get('../po/po_viewonly.php?id=' + docid, function(html){
		 $('#courierModel .modal-body').html(html);
		 $('#courierModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
	 $("#docno").html(doc_no);
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
      <h2 align="center"><i class="fa fa-quora"></i> Quote </h2><br>
      
      <?php if($_REQUEST['msg']!=''){?>
      	<h4 align="center">
        	<span 
			<?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span>
        </h4>
	  <?php }?><br>
      
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	    <div class="form-group">
         <div class="col-md-6" align="right"><label class="col-md-5 control-label"> Status</label>	  
			<div class="col-md-5" align="left">
			   <select name="status" id="status" class="form-control" onChange="document.frm1.submit();">
                <option value="">Select Quote Status</option>
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
		 <div class="col-md-6" align="left">
           <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
           <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
           <input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
          </div>
	    </div><!--close form group-->

	  </form>
      <form class="form-horizontal table-responsive" role="form">
        <button title="Add New Quote" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='quote_add.php?op=add<?=$pagenav?>'"><span>Add Quote</span></button><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="99%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Quote No</th>
              <th><a href="#" name="name" title="asc"></a>Sales Executive</th>
              <th><a href="#" name="name" title="asc" ></a>Party Name</th>
              <th data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Create Date</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
              <th data-hide="phone,tablet">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno = 0;
			$act = mysqli_query($link1,"select * from sf_quote_master where ".$filter_status." and create_location='".$_SESSION['mapped_location']."' order by quote_id desc");
			while($arow=mysqli_fetch_assoc($act)){
			$sno=$sno+1;
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo $arow['quote_no'];?></td>
              <td><?php echo $arow['sales_executive'];?></td>
              <td><?php echo $arow['party_id'];?></td>
              <td align="center"><?php echo dt_format($arow['update_dt']);?></td>
              <td><?php echo get_status($arow['status'],$link1);?></td>
              <td align="left">
              	<a class='btn btn-success' href='quote_view.php?id=<?php echo $arow['quote_no'];?>&tab=0&page=quote&status=<?=$_REQUEST["status"]?><?=$pagenav?>' title='View'><i class='fa fa-eye'></i></a>
                <?php if($arow['status']!=17 && $arow['status']!=9){ ?>  
                <a class='btn btn-danger' onClick="delete_quote('<?php echo $arow['quote_no']; ?>')" title='Cancel'><i class='fa fa-times'></i></a>
				<?php }?>
                <?php if($arow['status']!=9){ ?> 
              	<a class='btn btn-warning' href='quote_edit.php?id=<?php echo $arow['quote_no'];?>&tab=0&page=quote&status=<?=$_REQUEST["status"]?><?=$pagenav?>'  title='Edit'><i class='fa fa-edit'></i></a>
                <?php }?>
                <a class='btn btn-info' onClick="window.open('../print/print_quote.php?id=<?php echo base64_encode($arow['quote_no']);?>&page=quote');"  title='Print'><i class='fa fa-print'></i></a>
                <a class='btn' style="border-color: #337ab7 !important;" href='quote_attach.php?id=<?php echo $arow['quote_no'];?>&party=<?=$arow['party_id']?>&tab=0&page=quote&status=<?=$_REQUEST["status"]?><?=$pagenav?>'  title='Attach'><i class='fa fa-paperclip'></i></a>
                <?php if($arow['status']!=17 && $arow['status']!=9){ ?>  
                <a><select name="action" id="action<?php echo $sno;?>" class="" style="padding: 3px;border-radius: 4px;background-color: white;">
                    <option value="">Select Action</option>
                    <?php if($arow['status']!="14" && $arow['status']!="15" && $arow['status']!="16"){ ?>
                    <option value="Approval">Quote Approval</option>
                    <?php }?>
                    <?php if($arow['status']=="14"){ ?>
                    <option value="Make SO">Make SO</option>
                    <?php }?>
                    <option value="change_status">Change Status</option>
                   </select>
                </a>
                <a class="btn <?=$btncolor?>"  onclick="go_page('<?php echo $arow['quote_no'];?>','<?php echo $sno;?>');">GO</a></td>
               <?php } if($arow['so_ref_no']){ echo "Converted SO No. -> <button title='Click to view SO details' type='button' class='btn".$btncolor."' onClick = openModel('".base64_encode($arow['so_ref_no'])."');>".$arow['so_ref_no']."</button>";}?>
            </tr>
            <?php }?>
          </tbody>
          </table>
      <!--</div>-->
      </form>
    </div>
    
  </div>
</div>
 <!-- Start Model Mapped Modal -->
  <div class="modal modalTH fade" id="courierModel" role="dialog">
  <form class="form-horizontal" role="form" id="frm2" name="frm2">
    <div class="modal-dialog modal-dialogTH modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h2 class="modal-title" align="center"><i class='fa fa-shopping-basket faicon'></i> Sale Order</h2>
          <h4 id="docno" align="center"></h4>
        </div>
        <div class="modal-body modal-bodyTH">
         <!-- here dynamic task details will show -->
        </div>
        <div class="modal-footer">
          <button type="button" id="btnCancel" class="btn<?=$btncolor?>" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>
        </div>
        
      </div>
    </div>
    </form>
  </div><!--close Model Mapped modal-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>