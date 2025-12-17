<?php
////// Function ID ///////
$fun_id = array("a"=>array(24)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$_SESSION["messageIdent"]="";
$accessState=getAccessState($_SESSION['userid'],$link1);
@extract($_POST);
if($locationstate!=""){
	$loc_state="state='".$locationstate."'";
}else{
	$loc_state="state in (".$accessState.")";
}
## selected city
if($locationcity!=""){
	$loc_city="city='".$locationcity."'";
}else{
	$loc_city="1";
}
## selected location type
if($locationtype!=""){
	$loc_type="id_type='".$locationtype."'";
}else{
	$loc_type="1";
}
## selected location Status
if($locationstatus!=""){
	$loc_status="status='".$locationstatus."'";
}else{
	$loc_status="1";
}

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
 <script>
$(document).ready(function() {
	var dataTable = $('#myTable').DataTable( {
		"processing": true,
		"serverSide": true,
		"bStateSave": true,
		"order": [[ 1, "asc" ]],
		"ajax":{
			url :"../pagination/asp-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>", "locationstate": "<?=$_REQUEST['locationstate']?>", "locationcity": "<?=$_REQUEST['locationcity']?>", "locationtype": "<?=$_REQUEST['locationtype']?>", "locationstatus": "<?=$_REQUEST['locationstatus']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".employee-grid-error").html("");
				$("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="10">No data found in the server</th></tr></tbody>');
				$("#employee-grid_processing").css("display","none");
				
			}
		}
	});
});
////// function for open modal to view mapping details
function checkMapingInfo(refid,refname){
	$.get('mappingInfo.php?pk=' + refid, function(html){
		 $('#viewInfoModal .modal-body').html(html);
		 $('#viewInfoModal').modal({
			show: true,
			backdrop:"static"
		});
	 });
	 var locid = atob(refid);
	 var locnm = atob(refname);
	 $("#viewInfoModal #tile_name").html(locnm+" "+locid);
}
</script>
<style type="text/css">
 .stepwizard-step p {
  margin-top: 10px;
}
.stepwizard-row {
  display: table-row;
}
.stepwizard {
  display: table;
  width: 100%;
  position: relative;
}
.stepwizard-step button[disabled] {
  opacity: 1 !important;
  filter: alpha(opacity=100) !important;
}
.stepwizard-row:before {
  top: 14px;
  bottom: 0;
  position: absolute;
  content: " ";
  width: 100%;
  height: 1px;
  background-color: #ccc;
  z-order: 0;
}
.stepwizard-step {
  display: table-cell;
  text-align: center;
  position: relative;
  /*width: 70px*/
}

.stepwizard-step p {
  position: absolute;
  width: 100%;
  text-align: center;
}
</style>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
	<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
        <h2 align="center"><i class="fa fa-bank"></i> Customer/Locations (DMS) Master</h2>
	    <?php if($_REQUEST['msg']){?>
	    <br>
        <h4 align="center" style="color:#FF0000">
          <?=$_REQUEST['msg']?>
        </h4>
	    <?php }?>
        <form class="form-horizontal" role="form" name="form1" action="" method="post">
      <div class="row">
      	<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">State</label>
        	<select name="locationstate" id="locationstate" class="form-control"  onChange="document.form1.submit();">
                <option value=''>--Please Select-</option>
                <?php
				$circlequery="select distinct(state) from asc_master where state in ($accessState)  order by state";
				$circleresult=mysqli_query($link1,$circlequery) or die(mysqli_error($link1));
				while($circlearr=mysqli_fetch_array($circleresult)){
				?>
                <option value="<?=$circlearr['state']?>"<?php if($_REQUEST['locationstate']==$circlearr['state']){ echo "selected";}?>>
                  <?=ucwords($circlearr['state'])?>
                  </option>
                <?php 
				}
                ?>
              </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">City</label>
        	<select  name='locationcity' id="locationcity" class='form-control'  onChange="document.form1.submit();">
                <option value=''>--Please Select-</option>
                <?php
				$model_query="SELECT distinct city FROM asc_master where state='$_REQUEST[locationstate]' order by city";
				$check1=mysqli_query($link1,$model_query);
				while($br = mysqli_fetch_array($check1)){
			    ?>
                <option value="<?=$br['city']?>"<?php if($_REQUEST['locationcity']==$br['city']){echo 'selected';}?>>
                  <?=$br['city']?>
                  </option>
                <?php
                }
				?>
              </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Type</label>
        	<select  name="locationtype" id="locationtype" class='form-control' onChange="document.form1.submit();">
                <option value=''>All</option>
                <?php
				$type_query="SELECT locationname,locationtype FROM location_type where status='A' order by seq_id";
				$check_type=mysqli_query($link1,$type_query);
				while($br_type = mysqli_fetch_array($check_type)){
				?>
                <option value="<?=$br_type['locationtype']?>"<?php if($_REQUEST['locationtype']==$br_type['locationtype']){ echo "selected";}?>><?php echo $br_type['locationname']?></option>
                <?php }?>
              </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Status</label>
        	<select name="locationstatus" id="locationstatus" class="form-control"  onChange="document.form1.submit();">
                <option value=""<?php if($_REQUEST['locationstatus']==''){ echo "selected";}?>>All</option>
                <option value="active"<?php if($_REQUEST['locationstatus']=='active'){ echo "selected";}?>>Active</option>
                <option value="deactive"<?php if($_REQUEST['locationstatus']=='deactive'){ echo "selected";}?>>Deactive</option>
              </select>
          
            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
        </div>
      </div>
      <br/>
      <div class="row">
      	<div class="col-sm-6 col-md-6 col-lg-6">
        	<?php
			//// get excel process id ////
			//$processid=getExlCnclProcessid("Product",$link1);
			////// check this user have right to export the excel report
			//if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
		   ?>
		   <a href="excelexport.php?rname=<?=base64_encode("locationmaster")?>&rheader=<?=base64_encode("Location Master")?>&locstate=<?=base64_encode($_POST['locationstate'])?>&loccity=<?=base64_encode($_POST['locationcity'])?>&loctype=<?=base64_encode($_POST['locationtype'])?>&locstatus=<?=base64_encode($_POST['locationstatus'])?>" title="Export location details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export location details in excel"></i></a>
		   <?php
			//}
			?>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
       		<button title="Add New Customer/Location" type="button" class="btn <?=$btncolor?>" style="float:right;margin-bottom:20px" onClick="window.location.href='asp_add.php?op=add<?=$pagenav?>'"><span>Add New Customer/Location</span></button>
        </div>
      </div>
	  </form>
      <form class="form-horizontal" role="form">
	    <!--<div class="form-group"  id="page-wrap" style="margin-left:10px;">-->
        <table  width="100%" id="myTable" class="display table-responsive table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th>#</th>
              <th>Location Id</th>
              <th>Location Name</th>
              <th>State</th>
              <th>Location Type</th>
              <th>Phone No.</th>
              <th>Status</th>
			  <th>Approval</th>
              <th>View/Edit</th>
              <th>Mapping</th>
              <th>History</th>
            </tr>
          </thead>
        </table>
	    <!--</div>-->
	    <!--close form group-->
      </form>
    </div>
	<!--close tab pane-->
  </div><!--close row content-->
</div><!--close container fluid-->
	
<!-- Start view information Modal -->
<div class="modal modalTH fade" id="viewInfoModal" role="dialog">
	<div class="modal-dialog modal-dialogTH modal-lg">
  		<!-- Modal content-->
  		<div class="modal-content">
    		<div class="modal-header">
      			<button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title" align="center" id="tile_name"></h2>
    		</div>
    		<div class="modal-body modal-bodyTH">
     			<!-- here dynamic task details will show -->
    		</div>
    		<div class="modal-footer">
      			<button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
    		</div>
  		</div>
	</div>
</div>
<!--close view information modal-->	
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>