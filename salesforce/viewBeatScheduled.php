<?php
require_once("../config/config.php");
$id = base64_decode($_REQUEST['refid']);
$sch_date = base64_decode($_REQUEST['sch_date']);
$sch_by = base64_decode($_REQUEST['sch_by']);
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
  <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script><body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-clock-o fa-lg"></i> Beat Scheduled Details</h2>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">      
      	<table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Ref. No.</label></td>
                <td width="30%"><?php echo ($id); ?></td>
                <td width="20%"><label class="control-label">Scheduled Date</label></td>
                <td width="30%"><?php echo ($sch_date); ?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry Name</label></td>
                <td><?php echo  getAdminDetails($sch_by,"name",$link1);?></td>
                <td><label class="control-label"></label></td>
                <td>&nbsp;</td>
              </tr>
            </tbody>
          </table>
	<!-- row -->
	<div class="row">
					<h4 align="center">Beat Scheduled Summary</h4>
					<!-- widget content -->
					<div class="widget-body no-padding">

						<table id="dt_basic5" class="table table-striped table-bordered table-hover" width="100%">
							<thead>
								<tr class="<?=$tableheadcolor?>">					
			<th><strong>Plan Name</strong> </th>
            <th><strong>Plan Date</strong></th>
            <th><strong>Assigned To</strong></th>
            <th></i><strong>Visit Area</strong></th>
            <th><strong>Task Target Count</strong></th>
              </tr>
							</thead>
                           
                <tbody>
                  <?php
						$sno=0;
								$tsql=mysqli_query($link1,"SELECT * FROM pjp_data WHERE document_no ='".$id."'");
								if($tsql!=FALSE)
								{
										while($trow=mysqli_fetch_assoc($tsql))
										{
										$sno=$sno+1;
									 ?>	
								
                 	<tr title="" class="even pointer">
                    <td><?php echo $trow['task'];?></td>
                    <td><?php echo $trow['plan_date'];?></td>
                    <td><?php echo getAdminDetails($trow['assigned_user'],"name",$link1);?></td>
                    <td><?php echo $trow['visit_area'];?></td>
                    <td><?php echo $trow['task_count'];?></td>
                  </tr>
                 <?php }}?>
 </tbody>
      </table>
    </div>
									<!-- end widget content -->
				
							</div>
							<!-- end widget -->
      	
      </div><!--End form group-->
      <div class="form-group">
            <div class="col-md-12" align="center"><button title="Back" type="button" class="btn btn-primary" onClick="window.location.href='beatScheduled.php?refid=<?php echo $id;?>&sch_date=<?php echo $sch_date;?>&sch_by=<?php echo $sch_by;?><?=$pagenav?>'"><span>Back</span></button></div>
          </div> 
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>