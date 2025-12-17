<?php
////// Function ID ///////
$fun_id = array("a"=>array(66));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_GET);
////// filters value/////

//////End filters value/////
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
      <h2 align="center"><i class="fa fa-map-marker"></i>&nbsp;State Master</h2>
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
      <form class="form-horizontal" role="form">
	   <div class="col-md-12"><label class="col-md-5 control-label"></label>	  
			
        <button title="Add New City" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addState.php?op=add<?=$pagenav?>'"><span>Add New State</span></button>&nbsp;&nbsp; 
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr>
              <th><a href="#" name="entity_id" title="asc" ></a>#</th>
              <th><a href="#" name="name" title="asc" class="not-sort"></a>State</th>
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Zone</th>
              <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Code</th>
			   <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>StateCode</th>
			  <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>View/Change </th>
             
			  
              
            </tr>
          </thead>
          <tbody>
        <?php $i=1;
		$sql1 = "SELECT * FROM state_master  order by state";
       $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   while($row1=mysqli_fetch_assoc($rs1)) { ?>
	    <tr class="even pointer">
		<td><?php echo $i ;?><div align="left"></div></td>
		<td><?php echo $row1['state']?></td>
        <td><?php echo $row1['zone']?></td>
        <td align="center"><?php echo $row1['code']?></td>
		<td align="center"><?php echo $row1['statecode']?></td>
          <td align="center"><a href='edit_state.php?op=edit&id=<?php echo base64_encode($row1['sno']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
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