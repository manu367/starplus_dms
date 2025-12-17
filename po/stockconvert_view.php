<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST[id]);
$po_sql="SELECT * FROM stockconvert_master where doc_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
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
 <script type="text/javascript">
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
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-reply"></i> Stock Convert Details</h2><br/>
   <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Stock Convert From</label></td>
                <td width="30%"><?php echo str_replace("~",",",getLocationDetails($po_row['location_code'],"name,city,state",$link1));?></td>
                <td width="20%"><label class="control-label">Document No.</label></td>
                <td width="30%"><?php echo $po_row['doc_no']?></td>
              </tr>
              <tr>
                <td><label class="control-label">Document Date</label></td>
                <td><?php echo $po_row['requested_date'];?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['remark'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo getAdminDetails($po_row['entry_by'],"name",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $po_row['status'];?></td>
              </tr> 
              <tr>
                <td><label class="control-label">Cost Centre(Godown)</label></td>
                <td><?php $subl = getAnyDetails($po_row['sub_location'],"cost_center,sub_location_name","sub_location","sub_location_master",$link1); if($subl){ echo $subl;}else{ echo getAnyDetails($po_row['sub_location'],"name","asc_code","asc_master",$link1);}?></td>
                <td><label class="control-label">&nbsp;</label></td>
                <td>&nbsp;</td>
              </tr>  
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr>
                <th style="text-align:center" width="5%">#</th>
                <th style="text-align:center" width="20%">From Product Code</th>
			   <th style="text-align:center" width="15%">Stock Type</th>
                <th style="text-align:center" width="15%">Qty</th>
                <th style="text-align:center" width="15%">Convert Into</th>
                <th style="text-align:center" width="15%">Convert Stock Type</th>
                <th style="text-align:center" width="15%">Entry Date & Time</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM stockconvert_data where doc_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
			?>
              <tr>
                <td><?=$i?></td>
                <td><?php $data = getProductDetails($podata_row['prod_code'],"productname,productcolor,productcode,model_name",$link1); $d = explode('~', $data); echo $d[0].' | '.$d[1].' | '.$d[2]."|".$d[3];?></td>
				<td style="text-align:left"><?php if($podata_row['stock_type'] == 'okqty'){ echo "OK";} else if($podata_row['stock_type'] == 'broken') { echo "Damage";} else if($podata_row['stock_type'] == 'missing') { echo "Missing";} else {}?></td>
                <td style="text-align:right"><?=$podata_row['qty']?></td>
                <td style="text-align:left"><?php $todata = getProductDetails($podata_row['to_prod_code'],"productname,productcolor,productcode,model_name",$link1); $tod = explode('~', $todata); echo $tod[0].' | '.$tod[1].' | '.$tod[2]."|".$tod[3];?></td>
                <td style="text-align:left"><?php if($podata_row['convertstocktype'] == 'okqty'){ echo "OK";} else if($podata_row['convertstocktype'] == 'broken') { echo "Damage";} else if($podata_row['convertstocktype'] == 'missing') { echo "Missing";}else {}?></td>
                <td style="text-align:right"><?=$podata_row['entry_time']?></td>
              </tr>
            <?php
			$i++;
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
    <!--close panel-->
  </div><!--close panel group-->
  <div class="row" align="center">
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='stockconvert_list.php?<?=$pagenav?>'">
  </div>
  <br><br>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>