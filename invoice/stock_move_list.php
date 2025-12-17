<?php
////// Function ID ///////
$fun_id = array("u"=>array(25)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
//$_SESSION['retailBill']="";
////// filters value/////
$filter_str = 1;
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND DATE(entry_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND DATE(entry_date) <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['main_location'] !=''){
	$filter_str	.= " AND main_location = '".$_REQUEST['main_location']."'";
}
if($_REQUEST['stock_from'] !=''){
	$filter_str	.= " AND from_location = '".$_REQUEST['stock_from']."'";
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
	////// from date
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
	/////// to date
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
});
////// function for open model to view document details
function openDocModel(docid){
	$.get('viewstockmoveinfomodal.php?doc_id=' + docid, function(html){
		 $('#courierModel .modal-body').html(html);
		 $('#courierModel').modal({
			show: true,
			backdrop:"static"
		});
		$('#viewhead').html("<i class='fa fa-pencil-square-o fa-lg faicon'></i> Stock Movement Details");
	 });
}
</script>
<link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-cubes"></i> Stock Movement List</h2>
      <?php if(isset($_REQUEST['msg'])){?>
        <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
      	  <form class="form-horizontal" role="form" name="frm1" action="" method="POST">
      <div class="row">
      	<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">From Date</label>
        	<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate" autocomplete="off" id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo "";}?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
        	<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate" autocomplete="off" id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo "";}?>"style="width:160px;"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Main Location</label>
        	<select name="main_location" id="main_location" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();">
                <option value="" selected="selected">Please Select </option>
                <?php                                 
                $sql_chl="select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y'";
                $res_chl=mysqli_query($link1,$sql_chl);
                while($result_chl=mysqli_fetch_array($res_chl)){
                $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='".$result_chl['location_id']."'"));
                ?>
                <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['main_location'])echo "selected";?>><?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?></option>
                <?php
                }
                ?>
            </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Move From</label>
        	<select name="stock_from" id="stock_from" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();">
                <option value="" selected="selected">Please Select </option>
                <?php                                 
                $smfm_sql = "SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code='".$_REQUEST['main_location']."'";
                $smfm_res = mysqli_query($link1,$smfm_sql);
                while($smfm_row = mysqli_fetch_array($smfm_res)){
                ?>
                <option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['stock_from'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
                <?php
                }
                ?>
                <?php                                 
                $smf_sql = "SELECT sub_location, sub_location_name FROM sub_location_master WHERE main_location='".$_REQUEST['main_location']."' AND status='Active'";
                $smf_res = mysqli_query($link1,$smf_sql);
                while($smf_row = mysqli_fetch_array($smf_res)){
                ?>
                <option value="<?=$smf_row['sub_location']?>" <?php if($smf_row['sub_location']==$_REQUEST['stock_from'])echo "selected";?>><?=$smf_row['sub_location_name']." | ".$smf_row['sub_location']?></option>
                <?php
                }
                ?>
            </select>
            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
        </div>
      </div>
      <br/>
      <div class="row">
      	<div class="col-sm-6 col-md-6 col-lg-6">
		   <?php /*?><a href="excelexport.php?rname=<?=base64_encode("productmaster")?>&rheader=<?=base64_encode("Product Master")?>&brand=<?=base64_encode($_GET['brand'])?>&product_cat=<?=base64_encode($_GET['product_cat'])?>&product_sub_cat=<?=base64_encode($_GET['product_sub_cat'])?>&product=<?=base64_encode($_GET['product'])?>" title="Export Product details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Product details in excel"></i></a><?php */?>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
        	<button title="Add Stock Movement" type="button" class="btn<?=$btncolor?>" style="float:right;margin-bottom:20px" onClick="window.location.href='stock_move_within_loc.php?op=add<?=$pagenav?>'"><span>Add Stock Movement</span></button>
        </div>
      </div>
	  </form>
      <form class="form-horizontal" role="form">
<!--      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th width="5%">S.No</th>
              <th width="15%">Main Location</th>
              <th width="13%">Move From</th>
              <th width="12%">Move To</th>
              <th width="10%">Move Type</th>
              <th width="10%">Ref. No.</th>
              <th width="10%">Entry Date</th>
              <th width="10%">Status</th>
              <!--<th width="10%">Serial Attached</th>-->
              <th width="8%">View</th>
              <th width="7%">Print</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			///// get access location ///
			$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
			$sql = mysqli_query($link1,"SELECT * FROM stock_movement_master WHERE main_location IN (".$accesslocation.") AND ".$filter_str." ORDER BY id DESC");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
				  /// move from party
				  $billfrom=getLocationDetails($row['from_location'],"name,city,state",$link1);
				  $explodevalf=explode("~",$billfrom);
				  if($explodevalf[0]){ $fromparty=$billfrom; }else{ $fromparty=getAnyDetails($row['from_location'],"sub_location_name","sub_location","sub_location_master",$link1);}
				  /// move to party
				  $billto=getLocationDetails($row['to_location'],"name,city,state",$link1);
				  $explodeval=explode("~",$billto);
				  if($explodeval[0]){ $toparty=$billto; }else{ $toparty=getAnyDetails($row['to_location'],"sub_location_name","sub_location","sub_location_master",$link1);}
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo str_replace("~",",",getLocationDetails($row['main_location'],"name,city,state",$link1));?></td>
              <td><?php echo str_replace("~",",",$fromparty);?></td>
              <td><?php echo str_replace("~",",",$toparty);?></td>
              <td><?php echo getStockTypeName($row['move_stocktype']);?></td>
              <td><?php echo $row['doc_no'];?></td>
              <td><?php echo $row['entry_date'];?></td>
              <td <?php if($row['status']=="PFA"){ echo "class='red_small'";}?>><?php echo $row['status'];?></td>              
<?php /*?>              <td align="center">
			<?php 
			if($row['status']!="Cancelled"){
				if($row['imei_attach']==""){?>
                	<a href='serialUploadSM.php?id=<?php echo base64_encode($row['doc_no']);?><?=$pagenav?>'  title='Serial Attach'><i class="fa fa-upload fa-lg"></i></a>
			<?php 
				}else{ 
					echo "YES";
				}
			}?></td><?php */?>
              <td align="center"><a href="#" class="btn <?=$btncolor?>" title="Stock movement info" onClick="openDocModel('<?=base64_encode($row['doc_no'])?>')"><i class="fa fa-info-circle" title="Stock movement info"></i></a></td>
              <td align="center">
                <a href='../print/print_stockmovement.php?rb=view&id=<?php echo base64_encode($row['doc_no']);?><?=$pagenav?>' class="btn <?=$btncolor?>" target="_blank"  title='Print Document'><i class="fa fa-print fa-lg" title="Print Document"></i></a>
			  <?php if($row['serial_attach']){ ?>  &nbsp;&nbsp;<a href='../print/print_imei.php?rb=view&id=<?php echo base64_encode($row['doc_no']);?><?=$pagenav?>' target="_blank"  title='Print Serial'><i class="fa fa-print fa-lg" title="Print Serial"></i></a><?php }?></td>
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
  <form class="form-horizontal" role="form" id="frm2" name="frm2" method="post">
    <div class="modal-dialog modal-dialogTH modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" align="center" id="viewhead"></h4>
        </div>
        <div class="modal-body modal-bodyTH">
         <!-- here dynamic task details will show -->
        </div>
        <div class="modal-footer">
          <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
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