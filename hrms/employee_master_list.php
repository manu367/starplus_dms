<?php
require_once("../config/config.php");
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
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
	$(document).ready(function(){
		$('#myTable').dataTable();
	});
 </script>
<script language="javascript">
</script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-users"></i> Employee Master</h2>
      <?php if($_REQUEST['msg']!=''){?>
      	<h4 align="center">
        	<span 
			<?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span>
        </h4>
	  <?php }?>
	  
      <form class="form-horizontal table-responsive" role="form">
      	 <button title="Add New Employee" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='employee_master_add.php?op=add<?=$pagenav?>'"><span>Add New Employee</span></button>
     	 <br/><br/>
       <table  width="99%" id="myTable" class="table-responsive table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th><a href="#" name="entity_id" title="asc"></a>#</th>
              <th data-hide="phone" data-class="expand"><a href="#" name="name" title="asc"></a>Employee Id</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Emp. Name</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>City</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>State</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Location Type</th>
			  <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
              <th data-hide="phone,tablet">View/Edit</th>
            </tr>
          </thead>
          <tbody>
          <?php
		    $i=1;
			
			$query="Select * from hrms_employe_master where createby = '".$_SESSION['userid']."' order by empid";
			$result=mysqli_query($link1,$query) or die(mysqli_error($link1));
			while($arr_result=mysqli_fetch_array($result)){
          ?>
            <tr>
              <td><?=$i?></td>
              <td><?=$arr_result['loginid']?></td>
              <td><?=$arr_result['empname']?></td>
              <td><?=$arr_result['city'];?></td>
              <td><?=$arr_result['state'];?></td>
              <td><?=$arr_result['utype'];?></td>
			  <td><?=$arr_result['status'];?></td>
              <td align="center"><a href='employee_master_edit.php?op=edit&id=<?php echo base64_encode($arr_result['empid']);?>&EmpId=<?php echo base64_encode($arr_result['loginid']);?><?=$pagenav?>'  title='view'><i class="fa fa-edit fa-lg" title="view details"></i></a></td>
            </tr>
          <?php
		  $i++;
		
			} 
          ?>
          </tbody>
       </table>
       </form>
      <!--</div>--><!--close form group-->
    </div><!--close tab pane-->
  </div><!--close row content-->
</div><!--close container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>