<?php
////// Function ID ///////
$fun_id = array("u"=>array(135)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$accessloc = getAccessLocation($_SESSION['userid'],$link1);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= siteTitle ?></title>
    <script src="../js/jquery-1.10.1.min.js"></script>
 	<link href="../css/font-awesome.min.css" rel="stylesheet">
 	<link href="../css/abc.css" rel="stylesheet">
 	<script src="../js/bootstrap.min.js"></script>
 	<link href="../css/abc2.css" rel="stylesheet">
 	<link rel="stylesheet" href="../css/bootstrap.min.css">
 	<link rel="stylesheet" href="../css/bootstrap-select.min.css">
 	<script src="../js/bootstrap-select.min.js"></script>
    <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 	<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 	<script type="text/javascript">
	
	$(document).ready(function () {
		$('#fdate').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
	});
	$(document).ready(function () {
		$('#tdate').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
	});
	$(document).ready(function(){
		$('#myTable').dataTable();		
	});
	function openJsonModel(docid){
		var decodedString = atob(docid);
		 $('#myModal .modal-body').html(decodedString);
	}
	function openPreview(docid){
		$.get('claim_modelsappreview.php?doc_id=' + docid, function(html){
			 $('#courierModel .modal-body').html(html);
			 $('#courierModel').modal({
				show: true,
				backdrop:"static"
			});
			$('#viewhead').html("<i class='fa fa-pencil-square-o fa-lg faicon'></i> Claim Preview");
		 });
	}
	</script>
 	<link rel="stylesheet" href="../css/datepicker.css">
	<script src="../js/bootstrap-datepicker.js"></script>
<style type="text/css">
 .modal-lg{
	 width:auto;
 }
 </style>
</head>
<body>
	<div class="container-fluid">
   		<div class="row content">
            <?php
            include("../includes/leftnav2.php");
            ?>
            <div class="col-sm-9 tab-pane fade in active" id="home">
            	<h2 align="center"><i class="fa fa-send"></i> Post Claim List in SAP</h2>
                <?php if(isset($_REQUEST['msg'])){?>
                <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                  </button>
                    <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
                </div>
              <?php }?>
       	  <form class="form-horizontal" role="form" name="form1" action="" method="post">
                	<div class="row">
                        <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-6">From Date</label>
                            <input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo date("Y-m-01");}?>">
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-9">To Date</label>
                            <input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>">
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Party Name</label>
                            <select name="party_code" id="party_code" class="form-control selectpicker" data-live-search="true" >
                                    <option value="">--Please select--</option>
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
                       <?php /*?> <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-9">Status</label>
                            <select name="status" id="status" class="form-control selectpicker" data-live-search="true" >
                                    <option value="">All</option>
                                    <?php
									$sql_status = "SELECT DISTINCT(status) FROM claim_master WHERE 1";
									$res_status = mysqli_query($link1, $sql_status);
									while($result_status = mysqli_fetch_array($res_status)){
									?>
									<option value="<?= $result_status[0]?>" <?php if ($result_status[0] == $_REQUEST['status']) echo "selected"; ?> ><?=$result_status[0]?></option>
								  <?php
									}
									?>
                            	</select>
                        </div><?php */?>
                        <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-6">&nbsp;</label><br/>
                        	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                            <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1"><br/>
                        	<a href="../excelReports/claimdata_csv.php?rname=<?=base64_encode("claimdata")?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>&pcode=<?=base64_encode($_REQUEST['party_code'])?>&status=<?=base64_encode("Approved")?>" title="Export details in excel"><i class="fa fa-file-excel-o fa-2x text-success" title="Export details in excel"></i> </a>
                        </div>
                      </div>
              		</form>
                    <br/>                    
