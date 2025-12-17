<?php
require_once("../config/config.php");
@extract($_POST);
$date=date("Y-m-d");
////// filters value/////
$filter_str = 1;
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " and DATE(entry_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " and DATE(entry_date) <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST["task_type"]){
	$filter_str	.= " and task = '".$_REQUEST['task_type']."'";
}
if($_REQUEST["assign_to"]){
	$filter_str	.= " and assigned_user = '".$_REQUEST['assign_to']."'";
}
//////End filters value/////
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.js"></script>
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
});
</script>
<style type="text/css">
    div.dropdown-menu.open
    {
        max-width:200px !important;
        overflow:hidden;
    }
    ul.dropdown-menu.inner
    {
        max-width:200px !important;
        overflow-y:auto;
    }
</style>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      		<h2 align="center"><i class="fa fa-clock-o fa-lg"></i>&nbsp;Beat Scheduled</h2>
      		<?php if($_REQUEST['msg']){?><br>
      			<h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      		<?php }?>
            <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="post">
                <div class="form-group">
                  <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Scheduled From</label>
                     <div class="col-md-5 input-append date">
                        <div style="display:inline-block;float:left;"><input type="date" class="form-control span2" name="fdate" id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>"></div>
                     </div>
                  </div> 
                  <div class="col-md-6"><label class="col-md-5 control-label">Scheduled To</label>
                    <div class="col-md-5 input-append date">
                        <div style="display:inline-block;float:left;"><input type="date" class="form-control span2" name="tdate" id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>"style="width:160px;"></div>
                    </div>
                  </div>
                </div><!--close form group-->
                <div class="form-group">
                  <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Task Type</label>
                     <div class="col-md-5">
                        <select name='task_type' id='task_type' class="form-control">
                          <option value="">All</option>	
						 <?php
                         $res_task = mysqli_query($link1,"SELECT task_name FROM pjptask_master WHERE status='A' order by task_name");	
                         while($row_task = mysqli_fetch_assoc($res_task)){
                         ?>
                         <option value="<?=$row_task["task_name"]?>"<?php if($_REQUEST["task_type"]==$row_task["task_name"]){ echo "selected";}?>><?=$row_task["task_name"]?></option>
                         <?php 
                         }
                         ?>
                      </select>
                     </div>
                  </div> 
                  <div class="col-md-6"><label class="col-md-5 control-label">Assigned Users</label>
                    <div class="col-md-5">
                        <select name='assign_to' id='assign_to' class="form-control selectpicker" data-live-search="true">
                        	<option value="">All</option>
							 <?php
                             $res_user = mysqli_query($link1,"SELECT username,name,oth_empid FROM admin_users WHERE utype='7' AND status='Active' AND owner_code='DEHOHR001' order by name");	
                             while($row_user = mysqli_fetch_assoc($res_user)){
                             ?>
                             <option value="<?=$row_user["username"]?>"<?php if($_REQUEST["assign_to"]==$row_user["username"]){ echo "selected";}?>><?=$row_user["name"]." | ".$row_user["oth_empid"]." | ".$row_user["username"]?></option>
                             <?php 
                             }
                             ?>
                        </select>
                    </div>
                     <div class="col-md-2">
                    	<input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
                       	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                       	<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                    </div>
                  </div>
                </div><!--close form group-->
              </form>
              <div align="center">
              	 <a href="excelexport.php?rname=<?=base64_encode("beatscheduled")?>&rheader=<?=base64_encode("Beat Scheduled")?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>&assign_to=<?=base64_encode($_REQUEST['assign_to'])?>&task_type=<?=base64_encode($_REQUEST['task_type'])?>" title="Export in excel"><i class="fa fa-file-excel-o fa-2x" title="Export in excel"></i></a>
              </div>
				<form class="form-horizontal" role="form">	
      			<div class="form-group"  id="page-wrap" style="margin-left:10px;">
       				<table width="95%" id="myTable" class="table-bordered table-hover" align="center">
          				<thead>
							<tr class="<?=$tableheadcolor?>">
                                <th width="5%">#</th>
                                <th width="30%">Ref. No.</th>
                                <th width="20%">Scheduled Date</th>
                                <th width="30%">Scheduled By</th>
                                <th width="15%">View</th>
                            </tr>
          				</thead>
          				<tbody>
             			<?php 
						$i=1;
						$sql1 = "SELECT * FROM pjp_data WHERE ".$filter_str." GROUP BY document_no ORDER BY id DESC";
       					$rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   					while($row1=mysqli_fetch_assoc($rs1)) { ?>
	    					<tr>
								<td><?php echo $i ;?></td>
                                <td><?php echo $row1['document_no']?></td>
                                <td><?php echo $row1['entry_date']?></td>
                                <td><?php echo getAdminDetails($row1['entry_by'],"name",$link1);?></td>
                                <td align="center"><a class='btn btn-success' href='viewBeatScheduled.php?refid=<?php echo base64_encode($row1['document_no']);?>&sch_date=<?php echo base64_encode($row1['entry_date']);?>&sch_by=<?php echo base64_encode($row1['entry_by']);?><?=$pagenav?>' title='View'><i class='fa fa-eye'></i></a></td>
                            </tr>
	   					<?php 
	  						$i++;
						}
	   					?>  
          				</tbody>
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