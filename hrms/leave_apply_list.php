<?php
require_once("../config/config.php");
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
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
    <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-lightbulb-o"></i> Leave apply / View Status </h2>
     <?php if($_REQUEST['msg']!=''){?>
      	<h4 align="center">
        	<span 
			<?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span>
        </h4>
	  <?php }?>
      <form class="form-horizontal" role="form">
        <div style="display:inline-block;float:right">
        <button title="Add Department" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='leave_apply_add.php?<?=$pagenav?>'"><i class="fa fa-plus-circle fa-lg" ></i> &nbsp; <span> Leave Apply </span></button></div>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th style="text-align:center;" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Emp. Name</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Type of Leave</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>From Date</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>To Date</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Duration</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Purpose</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Status</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>View</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			
			$sql=mysqli_query($link1,"Select * from hrms_leave_request where empid='".$_SESSION['userid']."' order by id desc");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
			?>
            <tr class="even pointer">
              <td style="text-align:center;" ><?php echo $sno;?></td>
              <td><?php echo getAnyDetails($_SESSION['userid'],"empname","loginid","hrms_employe_master",$link1); ?></td>
              <td><?php echo $row['leave_type']; ?></td>
              <td style="text-align:center;" ><?php echo dt_format($row['from_date']); ?></td>
              <td style="text-align:center;" ><?php echo dt_format($row['to_date']); ?></td>
              <td style="text-align:center;" ><?php echo $row['leave_duration']; ?></td>
              <td><?php echo $row['purpose']; ?></td>
              <td><?php if($row['status']=='4'){ echo "Approved"; }else if($row['status']=='3'){ echo "Pending for Approval"; }else{ echo $row['status']; } ?></td>
              <td align="center"><a href='leave_apply_view.php?id=<?php echo base64_encode($row['id']);?><?=$pagenav?>'  title='Edit'><i class="fa fa-eye fa-lg" title="Edit"></i></a></td>
            </tr>
            <?php }?>
          </tbody>
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