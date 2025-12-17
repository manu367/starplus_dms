<?php
////// Function ID ///////
$fun_id = array("u"=>array(92)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

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
      <h2 align="center"><i class="fa fa-reply"></i> Stock Transfer List</h2>
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
      <form class="form-horizontal" role="form">
        <button title="Add New Stock Transfer" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='stock_transferN.php?op=add<?=$pagenav?>'"><span>Add New Stock Transfer</span></button>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th><a href="#" name="name" title="asc" ></a>Stock Transfer To</th>
              <th><a href="#" name="name" title="asc" ></a>Stock Transfer From</th>
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Document No.</th>
              <th data-hide="phone,tablet"><a href="#" name="date" title="asc" class="not-sort"></a>Document Date</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Entry By</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
              <th data-hide="phone,tablet">Print</th>
              <th data-hide="phone,tablet">View</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			///// get access location ///
			$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
			$sql=mysqli_query($link1,"Select * from billing_master where from_location in (".$accesslocation.") and type = 'STN' order by id desc");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo str_replace("~",",",getLocationDetails($row['to_location'],"name,city",$link1));?></td>
              <td><?php echo str_replace("~",",",getLocationDetails($row['from_location'],"name,city",$link1));?></td>
              <td><?php echo $row['challan_no'];?></td>
              <td><?php echo $row['sale_date'];?></td>
              <td><?php echo getAdminDetails($row['entry_by'],"name",$link1);?></td>
              <td <?php if($row['status']=="PFA"){ echo "class='red_small'";}?>><?php echo $row['status'];?></td>
              <td align="center"><a href='../print/print_stn.php?rb=view&id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>' target="_blank"  title='Print PO'><i class="fa fa-print fa-lg" title="Print PO"></i></a></td>
			  <td align="center"><a href='stnDetails.php?id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
			 
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