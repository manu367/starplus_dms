<?php
require_once("../config/config.php");
@extract($_GET);
////// filters value/////
## selected state
if($locationstate!=""){
	$loc_state="state='".$locationstate."'";
}else{
	$loc_state="1";
}
## selected product
if($product!=""){
	$product="product_code='".$product."'";
}else{
	$product="1";
}
## selected location type
if($locationtype!=""){
	$loc_type="location_type='".$locationtype."'";
}else{
	$loc_type="1";
}

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
 <script type="text/javascript" src="../js/ajax.js"></script>
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
      <h2 align="center"><i class=""></i>&nbsp;App Retailer</h2>
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
      <form class="form-horizontal" role="form">  			
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr>
              <th><a href="#" name="entity_id" title="asc" ></a>#</th>
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Name</th>
              <th><a href="#" name="name" title="asc" ></a>City</th>
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>State</th>
              <th data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Latitude</th>
			   <th data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Longitude</th>
         
            </tr>
          </thead>
          <tbody>
             <?php $i=1;
			 
			$sql1 = "SELECT * FROM my_retailer";
       $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   while($row1=mysqli_fetch_assoc($rs1)) { ?>
	    <tr class="even pointer">
		<td><?php echo $i ;?><div align="left"></div></td>
			<td><div align="left">&nbsp;<?php echo $row1['name']?></div></td>
          <td><div align="right">&nbsp;<?php echo $row1['city']?></div></td>
          <td align="right"><?php echo $row1['state']?></td>
           <td align="left"><?php echo $row1['latitude']?></td>
		   <td align="left"><?php echo $row1['longitude']?></td>
         
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
<?php
include("../includes/footer.php");
?>
</body>
</html>