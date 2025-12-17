<?php
////// Function ID ///////
$fun_id = array("u"=>array(137)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
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
	function openDocModel(docid){
		$.get('claim_modelview.php?doc_id=' + docid, function(html){
			 $('#courierModel .modal-body').html(html);
			 $('#courierModel').modal({
				show: true,
				backdrop:"static"
			});
			$('#viewhead').html("<i class='fa fa-pencil-square-o fa-lg faicon'></i> Claim Details");
		 });
	}
	</script>
 	<link rel="stylesheet" href="../css/datepicker.css">
	<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
	<div class="container-fluid">
   		<div class="row content">
            <?php
            include("../includes/leftnav2.php");
            ?>
            <div class="col-sm-9 tab-pane fade in active" id="home">
            	<h2 align="center"><i class="fa fa-balance-scale"></i> Party Claim Ledger</h2>
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
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">From Date</label>
                            <input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo date("Y-m-01");}?>">
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
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
                        <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-6">&nbsp;</label><br/>
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
<table width="100%" id="" class="table-striped table-bordered table-hover table">
  <thead>
  <tr class="<?=$tableheadcolor?>">
    <th width="5%">S.No.</th>
    <th width="20%">Party Name</th>
    <th width="15%">Claim Type</th>
    <th width="15%">Claim No./Ref. No.</th>
    <th width="10%">Date</th>
    <th width="10%">DR</th>
    <th width="10%">CR</th>
    <th width="15%">Status</th>
  </tr>
  </thead>
  <tbody>
  <?php
	$i = 1;
	$total_cr = 0.00;
	$total_dr = 0.00;
	if($_REQUEST["party_code"]){ $uid = "party_id ='".$_REQUEST["party_code"]."'";}else{ $uid = "1";}
	if($_REQUEST["fdate"]){
		$res_dv = mysqli_query($link1,"SELECT * FROM claim_master WHERE status='Approved' AND ".$uid." AND entry_date >= '".$_REQUEST["fdate"]."' AND entry_date <= '".$_REQUEST["tdate"]."' ORDER BY id DESC")or die("ER1 ".mysqli_error($link1));
	}else{
		$res_dv = mysqli_query($link1,"SELECT * FROM claim_master WHERE status='Approved' AND ".$uid." AND entry_date >= '".date("Y-m-01")."' AND entry_date <= '".$today."' ORDER BY id DESC")or die("ER1 ".mysqli_error($link1));
	}
	if(mysqli_num_rows($res_dv)>0){
	while($row_dv = mysqli_fetch_array($res_dv)){
		$party_det = str_replace("~"," , ",getAnyDetails($row_dv["party_id"],"name,city,state,asc_code","asc_code","asc_master",$link1));
	?>
  <tr>
    <td><?=$i?></td>
    <td><?=$party_det?></td>
    <td><?=$row_dv["claim_type"]?></td>
    <td><button title="Click to view claim details" type="button" class="btn<?=$btncolor?>" onClick="openDocModel('<?php echo base64_encode($row_dv["claim_no"]);?>');"><?php echo $row_dv["claim_no"];?></button></td>
    <td align="center"><?=$row_dv["entry_date"]?></td>
    <td align="right">&nbsp;</td>
    <td align="right"><?=$row_dv["total_amount"]?></td>
    <td align="left"><?=$row_dv["status"]?></td>
    </tr>
  <?php
  	///// check claim payment is received or not
	$res_pay = mysqli_query($link1,"SELECT * FROM payment_receive WHERE against_ref_no='".$row_dv["claim_no"]."'");
	if(mysqli_num_rows($res_pay)>0){
		$row_pay = mysqli_fetch_array($res_pay);
	?>
    <tr>
    <td><?=$i?></td>
    <td><?=$party_det?></td>
    <td><?=$row_dv["claim_type"]?></td>
    <td><?php echo $row_pay["doc_no"];?></td>
    <td align="center"><?=$row_pay["entry_dt"]?></td>
    <td align="right"><?=$row_pay["amount"]?></td>
    <td align="right">&nbsp;</td>
    <td align="left">Payment Received</td>
    </tr>		
	<?php
		$total_dr += $row_pay["amount"]; 
	}
	
  		$total_cr += $row_dv["total_amount"];
		$i++;
    }
  ?>
  <tr>
    <td colspan="5" align="right"><strong>Total</strong></td>
    <td align="right"><strong><?=$total_dr?></strong></td>
    <td align="right"><strong><?=$total_cr?></strong></td>
    <td align="right"><strong><?=$total_cr-$total_dr?></strong></td>
  </tr>
  <?php 
  }else{
  ?>
  <tr>
    <td colspan="8" align="center">No Data Found</td>
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