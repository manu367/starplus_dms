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
      <h2 align="center"><i class="fa fa-align-justify"></i> Application </h2>
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
        <button title="Apply for Loan" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='application_emp_loan.php?<?=$pagenav?>'"><i class="fa fa-plus-circle fa-lg" ></i> &nbsp; <span>New Loan </span></button>
        <button title="Apply for V Card" type="button" class="btn <?=$btncolor?>" style="float:right;margin-right:5px;" onClick="window.location.href='application_emp_vcard.php?<?=$pagenav?>'"><i class="fa fa-plus-circle fa-lg" ></i> &nbsp; <span>New Visiting Card </span></button>
        <button title="Apply for I Card" type="button" class="btn <?=$btncolor?>" style="float:right;margin-right:5px;" onClick="window.location.href='application_emp_icard.php?<?=$pagenav?>'"><i class="fa fa-plus-circle fa-lg" ></i> &nbsp; <span>New ID Card </span></button>
        </div>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th style="text-align:center;" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Type</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Apply By</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Request Date</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Status</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>View</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			
			$sql=mysqli_query($link1,"Select * from hrms_request_master where emp_id='".$_SESSION['userid']."' and type in ('IR','VR','LR') order by id desc");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
			?>
            <tr class="even pointer">
              <td style="text-align:center;" ><?php echo $sno;?></td>
              <td><?php echo $row['name']; ?></td>
              <td style="text-align:center;" ><?php echo getAnyDetails($row['emp_id'],'empname','loginid','hrms_employe_master',$link1)." | ".$row['emp_id']; ?></td>
              <td style="text-align:center;" ><?php echo dt_format($row['update_date']); ?></td>
              <td style="text-align:center;" ><?php echo $row['status']; ?></td>
              <td align="center"><a href='application_emp_view.php?id=<?php echo base64_encode($row['id']);?><?=$pagenav?>'  title='View'><i class="fa fa-eye fa-lg" title="View"></i></a></td>
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