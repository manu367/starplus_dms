<?php
require_once("../config/config.php");
@extract($_POST);
////// filters value/////
if($_REQUEST['fdate']=="" && $_REQUEST['tdate']==""){
	$filter_str = " entry_date = '".$today."'";
}else{
	$filter_str = "1";
}
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " and entry_date >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " and entry_date <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['isp_name'] !=''){
	$filter_str	.= " and userid = '".$_REQUEST['isp_name']."'";
}
//////End filters value/////
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=siteTitle?></title>
<script src="../js/jquery-1.10.1.min.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
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
});
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
    		<div class="col-sm-9">
      			<h2 align="center"><i class="fa fa-child"></i>&nbsp;Track Location</h2>
      			<?php if($_REQUEST['msg']){?>
	  			<h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      			<?php }?>
                <div class="form-group" id="page-wrap" style="margin-left:10px;">
	  				<form class="form-horizontal" role="form" name="form1" action="" method="get">
	  					<div class="form-group">
          					<div class="col-md-10"><label class="col-md-3 control-label">From Date</label>
              					<div class="col-md-3 input-append date">
  									<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
			   					</div>
              					<label class="col-md-3 control-label">To Date</label>
             					<div class="col-md-3 input-append date">
  									<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>" style="width:160px;" required></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
			   					</div>
          					</div>
        				</div>	    
        				<div class="form-group">
          					<div class="col-md-6"><label class="col-md-5 control-label">Users</label>
            					<div class="col-md-5">
               						<select name="isp_name" id="isp_name" class="form-control selectpicker" data-live-search="true">
                    					<option value="">All</option>
										<?php    
                                        $sql = "SELECT name,username,oth_empid FROM admin_users ORDER BY name";
                                        $sqlres1 = mysqli_query($link1, $sql);                                        
                                        while($ispname = mysqli_fetch_assoc($sqlres1)){ 
										?>
                    					<option value="<?=$ispname['username']?>" <?php if($ispname['username']==$_REQUEST['isp_name']){ echo 'selected'; } ?>><?=$ispname['name']." | ".$ispname['username']." ".$ispname['oth_empid']?></option>
                    						<?php 
										} ?>
                					</select>
            					</div>
          					</div>
		  					<div class="col-md-6"><label class="col-md-5 control-label"></label>	  
								<div class="col-md-3" align="left">	
                                    <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                                    <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                                    <input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
            					</div>
            					<div class="col-md-2" align="left">	
               						<a href="excelexport.php?rname=<?=base64_encode("trackuser")?>&rheader=<?=base64_encode("Track User")?>&user=<?=base64_encode($_REQUEST['isp_name'])?>&fdate=<?=$_REQUEST['fdate']?>&tdate=<?=$_REQUEST['tdate']?>" title="Export Track details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Track details in excel"></i></a>
            					</div>
          					</div>
	    				</div><!--close form group-->
	  				</form>
      				<form class="form-horizontal" role="form">
      				<!--	<div class="form-group"  id="page-wrap" style="margin-left:10px;">-->
       						<table width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          						<thead>
            						<tr class="<?=$tableheadcolor?>">
                                        <th><a href="#" name="entity_id" title="asc" ></a>#</th>
                                        <th><a href="#" name="name" title="asc" class="not-sort"></a>User Id</th>
                                        <th><a href="#" name="name" title="asc" class="not-sort"></a>Total Visit</th>
                                        <th><a href="#" name="name" title="asc" class="not-sort"></a>Total Air Distance Covered(in KM)</th>
                                        <th><a href="#" name="name" title="asc" class="not-sort"></a>Travel Date</th>
                                        <th><a href="#" name="name" title="asc" class="not-sort"></a>View</th>
                                    </tr>
                               	</thead>
                                <tbody>
								<?php 
                                $i=1;
                                $sql1 = "SELECT userid, entry_date, SUM(travel_km) AS totdist, COUNT(id) AS novisit FROM user_track WHERE ".$filter_str." GROUP BY userid,entry_date ORDER BY entry_date DESC";
                                $rs1 = mysqli_query($link1,$sql1);
                                while($row1=mysqli_fetch_assoc($rs1)) { 
                                ?>
	    							<tr class="even pointer">
                                        <td><?php echo $i ;?></td>
                                        <td><?php echo str_replace("~",",",getAdminDetails($row1['userid'],"name,username",$link1));?></td>
                                        <td><?=$row1["novisit"]?></td>
                                        <td><?php echo $row1['totdist']?></td>
                                        <td><?php  echo $row1['entry_date'];?></td>
                                        <td><a href='view_distance_covered.php?rb=view&id=<?php echo base64_encode($row1['userid']);?>&travel_date=<?php echo base64_encode($row1['entry_date']);?>&total_distance=<?php echo base64_encode($row1['totdist']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
            						</tr>
								<?php 
                                $i++;
                                }
								?>
          						</tbody>
          					</table>
      					<!--</div>-->
      				</form>
	  			</div>
    		</div>
  		</div>
	</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>