<?php
require_once("../config/config.php");
$docid = base64_decode($_REQUEST['doc_id']);
$po_sql = "SELECT from_location,to_location,entry_date FROM billing_master WHERE challan_no='".$docid."'";
$po_res = mysqli_query($link1,$po_sql);
$row1 = mysqli_fetch_assoc($po_res);
?>
<div class="panel panel-success table-responsive">
  <div class="panel-heading">Invoice/Document No. <?=$docid?></div>
  <div class="panel-body">
   <table class="table table-bordered" width="100%">
     <tbody>
        <tr>
          <td width="30%"><strong>From Location</strong></td>
          <td width="70%"><?php echo str_replace("~",",",getLocationDetails($row1['from_location'],"name,city,state,asc_code",$link1)); ?></td>
        </tr>
        <tr>
          <td><strong>To Location</strong></td>
          <td><?php echo str_replace("~",",",getLocationDetails($row1['to_location'],"name,city,state,asc_code",$link1)); ?></td>
        </tr>
        <tr>
          <td><strong>Invoice/Document Date</strong></td>
          <td><?php echo $row1['entry_date']?><input name="ref_no" id="ref_no" type="hidden" value="<?=base64_encode($docid);?>"/></td>
        </tr>
        <tr>
          <td><strong>POD 1</strong></td>
          <td><input type="file" class="form-control" name="pod1" id="pod1" accept="image/*,.pdf"/></td>
        </tr>
		<tr>
          <td><strong>POD 2</strong></td>
          <td><input type="file" class="form-control" name="pod2" id="pod2" accept="image/*"/></td>
        </tr>        
     </tbody>
   </table>
  </div><!--close panel body-->
</div><!--close panel-->