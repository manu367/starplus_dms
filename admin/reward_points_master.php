<?php
require_once("../config/config.php");
$accessState=getAccessState($_SESSION['userid'],$link1);
@extract($_POST);
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" language="javascript" >
$(document).ready(function() {
	var dataTable = $('#myTable').DataTable( {
		"responsive": true, 
		"processing": true,
		"serverSide": true,
		"order":  [[0,"asc"]],
		"ajax":{
			url :"../pagination/rwdpoints-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>", "status": "<?=$status?>", "location_state": "<?=$locationstate?>", "location_type": "<?=$locationtype?>", "product": "<?=$product?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".myTable-error").html("");
				$("#myTable").append('<tbody class="myTable-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
				$("#myTable_processing").css("display","none");
				
			}
		}
	} );
} );
</script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-money"></i>&nbsp;Reward Point Matrix (To Be Earn)</h2>
      <?php if(isset($_REQUEST['msg'])){?>
        <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
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
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Location State</label>
        	<select name="locationstate" id="locationstate" class="form-control selectpicker" data-live-search="true" onChange="document.form1.submit();">
                  <option value=''>--Please Select-</option>
                  <?php
				$circlequery="select distinct(state) from asc_master WHERE state in ($accessState) order by state";
				$circleresult=mysqli_query($link1,$circlequery) or die(mysqli_error($link1));
				while($circlearr=mysqli_fetch_array($circleresult)){
				?>
				  <option value="<?=$circlearr['state']?>"<?php if($_REQUEST['locationstate']==$circlearr['state']){ echo "selected";}?>><?=ucwords($circlearr['state'])?></option>
				<?php 
				}
                ?>
                </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Product</label>
            <select  name='product' id="product" class='form-control selectpicker' data-live-search="true"  onChange="document.form1.submit();">
                  <option value=''>--Please Select-</option>
				  <?php
				$model_query="SELECT * FROM product_master ";
				$check1=mysqli_query($link1,$model_query);
				while($br = mysqli_fetch_array($check1)){
			    ?>
				  <option value="<?=$br['productcode']?>"<?php if($_REQUEST['product']==$br['productcode']){echo 'selected';}?>><?=getProduct($br['productcode'],$link1)." - ".$br['productcode']?></option>
				<?php
                }
				?>
               </select>
        </div>
        <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-12">Location Type</label>
            <select  name="locationtype" id="locationtype" class='form-control' >
                 <option value=''>--Please Select-</option>
                 <?php
				$type_query="SELECT locationname,locationtype FROM location_type where status='A' order by seq_id";
				$check_type=mysqli_query($link1,$type_query);
				while($br_type = mysqli_fetch_array($check_type)){
				?>
                <option value="<?=$br_type['locationtype']?>"<?php if($_REQUEST['locationtype']==$br_type['locationtype']){ echo "selected";}?>><?php echo $br_type['locationname']?></option>
                <?php }?>
               </select>
        </div>
        <div class="col-sm-2 col-md-2 col-lg-2"><label class="col-md-6">Status</label>
        	<select name="status" id="status" class="form-control">
                <option value=""<?php if(isset($_REQUEST['status'])){if($_REQUEST['status']==''){ echo "selected";}}?>>All</option>
                <option value="A"<?php if(isset($_REQUEST['status'])){if($_REQUEST['status']=="A"){ echo "selected";}}?>>Active</option>
                <option value="D"<?php if(isset($_REQUEST['status'])){if($_REQUEST['status']=="D"){ echo "selected";}}?>>Deactive</option>
            </select>
        </div>
        <div class="col-sm-1 col-md-1 col-lg-1"><label class="col-md-3">&nbsp;</label><br/>
            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
			<input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
        </div>
      </div>
	  </form>
      <br/>
      <form class="form-horizontal" role="form">  
      	<a href="excelexport.php?rname=<?=base64_encode("rewardpointmaster")?>&rheader=<?=base64_encode("Reward Point Master")?>&location_state=<?=base64_encode($_REQUEST['locationstate'])?>&product=<?=base64_encode($_REQUEST['product'])?>&location_type=<?=base64_encode($_REQUEST['locationtype'])?>&status=<?=base64_encode($_REQUEST['status'])?>" title="Export reward details in excel" style="float:left"><i class="fa fa-file-excel-o fa-2x" title="Export reward details in excel"></i></a>
        <button title="Add New Reward" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='addRewardPoint.php?op=add<?=$pagenav?>'"><span>Add New Reward</span></button>&nbsp;&nbsp;
        <button title="Upload Reward" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='reward_point_uploader.php?op=upload<?=$pagenav?>'"><span>Upload Reward</span></button> 
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th>#</th>
              <th>Location State</th>
              <th>Location Type</th>
              <th>Product</th>
			  <th>Reward Points</th>
              <th>Parent Reward</th>
              <th>Status</th>
			  <th>View/Edit</th>
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
?>
</body>
</html>