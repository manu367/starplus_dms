<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);

$sql_master = "SELECT * FROM sf_lead_master WHERE lid='".$docid."'";
$res_master = mysqli_query($link1,$sql_master);
$row_master = mysqli_fetch_assoc($res_master);
?>
<table class="table table-bordered" width="100%" id="itemsTable3">
    <tbody>
      <tr class="alert-info">
        <td width="35%"><strong>Lead Id</strong></td>
        <td width="65%"><?=$row_master["reference"]?></td>
      </tr>
      <tr class="alert-info">
        <td><strong>Create Date</strong></td>
        <td><?=dt_format($row_master["tdate"])?></td>
      </tr>
      <tr class="alert-info">
        <td><strong>Initial Remark</strong></td>
        <td><?=$row_master["intial_remark"]?></td>
      </tr>
      <tr class="alert-info">
        <td><strong>Lead Source</strong></td>
        <td><?php echo get_leadsource($row_master['lead_source'],$link1);?></td>
      </tr>
      <tr class="alert-info">
        <td><strong>Status</strong></td>
        <td><?=get_status($row_master['status'],$link1)?></td>
      </tr>
      <tr class="alert-success">
        <td><strong>Party Name</strong></td>
        <td><?=$row_master["partyid"]?></td>
      </tr>
      <tr class="alert-success">
        <td><strong>Contact No.</strong></td>
        <td><?=$row_master["party_contact"]?></td>
      </tr>
      <tr class="alert-success">
        <td><strong>Email</strong></td>
        <td><?=$row_master["party_email"]?></td>
      </tr>
      <tr class="alert-success">
        <td><strong>Address</strong></td>
        <td><?=$row_master["party_address"]?></td>
      </tr>
      <tr>
        <td><strong>Transfer To</strong></td>
        <td><select name="dept" class="form-control required" id="dept" required>
                <option value="">Select </option>
                <?php 
                 $dept=mysqli_query($link1,"select name,username from admin_users where status='active' AND utype='7'");
                 while($drow=mysqli_fetch_assoc($dept)){?>
                <option value="<?php echo $drow['username'];?>"<?php if($drow['username']==$row_master['dept_id']){echo "selected='selected'";}?>><?php echo ucwords($drow['name']); ?></option>
                <?php } ?>
            </select></td>
      </tr>
      <tr>
        <td><strong>Remark</strong></td>
        <td><textarea name="transferremark" id="transferremark" class="form-control addressfield" style="resize:vertical"></textarea><input name="ref_no" id="ref_no" type="hidden" value="<?=base64_encode($row_master['lid']);?>"/><input name="leadref" id="leadref" type="hidden" value="<?=base64_encode($row_master['reference']);?>"/></td>
      </tr>
    </tbody>
</table>