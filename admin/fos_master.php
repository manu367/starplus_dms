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
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-book"></i>&nbsp;FOS Master</h2>
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
	  
      <form class="form-horizontal" role="form">
	   <div class="col-md-12"><label class="col-md-5 control-label"></label>	  
			
        <button title="Add New FOS" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='add_fos.php?op=add<?=$pagenav?>'"><span>Add New FOS</span></button>&nbsp;&nbsp; 
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr>
              <th><a href="#" name="entity_id" title="asc" ></a>#</th>
			  <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>FOS Name</th>
              <th><a href="#" name="name" title="asc" class="not-sort"></a>State</th>
              
              <th><a href="#" name="name" title="asc" ></a>Account No.</th>
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Identity Proof</th>
              
              <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Status</th>
			   <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Change Status</th>
			  <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Edit</th>
			 
             <th data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Mapping</th>
			  
              
            </tr>
          </thead>
          <tbody>
             <?php $i=1;
			$sql1 = "SELECT * FROM fos_master  order by id desc";
       $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   while($row1=mysqli_fetch_assoc($rs1)) { ?>
	    <tr class="even pointer">
		<td><?php echo $i ;?><div align="left"></div></td>
		 <td><?php echo $row1['name']?></td>
		<td><?php echo $row1['state']?></td>
         
          <td align="right"><?php echo $row1['acc_no']?></td>
          <td><?php echo $row1['identity']?></td>
         <td align="center"><?php echo $row1['status']?></td>
		 
			   <td><a href='active_fos.php?op=status&id=<?php echo base64_encode($row1['id']);?>&status=<?php echo $row1['status'];?><?=$pagenav?>'  title='view'>Change Status</td>
          <td align="center"><a href='edit_fos.php?op=edit&id=<?php echo base64_encode($row1['id']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="edit details"></i></a></td>
		 
		  <td align="center"><a href='mapping.php?op=mapping&id=<?php echo base64_encode($row1['name']);?><?=$pagenav?>'  title='view'><i class="fa fa-map-signs fa-lg" title="mapping details"></i></a></td>
            </tr>
	   <?php 
	  $i++;
	   }?>
	   
	  
          </tbody>
          </table>
      </div>
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