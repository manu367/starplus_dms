<?php
require_once("../config/config.php");
@extract($_POST);
////// filters value/////
$filter_str = 1;
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " and DATE(entry_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " and DATE(entry_date) <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['feedback_by'] !=''){
	$filter_str	.= " and entry_by = '".$_REQUEST['feedback_by']."'";
}
if($_REQUEST['feedback_type'] !=''){
	$filter_str	.= " and module = '".$_REQUEST['feedback_type']."'";
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
$(document).ready(function(){
    /////// datatable
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
                <div class="form-group">
                  <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Feedback From</label>
                     <div class="col-sm-5 col-md-5 col-lg-5 input-append date">
                        <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate" autocomplete="off" id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo "";}?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                     </div>
                  </div> 
                  <div class="col-md-6"><label class="col-md-5 control-label">Feedback To</label>
                    <div class="col-md-5 input-append date">
                        <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate" autocomplete="off" id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo "";}?>"style="width:160px;"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                    </div>
                  </div>
                </div><!--close form group-->
                <div class="form-group">
                  <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Feedback By</label>
                     <div class="col-sm-5 col-md-5 col-lg-5">
                        <select  name='feedback_by' id="feedback_by" class='form-control selectpicker' data-live-search="true">
                          <option value=''>All</option>
                          	<?php
                        	$sql = "SELECT username,name,oth_empid FROM admin_users where 1 order by name";
                        	$res = mysqli_query($link1,$sql);
                        	while($row = mysqli_fetch_array($res)){
                        	?>
                          <option value="<?=$row['username']?>"<?php if($_REQUEST['feedback_by']==$row['username']){echo 'selected';}?>><?=$row['name']." | ".$row['oth_empid']?></option>
                        	<?php
                        	}
                        	?>
                       </select>
                     </div>
                  </div> 
                  <div class="col-md-6"><label class="col-md-5 control-label">Feedback Type</label>
                    <div class="col-md-3">
                       <select name="feedback_type" id="feedback_type" class="form-control">
                          <option value=''>All</option>
                          <option value='Complaints'<?php if($_REQUEST['feedback_type']=="Complaints"){echo "selected";}?>>Complaints</option>
                          <option value='General'<?php if($_REQUEST['feedback_type']=="General"){echo "selected";}?>>General</option>
                          <option value='Query'<?php if($_REQUEST['feedback_type']=="Query"){echo "selected";}?>>Query</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                    	<input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
                       	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                       	<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                    </div>
                  </div>
                </div><!--close form group-->
                 <!--close form group-->
                <div class="form-group">
                  <div class="col-md-6">&nbsp;</div>
                  <div class="col-md-6">
                    <label class="col-md-5 control-label"></label>
                    <div class="col-md-5" align="left">
                      <?php
						//// get excel process id ////
						//$processid = getExlCnclProcessid("Admin Users", $link1);
						////// check this user have right to export the excel report
						//if (getExcelRight($_SESSION['userid'], $processid, $link1) == 1) {
						?>
                      <a href="excelexport.php?rname=<?=base64_encode("feedbackReport")?>&rheader=<?=base64_encode("Feedback")?>&feedback_by=<?=base64_encode($_REQUEST['feedback_by'])?>&feedback_type=<?=base64_encode($_REQUEST['feedback_type'])?>&fromDate=<?=base64_encode($_REQUEST['fdate'])?>&toDate=<?=base64_encode($_REQUEST['tdate'])?>" title="Export feedback details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export feedback details in excel"></i></a>
                      <?php
						//}
						?>
                    </div>
                  </div>
                </div>
                <!--close form group-->
              </form>
      		<div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       		<table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          		<thead>
            		<tr class="<?=$tableheadcolor?>">
              			<th width="5%"><a href="#" name="entity_id" title="asc" ></a>S.No.</th>
                        <th width="13%" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Feedback Type</th>
                        <th width="17%">Feedback For</th>
                        <th width="17%"><a href="#" name="name" title="asc" ></a>Subject</th>
                        <th width="22%" data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Feedback</th>
                        <th width="12%" data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Updated By</th>
                        <th width="12%" data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Updated Date</th>
                        <th width="7%" data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>View Location</th>
                        <th width="12%" data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Image</th>
            		</tr>
          		</thead>
          		<tbody>
             	<?php 
				$i=1;
				$fb_sql = "SELECT * FROM query_master where ".$filter_str." order by id desc";
       			$fb_res = mysqli_query($link1,$fb_sql) or die(mysqli_error($link1));
	   			while($fb_row = mysqli_fetch_assoc($fb_res)) { 
					$expl = explode("~",$fb_row["request"]);
				?>
	    			<tr>
						<td><?php echo $i ;?></td>
                        <td><?php echo $fb_row['problem']?></td>
                        <td><?php echo $fb_row['module']?></td>
                        <td><?php echo $expl[0]?></td>
                        <td><?php echo $expl[1]?></td>
                        <td><?php echo getAdminDetails($fb_row['entry_by'],"name",$link1);?></td>
                        <td><?php echo $fb_row['entry_date']?></td>
                        <td align='center'><a href='#' id="loc<?=$i?>" title='Location Details' data-toggle='popover' data-trigger='focus' data-content='<?=$fb_row['address']?>'><i class='fa fa-map-marker fa-lg'></i></a></td>
                        <td align='center'><?php if ($fb_row['attachment'] != '') { ?><img src="../salesapi/feedbackimg/<?=substr($fb_row['entry_date'],0,7)?>/<?= $fb_row['attachment']; ?>" alt="" id="image<?=$i?>" onClick="getThisValue(<?=$i?>)" style="width: 100%;"/><?php }else{ echo "Not clicked";} ?></td>         
            		</tr>
	   			<?php 
	  			$i++;
				}
	   			?>  
          		</tbody>
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