<?php
require_once("../config/config.php");
$docid = $_REQUEST['doc_id'];
$partycode = $_REQUEST['partycode'];
$po_sql = "SELECT * FROM battery_charging_status WHERE id='".$docid."'";
$po_res = mysqli_query($link1,$po_sql);
$row1 = mysqli_fetch_assoc($po_res);
?>
<div class="panel panel-success table-responsive">
  <div class="panel-heading">Fill Charging Parameters</div>
  <div class="panel-body">
   <table class="table table-bordered" width="100%">
     <tbody>
     	<tr>
          <td width="30%"><strong>Vendor / Supplier</strong></td>
          <td width="70%"><?php echo getVendorDetails($partycode,"name,city,state",$link1)." (".$partycode.")"; ?></td>
        </tr>
        <tr>
          <td><strong>Ref. Doc. No.</strong></td>
          <td><?php echo $row1['doc_no']; ?></td>
        </tr>
        <tr>
          <td><strong>Product Name</strong></td>
          <td><?php echo getProductDetails($row1["prod_code"],"productname",$link1)." (".$row1["prod_code"].")";?></td>
        </tr>        
        <tr>
          <td class="bg-warning"><strong>Serial No.</strong></td>
          <td class="bg-warning"><?php echo $row1['serial_no']?></td>
        </tr>
        <tr>
          <td><strong>Import Date</strong></td>
          <td><?php echo $row1['import_date']?></td>
        </tr>
        <tr>
          <td><strong>Status</strong></td>
          <td><?php echo $row1['status']?></td>
        </tr>
        <tr>
          <td><strong>Input Voltage <span class="red_small">*</span></strong></td>
          <td>
          	<input name="inputvoltage" id="inputvoltage" type="text" class="form-control required"  required/>
          </td>
        </tr>
        <tr>
          <td><strong>Output Voltage <span class="red_small">*</span></strong></td>
          <td>
          	<input name="outputvoltage" id="outputvoltage" type="text" class="form-control required"  required/>
          </td>
        </tr>
        <tr>
          <td><strong>Remark</strong></td>
          <td>
            <textarea name="chgrmk" id="chgrmk" class="form-control" style="resize:vertical"></textarea>
            <input name="ref_no" id="ref_no" type="hidden" value="<?=base64_encode($docid);?>"/>
            <input name="serial_no" id="serial_no" type="hidden" value="<?=base64_encode($row1['serial_no']);?>"/>
            <input name="part_code" id="part_code" type="hidden" value="<?=base64_encode($row1['prod_code']);?>"/>
          </td>
        </tr>
     </tbody>
   </table>
  </div><!--close panel body-->
</div><!--close panel-->