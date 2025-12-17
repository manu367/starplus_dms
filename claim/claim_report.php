<?php
////// Function ID ///////
$fun_id = array("a"=>array(106)); // User:
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
            	<h2 align="center"><i class="fa fa-file-excel-o"></i> Claim Report</h2>
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
                        <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-9">From Date</label>
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
                        
                  		<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Claim Type</label>
                        	<select name="claim_type" id="claim_type" class="form-control selectpicker" data-live-search="true">
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
                        <div class="row">
                        <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-9">Status</label>
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
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1"><br/>
                        	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                            <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12" align="center">
                            &nbsp;
                        </div>
                     </div>
                      <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12" align="center">
                            <a href="../excelReports/claimdata_csv.php?rname=<?=base64_encode("claimdata")?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>&pcode=<?=base64_encode($_REQUEST['party_code'])?>&status=<?=base64_encode($_REQUEST['status'])?>&claimtype=<?=base64_encode($_REQUEST['claim_type'])?>" title="Export details in excel"><i class="fa fa-file-excel-o fa-2x text-success" title="Export details in excel"></i> </a>
                        </div>
                     </div>
              		</form>                    

       	  </div>
      		</div>
		</div>
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>