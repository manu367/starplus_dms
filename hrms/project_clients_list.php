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
      <h2 align="center"><i class="fa fa-gear"></i> Project / Clients </h2>
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
        <button title="Add Notice" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='project_clients_add.php?<?=$pagenav?>'"><i class="fa fa-plus-circle fa-lg" ></i> &nbsp; <span>Add Project </span></button></div>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th style="text-align:center;" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Project Id</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Project</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Entry Date</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Client</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Manager</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Tentative Date</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Status</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>View/Edit</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			
			$sql=mysqli_query($link1,"Select * from hrms_project_master where handle_by = '".$_SESSION['userid']."' order by id desc");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
			?>
            <tr class="even pointer">
              <td style="text-align:center;" ><?php echo $sno;?></td>
              <td style="text-align:center;" ><?php echo $row['project_id']; ?></td>
              <td><?php echo $row['project_name']; ?></td>
              <td style="text-align:center;" ><?php echo dt_format($row['entry_date']); ?></td>
              <td><?php echo $row['client']; ?></td>
              <td style="text-align:center;" ><?php echo getAnyDetails($row['manage_by'],'empname','loginid','hrms_employe_master',$link1)." | ".$row['manage_by']; ?></td>
              <td style="text-align:center;" ><?php echo dt_format($row['tentative_date']); ?></td>
              <td style="text-align:center;" ><?php echo $row['status']; ?></td>
              <td align="center"><a href='project_clients_edit.php?id=<?php echo base64_encode($row['project_id']);?><?=$pagenav?>'  title='Edit'><i class="fa fa-edit fa-lg" title="Edit"></i></a></td>
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