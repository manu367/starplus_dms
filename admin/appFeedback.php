<?php
require_once("../config/config.php");
@extract($_POST);
///////filter value
if($department){
	$dept = " AND department ='".$department."'";
}else{
	$dept = "";
}
if($subdepartment){
	$subdept = " AND subdepartment ='".$subdepartment."'";
}else{
	$subdept = "";
}
//////End filters value/////
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <style>
/* The Modal (background) */
.modal {
		display: none; /* Hidden by default */
		position: fixed; /* Stay in place */
		z-index: 1; /* Sit on top */
		padding-top: 50px; /* Location of the box */
		left: 0;
		top: 0;
		width: 100%; /* Full width */
		height: 100%; /* Full height */
		overflow: auto; /* Enable scroll if needed */
		background-color: rgb(0,0,0); /* Fallback color */
		background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
	}

	/* Modal Content */
	.modal-content {
		background-color: #fefefe;
		margin: auto;
		padding: 20px;
		border: 1px solid #888;
		width: 50%;
		height: 50%;
		margin-top: 20px;
	}

	/* The Close Button */
	.close {
		color: #aaaaaa;
		float: right;
		font-size: 28px;
		font-weight: bold;
	}

	.close:hover,
	.close:focus {
		color: #000;
		text-decoration: none;
		cursor: pointer;
	}
