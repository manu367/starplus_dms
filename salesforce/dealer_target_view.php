<?php
require_once("../config/config.php");
$docid = base64_decode($_REQUEST['id']);
// echo $docid;
// exit;
$target_sql = "SELECT * FROM dealer_target WHERE target_no = '".$docid."'";
// echo $target_sql;
// exit;
$target_res = mysqli_query($link1,$target_sql)or die("er 1".mysqli_error($link1));
$target_row = mysqli_fetch_assoc($target_res);
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
 
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-bullseye"></i> View Target </h2><br/>
   <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">   
   <div class="panel-group">
    <div class="panel panel-default table-responsive">
        <div class="panel-heading heading1">Target Information</div>
        <div class="panel-body">
         <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Target No.</label></td>
                <td width="30%"><?php echo $target_row['target_no']; ?></td>
                <td width="20%"><label class="control-label">Target Type</label></td>
                <td width="30%"><?php echo $target_row['party_code']; ?></td>
              </tr>
              <tr>
                <td><label class="control-label">Target Month</label></td>
                <td>
					<?php 
						if($target_row['month'] == '01'){ echo "JAN"; }
						else if($target_row['month'] == '02'){ echo "FEB"; }
						else if($target_row['month'] == '03'){ echo "MAR"; }
						else if($target_row['month'] == '04'){ echo "APR"; }
						else if($target_row['month'] == '05'){ echo "MAY"; }
						else if($target_row['month'] == '06'){ echo "JUN"; }
						else if($target_row['month'] == '07'){ echo "JUL"; }
						else if($target_row['month'] == '08'){ echo "AUG"; }
						else if($target_row['month'] == '09'){ echo "SEP"; }
						else if($target_row['month'] == '10'){ echo "OCT"; }
						else if($target_row['month'] == '11'){ echo "NOV"; }
						else if($target_row['month'] == '12'){ echo "DEC"; }
						else{}
					?>
                </td>
                <td><label class="control-label">Target Year</label></td>
                <td><?php echo $target_row['year']; ?></td>
              </tr>
               <tr>
                <td><label class="control-label">Employee Name</label></td>
                <td><?php echo getAdminDetails($target_row['user_id'],"name",$link1)." | ".$target_row['emp_id'];?></td>
                <!-- <td><label class="control-label">Period Type</label></td>
                <td><?php echo $target_row['period_type']; ?></td> -->
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo getAdminDetails($target_row['entry_by'],"name",$link1)." | ".$target_row['entry_by'];?></td>
              </tr>
             
			  <tr>
                <td><label class="control-label">Entry Date</label></td>
                <td><?php echo dt_format($target_row['entry_date']); ?></td>
                <td><label class="control-label">Remark</label></td>
				<td colspan="3"><?=$target_row['remark'];?></td>
              </tr>
			  
			  <?php if($target_row['party_code'] == "Value"){ ?>
			  <tr style="background-color:#FBF7BB;">
			  	<td><label class="control-label">Target Value</label></td>
				<td colspan="3"><?=$target_row['target_val'];?></td>
			  </tr>
			  <?php } ?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <br><br>
	<?php if($target_row['party_code'] != "Qty"){ ?>
    <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr class="<?=$tableheadcolor?>" >
                <th style="text-align:center" width="6%">#</th>
				<th style="text-align:center" width="21%">Product</th>
                <th style="text-align:center" width="17%">Task Name</th>
                <th style="text-align:center" width="26%">Remark</th>
                <th style="text-align:center" width="15%">Target Value</th>
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			// echo "SELECT * FROM dealer_target WHERE target_no = '".$target_row['target_no']."'";
			// exit;
			$targetdata_sql = "SELECT * FROM dealer_target WHERE target_no = '".$target_row['target_no']."'";
			$targetdata_res = mysqli_query($link1,$targetdata_sql);
			while($targetdata_row = mysqli_fetch_assoc($targetdata_res)){
				//$proddet = explode("~",getProductDetails($targetdata_row['prod_code'],"productname,productcolor,productcode",$link1));
			?>
              <tr>
                <td style="text-align:center"><?=$i?></td>
				<td><?=$targetdata_row['prod_code'];//$proddet[0]." | ".$proddet[1]." | ".$proddet[2]?></td>
                <td style="text-align:left"><?=$targetdata_row['task_name']?></td>
                <td style="text-align:left"><?=$targetdata_row['remark']?></td>
                <td style="text-align:right"><?=round($targetdata_row['target_val'])?></td>
                </tr>
            <?php
			$i++;
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	<?php } ?>
	<div class="form-group">
	  <div class="col-md-12" style="text-align:center;" > 
		  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onClick="window.location.href='dealer_target_list.php?<?=$pagenav?>'">
	  </div>  
	</div>
	<br><br>
  </form>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>