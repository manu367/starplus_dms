<?php
require_once("../config/config.php");
$_SESSION['msgCnlSTC'] ="";
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
      <h2 align="center"><i class="fa fa-bars"></i> Stock Convert List</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
      <form class="form-horizontal" role="form">
        <button title="Stock Convert" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='stock_convert.php?op=add<?=$pagenav?>'"><span>Stock Convert</span></button>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th><a href="#" name="name" title="asc" ></a>Stock Convert From</th>
              <th><a href="#" name="name" title="asc" ></a>Document No.</th>
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Document Date</th>
			  <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Remark</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Entry By</th>
			  <th data-hide="phone,tablet">Status</th>
              <th data-hide="phone,tablet">Print</th>
              <th data-hide="phone,tablet">Serial Attach</th>
              <th data-hide="phone,tablet">View</th>
              <th data-hide="phone,tablet">Cancel</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			//// get cancel rights
			$isCnlRight = getCancelRightNew($_SESSION['userid'],"4",$link1);
			///// get access location ///
			$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
			$sql=mysqli_query($link1,"Select * from stockconvert_master where location_code in (".$accesslocation.")  order by id desc");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
				  //// check serial no. is uploaded or not
				  $rs12=mysqli_query($link1,"SELECT serial_attach, prod_code FROM stockconvert_data WHERE doc_no='".$row['doc_no']."'");
				  $check=1;
				  while($row12=mysqli_fetch_array($rs12)){
					$get_result12 = explode("~",getAnyDetails($row12['prod_code'],"productcode,is_serialize","productcode" ,"product_master",$link1));
					if($get_result12[1]=='Y'){ if($row12['serial_attach']=="Y"){ $check*=1;}else{ $check*=0;}}else{ $check*=1;}
				  }
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo str_replace("~",",",getLocationDetails($row['location_code'],"name,city",$link1));?></td>
              <td><?php echo $row['doc_no'];?></td>
              <td><?php echo $row['requested_date'];?></td>
			  <td><?php echo $row['remark'];?></td>
              <td><?php echo getAdminDetails($row['entry_by'],"name",$link1);?></td>
              <td <?php if($row['status']=="PFA"){ echo "class='red_small'";}?>><?php echo $row['status'];?></td>
              <td align="center"><?php
			  if($check==1){
              ?><a href='../print/stockconvert_print.php?rb=view&id=<?php echo base64_encode($row['doc_no']);?><?=$pagenav?>' target="_blank"  title='Print'><i class="fa fa-print fa-lg" title="Print"></i></a> <?php if($row['serial_attach']=="Y" && $row['status']!="Cancelled"){ ?>  &nbsp;&nbsp;<a href='../print/stockconvert_serialprint.php?rb=view&id=<?php echo base64_encode($row['doc_no']);?><?=$pagenav?>' target="_blank"  title='Print Serial'><i class="fa fa-print fa-lg" title="Print Serial"></i></a><?php }}else{ echo "<b style='color:#F30'>Please Upload Serial No. for Serialized Product</b>";}?></td>
			  <td align="center"><?php if($row['status']!="Cancelled"){if($row['serial_attach']==""){ if($check==0){?><a href='upload_convert_serial.php?id=<?php echo base64_encode($row['doc_no']);?><?=$pagenav?>' title='<?=$imeitag?> Attach'><i class="fa fa-upload fa-lg"></i></a>&nbsp;&nbsp;&nbsp;<a href='serial_scan_stock_convert.php?id=<?php echo base64_encode($row['doc_no']);?>&docdate=<?php echo base64_encode($row['entry_date']);?>&location=<?php echo base64_encode($row['location_code']);?>&stocktype=<?php echo base64_encode($row['stock_type']);?><?=$pagenav?>' title='Serial Scan'><i class="fa fa-qrcode fa-lg"></i></a><?php }else{ echo "Not Applicable";}}else{ echo "YES";}}?></td>
			  <td align="center"><a href='stockconvert_view.php?op=edit&id=<?php echo base64_encode($row['doc_no']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
			  <td align="center"><?php if($isCnlRight==1){if($row['status']=="Processed"){?><a href='cancelstockconvert.php?id=<?php echo base64_encode($row['doc_no']);?><?=$pagenav?>' title='cancel'><i class="fa fa-remove fa-lg" title="cancel details"></i></a><?php }}?></td> 
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