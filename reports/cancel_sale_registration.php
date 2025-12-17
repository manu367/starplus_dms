<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);

$sql_master = "SELECT * FROM sale_registration WHERE id='".$docid."'";
$res_master = mysqli_query($link1,$sql_master);
$row_master = mysqli_fetch_assoc($res_master);
?>
<table class="table table-bordered" width="100%">
    <tbody>
      <tr>
        <td width="35%"><strong>Serial No.</strong></td>
        <td width="65%"><?=$row_master['serial_no']?></td>
      </tr>
      <tr>
        <td width="35%"><strong>Location Name</strong></td>
        <td width="65%"><?=getLocationDetails($row_master['location_code'],"name,city,state",$link1)." (".$row_master['location_code'].")"?></td>
      </tr>
      <tr>
        <td width="35%"><strong>Product Name</strong></td>
        <td width="65%"><?=$row_master['prod_name']?></td>
      </tr>
      <tr>
        <td><strong>Model Name</strong></td>
        <td><?=$row_master['model_name']?></td>
      </tr>
      <tr>
        <td><strong>Product Category</strong></td>
        <td><?=$row_master['prod_subcat']?></td>
      </tr>
      <tr>
        <td><strong>Customer Name</strong></td>
        <td><?=$row_master['customer_name']?></td>
      </tr>
      <tr>
        <td width="35%"><strong>Status <span class="red_small">*</span></strong></td>
        <td width="65%">
        	<select name="status" class="form-control required" id="status" required>
                <option value="Cancelled">Cancelled</option>
            </select></td>
      </tr>
      <tr>
        <td><strong>Remark</strong></td>
        <td><textarea name="remark" id="remark" class="form-control addressfield" style="resize:vertical"></textarea><input name="ref_no" id="ref_no" type="hidden" value="<?=base64_encode($row_master['id']);?>"/></td>
      </tr>
    </tbody>
</table>