<?php
////// Function ID ///////
$fun_id = array("a"=>array(95));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
////// filters value/////
$filter_str = "1";
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND DATE(create_date) >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND DATE(create_date) <= '".$_REQUEST['tdate']."'";
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <script src="../js/jquery-1.10.1.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
<script src="../js/bootstrap-select.min.js"></script>
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable({
        stateSave: true,
    });
	////// from date
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
	/////// to date
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
});
</script>
<link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-upload"></i> Uploaded Serial Nos.</h2>
      <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php 
            unset($_POST);
         }?>
      <form class="form-horizontal" role="form" name="frm1" action="" method="POST">
		<div class="row">
        	<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">From Date</label>
            	<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate" autocomplete="off" id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo "";}?>" onChange="document.frm1.submit();"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
        	</div>
            <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
            	<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate" autocomplete="off" id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo "";}?>"style="width:160px;" onChange="document.frm1.submit();"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
            </div>   
            <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">&nbsp;</label>
                    <a href="excelexport.php?rname=<?=base64_encode("serial_data")?>&rheader=<?=base64_encode("Serial Data")?>&fdate=<?=$_REQUEST['fdate']?>&tdate=<?=$_REQUEST['tdate']?>" title="Export Serial Data in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Serial data in excel"></i></a>  
           	</div>
       	</div>
    </form>
    <form class="form-horizontal" role="form">
        <button title="Upload Serial" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='upload_serial_data.php?op=add<?=$pagenav?>'"><span>Upload Serial</span></button>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
             
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Serial No.</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Product Code</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Model Code</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Product Name</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Dealer Code</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Dealer Name</th>
			  <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Create On</th>
			  <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Update On</th>
            </tr>
          </thead>
          <tbody>
            <?php
			             $sno=0;
                   $sql=mysqli_query($link1,"SELECT * FROM serial_upload_data WHERE ".$filter_str)or die("er1".mysqli_error($link1));
			             while($row=mysqli_fetch_assoc($sql)){
				               $sno=$sno+1;
			      ?>
            <tr class="even pointer">
                    <td><?php echo $sno;?></td>             
                    <td align="left"><?=$row['serial_no']?></td>
                    <td align="left"><?=$row['product_code']?></td>
                    <td align="left"><?=$row['model_code']?></td>
                    <td align="left"><?=$row['product_name']?></td>
                    <td align="left"><?=$row['dealer_code']?></td>
                    <td align="left"><?=$row['dealer_name']?></td>
				<td align="left"><?=$row['create_date']?></td>
				<td align="left"><?=$row['update_date']?></td>
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