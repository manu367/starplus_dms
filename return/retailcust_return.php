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
      <h2 align="center"><i class="fa fa-reply-all fa-lg"></i> Retail Customer Return List</h2>
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
      <form class="form-horizontal" role="form">
        <button title="Add Retail Return" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='add_retailcustreturn.php?op=add<?=$pagenav?>'"><span>Add New Retail Return</span></button>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th><a href="#" name="name" title="asc" ></a>Retail Return To</th>
              <th><a href="#" name="name" title="asc" ></a>Retail Return From</th>
			  <th><a href="#" name="name" title="asc" ></a>Invoice No.</th>
              <th data-hide="phone,tablet"><a href="#" name="date" title="asc" class="not-sort"></a> Date </th>
            <th data-hide="phone,tablet"><a href="#" name="date" title="asc" class="not-sort"></a> Status </th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a> Entry By </th>
              <th width="8%" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Print</th>
              <th data-hide="phone,tablet">View</th>
              <th data-hide="phone,tablet">Cancel</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			/// get access location ///
			
			$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
			$sql=mysqli_query($link1,"Select * from billing_master where from_location in (".$accesslocation.") and document_type='RETAIL PRN' order by id desc");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo str_replace("~",",",getLocationDetails($row['from_location'],"name,city,state",$link1));?></td>
              <td><?php echo str_replace("~",",",getCustomerDetails($row['to_location'],"customername,city,state",$link1));?></td>
              <td><?php echo $row['challan_no'];?></td>
              <td><?php echo $row['sale_date'];?></td>
              <td><?php echo $row['status'];?></td>
              <td><?php echo getAdminDetails($row['entry_by'],"name",$link1);?></td>
                 
             <td align="center"><a href='../print/printretail_return.php?id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>' target="_blank"  title='Print Invoice'><i class="fa fa-print fa-lg" title="Print Invoice"></i></a></td>
                  
              <td align="center"><a href='retailReturnDetails.php?op=edit&id=<?php echo base64_encode($row['challan_no']);?>&from=<?php echo base64_encode($row['to_location']);?>&to=<?php echo base64_encode($row['from_location']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
            <td align="center">
	<?php  if($row['status'] != 'Cancelled') { ?><a href='cancelRetailReturn.php?id=<?php echo base64_encode($row['challan_no']);?>&from=<?php echo base64_encode($row['to_location']);?>&to=<?php echo base64_encode($row['from_location']);?><?=$pagenav?>' title='Cancel Retail Return'><i class="fa fa-remove fa-lg" title="Cancel Retail Return"></i></a><?php }?>
                       </td>
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