</style>
 <title><?=siteTitle?></title>
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
/////// datatable
$(document).ready(function() {
	var dataTable = $('#myTable').DataTable( {
		"processing": true,
		"serverSide": true,
		//"bStateSave": true,
		"order": [[ 0, "desc" ]],
		"ajax":{
			url :"../pagination/appfb-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>", "fdate": "<?=$_REQUEST['fdate']?>", "tdate": "<?=$_REQUEST['tdate']?>", "feedback_by": "<?=$_REQUEST['feedback_by']?>", "feedback_type": "<?=$_REQUEST['feedback_type']?>", "department": "<?=$_REQUEST['department']?>", "subdepartment": "<?=$_REQUEST['subdepartment']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".employee-grid-error").html("");
				$("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#employee-grid_processing").css("display","none");
				
			}
		}
	});
}); 
$(document).ready(function(){
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
	$('[data-toggle="popover"]').popover({
		trigger : 'hover',
		placement:'top',
		html : true
	});
	$('#myTable').on( 'draw.dt', function () {
   		 rePop();
	});
});
function rePop(){
	$('[data-toggle="popover"]').popover({
		trigger : 'hover',
		placement : 'top',
		html : true
	}); 
}
/*$(document).ready(function() {
    var eventFired = function ( ) {
        rePop();     
    }
 
    $('#myTable')
        .on( 'order.dt',  function () { eventFired( 'Order' ); } )
        .on( 'search.dt', function () { eventFired( 'Search' ); } )
        .on( 'page.dt',   function () { eventFired( 'Page' ); } )
        .dataTable();
});*/

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
    	<div class="col-sm-9 tab-pane fade in active" id="home">
      		<h2 align="center"><i class="fa fa-file"></i>&nbsp;App Feedback</h2>
      		<?php if($_REQUEST['msg']){?><br>
      		<h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      		<?php }?>
            <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="post">
            	<div class="row">
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">From Date</label>
                        <input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>" required>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
                        <input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>" required>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Department</label>
                        <select name='department' id='department' class="form-control" onChange="document.form1.submit();">
                            <option value="">All</option>
                            <?php
                            $res_dept=mysqli_query($link1,"select * from hrms_department_master where status='1' order by dname")or die("erro1".mysqli_error($link1));
                            while($row_dept=mysqli_fetch_assoc($res_dept)){
                            ?>
                            <option value="<?=$row_dept['departmentid']?>"<?php if($_REQUEST['department'] ==$row_dept['departmentid']) { echo 'selected'; }?>><?=$row_dept['dname'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Sub-Department</label>
                    	<span id="subdptdiv">
                        <select name='subdepartment' id='subdepartment' class="form-control" onChange="document.form1.submit();">
                            <option value="">All</option>
                            <?php
                            $res_sdept=mysqli_query($link1,"select * from hrms_subdepartment_master where status='1' AND departmentid='".$_REQUEST['department']."' order by department,subdept")or die("erro1".mysqli_error($link1));
                            while($row_sdept=mysqli_fetch_assoc($res_sdept)){
                            ?>
                            <option value="<?=$row_sdept['subdeptid']?>"<?php if($_REQUEST['subdepartment'] ==$row_sdept['subdeptid']) { echo 'selected'; }?>><?=$row_sdept['subdept'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                        </span>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Employee Name</label>
                        <select name="feedback_by" id="feedback_by" class="form-control selectpicker" data-live-search="true">
                        <option value="">All</option>
                        <?php
						$sql = mysqli_query($link1, "SELECT name,username,oth_empid FROM admin_users WHERE status='active' ".$dept." ".$subdept." ORDER BY name");
						while ($row = mysqli_fetch_assoc($sql)) {
                                        ?>
                        <option value="<?= $row['username']; ?>" <?php if ($_REQUEST['feedback_by'] == $row['username']) { echo "selected";}?>><?= $row['name']." | ".$row['username']." ".$row['oth_empid'];?>
                        </option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Type</label>
                        <select name="feedback_type" id="feedback_type" class="form-control">
                          <option value=''>All</option>
                          <option value='Complaints'<?php if($_REQUEST['feedback_type']=="Complaints"){echo "selected";}?>>Complaints</option>
                          <option value='General'<?php if($_REQUEST['feedback_type']=="General"){echo "selected";}?>>General</option>
                          <option value='Query'<?php if($_REQUEST['feedback_type']=="Query"){echo "selected";}?>>Query</option>
                        </select>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">&nbsp;</label><br/>
                        <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                        <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                        <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><br/>
                    	<?php if(daysDifference($_REQUEST['tdate'],$_REQUEST['fdate'])<=30 && $_REQUEST["Submit"]!=""){?>
                    	<a href="excelexport.php?rname=<?=base64_encode("feedbackReport")?>&rheader=<?=base64_encode("Feedback")?>&feedback_by=<?=base64_encode($_REQUEST['feedback_by'])?>&feedback_type=<?=base64_encode($_REQUEST['feedback_type'])?>&fromDate=<?=base64_encode($_REQUEST['fdate'])?>&toDate=<?=base64_encode($_REQUEST['tdate'])?>&department=<?=base64_encode($_REQUEST['department'])?>&subdepartment=<?=base64_encode($_REQUEST['subdepartment'])?>" title="Export feedback details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export feedback details in excel"></i></a>
                        <?php }else{ echo "You can download excel upto 30 days";}?>
                    </div>
                  </div>
              </form>
      		<div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       		<table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          		<thead>
            		<tr class="<?=$tableheadcolor?>">
              			<th width="2%">S.No.</th>
                        <th width="13%">Ref. No.</th>
                        <th width="10%">Feedback Type</th>
                        <th width="10%">Feedback For</th>
                        <th width="13%">Subject</th>
                        <th width="17%">Feedback</th>
                        <th width="10%">Updated By</th>
                        <th width="10%">Department</th>
                        <th width="10%">Updated Date</th>
                        <th width="5%">View Location</th>
                        <th width="10%">Image</th>
            		</tr>
          		</thead>
          	</table>
   		  </div>
		</div>
	</div>
</div>
<!-- The Modal -->
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
		<span class="close">&times;</span>
        <p id="img" style="text-align: center;"></p>
    </div>
</div>

<?php
include("../includes/footer.php");
?>
<script>
// Get the modal
var modal = document.getElementById('myModal');

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
function getThisValue(i) {
	var img = $("#image"+i).attr('src');
    $("#img").html('<img src="'+img+'" width="550px"/>');
    modal.style.display = "block";
}
// When the user clicks on <span> (x), close the modal
span.onclick = function() {
	modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
	if (event.target == modal) {
		modal.style.display = "none";
	}
}
        
//        $(document).on('click',"#myBtn1",function(){
//           
//        });
</script>
</body>
</html>