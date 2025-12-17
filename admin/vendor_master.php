<?php
////// Function ID ///////
$fun_id = array("a"=>array(59));
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

<title>Distributer Management System</title>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
/////-------------------------------------------------
function confirmDel(store,st){
var where_to= confirm("Do you really want to Update this Record??");
if (where_to== true)
 {
  //alert(window.location.href)
  var url="<?php echo $DelAction ?>";
  window.location=url+store;
}
else
 {
return false;
  }
}
/////-------------------------------------------------
function confirmLogout(){
var where_to= confirm("Do you really want to Logout??");
if (where_to== true)
 {
  var url="<?php echo $logoutAction ?>";
  window.location=url;
}
else
 {
return false;
  }
}
////-------------->
</script>
</head>
<body onLoad="javascript:window.focus()">
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-shopping-basket"></i>&nbsp;Vendor Master</h2>
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
      <form class="form-horizontal" role="form" method="get">
	  <div style="display:inline-block;float:right"><button title="Add Foreign Supplier" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addforeign_supplier.php?op=add'"><span>Add Foreign Vendor</span></button>&nbsp;&nbsp;</div>
        <button title="Add New Vendor" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addVendor.php?op=add'"><span>Add New Vendor</span></button>&nbsp;&nbsp; <a href="excelexport.php?rname=<?=base64_encode("vendormaster")?>&rheader=<?=base64_encode("Vendor Master")?>" style="float:right;padding-right:20px;"  title="Export in Excel"><i class="fa fa-file-excel-o fa-2x" title="Export in excel"></i></a>&nbsp;&nbsp;&nbsp;
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th><a href="#" name="entity_id" title="asc" ></a>Status</th>
              <th data-class="expand" width="5%"><a href="#" name="entity_id" title="asc" ></a>Edit</th>
              <th data-hide="phone,tablet" width="1%"><a href="#" name="name" title="asc"></a>SNO</th>
			  <th data-hide="phone,tablet" width="15%"><a href="#" name="phone" title="asc" class="not-sort"></a>Vendor Name</th>
			  <th data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>City</th>
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort" ></a>State</th>
              
              <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Country</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Contact NO</th>
			  <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Email</th>
			  <th data-hide="phone,tablet" width="17%"><a href="#" name="name" title="asc" class="not-sort"></a>Address</th>
			  
              
            </tr>
          </thead>
          <tbody>
           <?php $i=1;
	  $query = "SELECT * FROM vendor_master order by created_date desc";
          $rs1 = mysqli_query($link1,$query) or die(mysqli_error($link1));
	   while($row1=mysqli_fetch_array($rs1)) { ?>
        <tr <?php if($i%2==1)echo "class='Table_body'";else echo "class='Table_body4'";?>>
          <td width="3%"><a href="#" class="style1" onClick="confirmDel('activeVendor.php?a=<?=$row1['sno']?>&status=<?=$row1['status']?>')"><?php echo $row1['status'];?></a></td>
          <td height="25"><?php if($row1['vendor_origin'] == 'Domestic') {?><a href="editvendor.php?sno=<?=$row1['sno']?>&vendor_origin=<?=$row1['vendor_origin']?>&mode_of_ship=<?=$row1['mode_of_ship']?>" > <?php } else { ?><a href="editforeignvendor.php?sno=<?=$row1['sno']?>&vendor_origin=<?=$row1['vendor_origin']?>&mode_of_ship=<?=$row1['mode_of_ship']?>"><?php } ?><img src="../img/view4.png" alt="Edit" align="center" border="0" ></a></td>
          <td>&nbsp;<?=$i?></td>
          <td>&nbsp;<?php echo $row1['name']?></td>
          <td>&nbsp;<?php echo $row1['city']?></td>
          <td>&nbsp;<?php echo $row1['state']?></td>
          <td>&nbsp;<?php echo $row1['country']?></td>
          <td>&nbsp;<?php echo $row1['phone']?></td>
          <td>&nbsp;<?php echo $row1['email']?></td>
          <td>&nbsp;<?php echo $row1['address']?></td>
        </tr>
        <?php 
	  $i++;
	   }  
	   ?>
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