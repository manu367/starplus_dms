<?php
require_once("../config/config.php");
$serial_no = $_REQUEST['serialno'];
$prod_code = $_REQUEST['prodcode'];
$import_date = $_REQUEST['importdate'];
?>
<div class="panel panel-success table-responsive">
  <div class="panel-heading">Charging History Details</div>
  <div class="panel-body">
  <table class="table table-bordered" width="100%">
     <tbody>
        <tr>
          <td width="30%" class="bg-warning"><strong>Serial No.</strong></td>
          <td width="70%" class="bg-warning"><?php echo $serial_no?></td>
        </tr>
        <tr>
          <td><strong>Product Name</strong></td>
          <td><?php echo getProductDetails($prod_code,"productname",$link1)." (".$prod_code.")";?></td>
        </tr>        
        <tr>
          <td><strong>Import Date</strong></td>
          <td><?php echo $import_date?></td>
        </tr>
     </tbody>
   </table>
   <table class="table table-bordered" width="100%">
    <thead>
    <tr class="<?=$tableheadcolor?>">
        <th width="5%">S.No.</th>
        <th width="15%">Input Voltage</th>
        <th width="15%">Output Voltage</th>
        <th width="15%">Charging Remark</th>
        <th width="15%">Charging Date</th>
        <th width="15%">Updated By</th>
        <th width="20%">Updated On</th>
      </tr>
    </thead>
    <tbody>
    <?php
	$i=1;
    $sql_taskhist = "SELECT * FROM battery_charging_history WHERE serial_no='".$serial_no."' ORDER BY id DESC"; 
    $res_taskhist = mysqli_query($link1,$sql_taskhist);
    while($row_taskhist = mysqli_fetch_assoc($res_taskhist)){
    ?>
    <tr>
      <td><?=$i;?></td>	
      <td><?=$row_taskhist['input_voltage'];?></td>
      <td><?=$row_taskhist['output_voltage'];?></td>
      <td><?=$row_taskhist['charging_remark'];?></td>
      <td><?=$row_taskhist['charging_date'];?></td>
      <td><?=$row_taskhist['update_by'];?></td>
      <td><?=$row_taskhist['update_date'];?></td>       
    </tr>
    <?php
	$i++;
    }
    ?>
    </tbody>
  </table>
  </div><!--close panel body-->
</div><!--close panel-->