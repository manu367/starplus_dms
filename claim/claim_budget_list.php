<?php
////// Function ID ///////
$fun_id = array("u"=>array(138)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
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
 <script>
/*$(document).ready(function(){
    $('#myTable').dataTable();
});*/
$(document).ready(function() {
	var dataTable = $('#target_grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"bStateSave": true,
		"order":  [[0,"asc"]],
		"ajax":{
			url :"../pagination/claimbgt_grid_data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>",  "party_code": "<?=$_REQUEST["party_code"]?>","selyear": "<?=$_REQUEST['selyear']?>","selmonth": "<?=$_REQUEST['selmonth']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".target_grid_error").html("");
				$("#target_grid").append('<tbody class="target_grid_error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
				$("#target_grid_processing").css("display","none");
			}
		}
	});
});
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
      <h2 align="center"><i class="fa fa-suitcase"></i> Claim Budget List </h2>
      <?php if($_REQUEST['msg']!=''){?>
      <h4 align="center"> <span 
			<?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>> <?php echo $_REQUEST['msg'];?> </span> </h4>
      <?php }?>
      <?php
		if(isset($_SESSION["logres"]) && $_SESSION["logres"]){
			echo '<div class="py-2 overflow-hidden" style="background:#f1f1f1;padding:15px;line-height:20px;color:#e51111;margin:15px;font-size:12px;">';
			echo '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.$_SESSION["logres"]["msg"];
			echo '<br/><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.implode(" , ",$_SESSION["logres"]["invalid"]);
			echo '</div>';
		}
		unset($_SESSION["logres"]);
		?>
      <form class="form-horizontal" role="form" name="form1" action="" method="post">
        <div class="row">
          <div class="col-sm-3 col-md-3 col-lg-3">
            <label class="col-md-6">Year</label>
            <select name="selyear" id="selyear" class="form-control" onChange="document.form1.submit();">
              <?php 
                    for($i=0; $i<3; $i++){ 
                        $year = date('Y', strtotime(date("Y"). ' + '.$i.' year'));
                    ?>
              <option value="<?=$year?>"<?php if($_REQUEST["selyear"]==$year){ echo "selected";}?>>
                <?=$year?>
              </option>
              <?php } ?>
            </select>
          </div>
          <div class="col-sm-3 col-md-3 col-lg-3">
            <label class="col-md-9">Party Name</label>
            <select name="party_code" id="party_code" class="form-control selectpicker" data-live-search="true" >
              <option value="">All</option>
              <?php
									$sql_parent = "select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y'";
									$res_parent = mysqli_query($link1, $sql_parent);
									while ($result_parent = mysqli_fetch_array($res_parent)) {   
										$party_det = mysqli_fetch_array(mysqli_query($link1, "select name , city, state,id_type from asc_master where asc_code='" . $result_parent['location_id'] . "'"));
										if($party_det['name']){
									?>
              <option value="<?= $result_parent['location_id']?>" <?php if ($result_parent['location_id'] == $_REQUEST['party_code']) echo "selected"; ?> >
                <?= $party_det['name'] . " | " . $party_det['city'] . " | " . $party_det['state'] . " | " . $result_parent['location_id']?>
              </option>
              <?php
										}
									}
									?>
            </select>
          </div>
          <div class="col-sm-2 col-md-2 col-lg-2">
            <label class="col-md-6">&nbsp;</label>
            <br/>
            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
            <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
          </div>
          <div class="col-sm-1 col-md-1 col-lg-1"><br/>
              <?php /*?> <a href="../reports/excelexport.php?rname=<?= base64_encode("targetReport") ?>&rheader=<?= base64_encode("Target Report") ?>&user_id=<?= base64_encode($_REQUEST['user_id']) ?>&selyear=<?= base64_encode($_REQUEST['selyear']) ?>&selmonth=<?= base64_encode($_REQUEST['selmonth']) ?>" title="Export details in excel" class="text-success"><i class="fa fa-file-excel-o fa-2x" title="Export details in excel"></i></a><?php */?>
          </div>
        </div>
      </form>
      <br/>
      <form class="form-horizontal" role="form">
        <div style="display:inline-block;float:right">
          <button title="Upload Claim Budget" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='claim_budget_upload.php?<?=$pagenav?>'"><i class="fa fa-upload"></i> <span>Upload Claim Budget</span></button>
              </div>
        <div style="display:inline-block;float:right">
          <button title="Add Claim Budget" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='claim_budget_add.php?op=add<?=$pagenav?>'"><i class="fa fa-plus-circle"></i> <span>Add Claim Budget</span></button>
        </div>
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/>
            <br/>
            <table  width="98%" id="target_grid" class="table-striped table-bordered table-hover" align="center">
              <thead>
                <tr class="<?=$tableheadcolor?>">
                  <th>S.No</th>
                  <th>Party Name</th>
                  <th>Claim Type</th>
                  <th>Budget Year</th>
                  <th>Yearly Budget</th>
                  <th>Monthly Budget</th>
                  <th>Manpower</th>
                  <th>Status</th>
                  <th>View</th>
                </tr>
              </thead>
            </table>
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