<?php
require_once("../config/config.php");
$getid=base64_decode($_REQUEST['empcode']);
////// get details of selected location////
$res_locdet=mysqli_query($link1,"SELECT * FROM admin_users where username = '".$getid."'")or die(mysqli_error($link1));
$row_locdet=mysqli_fetch_array($res_locdet);
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link href="../css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script>
	function makeTable(){
		$('#myTable').dataTable();
	}
 </script>
 </head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-history"></i> History </h2>
      <h4 align="center">
          <?=$row_locdet['name']."  (".$row_locdet['username'].")";?>
        </h4>
		<br><br>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      <div class="row">
      	<div class="col-sm-4 py-2" style="height: 200px;">
            <div class="card h-100 text-white bg-success">
                <div class="card-body">
                    <div style="float:left;display:inline-block"><h3 class="card-title"> Personal </h3></div> <div style="float:right;display:inline-block"><h3 class="card-title"><i class='fa fa-user'></i></h3></div>
                    <p class="card-text">Here you can see all personal information of employee.</p>
                    <a href="#" class="btn btn-outline-light" onClick="checkPersonal('<?=$row_locdet['username']?>');">View</a>
                </div>
            </div>
        </div>
        <div class="col-sm-4 py-2" style="height: 200px;">
            <div class="card h-100 text-white bg-warning">
                <div class="card-body">
                    <div style="float:left;display:inline-block"><h3 class="card-title"> HR. related </h3></div> <div style="float:right;display:inline-block"><h3 class="card-title"><i class='fa fa-institution'></i></h3></div>
                    <p class="card-text">Here you can see all HR. related information of employee.</p>
                    <a href="hr_related_status.php?id=<?=base64_encode($row_locdet['username']);?><?=$pagenav?>" class="btn btn-outline-light" >View</a>
                </div>
            </div>
        </div>
        <div class="col-sm-4 py-2" style="height: 200px;">
            <div class="card h-100 text-white bg-danger">
                <div class="card-body">
                    <div style="float:left;display:inline-block"><h3 class="card-title"> Work related </h3></div> <div style="float:right;display:inline-block"><h3 class="card-title"><i class='fa fa-wrench'></i></h3></div>
                    <p class="card-text">Here you can see all work related information of employee.</p>
                    <a href="emp_activities.php?id=<?=base64_encode($row_locdet['username']);?><?=$pagenav?>" class="btn btn-outline-light" >View</a>
                </div>
            </div>
        </div>
       </div>
       
	   
	   <br><br><br><br>
      	<div class="form-group">
            <div class="col-md-12" align="center">
            <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='adminusermgt.php?<?=$pagenav?>'">
            </div>
         </div> 
      </div><!--End form group-->
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
<!-- Start Inventory Modal -->
<div class="modal modalTH fade" id="viewModal" role="dialog">
	<div class="modal-dialog modal-dialogTH modal-lg">
  		<!-- Modal content-->
  		<div class="modal-content">
    		<div class="modal-header header1">
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
<!--close Inventory modal-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>
////// function for open model to see the personal info
function checkPersonal(refid){
	$.get('personal_status.php?pk=' + refid, function(html){
		 $('#viewModal .modal-body').html(html);
		 $('#viewModal').modal({
			show: true,
			backdrop:"static"
		});
		makeTable();
	 });
	 $("#viewModal #tile_name").html("<i class='fa fa-user'></i> Personal ");
}
////// function for show/hide password //////////s
function showPass(val){
	if(val == 1){
		document.getElementById("pass_change").type = "text";
	}else{
		document.getElementById("pass_change").type = "password";
	}
}
</script>
</body>
</html>