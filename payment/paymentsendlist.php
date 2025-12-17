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
 
 <!-- function to cancel paymnet -->
<script>
 function cancel_payment(id,loc)
 {
 //alert(id);
 var x = confirm("Do you want to delete ?");
  if(x){
	window.location="cancel_payment.php?id="+id+"&str=del_payment"; 
   }
 }
 </script> 
 <!-- end function to cancel paymnet -->
 
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
      <h2 align="center"><i class="fa fa-rupee"></i> Payment List</h2>
      <?php if(isset($_REQUEST['msg']) && $_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
      <form class="form-horizontal" role="form">
        <button title="Add Payment" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='payment_send.php?op=add<?=$pagenav?>'"><span>Add New Payment</span></button>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th><a href="#" name="name" title="asc" ></a>From Location</th>
              <th><a href="#" name="name" title="asc" ></a>To Location</th>
              <th><a href="#" name="name" title="asc" ></a>Document No</th>
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Amount</th>
               <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Payment Date</th>
              <th data-hide="phone,tablet"><a href="#" name="date" title="asc" class="not-sort"></a>Status</th>
              <th data-hide="phone,tablet">View/Edit/Delete</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			///// get access location ///
			$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
			$sql=mysqli_query($link1,"SELECT * FROM payment_send WHERE from_location IN (".$accesslocation.") ORDER BY id DESC");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo str_replace("~",",",getLocationDetails($row['from_location'],"name,city,state",$link1));?></td>
              <td><?php echo str_replace("~",",",getVendorDetails($row['to_location'],"name,city,state",$link1));?></td>
              <td><?php echo $row['doc_no'];?></td>
              <td align="right"><?php echo $row['amount'];?></td>
               <td align="center"><?php echo dt_format($row['entry_dt']);?></td>
              <td <?php if($row['status']=="PFA"){ echo "class='red_small'";}?>><?php echo $row['status'];?></td>
              <td align="center"><a href='payment_send_details.php?op=edit&location=<?php echo $row['from_location']?>&id=<?php echo base64_encode($row['doc_no']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a>   <?php if($row['status']=="PFA"){?>&nbsp; <a href='edit_paymentsend.php?op=edit&location=<?php echo $row['from_location']?>&id=<?php echo base64_encode($row['doc_no']);?><?=$pagenav?>'  title='edit'> <i class="fa fa-edit fa-lg" title="Edit payment"></i></a><?php } ?>&nbsp; <?php if($row['status']!='Cancelled'){?><a class='btn yellow' onClick="cancel_payment('<?php echo $row['doc_no']?>','<?php echo $row['from_location']?>')" title='Cancel'><i class='fa fa-trash fa-lg' title="Cancel payment"></i></a><?php } ?></td>
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