<table width="100%" id="myTable" class="table-striped table-bordered table-hover">
  <thead>
  <tr class="<?=$tableheadcolor?>">
    <th width="3%">S.No.</th>
    <th width="18%">Party Name</th>
    <th width="10%">Claim Type</th>
    <th width="12%">Claim No.</th>
    <th width="10%">Claim Amount</th>
    <th width="10%">Entry By</th>
    <th width="7%">Entry Date</th>
    <th width="10%">Status</th>
    <th width="5%">View</th>
    <th width="5%">Preview</th>
    <th width="5%">Post in SAP</th>
    <th width="5%">Print</th>
  </tr>
  </thead>
  <tbody>
  <?php
	$i = 1;
	if($_REQUEST["party_code"]){ $uid = "party_id ='".$_REQUEST["party_code"]."'";}else{ $uid = "party_id IN (".$accessloc.")";}
	if($_REQUEST["status"]){ $sts = "status ='".$_REQUEST["status"]."'";}else{ $sts = "1";}
	if($_REQUEST["fdate"]){
		$res_dv = mysqli_query($link1,"SELECT * FROM claim_master WHERE status='Approved' AND ".$uid." AND ".$sts." AND entry_date >= '".$_REQUEST["fdate"]."' AND entry_date <= '".$_REQUEST["tdate"]."' ORDER BY id DESC")or die("ER1 ".mysqli_error($link1));
	}else{
		$res_dv = mysqli_query($link1,"SELECT * FROM claim_master WHERE status='Approved' AND ".$uid." AND ".$sts." AND entry_date >= '".date("Y-m-01")."' AND entry_date <= '".$today."' ORDER BY id DESC")or die("ER1 ".mysqli_error($link1));
	}
	if(mysqli_num_rows($res_dv)>0){
	while($row_dv = mysqli_fetch_array($res_dv)){
		$party_det = str_replace("~"," , ",getAnyDetails($row_dv["party_id"],"name,city,state,asc_code","asc_code","asc_master",$link1));
		////// make JSON for SAP
		$post_json = array(
		"party_id" => $row_dv["party_id"],
		"party_name" => $party_det,
		"claim_type" => $row_dv["claim_type"],
		"claim_no" => $row_dv["claim_no"],
		"entry_date" => $row_dv["entry_date"]." ".$row_dv["entry_time"],
		"status" => $row_dv["status"],
		"total_amt" => $row_dv["total_amount"]
		);
	?>
  <tr>
    <td><?=$i?></td>
    <td><?=$party_det?></td>
    <td><?=$row_dv["claim_type"]?></td>
    <td><?=$row_dv["claim_no"]?></td>
    <td><?=$row_dv["total_amount"]?></td>
    <td><?=getAnyDetails($row_dv["entry_by"],"name","username","admin_users",$link1)?></td>
    <td><?=$row_dv["entry_date"]." ".$row_dv["entry_time"]?></td>
    <td align="left"><?=$row_dv["status"]?></td>
    <td align="center"><a href='claim_view.php?id=<?=base64_encode($row_dv['claim_no'])?>&pgnm=<?=base64_encode("claim_post_in_sap");?><?=$pagenav?>' title='view' class="btn <?=$btncolor?>"><i class='fa fa-eye' title='view details'></i></a></td>
    <td align="center"><a href="#" class="btn <?=$btncolor?>" title="Preview" onClick="openPreview('<?=base64_encode($row_dv["claim_no"])?>')"><i class="fa fa-bullseye" title="Preview"></i></a></td>
    <td align="center"><a href="#" class="btn <?=$btncolor?>" title="Print" data-toggle="modal" data-target="#myModal" onClick="openJsonModel('<?=base64_encode(json_encode($post_json))?>')"><i class="fa fa-send" title="post data in SAP"></i></a></td>
    <td align="center"><a href="../print/print_claim.php?id=<?=base64_encode($row_dv['claim_no'])?>" target="_blank" class="btn <?=$btncolor?>" title="Print"><i class="fa fa-print" title="print"></i></a></td>
  </tr>
  <?php
		$i++;
    }
  ?>
  <?php 
  }else{
  ?>
  <tr>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">No</td>
    <td align="center">Data</td>
    <td align="left">Found</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <?php
  }
  ?>
  </tbody>
</table>
       	  </div>
      		</div>
		</div>
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
    <!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">JSON data</h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
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
</body>
</html>