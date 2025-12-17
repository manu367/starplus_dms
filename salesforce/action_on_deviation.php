<?php
require_once("../config/config.php");
$docid = $_REQUEST['doc_id'];
$po_sql = "SELECT * FROM deviation_request where id='".$docid."'";
$po_res = mysqli_query($link1,$po_sql);
$row1 = mysqli_fetch_assoc($po_res);
$schdate = mysqli_fetch_assoc(mysqli_query($link1,"SELECT plan_date FROM pjp_data WHERE id='".$row1["pjp_id"]."'"));
?>
<div class="panel panel-success table-responsive">
  <div class="panel-heading">Approval Action Against Deviation</div>
  <div class="panel-body">
   <table class="table table-bordered" width="100%">
     <tbody>
        <tr>
          <td width="25%"><strong>User Name</strong></td>
          <td width="75%"><?php echo getAdminDetails($row1['entry_by'],"name",$link1)." (".$row1['entry_by'].")"; ?></td>
        </tr>
        <tr>
          <td width="25%"><strong>Scheduled Date</strong></td>
          <td width="75%"><?php echo $schdate['plan_date']?></td>
        </tr>
        
        <tr>
          <td width="25%"><strong>Scheduled Visit</strong></td>
          <td width="75%"><?php echo $row1['sch_visit']?></td>
        </tr>
        <tr>
          <td><strong>Change Visit</strong></td>
          <td><?php echo $row1['change_visit']?></td>
        </tr>
        <tr>
          <td><strong>Request Raised On</strong></td>
          <td><?php echo $row1['entry_date']?></td>
        </tr>
        <tr>
          <td width="25%"><strong>Request Remark</strong></td>
          <td width="75%"><?php echo $row1['remark']?></td>
        </tr>
        <tr>
          <td width="25%"><strong>Approval Status <span class="red_small">*</span></strong></td>
          <td width="75%">
          	<select name='app_status' id='app_status' class="form-control required"  required>	
            		<option value="">--Please Select--</option>	
                    <option value="Approved"<?php if($row1['app_status']=="Approved"){ echo "selected";}?>>Approved</option>
                    <option value="Rejected"<?php if($row1['app_status']=="Rejected"){ echo "selected";}?>>Rejected</option>
                 </select></td>
        </tr>
        <tr>
          <td width="25%"><strong>Remark</strong></td>
          <td width="75%">
            <textarea name="apprmk" id="apprmk" class="form-control" style="resize:vertical"><?=$row1['app_remark']?></textarea>
            <input name="ref_no" id="ref_no" type="hidden" value="<?=base64_encode($docid);?>"/>
          </td>
        </tr>
     </tbody>
   </table>
  </div><!--close panel body-->
</div><!--close panel-->
